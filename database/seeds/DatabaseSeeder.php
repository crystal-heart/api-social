<?php

use Illuminate\Database\Seeder;
use DB;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        
        $structs  = base_path().'/db/init.sql';
        DB::unprepared(file_get_contents($structs));
       
    }
}
