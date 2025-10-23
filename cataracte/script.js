/**
 * Module Aide Cataracte - JavaScript
 * Version: 1.0.1 - Compensation des effets de la cataracte
 * 
 * IMPORTANT: Utilise uniquement les cookies (pas localStorage)
 * WIDGET PRÉSERVÉ: Le widget d'accessibilité n'est jamais affecté
 */

(function($) {
    'use strict';

    /**
     * Classe du module Aide Cataracte
     */
    class CataracteModule {
        constructor() {
            this.module = $('#acc-module-cataracte');
            this.toggle = $('#acc-cataracte-toggle');
            this.content = $('#acc-cataracte-content');
            
            // Contrôles - Éblouissement
            this.glareToggle = $('#acc-cataracte-glare');
            this.glareIntensity = $('#acc-cataracte-glare-intensity');
            this.glareGroup = $('#acc-cataracte-glare-intensity-group');
            this.glareDecrease = $('#acc-cataracte-glare-decrease');
            this.glareIncrease = $('#acc-cataracte-glare-increase');
            
            // Contrôles - Autres
            this.colorToggle = $('#acc-cataracte-color');
            this.sharpnessToggle = $('#acc-cataracte-sharpness');
            this.effectsToggle = $('#acc-cataracte-effects');
            
            // Bouton reset
            this.resetBtn = $('#acc-cataracte-reset');
            
            // État
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
            console.log('✓ Module Aide Cataracte initialisé (v1.0.1)', this.settings);
        }

        /**
         * Paramètres par défaut
         */
        getDefaultSettings() {
            return {
                reduceGlare: false,
                glareIntensity: 20,
                colorCorrection: false,
                sharpness: false,
                removeEffects: false
            };
        }

        /**
         * Liaison des événements
         */
        bindEvents() {
            // Toggle principal
            this.toggle.on('change', () => this.handleToggle());
            
            // Éblouissement
            this.glareToggle.on('change', () => {
                this.settings.reduceGlare = this.glareToggle.is(':checked');
                this.applyGlareReduction();
                this.savePreference('reduce_glare', this.settings.reduceGlare);
                
                if (this.settings.reduceGlare) {
                    this.glareGroup.slideDown(200);
                } else {
                    this.glareGroup.slideUp(200);
                }
            });
            
            this.glareIntensity.on('input', () => this.handleGlareChange());
            this.glareDecrease.on('click', () => this.decreaseGlare());
            this.glareIncrease.on('click', () => this.increaseGlare());
            
            // Correction couleurs
            this.colorToggle.on('change', () => {
                this.settings.colorCorrection = this.colorToggle.is(':checked');
                this.applyColorCorrection();
                this.savePreference('color_correction', this.settings.colorCorrection);
            });
            
            // Netteté
            this.sharpnessToggle.on('change', () => {
                this.settings.sharpness = this.sharpnessToggle.is(':checked');
                this.applySharpness();
                this.savePreference('sharpness', this.settings.sharpness);
            });
            
            // Effets
            this.effectsToggle.on('change', () => {
                this.settings.removeEffects = this.effectsToggle.is(':checked');
                this.applyRemoveEffects();
                this.savePreference('remove_effects', this.settings.removeEffects);
            });
            
            // Reset
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
                console.log('✓ Aide cataracte activée');
            } else {
                this.content.slideUp(300);
                this.removeAllSettings();
                this.savePreference('active', false);
                console.log('✓ Aide cataracte désactivée');
            }
        }

        /**
         * Gestion de l'éblouissement
         */
        handleGlareChange() {
            this.settings.glareIntensity = parseInt(this.glareIntensity.val());
            $('#acc-cataracte-glare-intensity-value').text(this.settings.glareIntensity + '%');
            this.applyGlareReduction();
            this.savePreference('glare_intensity', this.settings.glareIntensity);
        }

        decreaseGlare() {
            let current = parseInt(this.glareIntensity.val());
            let newValue = Math.max(10, current - 5);
            this.glareIntensity.val(newValue).trigger('input');
        }

        increaseGlare() {
            let current = parseInt(this.glareIntensity.val());
            let newValue = Math.min(40, current + 5);
            this.glareIntensity.val(newValue).trigger('input');
        }

        /**
         * Applique la réduction de l'éblouissement
         */
        applyGlareReduction() {
            $('#acc-cat-glare-style').remove();
            
            if (this.settings.reduceGlare) {
                const opacity = 1 - (this.settings.glareIntensity / 100);
                const brightness = 1 - (this.settings.glareIntensity / 100) * 0.3;
                
                const css = `
                    <style id="acc-cat-glare-style">
                        /* Réduction de l'éblouissement - SAUF widget */
                        body {
                            background: #f5f5f5 !important;
                        }
                        
                        /* Assombrir tous les éléments lumineux */
                        body *:not(#acc-widget-panel):not(#acc-widget-panel *):not(#acc-magnifier):not(#acc-magnifier *) {
                            filter: brightness(${brightness}) !important;
                        }
                        
                        /* Réduire l'opacité des images et éléments lumineux */
                        img:not(#acc-widget-panel *):not(#acc-magnifier *),
                        video:not(#acc-widget-panel *):not(#acc-magnifier *),
                        [style*="background-image"]:not(#acc-widget-panel):not(#acc-widget-panel *):not(#acc-magnifier):not(#acc-magnifier *) {
                            opacity: ${opacity} !important;
                        }
                        
                        /* Éviter les fonds blancs purs */
                        [style*="background: white"]:not(#acc-widget-panel):not(#acc-widget-panel *),
                        [style*="background: #fff"]:not(#acc-widget-panel):not(#acc-widget-panel *),
                        [style*="background: #ffffff"]:not(#acc-widget-panel):not(#acc-widget-panel *),
                        [style*="background-color: white"]:not(#acc-widget-panel):not(#acc-widget-panel *),
                        [style*="background-color: #fff"]:not(#acc-widget-panel):not(#acc-widget-panel *),
                        [style*="background-color: #ffffff"]:not(#acc-widget-panel):not(#acc-widget-panel *) {
                            background: #f5f5f5 !important;
                        }
                    </style>
                `;
                $('head').append(css);
                console.log(`✓ Éblouissement réduit: ${this.settings.glareIntensity}%`);
            }
        }

        /**
         * Applique la correction des couleurs
         */
        applyColorCorrection() {
            $('#acc-cat-color-style').remove();
            
            if (this.settings.colorCorrection) {
                const css = `
                    <style id="acc-cat-color-style">
                        /* Correction des couleurs - SAUF widget */
                        /* Correction subtile pour compenser le jaunissement de la cataracte */
                        body *:not(#acc-widget-panel):not(#acc-widget-panel *):not(#acc-magnifier):not(#acc-magnifier *) {
                            filter: saturate(1.08) contrast(1.05) !important;
                        }
                        
                        /* Réduction légère du jaunissement sur les fonds clairs */
                        body:not(#acc-widget-panel),
                        div:not(#acc-widget-panel):not(#acc-widget-panel *):not(#acc-magnifier):not(#acc-magnifier *),
                        section:not(#acc-widget-panel):not(#acc-widget-panel *):not(#acc-magnifier):not(#acc-magnifier *),
                        article:not(#acc-widget-panel):not(#acc-widget-panel *):not(#acc-magnifier):not(#acc-magnifier *) {
                            filter: saturate(1.08) contrast(1.05) hue-rotate(-3deg) !important;
                        }
                        
                        /* Amélioration subtile des bleus et violets (souvent difficiles à distinguer avec la cataracte) */
                        [style*="color: navy"]:not(#acc-widget-panel):not(#acc-widget-panel *),
                        [style*="color: #000080"]:not(#acc-widget-panel):not(#acc-widget-panel *),
                        [style*="color: rgb(0, 0, 128)"]:not(#acc-widget-panel):not(#acc-widget-panel *),
                        [style*="color: darkblue"]:not(#acc-widget-panel):not(#acc-widget-panel *) {
                            filter: brightness(1.15) saturate(1.1) !important;
                        }
                        
                        /* Amélioration subtile des violets */
                        [style*="color: purple"]:not(#acc-widget-panel):not(#acc-widget-panel *),
                        [style*="color: #800080"]:not(#acc-widget-panel):not(#acc-widget-panel *),
                        [style*="color: rgb(128, 0, 128)"]:not(#acc-widget-panel):not(#acc-widget-panel *),
                        [style*="color: violet"]:not(#acc-widget-panel):not(#acc-widget-panel *) {
                            filter: brightness(1.1) saturate(1.15) !important;
                        }
                    </style>
                `;
                $('head').append(css);
                console.log('✓ Couleurs corrigées (subtil)');
            }
        }

        /**
         * Applique l'amélioration de la netteté
         */
        applySharpness() {
            $('#acc-cat-sharpness-style').remove();
            
            if (this.settings.sharpness) {
                const css = `
                    <style id="acc-cat-sharpness-style">
                        /* Amélioration de la netteté - SAUF widget */
                        *:not(#acc-widget-panel):not(#acc-widget-panel *):not(#acc-magnifier):not(#acc-magnifier *) {
                            text-rendering: optimizeLegibility !important;
                            -webkit-font-smoothing: antialiased !important;
                            -moz-osx-font-smoothing: grayscale !important;
                        }
                        
                        /* Renforcement du texte */
                        p:not(#acc-widget-panel *):not(#acc-magnifier *),
                        span:not(#acc-widget-panel *):not(#acc-magnifier *),
                        div:not(#acc-widget-panel *):not(#acc-magnifier *),
                        a:not(#acc-widget-panel *):not(#acc-magnifier *),
                        li:not(#acc-widget-panel *):not(#acc-magnifier *) {
                            text-shadow: 0 0 0.5px rgba(0,0,0,0.3) !important;
                            font-weight: 500 !important;
                        }
                        
                        /* Titres plus nets */
                        h1:not(#acc-widget-panel *):not(#acc-magnifier *),
                        h2:not(#acc-widget-panel *):not(#acc-magnifier *),
                        h3:not(#acc-widget-panel *):not(#acc-magnifier *),
                        h4:not(#acc-widget-panel *):not(#acc-magnifier *),
                        h5:not(#acc-widget-panel *):not(#acc-magnifier *),
                        h6:not(#acc-widget-panel *):not(#acc-magnifier *) {
                            text-shadow: 0 0 1px rgba(0,0,0,0.5) !important;
                            font-weight: 700 !important;
                        }
                        
                        /* Images plus nettes */
                        img:not(#acc-widget-panel *):not(#acc-magnifier *) {
                            image-rendering: -webkit-optimize-contrast !important;
                            image-rendering: crisp-edges !important;
                        }
                    </style>
                `;
                $('head').append(css);
                console.log('✓ Netteté améliorée');
            }
        }

        /**
         * Applique la suppression des effets visuels
         */
        applyRemoveEffects() {
            $('#acc-cat-effects-style').remove();
            
            if (this.settings.removeEffects) {
                const css = `
                    <style id="acc-cat-effects-style">
                        /* Suppression des effets - SAUF widget */
                        
                        /* Suppression des animations */
                        *:not(#acc-widget-panel):not(#acc-widget-panel *):not(#acc-magnifier):not(#acc-magnifier *) {
                            animation: none !important;
                            transition: none !important;
                        }
                        
                        /* Suppression des ombres (halos) */
                        *:not(#acc-widget-panel):not(#acc-widget-panel *):not(#acc-magnifier):not(#acc-magnifier *) {
                            box-shadow: none !important;
                            text-shadow: none !important;
                        }
                        
                        /* Suppression des effets de transparence */
                        *:not(#acc-widget-panel):not(#acc-widget-panel *):not(img) {
                            opacity: 1 !important;
                        }
                        
                        /* Suppression des dégradés */
                        *:not(#acc-widget-panel):not(#acc-widget-panel *) {
                            background-image: none !important;
                        }
                        
                        /* Suppression des flous */
                        *:not(#acc-widget-panel):not(#acc-widget-panel *) {
                            backdrop-filter: none !important;
                            -webkit-backdrop-filter: none !important;
                        }
                        
                        /* Bordures simples uniquement */
                        *:not(#acc-widget-panel):not(#acc-widget-panel *) {
                            border-radius: 0 !important;
                        }
                        
                        /* Supprimer les effets de survol qui peuvent créer des halos */
                        *:not(#acc-widget-panel):not(#acc-widget-panel *):hover {
                            transform: none !important;
                            filter: none !important;
                        }
                    </style>
                `;
                $('head').append(css);
                console.log('✓ Effets visuels supprimés');
            }
        }

        /**
         * Applique tous les paramètres
         */
        applyAllSettings() {
            this.applyGlareReduction();
            this.applyColorCorrection();
            this.applySharpness();
            this.applyRemoveEffects();
            console.log('✓ Tous les réglages appliqués');
        }

        /**
         * Supprime tous les paramètres
         */
        removeAllSettings() {
            $('#acc-cat-glare-style').remove();
            $('#acc-cat-color-style').remove();
            $('#acc-cat-sharpness-style').remove();
            $('#acc-cat-effects-style').remove();
            console.log('✓ Tous les réglages supprimés');
        }

        /**
         * Réinitialise tous les paramètres
         */
        reset() {
            if (confirm('Réinitialiser tous les paramètres d\'aide cataracte ?')) {
                this.settings = this.getDefaultSettings();
                this.updateUI();
                this.removeAllSettings();
                this.saveAllSettings();
                console.log('✓ Paramètres réinitialisés');
            }
        }

        /**
         * Met à jour l'interface
         */
        updateUI() {
            // Toggles
            this.glareToggle.prop('checked', this.settings.reduceGlare);
            this.colorToggle.prop('checked', this.settings.colorCorrection);
            this.sharpnessToggle.prop('checked', this.settings.sharpness);
            this.effectsToggle.prop('checked', this.settings.removeEffects);
            
            // Slider
            this.glareIntensity.val(this.settings.glareIntensity);
            $('#acc-cataracte-glare-intensity-value').text(this.settings.glareIntensity + '%');
            
            // Affichage du groupe conditionnel
            if (this.settings.reduceGlare) {
                this.glareGroup.show();
            } else {
                this.glareGroup.hide();
            }
        }

        /**
         * Sauvegarde une préférence
         */
        savePreference(key, value) {
            const cookieName = `acc_cataracte_${key}`;
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
            const cookieName = `acc_cataracte_${key}`;
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
            this.settings.reduceGlare = this.getPreference('reduce_glare', false);
            this.settings.glareIntensity = this.getPreference('glare_intensity', 20);
            this.settings.colorCorrection = this.getPreference('color_correction', false);
            this.settings.sharpness = this.getPreference('sharpness', false);
            this.settings.removeEffects = this.getPreference('remove_effects', false);
            
            this.toggle.prop('checked', this.isActive);
            if (this.isActive) { 
                this.content.show(); 
            }
            console.log('✓ Paramètres chargés:', this.settings);
        }

        /**
         * Sauvegarde tous les paramètres
         */
        saveAllSettings() {
            this.savePreference('active', this.isActive);
            this.savePreference('reduce_glare', this.settings.reduceGlare);
            this.savePreference('glare_intensity', this.settings.glareIntensity);
            this.savePreference('color_correction', this.settings.colorCorrection);
            this.savePreference('sharpness', this.settings.sharpness);
            this.savePreference('remove_effects', this.settings.removeEffects);
        }
    }

    /**
     * Initialisation
     */
    $(document).ready(function() {
        const initModule = function() {
            if ($('#acc-module-cataracte').length) {
                window.accCataracteModule = new CataracteModule();
                console.log('✓ Module Aide Cataracte prêt (v1.0.1)');
            } else {
                console.warn('Module Cataracte: HTML non trouvé');
            }
        };
        
        initModule();
    });

})(jQuery);