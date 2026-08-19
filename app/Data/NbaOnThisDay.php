<?php

namespace App\Data;

use Carbon\Carbon;

/**
 * A small, hand-picked seed of well-documented NBA moments, keyed by
 * calendar day ("n-j", no leading zeros). Meant to grow over time the
 * same way TagSeeder's lists do — add an entry, done.
 */
class NbaOnThisDay
{
    protected static array $moments = [
        '3-2' => [
            'year' => 1962,
            'text' => "Wilt Chamberlain scores 100 points for the Philadelphia Warriors against the New York Knicks in Hershey, PA — still the NBA's single-game scoring record.",
            'tag' => null,
        ],
        '6-14' => [
            'year' => 1998,
            'text' => "Michael Jordan hits the title-winning jumper over Bryon Russell in Game 6, sealing the Chicago Bulls' sixth championship.",
            'tag' => 'chicago-bulls',
        ],
        '1-22' => [
            'year' => 2006,
            'text' => 'Kobe Bryant scores 81 points against the Toronto Raptors — the second-highest single-game total in NBA history.',
            'tag' => 'los-angeles-lakers',
        ],
        '6-19' => [
            'year' => 2016,
            'text' => "The Cleveland Cavaliers complete a 3-1 Finals comeback, winning Game 7 in Oakland for the franchise's first championship.",
            'tag' => 'cleveland-cavaliers',
        ],
        '1-26' => [
            'year' => 2020,
            'text' => 'Kobe Bryant, his daughter Gianna, and seven others are killed in a helicopter crash in Calabasas, California.',
            'tag' => 'los-angeles-lakers',
        ],
        '11-7' => [
            'year' => 1991,
            'text' => 'Magic Johnson announces he is HIV-positive and retires from the NBA, stunning the sports world.',
            'tag' => 'los-angeles-lakers',
        ],
        '7-20' => [
            'year' => 2021,
            'text' => 'Giannis Antetokounmpo scores 50 points to close out Game 6, giving the Milwaukee Bucks their first title in 50 years.',
            'tag' => 'milwaukee-bucks',
        ],
        '5-16' => [
            'year' => 1980,
            'text' => 'Rookie Magic Johnson plays center and scores 42 points in Game 6, clinching the title for the Lakers over Philadelphia.',
            'tag' => 'los-angeles-lakers',
        ],
        '8-23' => [
            'year' => 1978,
            'text' => 'Kobe Bryant is born in Philadelphia, Pennsylvania.',
            'tag' => 'los-angeles-lakers',
        ],
        '12-30' => [
            'year' => 1984,
            'text' => 'LeBron James is born in Akron, Ohio.',
            'tag' => 'cleveland-cavaliers',
        ],
        '4-16' => [
            'year' => 1947,
            'text' => 'Kareem Abdul-Jabbar is born in New York City.',
            'tag' => 'los-angeles-lakers',
        ],
        '12-7' => [
            'year' => 1956,
            'text' => 'Larry Bird is born in West Baden Springs, Indiana.',
            'tag' => 'boston-celtics',
        ],
    ];

    /**
     * Returns today's moment if the calendar day has one, otherwise a
     * deterministic pick from the archive so the banner always has
     * something to show. 'exact' tells the view which framing to use.
     */
    public static function forDate(Carbon $date): array
    {
        $key = $date->month . '-' . $date->day;

        if (isset(static::$moments[$key])) {
            return static::$moments[$key] + ['exact' => true];
        }

        $keys = array_keys(static::$moments);
        $pick = static::$moments[$keys[$date->dayOfYear % count($keys)]];

        return $pick + ['exact' => false];
    }
}
