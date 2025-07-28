<?php
 
namespace Database\Seeders;
  
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
  
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'superadmin', 
            'email' => 'superadmin@piaggio.com',
            'password' => bcrypt('123456')
        ]);
    
        $user->assignRole('superadmin');
        
    }
}