<?php

namespace App\Support;

use App\Models\Memory;

class AnnotationRenderer
{
    /**
     * Render a memory's body as HTML with its approved annotations wrapped
     * in <mark> spans. Expects $memory->annotations to already be loaded
     * (approved only, ordered by start_offset) to avoid N+1 queries.
     *
     * All text is escaped; the only literal HTML introduced is the <mark>
     * wrapper itself. Overlapping annotations are resolved by keeping
     * whichever one starts first and dropping any that overlap it.
     */
    public static function render(Memory $memory): string
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
            $html .= '<mark class="annotation" data-annotation-id="' . e($annotation->id) . '" data-note="' . e($annotation->body) . '">'
                . e($quote)
                . '</mark>';

            $pos = $annotation->end_offset;
        }

        $html .= e(mb_substr($body, $pos));

        return $html;
    }
}
