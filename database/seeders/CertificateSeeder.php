<?php

namespace Database\Seeders;

use DB;
use Carbon\Carbon;
use App\Models\Certificate;
use Illuminate\Database\Seeder;

class CertificateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('certificates')->insert([
            'name' => 'Master Certificate',
            'content' => 'This certificate has been issued for successful completion of a Piaggio training course.',
            'is_master' => true,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]); 
    }
}
