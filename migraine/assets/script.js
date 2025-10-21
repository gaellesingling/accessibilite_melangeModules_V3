/**
 * Module Soulagement Migraines - JavaScript
 * Réduit les déclencheurs visuels des migraines ophtalmiques
 * Version: 1.6.0 (Suppression sépia + boutons +/- fonctionnels)
 * 
 * IMPORTANT: Utilise uniquement les cookies (pas localStorage)
 */

(function($) {
    'use strict';

    /**
     * Classe du module Soulagement Migraines
     */
    class MigraineModule {
        constructor() {
            this.module = $('#acc-module-migraine');
            this.toggle = $('#acc-migraine-toggle');
            this.content = $('#acc-migraine-content');
            
            // Contrôles
            this.featureToggles = this.module.find('.acc-feature-input');
            this.colorThemeSelect = $('#acc-migraine-color-theme');
            this.intensitySlider = $('#acc-migraine-color-theme-intensity');
            this.intensityGroup = $('#acc-migraine-intensity-group');
            this.intensityDecrease = $('#acc-migraine-intensity-decrease');
            this.intensityIncrease = $('#acc-migraine-intensity-increase');
            
            // Boutons presets
            this.presetMildBtn = $('#acc-migraine-preset-mild');
            this.presetModerateBtn = $('#acc-migraine-preset-moderate');
            this.presetStrongBtn = $('#acc-migraine-preset-strong');
            this.presetCrisisBtn = $('#acc-migraine-preset-crisis');
            this.resetBtn = $('#acc-migraine-reset');
            
            this.settings = this.getDefaultSettings();
            this.isActive = false;
            
            this.init();
        }

        /**
         * Initialisation
         */
        init() {
            this.loadSettings();
            this.bindEvents();
            this.updateUI();
            
            if (this.isActive) {
                this.applyAllSettings();
            }
            console.log('✓ Module Soulagement Migraines initialisé', this.settings);
        }

        /**
         * Paramètres par défaut
         */
        getDefaultSettings() {
            return {
                darkMode: false,
                brightness: 100,
                blueLight: 0,
                saturation: 100,
                contrast: 100,
                colorTheme: 'none',
                colorThemeIntensity: 100, 
                removePatterns: false,
                increaseSpacing: false
            };
        }

        /**
         * Liaison des événements
         */
        bindEvents() {
            this.toggle.on('change', () => this.handleToggle());
            this.featureToggles.on('change', (e) => this.handleFeatureToggle(e));
            this.colorThemeSelect.on('change', () => this.handleColorTheme());
            this.intensitySlider.on('input', () => this.handleColorThemeIntensity());
            
            // Boutons +/- pour l'intensité
            this.intensityDecrease.on('click', () => this.decreaseIntensity());
            this.intensityIncrease.on('click', () => this.increaseIntensity());
            
            // Presets
            this.presetMildBtn.on('click', () => this.applyPreset('mild'));
            this.presetModerateBtn.on('click', () => this.applyPreset('moderate'));
            this.presetStrongBtn.on('click', () => this.applyPreset('strong'));
            this.presetCrisisBtn.on('click', () => this.applyPreset('crisis'));
            
            this.resetBtn.on('click', () => this.reset());
        }

        /**
         * Gère l'activation/désactivation du module
         */
        handleToggle() {
            this.isActive = this.toggle.is(':checked');
            
            if (this.isActive) {
                this.content.slideDown(300);
                this.applyAllSettings();
                this.savePreference('active', true);
                this.announce('Module soulagement migraines activé');
                console.log('✓ Soulagement migraines activé');
            } else {
                this.content.slideUp(300);
                this.removeAllSettings();
                this.savePreference('active', false);
                this.announce('Module soulagement migraines désactivé');
                console.log('✓ Soulagement migraines désactivé');
            }
        }

        /**
         * Gère tous les interrupteurs (toggles)
         */
        handleFeatureToggle(e) {
            const $input = $(e.currentTarget);
            const feature = $input.data('feature');

            if (feature === 'remove-patterns') {
                this.handleRemovePatterns($input);
            }
        }
        
        /**
         * Gère le thème de couleur
         */
        handleColorTheme() {
            const newTheme = this.colorThemeSelect.val();
            this.settings.colorTheme = newTheme;
            
            // Afficher/masquer le slider d'intensité (seulement pour amber)
            if (newTheme === 'amber') {
                this.intensityGroup.slideDown(200);
            } else {
                this.intensityGroup.slideUp(200);
            }
            
            this.applyColorTheme(); 
            this.savePreference('color_theme', this.settings.colorTheme);
            this.announce('Thème changé en ' + this.settings.colorTheme);
        }

        /**
         * Gère le slider d'intensité du thème
         */
        handleColorThemeIntensity() {
            this.settings.colorThemeIntensity = parseInt(this.intensitySlider.val());
            $('#acc-migraine-intensity-value').text(this.settings.colorThemeIntensity + '%');
            this.intensitySlider.attr('aria-valuenow', this.settings.colorThemeIntensity)
                                .attr('aria-valuetext', this.settings.colorThemeIntensity + ' pourcent');
            this.applyColorTheme(); 
            this.savePreference('color_theme_intensity', this.settings.colorThemeIntensity);
        }

        /**
         * Diminue l'intensité du thème
         */
        decreaseIntensity() {
            let currentValue = parseInt(this.intensitySlider.val());
            const step = parseInt(this.intensitySlider.attr('step')) || 10;
            const min = parseInt(this.intensitySlider.attr('min')) || 0;
            
            let newValue = currentValue - step;
            if (newValue < min) newValue = min;
            
            this.intensitySlider.val(newValue).trigger('input');
            this.announce('Intensité diminuée à ' + newValue + '%');
        }

        /**
         * Augmente l'intensité du thème
         */
        increaseIntensity() {
            let currentValue = parseInt(this.intensitySlider.val());
            const step = parseInt(this.intensitySlider.attr('step')) || 10;
            const max = parseInt(this.intensitySlider.attr('max')) || 100;
            
            let newValue = currentValue + step;
            if (newValue > max) newValue = max;
            
            this.intensitySlider.val(newValue).trigger('input');
            this.announce('Intensité augmentée à ' + newValue + '%');
        }

        /**
         * Gère la suppression des motifs
         */
        handleRemovePatterns(inputElement) {
            const isChecked = $(inputElement).is(':checked');
            this.settings.removePatterns = isChecked;
            this.applyRemovePatterns();
            this.savePreference('remove_patterns', this.settings.removePatterns);
            this.announce(isChecked ? 'Motifs supprimés' : 'Motifs restaurés');
        }

        /**
         * Applique tous les paramètres
         */
        applyAllSettings() {
            this.applyDarkMode();
            this.applyFilters();
            this.applyColorTheme();
            this.applyRemovePatterns();
            this.applyIncreaseSpacing();
            console.log('✓ Tous les réglages appliqués');
        }
        
        /**
         * Applique le mode sombre
         */
        applyDarkMode() {
            $('#acc-migraine-darkmode-style').remove();
            if (this.settings.darkMode) {
                const css = `
                    html { filter: invert(1) hue-rotate(180deg) !important; }
                    img, video, iframe, [style*="background-image"], picture, figure { filter: invert(1) hue-rotate(180deg) !important; }
                `;
                $('<style>', { id: 'acc-migraine-darkmode-style', html: css }).appendTo('head');
                console.log('✓ Mode sombre appliqué');
            }
        }

        /**
         * Applique tous les filtres (luminosité, lumière bleue, saturation, contraste)
         */
        applyFilters() {
            $('#acc-migraine-filters-style').remove();
            const brightness = this.settings.brightness / 100;
            const saturation = this.settings.saturation / 100;
            const contrast = this.settings.contrast / 100;
            const blueLightIntensity = this.settings.blueLight;
            const sepiaValue = blueLightIntensity / 100 * 0.3;
            let filters = [];
            if (brightness !== 1) filters.push(`brightness(${brightness})`);
            if (saturation !== 1) filters.push(`saturate(${saturation})`);
            if (contrast !== 1) filters.push(`contrast(${contrast})`);
            if (blueLightIntensity > 0) {
                filters.push(`sepia(${sepiaValue})`);
                filters.push(`hue-rotate(-10deg)`);
            }
            if (filters.length > 0) {
                const css = `html { filter: ${filters.join(' ')} !important; }`;
                $('<style>', { id: 'acc-migraine-filters-style', html: css }).appendTo('head');
                console.log('✓ Filtres appliqués:', filters.join(', '));
            }
        }

        /**
         * Applique le thème de couleur
         */
        applyColorTheme() {
            $('#acc-migraine-theme-style').remove();
            
            let baseCss = `
                html { isolation: isolate; }
                body::after {
                    content: ''; position: fixed; top: 0; left: 0;
                    width: 100vw; height: 100vh;
                    pointer-events: none; z-index: 9999999;
                    display: none; transition: opacity 0.3s ease;
                }
            `;

            let filterCss = ''; 
            let overlayCss = ''; 
            const intensity = this.settings.colorThemeIntensity / 100; 

            switch (this.settings.colorTheme) {
                case 'grayscale':
                    filterCss = 'html { filter: grayscale(1) !important; }';
                    break;
                case 'amber':
                    overlayCss = `
                        body::after {
                            display: block;
                            background-color: #FFD699;
                            mix-blend-mode: color;
                            opacity: ${intensity}; 
                        }
                    `;
                    break;
                case 'none':
                default:
                    break;
            }

            $('<style>', {
                id: 'acc-migraine-theme-style',
                html: baseCss + filterCss + overlayCss
            }).appendTo('head');
            
            console.log(`✓ Thème (${this.settings.colorTheme}) appliqué avec intensité ${this.settings.colorThemeIntensity}%`);
        }

        /**
         * Applique la suppression des motifs
         */
        applyRemovePatterns() {
            $('#acc-migraine-patterns-style').remove();
            if (this.settings.removePatterns) {
                const css = `
                    * { background-image: none !important; }
                    img, picture, figure { background-image: unset !important; }
                    body, div, section, article, aside, header, footer, nav { background-image: none !important; }
                `;
                $('<style>', { id: 'acc-migraine-patterns-style', html: css }).appendTo('head');
                console.log('✓ Motifs supprimés');
            }
        }

        /**
         * Applique l'augmentation de l'espacement
         */
        applyIncreaseSpacing() {
            $('#acc-migraine-spacing-style').remove();
            if (this.settings.increaseSpacing) {
                const css = `
                    body { line-height: 1.8 !important; }
                    p { margin-bottom: 2em !important; }
                    h1, h2, h3, h4, h5, h6 { margin-top: 2em !important; margin-bottom: 1.5em !important; }
                    li { margin-bottom: 1em !important; }
                `;
                $('<style>', { id: 'acc-migraine-spacing-style', html: css }).appendTo('head');
                console.log('✓ Espacement augmenté');
            }
        }

        /**
         * Applique un preset
         */
        applyPreset(presetName) {
            let preset = {};
            const defaultIntensity = 100; 
            
            switch (presetName) {
                case 'mild': 
                    preset = {
                        darkMode: false, brightness: 90, blueLight: 20, saturation: 80, contrast: 95,
                        colorTheme: 'none', colorThemeIntensity: defaultIntensity, removePatterns: false, increaseSpacing: false
                    };
                    this.announce('Preset doux appliqué');
                    break;
                case 'moderate': 
                    preset = {
                        darkMode: false, brightness: 75, blueLight: 40, saturation: 60, contrast: 90,
                        colorTheme: 'amber', colorThemeIntensity: defaultIntensity, removePatterns: true, increaseSpacing: false
                    };
                    this.announce('Preset modéré appliqué');
                    break;
                case 'strong': 
                    preset = {
                        darkMode: true, brightness: 60, blueLight: 60, saturation: 40, contrast: 85,
                        colorTheme: 'amber', colorThemeIntensity: defaultIntensity, removePatterns: true, increaseSpacing: true
                    };
                    this.announce('Preset fort appliqué');
                    break;
                case 'crisis': 
                    preset = {
                        darkMode: true, brightness: 50, blueLight: 80, saturation: 20, contrast: 80,
                        colorTheme: 'grayscale', colorThemeIntensity: defaultIntensity, removePatterns: true, increaseSpacing: true
                    };
                    this.announce('🚨 Preset crise appliqué - Protection maximale');
                    break;
            }
            
            this.settings = preset;
            this.updateUI(); 
            this.applyAllSettings(); 
            this.saveAllSettings(); 
            console.log(`✓ Preset "${presetName}" appliqué`, preset);
        }

        /**
         * Supprime tous les paramètres
         */
        removeAllSettings() {
            $('#acc-migraine-darkmode-style').remove();
            $('#acc-migraine-filters-style').remove();
            $('#acc-migraine-theme-style').remove();
            $('#acc-migraine-patterns-style').remove();
            $('#acc-migraine-spacing-style').remove();
            console.log('✓ Tous les réglages supprimés');
        }

        /**
         * Réinitialise tous les paramètres
         */
        reset() {
            if (confirm('Réinitialiser tous les paramètres de soulagement migraines ?')) {
                this.settings = this.getDefaultSettings(); 
                this.updateUI();
                this.applyAllSettings();
                this.saveAllSettings();
                this.announce('Paramètres réinitialisés');
                console.log('✓ Paramètres réinitialisés');
            }
        }

        /**
         * Met à jour l'interface
         */
        updateUI() {
            this.featureToggles.each((index, el) => {
                const $el = $(el);
                const feature = $el.data('feature');
                const key = feature.replace(/-([a-z])/g, (g) => g[1].toUpperCase());
                if (this.settings.hasOwnProperty(key)) {
                    $el.prop('checked', this.settings[key]);
                }
            });
            this.colorThemeSelect.val(this.settings.colorTheme);
            this.intensitySlider.val(this.settings.colorThemeIntensity);
            $('#acc-migraine-intensity-value').text(this.settings.colorThemeIntensity + '%');
            this.intensitySlider.attr('aria-valuenow', this.settings.colorThemeIntensity)
                                .attr('aria-valuetext', this.settings.colorThemeIntensity + ' pourcent');

            // Afficher/masquer le groupe d'intensité (seulement pour amber)
            if (this.settings.colorTheme === 'amber') {
                this.intensityGroup.show();
            } else {
                this.intensityGroup.hide();
            }
        }

        /**
         * Sauvegarde une préférence
         */
        savePreference(key, value) {
            const cookieName = `acc_migraine_${key}`;
            const cookieValue = JSON.stringify(value);
            const expiryDays = 365;
            const date = new Date();
            date.setTime(date.getTime() + (expiryDays * 24 * 60 * 60 * 1000));
            const expires = "expires=" + date.toUTCString();
            document.cookie = `${cookieName}=${cookieValue};${expires};path=/;SameSite=Lax`;
        }

        /**
         * Récupère une préférence
         */
        getPreference(key, defaultValue) {
            const cookieName = `acc_migraine_${key}`;
            const name = cookieName + "=";
            const decodedCookie = decodeURIComponent(document.cookie);
            const cookieArray = decodedCookie.split(';');
            for (let i = 0; i < cookieArray.length; i++) {
                let cookie = cookieArray[i].trim();
                if (cookie.indexOf(name) === 0) {
                    const value = cookie.substring(name.length, cookie.length);
                    try { return JSON.parse(value); } catch(e) { return value; }
                }
            }
            return defaultValue;
        }

        /**
         * Charge les paramètres
         */
        loadSettings() {
            this.isActive = this.getPreference('active', false);
            this.settings.darkMode = this.getPreference('dark_mode', false);
            this.settings.brightness = this.getPreference('brightness', 100);
            this.settings.blueLight = this.getPreference('blue_light_filter', 0);
            this.settings.saturation = this.getPreference('saturation', 100);
            this.settings.contrast = this.getPreference('contrast', 100);
            this.settings.colorTheme = this.getPreference('color_theme', 'none');
            this.settings.colorThemeIntensity = this.getPreference('color_theme_intensity', 100); 
            this.settings.removePatterns = this.getPreference('remove_patterns', false);
            this.settings.increaseSpacing = this.getPreference('increase_spacing', false);
            this.toggle.prop('checked', this.isActive);
            if (this.isActive) { this.content.show(); }
            console.log('✓ Paramètres chargés:', this.settings);
        }

        /**
         * Sauvegarde tous les paramètres
         */
        saveAllSettings() {
            this.savePreference('active', this.isActive);
            this.savePreference('dark_mode', this.settings.darkMode);
            this.savePreference('brightness', this.settings.brightness);
            this.savePreference('blue_light_filter', this.settings.blueLight);
            this.savePreference('saturation', this.settings.saturation);
            this.savePreference('contrast', this.settings.contrast);
            this.savePreference('color_theme', this.settings.colorTheme);
            this.savePreference('color_theme_intensity', this.settings.colorThemeIntensity); 
            this.savePreference('remove_patterns', this.settings.removePatterns);
            this.savePreference('increase_spacing', this.settings.increaseSpacing);
        }

        /**
         * Annonce pour les lecteurs d'écran
         */
        announce(message) {
            let $announcer = $('#acc-screen-reader-announcer');
            if (!$announcer.length) {
                $announcer = $('<div>', {
                    id: 'acc-screen-reader-announcer',
                    'aria-live': 'polite', 'aria-atomic': 'true',
                    css: { position: 'absolute', left: '-10000px', width: '1px', height: '1px', overflow: 'hidden' }
                }).appendTo('body');
            }
            $announcer.text('');
            setTimeout(() => { $announcer.text(message); }, 100);
        }
    }

    /**
     * Initialisation
     */
    $(document).ready(function() {
        if ($('#acc-module-migraine').length) {
            window.accMigraineModule = new MigraineModule();
            console.log('✓ Module Soulagement Migraines prêt');
        }
    });

})(jQuery);