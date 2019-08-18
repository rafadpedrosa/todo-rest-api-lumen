<?php

use Illuminate\Database\Seeder;

class lunchTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Http\Models\lunch::class,2)->create();
    }
}
