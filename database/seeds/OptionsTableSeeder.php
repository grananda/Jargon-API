<?php

use App\Models\Options\Option;
use App\Models\Options\OptionCategory;
use Illuminate\Support\Facades\Event;

class OptionsTableSeeder extends AbstractSeeder
{
    public function run()
    {
        $this->truncateTables(['option_categories', 'options']);

        Event::fake([
            \App\Events\Option\OptionWasCreated::class,
            \App\Events\Option\OptionWasUpdated::class,
            \App\Events\Option\OptionWasDeleted::class,
        ]);

        $optionCategories = $this->getSeedFileContents('options');

        foreach ($optionCategories as $optionCategory) {
            /** @var \App\Models\Options\OptionCategory $optionCatItem */
            $optionCatItem = factory(OptionCategory::class)->create([
                'title'       => $optionCategory['title'],
                'description' => $optionCategory['description'],
            ]);

            foreach ($optionCategory['options'] as $option) {
                factory(Option::class)->create([
                    'title'              => $option['title'],
                    'description'        => $option['description'],
                    'option_category_id' => $optionCatItem->id,
                    'option_key'         => $option['option_key'],
                    'option_value'       => $option['option_value'],
                    'option_scope'       => $option['option_scope'],
                    'option_type'        => $option['option_type'],
                    'is_private'         => $option['is_private'],
                ]);
            }
        }
    }
}
