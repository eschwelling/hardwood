<?php

namespace App\Support;

use App\Models\Memory;

class AnnotationRenderer
{
    /**
     * Render a memory's body as HTML with its annotations wrapped in <mark>
     * spans. Expects $memory->annotations to already be loaded (approved
     * only for the public feed, every status for admin) to avoid N+1s.
     *
     * All text is escaped; the only literal HTML introduced is the <mark>
     * wrapper and, in admin mode, tiny inline moderation controls right
     * after non-approved marks. Overlapping annotations are resolved by
     * keeping whichever one starts first and dropping any that overlap it.
     */
    public static function render(Memory $memory, bool $isAdmin = false): string
    {
        $body = $memory->body;
        $length = mb_strlen($body);

        $ranges = [];
        $cursor = 0;

        foreach ($memory->annotations as $annotation) {
            if ($annotation->start_offset < $cursor
                || $annotation->end_offset <= $annotation->start_offset
                || $annotation->end_offset > $length) {
                continue;
            }

            $ranges[] = $annotation;
            $cursor = $annotation->end_offset;
        }

        $html = '';
        $pos = 0;

        foreach ($ranges as $annotation) {
            $html .= e(mb_substr($body, $pos, $annotation->start_offset - $pos));

            $quote = mb_substr($body, $annotation->start_offset, $annotation->end_offset - $annotation->start_offset);
            $class = $annotation->status === 'approved' ? 'annotation' : 'annotation annotation-' . $annotation->status;

            $html .= '<mark class="' . e($class) . '" data-annotation-id="' . e($annotation->id) . '" data-note="' . e($annotation->body) . '">'
                . e($quote)
                . '</mark>';

            if ($isAdmin && $annotation->status !== 'approved') {
                $html .= static::adminControls($annotation);
            }

            $pos = $annotation->end_offset;
        }

        $html .= e(mb_substr($body, $pos));

        return $html;
    }

    private static function adminControls($annotation): string
    {
        $csrf = csrf_field();

        $approve = '<form method="POST" action="' . route('admin.annotations.approve', $annotation) . '" class="annotation-admin-form">'
            . $csrf . '<button type="submit" title="Approve annotation">✓</button></form>';

        $reject = '<form method="POST" action="' . route('admin.annotations.reject', $annotation) . '" class="annotation-admin-form">'
            . $csrf . '<button type="submit" title="Reject annotation">✗</button></form>';

        return '<span class="annotation-admin">' . $approve . $reject . '</span>';
    }
}
