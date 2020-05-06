<?php

namespace App\Library;

use Nip\I18n\TranslatorServiceProvider;

/**
 * Class Application
 * @package App\Library
 */
class Application extends \Nip\Application\Application
{
    /**
     * @return array
     */
    public function getGenericProviders()
    {
        $providers = parent::getGenericProviders();
        if (($key = array_search(TranslatorServiceProvider::class, $providers)) !== false) {
            unset($providers[$key]);
        }
        return $providers;
    }

}