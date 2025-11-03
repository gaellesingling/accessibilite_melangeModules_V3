<?php
/**
 * Widget markup (front)
 * This is printed in the footer or via shortcode.
 */

$default_logo = '';

if ( function_exists( 'a11y_widget_get_launcher_logo_image_markup' ) ) {
    $default_logo = (string) a11y_widget_get_launcher_logo_image_markup( 'rouge', 'default' );
} elseif ( function_exists( 'a11y_widget_get_logo_svg_from_file' ) ) {
    $default_logo = a11y_widget_get_logo_svg_from_file( 'logo_rouge.svg' );
}

if ( '' === $default_logo ) {
    $default_logo = '<svg viewBox="0 0 24 24" role="img" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="12" fill="#dc2626" /><path fill="#ffffff" d="M12 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm6.75 6.5h-4.5v11a1 1 0 1 1-2 0v-5h-1v5a1 1 0 1 1-2 0v-11h-4.5a1 1 0 1 1 0-2h14a1 1 0 1 1 0 2Z" /></svg>';
}

if ( function_exists( 'a11y_widget_get_launcher_logo_image_markup' ) ) {
    $launcher_logo_markup = (string) a11y_widget_get_launcher_logo_image_markup( null, 'launcher' );
    $panel_logo_markup    = (string) a11y_widget_get_launcher_logo_image_markup( null, 'panel' );
} elseif ( function_exists( 'a11y_widget_get_launcher_logo_markup' ) ) {
    $launcher_logo_markup = (string) a11y_widget_get_launcher_logo_markup();
    $panel_logo_markup    = (string) a11y_widget_get_launcher_logo_markup();

    if ( '' === trim( $launcher_logo_markup ) ) {
        $launcher_logo_markup = $default_logo;
    }

    if ( '' === trim( $panel_logo_markup ) ) {
        $panel_logo_markup = $default_logo;
    }
} else {
    $launcher_logo_markup = $default_logo;
    $panel_logo_markup    = $default_logo;
}

$launcher_has_logo = (bool) preg_match( '/<(svg|img)\b/i', $launcher_logo_markup );
$launcher_classes  = 'a11y-launcher' . ( $launcher_has_logo ? ' has-logo' : '' );
$logo_scale_value  = 1.0;
$launcher_size_px  = 56;
$panel_logo_px     = 28;

if ( function_exists( 'a11y_widget_get_launcher_logo_scale' ) ) {
    $logo_scale_value = (float) a11y_widget_get_launcher_logo_scale();

    if ( $logo_scale_value <= 0 ) {
        $logo_scale_value = 1.0;
    }
}

$launcher_size_px = max( 1, (int) round( $launcher_size_px * $logo_scale_value ) );
$panel_logo_px    = max( 1, (int) round( $panel_logo_px * $logo_scale_value ) );

$logo_scale_css_value = rtrim( rtrim( number_format( $logo_scale_value, 2, '.', '' ), '0' ), '.' );

$logo_scale_style = sprintf(
    '--a11y-widget-logo-scale: %1$s; --a11y-launcher-size: %2$dpx; --a11y-panel-logo-size: %3$dpx;',
    $logo_scale_css_value,
    $launcher_size_px,
    $panel_logo_px
);

$background_mode = 'modal';

if ( function_exists( 'a11y_widget_get_background_mode' ) ) {
    $background_mode = a11y_widget_get_background_mode();
}

if ( ! in_array( $background_mode, array( 'modal', 'interactive' ), true ) ) {
    $background_mode = 'modal';
}

$root_class_names = array( 'a11y-root', 'a11y-root--mode-' . $background_mode );

if ( function_exists( 'sanitize_html_class' ) ) {
    $root_class_names = array_map( 'sanitize_html_class', $root_class_names );
}

$root_classes = implode( ' ', array_filter( array_unique( $root_class_names ) ) );
?>
<div
  id="a11y-widget-root"
  class="<?php echo esc_attr( $root_classes ); ?>"
  data-a11y-filter-exempt
  data-background-mode="<?php echo esc_attr( $background_mode ); ?>"
  style="<?php echo esc_attr( $logo_scale_style ); ?>"
>
  <button class="<?php echo esc_attr( $launcher_classes ); ?>" id="a11y-launcher" aria-haspopup="dialog" aria-expanded="false" aria-controls="a11y-panel" aria-label="<?php echo esc_attr__('Ouvrir le module d’accessibilité', 'a11y-widget'); ?>" data-a11y-preserve-colors data-a11y-filter-exempt>
    <?php echo $launcher_logo_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
  </button>

  <section
    class="a11y-panel is-right"
    id="a11y-panel"
    role="dialog"
    aria-modal="<?php echo esc_attr( 'modal' === $background_mode ? 'true' : 'false' ); ?>"
    aria-labelledby="a11y-title"
    aria-hidden="true"
    hidden
    data-a11y-preserve-colors
  >
    <?php
    $panel_label_left  = esc_attr__( 'Placer le panneau à gauche', 'a11y-widget' );
    $panel_label_right = esc_attr__( 'Placer le panneau à droite', 'a11y-widget' );
    ?>
    <header class="a11y-header">
      <button
        type="button"
        class="a11y-side-toggle"
        id="a11y-side-toggle"
        aria-pressed="false"
        aria-label="<?php echo $panel_label_left; ?>"
        data-label-left="<?php echo $panel_label_left; ?>"
        data-label-right="<?php echo $panel_label_right; ?>"
        title="<?php echo $panel_label_left; ?>"
      >
        <svg class="a11y-side-toggle__icon" viewBox="0 0 24 24" aria-hidden="true">
          <polyline points="8 5 3 12 8 19" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></polyline>
          <polyline points="16 5 21 12 16 19" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></polyline>
          <line x1="12" y1="4" x2="12" y2="20" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></line>
        </svg>
      </button>
      <button
        type="button"
        class="a11y-icon a11y-info-trigger"
        id="a11y-info-trigger"
        aria-haspopup="dialog"
        aria-expanded="false"
        aria-controls="a11y-info-dialog"
        aria-label="<?php echo esc_attr__( 'Ouvrir la fenêtre didactique', 'a11y-widget' ); ?>"
        data-open-label="<?php echo esc_attr__( 'Ouvrir la fenêtre didactique', 'a11y-widget' ); ?>"
        data-close-label="<?php echo esc_attr__( 'Fermer la fenêtre didactique', 'a11y-widget' ); ?>"
      >
        <svg
          class="a11y-info-trigger__icon"
          id="Calque_2"
          data-name="Calque 2"
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 286.84 286.84"
          aria-hidden="true"
          focusable="false"
        >
          <defs>
            <style>
              .cls-1 {
                fill: #fff;
              }
            </style>
          </defs>
          <g id="Calque_1-2" data-name="Calque 1">
            <g>
              <path
                class="cls-1"
                d="M286.84,0v286.84H0V0h286.84ZM253.1,22.5c-28.15-12.98-92.14,22.62-108.73,21.22-12.27-1.04-42.41-16.99-64.36-19.65-13.62-1.65-26.98-2.32-40.63-1.57v196.85c29.77-9.33,87.59,23.93,109.23,22.48,25.26-1.7,72.03-30.76,104.5-22.48V22.5ZM28.12,50.62h-11.25v191.23c37.57-9.71,101.95,23.59,131.7,22.23,32.64-1.49,87.64-30.87,127.02-22.23V50.62h-11.25v179.98c-34.88-10.97-92.46,22.32-120.02,20.91-31.12-1.6-79.09-30.66-116.2-20.91V50.62Z"
              />
              <path
                d="M253.1,22.5v196.85c-32.46-8.29-79.24,20.78-104.5,22.48-21.64,1.46-79.46-31.8-109.23-22.48V22.5c13.66-.75,27.01-.08,40.63,1.57,21.96,2.66,52.09,18.61,64.36,19.65,16.59,1.41,80.58-34.19,108.73-21.22ZM140.61,224.97V59.06c-25.5-19.01-59.91-19.74-89.99-25.31v174.36l89.99,16.87ZM241.85,33.75c-30.08,5.57-64.49,6.3-89.99,25.31v165.92l89.99-16.87V33.75Z"
              />
              <path
                d="M28.12,50.62v179.98c37.1-9.75,85.07,19.31,116.2,20.91,27.57,1.41,85.15-31.88,120.02-20.91V50.62h11.25v191.23c-39.38-8.64-94.38,20.74-127.02,22.23-29.75,1.36-94.13-31.94-131.7-22.23V50.62h11.25Z"
              />
              <path
                class="cls-1"
                d="M140.61,224.97l-89.99-16.87V33.75c30.08,5.57,64.49,6.3,89.99,25.31v165.92Z"
              />
              <path
                class="cls-1"
                d="M241.85,33.75v174.36l-89.99,16.87V59.06c25.5-19.01,59.91-19.74,89.99-25.31Z"
              />
            </g>
          </g>
        </svg>
      </button>
      <h2 id="a11y-title" class="a11y-title"><?php echo esc_html__('Accessibilité du site', 'a11y-widget'); ?></h2>
      <button
        type="button"
        class="a11y-tutorial-toggle"
        id="a11y-tutorial-toggle"
        aria-expanded="false"
        aria-controls="a11y-tutorial"
      >
        <?php echo esc_html__( 'Aria', 'a11y-widget' ); ?>
      </button>
      <div class="a11y-spacer" aria-hidden="true"></div>
      <button class="a11y-close" id="a11y-close" aria-label="<?php echo esc_attr__('Fermer le module', 'a11y-widget'); ?>">✕</button>
    </header>

    <aside
      id="a11y-info-dialog"
      class="a11y-info-dialog"
      role="dialog"
      aria-modal="false"
      aria-labelledby="a11y-info-title"
      aria-hidden="true"
      hidden
    >
      <div class="a11y-info-dialog__inner">
        <div class="a11y-info-dialog__header" id="a11y-info-handle">
          <h3 class="a11y-info-dialog__title" id="a11y-info-title"><?php echo esc_html__( 'Fenêtre didactique', 'a11y-widget' ); ?></h3>
          <button
            type="button"
            class="a11y-info-dialog__close"
            id="a11y-info-close"
            aria-label="<?php echo esc_attr__( 'Fermer la fenêtre didactique', 'a11y-widget' ); ?>"
          >
            <span aria-hidden="true">✕</span>
          </button>
        </div>
        <div class="a11y-info-dialog__content" id="a11y-info-content">
          <nav
            class="a11y-info-menu"
            data-role="info-menu"
            aria-label="<?php echo esc_attr__( 'Contenu didactique', 'a11y-widget' ); ?>"
          >
            <ul class="a11y-info-menu__list">
              <li class="a11y-info-menu__item a11y-info-disclosure">
                <button
                  type="button"
                  class="a11y-info-menu__toggle a11y-info-disclosure__toggle"
                  id="a11y-info-menu-toggle-luminosite"
                  aria-expanded="false"
                  aria-controls="a11y-info-menu-panel-luminosite"
                  data-role="info-menu-toggle"
                >
                  <span class="a11y-info-menu__label"><?php echo esc_html__( 'Luminosité', 'a11y-widget' ); ?></span>
                </button>
                <div
                  class="a11y-info-menu__panel a11y-info-disclosure__panel"
                  id="a11y-info-menu-panel-luminosite"
                  role="region"
                  aria-labelledby="a11y-info-menu-toggle-luminosite"
                  aria-hidden="true"
                  hidden
                  data-role="info-menu-panel"
                >
                  <div class="a11y-info-panel__body">
                    <p class="a11y-info-panel__intro"><?php echo esc_html__( 'Module luminosité', 'a11y-widget' ); ?></p>
                    <h4 class="a11y-info-heading"><?php echo esc_html__( 'Module Luminosité - Accessibilité Modulaire', 'a11y-widget' ); ?></h4>
                    <div class="a11y-info-submenus">
                      <div class="a11y-info-submenu a11y-info-disclosure a11y-info-disclosure--nested">
                        <button
                          type="button"
                          class="a11y-info-submenu__toggle a11y-info-disclosure__toggle"
                          id="a11y-info-submenu-toggle-luminosite-description"
                          aria-expanded="false"
                          aria-controls="a11y-info-submenu-panel-luminosite-description"
                          data-role="info-submenu-toggle"
                        >
                          <span><?php echo esc_html__( '💡 Description', 'a11y-widget' ); ?></span>
                        </button>
                        <div
                          class="a11y-info-submenu__panel a11y-info-disclosure__panel"
                          id="a11y-info-submenu-panel-luminosite-description"
                          role="region"
                          aria-labelledby="a11y-info-submenu-toggle-luminosite-description"
                          aria-hidden="true"
                          hidden
                          data-role="info-submenu-panel"
                        >
                          <p class="a11y-info-panel__text"><?php echo esc_html__( 'Module de gestion de la luminosité et des modes d’affichage permettant aux utilisateurs d’ajuster :', 'a11y-widget' ); ?></p>
                          <ul class="a11y-info-list">
                            <li class="a11y-info-list__item">
                              <p class="a11y-info-list__label"><strong><?php echo esc_html__( 'Mode Normal', 'a11y-widget' ); ?></strong> — <?php echo esc_html__( 'Affichage par défaut du site', 'a11y-widget' ); ?></p>
                            </li>
                            <li class="a11y-info-list__item">
                              <p class="a11y-info-list__label"><strong><?php echo esc_html__( 'Mode nuit', 'a11y-widget' ); ?></strong> : <?php echo esc_html__( 'Fond sombre avec texte clair pour réduire la fatigue oculaire', 'a11y-widget' ); ?></p>
                              <p class="a11y-info-list__detail"><?php echo esc_html__( 'Fond noir (#1a1a1a) avec texte blanc', 'a11y-widget' ); ?></p>
                            </li>
                            <li class="a11y-info-list__item">
                              <p class="a11y-info-list__label"><strong><?php echo esc_html__( 'Mode lumière bleue', 'a11y-widget' ); ?></strong> : <?php echo esc_html__( 'Filtre chaud pour protéger les yeux en soirée', 'a11y-widget' ); ?></p>
                              <p class="a11y-info-list__detail"><?php echo esc_html__( 'Filtre orange chaud (sepia 90 % + hue-rotate -10deg)', 'a11y-widget' ); ?></p>
                            </li>
                            <li class="a11y-info-list__item">
                              <p class="a11y-info-list__label"><strong><?php echo esc_html__( 'Mode contraste élevé', 'a11y-widget' ); ?></strong> : <?php echo esc_html__( 'Contraste AAA pour une meilleure lisibilité', 'a11y-widget' ); ?></p>
                              <p class="a11y-info-list__detail"><?php echo esc_html__( 'Contraste maximum (noir sur blanc ou blanc sur noir)', 'a11y-widget' ); ?></p>
                            </li>
                            <li class="a11y-info-list__item">
                              <p class="a11y-info-list__label"><strong><?php echo esc_html__( 'Mode contraste faible', 'a11y-widget' ); ?></strong> : <?php echo esc_html__( 'Contraste réduit pour les sensibilités visuelles', 'a11y-widget' ); ?></p>
                              <p class="a11y-info-list__detail"><?php echo esc_html__( 'Réduction du contraste pour limiter les sensibilités visuelles', 'a11y-widget' ); ?></p>
                            </li>
                            <li class="a11y-info-list__item">
                              <p class="a11y-info-list__label"><strong><?php echo esc_html__( 'Mode niveaux de gris', 'a11y-widget' ); ?></strong> : <?php echo esc_html__( 'Suppression des couleurs', 'a11y-widget' ); ?></p>
                              <p class="a11y-info-list__detail"><?php echo esc_html__( 'Suppression des couleurs (grayscale 100 %)', 'a11y-widget' ); ?></p>
                            </li>
                            <li class="a11y-info-list__item">
                              <p class="a11y-info-list__label"><strong><?php echo esc_html__( 'Réglages avancés', 'a11y-widget' ); ?></strong> : <?php echo esc_html__( 'Contraste, luminosité et saturation personnalisés', 'a11y-widget' ); ?></p>
                              <ul class="a11y-info-list a11y-info-list--sub">
                                <li class="a11y-info-list__item"><p class="a11y-info-list__detail"><strong><?php echo esc_html__( 'Contraste', 'a11y-widget' ); ?></strong> : <?php echo esc_html__( '50 % - 200 % (défaut : 100 %)', 'a11y-widget' ); ?></p></li>
                                <li class="a11y-info-list__item"><p class="a11y-info-list__detail"><strong><?php echo esc_html__( 'Luminosité', 'a11y-widget' ); ?></strong> : <?php echo esc_html__( '50 % - 150 % (défaut : 100 %)', 'a11y-widget' ); ?></p></li>
                                <li class="a11y-info-list__item"><p class="a11y-info-list__detail"><strong><?php echo esc_html__( 'Saturation', 'a11y-widget' ); ?></strong> : <?php echo esc_html__( '0 % - 200 % (défaut : 100 %)', 'a11y-widget' ); ?></p></li>
                              </ul>
                            </li>
                          </ul>
                        </div>
                      </div>
                      <div class="a11y-info-submenu a11y-info-disclosure a11y-info-disclosure--nested">
                        <button
                          type="button"
                          class="a11y-info-submenu__toggle a11y-info-disclosure__toggle"
                          id="a11y-info-submenu-toggle-luminosite-storage"
                          aria-expanded="false"
                          aria-controls="a11y-info-submenu-panel-luminosite-storage"
                          data-role="info-submenu-toggle"
                        >
                          <span><?php echo esc_html__( '💾 Persistance des données', 'a11y-widget' ); ?></span>
                        </button>
                        <div
                          class="a11y-info-submenu__panel a11y-info-disclosure__panel"
                          id="a11y-info-submenu-panel-luminosite-storage"
                          role="region"
                          aria-labelledby="a11y-info-submenu-toggle-luminosite-storage"
                          aria-hidden="true"
                          hidden
                          data-role="info-submenu-panel"
                        >
                          <p class="a11y-info-panel__text"><?php echo esc_html__( 'Les préférences sont sauvegardées dans des cookies (durée : 365 jours).', 'a11y-widget' ); ?></p>
                          <p class="a11y-info-panel__note">
                            <strong><?php echo esc_html__( 'IMPORTANT', 'a11y-widget' ); ?></strong>
                            <?php
                            echo wp_kses_post(
                              sprintf(
                                /* translators: 1: <code>localStorage</code>, 2: <code>sessionStorage</code> */
                                __( 'Ce module utilise uniquement des cookies, jamais %1$s ou %2$s.', 'a11y-widget' ),
                                '<code>localStorage</code>',
                                '<code>sessionStorage</code>'
                              )
                            );
                            ?>
                          </p>
                        </div>
                      </div>
                      <div class="a11y-info-submenu a11y-info-disclosure a11y-info-disclosure--nested">
                        <button
                          type="button"
                          class="a11y-info-submenu__toggle a11y-info-disclosure__toggle"
                          id="a11y-info-submenu-toggle-luminosite-css"
                          aria-expanded="false"
                          aria-controls="a11y-info-submenu-panel-luminosite-css"
                          data-role="info-submenu-toggle"
                        >
                          <span><?php echo esc_html__( '🎨 Implémentation CSS', 'a11y-widget' ); ?></span>
                        </button>
                        <div
                          class="a11y-info-submenu__panel a11y-info-disclosure__panel"
                          id="a11y-info-submenu-panel-luminosite-css"
                          role="region"
                          aria-labelledby="a11y-info-submenu-toggle-luminosite-css"
                          aria-hidden="true"
                          hidden
                          data-role="info-submenu-panel"
                        >
                          <p class="a11y-info-panel__text"><?php echo esc_html__( 'Le module applique la propriété CSS filter sur l’élément body.', 'a11y-widget' ); ?></p>
                          <pre class="a11y-info-code"><code><?php echo esc_html( "body {\n    filter: contrast(120%) brightness(110%) saturate(90%);\n}" ); ?></code></pre>
                          <h5 class="a11y-info-subheading"><?php echo esc_html__( 'Mode nuit', 'a11y-widget' ); ?></h5>
                          <pre class="a11y-info-code"><code><?php echo esc_html( "body {\n    background-color: #1a1a1a !important;\n    color: #f5f5f5 !important;\n    filter: invert(1) hue-rotate(180deg);\n}\n\nimg, video, [style*=\"background-image\"] {\n    filter: invert(1) hue-rotate(180deg);\n}" ); ?></code></pre>
                          <h5 class="a11y-info-subheading"><?php echo esc_html__( 'Mode contraste élevé', 'a11y-widget' ); ?></h5>
                          <pre class="a11y-info-code"><code><?php echo esc_html( "body {\n    filter: contrast(200%);\n}\n\n* {\n    border-color: currentColor !important;\n}" ); ?></code></pre>
                        </div>
                      </div>
                      <div class="a11y-info-submenu a11y-info-disclosure a11y-info-disclosure--nested">
                        <button
                          type="button"
                          class="a11y-info-submenu__toggle a11y-info-disclosure__toggle"
                          id="a11y-info-submenu-toggle-luminosite-usecases"
                          aria-expanded="false"
                          aria-controls="a11y-info-submenu-panel-luminosite-usecases"
                          data-role="info-submenu-toggle"
                        >
                          <span><?php echo esc_html__( '🔍 Cas d’usage', 'a11y-widget' ); ?></span>
                        </button>
                        <div
                          class="a11y-info-submenu__panel a11y-info-disclosure__panel"
                          id="a11y-info-submenu-panel-luminosite-usecases"
                          role="region"
                          aria-labelledby="a11y-info-submenu-toggle-luminosite-usecases"
                          aria-hidden="true"
                          hidden
                          data-role="info-submenu-panel"
                        >
                          <ul class="a11y-info-submenu-list">
                            <li class="a11y-info-submenu-item a11y-info-disclosure a11y-info-disclosure--level-2">
                              <button
                                type="button"
                                class="a11y-info-submenu__toggle a11y-info-disclosure__toggle"
                                id="a11y-info-submenu-toggle-luminosite-usecases-malvoyants"
                                aria-expanded="false"
                                aria-controls="a11y-info-submenu-panel-luminosite-usecases-malvoyants"
                                data-role="info-submenu-toggle"
                              >
                                <span><?php echo esc_html__( 'Pour les malvoyants', 'a11y-widget' ); ?></span>
                              </button>
                              <div
                                class="a11y-info-submenu__panel a11y-info-disclosure__panel"
                                id="a11y-info-submenu-panel-luminosite-usecases-malvoyants"
                                role="region"
                                aria-labelledby="a11y-info-submenu-toggle-luminosite-usecases-malvoyants"
                                aria-hidden="true"
                                hidden
                                data-role="info-submenu-panel"
                              >
                                <ul class="a11y-info-list">
                                  <li class="a11y-info-list__item"><p class="a11y-info-list__label"><?php echo esc_html__( 'Mode contraste élevé pour améliorer la lisibilité', 'a11y-widget' ); ?></p></li>
                                  <li class="a11y-info-list__item"><p class="a11y-info-list__label"><?php echo esc_html__( 'Ajustement de la luminosité', 'a11y-widget' ); ?></p></li>
                                </ul>
                              </div>
                            </li>
                            <li class="a11y-info-submenu-item a11y-info-disclosure a11y-info-disclosure--level-2">
                              <button
                                type="button"
                                class="a11y-info-submenu__toggle a11y-info-disclosure__toggle"
                                id="a11y-info-submenu-toggle-luminosite-usecases-photosensibilite"
                                aria-expanded="false"
                                aria-controls="a11y-info-submenu-panel-luminosite-usecases-photosensibilite"
                                data-role="info-submenu-toggle"
                              >
                                <span><?php echo esc_html__( 'Pour la photosensibilité', 'a11y-widget' ); ?></span>
                              </button>
                              <div
                                class="a11y-info-submenu__panel a11y-info-disclosure__panel"
                                id="a11y-info-submenu-panel-luminosite-usecases-photosensibilite"
                                role="region"
                                aria-labelledby="a11y-info-submenu-toggle-luminosite-usecases-photosensibilite"
                                aria-hidden="true"
                                hidden
                                data-role="info-submenu-panel"
                              >
                                <ul class="a11y-info-list">
                                  <li class="a11y-info-list__item"><p class="a11y-info-list__label"><?php echo esc_html__( 'Mode nuit pour réduire l’éblouissement', 'a11y-widget' ); ?></p></li>
                                  <li class="a11y-info-list__item"><p class="a11y-info-list__label"><?php echo esc_html__( 'Réduction du contraste pour les sensibilités visuelles', 'a11y-widget' ); ?></p></li>
                                </ul>
                              </div>
                            </li>
                            <li class="a11y-info-submenu-item a11y-info-disclosure a11y-info-disclosure--level-2">
                              <button
                                type="button"
                                class="a11y-info-submenu__toggle a11y-info-disclosure__toggle"
                                id="a11y-info-submenu-toggle-luminosite-usecases-fatigue"
                                aria-expanded="false"
                                aria-controls="a11y-info-submenu-panel-luminosite-usecases-fatigue"
                                data-role="info-submenu-toggle"
                              >
                                <span><?php echo esc_html__( 'Pour la fatigue oculaire', 'a11y-widget' ); ?></span>
                              </button>
                              <div
                                class="a11y-info-submenu__panel a11y-info-disclosure__panel"
                                id="a11y-info-submenu-panel-luminosite-usecases-fatigue"
                                role="region"
                                aria-labelledby="a11y-info-submenu-toggle-luminosite-usecases-fatigue"
                                aria-hidden="true"
                                hidden
                                data-role="info-submenu-panel"
                              >
                                <ul class="a11y-info-list">
                                  <li class="a11y-info-list__item"><p class="a11y-info-list__label"><?php echo esc_html__( 'Mode lumière bleue en fin de journée', 'a11y-widget' ); ?></p></li>
                                  <li class="a11y-info-list__item"><p class="a11y-info-list__label"><?php echo esc_html__( 'Mode nuit pour une utilisation prolongée', 'a11y-widget' ); ?></p></li>
                                </ul>
                              </div>
                            </li>
                            <li class="a11y-info-submenu-item a11y-info-disclosure a11y-info-disclosure--level-2">
                              <button
                                type="button"
                                class="a11y-info-submenu__toggle a11y-info-disclosure__toggle"
                                id="a11y-info-submenu-toggle-luminosite-usecases-daltonisme"
                                aria-expanded="false"
                                aria-controls="a11y-info-submenu-panel-luminosite-usecases-daltonisme"
                                data-role="info-submenu-toggle"
                              >
                                <span><?php echo esc_html__( 'Pour le daltonisme', 'a11y-widget' ); ?></span>
                              </button>
                              <div
                                class="a11y-info-submenu__panel a11y-info-disclosure__panel"
                                id="a11y-info-submenu-panel-luminosite-usecases-daltonisme"
                                role="region"
                                aria-labelledby="a11y-info-submenu-toggle-luminosite-usecases-daltonisme"
                                aria-hidden="true"
                                hidden
                                data-role="info-submenu-panel"
                              >
                                <ul class="a11y-info-list">
                                  <li class="a11y-info-list__item"><p class="a11y-info-list__label"><?php echo esc_html__( 'Mode niveaux de gris pour éliminer la confusion des couleurs', 'a11y-widget' ); ?></p></li>
                                  <li class="a11y-info-list__item"><p class="a11y-info-list__label"><?php echo esc_html__( 'Ajustement de la saturation', 'a11y-widget' ); ?></p></li>
                                </ul>
                              </div>
                            </li>
                          </ul>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </aside>

    <div class="a11y-content" id="a11y-content">
      <?php
      $tutorial_intro  = esc_html__( 'Activez chaque fonctionnalité grâce aux raccourcis clavier ci-dessous.', 'a11y-widget' );
      $tutorial_legend = esc_html__( 'Windows / Linux : Alt + touche — macOS : Ctrl + Option + touche', 'a11y-widget' );
      $tutorial_empty  = esc_html__( 'Aucun raccourci n’est disponible pour le moment.', 'a11y-widget' );
      ?>
      <section
        class="a11y-tutorial"
        id="a11y-tutorial"
        role="region"
        aria-labelledby="a11y-tutorial-title"
        hidden
        aria-hidden="true"
        data-role="tutorial"
        data-platform-win-label="<?php echo esc_attr__( 'Windows / Linux', 'a11y-widget' ); ?>"
        data-platform-mac-label="<?php echo esc_attr__( 'macOS', 'a11y-widget' ); ?>"
        data-shortcut-win-pattern="<?php echo esc_attr__( 'Alt + %s', 'a11y-widget' ); ?>"
        data-shortcut-mac-pattern="<?php echo esc_attr__( 'Ctrl + Option + %s', 'a11y-widget' ); ?>"
      >
        <h3 class="a11y-tutorial__title" id="a11y-tutorial-title"><?php echo esc_html__( 'Aria', 'a11y-widget' ); ?></h3>
        <p class="a11y-tutorial__intro"><?php echo $tutorial_intro; ?></p>
        <p class="a11y-tutorial__legend" data-role="tutorial-legend"><?php echo $tutorial_legend; ?></p>
        <ul class="a11y-tutorial__list" data-role="tutorial-list"></ul>
        <p class="a11y-tutorial__empty" data-role="tutorial-empty" hidden><?php echo $tutorial_empty; ?></p>
      </section>

      <?php
      $search_label       = esc_html__( 'Rechercher une fonctionnalité', 'a11y-widget' );
      $search_placeholder = esc_attr__( 'Rechercher une fonctionnalité…', 'a11y-widget' );
      $search_empty       = esc_html__( 'Aucun résultat ne correspond à votre recherche pour le moment.', 'a11y-widget' );
      ?>
      <form class="a11y-search" role="search" data-role="search-form">
        <label class="a11y-search__label" for="a11y-search"><?php echo $search_label; ?></label>
        <input
          type="search"
          id="a11y-search"
          class="a11y-search__input"
          placeholder="<?php echo $search_placeholder; ?>"
          autocomplete="off"
        />
      </form>

      <section
        class="a11y-search-results"
        data-role="search-results"
        hidden
        aria-hidden="true"
        aria-live="polite"
      >
        <h3 class="a11y-search-results__title" data-sr-only><?php echo esc_html__( 'Résultats de recherche', 'a11y-widget' ); ?></h3>
        <div class="a11y-search-results__list" data-role="search-list"></div>
        <p class="a11y-empty" data-role="search-empty" hidden><?php echo $search_empty; ?></p>
      </section>

      <?php $sections = a11y_widget_get_sections(); ?>
      <?php if ( ! empty( $sections ) ) : ?>
        <?php
        $tablist_id  = 'a11y-section-tabs';
        $tabpanel_id = 'a11y-section-panel';
        $template_id = 'a11y-feature-template';
        $payload     = array();
        ?>
        <nav
          id="<?php echo esc_attr( $tablist_id ); ?>"
          class="a11y-tabs"
          role="tablist"
          aria-label="<?php echo esc_attr__( 'Catégories d’accessibilité', 'a11y-widget' ); ?>"
          data-role="section-tablist"
        >
          <?php foreach ( $sections as $index => $section ) :
            $section_slug  = ! empty( $section['slug'] ) ? sanitize_title( $section['slug'] ) : '';
            $section_id    = $section_slug ? $section_slug : ( ! empty( $section['id'] ) ? sanitize_title( $section['id'] ) : sanitize_title( uniqid( 'a11y-sec-', true ) ) );
            $section_title = isset( $section['title'] ) ? $section['title'] : '';
            $children      = isset( $section['children'] ) ? (array) $section['children'] : array();
            $features_data = array();
            $section_icon  = isset( $section['icon'] ) ? sanitize_key( $section['icon'] ) : '';
            $icon_markup   = '';

            if ( ! empty( $children ) ) {
                foreach ( $children as $feature ) {
                    $feature_slug       = isset( $feature['slug'] ) ? sanitize_title( $feature['slug'] ) : '';
                    $feature_label      = isset( $feature['label'] ) ? $feature['label'] : '';
                    $feature_hint       = isset( $feature['hint'] ) ? $feature['hint'] : '';
                    $feature_aria_label = isset( $feature['aria_label'] ) ? $feature['aria_label'] : $feature_label;

                    if ( '' === $feature_slug || '' === $feature_label ) {
                        continue;
                    }

                    $children_payload = array();
                    if ( isset( $feature['children'] ) && is_array( $feature['children'] ) ) {
                        foreach ( $feature['children'] as $sub_feature ) {
                            $sub_slug       = isset( $sub_feature['slug'] ) ? sanitize_title( $sub_feature['slug'] ) : '';
                            $sub_label      = isset( $sub_feature['label'] ) ? $sub_feature['label'] : '';
                            $sub_hint       = isset( $sub_feature['hint'] ) ? $sub_feature['hint'] : '';
                            $sub_aria_label = isset( $sub_feature['aria_label'] ) ? $sub_feature['aria_label'] : $sub_label;

                            if ( '' === $sub_slug || '' === $sub_label ) {
                                continue;
                            }

                            $children_payload[] = array(
                                'slug'       => $sub_slug,
                                'label'      => wp_strip_all_tags( $sub_label ),
                                'hint'       => wp_strip_all_tags( $sub_hint ),
                                'aria_label' => wp_strip_all_tags( $sub_aria_label ),
                            );
                        }
                    }

                    $feature_payload = array(
                        'slug'       => $feature_slug,
                        'label'      => wp_strip_all_tags( $feature_label ),
                        'hint'       => wp_strip_all_tags( $feature_hint ),
                        'aria_label' => wp_strip_all_tags( $feature_aria_label ),
                    );

                    if ( isset( $feature['template'] ) ) {
                        $feature_template = sanitize_key( $feature['template'] );
                        if ( '' !== $feature_template ) {
                            $feature_payload['template'] = $feature_template;
                        }
                    }

                    if ( isset( $feature['settings'] ) && is_array( $feature['settings'] ) ) {
                        $settings_payload = array();
                        foreach ( $feature['settings'] as $setting_key => $setting_value ) {
                            $setting_slug = sanitize_key( $setting_key );

                            if ( '' === $setting_slug ) {
                                continue;
                            }

                            if ( is_scalar( $setting_value ) ) {
                                $settings_payload[ $setting_slug ] = wp_strip_all_tags( (string) $setting_value );
                            }
                        }

                        if ( ! empty( $settings_payload ) ) {
                            $feature_payload['settings'] = $settings_payload;
                        }
                    }

                    if ( ! empty( $children_payload ) ) {
                        $feature_payload['children'] = $children_payload;
                    }

                    $features_data[] = $feature_payload;
                }
            }

            if ( '' !== $section_icon && function_exists( 'a11y_widget_get_icon_markup' ) ) {
                $icon_markup = a11y_widget_get_icon_markup(
                    $section_icon,
                    array(
                        'class' => 'a11y-tab__icon-svg',
                    )
                );
            }

            $payload[] = array(
                'index'    => (int) $index,
                'id'       => $section_id,
                'slug'     => $section_slug ? $section_slug : $section_id,
                'title'    => wp_strip_all_tags( $section_title ),
                'icon'     => $section_icon,
                'features' => $features_data,
            );

            $tab_id   = 'a11y-tab-' . $section_id;
            $panel_id = 'a11y-panel-' . $section_id;
            $is_first = 0 === (int) $index;
            $tab_class = 'a11y-tab';
            ?>
            <div class="a11y-tab-item" role="presentation" data-role="tab-item" data-section-id="<?php echo esc_attr( $section_id ); ?>">
              <button
                type="button"
                class="<?php echo esc_attr( $tab_class ); ?>"
                role="tab"
                id="<?php echo esc_attr( $tab_id ); ?>"
                aria-selected="false"
                aria-controls="<?php echo esc_attr( $panel_id ); ?>"
                tabindex="<?php echo $is_first ? '0' : '-1'; ?>"
                data-role="section-tab"
                data-section-index="<?php echo esc_attr( $index ); ?>"
                data-section-id="<?php echo esc_attr( $section_id ); ?>"
                data-tablist-id="<?php echo esc_attr( $tablist_id ); ?>"
                <?php if ( '' !== $section_icon ) : ?>
                  data-section-icon="<?php echo esc_attr( $section_icon ); ?>"
                <?php endif; ?>
              >
                <?php if ( '' !== $icon_markup ) : ?>
                  <span class="a11y-tab__icon" aria-hidden="true"><?php echo $icon_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
                <?php endif; ?>
                <span class="a11y-tab__label"><?php echo esc_html( $section_title ); ?></span>
              </button>
              <section
                class="a11y-section-panel"
                role="tabpanel"
                id="<?php echo esc_attr( $panel_id ); ?>"
                tabindex="0"
                aria-live="polite"
                data-role="section-panel"
                data-section-id="<?php echo esc_attr( $section_id ); ?>"
                aria-labelledby="<?php echo esc_attr( $tab_id ); ?>"
                hidden aria-hidden="true"
              >
                <div class="a11y-grid" data-role="feature-grid"></div>
                <p class="a11y-empty" data-role="feature-empty" hidden><?php echo esc_html__( 'Aucune fonctionnalité disponible pour le moment.', 'a11y-widget' ); ?></p>
              </section>
            </div>
          <?php endforeach; ?>
        </nav>

        <template id="<?php echo esc_attr( $template_id ); ?>" data-role="feature-placeholder-template">
          <article class="a11y-card" data-role="feature-card">
            <div class="meta" data-role="feature-meta">
              <span class="label" data-role="feature-label"></span>
            </div>
            <label class="a11y-switch" data-role="feature-switch">
              <input type="checkbox" data-role="feature-input" data-feature="" aria-label="" />
              <span class="track"></span><span class="thumb"></span>
            </label>
          </article>
        </template>

        <script type="application/json" data-role="feature-data">
          <?php echo wp_json_encode( $payload ); ?>
        </script>
      <?php else : ?>
        <p class="a11y-empty"><?php echo esc_html__( 'Aucune fonctionnalité disponible pour le moment.', 'a11y-widget' ); ?></p>
      <?php endif; ?>
    </div>

    <footer class="a11y-footer">
      <div>
        <button class="a11y-btn" id="a11y-reset"><?php echo esc_html__('Réinitialiser', 'a11y-widget'); ?></button>
      </div>
      <div>
        <button class="a11y-btn primary" id="a11y-close2"><?php echo esc_html__('Fermer', 'a11y-widget'); ?></button>
      </div>
    </footer>
  </section>
</div>

<div
  class="a11y-overlay"
  id="a11y-overlay"
  role="presentation"
  aria-hidden="true"
  data-a11y-filter-exempt
  data-background-mode="<?php echo esc_attr( $background_mode ); ?>"
></div>
