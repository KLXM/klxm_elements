<?php

/**
 * Bild & Text Element - UIkit Template
 *
 * @var array $elementData
 */

// --- Inhalt ---
$badge      = $elementData['badge'] ?? '';
$heading    = $elementData['heading'] ?? '';
$tag        = $elementData['tag'] ?? 'h2';
$subheading = $elementData['subheading'] ?? '';
$text       = $elementData['text'] ?? '';

// --- Bild ---
$image      = $elementData['image'] ?? '';
$imageAlt   = $elementData['image_alt'] ?? '';
$imageRatio = $elementData['image_ratio'] ?? '';

// --- Design ---
$mediaPosition = $elementData['media_position'] ?? 'left';
$imageWidth    = $elementData['image_width'] ?? '1-2';
$verticalAlign = $elementData['vertical_align'] ?? 'middle';
$imageRounded  = !empty($elementData['image_rounded']);
$imageShadow   = $elementData['image_shadow'] ?? '';
$imageStyle    = $elementData['image_style'] ?? '';

// --- Link ---
$linkType     = $elementData['link_type'] ?? '';
$linkUrl      = $elementData['link_url'] ?? '';
$linkInternal = $elementData['link_internal'] ?? '';
$linkText     = $elementData['link_text'] ?? 'Mehr erfahren';
$linkStyle    = $elementData['link_style'] ?? 'uk-button-default';

// --- Sektion ---
$sectionBg    = $elementData['section_bg'] ?? '';
$sectionBgImg = $elementData['section_bg_image'] ?? '';
$sectionPad   = $elementData['section_padding'] ?? '';
$container    = $elementData['container_width'] ?? 'uk-container';
$sectionLight = !empty($elementData['section_light']);
$enableSection = !array_key_exists('enable_section', $elementData) || !empty($elementData['enable_section']);
$enableContainer = !array_key_exists('enable_container', $elementData) || !empty($elementData['enable_container']);

// Abbruch wenn kein Inhalt
if (empty($image) && empty($heading) && empty($text)) {
    return;
}

// Link-URL ermitteln
$finalLink = '';
if ($linkType === 'external' && $linkUrl) {
    $finalLink = $linkUrl;
} elseif ($linkType === 'internal' && $linkInternal) {
    $finalLink = rex_getUrl((int) $linkInternal);
}

// Textbreite aus Bildbreite ableiten
$textWidthMap = [
    '1-3' => '2-3',
    '2-5' => '3-5',
    '1-2' => '1-2',
    '3-5' => '2-5',
    '2-3' => '1-3',
];
$imageWidthClass = 'uk-width-' . $imageWidth . '@m';
$textWidthClass  = 'uk-width-' . ($textWidthMap[$imageWidth] ?? '1-2') . '@m';

// Vertikale Ausrichtung
$verticalClass = match ($verticalAlign) {
    'middle' => 'uk-flex-middle',
    'bottom' => 'uk-flex-bottom',
    default  => '',
};

// Bild-Klassen
$imgClasses = ['uk-width-1-1'];
if ($imageRounded) {
    $imgClasses[] = 'uk-border-rounded';
}
if ($imageShadow) {
    $imgClasses[] = 'uk-box-shadow-' . $imageShadow;
}

$ratioToPreset = static function (string $ratio): string {
    $map = [
        '16_9' => 'klxm_card_16_9',
        '21_9' => 'klxm_card_21_9',
        '4_3' => 'klxm_card_4_3',
        '1_1' => 'klxm_card_1_1',
        '3_2' => 'klxm_card_3_2',
        '3_4' => 'klxm_card_3_4',
        'original' => 'klxm_card_original',
        '' => 'klxm_card_original',
    ];

    return $map[$ratio] ?? 'klxm_card_16_9';
};

// Bild-URL über Media Manager
$imageUrl = '';
if ($image) {
    $preset = $ratioToPreset((string) $imageRatio);
    $type = \FriendsOfREDAXO\Builder\Config\MediaTypeRegistry::buildVirtualType($preset, 1200);
    $imageUrl = rex_media_manager::getUrl($type, $image);
}

$resolvedImageAlt = \FriendsOfREDAXO\Builder\MediaAltResolver::resolve((string) $image, (string) $imageAlt, (string) $heading);

// Srcset für responsive Bilder
$srcset = '';
if ($image) {
    $sizes = [400, 800, 1200, 1600];
    $parts = [];
    $preset = $ratioToPreset((string) $imageRatio);
    foreach ($sizes as $w) {
        $type = \FriendsOfREDAXO\Builder\Config\MediaTypeRegistry::buildVirtualType($preset, $w);
        $parts[] = rex_media_manager::getUrl($type, $image) . ' ' . $w . 'w';
    }
    $srcset = implode(', ', $parts);
}

$estimateContainerMaxPx = static function (string $containerWidth): int {
    if (str_contains($containerWidth, 'xsmall')) {
        return 640;
    }
    if (str_contains($containerWidth, 'small')) {
        return 900;
    }
    if (str_contains($containerWidth, 'xlarge')) {
        return 1600;
    }
    if (str_contains($containerWidth, 'large')) {
        return 1400;
    }
    if (str_contains($containerWidth, 'expand') || $containerWidth === '') {
        return 1920;
    }

    return 1200;
};

$estimateMediaFraction = static function (string $width): float {
    if (preg_match('/^(\d+)-(\d+)$/', $width, $m) === 1) {
        $a = (int) $m[1];
        $b = (int) $m[2];
        if ($a > 0 && $b > 0) {
            return min(1.0, max(0.2, $a / $b));
        }
    }

    return 0.5;
};

$containerMaxPx = $estimateContainerMaxPx((string) $container);
$mediaFraction = $estimateMediaFraction((string) $imageWidth);
$desktopImagePx = (int) max(220, round($containerMaxPx * $mediaFraction));
$tabletImageVw = (int) max(35, round(100 * min(1.0, max(0.3, $mediaFraction))));
$mobileImageVw = 100;

// Sektion aufbauen
$sectionClasses = [];
if ($sectionBg) {
    $sectionClasses[] = $sectionBg;
}
if ($sectionPad) {
    $sectionClasses[] = $sectionPad;
}
if ($sectionLight) {
    $sectionClasses[] = 'uk-light';
}

$sectionStyle = '';
if ($sectionBgImg) {
    $ext = strtolower(pathinfo($sectionBgImg, PATHINFO_EXTENSION));
    if (!in_array($ext, ['mp4', 'webm', 'ogg'], true)) {
        $bgUrl = rex_media_manager::getUrl('content_slideshow', $sectionBgImg);
        $sectionStyle = ' style="background-image: url(\'' . $bgUrl . '\'); background-size: cover; background-position: center;"';
    }
}

$wrapper = new rex_fragment();
$wrapper->setVar('enable_section', $enableSection, false);
$wrapper->setVar('enable_container', $enableContainer, false);
$wrapper->setVar('section_bg', $sectionBg, false);
$wrapper->setVar('section_bg_image', $sectionBgImg, false);
$wrapper->setVar('section_padding', $sectionPad, false);
$wrapper->setVar('container_width', $container, false);
$wrapper->setVar('section_light', $sectionLight, false);

$wrapperClose = new rex_fragment();
$wrapperClose->setVar('mode', 'close', false);
$wrapperClose->setVar('enable_section', $enableSection, false);
$wrapperClose->setVar('enable_container', $enableContainer, false);
$wrapperClose->setVar('section_bg_image', $sectionBgImg, false);
$wrapperClose->setVar('container_width', $container, false);

?>
<?= $wrapper->parse('klxm_elements/wrapper.php') ?>
    <div class="uk-grid uk-grid-large <?= $verticalClass ?>" uk-grid>

        <?php
        // Bild-Element (als eigenes Fragment)
        $mediaBlock = static function () use ($imageUrl, $srcset, $resolvedImageAlt, $imgClasses, $image, $imageStyle, $desktopImagePx, $tabletImageVw, $mobileImageVw): void {
            if (empty($image) || empty($imageUrl)) {
                return;
            }

            $wrapClass = 'uk-margin-remove';
            if ($imageStyle === 'stacked') {
                $wrapClass .= ' cb-image-stack';
            } elseif ($imageStyle === 'overlap') {
                $wrapClass .= ' cb-image-overlap';
            }
            ?>
            <figure class="<?= $wrapClass ?>">
                <img
                    src="<?= rex_escape($imageUrl) ?>"
                    <?= $srcset ? 'srcset="' . rex_escape($srcset) . '"' : '' ?>
                    sizes="(min-width: 1200px) <?= $desktopImagePx ?>px, (min-width: 640px) <?= $tabletImageVw ?>vw, <?= $mobileImageVw ?>vw"
                    alt="<?= rex_escape($resolvedImageAlt) ?>"
                    class="<?= implode(' ', $imgClasses) ?>"
                    loading="lazy"
                >
            </figure>
            <?php
        };
        ?>

        <?php if ($mediaPosition === 'left'): ?>
            <div class="<?= $imageWidthClass ?>">
                <?php $mediaBlock(); ?>
            </div>
        <?php endif; ?>

        <div class="<?= $textWidthClass ?>">

            <?php if ($badge): ?>
                <span class="uk-badge uk-margin-small-bottom"><?= rex_escape($badge) ?></span>
            <?php endif; ?>

            <?php if ($heading): ?>
                <<?= $tag ?> class="uk-margin-small-top uk-margin-small-bottom">
                    <?= rex_escape($heading) ?>
                </<?= $tag ?>>
            <?php endif; ?>

            <?php if ($subheading): ?>
                <p class="uk-text-lead uk-margin-small-bottom">
                    <?= rex_escape($subheading) ?>
                </p>
            <?php endif; ?>

            <?php if ($text): ?>
                <div class="uk-margin"><?= $text ?></div>
            <?php endif; ?>

            <?php if ($finalLink && $linkText): ?>
                <div class="uk-margin-top">
                    <a href="<?= rex_escape($finalLink) ?>" class="uk-button <?= rex_escape($linkStyle) ?>">
                        <?= rex_escape($linkText) ?>
                        <?php if ($linkStyle === 'uk-button-text'): ?>
                            <span uk-icon="chevron-right"></span>
                        <?php endif; ?>
                    </a>
                </div>
            <?php endif; ?>

        </div>

        <?php if ($mediaPosition === 'right'): ?>
            <div class="<?= $imageWidthClass ?>">
                <?php $mediaBlock(); ?>
            </div>
        <?php endif; ?>

    </div>
<?= $wrapperClose->parse('klxm_elements/wrapper.php') ?>

<?php if ($imageStyle === 'stacked' || $imageStyle === 'overlap'): ?>
<style>
/* --- Bildstapel / Overlap Effekte --- */
.cb-image-stack {
    position: relative;
    padding: 24px 24px 0 0;
    overflow: visible;
}
.cb-image-stack img {
    position: relative;
    z-index: 2;
    display: block;
}
.cb-image-stack::before {
    content: '';
    position: absolute;
    inset: auto 0 -16px -16px;
    width: 75%;
    height: 75%;
    background: var(--uk-color-primary, #1e87f0);
    opacity: 0.12;
    border-radius: 4px;
    z-index: 1;
}
.cb-image-stack::after {
    content: '';
    position: absolute;
    inset: 0 -16px -16px auto;
    width: 60%;
    height: 60%;
    background: var(--uk-color-primary, #1e87f0);
    opacity: 0.06;
    border-radius: 4px;
    z-index: 0;
}

.cb-image-overlap {
    overflow: visible;
    position: relative;
    z-index: 2;
}
@media (min-width: 960px) {
    .cb-image-overlap {
        margin-inline-end: -60px;
    }
    .cb-image-overlap img {
        filter: drop-shadow(0 8px 24px rgba(0,0,0,.18));
    }
}

/* Dark Mode kompatibel */
body.rex-theme-dark .cb-image-stack::before,
body.rex-theme-dark .cb-image-stack::after {
    opacity: 0.08;
}
@media (prefers-color-scheme: dark) {
    body.rex-has-theme:not(.rex-theme-light) .cb-image-stack::before,
    body.rex-has-theme:not(.rex-theme-light) .cb-image-stack::after {
        opacity: 0.08;
    }
}
</style>
<?php endif; ?>
