<?php
/**
 * Module Soulagement Migraines
 * Réduit les déclencheurs visuels des migraines ophtalmiques
 * 
 * @package AccessibilityModular
 * @subpackage Modules
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe du module Soulagement Migraines
 */
class ACC_Module_Migraine {
    
    /**
     * Version du module
     */
    const VERSION = '1.0.0';
    
    /**
     * Nom du module
     */
    const MODULE_NAME = 'migraine';
    
    /**
     * Constructeur
     */
    public function __construct() {
        $this->init_hooks();
    }
    
    /**
     * Initialise les hooks
     */
    private function init_hooks() {
        add_filter('acc_module_metadata', array($this, 'add_metadata'), 10, 2);
        add_action('wp_head', array($this, 'add_meta_tags'), 1);
    }
    
    /**
     * Ajoute des métadonnées au module
     * 
     * @param array $metadata Métadonnées existantes
     * @param string $module_name Nom du module
     * @return array
     */
    public function add_metadata($metadata, $module_name) {
        if ($module_name !== self::MODULE_NAME) {
            return $metadata;
        }
        
        $metadata['relief_features'] = $this->get_relief_features();
        $metadata['wcag_compliance'] = array(
            'criteria' => array('1.4.3', '1.4.6', '1.4.8', '1.4.12'),
            'level' => 'AAA'
        );
        $metadata['rgaa_compliance'] = array(
            'criteria' => array('3.2', '3.3', '10.3'),
            'level' => 'AA'
        );
        $metadata['medical_conditions'] = array(
            'migraine_ophtalmique',
            'photophobie',
            'fatigue_oculaire',
            'sensibilite_lumiere'
        );
        
        return $metadata;
    }
    
    /**
     * Ajoute les meta tags
     */
    public function add_meta_tags() {
        // Vérifie si le mode sombre est actif
        if ($this->is_dark_mode_active()) {
            echo '<meta name="color-scheme" content="dark light">' . "\n";
        }
    }
    
    /**
     * Vérifie si le mode sombre est actif
     * 
     * @return bool
     */
    private function is_dark_mode_active() {
        return isset($_COOKIE['acc_migraine_dark_mode']) && 
               json_decode(stripslashes($_COOKIE['acc_migraine_dark_mode']), true) === true;
    }
    
    /**
     * Retourne les fonctionnalités de soulagement
     * 
     * @return array
     */
    private function get_relief_features() {
        return array(
            'dark_mode' => __('Mode sombre pour réduire la luminosité', 'accessibility-modular'),
            'brightness' => __('Contrôle de la luminosité globale', 'accessibility-modular'),
            'blue_light_filter' => __('Filtre de lumière bleue', 'accessibility-modular'),
            'saturation' => __('Désaturation des couleurs vives', 'accessibility-modular'),
            'contrast' => __('Ajustement du contraste', 'accessibility-modular'),
            'color_theme' => __('Teintes apaisantes (gris, ambré)', 'accessibility-modular'),
            'color_theme_intensity' => __('Intensité des teintes', 'accessibility-modular'),
            'remove_patterns' => __('Suppression des motifs répétitifs', 'accessibility-modular'),
            'increase_spacing' => __('Augmentation de l\'espace blanc', 'accessibility-modular')
        );
    }
    
    /**
     * Retourne les paramètres par défaut
     * 
     * @return array
     */
    public static function get_default_settings() {
        return array(
            'dark_mode' => false,
            'brightness' => 100,
            'blue_light_filter' => 0,
            'saturation' => 100,
            'contrast' => 100,
            'color_theme' => 'none',
            'color_theme_intensity' => 100,
            'remove_patterns' => false,
            'increase_spacing' => false
        );
    }
    
    /**
     * Valide les paramètres
     * 
     * @param array $settings Paramètres à valider
     * @return array
     */
    public static function validate_settings($settings) {
        $defaults = self::get_default_settings();
        $validated = array();
        
        // Validation de chaque paramètre
        if (isset($settings['dark_mode'])) { 
            $validated['dark_mode'] = (bool) $settings['dark_mode']; 
        }
        
        if (isset($settings['brightness'])) { 
            $b = intval($settings['brightness']); 
            if ($b >= 50 && $b <= 100) { 
                $validated['brightness'] = $b; 
            } 
        }
        
        if (isset($settings['blue_light_filter'])) { 
            $f = intval($settings['blue_light_filter']); 
            if ($f >= 0 && $f <= 100) { 
                $validated['blue_light_filter'] = $f; 
            } 
        }
        
        if (isset($settings['saturation'])) { 
            $s = intval($settings['saturation']); 
            if ($s >= 0 && $s <= 100) { 
                $validated['saturation'] = $s; 
            } 
        }
        
        if (isset($settings['contrast'])) { 
            $c = intval($settings['contrast']); 
            if ($c >= 80 && $c <= 150) { 
                $validated['contrast'] = $c; 
            } 
        }
        
        if (isset($settings['color_theme'])) { 
            $themes = array('none', 'grayscale', 'amber'); 
            if (in_array($settings['color_theme'], $themes)) { 
                $validated['color_theme'] = $settings['color_theme']; 
            } 
        }
        
        // Validation intensité
        if (isset($settings['color_theme_intensity'])) {
            $intensity = intval($settings['color_theme_intensity']);
            if ($intensity >= 0 && $intensity <= 100) {
                $validated['color_theme_intensity'] = $intensity;
            }
        }

        if (isset($settings['remove_patterns'])) { 
            $validated['remove_patterns'] = (bool) $settings['remove_patterns']; 
        }
        
        if (isset($settings['increase_spacing'])) { 
            $validated['increase_spacing'] = (bool) $settings['increase_spacing']; 
        }
        
        return array_merge($defaults, $validated);
    }
    
    /**
     * Nettoie les cookies du module
     */
    public static function clear_cookies() {
        $cookies = array(
            'acc_migraine_dark_mode',
            'acc_migraine_brightness',
            'acc_migraine_blue_light_filter',
            'acc_migraine_saturation',
            'acc_migraine_contrast',
            'acc_migraine_color_theme',
            'acc_migraine_color_theme_intensity',
            'acc_migraine_remove_patterns',
            'acc_migraine_increase_spacing'
        );
        
        foreach ($cookies as $cookie) {
            if (isset($_COOKIE[$cookie])) {
                setcookie($cookie, '', time() - 3600, '/');
            }
        }
    }
}

// Initialise le module
new ACC_Module_Migraine();