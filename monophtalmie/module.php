<?php
/**
 * Module Aide Monophtalmie
 * Compense les défis visuels de la vision monoculaire
 * 
 * @package AccessibilityModular
 * @subpackage Modules
 * @version 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe du module Aide Monophtalmie
 */
class ACC_Module_Monophtalmie {
    
    /**
     * Version du module
     */
    const VERSION = '1.1.0';
    
    /**
     * Nom du module
     */
    const MODULE_NAME = 'monophtalmie';
    
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
            'criteria' => array('1.4.3', '1.4.4', '1.4.8', '1.4.10', '2.5.5'),
            'level' => 'AAA'
        );
        $metadata['rgaa_compliance'] = array(
            'criteria' => array('3.2', '3.3', '10.4', '10.11'),
            'level' => 'AAA'
        );
        $metadata['medical_conditions'] = array(
            'monophtalmie',
            'vision_monoculaire',
            'champ_visuel_reduit',
            'vision_basse',
            'absence_vision_stereoscopique'
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
            'magnifier' => __('Loupe qui suit le curseur', 'accessibility-modular'),
            'magnifier_zoom' => __('Zoom réglable (150% à 400%)', 'accessibility-modular'),
            'depth_indicators' => __('Ombres accentuées pour la profondeur', 'accessibility-modular'),
            'reduce_field' => __('Adaptation du champ visuel', 'accessibility-modular'),
            'field_position' => __('Position du contenu (gauche/centre/droite)', 'accessibility-modular'),
            'low_vision_mode' => __('Mode vision basse (contraste + zoom)', 'accessibility-modular')
        );
    }
    
    /**
     * Retourne les paramètres par défaut
     * 
     * @return array
     */
    public static function get_default_settings() {
        return array(
            'magnifier' => false,
            'magnifier_zoom' => 200,
            'depth_indicators' => false,
            'reduce_field' => false,
            'field_position' => 'center',
            'low_vision_mode' => false
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
        
        if (isset($settings['magnifier'])) {
            $validated['magnifier'] = (bool) $settings['magnifier'];
        }
        
        if (isset($settings['magnifier_zoom'])) {
            $zoom = intval($settings['magnifier_zoom']);
            if ($zoom >= 150 && $zoom <= 400) {
                $validated['magnifier_zoom'] = $zoom;
            }
        }
        
        if (isset($settings['depth_indicators'])) {
            $validated['depth_indicators'] = (bool) $settings['depth_indicators'];
        }
        
        if (isset($settings['reduce_field'])) {
            $validated['reduce_field'] = (bool) $settings['reduce_field'];
        }
        
        if (isset($settings['field_position'])) {
            $position = sanitize_text_field($settings['field_position']);
            if (in_array($position, array('left', 'center', 'right'))) {
                $validated['field_position'] = $position;
            }
        }
        
        if (isset($settings['low_vision_mode'])) {
            $validated['low_vision_mode'] = (bool) $settings['low_vision_mode'];
        }
        
        return array_merge($defaults, $validated);
    }
    
    /**
     * Nettoie les cookies du module
     */
    public static function clear_cookies() {
        $cookies = array(
            'acc_monophtalmie_magnifier',
            'acc_monophtalmie_magnifier_zoom',
            'acc_monophtalmie_depth_indicators',
            'acc_monophtalmie_reduce_field',
            'acc_monophtalmie_field_position',
            'acc_monophtalmie_low_vision_mode'
        );
        
        foreach ($cookies as $cookie) {
            if (isset($_COOKIE[$cookie])) {
                setcookie($cookie, '', time() - 3600, '/');
            }
        }
    }
}

// Initialise le module
new ACC_Module_Monophtalmie();
