<?php

declare(strict_types=1);

namespace Simtabi\Laranail\Ichava\EmojiSets\Enums;

use Simtabi\Laranail\Ichava\Contracts\IconSetVariantInterface;
use Simtabi\Laranail\Ichava\EmojiSets\Constants\IconsConstants;
use Simtabi\Laranail\Ichava\Traits\HasIconSetVariants;

/**
 * The emoji *source* (style/artist).
 *
 * Cases mirror the top-level directories under `resources/assets/svg/files/`
 * and the keys under `metadata.data.categories` in the package config.json.
 *
 * This is the enum the Ichava engine treats as the canonical variant
 * dimension (it implements IconSetVariantInterface). The orthogonal
 * Unicode CLDR grouping (smileys, animals, food, ...) lives in
 * the sibling ``Category`` enum -- they classify two different axes.
 */
enum Set: string implements IconSetVariantInterface
{
    use HasIconSetVariants;

    case TWEMOJI = 'twemoji';
    case OPENMOJI_COLOR = 'openmoji-color';
    case OPENMOJI_BLACK = 'openmoji-black';

    public function getPath(): string
    {
        return IconsConstants::getSvgPath($this->value);
    }

    protected static function getDefaultValue(): string
    {
        return IconsConstants::getDefaultCategory() ?? self::TWEMOJI->value;
    }

    protected static function getClassPrefix(): string
    {
        return IconsConstants::getPrefix();
    }
}
