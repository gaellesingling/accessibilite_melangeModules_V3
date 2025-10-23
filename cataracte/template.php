<?php
/**
 * Template du module Aide Cataracte
 * Interface utilisateur pour les personnes atteintes de cataracte
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="acc-module acc-module-cataracte" id="acc-module-cataracte" data-module="cataracte">
    <div class="acc-module-header">
        <h3 class="acc-module-title">
            <span class="acc-module-icon" aria-hidden="true">👁️‍🗨️</span>
            <?php esc_html_e('Aide Cataracte', 'accessibility-modular'); ?>
        </h3>
        <label class="acc-module-toggle">
            <input 
                type="checkbox" 
                id="acc-cataracte-toggle"
                aria-label="<?php esc_attr_e('Activer/désactiver l\'aide cataracte', 'accessibility-modular'); ?>"
            />
            <span class="acc-module-toggle-slider"></span>
        </label>
    </div>

    <div class="acc-module-content" id="acc-cataracte-content" style="display: none;">
        
        <div class="acc-info-box" style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 12px; margin-bottom: 20px; border-radius: 4px;">
            <p style="margin: 0; font-size: 13px; color: #856404;">
                <strong>ℹ️ <?php esc_html_e('À savoir', 'accessibility-modular'); ?> :</strong>
                <?php esc_html_e('Ces outils compensent la vision floue, l\'éblouissement et la mauvaise perception des couleurs liés à la cataracte.', 'accessibility-modular'); ?>
            </p>
        </div>

        <!-- RÉDUIRE L'ÉBLOUISSEMENT -->
        <div class="acc-control-group">
            <div class="acc-feature-item" style="margin-bottom: 8px;">
                <label for="acc-cataracte-glare" class="acc-control-label" style="font-weight: 500;">
                    <?php esc_html_e('☀️ Réduire l\'éblouissement', 'accessibility-modular'); ?>
                </label>
                <label class="acc-module-toggle acc-feature-toggle">
                    <input 
                        type="checkbox" 
                        id="acc-cataracte-glare" 
                        class="acc-feature-input"
                        data-feature="reduce-glare"
                        aria-describedby="acc-cataracte-glare-desc"
                    />
                    <span class="acc-module-toggle-slider"></span>
                </label>
            </div>
            <p id="acc-cataracte-glare-desc" class="acc-control-description">
                <?php esc_html_e('Filtre les lumières vives et réduit l\'éblouissement', 'accessibility-modular'); ?>
            </p>
        </div>

        <!-- INTENSITÉ ANTI-ÉBLOUISSEMENT -->
        <div class="acc-control-group" id="acc-cataracte-glare-intensity-group" style="display: none;">
            <label for="acc-cataracte-glare-intensity" class="acc-control-label">
                <?php esc_html_e('Intensité anti-éblouissement', 'accessibility-modular'); ?>
                <span class="acc-control-value" id="acc-cataracte-glare-intensity-value">20%</span>
            </label>
            <div class="acc-slider-container">
                <button 
                    type="button" 
                    class="acc-slider-limit acc-slider-decrease" 
                    id="acc-cataracte-glare-decrease"
                    aria-label="<?php esc_attr_e('Diminuer l\'intensité', 'accessibility-modular'); ?>"
                >-</button>
                <input 
                    type="range" 
                    id="acc-cataracte-glare-intensity" 
                    class="acc-slider"
                    min="10" 
                    max="40" 
                    step="5" 
                    value="20"
                    aria-label="<?php esc_attr_e('Ajuster l\'intensité anti-éblouissement', 'accessibility-modular'); ?>"
                />
                <button 
                    type="button" 
                    class="acc-slider-limit acc-slider-increase" 
                    id="acc-cataracte-glare-increase"
                    aria-label="<?php esc_attr_e('Augmenter l\'intensité', 'accessibility-modular'); ?>"
                >+</button>
            </div>
        </div>

        <!-- CORRECTION DES COULEURS -->
        <div class="acc-control-group">
            <div class="acc-feature-item" style="margin-bottom: 8px;">
                <label for="acc-cataracte-color" class="acc-control-label" style="font-weight: 500;">
                    <?php esc_html_e('🎨 Correction des couleurs', 'accessibility-modular'); ?>
                </label>
                <label class="acc-module-toggle acc-feature-toggle">
                    <input 
                        type="checkbox" 
                        id="acc-cataracte-color" 
                        class="acc-feature-input"
                        data-feature="color-correction"
                        aria-describedby="acc-cataracte-color-desc"
                    />
                    <span class="acc-module-toggle-slider"></span>
                </label>
            </div>
            <p id="acc-cataracte-color-desc" class="acc-control-description">
                <?php esc_html_e('Compense le jaunissement et améliore la distinction des couleurs', 'accessibility-modular'); ?>
            </p>
        </div>

        <!-- AMÉLIORER LA NETTETÉ -->
        <div class="acc-control-group">
            <div class="acc-feature-item" style="margin-bottom: 8px;">
                <label for="acc-cataracte-sharpness" class="acc-control-label" style="font-weight: 500;">
                    <?php esc_html_e('🔍 Améliorer la netteté', 'accessibility-modular'); ?>
                </label>
                <label class="acc-module-toggle acc-feature-toggle">
                    <input 
                        type="checkbox" 
                        id="acc-cataracte-sharpness" 
                        class="acc-feature-input"
                        data-feature="sharpness"
                        aria-describedby="acc-cataracte-sharpness-desc"
                    />
                    <span class="acc-module-toggle-slider"></span>
                </label>
            </div>
            <p id="acc-cataracte-sharpness-desc" class="acc-control-description">
                <?php esc_html_e('Rend le texte et les images plus nets pour compenser le flou', 'accessibility-modular'); ?>
            </p>
        </div>

        <!-- SUPPRIMER LES EFFETS VISUELS -->
        <div class="acc-control-group">
            <div class="acc-feature-item" style="margin-bottom: 8px;">
                <label for="acc-cataracte-effects" class="acc-control-label" style="font-weight: 500;">
                    <?php esc_html_e('✨ Supprimer les effets visuels', 'accessibility-modular'); ?>
                </label>
                <label class="acc-module-toggle acc-feature-toggle">
                    <input 
                        type="checkbox" 
                        id="acc-cataracte-effects" 
                        class="acc-feature-input"
                        data-feature="remove-effects"
                        aria-describedby="acc-cataracte-effects-desc"
                    />
                    <span class="acc-module-toggle-slider"></span>
                </label>
            </div>
            <p id="acc-cataracte-effects-desc" class="acc-control-description">
                <?php esc_html_e('Enlève animations, ombres et effets qui créent des halos', 'accessibility-modular'); ?>
            </p>
        </div>

        <div class="acc-control-group" style="margin-top: 15px;">
            <div class="acc-button-group">
                <button 
                    type="button" 
                    id="acc-cataracte-reset" 
                    class="acc-button"
                    aria-label="<?php esc_attr_e('Réinitialiser les paramètres', 'accessibility-modular'); ?>"
                >
                    <?php esc_html_e('Réinitialiser', 'accessibility-modular'); ?>
                </button>
            </div>
        </div>
    </div>
</div>