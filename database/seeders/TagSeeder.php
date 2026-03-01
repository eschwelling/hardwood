<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        // Teams
        $teams = [
            'Atlanta Hawks', 'Boston Celtics', 'Brooklyn Nets', 'Charlotte Hornets',
            'Chicago Bulls', 'Cleveland Cavaliers', 'Dallas Mavericks', 'Denver Nuggets',
            'Detroit Pistons', 'Golden State Warriors', 'Houston Rockets', 'Indiana Pacers',
            'Los Angeles Clippers', 'Los Angeles Lakers', 'Memphis Grizzlies', 'Miami Heat',
            'Milwaukee Bucks', 'Minnesota Timberwolves', 'New Orleans Pelicans', 'New York Knicks',
            'Oklahoma City Thunder', 'Orlando Magic', 'Philadelphia 76ers', 'Phoenix Suns',
            'Portland Trail Blazers', 'Sacramento Kings', 'San Antonio Spurs', 'Toronto Raptors',
            'Utah Jazz', 'Washington Wizards',
        ];

        foreach ($teams as $team) {
            Tag::firstOrCreate(['slug' => Str::slug($team)], [
                'type' => 'team',
                'name' => $team,
                'slug' => Str::slug($team),
            ]);
        }

        // Decades
        $decades = ['1960s', '1970s', '1980s', '1990s', '2000s', '2010s', '2020s'];

        foreach ($decades as $decade) {
            Tag::firstOrCreate(['slug' => Str::slug($decade)], [
                'type' => 'decade',
                'name' => $decade,
                'slug' => Str::slug($decade),
            ]);
        }

        // Experience types
        $experiences = [
            'Attended the game',
            'Watched at home',
            'Watched at a bar',
            'Heard it on the radio',
            'Pickup ball',
            'Other',
        ];

        foreach ($experiences as $experience) {
            Tag::firstOrCreate(['slug' => Str::slug($experience)], [
                'type' => 'experience',
                'name' => $experience,
                'slug' => Str::slug($experience),
            ]);
        }
    }
}
