<?php

namespace App\Traits;

trait GetLocalizationTrait
{
    public function getLocalizedField($field)
    {
        foreach( $this->localization as $lang ) {
            if ( $lang->field == $field and $lang->language == app()->getLocale() ) {
                return $lang->value;
            }
        }
        foreach( $this->localization as $lang ) {
            if ( $lang->field == $field) {
                return $lang->value;
            }
        }
    }
}
