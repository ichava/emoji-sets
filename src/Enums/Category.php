<?php

declare(strict_types=1);

namespace Simtabi\Laranail\Ichava\EmojiSets\Enums;

/**
 * Unicode CLDR emoji groups.
 *
 * These 10 cases mirror the canonical groupings used by every major
 * emoji picker. Inside each ``Set`` directory under
 * ``resources/assets/svg/files/<set>/``, emojis are filed into a
 * subdirectory whose name matches one of these slugs.
 *
 * This is a plain backed enum -- the engine's variant/category contract
 * is fulfilled by the ``Set`` enum. ``Category`` is exposed purely so
 * callers can iterate / filter by Unicode group without hard-coding
 * the strings.
 */
enum Category: string
{
    case SMILEYS_EMOTION = 'smileys-emotion';
    case PEOPLE_BODY = 'people-body';
    case COMPONENT = 'component';
    case ANIMALS_NATURE = 'animals-nature';
    case FOOD_DRINK = 'food-drink';
    case TRAVEL_PLACES = 'travel-places';
    case ACTIVITIES = 'activities';
    case OBJECTS = 'objects';
    case SYMBOLS = 'symbols';
    case FLAGS = 'flags';

    /**
     * Human-readable display name for picker UIs.
     */
    public function label(): string
    {
        return match ($this) {
            self::SMILEYS_EMOTION => 'Smileys & Emotion',
            self::PEOPLE_BODY => 'People & Body',
            self::COMPONENT => 'Component',
            self::ANIMALS_NATURE => 'Animals & Nature',
            self::FOOD_DRINK => 'Food & Drink',
            self::TRAVEL_PLACES => 'Travel & Places',
            self::ACTIVITIES => 'Activities',
            self::OBJECTS => 'Objects',
            self::SYMBOLS => 'Symbols',
            self::FLAGS => 'Flags',
        };
    }

    /**
     * All categories in their canonical Unicode display order.
     *
     * @return list<self>
     */
    public static function ordered(): array
    {
        return [
            self::SMILEYS_EMOTION,
            self::PEOPLE_BODY,
            self::COMPONENT,
            self::ANIMALS_NATURE,
            self::FOOD_DRINK,
            self::TRAVEL_PLACES,
            self::ACTIVITIES,
            self::OBJECTS,
            self::SYMBOLS,
            self::FLAGS,
        ];
    }
}
