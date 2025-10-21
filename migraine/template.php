<?php
/**
 * Template du module Soulagement Migraines
 * Interface utilisateur pour réduire les déclencheurs visuels
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="acc-module acc-module-migraine" id="acc-module-migraine" data-module="migraine">
    <div class="acc-module-header">
        <h3 class="acc-module-title">
            <span class="acc-module-icon" aria-hidden="true">🧠</span>
            <?php esc_html_e('Soulagement Migraines', 'accessibility-modular'); ?>
        </h3>
        <label class="acc-module-toggle">
            <input 
                type="checkbox" 
                id="acc-migraine-toggle"
                aria-label="<?php esc_attr_e('Activer/désactiver le soulagement migraines', 'accessibility-modular'); ?>"
            />
            <span class="acc-module-toggle-slider"></span>
        </label>
    </div>

    <div class="acc-module-content" id="acc-migraine-content" style="display: none;">
        
        <div class="acc-info-box" style="background: #e3f2fd; border-left: 4px solid #2196f3; padding: 12px; margin-bottom: 20px; border-radius: 4px;">
            <p style="margin: 0; font-size: 13px; color: #1565c0;">
                <strong>ℹ️ <?php esc_html_e('À savoir', 'accessibility-modular'); ?> :</strong>
                <?php esc_html_e('Ces réglages réduisent les déclencheurs visuels courants des migraines ophtalmiques : lumière intense, couleurs vives, motifs répétitifs.', 'accessibility-modular'); ?>
            </p>
        </div>

        <div class="acc-control-group">
            <label for="acc-migraine-color-theme" class="acc-control-label">
                <?php esc_html_e('Thème de couleur', 'accessibility-modular'); ?>
            </label>
            <select 
                id="acc-migraine-color-theme" 
                class="acc-select"
                aria-label="<?php esc_attr_e('Sélectionner un thème de couleur', 'accessibility-modular'); ?>"
            >
                <option value="none"><?php esc_html_e('Aucun', 'accessibility-modular'); ?></option>
                <option value="grayscale"><?php esc_html_e('Niveaux de gris', 'accessibility-modular'); ?></option>
                <option value="amber"><?php esc_html_e('Teinte chaude/ambrée', 'accessibility-modular'); ?></option>
            </select>
            <p class="acc-control-description">
                <?php esc_html_e('Applique une teinte colorée apaisante sur toute la page', 'accessibility-modular'); ?>
            </p>
        </div>

        <div class="acc-control-group" id="acc-migraine-intensity-group" style="display: none;">
             <label for="acc-migraine-color-theme-intensity" class="acc-control-label">
                <?php esc_html_e('Intensité du thème', 'accessibility-modular'); ?>
                <span class="acc-control-value" id="acc-migraine-intensity-value">100%</span>
            </label>
            <div class="acc-slider-container">
                <button 
                    type="button" 
                    class="acc-slider-limit acc-slider-decrease" 
                    id="acc-migraine-intensity-decrease"
                    aria-label="<?php esc_attr_e('Diminuer l\'intensité', 'accessibility-modular'); ?>"
                >-</button>
                <input 
                    type="range" 
                    id="acc-migraine-color-theme-intensity" 
                    class="acc-slider"
                    min="0" 
                    max="100" 
                    step="10" 
                    value="100"
                    aria-label="<?php esc_attr_e('Ajuster l\'intensité du thème', 'accessibility-modular'); ?>"
                    aria-valuemin="0"
                    aria-valuemax="100"
                    aria-valuenow="100"
                    aria-valuetext="100 pourcent"
                />
                <button 
                    type="button" 
                    class="acc-slider-limit acc-slider-increase" 
                    id="acc-migraine-intensity-increase"
                    aria-label="<?php esc_attr_e('Augmenter l\'intensité', 'accessibility-modular'); ?>"
                >+</button>
            </div>
            <p class="acc-control-description">
                <?php esc_html_e('Ajuste l\'intensité du thème Ambré', 'accessibility-modular'); ?>
            </p>
        </div>


        <div class="acc-control-group">
            <div class="acc-feature-item" style="margin-bottom: 8px;">
                <label for="acc-migraine-remove-patterns" class="acc-control-label" style="font-weight: 500;">
                    <?php esc_html_e('Supprimer les motifs', 'accessibility-modular'); ?>
                </label>
                <label class="acc-module-toggle acc-feature-toggle">
                    <input 
                        type="checkbox" 
                        id="acc-migraine-remove-patterns" 
                        class="acc-feature-input"
                        data-feature="remove-patterns"
                        aria-describedby="acc-migraine-patterns-desc"
                    />
                    <span class="acc-module-toggle-slider"></span>
                </label>
            </div>
            <p id="acc-migraine-patterns-desc" class="acc-control-description">
                <?php esc_html_e('Supprime les arrière-plans à motifs répétitifs (rayures, damiers, etc.)', 'accessibility-modular'); ?>
            </p>
        </div>

        <div class="acc-control-group" style="margin-top: 25px; padding-top: 20px; border-top: 1px solid #ddd;">
            <label class="acc-control-label" style="margin-bottom: 12px; display: block;">
                <?php esc_html_e('Presets rapides', 'accessibility-modular'); ?>
            </label>
            <div class="acc-preset-buttons" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <button 
                    type="button" 
                    id="acc-migraine-preset-mild" 
                    class="acc-button acc-button-secondary"
                    style="padding: 8px 12px; font-size: 13px;"
                    aria-label="<?php esc_attr_e('Appliquer le preset doux', 'accessibility-modular'); ?>"
                >
                    <?php esc_html_e('🌤️ Doux', 'accessibility-modular'); ?>
                </button>
                <button 
                    type="button" 
                    id="acc-migraine-preset-moderate" 
                    class="acc-button acc-button-secondary"
                    style="padding: 8px 12px; font-size: 13px;"
                    aria-label="<?php esc_attr_e('Appliquer le preset modéré', 'accessibility-modular'); ?>"
                >
                    <?php esc_html_e('☁️ Modéré', 'accessibility-modular'); ?>
                </button>
                <button 
                    type="button" 
                    id="acc-migraine-preset-strong" 
                    class="acc-button acc-button-secondary"
                    style="padding: 8px 12px; font-size: 13px;"
                    aria-label="<?php esc_attr_e('Appliquer le preset fort', 'accessibility-modular'); ?>"
                >
                    <?php esc_html_e('🌙 Fort', 'accessibility-modular'); ?>
                </button>
                <button 
                    type="button" 
                    id="acc-migraine-preset-crisis" 
                    class="acc-button"
                    style="padding: 8px 12px; font-size: 13px; background: #f44336; color: white;"
                    aria-label="<?php esc_attr_e('Appliquer le preset crise', 'accessibility-modular'); ?>"
                >
                    <?php esc_html_e('🚨 Crise', 'accessibility-modular'); ?>
                </button>
            </div>
            <p class="acc-control-description" style="margin-top: 10px;">
                <?php esc_html_e('Configurations prédéfinies selon l\'intensité de vos symptômes', 'accessibility-modular'); ?>
            </p>
        </div>

        <div class="acc-control-group" style="margin-top: 15px;">
            <div class="acc-button-group">
                <button 
                    type="button" 
                    id="acc-migraine-reset" 
                    class="acc-button"
                    aria-label="<?php esc_attr_e('Réinitialiser les paramètres de soulagement migraines', 'accessibility-modular'); ?>"
                >
                    <?php esc_html_e('Réinitialiser', 'accessibility-modular'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* ---------------------------------- */
/* STYLES CSS POUR LES TOGGLES */
/* ---------------------------------- */

.acc-module-toggle {
    position: relative;
    display: inline-block;
    width: 44px;
    height: 24px;
}

/* Masquer la checkbox HTML */
.acc-module-toggle input {
    opacity: 0;
    width: 0;
    height: 0;
}

/* Le "slider" (fond de l'interrupteur) */
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

/* Le "curseur" (cercle blanc) */
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

/* État coché : couleur de fond */
input:checked + .acc-module-toggle-slider {
    background-color: #2196F3;
}

/* État coché : position du curseur */
input:checked + .acc-module-toggle-slider:before {
    transform: translateX(20px);
}

/* Style de focus pour l'accessibilité */
input:focus + .acc-module-toggle-slider {
    box-shadow: 0 0 1px #2196F3;
}

/* Styles pour le conteneur du slider */
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

/* ---------------------------------- */
/* STYLES PRÉEXISTANTS */
/* ---------------------------------- */

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

.acc-button-secondary {
    background: #f5f5f5;
    border: 1px solid #ddd;
    color: #333;
}

.acc-button-secondary:hover {
    background: #e0e0e0;
}

.acc-preset-buttons button {
    transition: all 0.2s;
}

.acc-preset-buttons button:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
</style>