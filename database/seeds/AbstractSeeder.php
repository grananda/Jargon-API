<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

abstract class AbstractSeeder extends Seeder
{
    /**
     * The Faker Generator instance.
     *
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        // Ensure all models are guarded, this way we can detect issues better
        Model::reguard();

        $this->faker = app(Faker\Generator::class);
    }

    /**
     * Truncates the given tables.
     *
     * @param string|array $tables
     *
     * @return void
     */
    protected function truncateTables($tables)
    {
        Schema::disableForeignKeyConstraints();

        foreach ((array) $tables as $table) {
            DB::table($table)->truncate();
        }

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Returns the given seeder file as an path.
     *
     * @param string $fileName
     *
     * @return string
     */
    protected function getSeedFilePath(string $fileName): string
    {
        $path = __DIR__.'/data';

        return "{$path}/{$fileName}.json";
    }

    /**
     * Returns the given file contents as an array.
     *
     * @param string $fileName
     *
     * @return array
     */
    protected function getSeedFileContents(string $fileName)
    {
        $filePath = $this->getSeedFilePath($fileName);

        return json_decode(file_get_contents($filePath), true);
    }

    /**
     * Returns an image url with the given width and height.
     *
     * @param int $width
     * @param int $height
     *
     * @return string
     */
    protected function fakeImage(int $width = 640, int $height = 480)
    {
        return $this->faker->imageUrl($width, $height);
    }
}
