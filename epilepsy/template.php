<?php
/**
 * Template du module Protection Épilepsie
 * Interface utilisateur pour la prévention des crises
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="acc-module acc-module-epilepsy" id="acc-module-epilepsy" data-module="epilepsy">
    <div class="acc-module-header">
        <h3 class="acc-module-title">
            <span class="acc-module-icon" aria-hidden="true">⚡</span>
            <?php esc_html_e('Protection Épilepsie', 'accessibility-modular'); ?>
        </h3>
        <label class="acc-module-toggle">
            <input 
                type="checkbox" 
                id="acc-epilepsy-toggle"
                aria-label="<?php esc_attr_e('Activer/désactiver la protection épilepsie', 'accessibility-modular'); ?>"
            />
            <span class="acc-module-toggle-slider"></span>
        </label>
    </div>

    <div class="acc-module-content" id="acc-epilepsy-content" style="display: none;">
        
        <div class="acc-control-group">
            <div class="acc-feature-item">
                <div class="acc-feature-header">
                    <span class="acc-feature-icon" aria-hidden="true">🎬</span>
                    <label for="acc-epilepsy-stop-animations" class="acc-control-label">
                        <?php esc_html_e('Arrêter toutes les animations', 'accessibility-modular'); ?>
                    </label>
                </div>
                <label class="acc-module-toggle acc-feature-toggle">
                    <input 
                        type="checkbox" 
                        id="acc-epilepsy-stop-animations"
                        class="acc-feature-input"
                        data-feature="stop-animations"
                        aria-label="<?php esc_attr_e('Activer l\'arrêt des animations', 'accessibility-modular'); ?>"
                    />
                    <span class="acc-module-toggle-slider"></span>
                </label>
            </div>
            <p class="acc-control-hint">
                <?php esc_html_e('Désactive toutes les animations CSS et JavaScript', 'accessibility-modular'); ?>
            </p>
        </div>

        <div class="acc-control-group">
            <div class="acc-feature-item">
                <div class="acc-feature-header">
                    <span class="acc-feature-icon" aria-hidden="true">🖼️</span>
                    <label for="acc-epilepsy-stop-gifs" class="acc-control-label">
                        <?php esc_html_e('Arrêter les GIFs animés', 'accessibility-modular'); ?>
                    </label>
                </div>
                <label class="acc-module-toggle acc-feature-toggle">
                    <input 
                        type="checkbox" 
                        id="acc-epilepsy-stop-gifs"
                        class="acc-feature-input"
                        data-feature="stop-gifs"
                        aria-label="<?php esc_attr_e('Activer l\'arrêt des GIFs', 'accessibility-modular'); ?>"
                    />
                    <span class="acc-module-toggle-slider"></span>
                </label>
            </div>
            <p class="acc-control-hint">
                <?php esc_html_e('Fige les GIFs sur la première image', 'accessibility-modular'); ?>
            </p>
        </div>

        <div class="acc-control-group">
            <div class="acc-feature-item">
                <div class="acc-feature-header">
                    <span class="acc-feature-icon" aria-hidden="true">🎥</span>
                    <label for="acc-epilepsy-stop-videos" class="acc-control-label">
                        <?php esc_html_e('Arrêter les vidéos automatiques', 'accessibility-modular'); ?>
                    </label>
                </div>
                <label class="acc-module-toggle acc-feature-toggle">
                    <input 
                        type="checkbox" 
                        id="acc-epilepsy-stop-videos"
                        class="acc-feature-input"
                        data-feature="stop-videos"
                        aria-label="<?php esc_attr_e('Activer l\'arrêt des vidéos', 'accessibility-modular'); ?>"
                    />
                    <span class="acc-module-toggle-slider"></span>
                </label>
            </div>
            <p class="acc-control-hint">
                <?php esc_html_e('Met en pause les vidéos en lecture automatique', 'accessibility-modular'); ?>
            </p>
        </div>

        <div class="acc-control-group">
            <div class="acc-feature-item">
                <div class="acc-feature-header">
                    <span class="acc-feature-icon" aria-hidden="true">🌊</span>
                    <label for="acc-epilepsy-remove-parallax" class="acc-control-label">
                        <?php esc_html_e('Supprimer les effets parallax', 'accessibility-modular'); ?>
                    </label>
                </div>
                <label class="acc-module-toggle acc-feature-toggle">
                    <input 
                        type="checkbox" 
                        id="acc-epilepsy-remove-parallax"
                        class="acc-feature-input"
                        data-feature="remove-parallax"
                        aria-label="<?php esc_attr_e('Activer la suppression parallax', 'accessibility-modular'); ?>"
                    />
                    <span class="acc-module-toggle-slider"></span>
                </label>
            </div>
            <p class="acc-control-hint">
                <?php esc_html_e('Désactive les effets de défilement parallaxe', 'accessibility-modular'); ?>
            </p>
        </div>

        <div class="acc-control-group">
            <div class="acc-feature-item">
                <div class="acc-feature-header">
                    <span class="acc-feature-icon" aria-hidden="true">⏸️</span>
                    <label for="acc-epilepsy-reduce-motion" class="acc-control-label">
                        <?php esc_html_e('Réduire tous les mouvements', 'accessibility-modular'); ?>
                    </label>
                </div>
                <label class="acc-module-toggle acc-feature-toggle">
                    <input 
                        type="checkbox" 
                        id="acc-epilepsy-reduce-motion"
                        class="acc-feature-input"
                        data-feature="reduce-motion"
                        aria-label="<?php esc_attr_e('Activer la réduction de mouvement', 'accessibility-modular'); ?>"
                    />
                    <span class="acc-module-toggle-slider"></span>
                </label>
            </div>
            <p class="acc-control-hint">
                <?php esc_html_e('Active le mode réduction de mouvement globale', 'accessibility-modular'); ?>
            </p>
        </div>

        <div class="acc-control-group">
            <div class="acc-feature-item">
                <div class="acc-feature-header">
                    <span class="acc-feature-icon" aria-hidden="true">⚡</span>
                    <label for="acc-epilepsy-block-flashing" class="acc-control-label">
                        <?php esc_html_e('Bloquer les flashs rapides', 'accessibility-modular'); ?>
                    </label>
                </div>
                <label class="acc-module-toggle acc-feature-toggle">
                    <input 
                        type="checkbox" 
                        id="acc-epilepsy-block-flashing"
                        class="acc-feature-input"
                        data-feature="block-flashing"
                        aria-label="<?php esc_attr_e('Activer le blocage des flashs', 'accessibility-modular'); ?>"
                    />
                    <span class="acc-module-toggle-slider"></span>
                </label>
            </div>
            <p class="acc-control-hint">
                <?php esc_html_e('Détecte et bloque les changements rapides de luminosité', 'accessibility-modular'); ?>
            </p>
        </div>

        <div class="acc-control-group" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd;">
            <button 
                type="button" 
                id="acc-epilepsy-activate-all" 
                class="acc-button acc-button-primary"
                style="width: 100%; background: #d32f2f; color: white; font-weight: bold; padding: 12px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; transition: background 0.2s;"
                aria-label="<?php esc_attr_e('Activer toutes les protections d\'urgence', 'accessibility-modular'); ?>"
            >
                <?php esc_html_e('🚨 ACTIVER TOUTES LES PROTECTIONS', 'accessibility-modular'); ?>
            </button>
            <p class="acc-control-hint" style="text-align: center; margin-top: 10px;">
                <?php esc_html_e('Active instantanément toutes les protections pour une sécurité maximale', 'accessibility-modular'); ?>
            </p>
        </div>

        <div class="acc-control-group" style="margin-top: 15px;">
            <div class="acc-button-group">
                <button 
                    type="button" 
                    id="acc-epilepsy-reset" 
                    class="acc-button"
                    aria-label="<?php esc_attr_e('Réinitialiser les paramètres de protection épilepsie', 'accessibility-modular'); ?>"
                >
                    <?php esc_html_e('Réinitialiser', 'accessibility-modular'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Styles pour les features avec toggle switches */
.acc-feature-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.acc-feature-header {
    display: flex;
    align-items: center;
    gap: 8px;
}

.acc-feature-icon {
    font-size: 20px;
}

.acc-feature-toggle {
    flex-shrink: 0;
}

.acc-control-hint {
    font-size: 12px;
    color: #666;
    margin: 5px 0 0 0;
    line-height: 1.4;
}

.acc-button-primary:hover {
    background: #b71c1c !important;
}

.acc-safety-warning {
    animation: none !imporant;
}
</style>