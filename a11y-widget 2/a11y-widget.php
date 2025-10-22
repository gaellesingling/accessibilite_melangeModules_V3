<?php
/**
 * Plugin Name: A11y Widget – Module d’accessibilité (mini)
 * Description: Bouton flottant qui ouvre un module d’accessibilité avec placeholders (à brancher selon vos besoins). Shortcode: [a11y_widget]. 
 * Version: 1.4.0
 * Author: ChatGPT
 * License: GPL-2.0-or-later
 * Text Domain: a11y-widget
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'A11Y_WIDGET_VERSION', '1.4.0' );
define( 'A11Y_WIDGET_URL', plugin_dir_url( __FILE__ ) );
define( 'A11Y_WIDGET_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Enqueue front assets
 */
function a11y_widget_enqueue() {
    // Only load on front-end
    if ( is_admin() ) { return; }

    wp_enqueue_style(
        'a11y-widget',
        A11Y_WIDGET_URL . 'assets/widget.css',
        array(),
        A11Y_WIDGET_VERSION
    );

    wp_enqueue_script(
        'a11y-widget',
        A11Y_WIDGET_URL . 'assets/widget.js',
        array(),
        A11Y_WIDGET_VERSION,
        true
    );
}
add_action( 'wp_enqueue_scripts', 'a11y_widget_enqueue' );

/**
 * Render the widget HTML
 */
function a11y_widget_markup() {
    // Allow theme/plugins to disable automatic output
    $enable_auto = apply_filters( 'a11y_widget_enable_auto', true );
    if ( did_action('a11y_widget_printed') ) { return; } // avoid duplicates

    ob_start();
    include A11Y_WIDGET_PATH . 'templates/widget.php';
    $html = ob_get_clean();

    /**
     * Filter: change/augment the HTML before output
     */
    $html = apply_filters( 'a11y_widget_markup', $html );

    echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    do_action('a11y_widget_printed');
}

/**
 * Default widget sections definition (hierarchical: level 1 categories + level 2 placeholders).
 *
 * @return array[]
 */
function a11y_widget_get_default_sections() {
    $heading_selector = function_exists( 'a11y_widget_get_reading_guide_heading_selector' )
        ? a11y_widget_get_reading_guide_heading_selector()
        : 'main h2, main h3';
    $syllable_selector = function_exists( 'a11y_widget_get_reading_guide_syllable_selector' )
        ? a11y_widget_get_reading_guide_syllable_selector()
        : 'main p, main li';

    return array(
        array(
            'slug'     => 'vision',
            'title'    => __( 'Vision', 'a11y-widget' ),
            'icon'     => 'eye',
            'children' => array(
                array(
                    'slug'       => 'vision-daltonisme',
                    'label'      => __( 'Daltonisme', 'a11y-widget' ),
                    'aria_label' => __( 'Options pour le daltonisme', 'a11y-widget' ),
                    'children'   => array(
                        array(
                            'slug'        => 'vision-daltonisme-deuteranopie',
                            'label'       => __( 'Deutéranopie', 'a11y-widget' ),
                            'aria_label'  => __( 'Activer le mode deutéranopie', 'a11y-widget' ),
                            'placeholder' => true,
                        ),
                        array(
                            'slug'        => 'vision-daltonisme-protanopie',
                            'label'       => __( 'Protanopie', 'a11y-widget' ),
                            'aria_label'  => __( 'Activer le mode protanopie', 'a11y-widget' ),
                            'placeholder' => true,
                        ),
                        array(
                            'slug'        => 'vision-daltonisme-deuteranomalie',
                            'label'       => __( 'Deutéranomalie', 'a11y-widget' ),
                            'aria_label'  => __( 'Activer le mode deutéranomalie', 'a11y-widget' ),
                            'placeholder' => true,
                        ),
                        array(
                            'slug'        => 'vision-daltonisme-protanomalie',
                            'label'       => __( 'Protanomalie', 'a11y-widget' ),
                            'aria_label'  => __( 'Activer le mode protanomalie', 'a11y-widget' ),
                            'placeholder' => true,
                        ),
                        array(
                            'slug'        => 'vision-daltonisme-tritanopie',
                            'label'       => __( 'Tritanopie', 'a11y-widget' ),
                            'aria_label'  => __( 'Activer le mode tritanopie', 'a11y-widget' ),
                            'placeholder' => true,
                        ),
                        array(
                            'slug'        => 'vision-daltonisme-tritanomalie',
                            'label'       => __( 'Tritanomalie', 'a11y-widget' ),
                            'aria_label'  => __( 'Activer le mode tritanomalie', 'a11y-widget' ),
                            'placeholder' => true,
                        ),
                        array(
                            'slug'        => 'vision-daltonisme-achromatopsie',
                            'label'       => __( 'Achromatopsie', 'a11y-widget' ),
                            'aria_label'  => __( 'Activer le mode achromatopsie', 'a11y-widget' ),
                            'placeholder' => true,
                        ),
                    ),
                ),
                array(
                    'slug'       => 'vision-migraine',
                    'label'      => __( 'Soulagement migraines', 'a11y-widget' ),
                    'hint'       => __( 'Filtre chaud, réduction de la lumière bleue et options anti-déclencheurs.', 'a11y-widget' ),
                    'aria_label' => __( 'Configurer les réglages pour soulager les migraines visuelles', 'a11y-widget' ),
                    'template'   => 'migraine-relief',
                    'settings'   => array(
                        'intro'                        => __( 'Ces réglages réduisent les déclencheurs visuels courants (lumière bleue, contrastes violents, motifs répétitifs) pour soulager les migraines ophtalmiques.', 'a11y-widget' ),
                        'theme_label'                  => __( 'Thème de confort visuel', 'a11y-widget' ),
                        'theme_hint'                   => __( 'Ajustez la palette pour adoucir la page.', 'a11y-widget' ),
                        'theme_option_none'            => __( 'Standard atténué', 'a11y-widget' ),
                        'theme_option_none_aria'       => __( 'Utiliser le rendu standard atténué', 'a11y-widget' ),
                        'theme_option_grayscale'       => __( 'Tons neutres', 'a11y-widget' ),
                        'theme_option_grayscale_aria'  => __( 'Passer en niveaux de gris doux', 'a11y-widget' ),
                        'theme_option_amber'           => __( 'Filtre ambré', 'a11y-widget' ),
                        'theme_option_amber_aria'      => __( 'Activer le filtre ambré pour réduire la lumière bleue', 'a11y-widget' ),
                        'intensity_label'              => __( 'Intensité du filtre ambré', 'a11y-widget' ),
                        'intensity_hint'               => __( 'Plus l’intensité est élevée, plus la teinte chaude est prononcée.', 'a11y-widget' ),
                        'intensity_value_suffix'       => __( '%', 'a11y-widget' ),
                        'intensity_decrease'           => __( 'Diminuer l’intensité', 'a11y-widget' ),
                        'intensity_increase'           => __( 'Augmenter l’intensité', 'a11y-widget' ),
                        'remove_patterns_label'        => __( 'Supprimer les motifs répétitifs', 'a11y-widget' ),
                        'remove_patterns_hint'         => __( 'Élimine les arrière-plans très contrastés ou à motifs susceptibles de déclencher une crise.', 'a11y-widget' ),
                        'increase_spacing_label'       => __( 'Espacement augmenté', 'a11y-widget' ),
                        'increase_spacing_hint'        => __( 'Ajoute de l’espace entre les paragraphes et lignes pour calmer la lecture.', 'a11y-widget' ),
                        'presets_label'                => __( 'Presets rapides', 'a11y-widget' ),
                        'preset_mild_label'            => __( 'Soulagement doux', 'a11y-widget' ),
                        'preset_mild_hint'             => __( 'Atténuer légèrement la luminosité sans modifier la palette.', 'a11y-widget' ),
                        'preset_moderate_label'        => __( 'Mode focus', 'a11y-widget' ),
                        'preset_moderate_hint'         => __( 'Réduction nette de la lumière bleue et du contraste.', 'a11y-widget' ),
                        'preset_strong_label'          => __( 'Protection renforcée', 'a11y-widget' ),
                        'preset_strong_hint'           => __( 'Combinaison filtre ambré + suppression des motifs.', 'a11y-widget' ),
                        'preset_crisis_label'          => __( 'Mode crise', 'a11y-widget' ),
                        'preset_crisis_hint'           => __( 'Configuration maximale pour les situations critiques.', 'a11y-widget' ),
                        'reset_label'                  => __( 'Réinitialiser les réglages migraine', 'a11y-widget' ),
                        'reset_aria'                   => __( 'Réinitialiser toutes les options de soulagement migraine', 'a11y-widget' ),
                        'live_region_label'            => __( 'Notification des réglages migraine', 'a11y-widget' ),
                    ),
                ),
            ),
        ),
        array(
            'slug'     => 'luminosite',
            'title'    => __( 'Luminosité', 'a11y-widget' ),
            'icon'     => 'sun',
            'children' => array(
                array(
                    'slug'       => 'luminosite-reglages',
                    'label'      => __( 'Modes de luminosité', 'a11y-widget' ),
                    'hint'       => '',
                    'aria_label' => __( 'Configurer les options de luminosité', 'a11y-widget' ),
                    'template'   => 'brightness-settings',
                    'settings'   => array(
                        'modes_label'             => __( 'Mode d’affichage', 'a11y-widget' ),
                        'modes_hint'              => '',
                        'mode_normal_label'       => __( 'Normal', 'a11y-widget' ),
                        'mode_normal_aria'        => __( 'Revenir au mode normal', 'a11y-widget' ),
                        'mode_night_label'        => __( 'Mode nuit', 'a11y-widget' ),
                        'mode_night_aria'         => __( 'Activer le mode nuit', 'a11y-widget' ),
                        'mode_blue_light_label'   => __( 'Lumière bleue', 'a11y-widget' ),
                        'mode_blue_light_aria'    => __( 'Activer le filtre anti lumière bleue', 'a11y-widget' ),
                        'mode_high_contrast_label'=> __( 'Contraste +', 'a11y-widget' ),
                        'mode_high_contrast_aria' => __( 'Activer le mode contraste élevé', 'a11y-widget' ),
                        'mode_low_contrast_label' => __( 'Contraste -', 'a11y-widget' ),
                        'mode_low_contrast_aria'  => __( 'Activer le mode contraste réduit', 'a11y-widget' ),
                        'mode_grayscale_label'    => __( 'Niveaux de gris', 'a11y-widget' ),
                        'mode_grayscale_aria'     => __( 'Activer le mode niveaux de gris', 'a11y-widget' ),
                        'advanced_label'          => __( 'Réglages avancés', 'a11y-widget' ),
                        'advanced_hint'           => '',
                        'contrast_label'          => __( 'Contraste', 'a11y-widget' ),
                        'contrast_decrease'       => __( 'Diminuer le contraste', 'a11y-widget' ),
                        'contrast_increase'       => __( 'Augmenter le contraste', 'a11y-widget' ),
                        'brightness_label'        => __( 'Luminosité', 'a11y-widget' ),
                        'brightness_decrease'     => __( 'Diminuer la luminosité', 'a11y-widget' ),
                        'brightness_increase'     => __( 'Augmenter la luminosité', 'a11y-widget' ),
                        'saturation_label'        => __( 'Saturation', 'a11y-widget' ),
                        'saturation_decrease'     => __( 'Diminuer la saturation', 'a11y-widget' ),
                        'saturation_increase'     => __( 'Augmenter la saturation', 'a11y-widget' ),
                        'reset_label'             => __( 'Réinitialiser', 'a11y-widget' ),
                        'reset_aria'              => __( 'Réinitialiser les réglages de luminosité', 'a11y-widget' ),
                    ),
                ),
            ),
        ),
        array(
            'slug'     => 'cognitif',
            'title'    => __( 'Cognitif', 'a11y-widget' ),
            'icon'     => 'brain',
            'children' => array(
                array(
                    'slug'       => 'cognitif-reading-guide',
                    'label'      => __( 'Guide de lecture', 'a11y-widget' ),
                    'hint'       => '',
                    'aria_label' => __( 'Activer le guide de lecture', 'a11y-widget' ),
                    'template'   => 'reading-guide',
                    'settings'   => array(
                        'rule_label'                     => __( 'Règle de lecture', 'a11y-widget' ),
                        'rule_hint'                      => '',
                        'personalization_label'          => __( 'Personnalisation de la règle', 'a11y-widget' ),
                        'color_label'                    => __( 'Couleur', 'a11y-widget' ),
                        'color_hint'                     => '',
                        'opacity_label'                  => __( 'Opacité', 'a11y-widget' ),
                        'opacity_hint'                   => '',
                        'height_label'                   => __( 'Hauteur', 'a11y-widget' ),
                        'height_hint'                    => '',
                        'summary_label'                  => __( 'Sommaire automatique', 'a11y-widget' ),
                        'summary_hint'                   => '',
                        'summary_toggle_label'           => __( 'Activer le sommaire', 'a11y-widget' ),
                        'summary_title_default'          => __( 'Sommaire', 'a11y-widget' ),
                        'syllable_label'                 => __( 'Séparation syllabique', 'a11y-widget' ),
                        'syllable_hint'                  => '',
                        'syllable_toggle_label'          => __( 'Activer la séparation syllabique', 'a11y-widget' ),
                        'syllable_selector_label'        => __( 'Zones à syllaber', 'a11y-widget' ),
                        'syllable_selector_hint'         => '',
                        'syllable_selector_default'      => $syllable_selector,
                        'syllable_selector_placeholder'  => __( 'Ex. article p, article li', 'a11y-widget' ),
                        'focus_label'                    => __( 'Mode focus', 'a11y-widget' ),
                        'focus_hint'                     => '',
                        'focus_toggle_label'             => __( 'Activer le mode focus', 'a11y-widget' ),
                        'selectors'                      => array(
                            'headings'                      => $heading_selector,
                            'content_attribute'             => 'data-reading-guide-content',
                            'toc_attribute'                 => 'data-reading-guide-toc',
                            'toc_title_attribute'           => 'data-reading-guide-toc-title',
                            'syllable_attribute'            => 'data-reading-guide-syllables',
                            'animation_exempt_attribute'    => 'data-reading-guide-allow-animation',
                        ),
                    ),
                ),
                array(
                    'slug'       => 'cognitif-dyslexie',
                    'label'      => __( 'Dyslexie', 'a11y-widget' ),
                    'hint'       => '',
                    'aria_label' => __( 'Activer le surligneur de lettres', 'a11y-widget' ),
                    'template'   => 'dyslexie-highlighter',
                    'settings'   => array(
                        'letter_label'        => __( 'Lettre à surligner', 'a11y-widget' ),
                        'letter_placeholder'  => __( 'Entrez une lettre', 'a11y-widget' ),
                        'color_label'         => __( 'Couleur du surlignage', 'a11y-widget' ),
                        'accent_label'        => __( 'Prendre les accents en compte', 'a11y-widget' ),
                        'no_letter_warning'   => __( 'Saisissez une lettre pour commencer le surlignage.', 'a11y-widget' ),
                        'font_label'          => __( 'Police du texte', 'a11y-widget' ),
                        'font_help'           => '',
                        'font_option_default' => __( 'Police du site', 'a11y-widget' ),
                        'font_option_arial'   => __( 'Arial', 'a11y-widget' ),
                        'font_option_verdana' => __( 'Verdana', 'a11y-widget' ),
                        'font_option_trebuchet' => __( 'Trebuchet MS', 'a11y-widget' ),
                        'font_option_comic'   => __( 'Comic Sans MS', 'a11y-widget' ),
                        'font_option_open'    => __( 'OpenDyslexic', 'a11y-widget' ),
                        'font_option_dyslexic' => __( 'OpenDyslexic Alta', 'a11y-widget' ),
                        'font_option_luciole' => __( 'Luciole', 'a11y-widget' ),
                        'font_option_atkinson' => __( 'Atkinson Hyperlegible', 'a11y-widget' ),
                        'font_option_inconstant' => __( 'Inconstant', 'a11y-widget' ),
                        'font_option_accessible_dfa' => __( 'Accessible DfA', 'a11y-widget' ),
                        'size_label'          => __( 'Taille du texte', 'a11y-widget' ),
                        'size_help'           => '',
                        'line_label'          => __( 'Espacement des lignes', 'a11y-widget' ),
                        'line_help'           => '',
                        'styles_label'        => __( 'Styles du texte', 'a11y-widget' ),
                        'styles_help'         => '',
                        'disable_italic_label'=> __( 'Supprimer l’italique', 'a11y-widget' ),
                        'disable_bold_label'  => __( 'Supprimer le gras', 'a11y-widget' ),
                        'reset_label'         => __( 'Réinitialiser les réglages du texte', 'a11y-widget' ),
                    ),
                ),
            ),
        ),
        array(
            'slug'     => 'moteur',
            'title'    => __( 'Moteur', 'a11y-widget' ),
            'icon'     => 'hand',
            'children' => array(
                array(
                    'slug'       => 'moteur-boutons',
                    'label'      => __( 'Boutons', 'a11y-widget' ),
                    'hint'       => '',
                    'aria_label' => __( 'Configurer l’apparence des boutons', 'a11y-widget' ),
                    'template'   => 'button-settings',
                    'settings'   => array(
                        'size_label'  => __( 'Taille des boutons', 'a11y-widget' ),
                        'size_help'   => '',
                        'theme_label' => __( 'Couleurs des boutons', 'a11y-widget' ),
                        'theme_help'  => '',
                        'theme_prev'  => __( 'Thème précédent', 'a11y-widget' ),
                        'theme_next'  => __( 'Thème suivant', 'a11y-widget' ),
                        'reset_label' => __( 'Réinitialiser les boutons', 'a11y-widget' ),
                    ),
                ),
                array(
                    'slug'       => 'moteur-curseur',
                    'label'      => __( 'Curseur', 'a11y-widget' ),
                    'hint'       => '',
                    'aria_label' => __( 'Configurer le curseur personnalisé', 'a11y-widget' ),
                    'template'   => 'cursor-settings',
                    'settings'   => array(
                        'size_label' => __( 'Taille du curseur', 'a11y-widget' ),
                        'size_help'  => '',
                        'color_label' => __( 'Couleur du curseur', 'a11y-widget' ),
                        'color_help'  => '',
                    ),
                ),
            ),
        ),
        array(
            'slug'     => 'epilepsie',
            'title'    => __( 'Épilepsie', 'a11y-widget' ),
            'icon'     => 'bolt',
            'children' => array(
                array(
                    'slug'       => 'epilepsie-protection',
                    'label'      => __( 'Protection épilepsie', 'a11y-widget' ),
                    'hint'       => __( 'Bloquez les animations, GIFs, vidéos et flashs dangereux.', 'a11y-widget' ),
                    'aria_label' => __( 'Activer la protection contre les déclencheurs épileptiques', 'a11y-widget' ),
                    'template'   => 'epilepsy-protection',
                    'settings'   => array(
                        'intro'                    => __( 'Activez une protection médicale pour réduire les déclencheurs de crises : animations rapides, GIFs, vidéos en autoplay, effets parallax et flashs lumineux.', 'a11y-widget' ),
                        'stop_animations_label'    => __( 'Arrêter les animations', 'a11y-widget' ),
                        'stop_animations_hint'     => __( 'Force la durée des animations CSS et JavaScript à 0 s pour stopper les clignotements.', 'a11y-widget' ),
                        'stop_gifs_label'          => __( 'Figer les GIFs animés', 'a11y-widget' ),
                        'stop_gifs_hint'           => __( 'Capture la première image des GIFs et bloque ceux ajoutés dynamiquement.', 'a11y-widget' ),
                        'stop_videos_label'        => __( 'Bloquer l’autoplay des vidéos', 'a11y-widget' ),
                        'stop_videos_hint'         => __( 'Met en pause les vidéos et désactive l’autoplay sur YouTube/Vimeo.', 'a11y-widget' ),
                        'remove_parallax_label'    => __( 'Supprimer les effets parallax', 'a11y-widget' ),
                        'remove_parallax_hint'     => __( 'Neutralise les transformations 3D et les arrière-plans fixes.', 'a11y-widget' ),
                        'reduce_motion_label'      => __( 'Réduire tous les mouvements', 'a11y-widget' ),
                        'reduce_motion_hint'       => __( 'Force prefers-reduced-motion et désactive le défilement animé.', 'a11y-widget' ),
                        'block_flashing_label'     => __( 'Détecter et bloquer les flashs', 'a11y-widget' ),
                        'block_flashing_hint'      => __( 'Analyse la luminosité pour couper la page en cas de flash dangereux.', 'a11y-widget' ),
                        'activate_all_label'       => __( 'Activer toutes les protections', 'a11y-widget' ),
                        'activate_all_aria'        => __( 'Activer immédiatement toutes les protections épilepsie', 'a11y-widget' ),
                        'activate_all_confirm'     => __( 'Activer toutes les protections critiques ? Cela arrêtera animations, GIFs, vidéos, effets parallax, mouvements et flashs.', 'a11y-widget' ),
                        'reset_label'              => __( 'Réinitialiser les protections', 'a11y-widget' ),
                        'reset_aria'               => __( 'Réinitialiser les protections épilepsie', 'a11y-widget' ),
                        'live_region_label'        => __( 'Notifications des protections épilepsie', 'a11y-widget' ),
                        'gif_placeholder_label'    => __( 'GIF désactivé pour votre sécurité', 'a11y-widget' ),
                        'gif_placeholder_hint'     => __( 'Un GIF animé a été masqué pour éviter les déclencheurs lumineux.', 'a11y-widget' ),
                        'flash_overlay_title'      => __( 'Flash dangereux détecté', 'a11y-widget' ),
                        'flash_overlay_body'       => __( 'La page est temporairement masquée pour éviter un déclencheur photosensible.', 'a11y-widget' ),
                        'flash_overlay_dismiss'    => __( 'Fermer l’alerte', 'a11y-widget' ),
                    ),
                ),
            ),
        ),
        array(
            'slug'     => 'audition',
            'title'    => __( 'Audition', 'a11y-widget' ),
            'icon'     => 'ear',
            'children' => array(
                array(
                    'slug'        => 'audition-placeholder',
                    'label'       => __( 'Exemple : activer les sous-titres', 'a11y-widget' ),
                    'aria_label'  => __( 'Exemple de réglage pour l’audition', 'a11y-widget' ),
                    'placeholder' => true,
                ),
            ),
        ),
    );
}

/**
 * Retrieve the SVG markup for a named icon.
 *
 * Icon paths are adapted from the Lucide icon set (https://lucide.dev) and
 * distributed under the MIT License.
 *
 * @param string $icon_key Registered icon identifier.
 * @param array  $args     Optional arguments. Supports `class` for custom classes.
 *
 * @return string
 */
function a11y_widget_get_icon_markup( $icon_key, $args = array() ) {
    $icon_key = sanitize_key( $icon_key );

    if ( '' === $icon_key ) {
        return '';
    }

    $icons = array(
        'eye'    => array(
            'viewBox' => '0 0 24 24',
            'elements' => array(
                array(
                    'type' => 'path',
                    'd'    => 'M2 12s4.5-6 10-6 10 6 10 6-4.5 6-10 6S2 12 2 12Z',
                ),
                array(
                    'type' => 'circle',
                    'cx'   => '12',
                    'cy'   => '12',
                    'r'    => '3',
                ),
            ),
        ),
        'sun'    => array(
            'viewBox' => '0 0 24 24',
            'elements' => array(
                array(
                    'type' => 'circle',
                    'cx'   => '12',
                    'cy'   => '12',
                    'r'    => '4',
                ),
                array(
                    'type' => 'path',
                    'd'    => 'M12 2v2',
                ),
                array(
                    'type' => 'path',
                    'd'    => 'M12 20v2',
                ),
                array(
                    'type' => 'path',
                    'd'    => 'M4.93 4.93L6.34 6.34',
                ),
                array(
                    'type' => 'path',
                    'd'    => 'M17.66 17.66L19.07 19.07',
                ),
                array(
                    'type' => 'path',
                    'd'    => 'M2 12h2',
                ),
                array(
                    'type' => 'path',
                    'd'    => 'M20 12h2',
                ),
                array(
                    'type' => 'path',
                    'd'    => 'M4.93 19.07L6.34 17.66',
                ),
                array(
                    'type' => 'path',
                    'd'    => 'M17.66 6.34L19.07 4.93',
                ),
            ),
        ),
        'ear'    => array(
            'viewBox' => '0 0 24 24',
            'elements' => array(
                array(
                    'type' => 'path',
                    'd'    => 'M6.5 9a5.5 5.5 0 1 1 11 0c0 2.2-1.6 3.6-1.6 4.8a3.7 3.7 0 0 1-7.4 0v-.3c0-1.4-.5-2.4-1.4-3.2',
                ),
                array(
                    'type' => 'path',
                    'd'    => 'M9.6 9.2c.6-1.4 1.9-2.3 3.4-2.3 2.1 0 3.5 1.4 3.5 3.5 0 1.4-.6 2.4-1.5 3.2l-.5.4',
                ),
                array(
                    'type' => 'path',
                    'd'    => 'M6.8 19.6c.4 1.3 1.5 2.2 2.9 2.2h1',
                ),
            ),
        ),
        'brain'  => array(
            'viewBox' => '0 0 24 24',
            'elements' => array(
                array(
                    'type' => 'path',
                    'd'    => 'M9 4A3.5 3.5 0 0 0 5.5 7.5V8.75C4.64 9.18 4 10.3 4 11.6c0 1.3 .62 2.45 1.62 3.04L6 14.86V16c0 2.21 1.79 4 4 4h1.5c.83 0 1.5-.67 1.5-1.5V7.5A3.5 3.5 0 0 0 9 4Z',
                ),
                array(
                    'type' => 'path',
                    'd'    => 'M15 4A3.5 3.5 0 0 1 18.5 7.5V8.75c.86 .43 1.5 1.55 1.5 2.85 0 1.3-.62 2.45-1.62 3.04L18 14.86V16c0 2.21-1.79 4-4 4h-1.5c-.83 0-1.5-.67-1.5-1.5V7.5A3.5 3.5 0 0 1 15 4Z',
                ),
                array(
                    'type' => 'path',
                    'd'    => 'M9 10.5a1.5 1.5 0 0 0 0 3',
                ),
                array(
                    'type' => 'path',
                    'd'    => 'M15 10.5a1.5 1.5 0 0 1 0 3',
                ),
                array(
                    'type' => 'path',
                    'd'    => 'M12 6v12',
                ),
            ),
        ),
        'hand'   => array(
            'viewBox' => '0 0 24 24',
            'elements' => array(
                array(
                    'type' => 'path',
                    'd'    => 'M18 11.2V6.2a2.2 2.2 0 1 0-4.4 0v5',
                ),
                array(
                    'type' => 'path',
                    'd'    => 'M13.6 10.6V4.8a2.2 2.2 0 1 0-4.4 0v6.6',
                ),
                array(
                    'type' => 'path',
                    'd'    => 'M9.2 11V6.6a2 2 0 1 0-4 0v7c0 3.9 2 7.2 5.1 8.9a6.5 6.5 0 0 0 3 .7h1c3.2 0 5.7-2.6 5.7-5.7V12',
                ),
                array(
                    'type' => 'path',
                    'd'    => 'M6.2 12.5c0-2-.8-3.4-1.8-4.3',
                ),
                array(
                    'type' => 'path',
                    'd'    => 'M9.5 15.3c.8 1.7 2.5 2.9 4.5 2.9A3.2 3.2 0 0 0 17.2 15V12',
                ),
            ),
        ),
        'bolt'   => array(
            'viewBox' => '0 0 24 24',
            'elements' => array(
                array(
                    'type' => 'path',
                    'd'    => 'M13 2 4 14h7l-1 8 9-12h-7l1-8Z',
                ),
            ),
        ),
    );

    if ( ! isset( $icons[ $icon_key ] ) ) {
        return '';
    }

    $icon   = $icons[ $icon_key ];
    $class  = '';
    $output = '';

    if ( ! empty( $args['class'] ) ) {
        $classes = is_array( $args['class'] ) ? $args['class'] : preg_split( '/\s+/', (string) $args['class'] );
        $classes = array_map( 'sanitize_html_class', array_filter( (array) $classes ) );
        if ( ! empty( $classes ) ) {
            $class = implode( ' ', $classes );
        }
    }

    ob_start();
    ?>
    <svg
        viewBox="<?php echo esc_attr( isset( $icon['viewBox'] ) ? $icon['viewBox'] : '0 0 24 24' ); ?>"
        fill="none"
        stroke="currentColor"
        stroke-width="1.8"
        stroke-linecap="round"
        stroke-linejoin="round"
        focusable="false"
        aria-hidden="true"
        <?php if ( '' !== $class ) : ?>class="<?php echo esc_attr( $class ); ?>"<?php endif; ?>
    >
        <?php foreach ( $icon['elements'] as $element ) :
            $type = isset( $element['type'] ) ? $element['type'] : '';

            if ( 'path' === $type && ! empty( $element['d'] ) ) :
                ?>
                <path d="<?php echo esc_attr( $element['d'] ); ?>" />
                <?php
            elseif ( 'circle' === $type && isset( $element['cx'], $element['cy'], $element['r'] ) ) :
                ?>
                <circle cx="<?php echo esc_attr( $element['cx'] ); ?>" cy="<?php echo esc_attr( $element['cy'] ); ?>" r="<?php echo esc_attr( $element['r'] ); ?>" />
                <?php
            endif;
        endforeach; ?>
    </svg>
    <?php
    $output = ob_get_clean();

    return trim( $output );
}

/**
 * Normalize nested children for a feature definition.
 *
 * @param array $feature Feature data.
 *
 * @return array
 */
function a11y_widget_normalize_nested_children( $feature ) {
    if ( ! is_array( $feature ) ) {
        return array();
    }

    if ( empty( $feature['children'] ) || ! is_array( $feature['children'] ) ) {
        if ( isset( $feature['children'] ) && ! is_array( $feature['children'] ) ) {
            unset( $feature['children'] );
        }

        return $feature;
    }

    $normalized_children = array();

    foreach ( $feature['children'] as $child ) {
        if ( ! is_array( $child ) || empty( $child['slug'] ) ) {
            continue;
        }

        $child_slug = sanitize_key( $child['slug'] );

        if ( '' === $child_slug ) {
            continue;
        }

        $child['slug'] = $child_slug;
        $normalized_children[] = a11y_widget_normalize_nested_children( $child );
    }

    $feature['children'] = $normalized_children;

    return $feature;
}

/**
 * Remove hint text from placeholder features, including nested children.
 *
 * A feature is considered a placeholder when it explicitly sets the
 * `placeholder` flag or when it originates from the Markdown directory and
 * does not define a custom template. These entries are meant to be wiring
 * stubs, so their descriptive text should remain hidden in the interface.
 *
 * @param array $feature Feature data.
 *
 * @return array
 */
function a11y_widget_strip_placeholder_hint_from_feature( $feature ) {
    if ( ! is_array( $feature ) ) {
        return $feature;
    }

    $has_placeholder_flag   = ! empty( $feature['placeholder'] );
    $has_markdown_origin    = isset( $feature['source'] );
    $defines_custom_template = isset( $feature['template'] ) && '' !== $feature['template'];

    if ( $has_placeholder_flag || ( $has_markdown_origin && ! $defines_custom_template ) ) {
        $feature['hint'] = '';
    }

    if ( ! empty( $feature['children'] ) && is_array( $feature['children'] ) ) {
        foreach ( $feature['children'] as $index => $child_feature ) {
            $feature['children'][ $index ] = a11y_widget_strip_placeholder_hint_from_feature( $child_feature );
        }
    }

    return $feature;
}

/**
 * Strip hint text from placeholder features within a collection of sections.
 *
 * @param array $sections Sections with their features.
 *
 * @return array
 */
function a11y_widget_strip_placeholder_hints( $sections ) {
    if ( empty( $sections ) || ! is_array( $sections ) ) {
        return $sections;
    }

    foreach ( $sections as $section_index => $section ) {
        if ( empty( $section['children'] ) || ! is_array( $section['children'] ) ) {
            continue;
        }

        foreach ( $section['children'] as $feature_index => $feature ) {
            $section['children'][ $feature_index ] = a11y_widget_strip_placeholder_hint_from_feature( $feature );
        }

        $sections[ $section_index ] = $section;
    }

    return $sections;
}

/**
 * Parse Markdown feature files located in the plugin `features/` directory.
 *
 * File format (per line, bullet list):
 *   # Mon titre de section (catégorie niveau 1)
 *   - `slug` **Label** : Hint optionnel (placeholders niveau 2)
 *
 * @return array[] Parsed sections.
 */
function a11y_widget_parse_markdown_sections() {
    static $cache = null;

    if ( null !== $cache ) {
        return $cache;
    }

    $dir = trailingslashit( A11Y_WIDGET_PATH ) . 'features';

    if ( ! is_dir( $dir ) ) {
        return array();
    }

    $files = glob( trailingslashit( $dir ) . '*.md' );
    if ( false === $files || empty( $files ) ) {
        return array();
    }

    sort( $files );

    $sections      = array();
    $section_order = array();

    foreach ( $files as $file ) {
        $lines = file( $file, FILE_IGNORE_NEW_LINES );
        if ( false === $lines ) {
            continue;
        }

        $current_section = null;

        foreach ( $lines as $raw_line ) {
            $line = trim( $raw_line );

            if ( '' === $line ) {
                continue;
            }

            if ( preg_match( '/^#{1,6}\s*(.+)$/u', $line, $matches ) ) {
                $title = wp_strip_all_tags( trim( $matches[1] ) );
                if ( '' === $title ) {
                    continue;
                }

                $slug = sanitize_title( $title );
                if ( '' === $slug ) {
                    $current_section = null;
                    continue;
                }

                if ( ! isset( $sections[ $slug ] ) ) {
                    $sections[ $slug ] = array(
                        'slug'           => $slug,
                        'title'          => $title,
                        'children'       => array(),
                        'children_order' => array(),
                    );
                    $section_order[] = $slug;
                } elseif ( '' === $sections[ $slug ]['title'] ) {
                    $sections[ $slug ]['title'] = $title;
                }

                $current_section = $slug;
                continue;
            }

            if ( 0 !== strpos( $line, '-' ) || null === $current_section ) {
                continue;
            }

            if ( preg_match( '/-\s*`([^`]+)`\s*(?:\*\*(.+?)\*\*|([^:]+))?\s*(?::\s*(.+))?$/u', $line, $matches ) ) {
                $slug = sanitize_key( $matches[1] );
                if ( '' === $slug ) {
                    continue;
                }

                if ( isset( $sections[ $current_section ]['children'][ $slug ] ) ) {
                    continue;
                }

                $raw_label = '';
                if ( ! empty( $matches[2] ) ) {
                    $raw_label = $matches[2];
                } elseif ( ! empty( $matches[3] ) ) {
                    $raw_label = trim( $matches[3] );
                }

                if ( '' === $raw_label ) {
                    $raw_label = $slug;
                }

                $raw_label = wp_strip_all_tags( $raw_label );

                $hint = '';
                if ( isset( $matches[4] ) ) {
                    $hint = wp_strip_all_tags( trim( $matches[4] ) );
                }

                $sections[ $current_section ]['children'][ $slug ] = array(
                    'slug'       => $slug,
                    'label'      => $raw_label,
                    'hint'       => $hint,
                    'aria_label' => sprintf( __( 'Activer %s', 'a11y-widget' ), $raw_label ),
                    'source'     => basename( $file ),
                );
                $sections[ $current_section ]['children_order'][] = $slug;
            }
        }
    }

    $ordered_sections = array();
    foreach ( $section_order as $slug ) {
        if ( ! isset( $sections[ $slug ] ) ) {
            continue;
        }

        $section = $sections[ $slug ];
        $ordered_children = array();
        if ( ! empty( $section['children_order'] ) ) {
            foreach ( $section['children_order'] as $child_slug ) {
                if ( isset( $section['children'][ $child_slug ] ) ) {
                    $ordered_children[] = $section['children'][ $child_slug ];
                }
            }
        }

        $section['children'] = $ordered_children;
        unset( $section['children_order'] );

        $ordered_sections[] = $section;
    }

    $cache = $ordered_sections;

    return $cache;
}

/**
 * Merge default and Markdown-defined sections without overwriting existing slugs.
 *
 * @return array[]
 */
function a11y_widget_get_sections() {
    $defaults          = a11y_widget_get_default_sections();
    $sections_by_slug  = array();
    $ordered_slugs     = array();
    $child_slug_global = array();

    foreach ( $defaults as $section ) {
        if ( empty( $section['slug'] ) ) {
            continue;
        }

        $slug = sanitize_title( $section['slug'] );
        if ( '' === $slug ) {
            continue;
        }

        if ( ! isset( $sections_by_slug[ $slug ] ) ) {
            $sections_by_slug[ $slug ] = array(
                'slug'           => $slug,
                'title'          => isset( $section['title'] ) ? $section['title'] : '',
                'icon'           => isset( $section['icon'] ) ? sanitize_key( $section['icon'] ) : '',
                'children'       => array(),
                'children_order' => array(),
            );
            $ordered_slugs[] = $slug;
        } else {
            if ( isset( $section['title'] ) && '' !== $section['title'] && '' === $sections_by_slug[ $slug ]['title'] ) {
                $sections_by_slug[ $slug ]['title'] = $section['title'];
            }

            if ( isset( $section['icon'] ) && '' === $sections_by_slug[ $slug ]['icon'] ) {
                $sections_by_slug[ $slug ]['icon'] = sanitize_key( $section['icon'] );
            }
        }

        $children = array();
        if ( isset( $section['children'] ) && is_array( $section['children'] ) ) {
            $children = $section['children'];
        }

        foreach ( $children as $child ) {
            if ( empty( $child['slug'] ) ) {
                continue;
            }

            $child_slug = sanitize_key( $child['slug'] );
            if ( '' === $child_slug ) {
                continue;
            }

            if ( isset( $sections_by_slug[ $slug ]['children'][ $child_slug ] ) ) {
                continue;
            }

            $child['slug'] = $child_slug;
            $child         = a11y_widget_normalize_nested_children( $child );
            $sections_by_slug[ $slug ]['children'][ $child_slug ] = $child;
            $sections_by_slug[ $slug ]['children_order'][]        = $child_slug;
            $child_slug_global[ $child_slug ]                     = true;
        }
    }

    $extra_sections = a11y_widget_parse_markdown_sections();

    foreach ( $extra_sections as $section ) {
        if ( empty( $section['slug'] ) ) {
            continue;
        }

        $slug = sanitize_title( $section['slug'] );
        if ( '' === $slug ) {
            continue;
        }

        if ( ! isset( $sections_by_slug[ $slug ] ) ) {
            $sections_by_slug[ $slug ] = array(
                'slug'           => $slug,
                'title'          => isset( $section['title'] ) ? $section['title'] : '',
                'icon'           => isset( $section['icon'] ) ? sanitize_key( $section['icon'] ) : '',
                'children'       => array(),
                'children_order' => array(),
            );
            $ordered_slugs[] = $slug;
        } else {
            if ( '' !== $section['title'] && '' === $sections_by_slug[ $slug ]['title'] ) {
                $sections_by_slug[ $slug ]['title'] = $section['title'];
            }

            if ( isset( $section['icon'] ) && '' === $sections_by_slug[ $slug ]['icon'] ) {
                $sections_by_slug[ $slug ]['icon'] = sanitize_key( $section['icon'] );
            }
        }

        if ( empty( $section['children'] ) ) {
            continue;
        }

        foreach ( $section['children'] as $child ) {
            if ( empty( $child['slug'] ) ) {
                continue;
            }

            $child_slug = sanitize_key( $child['slug'] );
            if ( '' === $child_slug ) {
                continue;
            }

            if ( isset( $child_slug_global[ $child_slug ] ) ) {
                continue;
            }

            if ( isset( $sections_by_slug[ $slug ]['children'][ $child_slug ] ) ) {
                continue;
            }

            $child['slug']                                   = $child_slug;
            $child                                           = a11y_widget_normalize_nested_children( $child );
            $child_slug_global[ $child_slug ]                = true;
            $sections_by_slug[ $slug ]['children'][ $child_slug ] = $child;
            $sections_by_slug[ $slug ]['children_order'][]        = $child_slug;
        }
    }

    $sections = array();
    foreach ( $ordered_slugs as $slug ) {
        if ( ! isset( $sections_by_slug[ $slug ] ) ) {
            continue;
        }

        $section = $sections_by_slug[ $slug ];
        if ( ! isset( $section['title'] ) || ! is_string( $section['title'] ) ) {
            $section['title'] = '';
        }

        $ordered_children = array();
        if ( isset( $section['children_order'] ) && is_array( $section['children_order'] ) ) {
            foreach ( $section['children_order'] as $child_slug ) {
                if ( isset( $section['children'][ $child_slug ] ) ) {
                    $ordered_children[] = $section['children'][ $child_slug ];
                }
            }
        }

        $section['children'] = $ordered_children;
        unset( $section['children_order'] );

        $sections[] = $section;
    }

    $sections = a11y_widget_apply_custom_feature_layout( $sections );
    $sections = a11y_widget_strip_placeholder_hints( $sections );

    /**
     * Filter the final list of sections sent to the template.
     *
     * @param array $sections Sections with children.
     */
    return apply_filters( 'a11y_widget_sections', $sections );
}

/**
 * Apply the administrator-defined feature layout to sections.
 *
 * @param array $sections Sections with their features.
 *
 * @return array
 */
function a11y_widget_apply_custom_feature_layout( $sections ) {
    if ( empty( $sections ) || ! is_array( $sections ) ) {
        return array();
    }

    if ( ! function_exists( 'a11y_widget_get_feature_layout' ) ) {
        return $sections;
    }

    $layout = a11y_widget_get_feature_layout();

    if ( empty( $layout ) || ! is_array( $layout ) ) {
        return $sections;
    }

    $feature_map = array();

    foreach ( $sections as $section ) {
        if ( empty( $section['children'] ) || ! is_array( $section['children'] ) ) {
            continue;
        }

        foreach ( $section['children'] as $feature ) {
            if ( empty( $feature['slug'] ) ) {
                continue;
            }

            $feature_slug = sanitize_key( $feature['slug'] );

            if ( '' === $feature_slug ) {
                continue;
            }

            $feature['slug']          = $feature_slug;
            $feature_map[ $feature_slug ] = $feature;
        }
    }

    if ( empty( $feature_map ) ) {
        return $sections;
    }

    $assigned            = array();
    $feature_destination = array();

    foreach ( $layout as $section_slug => $child_slugs ) {
        if ( ! is_array( $child_slugs ) ) {
            continue;
        }

        $section_slug = sanitize_title( $section_slug );

        if ( '' === $section_slug ) {
            continue;
        }

        foreach ( $child_slugs as $child_slug ) {
            $child_slug = sanitize_key( $child_slug );

            if ( '' === $child_slug ) {
                continue;
            }

            if ( isset( $feature_destination[ $child_slug ] ) ) {
                continue;
            }

            $feature_destination[ $child_slug ] = $section_slug;
        }
    }

    foreach ( $sections as &$section ) {
        if ( empty( $section['slug'] ) ) {
            continue;
        }

        $section_slug = sanitize_title( $section['slug'] );

        if ( '' === $section_slug ) {
            continue;
        }

        $ordered_children = array();

        if ( isset( $layout[ $section_slug ] ) && is_array( $layout[ $section_slug ] ) ) {
            foreach ( $layout[ $section_slug ] as $child_slug ) {
                $child_slug = sanitize_key( $child_slug );

                if ( '' === $child_slug ) {
                    continue;
                }

                if ( isset( $assigned[ $child_slug ] ) || ! isset( $feature_map[ $child_slug ] ) ) {
                    continue;
                }

                $feature = $feature_map[ $child_slug ];
                $feature['slug'] = $child_slug;

                $ordered_children[]      = $feature;
                $assigned[ $child_slug ] = true;
            }
        }

        if ( isset( $section['children'] ) && is_array( $section['children'] ) ) {
            foreach ( $section['children'] as $feature ) {
                if ( empty( $feature['slug'] ) ) {
                    continue;
                }

                $child_slug = sanitize_key( $feature['slug'] );

                if ( '' === $child_slug || isset( $assigned[ $child_slug ] ) || ! isset( $feature_map[ $child_slug ] ) ) {
                    continue;
                }

                if ( isset( $feature_destination[ $child_slug ] ) && $feature_destination[ $child_slug ] !== $section_slug ) {
                    continue;
                }

                $feature['slug']         = $child_slug;
                $ordered_children[]      = $feature;
                $assigned[ $child_slug ] = true;
            }
        }

        $section['children'] = $ordered_children;
    }
    unset( $section );

    return $sections;
}

// Load admin settings and feature visibility management.
require_once A11Y_WIDGET_PATH . 'includes/admin-settings.php';

/**
 * Auto-inject in footer unless disabled
 */
function a11y_widget_auto_inject() {
    $enable_auto = apply_filters( 'a11y_widget_enable_auto', true );
    if ( $enable_auto ) {
        a11y_widget_markup();
    }
}
add_action( 'wp_footer', 'a11y_widget_auto_inject', 5 );

/**
 * Shortcode: [a11y_widget]
 */
function a11y_widget_shortcode() {
    ob_start();
    include A11Y_WIDGET_PATH . 'templates/widget.php';
    return ob_get_clean();
}
add_shortcode( 'a11y_widget', 'a11y_widget_shortcode' );
