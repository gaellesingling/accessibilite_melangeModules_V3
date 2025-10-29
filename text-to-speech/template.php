<?php
/**
 * Template Text-to-Speech
 * Interface utilisateur du module
 * @package AccessibilityModular
 * @subpackage Modules/TextToSpeech
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="acc-module acc-tts-module" id="acc-tts-module" data-module="text-to-speech">
    
    <div class="acc-module-header">
        <h3 class="acc-module-title">
            <span class="acc-module-icon" aria-hidden="true">🔊</span>
            Text-to-Speech
        </h3>
        <label class="acc-module-toggle">
            <input 
                type="checkbox" 
                id="acc-tts-toggle"
                aria-label="Activer/désactiver la synthèse vocale"
            />
            <span class="acc-module-toggle-slider"></span>
        </label>
    </div>
    
    <div class="acc-module-content" id="acc-tts-content" style="display: none;">
        
        <div class="acc-control-group">
            <label class="acc-control-label">
                <span>Mode de lecture</span>
            </label>
            <div class="acc-button-group">
                <button 
                    type="button" 
                    class="acc-button active" 
                    id="acc-tts-mode-selection"
                    aria-pressed="true"
                >
                    📝 Sélection
                </button>
                <button 
                    type="button" 
                    class="acc-button" 
                    id="acc-tts-mode-page"
                    aria-pressed="false"
                >
                    📄 Page entière
                </button>
            </div>
            <p class="acc-control-hint">
                <strong>Sélection :</strong> Sélectionnez du texte et cliquez sur "Lire"<br>
                <strong>Page entière :</strong> Lit tout le contenu de la page
            </p>
        </div>
        
        <div class="acc-control-group">
            <label class="acc-control-label">
                <span>Contrôles</span>
            </label>
            <div class="acc-tts-controls">
                <button 
                    type="button" 
                    class="acc-tts-btn acc-tts-play" 
                    id="acc-tts-play"
                    aria-label="Lire le texte"
                    disabled
                >
                    <span class="acc-tts-icon">▶️</span>
                    <span class="acc-tts-label">Lire</span>
                </button>
                <button 
                    type="button" 
                    class="acc-tts-btn acc-tts-pause" 
                    id="acc-tts-pause"
                    aria-label="Mettre en pause"
                    disabled
                >
                    <span class="acc-tts-icon">⏸️</span>
                    <span class="acc-tts-label">Pause</span>
                </button>
                <button 
                    type="button" 
                    class="acc-tts-btn acc-tts-stop" 
                    id="acc-tts-stop"
                    aria-label="Arrêter la lecture"
                    disabled
                >
                    <span class="acc-tts-icon">⏹️</span>
                    <span class="acc-tts-label">Stop</span>
                </button>
            </div>
        </div>
        
        <div class="acc-control-group">
            <div class="acc-tts-status" id="acc-tts-status" aria-live="polite">
                <span class="acc-tts-status-icon">ℹ️</span>
                <span class="acc-tts-status-text">Prêt à lire</span>
            </div>
        </div>
        
        
        <div class="acc-control-group">
            <label class="acc-control-label" for="acc-tts-volume">
                <span>Volume</span>
                <span class="acc-control-value" id="acc-tts-volume-value">100%</span>
            </label>
            <div class="acc-slider-group">
                <button 
                    type="button" 
                    class="acc-slider-btn" 
                    id="acc-tts-volume-minus" 
                    aria-label="Diminuer le volume"
                >
                    -
                </button>
                <input 
                    type="range" 
                    id="acc-tts-volume" 
                    class="acc-slider"
                    min="0" 
                    max="100" 
                    value="100"
                    step="5"
                    aria-label="Régler le volume de la synthèse vocale"
                />
                <button 
                    type="button" 
                    class="acc-slider-btn" 
                    id="acc-tts-volume-plus" 
                    aria-label="Augmenter le volume"
                >
                    +
                </button>
            </div>
        </div>
        
        <div class="acc-control-group">
            <label class="acc-control-label" for="acc-tts-rate">
                <span>Vitesse de lecture</span>
                <span class="acc-control-value" id="acc-tts-rate-value">1.0x</span>
            </label>
            <div class="acc-slider-group">
                <button 
                    type="button" 
                    class="acc-slider-btn" 
                    id="acc-tts-rate-minus" 
                    aria-label="Diminuer la vitesse de lecture"
                >
                    -
                </button>
                <input 
                    type="range" 
                    id="acc-tts-rate" 
                    class="acc-slider"
                    min="0.5" 
                    max="2" 
                    value="1"
                    step="0.1"
                    aria-label="Régler la vitesse de lecture"
                />
                <button 
                    type="button" 
                    class="acc-slider-btn" 
                    id="acc-tts-rate-plus" 
                    aria-label="Augmenter la vitesse de lecture"
                >
                    +
                </button>
            </div>
            <p class="acc-control-hint">
                0.5x = Lent • 1.0x = Normal • 2.0x = Rapide
            </p>
        </div>
        
        <div class="acc-control-group">
            <label class="acc-control-label" for="acc-tts-voice">
                <span>Voix</span>
            </label>
            <select 
                id="acc-tts-voice" 
                class="acc-tts-voice-select"
                aria-label="Choisir une voix pour la synthèse vocale"
            >
                <option value="">Chargement des voix...</option>
            </select>
            <p class="acc-control-hint" id="acc-tts-voice-info">
                Les voix disponibles dépendent de votre système d'exploitation
            </p>
        </div>
        
        <div class="acc-tts-info">
            <p class="acc-tts-info-text">
                <strong>💡 Astuce :</strong> Sélectionnez du texte sur la page et cliquez sur "Lire" pour l'écouter.
            </p>
            <p class="acc-tts-info-text" style="font-size: 11px; color: #64748b; margin-top: 8px;">
                ⌨️ Raccourcis : Espace = Lecture/Pause • Échap = Stop
            </p>
        </div>
        
    </div>
    
</div>