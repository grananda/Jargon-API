<?php

use App\Models\Dialect;
use App\Models\Language;

class LanguagesTableSeeder extends AbstractSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->truncateTables(['languages']);

        $locales           = $this->getSeedFileContents('languages');
        $langKeys          = [];
        $insertedLanguages = [];

        foreach ($locales as $locale) {
            $codes = explode('_', $locale['code']);
            $def   = explode(' ', $locale['language']);

            $language   = $def[0]   ?? null;
            $langKey    = $codes[0] ?? null;
            $countryKey = $codes[1] ?? null;
            $country    = $def[1]   ?? null;

            $country = ! is_null($country) ? str_replace('(', '', $country) : null;
            $country = ! is_null($country) ? str_replace(')', '', $country) : null;

            if (! in_array($langKey, $langKeys)) {
                $_l = Language::create([
                    'name'     => $language,
                    'lang_key' => $langKey,
                ]);

                $langKeys[]                  = $langKey;
                $insertedLanguages[$langKey] = $_l->id;
            }

            Dialect::create([
                'name'        => $locale['language'],
                'locale'      => $locale['code'],
                'country'     => $country,
                'language_id' => $insertedLanguages[$langKey],
                'country_key' => $countryKey ?? null,
            ]);
        }
    }

    public static function getUsedLanguages()
    {
        return
            [
                [
                    'dialect_id' => Dialect::where('locale', 'en_US')->get()->first()->id,
                    'is_default' => true,
                ],
                [
                    'dialect_id' => Dialect::where('locale', 'es_ES')->get()->first()->id,
                    'is_default' => false,
                ],
            ];
    }
}
