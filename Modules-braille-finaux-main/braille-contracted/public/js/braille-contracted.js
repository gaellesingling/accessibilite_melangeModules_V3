jQuery(document).ready(function($) {
    // État global
    let isScreenReaderDetected = false;
    let lastTranslatedText = '';
    let braillePanel = null;
    let brailleToggle = null;
    let currentFocusElement = null;
    let lastFocusedElement = null;
    let imageBrailleOverlays = [];

    // Initialisation conforme RGAA
    initBrailleModule();

    function initBrailleModule() {
        createAccessibleToggle();
        createAccessiblePanel();
        loadRGAACompliantCSS();
        startEnhancedScreenReaderDetection();
        setupAccessibleEventHandlers();
        setupImageBrailleOverlays();
    }

    function loadRGAACompliantCSS() {
        const style = document.createElement('style');
        style.textContent = `
            #braille-contract-toggle {
                position: fixed;
                bottom: 20px;
                right: 80px;
                width: 50px;
                height: 50px;
                background: #4CAF50;
                color: white;
                border: none;
                border-radius: 50%;
                cursor: pointer;
                box-shadow: 0 2px 5px rgba(0,0,0,0.2);
                z-index: 999999;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 20px;
                transition: all 0.3s ease;
            }
            #braille-contract-toggle:focus { outline: 2px solid #000; outline-offset: 2px; }
            #braille-contract-toggle:hover { background: #45a049; transform: scale(1.05); }
            #braille-contract-toggle.active { background: #388E3C; }
            #braille-contract-panel {
                position: fixed;
                bottom: 80px;
                right: 20px;
                width: 90%;
                max-width: 500px;
                background: white;
                border: 1px solid #ddd;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                display: none;
                max-height: 70vh;
                overflow-y: auto;
                z-index: 999998;
                margin: 0 10px;
                border-left: 4px solid #4CAF50;
            }
            #braille-contract-panel[aria-hidden="false"] { display: block; animation: slideUp 0.3s ease-out; }
            @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
            .braille-contract-header { display: flex; justify-content: space-between; align-items: center; padding: 15px; border-bottom: 1px solid #eee; }
            .braille-contract-header h2 { margin: 0; font-size: 1.1em; color: #333; }
            .braille-contract-close { background: none; border: none; font-size: 20px; cursor: pointer; color: #777; padding: 5px; }
            .braille-contract-close:focus { outline: 2px solid #000; }
            .braille-contract-close:hover { color: #333; }
            .braille-contract-content { padding: 15px; }
            .braille-contract-result { margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee; }
            .braille-contract-original { font-size: 0.9em; color: #666; margin-bottom: 8px; word-break: break-word; }
            .braille-contract-output {
                font-family: monospace;
                font-size: 1.6em;
                line-height: 1.8;
                padding: 12px;
                background: #f0f8f0;
                border-radius: 6px;
                border: 1px solid #eee;
                min-height: 50px;
                white-space: pre-wrap;
                word-break: break-word;
            }
            .braille-char { display: inline-block; margin-right: 1px; line-height: 1.4; }
            .sr-only {
                position: absolute;
                width: 1px;
                height: 1px;
                padding: 0;
                margin: -1px;
                overflow: hidden;
                clip: rect(0, 0, 0, 0);
                white-space: nowrap;
                border: 0;
            }
            button:focus, [tabindex="0"]:focus { outline: 2px solid #000; outline-offset: 2px; }
            .braille-image-overlay {
                position: absolute;
                bottom: 100%;
                left: 0;
                background: rgba(255, 255, 255, 0.95);
                border: 1px solid #ddd;
                border-radius: 4px;
                padding: 8px;
                box-shadow: 0 2px 5px rgba(0,0,0,0.2);
                z-index: 1000;
                font-family: monospace;
                font-size: 1.4em;
                line-height: 1.6;
                max-width: 300px;
                word-break: break-word;
                margin-bottom: 5px;
                display: none;
            }
            .braille-image-overlay.visible { display: block; }
            img[aria-describedby] { position: relative; }
            .braille-image-toggle {
                position: absolute;
                top: 5px;
                right: 5px;
                width: 24px;
                height: 24px;
                background: #4CAF50;
                color: white;
                border: none;
                border-radius: 50%;
                cursor: pointer;
                font-size: 12px;
                line-height: 24px;
                text-align: center;
                z-index: 1001;
            }
            .braille-image-toggle:focus { outline: 2px solid #000; }
            .braille-image-toggle:hover { background: #388E3C; }
        `;
        document.head.appendChild(style);
    }

    function createAccessibleToggle() {
        brailleToggle = document.createElement('button');
        brailleToggle.id = 'braille-contract-toggle';
        brailleToggle.setAttribute('aria-label', 'Activer le braille contracté');
        brailleToggle.setAttribute('aria-expanded', 'false');
        brailleToggle.setAttribute('aria-controls', 'braille-contract-panel');
        brailleToggle.setAttribute('tabindex', '0');
        brailleToggle.innerHTML = '<span class="braille-icon" aria-hidden="true">⠇⠡</span><span class="sr-only">Braille contracté</span>';
        document.body.appendChild(brailleToggle);

        brailleToggle.addEventListener('click', togglePanel);
        brailleToggle.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                togglePanel();
            }
        });
    }

    function createAccessiblePanel() {
        braillePanel = document.createElement('div');
        braillePanel.id = 'braille-contract-panel';
        braillePanel.setAttribute('role', 'dialog');
        braillePanel.setAttribute('aria-modal', 'true');
        braillePanel.setAttribute('aria-labelledby', 'braille-contract-title');
        braillePanel.setAttribute('aria-hidden', 'true');
        braillePanel.setAttribute('tabindex', '-1');
        braillePanel.style.display = 'none';

        braillePanel.innerHTML = `
            <div class="braille-contract-header">
                <h2 id="braille-contract-title">Braille Contracté (Grade 2)</h2>
                <button class="braille-contract-close" aria-label="Fermer le panneau braille contracté" tabindex="0">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="braille-contract-content">
                <div class="braille-contract-result">
                    <div class="braille-contract-original" aria-label="Texte original"></div>
                    <div class="braille-contract-output" aria-hidden="true" role="region" aria-label="Traduction braille contractée"></div>
                    <div class="sr-only">Traduction en braille contracté du texte ci-dessus.</div>
                </div>
            </div>
        `;
        document.body.appendChild(braillePanel);

        braillePanel.querySelector('.braille-contract-close').addEventListener('click', hideBraillePanel);
        braillePanel.querySelector('.braille-contract-close').addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                hideBraillePanel();
            }
        });
    }

    function togglePanel() {
        const isActive = brailleToggle.classList.toggle('active');
        brailleToggle.setAttribute('aria-expanded', isActive);

        if (isActive) {
            showBraillePanel();
            announceToScreenReader("Module braille contracté activé. Appuyez sur Échap pour fermer.");
            if (document.activeElement) {
                handleFocusChange({ target: document.activeElement });
            }
        } else {
            hideBraillePanel();
            announceToScreenReader("Module braille contracté désactivé.");
        }
    }

    function showBraillePanel() {
        braillePanel.style.display = 'block';
        braillePanel.setAttribute('aria-hidden', 'false');
        braillePanel.focus();
    }

    function hideBraillePanel() {
        braillePanel.setAttribute('aria-hidden', 'true');
        setTimeout(() => {
            braillePanel.style.display = 'none';
        }, 300);
        brailleToggle.focus();
    }

    function setupAccessibleEventHandlers() {
        document.addEventListener('mouseup', handleTextSelection);
        document.addEventListener('keyup', handleTextSelection);
        document.addEventListener('focusin', handleFocusChange);
        document.addEventListener('keydown', handleKeyboardNavigation);
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && braillePanel.style.display === 'block') {
                hideBraillePanel();
            }
        });
    }

    function setupImageBrailleOverlays() {
        document.querySelectorAll('img[alt], img[aria-label], img[aria-labelledby]').forEach(img => {
            if (img.getAttribute('alt') || img.getAttribute('aria-label') || img.getAttribute('aria-labelledby')) {
                setupImageBrailleToggle(img);
            }
        });

        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(mutation => {
                mutation.addedNodes.forEach(node => {
                    if (node.nodeType === 1) {
                        node.querySelectorAll('img[alt], img[aria-label], img[aria-labelledby]').forEach(img => {
                            if (!img.querySelector('.braille-image-toggle')) {
                                setupImageBrailleToggle(img);
                            }
                        });
                    }
                });
            });
        });

        observer.observe(document.body, { childList: true, subtree: true });
    }

    function setupImageBrailleToggle(img) {
        const imgId = 'img-' + Math.random().toString(36).substr(2, 9);
        img.id = imgId;

        const toggleButton = document.createElement('button');
        toggleButton.className = 'braille-image-toggle';
        toggleButton.setAttribute('aria-label', 'Afficher la transcription braille');
        toggleButton.setAttribute('aria-expanded', 'false');
        toggleButton.setAttribute('aria-controls', imgId + '-braille');
        toggleButton.textContent = '⠇';
        toggleButton.addEventListener('click', function() {
            toggleImageBraille(img);
        });

        const brailleOverlay = document.createElement('div');
        brailleOverlay.id = imgId + '-braille';
        brailleOverlay.className = 'braille-image-overlay';
        brailleOverlay.setAttribute('aria-hidden', 'true');
        brailleOverlay.setAttribute('role', 'note');

        img.style.position = 'relative';
        img.appendChild(toggleButton);
        img.parentNode.insertBefore(brailleOverlay, img);

        imageBrailleOverlays.push({
            img: img,
            toggle: toggleButton,
            overlay: brailleOverlay
        });
    }

    function toggleImageBraille(img) {
        const imgId = img.id;
        const overlay = document.getElementById(imgId + '-braille');
        const toggle = img.querySelector('.braille-image-toggle');
        const isExpanded = toggle.getAttribute('aria-expanded') === 'true';

        if (isExpanded) {
            overlay.classList.remove('visible');
            toggle.setAttribute('aria-expanded', 'false');
        } else {
            let altText = '';
            if (img.getAttribute('alt')) {
                altText = img.getAttribute('alt');
            } else if (img.getAttribute('aria-label')) {
                altText = img.getAttribute('aria-label');
            } else if (img.getAttribute('aria-labelledby')) {
                const labelledBy = img.getAttribute('aria-labelledby');
                const labelElement = document.getElementById(labelledBy);
                if (labelElement) {
                    altText = labelElement.textContent.trim();
                }
            }

            if (altText) {
                translateImageText(imgId, altText);
                overlay.classList.add('visible');
                toggle.setAttribute('aria-expanded', 'true');
            }
        }
    }

    function translateImageText(imgId, text) {
        const overlay = document.getElementById(imgId + '-braille');
        overlay.innerHTML = '<div class="sr-only">Traduction en cours...</div>';

        jQuery.ajax({
            url: brailleContracted.ajaxurl,
            type: 'POST',
            data: {
                action: 'braille_contract_translate',
                text: text,
                nonce: brailleContracted.nonce
            },
            success: function(response) {
                if (response.success) {
                    overlay.innerHTML = response.data.braille;
                    overlay.setAttribute('aria-label', `Transcription braille: ${text}`);
                }
            },
            error: function() {
                overlay.innerHTML = '<div class="sr-only">Erreur de traduction</div>';
            }
        });
    }

    function startEnhancedScreenReaderDetection() {
        detectScreenReaderViaCSS();
        setupKeyboardShortcuts();
        setupARIADetection();
        setInterval(checkScreenReaderStatus, 2000);
        checkAccessibilityProperties();
    }

    function detectScreenReaderViaCSS() {
        const style = window.getComputedStyle(document.body, ':before');
        const content = style.getPropertyValue('content');
        if (content && content.includes('screenreader')) {
            activateScreenReaderMode();
        }
    }

    function setupKeyboardShortcuts() {
        const screenReaderShortcuts = [
            {key: 'Tab', ctrl: true},
            {key: 'ArrowDown', alt: true},
            {key: 'ArrowUp', alt: true},
            {key: 'Escape', ctrl: true},
            {key: 'Insert', shift: true},
            {key: 'F6', shift: true},
            {key: 'F7', shift: true},
            {key: 'NumpadPlus', alt: true}
        ];

        document.addEventListener('keydown', function(e) {
            if (brailleToggle.classList.contains('active')) return;

            screenReaderShortcuts.forEach(combo => {
                if (e.key === combo.key &&
                    (combo.ctrl ? e.ctrlKey : true) &&
                    (combo.alt ? e.altKey : true) &&
                    (combo.shift ? e.shiftKey : true)) {
                    activateScreenReaderMode();
                }
            });
        });
    }

    function setupARIADetection() {
        const ariaElements = document.querySelectorAll('[aria-live], [aria-label], [role]');
        if (ariaElements.length > 10) {
            activateScreenReaderMode();
        }

        const observer = new MutationObserver(function(mutations) {
            if (brailleToggle.classList.contains('active')) return;

            let ariaCount = 0;
            mutations.forEach(mutation => {
                if (mutation.addedNodes) {
                    mutation.addedNodes.forEach(node => {
                        if (node.nodeType === 1) {
                            ariaCount += node.querySelectorAll('[aria-live], [aria-label], [role]').length;
                        }
                    });
                }
            });

            if (ariaCount > 5) {
                activateScreenReaderMode();
            }
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['aria-live', 'aria-label', 'role']
        });
    }

    function checkScreenReaderStatus() {
        if (brailleToggle.classList.contains('active')) return;

        if (document.querySelector('.nvda, .jaws, .voiceover, .screen-reader, .a11y')) {
            activateScreenReaderMode();
            return;
        }

        const virtualFocus = document.querySelectorAll('[id^="virtual-focus-"], [class^="focus-"], [data-focus]');
        if (virtualFocus.length > 0) {
            activateScreenReaderMode();
            return;
        }

        if (window.accessibilityEnabled || window.accessible || window.screenReader) {
            activateScreenReaderMode();
            return;
        }
    }

    function checkAccessibilityProperties() {
        if (document.body.hasAttribute('data-a11y') ||
            document.body.classList.contains('a11y') ||
            document.documentElement.hasAttribute('data-accessibility')) {
            activateScreenReaderMode();
        }
    }

    function activateScreenReaderMode() {
        if (brailleToggle.classList.contains('active')) return;

        brailleToggle.classList.add('active');
        brailleToggle.setAttribute('aria-expanded', 'true');
        showBraillePanel();
        announceToScreenReader("Lecteur d'écran détecté. Module braille contracté activé automatiquement.");

        if (document.activeElement) {
            handleFocusChange({ target: document.activeElement });
        }
    }

    function handleTextSelection(e) {
        if (!brailleToggle.classList.contains('active')) return;

        const selection = window.getSelection();
        if (!selection || selection.toString().length === 0) return;

        const selectedText = selection.toString().trim();
        if (selectedText === lastTranslatedText) return;

        lastTranslatedText = selectedText;
        translateText(selectedText);
    }

    function handleFocusChange(e) {
        if (!brailleToggle.classList.contains('active')) return;

        const element = e.target;

        if (element.closest('#braille-contract-panel, #braille-contract-toggle')) {
            return;
        }

        lastFocusedElement = element;
        let textToTranslate = '';
        let isImage = false;

        if (element.tagName === 'IMG') {
            isImage = true;
            if (element.getAttribute('alt')) {
                textToTranslate = `Image: ${element.getAttribute('alt')}`;
            } else if (element.getAttribute('aria-label')) {
                textToTranslate = `Image: ${element.getAttribute('aria-label')}`;
            } else if (element.getAttribute('aria-labelledby')) {
                const labelledBy = element.getAttribute('aria-labelledby');
                const labelElement = document.getElementById(labelledBy);
                if (labelElement) {
                    textToTranslate = `Image: ${labelElement.textContent.trim()}`;
                }
            }

            if (textToTranslate) {
                translateText(textToTranslate);
                const imgId = element.id;
                if (imgId) {
                    const overlay = document.getElementById(imgId + '-braille');
                    if (overlay) {
                        overlay.classList.add('visible');
                        element.querySelector('.braille-image-toggle').setAttribute('aria-expanded', 'true');
                    }
                }
            }
        }
        else if (element.tagName === 'INPUT' || element.tagName === 'TEXTAREA' ||
                 element.isContentEditable || element.getAttribute('role') === 'textbox') {
            const label = document.querySelector(`label[for="${element.id}"]`);
            if (label) {
                textToTranslate = `Champ: ${label.textContent.trim()} - `;
            }
            textToTranslate += element.value || element.textContent;
        }
        else if (element.tagName === 'A' || element.tagName === 'BUTTON') {
            textToTranslate = element.textContent.trim();
            if (element.getAttribute('aria-label')) {
                textToTranslate = element.getAttribute('aria-label');
            }
        }
        else if (element.hasAttribute('aria-label') || element.hasAttribute('aria-labelledby')) {
            textToTranslate = element.getAttribute('aria-label') ||
                             (element.getAttribute('aria-labelledby') ?
                              document.getElementById(element.getAttribute('aria-labelledby')).textContent : '') ||
                             element.textContent.trim();
        }
        else if (element.textContent.trim()) {
            textToTranslate = element.textContent.trim();
        }

        if (textToTranslate) {
            translateText(textToTranslate);
        }
    }

    function handleKeyboardNavigation(e) {
        if (!brailleToggle.classList.contains('active')) return;

        if (['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight', 'Tab'].includes(e.key)) {
            setTimeout(() => {
                if (document.activeElement && document.activeElement !== lastFocusedElement) {
                    handleFocusChange({ target: document.activeElement });
                }
            }, 50);
        }
    }

    function translateText(text) {
        if (!text) return;

        document.querySelector('.braille-contract-original').textContent = text;
        document.querySelector('.braille-contract-output').innerHTML = '<div class="sr-only">Traduction en cours...</div>';

        jQuery.ajax({
            url: brailleContracted.ajaxurl,
            type: 'POST',
            data: {
                action: 'braille_contract_translate',
                text: text,
                nonce: brailleContracted.nonce
            },
            success: function(response) {
                if (response.success) {
                    document.querySelector('.braille-contract-output').innerHTML = response.data.braille;
                    announceToScreenReader(`Traduction: ${text}`);
                }
            },
            error: function() {
                document.querySelector('.braille-contract-output').innerHTML = '<div class="sr-only">Erreur de traduction</div>';
            }
        });
    }

    function announceToScreenReader(message) {
        const announcement = document.createElement('div');
        announcement.setAttribute('aria-live', 'assertive');
        announcement.setAttribute('class', 'sr-only');
        announcement.setAttribute('role', 'status');
        announcement.textContent = message;
        document.body.appendChild(announcement);

        setTimeout(() => {
            announcement.remove();
        }, 3000);
    }
});
