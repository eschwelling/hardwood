<?php

namespace Database\Seeders;

use App\Models\Dunk;
use Illuminate\Database\Seeder;

class DunkSeeder extends Seeder
{
    public function run(): void
    {
        $dunks = [
            "Vince Carter hurdles 7'2\" Frédéric Weis at the 2000 Sydney Olympics — the 'Dunk of Death,' still replayed two decades later.",
            "Michael Jordan takes off from the free-throw line at the 1988 Slam Dunk Contest, arm cocked the whole way to the rim.",
            "Julius Erving glides from behind the backboard for a reverse layup in the 1980 Finals — no dunk, but it rewired what people thought a body could do in the air.",
            "Dominique Wilkins and Jordan trade haymakers all night at the 1988 Dunk Contest, a rivalry that still splits barstool arguments.",
            "Blake Griffin dunks over a Kia in the 2011 contest, landing on the hood after clearing the windshield.",
            "LeBron James chases down Andre Iguodala and swats his layup attempt in Game 7 of the 2016 Finals — 'The Block' that saved a title.",
            "Kobe Bryant leaps over Jason Kidd's shoulders for a full-extension dunk in 2010, one knee nearly at Kidd's ear.",
            "Zach LaVine and Aaron Gordon go five rounds in the 2016 Dunk Contest, a duel many still call the best ever.",
            "Shaquille O'Neal tears the entire basket support down in a 1993 preseason game — the rim, the backboard, all of it.",
            "Vince Carter posterizes 7-footer Alonzo Mourning at the 2000 Olympics practice, foreshadowing what was coming for Weis.",
            "Dee Brown covers his eyes and pumps his own sneaker before throwing down at the 1991 Slam Dunk Contest.",
            "Darryl Dawkins shatters the backboard in Kansas City in 1979, and again three weeks later in Philadelphia — he named the dunks afterward.",
            "Isaiah Rider debuts the East Bay Funk Dunk at the 1994 contest, reaching between his legs mid-air like it was nothing.",
            "Spud Webb, at 5'7\", wins the 1986 Slam Dunk Contest over his own teammate Dominique Wilkins.",
            "Jason Richardson goes between the legs off an alley-oop feed to himself at the 2003 contest, a move nobody had landed before.",
            "Giannis Antetokounmpo takes three dribbles from the free-throw line and finishes off two feet in transition, a full-court gallop compressed into a single leap.",
        ];

        foreach ($dunks as $body) {
            Dunk::firstOrCreate(['body' => $body], [
                'ip_hash' => null,
                'status'  => 'approved',
            ]);
        }
    }
}
