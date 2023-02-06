<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        DB::table('users')->insert([
            ['username' => 'Pablo','email' => 'pablo@gmail.com','password' => '1234','type' => 'Administrador'],
            ['username' => 'Andres','email' => 'andres@gmail.com','password' => '4321','type' => 'Particular'],
            ['username' => 'Isa','email' => 'isa@gmail.com','password' => 'gatitos','type' => 'Profesional']
        ]);
        
    }
}
