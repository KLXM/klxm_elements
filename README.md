# KLXM Elements

Projekt-Elemente für den YForm Content Builder (UIkit-only).

Dieses Addon ergänzt den Haupt-Builder um **Projekt-Elemente** (externe, produktive Bausteine).

Theme-bezogene Optionen für UIkit-Elemente werden über Extension Points des Theme-Providers bezogen; das Addon selbst hält keine direkte Theme-Builder-Logik vor.

## 🧩 Rolle im Gesamtsystem

- **Core-Elemente** und **Starter-Elemente** liegen im Haupt-Addon `yform_content_builder`.
- **Projekt-Elemente** liegen in externen Addons wie `klxm_elements`.
- Die **Modul-Erstellung bleibt zentral** im Haupt-Addon auf `index.php?page=yform_content_builder/modules`.

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

- `yform_content_builder` → Seite `modules`

Direktlink im Backend:

- `index.php?page=yform_content_builder/modules`
