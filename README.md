# KLXM Elements

Projekt-Elemente für den YForm Content Builder (UIkit-only).

Dieses Addon ergänzt den Haupt-Builder um **Projekt-Elemente** (externe, produktive Bausteine).

Theme-bezogene Optionen für UIkit-Elemente werden über Extension Points des Theme-Providers bezogen; das Addon selbst hält keine direkte Theme-Builder-Logik vor.

## 🧩 Rolle im Gesamtsystem

- **Core-Elemente** und **Starter-Elemente** liegen im Haupt-Addon `builder`.
- **Projekt-Elemente** liegen in externen Addons wie `klxm_elements`.
- Die **Modul-Erstellung bleibt zentral** im Haupt-Addon auf `index.php?page=builder/modules`.

## 🎨 Projekt-Elemente (im `klxm_elements`-Addon, UIkit-only)

| Element | Schlüssel | Beschreibung |
|---------|-----------|--------------|
| Text & Bild | `media_text` | Text-Bild-Kombinationen, 4 Layouts, TinyMCE |
| Cards Grid | `cards` | UIkit-Grid mit Repeater, Farb- und Layout-Auswahl |
| Slideshow | `slideshow` | Bild-/Video-Slideshow |
| Gallery | `gallery` | Grid & Masonry, Mixed Media (Bilder + Videos) |
| Hero Banner | `hero_banner` | Fullscreen-Banner mit Overlay und Call-to-Action |
| Feature Grid | `feature_grid` | Icon-Feature-Liste im Grid |
| Moving Tiles | `moving_tiles` | Parallax-Tiles mit alternierenden Layouts |
| Testimonial | `testimonial` | Zitate mit Autor und Bild |
| Timeline | `timeline` | Zeitstrahl-Element |
| Downloads | `downloads` | Dateiliste aus dem Mediapool |
| Countdown | `countdown` | Countdown bis zu einem Datum |
| Kontakt-Picker | `contact_picker` | Einzelne Kontakte aus YForm-Profilen auswählen |
| Kontaktformular | `doform2026` | PHPMailer-Formular mit Validierung und AJAX |

## 🔧 Hinweis

Die Modul-Erstellung bleibt zentral in:

- `builder` → Seite `modules`

Direktlink im Backend:

- `index.php?page=builder/modules`

## 🖼️ Media-Manager-Integration

`klxm_elements` nutzt das zentrale Typmodell des Hauptaddons:

- Kein Anlegen eigener statischer MM-Typen in `install.php`.
- Registrierung eigener Presets über `BUILDER_MEDIA_TYPE_PRESETS` in `boot.php`.
- Ausgabe in Templates über virtuelle Typen `cb_<preset>__<width>`.

Beispiel (Template):

```php
<?php
$type = \FriendsOfREDAXO\Builder\Config\MediaTypeRegistry::buildVirtualType('klxm_card_16_9', 1200);
echo rex_media_manager::getUrl($type, $image);
```

Beispiel (Preset-Registrierung im Addon):

```php
rex_extension::register(
	'BUILDER_MEDIA_TYPE_PRESETS',
	static function (rex_extension_point $ep): array {
		$presets = (array) $ep->getSubject();
		$presets['klxm_card_16_9'] = [
			'ratio' => '16_9',
			'mode' => 'focuspoint',
			'widths' => [400, 800, 1200, 1600],
			'default_width' => 1200,
		];

		return $presets;
	},
	rex_extension::EARLY
);
```

Hinweis:

- Wenn `media_negotiator` installiert ist, erfolgt die Format-Aushandlung automatisch im Hauptaddon als letzter Effekt.
