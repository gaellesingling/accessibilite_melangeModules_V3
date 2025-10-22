<?php
/**
 * Template du module Aide Monophtalmie
 * Interface utilisateur pour la vision monoculaire
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="acc-module acc-module-monophtalmie" id="acc-module-monophtalmie" data-module="monophtalmie">
    <div class="acc-module-header">
        <h3 class="acc-module-title">
            <span class="acc-module-icon" aria-hidden="true">👁️</span>
            <?php esc_html_e('Aide Monophtalmie', 'accessibility-modular'); ?>
        </h3>
        <label class="acc-module-toggle">
            <input 
                type="checkbox" 
                id="acc-monophtalmie-toggle"
                aria-label="<?php esc_attr_e('Activer/désactiver l\'aide monophtalmie', 'accessibility-modular'); ?>"
            />
            <span class="acc-module-toggle-slider"></span>
        </label>
    </div>

    <div class="acc-module-content" id="acc-monophtalmie-content" style="display: none;">
        
        <div class="acc-info-box" style="background: #e0f2fe; border-left: 4px solid #0284c7; padding: 12px; margin-bottom: 20px; border-radius: 4px;">
            <p style="margin: 0; font-size: 13px; color: #075985;">
                <strong>ℹ️ <?php esc_html_e('À savoir', 'accessibility-modular'); ?> :</strong>
                <?php esc_html_e('Ces outils compensent les défis de la vision monoculaire : absence de vision 3D, champ visuel réduit, et difficulté à évaluer les distances.', 'accessibility-modular'); ?>
            </p>
        </div>

        <!-- LOUPE -->
        <div class="acc-control-group">
            <div class="acc-feature-item" style="margin-bottom: 8px;">
                <label for="acc-monophtalmie-magnifier" class="acc-control-label" style="font-weight: 500;">
                    <?php esc_html_e('🔍 Loupe', 'accessibility-modular'); ?>
                </label>
                <label class="acc-module-toggle acc-feature-toggle">
                    <input 
                        type="checkbox" 
                        id="acc-monophtalmie-magnifier" 
                        class="acc-feature-input"
                        data-feature="magnifier"
                        aria-describedby="acc-monophtalmie-magnifier-desc"
                    />
                    <span class="acc-module-toggle-slider"></span>
                </label>
            </div>
            <p id="acc-monophtalmie-magnifier-desc" class="acc-control-description">
                <?php esc_html_e('Active une loupe qui suit le curseur pour agrandir le contenu', 'accessibility-modular'); ?>
            </p>
        </div>

        <!-- ZOOM DE LA LOUPE -->
        <div class="acc-control-group" id="acc-monophtalmie-zoom-group" style="display: none;">
             <label for="acc-monophtalmie-magnifier-zoom" class="acc-control-label">
                <?php esc_html_e('Zoom de la loupe', 'accessibility-modular'); ?>
                <span class="acc-control-value" id="acc-monophtalmie-zoom-value">200%</span>
            </label>
            <div class="acc-slider-container">
                <button 
                    type="button" 
                    class="acc-slider-limit acc-slider-decrease" 
                    id="acc-monophtalmie-zoom-decrease"
                    aria-label="<?php esc_attr_e('Diminuer le zoom', 'accessibility-modular'); ?>"
                >-</button>
                <input 
                    type="range" 
                    id="acc-monophtalmie-magnifier-zoom" 
                    class="acc-slider"
                    min="150" 
                    max="400" 
                    step="50" 
                    value="200"
                    aria-label="<?php esc_attr_e('Ajuster le zoom de la loupe', 'accessibility-modular'); ?>"
                    aria-valuemin="150"
                    aria-valuemax="400"
                    aria-valuenow="200"
                    aria-valuetext="200 pourcent"
                />
                <button 
                    type="button" 
                    class="acc-slider-limit acc-slider-increase" 
                    id="acc-monophtalmie-zoom-increase"
                    aria-label="<?php esc_attr_e('Augmenter le zoom', 'accessibility-modular'); ?>"
                >+</button>
            </div>
            <p class="acc-control-description">
                <?php esc_html_e('Ajuste le niveau d\'agrandissement (150% à 400%)', 'accessibility-modular'); ?>
            </p>
        </div>

        <!-- INDICATEURS DE PROFONDEUR -->
        <div class="acc-control-group">
            <div class="acc-feature-item" style="margin-bottom: 8px;">
                <label for="acc-monophtalmie-depth" class="acc-control-label" style="font-weight: 500;">
                    <?php esc_html_e('📐 Indicateurs de profondeur', 'accessibility-modular'); ?>
                </label>
                <label class="acc-module-toggle acc-feature-toggle">
                    <input 
                        type="checkbox" 
                        id="acc-monophtalmie-depth" 
                        class="acc-feature-input"
                        data-feature="depth-indicators"
                        aria-describedby="acc-monophtalmie-depth-desc"
                    />
                    <span class="acc-module-toggle-slider"></span>
                </label>
            </div>
            <p id="acc-monophtalmie-depth-desc" class="acc-control-description">
                <?php esc_html_e('Accentue les ombres et contours pour améliorer la perception 3D', 'accessibility-modular'); ?>
            </p>
        </div>

        <!-- CHAMP VISUEL RÉDUIT -->
        <div class="acc-control-group">
            <div class="acc-feature-item" style="margin-bottom: 8px;">
                <label for="acc-monophtalmie-field" class="acc-control-label" style="font-weight: 500;">
                    <?php esc_html_e('👀 Adapter le champ visuel', 'accessibility-modular'); ?>
                </label>
                <label class="acc-module-toggle acc-feature-toggle">
                    <input 
                        type="checkbox" 
                        id="acc-monophtalmie-field" 
                        class="acc-feature-input"
                        data-feature="reduce-field"
                        aria-describedby="acc-monophtalmie-field-desc"
                    />
                    <span class="acc-module-toggle-slider"></span>
                </label>
            </div>
            <p id="acc-monophtalmie-field-desc" class="acc-control-description">
                <?php esc_html_e('Réduit la largeur du contenu pour compenser le champ visuel réduit', 'accessibility-modular'); ?>
            </p>
        </div>

        <!-- POSITION DU CONTENU (nouveau) -->
        <div class="acc-control-group" id="acc-monophtalmie-position-group" style="display: none;">
            <label class="acc-control-label" style="font-weight: 500; margin-bottom: 8px; display: block;">
                <?php esc_html_e('📍 Position du contenu', 'accessibility-modular'); ?>
            </label>
            <div class="acc-position-buttons" role="radiogroup" aria-label="<?php esc_attr_e('Choisir la position du contenu', 'accessibility-modular'); ?>">
                <button 
                    type="button" 
                    class="acc-position-btn" 
                    data-position="left"
                    role="radio"
                    aria-checked="false"
                    aria-label="<?php esc_attr_e('Positionner à gauche', 'accessibility-modular'); ?>"
                >
                    <span class="acc-position-icon">⬅️</span>
                    <span class="acc-position-label"><?php esc_html_e('Gauche', 'accessibility-modular'); ?></span>
                </button>
                <button 
                    type="button" 
                    class="acc-position-btn acc-position-active" 
                    data-position="center"
                    role="radio"
                    aria-checked="true"
                    aria-label="<?php esc_attr_e('Centrer', 'accessibility-modular'); ?>"
                >
                    <span class="acc-position-icon">↔️</span>
                    <span class="acc-position-label"><?php esc_html_e('Centre', 'accessibility-modular'); ?></span>
                </button>
                <button 
                    type="button" 
                    class="acc-position-btn" 
                    data-position="right"
                    role="radio"
                    aria-checked="false"
                    aria-label="<?php esc_attr_e('Positionner à droite', 'accessibility-modular'); ?>"
                >
                    <span class="acc-position-icon">➡️</span>
                    <span class="acc-position-label"><?php esc_html_e('Droite', 'accessibility-modular'); ?></span>
                </button>
            </div>
            <p class="acc-control-description" style="margin-top: 8px;">
                <?php esc_html_e('Positionne le contenu du côté de votre œil valide pour faciliter la lecture', 'accessibility-modular'); ?>
            </p>
        </div>

        <!-- MODE VISION BASSE -->
        <div class="acc-control-group">
            <div class="acc-feature-item" style="margin-bottom: 8px;">
                <label for="acc-monophtalmie-low-vision" class="acc-control-label" style="font-weight: 500;">
                    <?php esc_html_e('💡 Mode vision basse', 'accessibility-modular'); ?>
                </label>
                <label class="acc-module-toggle acc-feature-toggle">
                    <input 
                        type="checkbox" 
                        id="acc-monophtalmie-low-vision" 
                        class="acc-feature-input"
                        data-feature="low-vision-mode"
                        aria-describedby="acc-monophtalmie-low-vision-desc"
                    />
                    <span class="acc-module-toggle-slider"></span>
                </label>
            </div>
            <p id="acc-monophtalmie-low-vision-desc" class="acc-control-description">
                <?php esc_html_e('Contraste élevé, texte agrandi et interface simplifiée pour vision basse', 'accessibility-modular'); ?>
            </p>
        </div>

        <div class="acc-control-group" style="margin-top: 15px;">
            <div class="acc-button-group">
                <button 
                    type="button" 
                    id="acc-monophtalmie-reset" 
                    class="acc-button"
                    aria-label="<?php esc_attr_e('Réinitialiser les paramètres', 'accessibility-modular'); ?>"
                >
                    <?php esc_html_e('Réinitialiser', 'accessibility-modular'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Styles pour les toggles et sliders */
.acc-module-toggle {
    position: relative;
    display: inline-block;
    width: 44px;
    height: 24px;
}

.acc-module-toggle input {
    opacity: 0;
    width: 0;
    height: 0;
}

.acc-module-toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 24px;
}

.acc-module-toggle-slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .acc-module-toggle-slider {
    background-color: #2196F3;
}

input:checked + .acc-module-toggle-slider:before {
    transform: translateX(20px);
}

input:focus + .acc-module-toggle-slider {
    box-shadow: 0 0 1px #2196F3;
}

.acc-slider-container {
    display: flex;
    align-items: center;
    gap: 8px;
}

.acc-slider-container .acc-slider {
    flex-grow: 1;
    margin: 0;
}

.acc-slider-limit {
    font-size: 1.4em;
    font-weight: bold;
    color: #666;
    background: #f5f5f5;
    border: 1px solid #ddd;
    border-radius: 4px;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
    user-select: none;
}

.acc-slider-limit:hover {
    background: #e0e0e0;
    border-color: #bbb;
}

.acc-slider-limit:active {
    background: #d0d0d0;
    transform: scale(0.95);
}

.acc-slider-limit:focus {
    outline: 2px solid #2196F3;
    outline-offset: 2px;
}

.acc-feature-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.acc-feature-toggle {
    flex-shrink: 0;
}

.acc-control-description {
    font-size: 12px;
    color: #666;
    margin: 5px 0 0 0;
    line-height: 1.4;
}

/* NOUVEAU: Styles pour les boutons de position */
.acc-position-buttons {
    display: flex;
    gap: 8px;
    margin-top: 8px;
}

.acc-position-btn {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 12px 8px;
    background: #f5f5f5;
    border: 2px solid #ddd;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
    min-height: 70px;
}

.acc-position-btn:hover {
    background: #e8e8e8;
    border-color: #2196F3;
}

.acc-position-btn:focus {
    outline: 2px solid #2196F3;
    outline-offset: 2px;
}

.acc-position-btn.acc-position-active {
    background: #2196F3;
    border-color: #2196F3;
    color: white;
}

.acc-position-icon {
    font-size: 24px;
    margin-bottom: 4px;
    display: block;
}

.acc-position-label {
    font-size: 12px;
    font-weight: 500;
    text-transform: uppercase;
}

.acc-position-btn.acc-position-active .acc-position-label {
    color: white;
}
</style>
