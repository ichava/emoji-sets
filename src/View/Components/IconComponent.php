<?php

declare(strict_types=1);

namespace Simtabi\Laranail\Ichava\EmojiSets\View\Components;

use Simtabi\Laranail\Ichava\EmojiSets\Constants\IconsConstants;
use Simtabi\Laranail\Ichava\View\Components\IconComponent as BaseIconComponent;

/**
 * Blade component for the emoji-sets multi-source bundle.
 *
 * Usage:
 *
 *   {{-- Full form: pick a set + Unicode CLDR group + slug --}}
 *   <x-ichava-emoji-sets::icon name="twemoji/smileys-emotion/grinning-face" />
 *
 *   {{-- Default set (twemoji) -- short form skips the set segment --}}
 *   <x-ichava-emoji-sets::icon name="smileys-emotion/grinning-face" />
 *
 *   {{-- Via the generic Ichava engine --}}
 *   <x-ichava::icon name="ichava/emoji-sets::twemoji/flags/flag-japan" />
 */
class IconComponent extends BaseIconComponent
{
    protected function getIconSet(): string
    {
        return IconsConstants::getPackageName();
    }

    protected function getVendorPackage(): string
    {
        return IconsConstants::getVendorPackage();
    }
}
