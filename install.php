<?php

if (!rex_addon::get('builder')->isAvailable()) {
    throw new rex_functional_exception('Dieses Addon benötigt das "builder" Addon.');
}

// Keine statischen Media-Manager-Typen mehr anlegen.
// Die benötigten Bild-Varianten werden über BUILDER_MEDIA_TYPE_PRESETS
// registriert und dynamisch als cb_<preset>__<width> aufgelöst.

$this->setProperty('install', true);
