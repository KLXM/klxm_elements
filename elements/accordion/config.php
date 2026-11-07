<?php
/**
 * Accordion / Tabs Element - Erweiterte Konfiguration
 */

// Zentrale Konfigurationsklasse
$config = \KLXM\Elements\Config::class;
$hasThemeBuilder = $config::hasThemeBuilder();

return [
    'label' => 'Accordion / Tabs',
    'icon' => 'fa fa-list',
    'description' => 'Aufklappbare Inhaltsbereiche oder Tabs mit erweiterten Optionen',
    'version' => '1.14.0',
    'category' => 'content',
    'repeater' => true,
    'settings_modal' => [
        'label' => 'Layout-Einstellungen',
        'icon' => 'fa-cog',
        'fields' => $config::getSettingsModalFields(['style'])
    ],
    'fields' => array_merge(
        // Element-spezifische Felder
        [
        // Display Settings
        'display_type' => [
            'type' => 'choice',
            'label' => 'Darstellung',
            'choices' => [
                'accordion' => 'Accordion (aufklappbar)',
                'tabs' => 'Tabs (nebeneinander)',
                'tabs-left' => 'Tabs (links vertikal)',
            ],
            'default' => 'accordion',
        ],
        
        // Accordion Options
        'accordion_mode' => [
            'type' => 'choice',
            'label' => 'Verhalten beim Öffnen',
            'choices' => [
                'single' => 'Eins offen – öffnen schließt die anderen',
                'independent' => 'Jedes bleibt offen, bis es geschlossen wird',
            ],
            'default' => 'single',
            'notice' => 'Bisherige Einstellung „Mehrere öffnen“ entspricht „Jedes bleibt offen“.',
        ],
        'accordion_toggle_all' => [
            'type' => 'checkbox',
            'label' => 'Knopf „Alle aufklappen“',
            'notice' => 'Zeigt über dem Akkordeon „Alle aufklappen / Alle zuklappen“ (ab zwei Einträgen). Damit bleiben geöffnete Einträge offen, bis sie geschlossen werden.',
            'default' => true,
        ],
        'accordion_close_button' => [
            'type' => 'checkbox',
            'label' => 'Knopf „Zuklappen“ am Ende',
            'notice' => 'Zeigt am Ende jedes geöffneten Eintrags „Zuklappen“ – praktisch bei langen Inhalten.',
            'default' => true,
        ],
        'accordion_title_muted' => [
            'type' => 'checkbox',
            'label' => 'Titelbalken hinterlegt (muted)',
            'notice' => 'Gibt den Titelzeilen die dezente Hintergrundfarbe (uk-background-muted).',
            'default' => false,
        ],
        'accordion_gap_after' => [
            'type' => 'choice',
            'label' => 'Abstand danach',
            'choices' => [
                'none' => 'Keiner',
                'small' => 'Klein',
                'default' => 'Standard',
                'large' => 'Groß',
            ],
            'default' => 'default',
        ],
        'accordion_animation' => [
            'type' => 'choice',
            'label' => 'Animation',
            'choices' => [
                'true' => 'Standard (slide)',
                'false' => 'Keine',
            ],
            'default' => 'true',
        ],
        'first_open' => [
            'type' => 'checkbox',
            'label' => 'Erstes Element geöffnet',
            'default' => true,
        ],
        
        // Tab Options
        'tab_style' => [
            'type' => 'choice',
            'label' => 'Tab-Stil',
            'choices' => [
                'default' => 'Standard',
                'pill' => 'Pills (abgerundet)',
                'divider' => 'Mit Trennlinien',
            ],
            'default' => 'default',
        ],
        'tab_alignment' => [
            'type' => 'choice',
            'label' => 'Tab-Ausrichtung',
            'choices' => [
                'left' => 'Links',
                'center' => 'Zentriert',
                'right' => 'Rechts',
                'expand' => 'Volle Breite',
            ],
            'default' => 'left',
        ],
        
        // Style
        'style' => [
            'type' => 'choice',
            'label' => 'Stil',
            'choices' => [
                'default' => 'Standard',
                'primary' => 'Primary',
                'secondary' => 'Secondary',
                'muted' => 'Muted',
            ],
            'default' => 'default',
        ],
        
        // Items
        'items' => [
            'type' => 'repeater',
            'label' => 'Elemente',
            'fields' => [
                'title' => [
                    'type' => 'text',
                    'label' => 'Titel',
                ],
                'icon' => [
                    'type' => 'text',
                    'label' => 'Icon (optional)',
                    'notice' => 'UIkit-Icon: home, user, star, etc. | Font Awesome: fa-home',
                ],
                'content' => [
                    'type' => 'tinymce',
                    'profile' => 'default',
                    'label' => 'Inhalt',
                ],
                'image' => [
                    'type' => 'media',
                    'label' => 'Bild (optional)',
                    'notice' => 'Optionales Bild im Content-Bereich',
                ],
                'disabled' => [
                    'type' => 'checkbox',
                    'label' => 'Deaktiviert',
                    'notice' => 'Element wird ausgegraut dargestellt',
                ],
            ],
        ],
        ],
        
        // Section-Felder aus zentraler Config
        $config::getOptionalSectionFields()
    ),
];
