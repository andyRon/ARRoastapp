<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrewMethodsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('brew_methods')->insert([
            [
                'method' => 'Hario V60 Dripper',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'method' => 'Chemex',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'method' => 'Siphon',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'method' => 'Kyoto Cold Brew',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'method' => 'Clover',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'method' => 'Espresso',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'method' => 'Aeropress',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'method' => 'French Press',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'method' => 'Kalita Wave Dripper',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'method' => 'Nitrous',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
        ]);
    }
}
