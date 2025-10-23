<?php
/**
 * Module Aide Cataracte
 * Compense les défis visuels liés à la cataracte
 * 
 * @package AccessibilityModular
 * @subpackage Modules
 * @version 1.0.1
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe du module Aide Cataracte
 */
class ACC_Module_Cataracte {
    
    /**
     * Version du module
     */
    const VERSION = '1.0.1';
    
    /**
     * Nom du module
     */
    const MODULE_NAME = 'cataracte';
    
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
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }
    
    /**
     * Enqueue les scripts et styles
     */
    public function enqueue_scripts() {
        $module_path = plugin_dir_url(__FILE__);
        
        // Enqueue jQuery (dépendance)
        wp_enqueue_script('jquery');
        
        // Enqueue CSS
        wp_enqueue_style(
            'acc-cataracte-style',
            $module_path . 'style.css',
            array(),
            self::VERSION
        );
        
        // Enqueue JavaScript (avec jQuery comme dépendance)
        wp_enqueue_script(
            'acc-cataracte-script',
            $module_path . 'script.js',
            array('jquery'),
            self::VERSION,
            true
        );
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
        
        $metadata['features'] = $this->get_features();
        $metadata['wcag_compliance'] = array(
            'criteria' => array('1.4.3', '1.4.4', '1.4.6', '1.4.8', '1.4.11'),
            'level' => 'AAA'
        );
        $metadata['rgaa_compliance'] = array(
            'criteria' => array('3.2', '3.3', '10.4', '10.6', '10.11'),
            'level' => 'AAA'
        );
        $metadata['medical_conditions'] = array(
            'cataracte',
            'opacification_cristallin',
            'vision_floue',
            'sensibilite_lumiere',
            'eblouissement',
            'perception_couleurs_alteree',
            'mauvaise_perception_contrastes'
        );
        
        return $metadata;
    }
    
    /**
     * Retourne les fonctionnalités
     * 
     * @return array
     */
    private function get_features() {
        return array(
            'reduce_glare' => __('Réduction de l\'éblouissement', 'accessibility-modular'),
            'glare_intensity' => __('Intensité anti-éblouissement', 'accessibility-modular'),
            'color_correction' => __('Correction des couleurs', 'accessibility-modular'),
            'sharpness' => __('Amélioration de la netteté', 'accessibility-modular'),
            'remove_effects' => __('Suppression des effets visuels', 'accessibility-modular')
        );
    }
    
    /**
     * Retourne les paramètres par défaut
     * 
     * @return array
     */
    public static function get_default_settings() {
        return array(
            'reduce_glare' => false,
            'glare_intensity' => 20,
            'color_correction' => false,
            'sharpness' => false,
            'remove_effects' => false
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
        
        if (isset($settings['reduce_glare'])) {
            $validated['reduce_glare'] = (bool) $settings['reduce_glare'];
        }
        
        if (isset($settings['glare_intensity'])) {
            $intensity = intval($settings['glare_intensity']);
            if ($intensity >= 10 && $intensity <= 40) {
                $validated['glare_intensity'] = $intensity;
            }
        }
        
        if (isset($settings['color_correction'])) {
            $validated['color_correction'] = (bool) $settings['color_correction'];
        }
        
        if (isset($settings['sharpness'])) {
            $validated['sharpness'] = (bool) $settings['sharpness'];
        }
        
        if (isset($settings['remove_effects'])) {
            $validated['remove_effects'] = (bool) $settings['remove_effects'];
        }
        
        return array_merge($defaults, $validated);
    }
    
    /**
     * Nettoie les cookies du module
     */
    public static function clear_cookies() {
        $cookies = array(
            'acc_cataracte_active',
            'acc_cataracte_reduce_glare',
            'acc_cataracte_glare_intensity',
            'acc_cataracte_color_correction',
            'acc_cataracte_sharpness',
            'acc_cataracte_remove_effects'
        );
        
        foreach ($cookies as $cookie) {
            if (isset($_COOKIE[$cookie])) {
                setcookie($cookie, '', time() - 3600, '/');
            }
        }
    }
}

// Initialise le module
new ACC_Module_Cataracte();