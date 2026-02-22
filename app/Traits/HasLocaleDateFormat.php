<?php

declare(strict_types=1);

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\App;

trait HasLocaleDateFormat
{
    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ? $this->formatDateTimeByCurrentLocale($value) : null,
        );
    }

    protected function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ? $this->formatDateTimeByCurrentLocale($value) : null,
        );
    }

    protected function formatDateTimeByCurrentLocale(string $dateTime): string
    {
        $locale = App::getLocale();
        $format = $locale === 'tr' ? 'd/m/Y H:i' : 'm/d/Y H:i';

        return Carbon::parse($dateTime)->format($format);
    }
}
