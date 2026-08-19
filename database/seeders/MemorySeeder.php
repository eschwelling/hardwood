<?php

namespace Database\Seeders;

use App\Models\Annotation;
use App\Models\Dunk;
use App\Models\Memory;
use App\Models\Mixtape;
use App\Models\Resonate;
use App\Models\Tag;
use App\Models\Venue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MemorySeeder extends Seeder
{
    public function run(): void
    {
        $memories = [
            [
                'body' => "My dad woke me up at 11pm to watch the end of Game 6. I didn't even know what was happening, just that he was crying and I'd never seen that before. Jordan's last shot as a Bull, right in front of us on a 19-inch TV.",
                'team' => 'Chicago Bulls', 'decade' => '1990s', 'experience' => 'Watched at home',
                'date' => '1998-06-14', 'precision' => 'day',
                'resonates' => ['fire' => 14, 'goat' => 9, 'hype' => 4],
            ],
            [
                'body' => "Our whole apartment building erupted at the same time when Jordan hit that shot over Russell. You could hear it through every wall like the building itself was cheering.",
                'team' => 'Chicago Bulls', 'decade' => '1990s', 'experience' => 'Watched at home',
                'date' => '1998-06-14', 'precision' => 'day',
                'resonates' => ['fire' => 6, 'hype' => 2],
            ],
            [
                'body' => "I was 14 and drove to my buddy's house because his family had the good cable package. We sat two feet from the screen for the entire fourth quarter of Game 7 and neither of us said a word.",
                'team' => 'Cleveland Cavaliers', 'decade' => '2010s', 'experience' => 'Watched at a bar',
                'date' => '2016-06-19', 'precision' => 'day',
                'resonates' => ['fire' => 10, 'hype' => 7, 'goat' => 1],
            ],
            [
                'body' => "Cleveland had waited 52 years for a title in any sport. When the buzzer went off my entire street came outside at once, banging pots and pans like it was midnight on New Year's.",
                'team' => 'Cleveland Cavaliers', 'decade' => '2010s', 'experience' => 'Attended the game',
                'date' => '2016-06-19', 'precision' => 'day',
                'resonates' => ['fire' => 18, 'hype' => 11],
                'annotation' => ['start' => 0, 'end' => 45, 'body' => 'This is the exact energy I remember from that night.'],
            ],
            [
                'body' => "Reggie Miller scored eight points in nine seconds and I was standing in the Garden losing my mind while the Knicks fans around me just went completely silent. Never heard a building go that quiet that fast.",
                'team' => 'Indiana Pacers', 'decade' => '1990s', 'experience' => 'Attended the game',
                'venue' => 'Madison Square Garden', 'date' => '1995-05-07', 'precision' => 'day',
                'resonates' => ['fire' => 8, 'goat' => 5],
            ],
            [
                'body' => "My grandfather told this story every Thanksgiving until he passed — how the Garden turned on him and his buddies for cheering Miller, and how he didn't care one bit.",
                'team' => 'Indiana Pacers', 'decade' => '1990s', 'experience' => 'Attended the game',
                'venue' => 'Madison Square Garden', 'date' => '1995-05-07', 'precision' => 'day',
                'resonates' => ['fire' => 3, 'goat' => 2],
            ],
            [
                'body' => "Larry Bird stealing that inbounds pass is the first sports memory I have that felt like it mattered. I was six, sitting on the floor way too close to the TV, and my mom screamed so loud I thought something was wrong.",
                'team' => 'Boston Celtics', 'decade' => '1980s', 'experience' => 'Watched at home',
                'venue' => 'Boston Garden', 'date' => '1987-05-26', 'precision' => 'day',
                'resonates' => ['fire' => 12, 'goat' => 6, 'hype' => 3],
            ],
            [
                'body' => "Watching Magic and Bird go at it every spring was the whole reason I fell in love with this sport. Two guys who hated each other making the other one better every single night.",
                'team' => 'Boston Celtics', 'decade' => '1980s', 'experience' => 'Watched at home',
                'resonates' => ['goat' => 9, 'fire' => 4],
            ],
            [
                'body' => "Kobe dropped 81 and I was there for exactly none of it — fell asleep on the couch in the third quarter and woke up to my phone blowing up. Still mad about it twenty years later.",
                'team' => 'Los Angeles Lakers', 'decade' => '2000s', 'experience' => 'Watched at home',
                'date' => '2006-01', 'precision' => 'month',
                'resonates' => ['cry' => 15, 'fire' => 3],
            ],
            [
                'body' => "My uncle had floor seats once, just once, and he swears Kobe made eye contact with him before a free throw. I don't believe him but I've never once asked him to stop telling the story.",
                'team' => 'Los Angeles Lakers', 'decade' => '2000s', 'experience' => 'Attended the game',
                'venue' => 'Crypto.com Arena',
                'resonates' => ['fire' => 5, 'goat' => 2],
            ],
            [
                'body' => "Ray Allen's corner three to save the Finals happened right in front of my section. For a half second the entire building forgot how to make noise, and then it was the loudest place I've ever stood.",
                'team' => 'Miami Heat', 'decade' => '2010s', 'experience' => 'Attended the game',
                'date' => '2013-06-18', 'precision' => 'day',
                'resonates' => ['fire' => 13, 'hype' => 8],
            ],
            [
                'body' => "I still think about Ray Allen backpedaling into that corner like he had all the time in the world. Spurs fans in my section went from celebrating to dead silent in the time it took the ball to leave his hands.",
                'team' => 'San Antonio Spurs', 'decade' => '2010s', 'experience' => 'Attended the game',
                'date' => '2013-06-18', 'precision' => 'day',
                'resonates' => ['cry' => 7, 'fire' => 2],
            ],
            [
                'body' => "Dirk's fadeaway one-legger became the shot every kid on my street tried to copy for an entire summer. Nobody could land it, we just kept falling into the neighbor's hedge.",
                'team' => 'Dallas Mavericks', 'decade' => '2010s', 'experience' => 'Pickup ball',
                'resonates' => ['fire' => 6, 'hype' => 4],
            ],
            [
                'body' => "The Sonics leaving Seattle is still a wound that hasn't closed for a lot of us. I keep my old KeyArena ticket stubs in a drawer I probably open once a year, just to remember it happened.",
                'team' => 'Oklahoma City Thunder', 'decade' => '2000s', 'experience' => 'Other',
                'resonates' => ['cry' => 11],
            ],
            [
                'body' => "Giannis going coast to coast in transition looks like nothing I grew up watching — more like a running back who happens to be seven feet tall. I still yell at the TV every time he takes off.",
                'team' => 'Milwaukee Bucks', 'decade' => '2020s', 'experience' => 'Watched at home',
                'venue' => 'Fiserv Forum',
                'resonates' => ['fire' => 9, 'hype' => 6],
            ],
            [
                'body' => "My dad took me to my first game at the old Charlotte Coliseum and bought me a shirt three sizes too big on purpose, 'so you'll grow into it.' I wore it until it actually fit.",
                'team' => 'Charlotte Hornets', 'decade' => '1990s', 'experience' => 'Attended the game',
                'venue' => 'Charlotte Coliseum',
                'resonates' => ['fire' => 4, 'hype' => 2],
            ],
            [
                'body' => "Steph Curry's 3-point record night felt less like a basketball game and more like watching someone break a law of physics in real time. Every miss got a bigger reaction than most makes from other guys.",
                'team' => 'Golden State Warriors', 'decade' => '2010s', 'experience' => 'Watched at a bar',
                'resonates' => ['fire' => 16, 'hype' => 9],
            ],
            [
                'body' => "Listened to a whole Sixers playoff run on the radio because our TV broke mid-series and my parents refused to replace it until summer. I could describe Iverson's crossover from sound alone by the end of it.",
                'team' => 'Philadelphia 76ers', 'decade' => '2000s', 'experience' => 'Heard it on the radio',
                'resonates' => ['fire' => 7],
            ],
            [
                'body' => "The Palace of Auburn Hills on a Pistons playoff night was the loudest indoor place I've ever been, full stop. My ears rang for a full day afterward and I would do it again in a heartbeat.",
                'team' => 'Detroit Pistons', 'decade' => '2000s', 'experience' => 'Attended the game',
                'venue' => 'The Palace of Auburn Hills',
                'resonates' => ['fire' => 8, 'hype' => 5],
            ],
            [
                'body' => "I got benched the entire fourth quarter of a rec league final and sulked about it for a week, until I watched the tape back and realized our team actually played better without me. Humbling at fourteen years old.",
                'team' => 'New York Knicks', 'decade' => '2010s', 'experience' => 'Pickup ball',
                'resonates' => ['cry' => 3, 'fire' => 1],
            ],
            [
                'body' => "The Grizzlies grit-and-grind teams taught me that a team doesn't need a superstar to be unbelievably fun to watch. That defense felt personal, like they were mad at you specifically.",
                'team' => 'Memphis Grizzlies', 'decade' => '2010s', 'experience' => 'Watched at home',
                'venue' => 'FedExForum',
                'resonates' => ['fire' => 6, 'goat' => 1],
            ],
            [
                'body' => "My mom, who has never cared about sports a single day in her life, watched the entire Raptors championship run with me in 2019 because I begged her to. She still asks about Kawhi sometimes.",
                'team' => 'Toronto Raptors', 'decade' => '2010s', 'experience' => 'Watched at home',
                'date' => '2019', 'precision' => 'year',
                'resonates' => ['fire' => 10, 'hype' => 4],
            ],
            [
                'body' => "Watching Kevin Garnett scream at the sky after finally winning it all made me tear up on a couch I wasn't even supposed to be sitting on, since it was technically my sister's.",
                'team' => 'Boston Celtics', 'decade' => '2000s', 'experience' => 'Watched at home',
                'resonates' => ['cry' => 9, 'fire' => 3],
            ],
            [
                'body' => "Our high school gym smelled exactly like every gym in every arena I've been to since — floor wax and popcorn. I still get a little emotional walking into any NBA building for that smell alone.",
                'team' => 'Denver Nuggets', 'decade' => '1990s', 'experience' => 'Other',
                'resonates' => ['fire' => 2],
            ],
            [
                'body' => "The 2004 Pistons taught me that chemistry beats star power more than anyone gives it credit for. Nobody outside Detroit believed in that team until it was already over.",
                'team' => 'Detroit Pistons', 'decade' => '2000s', 'experience' => 'Watched at home',
                'date' => '2004', 'precision' => 'year',
                'resonates' => ['goat' => 4, 'fire' => 3],
            ],
            [
                'body' => "I skipped my own cousin's graduation party to watch the Suns' seven-seconds-or-less offense in the playoffs and I have exactly zero regrets about it, then or now.",
                'team' => 'Phoenix Suns', 'decade' => '2000s', 'experience' => 'Watched at a bar',
                'resonates' => ['fire' => 5, 'hype' => 3],
            ],
            [
                'body' => "The first NBA game I ever attended, my dad let me buy a foam finger even though he thought it was a waste of five dollars. I still have it in a box in my parents' garage.",
                'team' => 'Orlando Magic', 'decade' => '1990s', 'experience' => 'Attended the game',
                'venue' => 'Kia Center',
                'resonates' => ['fire' => 3, 'hype' => 2],
            ],
            [
                'body' => "Vince Carter's dunk contest run in 2000 is still the standard every dunk contest gets compared to and unfairly loses against. We didn't know how good we had it watching that live.",
                'team' => 'Toronto Raptors', 'decade' => '2000s', 'experience' => 'Watched at home',
                'resonates' => ['fire' => 11, 'hype' => 7],
            ],
            [
                'body' => "Allen Iverson stepping over Tyronn Lue is the single most replayed clip in every group chat I've ever been part of. Somebody posts it at least once a year like clockwork.",
                'team' => 'Philadelphia 76ers', 'decade' => '2000s', 'experience' => 'Watched at home',
                'resonates' => ['fire' => 9, 'hype' => 5],
            ],
        ];

        foreach ($memories as $m) {
            $memory = Memory::firstOrCreate(
                ['body' => $m['body']],
                [
                    'ip_hash' => hash('sha256', 'seed:' . $m['body']),
                    'status' => 'approved',
                    'game_date' => $this->seedDate($m['date'] ?? null, $m['precision'] ?? null),
                    'game_date_precision' => $m['precision'] ?? null,
                ]
            );

            if (!$memory->wasRecentlyCreated) {
                continue;
            }

            $memory->created_at = now()->subDays(random_int(3, 260));
            $memory->save();

            $tagNames = array_filter([$m['team'] ?? null, $m['decade'] ?? null, $m['experience'] ?? null]);
            $memory->tags()->attach(Tag::whereIn('name', $tagNames)->pluck('id'));

            if (!empty($m['venue'])) {
                $venue = Venue::where('name', $m['venue'])->first();
                if ($venue) {
                    $memory->venues()->attach($venue->id);
                }
            }

            foreach ($m['resonates'] ?? [] as $type => $count) {
                for ($i = 0; $i < $count; $i++) {
                    Resonate::create([
                        'memory_id' => $memory->id,
                        'ip_hash'   => hash('sha256', "seed:{$memory->id}:{$type}:{$i}"),
                        'type'      => $type,
                    ]);
                }
            }

            if (!empty($m['annotation'])) {
                Annotation::create([
                    'memory_id'    => $memory->id,
                    'start_offset' => $m['annotation']['start'],
                    'end_offset'   => $m['annotation']['end'],
                    'body'         => $m['annotation']['body'],
                    'ip_hash'      => hash('sha256', "seed:{$memory->id}:annotation"),
                    'status'       => 'approved',
                ]);
            }
        }

        $this->seedMixtapes();
    }

    private function seedDate(?string $date, ?string $precision): ?string
    {
        if (!$date) {
            return null;
        }

        return match ($precision) {
            'day'   => $date,
            'month' => $date . '-01',
            'year'  => $date . '-01-01',
            default => $date,
        };
    }

    private function seedMixtapes(): void
    {
        $this->seedMixtape(
            'Jordan, Bird, and the Shots That Shut Buildings Up',
            null,
            null,
            [
                "My dad woke me up at 11pm to watch the end of Game 6. I didn't even know what was happening, just that he was crying and I'd never seen that before. Jordan's last shot as a Bull, right in front of us on a 19-inch TV.",
                "Larry Bird stealing that inbounds pass is the first sports memory I have that felt like it mattered. I was six, sitting on the floor way too close to the TV, and my mom screamed so loud I thought something was wrong.",
                "Reggie Miller scored eight points in nine seconds and I was standing in the Garden losing my mind while the Knicks fans around me just went completely silent. Never heard a building go that quiet that fast.",
                "Ray Allen's corner three to save the Finals happened right in front of my section. For a half second the entire building forgot how to make noise, and then it was the loudest place I've ever stood.",
            ],
            [
                "Vince Carter hurdles 7'2\" Frédéric Weis at the 2000 Sydney Olympics — the 'Dunk of Death,' still replayed two decades later.",
                "Michael Jordan takes off from the free-throw line at the 1988 Slam Dunk Contest, arm cocked the whole way to the rim.",
            ]
        );

        $this->seedMixtape(
            'Cleveland Waited 52 Years For This',
            'Cleveland Cavaliers',
            '#860038',
            [
                "I was 14 and drove to my buddy's house because his family had the good cable package. We sat two feet from the screen for the entire fourth quarter of Game 7 and neither of us said a word.",
                "Cleveland had waited 52 years for a title in any sport. When the buzzer went off my entire street came outside at once, banging pots and pans like it was midnight on New Year's.",
            ],
            [
                "LeBron James chases down Andre Iguodala and swats his layup attempt in Game 7 of the 2016 Finals — 'The Block' that saved a title.",
            ]
        );
    }

    private function seedMixtape(string $title, ?string $teamName, ?string $teamColor, array $memoryBodies, array $dunkBodies): void
    {
        $mixtape = Mixtape::firstOrCreate(
            ['title' => $title],
            [
                'team_name'  => $teamName,
                'team_color' => $teamColor,
                'ip_hash'    => hash('sha256', 'seed-mixtape:' . $title),
                'status'     => 'approved',
            ]
        );

        if (!$mixtape->wasRecentlyCreated) {
            return;
        }

        $now = now();
        $position = 0;
        $rows = [];

        foreach ($memoryBodies as $body) {
            $memory = Memory::where('body', $body)->first();
            if ($memory) {
                $rows[] = ['type' => Memory::class, 'id' => $memory->id, 'position' => $position++];
            }
        }

        foreach ($dunkBodies as $body) {
            $dunk = Dunk::where('body', $body)->first();
            if ($dunk) {
                $rows[] = ['type' => Dunk::class, 'id' => $dunk->id, 'position' => $position++];
            }
        }

        if (count($rows) < Mixtape::MIN_TRACKS) {
            return;
        }

        DB::table('mixtape_trackables')->insert(array_map(fn ($row) => [
            'mixtape_id'     => $mixtape->id,
            'trackable_type' => $row['type'],
            'trackable_id'   => $row['id'],
            'position'       => $row['position'],
            'created_at'     => $now,
            'updated_at'     => $now,
        ], $rows));
    }
}
