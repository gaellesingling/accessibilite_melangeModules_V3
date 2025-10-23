/**
 * Module Aide Monophtalmie - JavaScript
 * Version: 2.4.0 - MODE VISION BASSE RENFORCÉ (Contraste 21:1 Garanti)
 * 
 * IMPORTANT: Utilise uniquement les cookies (pas localStorage)
 */

(function($) {
    'use strict';

    /**
     * Classe du module Aide Monophtalmie
     */
    class MonophtalmieModule {
        constructor() {
            this.module = $('#acc-module-monophtalmie');
            this.toggle = $('#acc-monophtalmie-toggle');
            this.content = $('#acc-monophtalmie-content');
            
            // Contrôles
            this.magnifierToggle = $('#acc-monophtalmie-magnifier');
            this.zoomSlider = $('#acc-monophtalmie-magnifier-zoom');
            this.zoomGroup = $('#acc-monophtalmie-zoom-group');
            this.zoomDecrease = $('#acc-monophtalmie-zoom-decrease');
            this.zoomIncrease = $('#acc-monophtalmie-zoom-increase');
            this.depthToggle = $('#acc-monophtalmie-depth');
            this.fieldToggle = $('#acc-monophtalmie-field');
            this.positionGroup = $('#acc-monophtalmie-position-group');
            this.positionButtons = $('.acc-position-btn');
            this.lowVisionToggle = $('#acc-monophtalmie-low-vision');
            this.resetBtn = $('#acc-monophtalmie-reset');
            
            // État
            this.settings = this.getDefaultSettings();
            this.isActive = false;
            
            // Loupe
            this.magnifier = null;
            this.magnifierContent = null;
            this.magnifierActive = false;
            this.rafId = null;
            
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
            console.log('✓ Module Aide Monophtalmie initialisé (v2.4.0 - Contraste renforcé 21:1)', this.settings);
        }

        /**
         * Paramètres par défaut
         */
        getDefaultSettings() {
            return {
                magnifier: false,
                magnifierZoom: 200,
                depthIndicators: false,
                reduceField: false,
                fieldPosition: 'center',
                lowVisionMode: false
            };
        }

        /**
         * Liaison des événements
         */
        bindEvents() {
            this.toggle.on('change', () => this.handleToggle());
            
            this.magnifierToggle.on('change', () => {
                this.settings.magnifier = this.magnifierToggle.is(':checked');
                this.toggleMagnifier(this.settings.magnifier);
                this.savePreference('magnifier', this.settings.magnifier);
                
                if (this.settings.magnifier) {
                    this.zoomGroup.slideDown(200);
                } else {
                    this.zoomGroup.slideUp(200);
                }
            });
            
            this.depthToggle.on('change', () => {
                this.settings.depthIndicators = this.depthToggle.is(':checked');
                this.applyDepthIndicators();
                this.savePreference('depth_indicators', this.settings.depthIndicators);
            });
            
            this.fieldToggle.on('change', () => {
                this.settings.reduceField = this.fieldToggle.is(':checked');
                this.applyReduceField();
                this.savePreference('reduce_field', this.settings.reduceField);
                
                if (this.settings.reduceField) {
                    this.positionGroup.slideDown(200);
                } else {
                    this.positionGroup.slideUp(200);
                }
            });
            
            this.positionButtons.on('click', (e) => {
                const button = $(e.currentTarget);
                const position = button.data('position');
                this.setFieldPosition(position);
            });
            
            this.lowVisionToggle.on('change', () => {
                this.settings.lowVisionMode = this.lowVisionToggle.is(':checked');
                this.applyLowVisionMode();
                this.savePreference('low_vision_mode', this.settings.lowVisionMode);
            });
            
            this.zoomSlider.on('input', () => this.handleZoomChange());
            this.zoomDecrease.on('click', () => this.decreaseZoom());
            this.zoomIncrease.on('click', () => this.increaseZoom());
            this.resetBtn.on('click', () => this.reset());
        }

        /**
         * Définit la position du champ visuel
         */
        setFieldPosition(position) {
            this.settings.fieldPosition = position;
            
            this.positionButtons.removeClass('acc-position-active').attr('aria-checked', 'false');
            $(`.acc-position-btn[data-position="${position}"]`).addClass('acc-position-active').attr('aria-checked', 'true');
            
            this.applyReduceField();
            this.savePreference('field_position', position);
            
            console.log(`✓ Position du contenu: ${position}`);
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
                console.log('✓ Aide monophtalmie activée');
            } else {
                this.content.slideUp(300);
                this.removeAllSettings();
                this.savePreference('active', false);
                console.log('✓ Aide monophtalmie désactivée');
            }
        }

        /**
         * Gère le changement de zoom
         */
        handleZoomChange() {
            this.settings.magnifierZoom = parseInt(this.zoomSlider.val());
            $('#acc-monophtalmie-zoom-value').text(this.settings.magnifierZoom + '%');
            this.zoomSlider.attr('aria-valuenow', this.settings.magnifierZoom);
            this.savePreference('magnifier_zoom', this.settings.magnifierZoom);
        }

        /**
         * Diminue le zoom
         */
        decreaseZoom() {
            let currentValue = parseInt(this.zoomSlider.val());
            const step = 50;
            const min = 150;
            
            let newValue = currentValue - step;
            if (newValue < min) newValue = min;
            
            this.zoomSlider.val(newValue).trigger('input');
        }

        /**
         * Augmente le zoom
         */
        increaseZoom() {
            let currentValue = parseInt(this.zoomSlider.val());
            const step = 50;
            const max = 400;
            
            let newValue = currentValue + step;
            if (newValue > max) newValue = max;
            
            this.zoomSlider.val(newValue).trigger('input');
        }

        /**
         * Active/désactive la loupe
         */
        toggleMagnifier(enabled) {
            if (enabled) {
                this.createMagnifier();
            } else {
                this.removeMagnifier();
            }
        }

        /**
         * LOUPE OPTIMISÉE
         */
        createMagnifier() {
            if (this.magnifier) {
                return;
            }
            
            const magnifierSize = 200;
            
            this.magnifier = $('<div>', {
                id: 'acc-magnifier',
                css: {
                    position: 'fixed',
                    width: magnifierSize + 'px',
                    height: magnifierSize + 'px',
                    border: '5px solid #2196F3',
                    borderRadius: '50%',
                    pointerEvents: 'none',
                    zIndex: 2147483647,
                    display: 'none',
                    overflow: 'hidden',
                    boxShadow: '0 8px 32px rgba(0,0,0,0.5)',
                    background: '#fff'
                }
            });
            
            this.magnifierContent = $('<div>', {
                css: {
                    position: 'absolute',
                    width: '10000px',
                    height: '10000px',
                    pointerEvents: 'none'
                }
            });
            
            this.magnifier.append(this.magnifierContent);
            $('body').append(this.magnifier);
            
            let updateMagnifier = (e) => {
                if (this.rafId) {
                    cancelAnimationFrame(this.rafId);
                }
                
                this.rafId = requestAnimationFrame(() => {
                    const zoom = this.settings.magnifierZoom / 100;
                    const halfSize = magnifierSize / 2;
                    
                    this.magnifier.css({
                        left: (e.clientX - halfSize) + 'px',
                        top: (e.clientY - halfSize) + 'px',
                        display: 'block'
                    });
                    
                    this.magnifierContent.empty();
                    const clone = $('html').clone();
                    
                    clone.find('#acc-magnifier').remove();
                    clone.find('script').remove();
                    
                    const offsetX = -(e.pageX * zoom - halfSize);
                    const offsetY = -(e.pageY * zoom - halfSize);
                    
                    clone.css({
                        transform: `scale(${zoom})`,
                        transformOrigin: '0 0',
                        position: 'absolute',
                        left: offsetX + 'px',
                        top: offsetY + 'px',
                        width: $('html').outerWidth() + 'px',
                        margin: 0,
                        padding: 0
                    });
                    
                    this.magnifierContent.append(clone);
                });
            };
            
            $(document).on('mousemove.magnifier', updateMagnifier);
            $(document).on('mouseenter.magnifier', () => this.magnifier.fadeIn(100));
            $(document).on('mouseleave.magnifier', () => this.magnifier.fadeOut(100));
            
            this.magnifierActive = true;
            console.log('✓ Loupe activée');
        }

        /**
         * Supprime la loupe
         */
        removeMagnifier() {
            if (this.magnifier) {
                $(document).off('.magnifier');
                if (this.rafId) {
                    cancelAnimationFrame(this.rafId);
                }
                this.magnifier.remove();
                this.magnifier = null;
                this.magnifierContent = null;
                this.magnifierActive = false;
                this.rafId = null;
                console.log('✓ Loupe désactivée');
            }
        }

        /**
         * Applique les indicateurs de profondeur
         */
        applyDepthIndicators() {
            $('#acc-mono-depth-style').remove();
            
            if (this.settings.depthIndicators) {
                const css = `
                    <style id="acc-mono-depth-style">
                        body:not(#acc-widget-panel):not(#acc-widget-panel *) {
                            position: relative !important;
                            background-color: #020617 !important;
                            color: #e2e8f0 !important;
                        }

                        body:not(#acc-widget-panel):not(#acc-widget-panel *)::before {
                            content: '';
                            position: fixed;
                            inset: 0;
                            pointer-events: none;
                            z-index: 2147483600;
                            background: rgba(2, 6, 23, 0.8);
                        }

                        #acc-widget-panel,
                        #acc-widget-panel * {
                            isolation: isolate;
                            mix-blend-mode: normal !important;
                            position: relative;
                            z-index: 2147483647 !important;
                            color: inherit !important;
                        }

                        body:not(#acc-widget-panel):not(#acc-widget-panel *) :where(article, section, main, aside, nav, header, footer, .card, .panel, .box, .content, .container, .wrapper, .module, .layout, .card-body, .card-content) {
                            background-color: #f1f5f9 !important;
                            color: #020617 !important;
                            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.22) !important;
                            border-color: rgba(15, 23, 42, 0.18) !important;
                        }

                        body:not(#acc-widget-panel):not(#acc-widget-panel *) :where(img, video, iframe, figure, picture) {
                            box-shadow: 0 12px 36px rgba(2, 6, 23, 0.42) !important;
                        }

                        body:not(#acc-widget-panel):not(#acc-widget-panel *) :where(.wp-site-blocks a, .entry-content a, .wp-block-button__link, .wp-element-button, .wp-block-navigation__container a) {
                            background-color: #0f172a !important;
                            color: #f8fafc !important;
                            border: 2px solid #1d4ed8 !important;
                            transition: background-color 0.2s ease, box-shadow 0.2s ease, color 0.2s ease;
                            text-decoration: none !important;
                        }

                        body:not(#acc-widget-panel):not(#acc-widget-panel *) :where(.wp-site-blocks a:visited, .entry-content a:visited, .wp-block-button__link:visited, .wp-element-button:visited, .wp-block-navigation__container a:visited) {
                            color: #e2e8f0 !important;
                        }

                        body:not(#acc-widget-panel):not(#acc-widget-panel *) :where(.wp-site-blocks a:hover, .wp-site-blocks a:focus, .entry-content a:hover, .entry-content a:focus, .wp-block-button__link:hover, .wp-block-button__link:focus, .wp-element-button:hover, .wp-element-button:focus, .wp-block-navigation__container a:hover, .wp-block-navigation__container a:focus) {
                            background-color: #1d4ed8 !important;
                            color: #f8fafc !important;
                            box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.55) !important;
                        }

                        body:not(#acc-widget-panel):not(#acc-widget-panel *) :where(h1, h2, h3, h4, h5, h6) {
                            color: #020617 !important;
                            text-shadow: 2px 2px 6px rgba(15, 23, 42, 0.35) !important;
                        }
                    </style>
                `;
                $('head').append(css);
                console.log('✓ Indicateurs de profondeur activés');
            }
        }

        /**
         * Applique la réduction du champ visuel
         */
        applyReduceField() {
            $('#acc-mono-field-style').remove();
            
            if (this.settings.reduceField) {
                let margin = '0 auto';
                
                switch(this.settings.fieldPosition) {
                    case 'left':
                        margin = '0 auto 0 0';
                        break;
                    case 'right':
                        margin = '0 0 0 auto';
                        break;
                    case 'center':
                    default:
                        margin = '0 auto';
                        break;
                }
                
                const css = `
                    <style id="acc-mono-field-style">
                        body {
                            display: flex !important;
                            flex-direction: column !important;
                            align-items: stretch !important;
                        }
                        
                        body > *:not(#acc-widget-panel):not(#acc-magnifier) {
                            max-width: 900px !important;
                            width: 100% !important;
                            margin: ${margin} !important;
                        }
                        
                        body > header,
                        body > footer,
                        body > nav {
                            max-width: 100% !important;
                        }
                        
                        #page, #wrapper, .site, .container, main {
                            max-width: 900px !important;
                            margin: ${margin} !important;
                        }
                        
                        #acc-widget-panel,
                        #acc-magnifier {
                            max-width: none !important;
                            margin: 0 !important;
                        }
                    </style>
                `;
                $('head').append(css);
                console.log(`✓ Champ visuel adapté (position: ${this.settings.fieldPosition})`);
            }
        }

        /**
        /**
         * Applique le mode vision basse
         * CORRIGÉ v2.4.0: Mode vision basse RENFORCÉ - Corrige TOUS les problèmes de contraste
         */
        applyLowVisionMode() {
            $('#acc-mono-lowvision-style').remove();
            
            if (this.settings.lowVisionMode) {
                const css = `
                    <style id="acc-mono-lowvision-style">
                        /* ==========================================
                           MODE VISION BASSE - PAGE UNIQUEMENT
                           Le widget d'accessibilité reste inchangé
                           RENFORCÉ v2.4.0: Cible TOUS les éléments
                           ========================================== */
                        
                        /* Fond noir pour la page */
                        body {
                            background: #000 !important;
                            color: #fff !important;
                        }
                        
                        /* RÈGLE UNIVERSELLE RENFORCÉE - Tous les éléments en blanc SAUF widget */
                        body *:not(#acc-widget-panel):not(#acc-widget-panel *):not(#acc-magnifier):not(#acc-magnifier *) {
                            color: #fff !important;
                            background-color: transparent !important;
                        }
                        
                        /* Cibler spécifiquement TOUS les types de conteneurs */
                        div:not(#acc-widget-panel):not(#acc-widget-panel *):not(#acc-magnifier):not(#acc-magnifier *),
                        section:not(#acc-widget-panel):not(#acc-magnifier),
                        article:not(#acc-widget-panel):not(#acc-magnifier),
                        main:not(#acc-widget-panel):not(#acc-magnifier),
                        aside:not(#acc-widget-panel):not(#acc-magnifier),
                        header:not(#acc-widget-panel):not(#acc-magnifier),
                        footer:not(#acc-widget-panel):not(#acc-magnifier),
                        nav:not(#acc-widget-panel):not(#acc-magnifier) {
                            background: #000 !important;
                            background-color: #000 !important;
                        }
                        
                        /* TOUS les éléments de texte - Renforcement maximum */
                        p:not(#acc-widget-panel *):not(#acc-magnifier *), 
                        li:not(#acc-widget-panel *):not(#acc-magnifier *), 
                        td:not(#acc-widget-panel *):not(#acc-magnifier *), 
                        th:not(#acc-widget-panel *):not(#acc-magnifier *), 
                        span:not(#acc-widget-panel *):not(#acc-magnifier *),
                        label:not(#acc-widget-panel *):not(#acc-magnifier *),
                        strong:not(#acc-widget-panel *):not(#acc-magnifier *),
                        b:not(#acc-widget-panel *):not(#acc-magnifier *),
                        em:not(#acc-widget-panel *):not(#acc-magnifier *),
                        i:not(#acc-widget-panel *):not(#acc-magnifier *),
                        small:not(#acc-widget-panel *):not(#acc-magnifier *),
                        code:not(#acc-widget-panel *):not(#acc-magnifier *),
                        pre:not(#acc-widget-panel *):not(#acc-magnifier *),
                        blockquote:not(#acc-widget-panel *):not(#acc-magnifier *),
                        cite:not(#acc-widget-panel *):not(#acc-magnifier *),
                        mark:not(#acc-widget-panel *):not(#acc-magnifier *),
                        time:not(#acc-widget-panel *):not(#acc-magnifier *),
                        address:not(#acc-widget-panel *):not(#acc-magnifier *),
                        dd:not(#acc-widget-panel *):not(#acc-magnifier *),
                        dt:not(#acc-widget-panel *):not(#acc-magnifier *),
                        figcaption:not(#acc-widget-panel *):not(#acc-magnifier *),
                        caption:not(#acc-widget-panel *):not(#acc-magnifier *),
                        legend:not(#acc-widget-panel *):not(#acc-magnifier *) {
                            color: #fff !important;
                            font-size: 115% !important;
                            line-height: 1.7 !important;
                            background-color: transparent !important;
                        }
                        
                        /* Titres agrandis - SAUF le widget */
                        h1:not(#acc-widget-panel *):not(#acc-magnifier *),
                        h2:not(#acc-widget-panel *):not(#acc-magnifier *),
                        h3:not(#acc-widget-panel *):not(#acc-magnifier *),
                        h4:not(#acc-widget-panel *):not(#acc-magnifier *),
                        h5:not(#acc-widget-panel *):not(#acc-magnifier *),
                        h6:not(#acc-widget-panel *):not(#acc-magnifier *) {
                            color: #fff !important;
                            background-color: transparent !important;
                        }
                        
                        h1:not(#acc-widget-panel *):not(#acc-magnifier *) { font-size: 2.4em !important; }
                        h2:not(#acc-widget-panel *):not(#acc-magnifier *) { font-size: 2em !important; }
                        h3:not(#acc-widget-panel *):not(#acc-magnifier *) { font-size: 1.7em !important; }
                        h4:not(#acc-widget-panel *):not(#acc-magnifier *),
                        h5:not(#acc-widget-panel *):not(#acc-magnifier *),
                        h6:not(#acc-widget-panel *):not(#acc-magnifier *) { font-size: 1.4em !important; }
                        
                        /* Liens très visibles - SAUF le widget */
                        a:not(#acc-widget-panel *):not(#acc-magnifier *) {
                            color: #4dabf7 !important;
                            background-color: transparent !important;
                            text-decoration: underline !important;
                            font-weight: 700 !important;
                            padding: 2px 4px !important;
                        }
                        
                        a:not(#acc-widget-panel *):not(#acc-magnifier *):hover,
                        a:not(#acc-widget-panel *):not(#acc-magnifier *):focus {
                            background: #4dabf7 !important;
                            background-color: #4dabf7 !important;
                            color: #000 !important;
                        }
                        
                        /* Boutons et formulaires - SAUF le widget */
                        button:not(#acc-widget-panel *):not(#acc-magnifier *),
                        input:not(#acc-widget-panel *):not(#acc-magnifier *):not([type="checkbox"]):not([type="radio"]),
                        select:not(#acc-widget-panel *):not(#acc-magnifier *),
                        textarea:not(#acc-widget-panel *):not(#acc-magnifier *) {
                            background: #222 !important;
                            background-color: #222 !important;
                            color: #fff !important;
                            border: 3px solid #fff !important;
                            font-size: 115% !important;
                            padding: 14px 22px !important;
                            min-height: 50px !important;
                            font-weight: 600 !important;
                        }
                        
                        button:not(#acc-widget-panel *):not(#acc-magnifier *):hover {
                            background: #4dabf7 !important;
                            background-color: #4dabf7 !important;
                            color: #000 !important;
                            border-color: #4dabf7 !important;
                        }
                        
                        /* Images contrastées - SAUF le widget */
                        img:not(#acc-widget-panel *):not(#acc-magnifier *) {
                            filter: contrast(1.4) brightness(1.2) !important;
                            border: 3px solid #fff !important;
                        }
                        
                        /* Tableaux - SAUF le widget */
                        table:not(#acc-widget-panel *):not(#acc-magnifier *) {
                            border: 3px solid #fff !important;
                            background: #000 !important;
                            background-color: #000 !important;
                        }
                        
                        table:not(#acc-widget-panel *):not(#acc-magnifier *) th,
                        table:not(#acc-widget-panel *):not(#acc-magnifier *) td {
                            border: 2px solid #fff !important;
                            padding: 12px !important;
                            color: #fff !important;
                            background-color: #000 !important;
                        }
                        
                        tbody:not(#acc-widget-panel *),
                        thead:not(#acc-widget-panel *),
                        tfoot:not(#acc-widget-panel *),
                        tr:not(#acc-widget-panel *) {
                            background: #000 !important;
                            background-color: #000 !important;
                        }
                        
                        /* Listes - SAUF le widget */
                        ul:not(#acc-widget-panel *):not(#acc-magnifier *),
                        ol:not(#acc-widget-panel *):not(#acc-magnifier *) {
                            padding-left: 30px !important;
                            color: #fff !important;
                        }
                        
                        /* Blocs de code */
                        pre:not(#acc-widget-panel *):not(#acc-magnifier *),
                        code:not(#acc-widget-panel *):not(#acc-magnifier *) {
                            background: #1a1a1a !important;
                            background-color: #1a1a1a !important;
                            color: #fff !important;
                            border: 2px solid #fff !important;
                        }
                        
                        /* Citations */
                        blockquote:not(#acc-widget-panel *):not(#acc-magnifier *) {
                            border-left: 5px solid #4dabf7 !important;
                            padding-left: 15px !important;
                            color: #fff !important;
                            background: #1a1a1a !important;
                            background-color: #1a1a1a !important;
                        }
                        
                        /* Focus très visible - SAUF le widget */
                        *:not(#acc-widget-panel *):not(#acc-magnifier *):focus {
                            outline: 4px solid #4dabf7 !important;
                            outline-offset: 3px !important;
                        }
                        
                        /* CORRECTION SPÉCIALE: Éléments avec classes ou IDs personnalisés */
                        [class]:not(#acc-widget-panel):not(#acc-widget-panel *):not(#acc-magnifier):not(#acc-magnifier *):not(body):not(html),
                        [id]:not(#acc-widget-panel):not(#acc-widget-panel *):not(#acc-magnifier):not(#acc-magnifier *):not(body):not(html) {
                            color: #fff !important;
                        }
                        
                        /* Forcer les conteneurs principaux */
                        #content:not(#acc-widget-panel):not(#acc-magnifier),
                        #main:not(#acc-widget-panel):not(#acc-magnifier),
                        #primary:not(#acc-widget-panel):not(#acc-magnifier),
                        .content:not(#acc-widget-panel):not(#acc-magnifier),
                        .main:not(#acc-widget-panel):not(#acc-magnifier),
                        .site-content:not(#acc-widget-panel):not(#acc-magnifier) {
                            background: #000 !important;
                            background-color: #000 !important;
                        }
                    </style>
                `;
                $('head').append(css);
                console.log('✓ Mode vision basse RENFORCÉ activé (contraste 21:1, widget préservé)');
            }
        }

        /**
         * Applique tous les paramètres
         */
        applyAllSettings() {
            if (this.settings.magnifier) {
                this.toggleMagnifier(true);
                this.zoomGroup.show();
            }
            if (this.settings.reduceField) {
                this.positionGroup.show();
            }
            this.applyDepthIndicators();
            this.applyReduceField();
            this.applyLowVisionMode();
            console.log('✓ Tous les réglages appliqués');
        }

        /**
         * Supprime tous les paramètres
         */
        removeAllSettings() {
            this.removeMagnifier();
            $('#acc-mono-depth-style').remove();
            $('#acc-mono-field-style').remove();
            $('#acc-mono-lowvision-style').remove();
            console.log('✓ Tous les réglages supprimés');
        }

        /**
         * Réinitialise tous les paramètres
         */
        reset() {
            if (confirm('Réinitialiser tous les paramètres d\'aide monophtalmie ?')) {
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
            this.magnifierToggle.prop('checked', this.settings.magnifier);
            this.depthToggle.prop('checked', this.settings.depthIndicators);
            this.fieldToggle.prop('checked', this.settings.reduceField);
            this.lowVisionToggle.prop('checked', this.settings.lowVisionMode);
            
            this.zoomSlider.val(this.settings.magnifierZoom);
            $('#acc-monophtalmie-zoom-value').text(this.settings.magnifierZoom + '%');

            this.positionButtons.removeClass('acc-position-active').attr('aria-checked', 'false');
            $(`.acc-position-btn[data-position="${this.settings.fieldPosition}"]`)
                .addClass('acc-position-active')
                .attr('aria-checked', 'true');

            if (this.settings.magnifier) {
                this.zoomGroup.show();
            } else {
                this.zoomGroup.hide();
            }
            
            if (this.settings.reduceField) {
                this.positionGroup.show();
            } else {
                this.positionGroup.hide();
            }
        }

        /**
         * Sauvegarde une préférence
         */
        savePreference(key, value) {
            const cookieName = `acc_monophtalmie_${key}`;
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
            const cookieName = `acc_monophtalmie_${key}`;
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
            this.settings.magnifier = this.getPreference('magnifier', false);
            this.settings.magnifierZoom = this.getPreference('magnifier_zoom', 200);
            this.settings.depthIndicators = this.getPreference('depth_indicators', false);
            this.settings.reduceField = this.getPreference('reduce_field', false);
            this.settings.fieldPosition = this.getPreference('field_position', 'center');
            this.settings.lowVisionMode = this.getPreference('low_vision_mode', false);
            
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
            this.savePreference('magnifier', this.settings.magnifier);
            this.savePreference('magnifier_zoom', this.settings.magnifierZoom);
            this.savePreference('depth_indicators', this.settings.depthIndicators);
            this.savePreference('reduce_field', this.settings.reduceField);
            this.savePreference('field_position', this.settings.fieldPosition);
            this.savePreference('low_vision_mode', this.settings.lowVisionMode);
        }
    }

    /**
     * Initialisation
     */
    $(document).ready(function() {
        const initModule = function() {
            if ($('#acc-module-monophtalmie').length) {
                window.accMonophtalmieModule = new MonophtalmieModule();
                console.log('✓ Module Aide Monophtalmie prêt (v2.4.0 - Contraste renforcé 21:1)');
            } else {
                console.warn('Module Monophtalmie: HTML non trouvé');
            }
        };
        
        initModule();
    });

})(jQuery);
