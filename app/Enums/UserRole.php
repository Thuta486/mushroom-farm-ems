<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case SuperAdmin = 'superadmin';

    public function label(): string
    {
        return match ($this) {
            self::Admin => __('app.status.admin'),
            self::SuperAdmin => __('app.status.superadmin'),
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $role) => [$role->value => $role->label()])
            ->all();
    }
}
