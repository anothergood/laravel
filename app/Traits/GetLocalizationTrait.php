<?php

namespace App\Traits;

trait GetLocalizationTrait
{
    public function getLocalizedField($field)
    {
        $localizations = $this->localization->where('field', $field)->mapWithKeys(function ($item) {
            return [$item['language'] => $item['value']];
        })->toArray();
        if ( array_key_exists(app()->getLocale(), $localizations) ) {
            return $localizations[app()->getLocale()];
        }
    }
}
