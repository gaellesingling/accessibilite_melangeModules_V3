<?php
/**
 * Module Protection Épilepsie
 * Prévient les crises en bloquant animations et flashs
 * 
 * @package AccessibilityModular
 * @subpackage Modules
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe du module Protection Épilepsie
 */
class ACC_Module_Epilepsy {
    
    /**
     * Version du module
     */
    const VERSION = '1.0.0';
    
    /**
     * Nom du module
     */
    const MODULE_NAME = 'epilepsy';
    
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
        
        $metadata['safety_features'] = $this->get_safety_features();
        $metadata['wcag_compliance'] = array(
            'criteria' => array('2.3.1', '2.3.2', '2.3.3'),
            'level' => 'AAA'
        );
        $metadata['rgaa_compliance'] = array(
            'criteria' => array('13.8', '13.17'),
            'level' => 'AA'
        );
        
        return $metadata;
    }
    
    /**
     * Ajoute les meta tags pour la sécurité
     */
    public function add_meta_tags() {
        // Vérifier si le module est actif
        if ($this->is_reduce_motion_active()) {
            echo '<meta name="color-scheme" content="light dark">' . "\n";
            echo '<style>@media (prefers-reduced-motion: reduce) { *, *::before, *::after { animation-duration: 0.01ms !important; animation-iteration-count: 1 !important; transition-duration: 0.01ms !important; scroll-behavior: auto !important; } }</style>' . "\n";
        }
    }
    
    /**
     * Vérifie si le mode réduction de mouvement est actif
     * 
     * @return bool
     */
    private function is_reduce_motion_active() {
        return isset($_COOKIE['acc_epilepsy_reduce_motion']) && 
               json_decode(stripslashes($_COOKIE['acc_epilepsy_reduce_motion']), true) === true;
    }
    
    /**
     * Retourne les fonctionnalités de sécurité
     * 
     * @return array
     */
    private function get_safety_features() {
        return array(
            'stop_animations' => __('Arrêt des animations CSS/JS', 'accessibility-modular'),
            'stop_gifs' => __('Blocage des GIFs animés', 'accessibility-modular'),
            'stop_videos' => __('Arrêt des vidéos automatiques', 'accessibility-modular'),
            'remove_parallax' => __('Suppression des effets parallax', 'accessibility-modular'),
            'reduce_motion' => __('Réduction globale des mouvements', 'accessibility-modular'),
            'block_flashing' => __('Blocage des flashs rapides', 'accessibility-modular')
        );
    }
    
    /**
     * Retourne les paramètres par défaut
     * 
     * @return array
     */
    public static function get_default_settings() {
        return array(
            'stop_animations' => false,
            'stop_gifs' => false,
            'stop_videos' => false,
            'remove_parallax' => false,
            'reduce_motion' => false,
            'block_flashing' => false
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
        
        // Validation de tous les paramètres booléens
        foreach ($defaults as $key => $default_value) {
            if (isset($settings[$key])) {
                $validated[$key] = (bool) $settings[$key];
            }
        }
        
        return array_merge($defaults, $validated);
    }
    
    /**
     * Nettoie les cookies du module
     */
    public static function clear_cookies() {
        $cookies = array(
            'acc_epilepsy_stop_animations',
            'acc_epilepsy_stop_gifs',
            'acc_epilepsy_stop_videos',
            'acc_epilepsy_remove_parallax',
            'acc_epilepsy_reduce_motion',
            'acc_epilepsy_block_flashing'
        );
        
        foreach ($cookies as $cookie) {
            if (isset($_COOKIE[$cookie])) {
                setcookie($cookie, '', time() - 3600, '/');
            }
        }
    }
}

// Initialise le module
new ACC_Module_Epilepsy();