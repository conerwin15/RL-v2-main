<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;
use App\Models\ContactCategory;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->insert([
            'key' => 'support_email',
            'value' => 'support-test@yopmail.com',
        ], [
            'key' => 'points_per_activity',
            'value' => 5,
        ], [
            'key' => 'correct_answer_points',
            'value' => 5,
        ]);    
    }
}
