<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;
use App\Models\ContactCategory;

class ContactCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('contact_categories')->insert([
            'category_name' => 'General Category',
            'role_id' => null,
            'email' => 'superadmin@piaggio.com'
        ]);    
    }
}
