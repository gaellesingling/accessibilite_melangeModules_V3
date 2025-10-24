<?php
/*
Plugin Name: Braille Non Contracté (Grade 1)
Description: Module de traduction en braille non contracté (Grade 1) pour le français.
Version: 1.0
Author: Votre Nom
*/

if (!defined('ABSPATH')) exit;

define('BRAILLE_NON_CONTRACTED_VERSION', '1.0');
define('BRAILLE_NON_CONTRACTED_PATH', plugin_dir_path(__FILE__));
define('BRAILLE_NON_CONTRACTED_URL', plugin_dir_url(__FILE__));

require_once BRAILLE_NON_CONTRACTED_PATH . 'includes/class-braille-non-contracted.php';

class Braille_Non_Contracted {
    private static $instance;

    public static function get_instance() {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('wp_footer', [$this, 'add_braille_ui']);
        add_action('wp_ajax_braille_non_contract_translate', [$this, 'ajax_translate']);
        add_action('wp_ajax_nopriv_braille_non_contract_translate', [$this, 'ajax_translate']);
    }

    public function enqueue_scripts() {
        wp_enqueue_style(
            'braille-non-contracted',
            BRAILLE_NON_CONTRACTED_URL . 'public/css/braille-non-contracted.css',
            [],
            BRAILLE_NON_CONTRACTED_VERSION
        );

        wp_enqueue_script(
            'braille-non-contracted',
            BRAILLE_NON_CONTRACTED_URL . 'public/js/braille-non-contracted.js',
            ['jquery'],
            BRAILLE_NON_CONTRACTED_VERSION,
            true
        );

        wp_localize_script('braille-non-contracted', 'brailleNonContracted', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('braille_non_contract_nonce')
        ]);
    }

    public function add_braille_ui() {
        // Le HTML est généré par JavaScript
    }

    public function ajax_translate() {
        check_ajax_referer('braille_non_contract_nonce', 'nonce');

        if (empty($_POST['text'])) {
            wp_send_json_error(__('Aucun texte à traduire', 'braille-non-contracted'));
        }

        $text = sanitize_text_field($_POST['text']);
        $wrapper = new Braille_Non_Contracted_Wrapper();
        $translation = $wrapper->translate($text);

        if (is_wp_error($translation)) {
            wp_send_json_error($translation->get_error_message());
        }

        wp_send_json_success([
            'original' => $text,
            'braille' => $translation
        ]);
    }
}

// Initialisation
Braille_Non_Contracted::get_instance();
