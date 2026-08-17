<?php

namespace Database\Seeders;

use App\Models\Venue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VenueSeeder extends Seeder
{
    public function run(): void
    {
        $venues = [
            // Current arenas
            ['name' => 'State Farm Arena', 'lat' => 33.7573, 'lng' => -84.3963],
            ['name' => 'TD Garden', 'lat' => 42.3662, 'lng' => -71.0621],
            ['name' => 'Barclays Center', 'lat' => 40.6826, 'lng' => -73.9754],
            ['name' => 'Spectrum Center', 'lat' => 35.2251, 'lng' => -80.8392],
            ['name' => 'United Center', 'lat' => 41.8807, 'lng' => -87.6742],
            ['name' => 'Rocket Arena', 'lat' => 41.4965, 'lng' => -81.6882],
            ['name' => 'American Airlines Center', 'lat' => 32.7905, 'lng' => -96.8103],
            ['name' => 'Ball Arena', 'lat' => 39.7487, 'lng' => -105.0077],
            ['name' => 'Little Caesars Arena', 'lat' => 42.3410, 'lng' => -83.0550],
            ['name' => 'Chase Center', 'lat' => 37.7680, 'lng' => -122.3877],
            ['name' => 'Toyota Center', 'lat' => 29.7508, 'lng' => -95.3621],
            ['name' => 'Gainbridge Fieldhouse', 'lat' => 39.7639, 'lng' => -86.1555],
            ['name' => 'Intuit Dome', 'lat' => 33.9457, 'lng' => -118.3416],
            ['name' => 'Crypto.com Arena', 'lat' => 34.0430, 'lng' => -118.2673],
            ['name' => 'FedExForum', 'lat' => 35.1382, 'lng' => -90.0505],
            ['name' => 'Kaseya Center', 'lat' => 25.7814, 'lng' => -80.1870],
            ['name' => 'Fiserv Forum', 'lat' => 43.0451, 'lng' => -87.9174],
            ['name' => 'Target Center', 'lat' => 44.9795, 'lng' => -93.2760],
            ['name' => 'Smoothie King Center', 'lat' => 29.9490, 'lng' => -90.0821],
            ['name' => 'Madison Square Garden', 'lat' => 40.7505, 'lng' => -73.9934],
            ['name' => 'Paycom Center', 'lat' => 35.4634, 'lng' => -97.5151],
            ['name' => 'Kia Center', 'lat' => 28.5392, 'lng' => -81.3839],
            ['name' => 'Wells Fargo Center', 'lat' => 39.9012, 'lng' => -75.1720],
            ['name' => 'Footprint Center', 'lat' => 33.4457, 'lng' => -112.0712],
            ['name' => 'Moda Center', 'lat' => 45.5316, 'lng' => -122.6668],
            ['name' => 'Golden 1 Center', 'lat' => 38.5802, 'lng' => -121.4997],
            ['name' => 'Frost Bank Center', 'lat' => 29.4269, 'lng' => -98.4375],
            ['name' => 'Scotiabank Arena', 'lat' => 43.6435, 'lng' => -79.3791],
            ['name' => 'Delta Center', 'lat' => 40.7683, 'lng' => -111.9011],
            ['name' => 'Capital One Arena', 'lat' => 38.8981, 'lng' => -77.0209],

            // Historic arenas — demolished or long since replaced, kept
            // around for the fans who were actually there.
            ['name' => 'Boston Garden', 'lat' => 42.3667, 'lng' => -71.0623],
            ['name' => 'Chicago Stadium', 'lat' => 41.8807, 'lng' => -87.6742],
            ['name' => 'The Forum', 'lat' => 33.9584, 'lng' => -118.3417],
            ['name' => 'Richfield Coliseum', 'lat' => 41.2381, 'lng' => -81.6212],
            ['name' => 'Market Square Arena', 'lat' => 39.7663, 'lng' => -86.1547],
            ['name' => 'Charlotte Coliseum', 'lat' => 35.1615, 'lng' => -80.8531],
            ['name' => 'The Omni Coliseum', 'lat' => 33.7576, 'lng' => -84.3956],
            ['name' => 'The Palace of Auburn Hills', 'lat' => 42.6898, 'lng' => -83.2458],
            ['name' => 'Capital Centre', 'lat' => 38.9384, 'lng' => -76.8683],
            ['name' => 'Reunion Arena', 'lat' => 32.7765, 'lng' => -96.8107],
            ['name' => 'Veterans Memorial Coliseum', 'lat' => 33.4515, 'lng' => -112.0879],
            ['name' => 'Seattle Center Coliseum', 'lat' => 47.6221, 'lng' => -122.3540],
        ];

        foreach ($venues as $venue) {
            Venue::firstOrCreate(['slug' => Str::slug($venue['name'])], [
                'name' => $venue['name'],
                'latitude' => $venue['lat'],
                'longitude' => $venue['lng'],
            ]);
        }
    }
}
