/**
 * Module Text-to-Speech - JavaScript
 * Version: 1.2.2 - Correction message "Erreur de lecture" au stop
 */

(function($) {
    'use strict';

    class TextToSpeech {
        constructor() {
            // Éléments DOM
            this.$module = $('#acc-tts-module');
            this.$toggle = $('#acc-tts-toggle');
            this.$content = $('#acc-tts-content');
            
            // Contrôles
            this.$playBtn = $('#acc-tts-play');
            this.$pauseBtn = $('#acc-tts-pause');
            this.$stopBtn = $('#acc-tts-stop');
            
            // Paramètres
            this.$volumeSlider = $('#acc-tts-volume');
            this.$volumeValue = $('#acc-tts-volume-value');
            this.$rateSlider = $('#acc-tts-rate');
            this.$rateValue = $('#acc-tts-rate-value');
            this.$voiceSelect = $('#acc-tts-voice');
            this.$voiceInfo = $('#acc-tts-voice-info');
            
            // Boutons +/-
            this.$volumeMinus = $('#acc-tts-volume-minus');
            this.$volumePlus = $('#acc-tts-volume-plus');
            this.$rateMinus = $('#acc-tts-rate-minus');
            this.$ratePlus = $('#acc-tts-rate-plus');
            
            // Mode
            this.$modeSelection = $('#acc-tts-mode-selection');
            this.$modePage = $('#acc-tts-mode-page');
            
            // Statut
            this.$status = $('#acc-tts-status');
            this.$statusText = this.$status.find('.acc-tts-status-text');
            this.$statusIcon = this.$status.find('.acc-tts-status-icon');
            
            // Variables
            this.synthesis = null;
            this.utterance = null;
            this.voices = [];
            this.isPlaying = false;
            this.isPaused = false;
            this.isStopping = false;
            this.currentMode = 'selection';
            this.selectedText = '';
            
            // Configuration
            this.config = {
                volume: 1.0,
                rate: 1.0,
                pitch: 1.0,
                voice: null,
            };
            
            this.init();
        }

        init() {
            if (!this.checkSupport()) {
                this.showError('Votre navigateur ne supporte pas la synthèse vocale.');
                return;
            }
            
            this.synthesis = window.speechSynthesis;
            this.loadPreferences();
            this.loadVoices();
            
            if (speechSynthesis.onvoiceschanged !== undefined) {
                speechSynthesis.onvoiceschanged = () => this.loadVoices();
            }
            
            this.bindEvents();
            this.applyPreferences();
            
            // CORRECTION : Version mise à jour
            console.log('Text-to-Speech initialisé v1.2.2 (correction message Stop)');
        }

        checkSupport() {
            return 'speechSynthesis' in window && 'SpeechSynthesisUtterance' in window;
        }

        bindEvents() {
            // Toggle principal
            this.$toggle.on('change', () => this.toggleModule());
            
            // Modes
            this.$modeSelection.on('click', () => this.switchMode('selection'));
            this.$modePage.on('click', () => this.switchMode('page'));
            
            // Contrôles
            this.$playBtn.on('click', () => this.play());
            this.$pauseBtn.on('click', () => this.pause());
            this.$stopBtn.on('click', () => this.stop());
            
            // Sliders
            this.$volumeSlider.on('input', (e) => this.updateVolume(parseFloat(e.target.value) / 100));
            this.$rateSlider.on('input', (e) => this.updateRate(parseFloat(e.target.value)));
            this.$voiceSelect.on('change', (e) => this.changeVoice(e.target.value));
            
            // Boutons +/-
            this.$volumeMinus.on('click', () => this.adjustVolume(-5));
            this.$volumePlus.on('click', () => this.adjustVolume(5));
            this.$rateMinus.on('click', () => this.adjustRate(-0.1));
            this.$ratePlus.on('click', () => this.adjustRate(0.1));
            
            // Sélection de texte
            $(document).on('mouseup keyup', () => {
                if (this.currentMode === 'selection') {
                    this.handleTextSelection();
                }
            });
            
            // Raccourcis clavier
            $(document).on('keydown', (e) => this.handleKeyboard(e));
        }

        toggleModule() {
            const isEnabled = this.$toggle.is(':checked');
            
            if (isEnabled) {
                this.$content.slideDown(300);
                this.announce('Module Text-to-Speech activé');
            } else {
                this.$content.slideUp(300);
                this.stop();
                this.announce('Module Text-to-Speech désactivé');
            }
            
            this.savePreference('enabled', isEnabled);
        }

        switchMode(mode) {
            this.currentMode = mode;
            
            this.$modeSelection.toggleClass('active', mode === 'selection').attr('aria-pressed', mode === 'selection');
            this.$modePage.toggleClass('active', mode === 'page').attr('aria-pressed', mode === 'page');
            
            this.stop();
            
            if (mode === 'selection') {
                this.$playBtn.prop('disabled', !this.getSelectedText());
                this.updateStatus('Sélectionnez du texte et cliquez sur "Lire"');
                this.announce('Mode sélection activé');
            } else {
                this.$playBtn.prop('disabled', false);
                this.updateStatus('Prêt à lire la page entière');
                this.announce('Mode page entière activé');
            }
            
            this.savePreference('mode', mode);
        }

        handleTextSelection() {
            const selectedText = this.getSelectedText();
            this.$playBtn.prop('disabled', !selectedText);
            
            if (selectedText) {
                this.updateStatus(`${selectedText.length} caractères sélectionnés`);
            } else {
                this.updateStatus('Sélectionnez du texte');
            }
        }

        getSelectedText() {
            const selection = window.getSelection();
            return selection ? selection.toString().trim() : '';
        }

        play() {
            if (this.isPlaying && !this.isPaused) {
                return;
            }
            
            if (this.isPaused && this.synthesis.paused) {
                this.synthesis.resume();
                this.isPaused = false;
                this.updateButtons('playing');
                this.updateStatus('Reprise de la lecture...', 'playing');
                this.announce('Lecture reprise');
                return;
            }
            
            const textToRead = this.getTextToRead();
            
            if (!textToRead) {
                this.showError('Aucun texte à lire');
                return;
            }
            
            this.selectedText = textToRead;
            this.startSpeaking(textToRead);
        }

        getTextToRead() {
            if (this.currentMode === 'selection') {
                return this.getSelectedText();
            } else {
                return $('body').text().replace(/\s+/g, ' ').trim();
            }
        }

        startSpeaking(text) {
            this.stop();
            
            this.utterance = new SpeechSynthesisUtterance(text);
            this.utterance.volume = this.config.volume;
            this.utterance.rate = this.config.rate;
            this.utterance.pitch = this.config.pitch;
            
            if (this.config.voice) {
                this.utterance.voice = this.config.voice;
            }
            
            this.utterance.onstart = () => {
                this.isPlaying = true;
                this.isPaused = false;
                this.updateButtons('playing');
                this.updateStatus('Lecture en cours...', 'playing');
                this.announce('Lecture démarrée');
            };
            
            this.utterance.onpause = () => {
                this.isPaused = true;
                this.updateButtons('paused');
                this.updateStatus('Lecture en pause', 'paused');
            };
            
            this.utterance.onresume = () => {
                this.isPaused = false;
                this.updateButtons('playing');
                this.updateStatus('Lecture en cours...', 'playing');
            };
            
            this.utterance.onend = () => {
                if (!this.isStopping) {
                    this.isPlaying = false;
                    this.isPaused = false;
                    this.updateButtons('stopped');
                    this.updateStatus('Lecture terminée', 'success');
                    this.announce('Lecture terminée');
                }
                this.isStopping = false;
            };
            
            this.utterance.onerror = (event) => {
                // CORRECTION : Ne pas traiter un arrêt manuel comme une erreur
                // Différents navigateurs utilisent différents codes d'erreur pour cancel
                if (this.isStopping || event.error === 'canceled' || event.error === 'interrupted') {
                    return;
                }
                
                console.error('Erreur TTS:', event);
                this.isPlaying = false;
                this.isPaused = false;
                this.updateButtons('stopped');
                this.updateStatus('Erreur de lecture', 'error');
                this.announce('Erreur lors de la lecture');
            };
            
            this.synthesis.speak(this.utterance);
        }

        pause() {
            if (!this.isPlaying || this.isPaused) {
                return;
            }
            
            this.synthesis.pause();
        }

        stop() {
            if (!this.isPlaying) {
                return;
            }
            
            this.isStopping = true;
            this.synthesis.cancel();
            
            this.isPlaying = false;
            this.isPaused = false;
            this.updateButtons('stopped');
            this.updateStatus('Lecture arrêtée', 'stopped');
            this.announce('Lecture arrêtée');
        }

        updateVolume(volume) {
            this.config.volume = Math.max(0, Math.min(1, volume));
            this.$volumeValue.text(Math.round(this.config.volume * 100) + '%');
            this.savePreference('volume', this.config.volume);
            
            if (this.utterance) {
                this.utterance.volume = this.config.volume;
            }
        }

        updateRate(rate) {
            this.config.rate = Math.max(0.5, Math.min(2, rate));
            this.$rateValue.text(this.config.rate.toFixed(1) + 'x');
            this.savePreference('rate', this.config.rate);
            
            if (this.utterance) {
                this.utterance.rate = this.config.rate;
            }
        }

        adjustVolume(delta) {
            const currentVolume = parseFloat(this.$volumeSlider.val());
            const newVolume = Math.max(0, Math.min(100, currentVolume + delta));
            this.$volumeSlider.val(newVolume).trigger('input');
        }

        adjustRate(delta) {
            const currentRate = parseFloat(this.$rateSlider.val());
            const newRate = Math.max(0.5, Math.min(2, currentRate + delta));
            this.$rateSlider.val(newRate.toFixed(1)).trigger('input');
        }

        changeVoice(voiceName) {
            const voice = this.voices.find(v => v.name === voiceName);
            if (voice) {
                this.config.voice = voice;
                this.savePreference('voice', voiceName);
                this.announce(`Voix changée: ${voice.name}`);
            }
        }

        loadVoices() {
            this.voices = this.synthesis.getVoices();
            
            if (this.voices.length === 0) {
                return;
            }
            
            this.$voiceSelect.empty();
            
            const frenchVoices = this.voices.filter(v => v.lang.startsWith('fr'));
            const otherVoices = this.voices.filter(v => !v.lang.startsWith('fr'));
            
            if (frenchVoices.length > 0) {
                const $frGroup = $('<optgroup label="🇫🇷 Français"></optgroup>');
                frenchVoices.forEach(voice => {
                    $frGroup.append($('<option></option>').val(voice.name).text(voice.name));
                });
                this.$voiceSelect.append($frGroup);
            }
            
            if (otherVoices.length > 0) {
                const $otherGroup = $('<optgroup label="🌍 Autres langues"></optgroup>');
                otherVoices.forEach(voice => {
                    $otherGroup.append($('<option></option>').val(voice.name).text(voice.name));
                });
                this.$voiceSelect.append($otherGroup);
            }
            
            this.$voiceInfo.text(`${this.voices.length} voix disponibles`);
            
            // Sélectionner la voix sauvegardée
            const savedVoiceName = this.getPreference('voice');
            if (savedVoiceName) {
                this.$voiceSelect.val(savedVoiceName);
                this.config.voice = this.voices.find(v => v.name === savedVoiceName);
            }
        }

        updateButtons(state) {
            switch(state) {
                case 'playing':
                    this.$playBtn.prop('disabled', true);
                    this.$pauseBtn.prop('disabled', false);
                    this.$stopBtn.prop('disabled', false);
                    break;
                case 'paused':
                    this.$playBtn.prop('disabled', false);
                    this.$pauseBtn.prop('disabled', true);
                    this.$stopBtn.prop('disabled', false);
                    break;
                case 'stopped':
                default:
                    this.$playBtn.prop('disabled', this.currentMode === 'selection' && !this.getSelectedText());
                    this.$pauseBtn.prop('disabled', true);
                    this.$stopBtn.prop('disabled', true);
                    break;
            }
        }

        updateStatus(text, type = 'info') {
            this.$statusText.text(text);
            // CORRECTION : Ajout de 'acc-tts-status-stopped' pour un nettoyage complet
            this.$status.removeClass('acc-tts-status-playing acc-tts-status-paused acc-tts-status-error acc-tts-status-success acc-tts-status-stopped');
            
            const iconMap = {
                'playing': '🔊',
                'paused': '⏸️',
                'stopped': '⏹️',
                'error': '❌',
                'success': '✓',
                'info': 'ℹ️'
            };
            
            this.$statusIcon.text(iconMap[type] || iconMap['info']);
            
            if (type !== 'info') {
                this.$status.addClass(`acc-tts-status-${type}`);
            }
        }

        handleKeyboard(e) {
            if (!this.$toggle.is(':checked')) {
                return;
            }
            
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
                return;
            }
            
            switch(e.key) {
                case ' ':
                    e.preventDefault();
                    if (this.isPlaying && !this.isPaused) {
                        this.pause();
                    } else {
                        this.play();
                    }
                    break;
                case 'Escape':
                    e.preventDefault();
                    this.stop();
                    break;
            }
        }

        announce(message) {
            const $announcer = $('<div>')
                .attr('role', 'status')
                .attr('aria-live', 'polite')
                .addClass('sr-only')
                .text(message);
            
            $('body').append($announcer);
            
            setTimeout(() => {
                $announcer.remove();
            }, 1000);
        }

        showError(message) {
            this.updateStatus(message, 'error');
            this.announce(message);
        }

        loadPreferences() {
            const enabled = this.getPreference('enabled', true);
            const mode = this.getPreference('mode', 'selection');
            const volume = this.getPreference('volume', 1.0);
            const rate = this.getPreference('rate', 1.0);
            const voice = this.getPreference('voice');
            
            this.$toggle.prop('checked', enabled);
            
            if (enabled) {
                this.$content.show();
            }
            
            this.currentMode = mode;
            this.config.volume = volume;
            this.config.rate = rate;
            
            if (voice) {
                const foundVoice = this.voices.find(v => v.name === voice);
                if (foundVoice) {
                    this.config.voice = foundVoice;
                }
            }
        }

        applyPreferences() {
            this.$volumeSlider.val(this.config.volume * 100);
            this.$volumeValue.text(Math.round(this.config.volume * 100) + '%');
            
            this.$rateSlider.val(this.config.rate);
            this.$rateValue.text(this.config.rate.toFixed(1) + 'x');
            
            this.$modeSelection.toggleClass('active', this.currentMode === 'selection');
            this.$modePage.toggleClass('active', this.currentMode === 'page');
            
            if (this.config.voice) {
                this.$voiceSelect.val(this.config.voice.name);
            }
        }

        savePreference(key, value) {
            const cookieName = 'acc_tts_' + key;
            const expiryDays = 365;
            const date = new Date();
            date.setTime(date.getTime() + (expiryDays * 24 * 60 * 60 * 1000));
            
            document.cookie = `${cookieName}=${encodeURIComponent(value)};expires=${date.toUTCString()};path=/;SameSite=Lax`;
        }

        getPreference(key, defaultValue = null) {
            const cookieName = 'acc_tts_' + key;
            const cookies = document.cookie.split(';');
            
            for (let cookie of cookies) {
                const [name, value] = cookie.trim().split('=');
                if (name === cookieName) {
                    try {
                        const decoded = decodeURIComponent(value);
                        if (decoded === 'true') return true;
                        if (decoded === 'false') return false;
                        if (!isNaN(decoded) && decoded !== '') return parseFloat(decoded);
                        return decoded;
                    } catch (e) {
                        return defaultValue;
                    }
                }
            }
            
            return defaultValue;
        }
    }

    // Initialiser le module au chargement du DOM
    $(document).ready(function() {
        new TextToSpeech();
    });

})(jQuery);