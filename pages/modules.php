<?php

$addon = rex_addon::get('klxm_elements');
$elementsDir = rex_path::addon('klxm_elements', 'elements');
$elements = [];

if (is_dir($elementsDir)) {
    $dirs = scandir($elementsDir);
    if (is_array($dirs)) {
        foreach ($dirs as $dir) {
            if ($dir === '.' || $dir === '..') {
                continue;
            }

            $configPath = $elementsDir . '/' . $dir . '/config.php';
            if (!file_exists($configPath)) {
                continue;
            }

            $config = include $configPath;
            if (!is_array($config)) {
                continue;
            }

            $label = isset($config['label']) ? (string) $config['label'] : ucfirst($dir);
            $elements[$dir] = $label;
        }
    }
}

asort($elements);

$csrf = rex_csrf_token::factory('klxm_elements_modules');

$generateInputCode = static function (string $elementKey, string $framework, int $valueId): string {
    $allowedElementsCode = var_export([$elementKey], true);

    return <<<PHP
<?php
use KLXM\YFormContentBuilder\Module;

\$builder = Module::createWithValue({$valueId}, null, [
    'framework' => '{$framework}',
    'label' => '',
    'description' => '',
    'allowed_elements' => {$allowedElementsCode},
]);

echo \$builder->getEditor();
?>
PHP;
};

$generateOutputCode = static function (string $elementKey, string $framework, int $valueId): string {
    $allowedElementsCode = var_export([$elementKey], true);

    return <<<PHP
<?php
use KLXM\YFormContentBuilder\Module;

\$rawValue = 'REX_VALUE[id={$valueId} output=html]';
try {
    \$slice = \$this->getCurrentSlice();
    if (\$slice) {
        \$rawValue = (string) \$slice->getValue({$valueId});
    }
} catch (rex_exception \$exception) {
}

\$builder = Module::createWithValue({$valueId}, \$rawValue, [
    'framework' => '{$framework}',
    'allowed_elements' => {$allowedElementsCode},
]);

echo \$builder->renderOutput();
?>
PHP;
};

if (rex_post('klxm_create_modules', 'bool')) {
    if (!$csrf->isValid()) {
        echo rex_view::error('Ungültiger CSRF-Token. Bitte erneut versuchen.');
        return;
    }

    $framework = rex_post('framework', 'string', 'uikit');
    $valueId = rex_post('value_id', 'int', 1);
    $selectedElements = rex_post('elements', 'array', []);

    if ($valueId < 1 || $valueId > 20) {
        $valueId = 1;
    }

    $allowedFrameworks = ['uikit'];
    if (!in_array($framework, $allowedFrameworks, true)) {
        $framework = 'uikit';
    }

    $createdModules = [];

    foreach ($selectedElements as $elementKey) {
        $elementKey = trim((string) $elementKey);
        if ($elementKey === '' || !isset($elements[$elementKey])) {
            continue;
        }

        $moduleKey = 'yfcb_' . $elementKey;
        $moduleName = 'KLXM: ' . $elements[$elementKey];

        $inputCode = $generateInputCode($elementKey, $framework, $valueId);
        $outputCode = $generateOutputCode($elementKey, $framework, $valueId);

        $existsSql = rex_sql::factory();
        $existsSql->setQuery('SELECT id FROM ' . rex::getTable('module') . ' WHERE `key` = :key', [':key' => $moduleKey]);

        if ($existsSql->getRows() > 0) {
            $updateSql = rex_sql::factory();
            $updateSql->setQuery(
                'UPDATE ' . rex::getTable('module') . ' SET `name` = :name, `input` = :input, `output` = :output WHERE `key` = :key',
                [
                    ':name' => $moduleName,
                    ':input' => $inputCode,
                    ':output' => $outputCode,
                    ':key' => $moduleKey,
                ]
            );
            $createdModules[] = $moduleName . ' (aktualisiert)';
        } else {
            $insertSql = rex_sql::factory();
            $insertSql->setQuery(
                'INSERT INTO ' . rex::getTable('module') . ' (`key`, `name`, `input`, `output`) VALUES (:key, :name, :input, :output)',
                [
                    ':key' => $moduleKey,
                    ':name' => $moduleName,
                    ':input' => $inputCode,
                    ':output' => $outputCode,
                ]
            );
            $createdModules[] = $moduleName . ' (neu erstellt)';
        }
    }

    if ($createdModules !== []) {
        $message = '<ul>';
        foreach ($createdModules as $module) {
            $message .= '<li>' . rex_escape($module) . '</li>';
        }
        $message .= '</ul>';
        echo rex_view::success('Module erstellt/aktualisiert: ' . $message);
    } else {
        echo rex_view::warning('Keine Elemente ausgewählt.');
    }
}

$content = '';
$content .= '<form method="post" action="' . rex_url::currentBackendPage() . '">';
$content .= $csrf->getHiddenField();
$content .= '<input type="hidden" name="klxm_create_modules" value="1">';

$content .= '<div class="row">';
$content .= '<div class="col-md-4">';
$content .= '<div class="form-group">';
$content .= '<label>Framework</label>';
$content .= '<select class="form-control" name="framework">';
$content .= '<option value="uikit" selected>UIkit</option>';
$content .= '</select>';
$content .= '</div>';
$content .= '</div>';

$content .= '<div class="col-md-4">';
$content .= '<div class="form-group">';
$content .= '<label>REX_VALUE Feld</label>';
$content .= '<input class="form-control" type="number" min="1" max="20" name="value_id" value="1">';
$content .= '</div>';
$content .= '</div>';
$content .= '</div>';

if ($elements === []) {
    $content .= '<div class="alert alert-warning">Keine KLXM-Elemente gefunden.</div>';
} else {
    $content .= '<h4>Elemente</h4>';
    $content .= '<div class="checkbox">';
    foreach ($elements as $key => $label) {
        $content .= '<label style="display:block;margin-bottom:6px;">';
        $content .= '<input type="checkbox" name="elements[]" value="' . rex_escape($key) . '" checked> ';
        $content .= rex_escape($label) . ' <small class="text-muted">(' . rex_escape($key) . ')</small>';
        $content .= '</label>';
    }
    $content .= '</div>';

    $content .= '<button class="btn btn-primary" type="submit">Module erstellen/aktualisieren</button>';
}

$content .= '</form>';

$fragment = new rex_fragment();
$fragment->setVar('title', 'KLXM Modul-Installation', false);
$fragment->setVar('body', $content, false);
echo $fragment->parse('core/page/section.php');
