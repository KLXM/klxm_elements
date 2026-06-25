<?php

use KLXM\Elements\AddonSettings;

$builderAddonKey = '';
if (rex_addon::exists('builder') && rex_addon::get('builder')->isAvailable()) {
    $builderAddonKey = 'builder';
} elseif (rex_addon::get('builder')->isAvailable()) {
    $builderAddonKey = 'builder';
}

if ($builderAddonKey === '') {
    return;
}

require_once rex_path::addon($builderAddonKey, 'lib/config/ThemeProviderBridge.php');

$registerForNames = static function (string $legacyName, callable $callable): void {
    $names = [$legacyName];
    if (str_starts_with($legacyName, 'BUILDER_')) {
        $names[] = 'BUILDER_' . substr($legacyName, strlen('BUILDER_'));
    }

    foreach (array_values(array_unique($names)) as $name) {
        rex_extension::register($name, $callable, rex_extension::EARLY);
    }
};

 $registerForNames(
    'BUILDER_ELEMENT_PATHS',
    static function (rex_extension_point $ep): array {
        $paths = (array) $ep->getSubject();
        $paths['klxm_elements'] = rex_path::addon('klxm_elements', 'elements/');
        return $paths;
    }
);

 $registerForNames(
    'BUILDER_ELEMENT_MODE',
    static function (rex_extension_point $ep): string {
        return AddonSettings::getElementMode();
    }
);

 $registerForNames(
    'BUILDER_MEDIA_TYPE_PRESETS',
    static function (rex_extension_point $ep): array {
        $presets = (array) $ep->getSubject();

        $klxmPresets = [
            'klxm_card_16_9' => [
                'ratio' => '16_9',
                'mode' => 'focuspoint',
                'widths' => [400, 800, 1200, 1600],
                'default_width' => 1200,
            ],
            'klxm_card_21_9' => [
                'ratio' => '21_9',
                'mode' => 'focuspoint',
                'widths' => [400, 800, 1200, 1600],
                'default_width' => 1200,
            ],
            'klxm_card_4_3' => [
                'ratio' => '4_3',
                'mode' => 'focuspoint',
                'widths' => [400, 800, 1200, 1600],
                'default_width' => 1200,
            ],
            'klxm_card_1_1' => [
                'ratio' => '1_1',
                'mode' => 'focuspoint',
                'widths' => [200, 400, 600, 800],
                'default_width' => 400,
            ],
            'klxm_card_3_2' => [
                'ratio' => '3_2',
                'mode' => 'focuspoint',
                'widths' => [400, 800, 1200, 1600],
                'default_width' => 1200,
            ],
            'klxm_card_3_4' => [
                'ratio' => '3_4',
                'mode' => 'focuspoint',
                'widths' => [400, 800, 1200, 1600],
                'default_width' => 1200,
            ],
            'klxm_card_original' => [
                'ratio' => 'original',
                'mode' => 'resize',
                'widths' => [400, 800, 1200, 1600],
                'default_width' => 1200,
            ],
            'klxm_content_full' => [
                'ratio' => 'original',
                'mode' => 'resize',
                'widths' => [768, 1200, 1600, 1920],
                'default_width' => 1920,
            ],
        ];

        return array_merge($presets, $klxmPresets);
    }
);
