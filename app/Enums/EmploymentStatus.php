<?php

namespace App\Enums;

enum EmploymentStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Terminated = 'terminated';

    public function label(): string
    {
        return match ($this) {
            self::Active => __('app.status.active'),
            self::Inactive => __('app.status.inactive'),
            self::Terminated => __('app.status.terminated'),
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $status) => [$status->value => $status->label()])
            ->all();
    }
}
