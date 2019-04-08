<?php

use App\Models\Currency;

class CurrenciesSeeder extends AbstractSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->truncateTables('currencies');

        $currencies = $this->getSeedFileContents('currencies');

        foreach ($currencies as $currency) {
            Currency::create([
                'code' => $currency['code'],
                'name' => $currency['name'],
            ]);
        }
    }
}
