<?php

declare(strict_types=1);

namespace Simtabi\Laranail\Ichava\EmojiSets\Constants;

use Simtabi\Laranail\Ichava\Constants\JsonConfigConstants;
use Simtabi\Laranail\Ichava\Support\PathResolver;

/**
 * Resolves emoji-sets metadata from its `resources/assets/svg/config.json`.
 *
 * @see \Simtabi\Laranail\Ichava\Constants\JsonConfigConstants
 */
final class IconsConstants extends JsonConfigConstants
{
    protected static function getConfigPath(): string
    {
        return PathResolver::resolvePackagePath(self::class, levelsUp: 3, append: 'resources/assets/svg');
    }
}
