<?php

declare(strict_types=1);

namespace Simtabi\Laranail\Ichava\EmojiSets\Providers;

use Simtabi\Laranail\Ichava\EmojiSets\Constants\IconsConstants;
use Simtabi\Laranail\Ichava\EmojiSets\View\Components\IconComponent;
use Simtabi\Laranail\Ichava\Services\IconRegistry;
use Simtabi\Laranail\Ichava\Support\ServiceProvider;
use Simtabi\Laranail\PackageTools\Exceptions\InvalidPackage;
use Simtabi\Laranail\PackageTools\Exceptions\InvalidPath;
use Simtabi\Laranail\PackageTools\Package;

/**
 * Registers the multi-set emoji collection with the Ichava registry.
 *
 * Ships three emoji sets in v1.0:
 *
 *   - twemoji        (Twitter/X, Unicode 17, CC-BY 4.0 assets)
 *   - openmoji-color (community-driven flat colourful, CC-BY-SA 4.0)
 *   - openmoji-black (monochrome variant, CC-BY-SA 4.0)
 *
 * Within each set, files are grouped by Unicode CLDR category
 * (smileys-emotion, people-body, animals-nature, ...) so picker UIs
 * can browse cleanly. See ATTRIBUTION.md for source + licence details.
 */
class IconsServiceProvider extends ServiceProvider
{
    /**
     * @throws InvalidPath
     * @throws InvalidPackage
     */
    public function configurePackage(Package $package): void
    {
        $package
            ->setName(IconsConstants::getVendorPackage())
            ->setPathFrom(source: $this, levelsUp: 2)
            ->hasConfigFile('emoji-sets');
    }

    public function bootingPackage(): void
    {
        $this->loadBladeComponent(componentClass: IconComponent::class, packageName: 'emoji-sets');

        $this->app->make(IconRegistry::class)->fromDirectory(
            $this->package->basePath('resources/assets/svg'),
            self::class,
        );
    }
}
