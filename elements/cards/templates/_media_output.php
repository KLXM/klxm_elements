<?php
/**
 * Media Output Helper für Cards
 * Variablen werden vom Parent-Template bereitgestellt:
 * $image, $imageSrc, $imageAlt, $imageTitle, $mediaPoolTitle, $mediaLightbox, $mediaCover, $isVideo, $isImage, $title
 * $videoDisplay (inline|poster), $videoControls (autoplay|controls|hover)
 */

use FriendsOfREDAXO\Builder\Media\ResponsiveImage;

if (empty($image)) return;

$imageAlt = (string) ($imageAlt ?? '');
$imageTitle = (string) ($imageTitle ?? '');
$mediaPoolTitle = (string) ($mediaPoolTitle ?? '');
$mediaLightbox = (bool) ($mediaLightbox ?? false);
$mediaCover = (bool) ($mediaCover ?? false);

if (!isset($isVideo) || !is_callable($isVideo)) {
    $isVideo = static fn (string $file): bool => \FriendsOfREDAXO\Builder\Helper::isVideo($file);
}
if (!isset($isImage) || !is_callable($isImage)) {
    $isImage = static fn (string $file): bool => \FriendsOfREDAXO\Builder\Helper::isImage($file);
}

$isVideoFile = $isVideo($image);
$isImageFile = $isImage($image);
$mediaRatio = (string) ($mediaRatio ?? '16-9');
$mediaRatioMobile = (string) ($mediaRatioMobile ?? '');
$mobileArtDirectionActive = $mediaRatioMobile !== '' && $mediaRatioMobile !== $mediaRatio;

$ratioToPreset = static function (string $ratio): string {
    $map = [
        '16-9' => 'klxm_card_16_9',
        '21-9' => 'klxm_card_21_9',
        '4-3' => 'klxm_card_4_3',
        '1-1' => 'klxm_card_1_1',
        '3-2' => 'klxm_card_3_2',
        '3-4' => 'klxm_card_3_4',
        'original' => 'klxm_card_original',
    ];

    return $map[$ratio] ?? 'klxm_card_16_9';
};

// Video-Optionen mit Defaults
$videoDisplay = $videoDisplay ?? 'inline';
$videoControls = $videoControls ?? 'autoplay';

if ($isVideoFile): ?>
    <?php
    $videoSrc = rex_url::media($image);
    $posterSrc = $videoSrc;
    
    // ========================================================================
    // LIGHTBOX MODUS: Immer Standbild mit Play-Button zeigen
    // ========================================================================
    if ($mediaLightbox): ?>
        <div class="uk-inline uk-width-1-1 uk-transition-toggle" uk-lightbox="video-autoplay: true">
            <a href="<?= $videoSrc ?>" 
               data-caption="<?= rex_escape($imageTitle ?: ($mediaPoolTitle ?: $imageAlt)) ?>" 
               data-type="video">
                <!-- Poster-Bild -->
                <img loading="lazy" 
                     src="<?= $posterSrc ?>" 
                     alt="<?= rex_escape($imageAlt ?: 'Video abspielen') ?>"
                     <?= $mediaCover ? 'class="cb-cover-img"' : 'class="uk-width-1-1"' ?>>
                
                <!-- Play-Button Overlay -->
                <div class="uk-position-center">
                    <div class="uk-icon-button uk-box-shadow-large uk-transition-scale-up" 
                         style="background: rgba(0,0,0,0.6); width: 70px; height: 70px; border-radius: 50%;">
                        <span uk-icon="icon: play; ratio: 2.5" style="color: #fff;"></span>
                    </div>
                </div>
            </a>
        </div>
    
    <?php 
    // ========================================================================
    // POSTER MODUS: Standbild mit Play-Button, Video startet bei Klick
    // ========================================================================
    elseif ($videoDisplay === 'poster'): 
        $posterContainerId = 'video-poster-' . uniqid();
    ?>
        <div class="uk-inline uk-width-1-1 uk-position-relative" id="<?= $posterContainerId ?>">
            <!-- Poster-Bild -->
            <img loading="lazy" 
                 src="<?= $posterSrc ?>" 
                 alt="<?= rex_escape($imageAlt ?: 'Video abspielen') ?>"
                 class="video-poster-image uk-width-1-1"
                 <?= $mediaCover ? 'style="object-fit:cover"' : '' ?>>
            
            <!-- Play-Button Overlay -->
            <div class="uk-position-center video-play-button" style="cursor: pointer;">
                <div class="uk-icon-button uk-box-shadow-large" 
                     style="background: rgba(0,0,0,0.6); width: 70px; height: 70px; border-radius: 50%; transition: transform 0.2s;">
                    <span uk-icon="icon: play; ratio: 2.5" style="color: #fff;"></span>
                </div>
            </div>
            
            <!-- Verstecktes Video (wird bei Klick eingeblendet) -->
            <video class="video-player uk-width-1-1" 
                   src="<?= $videoSrc ?>" 
                   style="display: none;"
                   <?= $videoControls === 'controls' ? 'controls' : 'muted loop' ?>
                   playsinline
                   <?= $mediaCover ? 'class="cb-cover-img"' : '' ?>></video>
        </div>
        
        <script nonce="<?= rex_response::getNonce() ?>">
        (function() {
            var container = document.getElementById('<?= $posterContainerId ?>');
            if (!container || container.dataset.initialized) return;
            container.dataset.initialized = 'true';
            
            var poster = container.querySelector('.video-poster-image');
            var button = container.querySelector('.video-play-button');
            var video = container.querySelector('.video-player');
            
            if (button && video && poster) {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    poster.style.display = 'none';
                    button.style.display = 'none';
                    video.style.display = 'block';
                    video.play();
                });
            }
        })();
        </script>
    
    <?php 
    // ========================================================================
    // INLINE MODUS: Video direkt abspielen
    // ========================================================================
    else: 
        // Attribute je nach Controls-Einstellung
        $videoAttrs = '';
        switch ($videoControls) {
            case 'controls':
                $videoAttrs = 'controls preload="metadata"';
                break;
            case 'hover':
                $videoAttrs = 'muted loop playsinline preload="metadata" uk-video="autoplay: hover"';
                break;
            case 'autoplay':
            default:
                $videoAttrs = 'muted loop playsinline uk-video="autoplay: inview"';
                break;
        }
    ?>
        <video loading="lazy" 
               src="<?= $videoSrc ?>" 
               poster="<?= $posterSrc ?>"
               <?= $videoAttrs ?>
               <?= $mediaCover ? 'class="cb-cover-img"' : 'class="uk-width-1-1"' ?>></video>
    <?php endif; ?>
    
<?php elseif ($isImageFile): 
    $preset = $ratioToPreset($mediaRatio);
    $presetMobile = $mobileArtDirectionActive ? $ratioToPreset($mediaRatioMobile) : '';

    // Medien-Anteil berechnen (für horizontale Layouts)
    $mediaFrac = 1.0;
    if (isset($isHorizontal) && $isHorizontal && isset($mediaWidth)) {
        if (preg_match('/^(\d+)-(\d+)/', $mediaWidth, $mw)) {
            $mediaFrac = intval($mw[1]) / max(1, intval($mw[2]));
        }
    }

    $imageBuilder = ResponsiveImage::forFile($image)
        ->withDesktopPreset($preset)
        ->withWidths([400, 800, 1200, 1600])
        ->withContainerWidth((string) ($containerWidth ?? 'uk-container'))
        ->withColumns((int) ($columns ?? 3), (int) ($columnsTablet ?? 2), (int) ($columnsMobile ?? 1))
        ->withMediaFraction($mediaFrac);

    if ($presetMobile !== '') {
        $imageBuilder->withMobilePreset($presetMobile);
    }
    
    // Cover: reines CSS statt uk-cover JS-Component (vermeidet Safari Resize-Probleme)
    $imageClass = $mediaCover ? 'cb-cover-img' : 'uk-width-1-1';

    $defaultImgAttributes = [
        'alt' => $imageAlt,
        'class' => $imageClass,
        'loading' => 'lazy',
    ];

    $imageTag = $imageBuilder->toPictureTag($defaultImgAttributes);

    $imageTagWithTitle = $imageBuilder->toPictureTag([
        'alt' => $imageAlt,
        'title' => $imageTitle,
        'class' => $imageClass,
        'loading' => 'lazy',
    ]);
?>
    <?php if ($mediaLightbox): ?>
        <div uk-lightbox>
            <a href="<?= rex_url::media($image) ?>" data-caption="<?= rex_escape($imageTitle ?: ($mediaPoolTitle ?: $imageAlt)) ?>">
                <?= $imageTag ?>
            </a>
        </div>
    <?php else: ?>
        <?= $imageTagWithTitle ?>
    <?php endif; ?>
<?php endif; ?>
