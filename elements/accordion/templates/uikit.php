<?php
/**
 * UIkit Template für Akkordeon/Tabs Element - Erweiterte Version
 * @var array $elementData
 */

$displayType = $elementData['display_type'] ?? 'accordion';
$style = $elementData['style'] ?? 'default';
$items = $elementData['items'] ?? [];
$uniqueId = uniqid('acc_');

// Accordion-Optionen
// Modus: „single“ = eins offen, „independent“ = jedes bleibt offen bis zum Schließen (früher Checkbox „Mehrere öffnen“)
$accordionMode = (string) ($elementData['accordion_mode'] ?? (!empty($elementData['accordion_multiple']) ? 'independent' : 'single'));
// „Alle aufklappen“: Standard an (auch für bestehende Inhalte ohne gespeicherten Wert), ab zwei aktiven Einträgen
$toggleAll = !array_key_exists('accordion_toggle_all', $elementData) || !empty($elementData['accordion_toggle_all']);
$activeItemCount = count(array_filter($items, static fn ($item) => empty($item['disabled'])));
$toggleAll = $toggleAll && $activeItemCount >= 2;
$titleMuted = !empty($elementData['accordion_title_muted']);
$closeButton = !array_key_exists('accordion_close_button', $elementData) || !empty($elementData['accordion_close_button']);
// „Alle aufklappen“ braucht Einträge, die offen bleiben
$multiple = 'independent' === $accordionMode || $toggleAll;
// Zuklappen ist bei UIkit ohnehin erlaubt; die alte Checkbox „Alle schließbar“ bleibt wirkungsgleich
$collapsible = true;
$animation = ($elementData['accordion_animation'] ?? 'true') === 'true';
$firstOpen = $elementData['first_open'] ?? true;

// Tab-Optionen
$tabStyle = $elementData['tab_style'] ?? 'default';
$tabAlignment = $elementData['tab_alignment'] ?? 'left';

// Sektion-Einstellungen
$sectionBg = $elementData['section_bg'] ?? '';
$sectionBgImage = $elementData['section_bg_image'] ?? '';
$sectionPadding = $elementData['section_padding'] ?? '';
$containerWidth = $elementData['container_width'] ?? 'uk-container';
$sectionLight = !empty($elementData['section_light']);
$enableSection = !array_key_exists('enable_section', $elementData) || !empty($elementData['enable_section']);
$enableContainer = !array_key_exists('enable_container', $elementData) || !empty($elementData['enable_container']);

if (empty($items)) {
    return;
}

// Style-Mapping für UIkit
$styleMap = [
    'default' => 'uk-card-default',
    'primary' => 'uk-card-primary',
    'secondary' => 'uk-card-secondary',
    'muted' => 'uk-card-muted',
];
$styleClass = $styleMap[$style] ?? '';

// Tab-Style Klassen
$tabStyleClasses = ['uk-subnav'];
switch ($tabStyle) {
    case 'pill':
        $tabStyleClasses[] = 'uk-subnav-pill';
        break;
    case 'divider':
        $tabStyleClasses[] = 'uk-subnav-divider';
        break;
    default:
        $tabStyleClasses[] = 'uk-subnav-default';
        break;
}

// Tab-Alignment Klassen
switch ($tabAlignment) {
    case 'center':
        $tabStyleClasses[] = 'uk-flex-center';
        break;
    case 'right':
        $tabStyleClasses[] = 'uk-flex-right';
        break;
    case 'expand':
        $tabStyleClasses = ['uk-tab', 'uk-child-width-expand'];
        break;
}

// Accordion uk-accordion Attribute
$accordionAttrs = [];
if (!$collapsible) {
    $accordionAttrs[] = 'collapsible: false';
}
if ($multiple) {
    $accordionAttrs[] = 'multiple: true';
}
if (!$animation) {
    $accordionAttrs[] = 'animation: false';
}
$accordionAttrStr = empty($accordionAttrs) ? '' : implode('; ', $accordionAttrs);

$wrapper = new rex_fragment();
$wrapper->setVar('enable_section', $enableSection, false);
$wrapper->setVar('enable_container', $enableContainer, false);
$wrapper->setVar('section_bg', $sectionBg, false);
$wrapper->setVar('section_bg_image', $sectionBgImage, false);
$wrapper->setVar('section_padding', $sectionPadding, false);
$wrapper->setVar('container_width', $containerWidth, false);
$wrapper->setVar('section_light', $sectionLight, false);

$wrapperClose = new rex_fragment();
$wrapperClose->setVar('mode', 'close', false);
$wrapperClose->setVar('enable_section', $enableSection, false);
$wrapperClose->setVar('enable_container', $enableContainer, false);
$wrapperClose->setVar('section_bg_image', $sectionBgImage, false);
$wrapperClose->setVar('container_width', $containerWidth, false);

// Icon Rendering Helper
$renderIcon = function($icon) {
    if (empty($icon)) {
        return '';
    }
    // Font Awesome
    if (strpos($icon, 'fa-') !== false) {
        return '<i class="fa ' . rex_escape($icon) . ' uk-margin-small-right"></i>';
    }
    // UIkit Icon
    $iconName = preg_replace('/^(uk-icon-|icon-)/', '', $icon);
    return '<span uk-icon="icon: ' . rex_escape($iconName) . '" class="uk-margin-small-right"></span>';
};
?>

<?= $wrapper->parse('klxm_elements/wrapper.php') ?>

<?php if ($displayType === 'tabs'): ?>
    <!-- TABS (Horizontal) -->
    <div class="tabs-element" uk-scrollspy="cls: uk-animation-fade">
        <ul class="<?= implode(' ', $tabStyleClasses) ?>" uk-switcher="animation: uk-animation-fade">
            <?php foreach ($items as $index => $item): ?>
                <?php $isDisabled = !empty($item['disabled']); ?>
                <li<?= $index === 0 ? ' class="uk-active"' : '' ?><?= $isDisabled ? ' class="uk-disabled"' : '' ?>>
                    <a href="#">
                        <?= $renderIcon($item['icon'] ?? '') ?>
                        <?= rex_escape($item['title'] ?? 'Tab ' . ($index + 1)) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
        <ul class="uk-switcher uk-margin">
            <?php foreach ($items as $index => $item): ?>
                <li>
                    <div class="uk-card uk-card-body <?= $styleClass ?>">
                        <?php if (!empty($item['image'])): ?>
                            <?php $resolvedImageAlt = \FriendsOfREDAXO\Builder\MediaAltResolver::resolve((string) $item['image'], '', (string) ($item['title'] ?? '')); ?>
                            <?php $contentCardType = \FriendsOfREDAXO\Builder\Config\MediaTypeRegistry::buildVirtualType('klxm_card_16_9', 1200); ?>
                            <img src="<?= rex_media_manager::getUrl($contentCardType, $item['image']) ?>" 
                                 alt="<?= rex_escape($resolvedImageAlt) ?>" 
                                 class="uk-margin-bottom" loading="lazy">
                        <?php endif; ?>
                        <?= $item['content'] ?? '' ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

<?php elseif ($displayType === 'tabs-left'): ?>
    <!-- TABS (Vertikal links) -->
    <div class="tabs-element-left" uk-scrollspy="cls: uk-animation-fade">
        <div class="uk-grid-small uk-child-width-expand@s" uk-grid>
            <div class="uk-width-auto@m">
                <ul class="uk-tab-left" uk-tab="connect: #<?= $uniqueId ?>-tab-content; animation: uk-animation-fade">
                    <?php foreach ($items as $index => $item): ?>
                        <?php $isDisabled = !empty($item['disabled']); ?>
                        <li<?= $index === 0 ? ' class="uk-active"' : '' ?><?= $isDisabled ? ' class="uk-disabled"' : '' ?>>
                            <a href="#">
                                <?= $renderIcon($item['icon'] ?? '') ?>
                                <?= rex_escape($item['title'] ?? 'Tab ' . ($index + 1)) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="uk-width-expand@m">
                <ul id="<?= $uniqueId ?>-tab-content" class="uk-switcher">
                    <?php foreach ($items as $index => $item): ?>
                        <li>
                            <div class="uk-card uk-card-body <?= $styleClass ?>">
                                <?php if (!empty($item['image'])): ?>
                                    <?php $resolvedImageAlt = \FriendsOfREDAXO\Builder\MediaAltResolver::resolve((string) $item['image'], '', (string) ($item['title'] ?? '')); ?>
                                    <?php $contentCardType = \FriendsOfREDAXO\Builder\Config\MediaTypeRegistry::buildVirtualType('klxm_card_16_9', 1200); ?>
                                    <img src="<?= rex_media_manager::getUrl($contentCardType, $item['image']) ?>" 
                                         alt="<?= rex_escape($resolvedImageAlt) ?>" 
                                         class="uk-margin-bottom" loading="lazy">
                                <?php endif; ?>
                                <?= $item['content'] ?? '' ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

<?php else: ?>
    <!-- ACCORDION -->
    <div class="accordion-element<?= $titleMuted ? ' accordion-element--muted' : '' ?>" uk-scrollspy="cls: uk-animation-fade">
        <?php if ($toggleAll): ?>
            <div class="accordion-element__toolbar uk-flex uk-flex-right uk-margin-small-bottom">
                <button type="button" class="uk-button uk-button-link uk-button-small accordion-element__toggle-all" data-klxm-accordion-toggle="<?= $uniqueId ?>" aria-controls="<?= $uniqueId ?>" aria-expanded="false" data-label-open="Alle aufklappen" data-label-close="Alle zuklappen">
                    <span uk-icon="icon: chevron-down; ratio: 0.9" aria-hidden="true"></span> <span class="accordion-element__toggle-label">Alle aufklappen</span>
                </button>
            </div>
        <?php endif; ?>
        <ul id="<?= $uniqueId ?>" uk-accordion<?= $accordionAttrStr ? '="' . $accordionAttrStr . '"' : '' ?>>
            <?php foreach ($items as $index => $item): ?>
                <?php 
                $title = $item['title'] ?? '';
                $content = $item['content'] ?? '';
                $isDisabled = !empty($item['disabled']);
                $openClass = ($firstOpen && $index === 0) ? ' uk-open' : '';
                $itemClasses = [$styleClass];
                if ($isDisabled) {
                    $itemClasses[] = 'uk-disabled';
                }
                ?>
                <li class="<?= implode(' ', array_filter($itemClasses)) ?><?= $openClass ?>">
                    <a class="uk-accordion-title<?= $titleMuted ? ' uk-background-muted' : '' ?><?= $isDisabled ? ' uk-text-muted' : '' ?>" href="#">
                        <?= $renderIcon($item['icon'] ?? '') ?>
                        <?= rex_escape($title) ?>
                    </a>
                    <div class="uk-accordion-content">
                      <div class="accordion-element__body">
                        <?php if (!empty($item['image'])): ?>
                            <?php $resolvedImageAlt = \FriendsOfREDAXO\Builder\MediaAltResolver::resolve((string) $item['image'], '', (string) $title); ?>
                            <?php $contentCardType = \FriendsOfREDAXO\Builder\Config\MediaTypeRegistry::buildVirtualType('klxm_card_16_9', 1200); ?>
                            <img src="<?= rex_media_manager::getUrl($contentCardType, $item['image']) ?>" 
                                 alt="<?= rex_escape($resolvedImageAlt) ?>" 
                                 class="uk-margin-bottom" loading="lazy">
                        <?php endif; ?>
                        <?= $content ?>
                        <?php if ($closeButton): ?>
                            <div class="accordion-element__close">
                                <button type="button" class="uk-button uk-button-link uk-button-small" data-klxm-accordion-close>
                                    <span uk-icon="icon: chevron-up; ratio: 0.9" aria-hidden="true"></span> Zuklappen<span class="uk-hidden-visually">: <?= rex_escape($title) ?></span>
                                </button>
                            </div>
                        <?php endif; ?>
                      </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if ($displayType === 'accordion' && !rex::getProperty('klxm_accordion_css')): ?>
    <?php rex::setProperty('klxm_accordion_css', true); ?>
    <style>
    /* Titelzeilen mit Luft; Werte per CSS-Variable im Projekt überschreibbar */
    .accordion-element {
        --klxm-accordion-title-py: 1rem;
        --klxm-accordion-title-px: 1.25rem;
        --klxm-accordion-content-pb: 1.5rem;
    }
    .accordion-element .uk-accordion-title {
        padding: var(--klxm-accordion-title-py) var(--klxm-accordion-title-px);
        line-height: 1.4;
    }
    .accordion-element--muted .accordion-element__body {
        padding-top: var(--klxm-accordion-content-pb);
    }
    .accordion-element .uk-accordion-content {
        margin-top: 0;
    }
    .accordion-element__body {
        padding: 0.25rem var(--klxm-accordion-title-px) var(--klxm-accordion-content-pb);
    }
    .accordion-element .uk-accordion-title {
        scroll-margin-top: 6rem;
    }
    .accordion-element__close {
        margin-top: 1rem;
        text-align: right;
    }
    .accordion-element__body > :last-child {
        margin-bottom: 0;
    }
    </style>
<?php endif; ?>

<?php if ($displayType === 'accordion' && ($toggleAll || $closeButton) && !rex::getProperty('klxm_accordion_toggle_js')): ?>
    <?php rex::setProperty('klxm_accordion_toggle_js', true); ?>
    <script>
    /* Akkordeon: „Alle aufklappen / Alle zuklappen“ (einmal je Seite) */
    (function () {
        function sync(button) {
            var list = document.getElementById(button.getAttribute('data-klxm-accordion-toggle'));
            if (!list) { return; }
            var items = Array.prototype.filter.call(list.children, function (li) { return !li.classList.contains('uk-disabled'); });
            var allOpen = items.length > 0 && items.every(function (li) { return li.classList.contains('uk-open'); });
            button.setAttribute('aria-expanded', allOpen ? 'true' : 'false');
            var label = button.querySelector('.accordion-element__toggle-label');
            if (label) { label.textContent = button.getAttribute(allOpen ? 'data-label-close' : 'data-label-open'); }
            var icon = button.querySelector('[uk-icon]');
            if (icon) { icon.style.transform = allOpen ? 'rotate(180deg)' : ''; }
        }
        // „Zuklappen“ am Ende eines Eintrags
        document.addEventListener('click', function (event) {
            var button = event.target.closest ? event.target.closest('[data-klxm-accordion-close]') : null;
            if (!button || typeof UIkit === 'undefined') { return; }
            var li = button.closest('li');
            var list = li ? li.parentElement : null;
            if (!list) { return; }
            var title = li.querySelector('.uk-accordion-title');
            UIkit.accordion(list).toggle(Array.prototype.indexOf.call(list.children, li), true);
            if (title) {
                title.focus({ preventScroll: true });
                if (title.getBoundingClientRect().top < 0) { title.scrollIntoView({ block: 'start', behavior: 'smooth' }); }
            }
        });
        document.addEventListener('click', function (event) {
            var button = event.target.closest ? event.target.closest('[data-klxm-accordion-toggle]') : null;
            if (!button) { return; }
            var list = document.getElementById(button.getAttribute('data-klxm-accordion-toggle'));
            if (!list || typeof UIkit === 'undefined') { return; }
            var open = button.getAttribute('aria-expanded') !== 'true';
            var accordion = UIkit.accordion(list);
            Array.prototype.forEach.call(list.children, function (li, index) {
                if (li.classList.contains('uk-disabled') || li.classList.contains('uk-open') === open) { return; }
                accordion.toggle(index, true);
            });
            window.setTimeout(function () { sync(button); }, 50);
        });
        // Einzelne Einträge auf/zu: Knopf-Beschriftung nachführen
        ['shown', 'hidden'].forEach(function (type) {
            document.addEventListener(type, function (event) {
                var list = event.target.closest ? event.target.closest('[uk-accordion]') : null;
                if (!list || !list.id) { return; }
                var button = document.querySelector('[data-klxm-accordion-toggle="' + list.id + '"]');
                if (button) { sync(button); }
            });
        });
        var syncAll = function () { document.querySelectorAll('[data-klxm-accordion-toggle]').forEach(sync); };
        if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', syncAll); } else { syncAll(); }
    })();
    </script>
<?php endif; ?>

<?= $wrapperClose->parse('klxm_elements/wrapper.php') ?>
