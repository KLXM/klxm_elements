<?php

use KLXM\Elements\AddonSettings;

if (!rex::getUser() || !rex::getUser()->isAdmin()) {
    echo rex_view::error('Nur Administratoren dürfen diese Seite bearbeiten.');
    return;
}

$csrfToken = rex_csrf_token::factory('klxm_elements_settings');

if ('save' === rex_request('func', 'string') && $csrfToken->isValid()) {
    $elementMode = rex_request('element_mode', 'string', 'replace');
    AddonSettings::setElementMode($elementMode);
    echo rex_view::success('Einstellungen wurden gespeichert.');
} elseif ('save' === rex_request('func', 'string')) {
    echo rex_view::error('CSRF-Token ist ungültig. Bitte Seite neu laden.');
}

$currentElementMode = AddonSettings::getElementMode();

$content = '';
$content .= '<form action="' . rex_url::currentBackendPage() . '" method="post">';
$content .= $csrfToken->getHiddenField();
$content .= '<input type="hidden" name="func" value="save">';

$content .= '<fieldset>';
$content .= '<legend>Element-Auswahl</legend>';
$content .= '<div class="form-group">';
$content .= '<label class="control-label" for="klxm-element-mode">Content-Builder-Modus</label>';
$content .= '<select class="form-control" id="klxm-element-mode" name="element_mode">';
$content .= '<option value="replace"' . ('replace' === $currentElementMode ? ' selected' : '') . '>Replace (nur KLXM-Elemente)</option>';
$content .= '<option value="merge"' . ('merge' === $currentElementMode ? ' selected' : '') . '>Merge (KLXM-Elemente + Demo-Elemente)</option>';
$content .= '</select>';
$content .= '<p class="help-block">Steuert, ob zusätzlich die Demo-Elemente aus yform_content_builder angeboten werden.</p>';
$content .= '</div>';
$content .= '</fieldset>';

$content .= '<p><button class="btn btn-save" type="submit">Speichern</button></p>';
$content .= '</form>';

$fragment = new rex_fragment();
$fragment->setVar('title', 'KLXM Elements Einstellungen', false);
$fragment->setVar('body', $content, false);
echo $fragment->parse('core/page/section.php');
