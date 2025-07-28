<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RoleSeeder::class);
    	$this->call(UserSeeder::class);
        $this->call(LanguageSeeder::class);
        $this->call(NotificationEventSeeder::class);
        $this->call(ContactCategorySeeder::class);
        $this->call(SettingSeeder::class);
        $this->call(LearnerBadgeSeeder::class);
        $this->call(CertificateSeeder::class);
    }
}
