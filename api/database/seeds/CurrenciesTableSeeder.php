<?php

use Illuminate\Database\Seeder;

class CurrenciesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('currencies')->insert([
            'currency' => 'US Dollars',
            'currency_short' => 'USD',
            'symbol' => '$',
            'value_in_usd' => 1,
        ]);

        DB::table('currencies')->insert([
            'currency' => 'Euro',
            'currency_short' => 'EUR',
            'symbol' => '€',
            'value_in_usd' => 1.19,
        ]);
    }
}
