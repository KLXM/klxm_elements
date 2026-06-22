<?php

use KLXM\Elements\AddonSettings;

if (!rex_addon::get('yform_content_builder')->isAvailable()) {
    return;
}

require_once rex_path::addon('yform_content_builder', 'lib/config/ThemeProviderBridge.php');

rex_extension::register(
    'YFORM_CONTENT_BUILDER_ELEMENT_PATHS',
    static function (rex_extension_point $ep): array {
        $paths = (array) $ep->getSubject();
        $paths['klxm_elements'] = rex_path::addon('klxm_elements', 'elements/');
        return $paths;
    },
    rex_extension::EARLY
);

rex_extension::register(
    'YFORM_CONTENT_BUILDER_ELEMENT_MODE',
    static function (rex_extension_point $ep): string {
        return AddonSettings::getElementMode();
    },
    rex_extension::EARLY
);
