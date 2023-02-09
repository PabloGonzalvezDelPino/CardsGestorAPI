<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
            ['username' => 'Pablo','email' => 'pablo@gmail.com','password' => Hash::make('1234'),'type' => 'Administrador'],
            //['username' => 'Andres','email' => 'andres@gmail.com','password' => Hash::make('1234'),'type' => 'Particular'],
            //['username' => 'Isa','email' => 'isa@gmail.com','password' => Hash::make('1234'),'type' => 'Profesional']
        ]);
      /* DB::table('cards')->insert([
            ['name' => 'Charmander','description' => 'Pokemon inicial de tipo Fugo','number'=>'asd'],
            ['name' => 'Charmeleon','description' => 'Primera evolución de Charmadner','number'=>'dsa'],
            ['name' => 'Charizard','description' => 'Segunda evolución de Charmadner','number'=>'sad'],
            ['name' => 'Squirtle','description' => 'Pokemon inicial de tipo agua','number'=>'das'],
            ['name' => 'Wartortle','description' => 'Priemra evolución de Squirtle','number'=>'qwe'],
            ['name' => 'Blastoise','description' => 'Segunda evolución de Squirtle','number'=>'aewqsv']
        ]);
        DB::table('collections')->insert([
            ['name' => 'Fuego','image' => 'url de la imagen','realeaseDate' => date('Y-m-d h:i:s'),'code' =>'qw'],
            ['name' => 'Agua','image' => 'url de la imagen','realeaseDate' => date('Y-m-d h:i:s'),'code' =>'sad'
            ]
        ]);
        DB::table('card_collection')->insert([
            ['card_id' => 1,'collection_id' => 1],
            ['card_id' => 2,'collection_id' => 1],
            ['card_id' => 3,'collection_id' => 1],
            ['card_id' => 4,'collection_id' => 2],
            ['card_id' => 5,'collection_id' => 2],
            ['card_id' => 6,'collection_id' => 2]
        ]);
        DB::table('card_user')->insert([
            ['card_id' => 1,'user_id' => 2,'amount' => 2,'price' => 10],
            ['card_id' => 5,'user_id' => 3,'amount' => 1,'price' => 50],
            ['card_id' => 2,'user_id' => 3,'amount' => 1,'price' => 100]
        ]);     */
    }
}
