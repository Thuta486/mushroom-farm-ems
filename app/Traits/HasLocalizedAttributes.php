<?php

namespace App\Traits;

trait HasLocalizedAttributes
{
    protected function localized(string $base): string
    {
        $locale = app()->getLocale();

        $en = $this->{$base . '_en'} ?? null;
        $my = $this->{$base . '_my'} ?? null;
        $legacy = $this->{$base} ?? null;

        if ($locale === 'my' && $my) {
            return $my;
        }

        if ($locale === 'en' && $en) {
            return $en;
        }

        return $en ?? $my ?? $legacy ?? '';
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->localized('name');
    }

    public function getDisplayPositionAttribute(): string
    {
        return $this->localized('position');
    }
}
