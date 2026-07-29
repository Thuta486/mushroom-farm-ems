<?php

namespace App\Enums;

enum WorkType: string
{
    case Harvesting = 'harvesting';
    case Cleaning = 'cleaning';
    case Packaging = 'packaging';
    case Supervision = 'supervision';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Harvesting => 'Harvesting',
            self::Cleaning => 'Cleaning',
            self::Packaging => 'Packaging',
            self::Supervision => 'Supervision',
            self::Other => 'Other',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $type) => [$type->value => $type->label()])
            ->all();
    }
}
