<?php

declare(strict_types=1);

namespace Simtabi\Laranail\Ichava\EmojiSets\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Simtabi\Laranail\Ichava\EmojiSets\Enums\Set;
use Simtabi\Laranail\Ichava\EmojiSets\Enums\Category;

/**
 * Pins the canonical `resources/` shape shared by every Ichava icon pack.
 *
 * The enum assertions are the load-bearing ones. bundled-icons shipped a
 * verbatim copy of metronic-icons' translation file -- wrong product, and
 * metronic's four categories against its own ten -- and nothing caught it,
 * because no pack registers translations and so nothing ever read the file.
 * Comparing keys against a real enum is what makes a copied file fail.
 *
 * This pack is the only one with two taxonomies, so it is the only one that
 * has to hold both in step.
 */
class ResourceShapeTest extends TestCase
{
    public function test_canonical_paths_exist(): void
    {
        foreach ([
            'assets/svg/config.json',
            'assets/svg/files',
            'lang/en/icons.php',
            'views/components',
        ] as $path) {
            $this->assertFileExists($this->resources() . '/' . $path);
        }
    }

    public function test_translation_group_is_not_named_after_the_locale(): void
    {
        // lang/en/en.php produced `<namespace>::en.name`, locale doubled.
        $this->assertFileDoesNotExist($this->resources() . '/lang/en/en.php');
    }

    public function test_english_does_not_duplicate_config_json_metadata(): void
    {
        // config.json is canonical for name and description -- IconRegistry
        // reads it. A copy here is exactly what drifted elsewhere.
        $lang = $this->lang();

        $this->assertArrayNotHasKey('name', $lang);
        $this->assertArrayNotHasKey('description', $lang);
    }

    public function test_set_keys_match_the_set_enum_exactly(): void
    {
        $expected = array_column(Set::cases(), 'value');
        sort($expected);

        foreach (['sets', 'set_descriptions'] as $group) {
            $actual = array_keys($this->lang()[$group]);
            sort($actual);

            $this->assertSame($expected, $actual, "lang/en/icons.php [{$group}] does not match the Set enum.");
        }
    }

    public function test_category_keys_match_the_category_enum_exactly(): void
    {
        $expected = array_column(Category::cases(), 'value');
        sort($expected);

        foreach (['categories', 'category_descriptions'] as $group) {
            $actual = array_keys($this->lang()[$group]);
            sort($actual);

            $this->assertSame(
                $expected,
                $actual,
                "lang/en/icons.php [{$group}] does not match the Category enum. "
                . 'A mismatch here usually means the file was copied from another pack.',
            );
        }
    }

    public function test_every_set_has_a_directory_on_disk(): void
    {
        // The sets are also directories under files/. A lang key naming a set
        // that does not ship is as wrong as one that does not exist.
        foreach (Set::cases() as $set) {
            $this->assertDirectoryExists($this->resources() . '/assets/svg/files/' . $set->value);
        }
    }

    public function test_config_json_is_this_package(): void
    {
        $config = json_decode(
            (string) file_get_contents($this->resources() . '/assets/svg/config.json'),
            true,
            flags: JSON_THROW_ON_ERROR,
        );

        $this->assertSame('ichava/emoji-sets', $config['package']['name']);
    }

    private function resources(): string
    {
        return dirname(__DIR__, 2) . '/resources';
    }

    /** @return array<string, mixed> */
    private function lang(): array
    {
        return require $this->resources() . '/lang/en/icons.php';
    }
}
