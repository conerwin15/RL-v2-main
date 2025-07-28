<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;
use App\Models\LearnerBadge;

class LearnerBadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('learner_badges')->insert([
            [
                'image' => 'bronze-medal.png',
                'name' => 'bronze',
            ],

            [
                'image' => 'silver-medal.png',
                'name' => 'silver',
            ],

            [
                'image' => 'gold-medal.png',
                'name' => 'gold',
            ],

            [
                'image' => 'diamond-medal.png',
                'name' => 'diamond',
            ],

        ]);
    }
}
