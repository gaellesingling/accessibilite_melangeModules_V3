/**
 * Module Protection Épilepsie - JavaScript
 * Prévient les crises en bloquant animations et flashs
 * Version: 1.0.0
 * 
 * IMPORTANT: Utilise uniquement les cookies (pas localStorage)
 */

(function($) {
    'use strict';

    /**
     * Classe du module Protection Épilepsie
     */
    class EpilepsyModule {
        constructor() {
            this.module = $('#acc-module-epilepsy');
            this.toggle = $('#acc-epilepsy-toggle');
            this.content = $('#acc-epilepsy-content');
            
            // Contrôles - NOUVEAUX SÉLECTEURS
            this.featureInputs = $('.acc-feature-input');
            
            // Boutons
            this.activateAllBtn = $('#acc-epilepsy-activate-all');
            this.resetBtn = $('#acc-epilepsy-reset');
            
            this.settings = this.getDefaultSettings();
            this.isActive = false;
            this.flashDetector = null;
            this.gifController = null;
            
            this.init();
        }

        /**
         * Initialisation
         */
        init() {
            this.loadSettings();
            this.bindEvents();
            this.updateUI();
            
            // Applique les paramètres sauvegardés
            if (this.isActive) {
                this.applyAllSettings();
            }
            
            console.log('✓ Module Protection Épilepsie initialisé', this.settings);
        }

        /**
         * Paramètres par défaut
         */
        getDefaultSettings() {
            return {
                stopAnimations: false,
                stopGifs: false,
                stopVideos: false,
                removeParallax: false,
                reduceMotion: false,
                blockFlashing: false
            };
        }

        /**
         * Liaison des événements
         */
        bindEvents() {
            // Toggle du module
            this.toggle.on('change', () => this.handleToggle());
            
            // Toggle switches des features
            this.featureInputs.on('change', (e) => this.handleFeatureToggle(e));
            
            // Boutons
            this.activateAllBtn.on('click', () => this.activateAll());
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
                this.announce('Module protection épilepsie activé');
                console.log('✓ Protection épilepsie activée');
            } else {
                this.content.slideUp(300);
                this.removeAllSettings();
                this.savePreference('active', false);
                this.announce('Module protection épilepsie désactivé');
                console.log('✓ Protection épilepsie désactivée');
            }
        }

        /**
         * Gère le toggle d'une feature
         */
        handleFeatureToggle(e) {
            const $input = $(e.currentTarget);
            const featureName = $input.data('feature');
            const isEnabled = $input.is(':checked');
            
            // Convertit le nom de feature en camelCase pour les settings
            const settingKey = this.featureNameToSettingKey(featureName);
            
            this.settings[settingKey] = isEnabled;
            this.applySetting(settingKey, isEnabled);
            this.savePreference(featureName, isEnabled);
            
            const message = isEnabled ? `${featureName} activé` : `${featureName} désactivé`;
            this.announce(message);
        }

        /**
         * Convertit le nom de feature en clé de setting
         */
        featureNameToSettingKey(featureName) {
            // stop-animations -> stopAnimations
            return featureName.replace(/-([a-z])/g, (g) => g[1].toUpperCase());
        }

        /**
         * Applique un paramètre spécifique
         */
        applySetting(settingKey, isEnabled) {
            switch(settingKey) {
                case 'stopAnimations':
                    this.applyStopAnimations();
                    break;
                case 'stopGifs':
                    this.applyStopGifs();
                    break;
                case 'stopVideos':
                    this.applyStopVideos();
                    break;
                case 'removeParallax':
                    this.applyRemoveParallax();
                    break;
                case 'reduceMotion':
                    this.applyReduceMotion();
                    break;
                case 'blockFlashing':
                    this.applyBlockFlashing();
                    break;
            }
        }

        /**
         * Active toutes les protections d'urgence
         */
        activateAll() {
            // Confirmation
            if (!confirm('⚠️ Activer TOUTES les protections contre l\'épilepsie ?\n\nCela va :\n- Arrêter toutes les animations\n- Bloquer les GIFs\n- Mettre en pause les vidéos\n- Supprimer les effets parallax\n- Réduire tous les mouvements\n- Bloquer les flashs')) {
                return;
            }
            
            // Active tout
            this.settings = {
                stopAnimations: true,
                stopGifs: true,
                stopVideos: true,
                removeParallax: true,
                reduceMotion: true,
                blockFlashing: true
            };
            
            this.updateUI();
            this.applyAllSettings();
            this.saveAllSettings();
            this.announce('🚨 Toutes les protections activées !');
            console.log('🚨 MODE PROTECTION MAXIMALE ACTIVÉ');
        }

        /**
         * Applique tous les paramètres
         */
        applyAllSettings() {
            this.applyStopAnimations();
            this.applyStopGifs();
            this.applyStopVideos();
            this.applyRemoveParallax();
            this.applyReduceMotion();
            this.applyBlockFlashing();
            console.log('✓ Toutes les protections appliquées');
        }

        /**
         * Applique l'arrêt des animations
         */
        applyStopAnimations() {
            $('#acc-epilepsy-animations-style').remove();
            
            if (this.settings.stopAnimations) {
                const css = `
                    /* Arrêt de toutes les animations */
                    *, *::before, *::after {
                        animation-duration: 0s !important;
                        animation-delay: 0s !important;
                        animation-iteration-count: 1 !important;
                        transition-duration: 0s !important;
                        transition-delay: 0s !important;
                    }
                    
                    /* Arrêt des keyframes */
                    @keyframes * {
                        0%, 100% {
                            opacity: 1;
                        }
                    }
                `;
                
                $('<style>', {
                    id: 'acc-epilepsy-animations-style',
                    html: css
                }).appendTo('head');
                
                console.log('✓ Animations arrêtées');
            }
        }

        /**
         * Applique l'arrêt des GIFs
         */
        applyStopGifs() {
            if (this.settings.stopGifs) {
                this.freezeAllGifs();
                
                // Observer les nouveaux GIFs ajoutés dynamiquement
                if (!this.gifController) {
                    this.gifController = new MutationObserver(() => {
                        this.freezeAllGifs();
                    });
                    
                    this.gifController.observe(document.body, {
                        childList: true,
                        subtree: true
                    });
                }
                
                console.log('✓ GIFs figés');
            } else {
                if (this.gifController) {
                    this.gifController.disconnect();
                    this.gifController = null;
                }
                this.unfreezeAllGifs();
            }
        }

        /**
         * Fige tous les GIFs
         */
        freezeAllGifs() {
            $('img[src$=".gif"]').each(function() {
                const $img = $(this);
                
                // Marquer comme figé
                if (!$img.data('acc-frozen')) {
                    const originalSrc = $img.attr('src');
                    $img.data('acc-original-src', originalSrc);
                    $img.data('acc-frozen', true);
                    
                    // Créer un canvas pour capturer la première frame
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    const img = new Image();
                    
                    img.crossOrigin = 'anonymous';
                    img.onload = function() {
                        canvas.width = img.width;
                        canvas.height = img.height;
                        ctx.drawImage(img, 0, 0);
                        
                        try {
                            const frozenSrc = canvas.toDataURL('image/png');
                            $img.attr('src', frozenSrc);
                        } catch(e) {
                            console.warn('Impossible de figer le GIF:', originalSrc);
                        }
                    };
                    img.src = originalSrc;
                }
            });
        }

        /**
         * Dégèle tous les GIFs
         */
        unfreezeAllGifs() {
            $('img[data-acc-frozen="true"]').each(function() {
                const $img = $(this);
                const originalSrc = $img.data('acc-original-src');
                
                if (originalSrc) {
                    $img.attr('src', originalSrc);
                    $img.removeData('acc-frozen');
                    $img.removeData('acc-original-src');
                }
            });
        }

        /**
         * Applique l'arrêt des vidéos
         */
        applyStopVideos() {
            if (this.settings.stopVideos) {
                // Arrêter toutes les vidéos
                $('video, iframe[src*="youtube"], iframe[src*="vimeo"]').each(function() {
                    const $elem = $(this);
                    
                    if ($elem.is('video')) {
                        $elem[0].pause();
                        $elem.attr('autoplay', false);
                    } else if ($elem.is('iframe')) {
                        const src = $elem.attr('src');
                        if (src && src.includes('autoplay=1')) {
                            $elem.attr('src', src.replace('autoplay=1', 'autoplay=0'));
                        }
                    }
                });
                
                console.log('✓ Vidéos arrêtées');
            }
        }

        /**
         * Applique la suppression des effets parallax
         */
        applyRemoveParallax() {
            $('#acc-epilepsy-parallax-style').remove();
            
            if (this.settings.removeParallax) {
                const css = `
                    /* Suppression des effets parallax */
                    * {
                        background-attachment: scroll !important;
                        transform: none !important;
                        will-change: auto !important;
                    }
                    
                    [data-parallax],
                    .parallax,
                    .parallax-bg,
                    .jarallax {
                        transform: none !important;
                        position: static !important;
                    }
                `;
                
                $('<style>', {
                    id: 'acc-epilepsy-parallax-style',
                    html: css
                }).appendTo('head');
                
                console.log('✓ Parallax supprimé');
            }
        }

        /**
         * Applique la réduction de mouvement
         */
        applyReduceMotion() {
            $('#acc-epilepsy-motion-style').remove();
            
            if (this.settings.reduceMotion) {
                const css = `
                    /* Réduction globale des mouvements */
                    @media (prefers-reduced-motion: reduce) {
                        *, *::before, *::after {
                            animation-duration: 0.01ms !important;
                            animation-iteration-count: 1 !important;
                            transition-duration: 0.01ms !important;
                            scroll-behavior: auto !important;
                        }
                    }
                    
                    /* Force la réduction même sans media query */
                    * {
                        scroll-behavior: auto !important;
                    }
                    
                    html {
                        scroll-behavior: auto !important;
                    }
                `;
                
                $('<style>', {
                    id: 'acc-epilepsy-motion-style',
                    html: css
                }).appendTo('head');
                
                console.log('✓ Mouvements réduits');
            }
        }

        /**
         * Applique le blocage des flashs
         */
        applyBlockFlashing() {
            if (this.settings.blockFlashing) {
                this.startFlashDetection();
                console.log('✓ Détection de flashs activée');
            } else {
                this.stopFlashDetection();
            }
        }

        /**
         * Démarre la détection de flashs
         */
        startFlashDetection() {
            if (this.flashDetector) return;
            
            let lastBrightness = null;
            let flashWindow = [];
            
            this.flashDetector = setInterval(() => {
                // Calcule la luminosité moyenne de l'écran
                const brightness = this.calculateScreenBrightness();
                
                if (lastBrightness !== null) {
                    const change = Math.abs(brightness - lastBrightness);
                    
                    // Si changement > 20%, c'est un flash potentiel
                    if (change > 0.2) {
                        flashWindow.push(Date.now());
                        
                        // Garde seulement les flashs des dernières 1000ms
                        flashWindow = flashWindow.filter(time => Date.now() - time < 1000);
                        
                        // Si plus de 3 flashs en 1 seconde = DANGER
                        if (flashWindow.length > 3) {
                            this.handleFlashDetected();
                            flashWindow = [];
                        }
                    }
                }
                
                lastBrightness = brightness;
            }, 100); // Vérifie toutes les 100ms
        }

        /**
         * Calcule la luminosité moyenne de l'écran
         */
        calculateScreenBrightness() {
            // Méthode simplifiée - en production, utiliseriez un canvas
            const body = document.body;
            const computedStyle = window.getComputedStyle(body);
            const bgColor = computedStyle.backgroundColor;
            
            // Parse RGB
            const rgb = bgColor.match(/\d+/g);
            if (rgb) {
                const r = parseInt(rgb[0]);
                const g = parseInt(rgb[1]);
                const b = parseInt(rgb[2]);
                
                // Formule de luminosité relative
                return (0.299 * r + 0.587 * g + 0.114 * b) / 255;
            }
            
            return 0.5;
        }

        /**
         * Gère la détection d'un flash
         */
        handleFlashDetected() {
            console.warn('⚠️ FLASH DÉTECTÉ ! Protection activée');
            
            // Affiche un overlay de protection
            const $overlay = $('<div>', {
                id: 'acc-flash-overlay',
                css: {
                    position: 'fixed',
                    top: 0,
                    left: 0,
                    width: '100%',
                    height: '100%',
                    background: 'rgba(0, 0, 0, 0.95)',
                    zIndex: 999999,
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    color: 'white',
                    fontSize: '24px',
                    textAlign: 'center',
                    padding: '20px'
                },
                html: '<div><strong>⚠️ FLASH DÉTECTÉ</strong><br><br>La page a été bloquée pour votre sécurité.<br><br><button id="acc-dismiss-flash" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">Fermer</button></div>'
            });
            
            $('body').append($overlay);
            
            $('#acc-dismiss-flash').on('click', function() {
                $overlay.fadeOut(500, function() {
                    $(this).remove();
                });
            });
            
            this.announce('ALERTE : Flash détecté et bloqué !');
        }

        /**
         * Arrête la détection de flashs
         */
        stopFlashDetection() {
            if (this.flashDetector) {
                clearInterval(this.flashDetector);
                this.flashDetector = null;
                console.log('✓ Détection de flashs désactivée');
            }
        }

        /**
         * Supprime tous les paramètres
         */
        removeAllSettings() {
            $('#acc-epilepsy-animations-style').remove();
            $('#acc-epilepsy-parallax-style').remove();
            $('#acc-epilepsy-motion-style').remove();
            
            if (this.gifController) {
                this.gifController.disconnect();
                this.gifController = null;
            }
            
            this.unfreezeAllGifs();
            this.stopFlashDetection();
            
            console.log('✓ Toutes les protections désactivées');
        }

        /**
         * Réinitialise tous les paramètres
         */
        reset() {
            if (confirm('Réinitialiser tous les paramètres de protection épilepsie ?')) {
                this.settings = this.getDefaultSettings();
                this.updateUI();
                this.applyAllSettings();
                this.saveAllSettings();
                this.announce('Paramètres de protection réinitialisés');
                console.log('✓ Paramètres réinitialisés');
            }
        }

        /**
         * Met à jour l'interface
         */
        updateUI() {
            $('#acc-epilepsy-stop-animations').prop('checked', this.settings.stopAnimations);
            $('#acc-epilepsy-stop-gifs').prop('checked', this.settings.stopGifs);
            $('#acc-epilepsy-stop-videos').prop('checked', this.settings.stopVideos);
            $('#acc-epilepsy-remove-parallax').prop('checked', this.settings.removeParallax);
            $('#acc-epilepsy-reduce-motion').prop('checked', this.settings.reduceMotion);
            $('#acc-epilepsy-block-flashing').prop('checked', this.settings.blockFlashing);
        }

        /**
         * Sauvegarde une préférence
         */
        savePreference(key, value) {
            const cookieName = `acc_epilepsy_${key.replace(/-/g, '_')}`;
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
            const cookieName = `acc_epilepsy_${key.replace(/-/g, '_')}`;
            const name = cookieName + "=";
            const decodedCookie = decodeURIComponent(document.cookie);
            const cookieArray = decodedCookie.split(';');
            
            for (let i = 0; i < cookieArray.length; i++) {
                let cookie = cookieArray[i].trim();
                if (cookie.indexOf(name) === 0) {
                    const value = cookie.substring(name.length, cookie.length);
                    try {
                        return JSON.parse(value);
                    } catch(e) {
                        return value;
                    }
                }
            }
            
            return defaultValue;
        }

        /**
         * Charge les paramètres
         */
        loadSettings() {
            this.isActive = this.getPreference('active', false);
            this.settings.stopAnimations = this.getPreference('stop_animations', false);
            this.settings.stopGifs = this.getPreference('stop_gifs', false);
            this.settings.stopVideos = this.getPreference('stop_videos', false);
            this.settings.removeParallax = this.getPreference('remove_parallax', false);
            this.settings.reduceMotion = this.getPreference('reduce_motion', false);
            this.settings.blockFlashing = this.getPreference('block_flashing', false);
            
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
            this.savePreference('stop-animations', this.settings.stopAnimations);
            this.savePreference('stop-gifs', this.settings.stopGifs);
            this.savePreference('stop-videos', this.settings.stopVideos);
            this.savePreference('remove-parallax', this.settings.removeParallax);
            this.savePreference('reduce-motion', this.settings.reduceMotion);
            this.savePreference('block-flashing', this.settings.blockFlashing);
        }

        /**
         * Annonce pour les lecteurs d'écran
         */
        announce(message) {
            let $announcer = $('#acc-screen-reader-announcer');
            if (!$announcer.length) {
                $announcer = $('<div>', {
                    id: 'acc-screen-reader-announcer',
                    'aria-live': 'assertive',
                    'aria-atomic': 'true',
                    css: {
                        position: 'absolute',
                        left: '-10000px',
                        width: '1px',
                        height: '1px',
                        overflow: 'hidden'
                    }
                }).appendTo('body');
            }
            
            $announcer.text('');
            setTimeout(() => {
                $announcer.text(message);
            }, 100);
        }
    }

    /**
     * Initialisation
     */
    $(document).ready(function() {
        if ($('#acc-module-epilepsy').length) {
            window.accEpilepsyModule = new EpilepsyModule();
            console.log('✓ Module Protection Épilepsie prêt');
        }
    });

})(jQuery);