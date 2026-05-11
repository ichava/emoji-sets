<?php

declare(strict_types=1);

namespace Simtabi\Laranail\Ichava\EmojiSets\Tests\Feature;

use Simtabi\Laranail\Ichava\EmojiSets\Constants\IconsConstants;
use Simtabi\Laranail\Ichava\EmojiSets\Enums\Category;
use Simtabi\Laranail\Ichava\EmojiSets\Enums\Set;
use Simtabi\Laranail\Ichava\EmojiSets\Providers\IconsServiceProvider;
use Simtabi\Laranail\Ichava\EmojiSets\Tests\TestCase;
use Simtabi\Laranail\Ichava\Services\IconRegistry;

class IconsTest extends TestCase
{
    public function test_provider_boots_without_error(): void
    {
        $providers = array_keys($this->app->getLoadedProviders());

        $this->assertContains(
            IconsServiceProvider::class,
            $providers
        );
    }

    public function test_constants_resolve_from_config_json(): void
    {
        $this->assertSame('ichava/emoji-sets', IconsConstants::getVendorPackage());
        $this->assertSame('Emoji Sets', IconsConstants::getTitle());
        $this->assertSame('emoji', IconsConstants::getPrefix());
    }

    public function test_set_enum_helpers_use_config_prefix(): void
    {
        // The Set enum is the canonical variant axis for this pack. The
        // class helpers should namespace each set with the config prefix.
        $this->assertSame('emoji-twemoji', Set::TWEMOJI->getClass());
        $this->assertSame('emoji-openmoji-color', Set::OPENMOJI_COLOR->getClass());
        $this->assertSame('emoji-openmoji-black', Set::OPENMOJI_BLACK->getClass());
    }

    public function test_default_set_falls_back_to_twemoji(): void
    {
        // No "default": true is set on the categories in config.json, so
        // HasIconSetVariants::default() should fall through to Set::getDefaultValue(),
        // which returns TWEMOJI as the project-wide default.
        $default = Set::default();

        $this->assertSame(Set::TWEMOJI, $default);
        $this->assertTrue(Set::TWEMOJI->isDefault());
        $this->assertFalse(Set::OPENMOJI_COLOR->isDefault());
    }

    public function test_category_enum_lists_unicode_cldr_groups(): void
    {
        // The 10 canonical Unicode emoji groups, in CLDR display order.
        $ordered = array_map(fn (Category $c) => $c->value, Category::ordered());

        $this->assertSame(
            [
                'smileys-emotion',
                'people-body',
                'component',
                'animals-nature',
                'food-drink',
                'travel-places',
                'activities',
                'objects',
                'symbols',
                'flags',
            ],
            $ordered,
        );
    }

    public function test_category_labels_are_human_readable(): void
    {
        // Picker UIs render `label()` directly, so this contract is
        // load-bearing for anyone consuming the enum.
        $this->assertSame('Smileys & Emotion', Category::SMILEYS_EMOTION->label());
        $this->assertSame('Animals & Nature', Category::ANIMALS_NATURE->label());
        $this->assertSame('Travel & Places', Category::TRAVEL_PLACES->label());
    }

    public function test_icon_registry_picks_up_the_package(): void
    {
        /** @var IconRegistry $registry */
        $registry = $this->app->make(IconRegistry::class);

        $this->assertTrue(
            $registry->isRegistered('ichava/emoji-sets'),
            'IconRegistry should have ichava/emoji-sets registered after boot.'
        );
    }
}
