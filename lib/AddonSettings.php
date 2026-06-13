<?php

declare(strict_types=1);

namespace KLXM\Elements;

final class AddonSettings
{
    private const KEY_ELEMENT_MODE = 'element_mode';

    public static function getElementMode(): string
    {
        $mode = (string) \rex_addon::get('klxm_elements')->getConfig(self::KEY_ELEMENT_MODE, 'replace');

        return $mode === 'merge' ? 'merge' : 'replace';
    }

    public static function setElementMode(string $mode): void
    {
        \rex_addon::get('klxm_elements')->setConfig(self::KEY_ELEMENT_MODE, $mode === 'merge' ? 'merge' : 'replace');
    }
}
