<?php

namespace Database\Seeders;

use DB;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([

            [
                'name' => 'superadmin',
                'guard_name' => 'web',
            ],

            [
                'name' => 'admin',
                'guard_name' => 'web',
            ],


            [
                'name' => 'dealer',
                'guard_name' => 'web',
            ],

            [
                'name' => 'staff',
                'guard_name' => 'web',
            ]

        ]);    

        	      
    }
}
