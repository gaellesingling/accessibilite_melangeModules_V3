<?php
/**
 * Module Text-to-Speech
 * Synthèse vocale pour lire le contenu de la page
 * 
 * @package AccessibilityModular
 * @subpackage Modules
 * @version 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class ACC_Module_Text_To_Speech {
    
    private $module_name = 'text-to-speech';
    private $module_path;
    private $config;
    
    public function __construct() {
        $this->module_path = ACC_MODULES_DIR . $this->module_name . '/';
        $this->load_config();
        $this->init_hooks();
    }
    
    /**
     * Charge la configuration du module
     */
    private function load_config() {
        $config_file = $this->module_path . 'config.json';
        
        if (file_exists($config_file)) {
            $config_content = file_get_contents($config_file);
            $this->config = json_decode($config_content, true);
        }
    }
    
    /**
     * Initialise les hooks WordPress
     */
    private function init_hooks() {
        // Enregistrer les paramètres admin
        add_action('admin_init', [$this, 'register_settings']);
        
        // Ajouter les champs dans l'admin
        add_action('acc_admin_settings_fields', [$this, 'render_admin_fields']);
        
        // Traiter l'ajout/suppression de clés API
        add_action('admin_post_acc_tts_add_api_key', [$this, 'handle_add_api_key']);
        add_action('admin_post_acc_tts_remove_api_key', [$this, 'handle_remove_api_key']);
        add_action('admin_post_acc_tts_reset_usage', [$this, 'handle_reset_usage']);
        
        // Enqueue les assets (déjà géré par le plugin principal)
        // Les fichiers script.js et style.css seront chargés automatiquement
    }
    
    /**
     * Enregistre les paramètres du module
     */
    public function register_settings() {
        // Liste des clés API avec leurs limites
        register_setting('acc_settings_group', 'acc_tts_api_keys', [
            'type' => 'string',
            'sanitize_callback' => [$this, 'sanitize_api_keys'],
            'default' => json_encode([])
        ]);
        
        // Activer le module
        register_setting('acc_settings_group', 'acc_tts_enabled', [
            'type' => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
            'default' => true
        ]);
        
        // Activer la rotation automatique
        register_setting('acc_settings_group', 'acc_tts_auto_rotate', [
            'type' => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
            'default' => true
        ]);
    }
    
    /**
     * Sanitize les clés API
     */
    public function sanitize_api_keys($value) {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $value;
            }
        }
        return json_encode([]);
    }
    
    /**
     * Récupère toutes les clés API
     */
    public function get_api_keys() {
        $keys_json = get_option('acc_tts_api_keys', '[]');
        $keys = json_decode($keys_json, true);
        return is_array($keys) ? $keys : [];
    }
    
    /**
     * Sauvegarde les clés API
     */
    private function save_api_keys($keys) {
        update_option('acc_tts_api_keys', json_encode($keys));
    }
    
    /**
     * Récupère la clé API active
     */
    public function get_active_api_key() {
        $keys = $this->get_api_keys();
        
        if (empty($keys)) {
            return null;
        }
        
        // Chercher la première clé active avec de l'usage disponible
        foreach ($keys as $key) {
            if ($key['active'] && $key['usage'] < $key['limit']) {
                return $key['key'];
            }
        }
        
        // Si rotation automatique activée, réinitialiser et retourner la première
        if (get_option('acc_tts_auto_rotate', true)) {
            $this->reset_all_usage();
            return !empty($keys) ? $keys[0]['key'] : null;
        }
        
        return null;
    }
    
    /**
     * Incrémente l'usage d'une clé API
     */
    public function increment_api_usage($api_key) {
        $keys = $this->get_api_keys();
        
        foreach ($keys as &$key) {
            if ($key['key'] === $api_key) {
                $key['usage']++;
                $key['last_used'] = current_time('mysql');
                
                // Si limite atteinte, désactiver la clé
                if ($key['usage'] >= $key['limit']) {
                    $key['active'] = false;
                }
                
                $this->save_api_keys($keys);
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Réinitialise l'usage de toutes les clés
     */
    private function reset_all_usage() {
        $keys = $this->get_api_keys();
        
        foreach ($keys as &$key) {
            $key['usage'] = 0;
            $key['active'] = true;
        }
        
        $this->save_api_keys($keys);
    }
    
    /**
     * Traite l'ajout d'une clé API
     */
    public function handle_add_api_key() {
        if (!current_user_can('manage_options')) {
            wp_die('Accès non autorisé');
        }
        
        check_admin_referer('acc_tts_add_api_key');
        
        $api_key = sanitize_text_field($_POST['api_key'] ?? '');
        $limit = intval($_POST['limit'] ?? 1000);
        $label = sanitize_text_field($_POST['label'] ?? 'Clé API ' . (count($this->get_api_keys()) + 1));
        
        if (empty($api_key)) {
            wp_redirect(add_query_arg('acc_tts_error', 'empty_key', wp_get_referer()));
            exit;
        }
        
        $keys = $this->get_api_keys();
        
        // Vérifier si la clé existe déjà
        foreach ($keys as $key) {
            if ($key['key'] === $api_key) {
                wp_redirect(add_query_arg('acc_tts_error', 'duplicate_key', wp_get_referer()));
                exit;
            }
        }
        
        // Ajouter la nouvelle clé
        $keys[] = [
            'key' => $api_key,
            'label' => $label,
            'limit' => $limit,
            'usage' => 0,
            'active' => true,
            'created' => current_time('mysql'),
            'last_used' => null
        ];
        
        $this->save_api_keys($keys);
        
        wp_redirect(add_query_arg('acc_tts_success', 'key_added', wp_get_referer()));
        exit;
    }
    
    /**
     * Traite la suppression d'une clé API
     */
    public function handle_remove_api_key() {
        if (!current_user_can('manage_options')) {
            wp_die('Accès non autorisé');
        }
        
        check_admin_referer('acc_tts_remove_api_key');
        
        $index = intval($_GET['index'] ?? -1);
        $keys = $this->get_api_keys();
        
        if (isset($keys[$index])) {
            array_splice($keys, $index, 1);
            $this->save_api_keys($keys);
        }
        
        wp_redirect(remove_query_arg(['action', 'index', '_wpnonce'], wp_get_referer()));
        exit;
    }
    
    /**
     * Traite la réinitialisation de l'usage
     */
    public function handle_reset_usage() {
        if (!current_user_can('manage_options')) {
            wp_die('Accès non autorisé');
        }
        
        check_admin_referer('acc_tts_reset_usage');
        
        $this->reset_all_usage();
        
        wp_redirect(add_query_arg('acc_tts_success', 'usage_reset', wp_get_referer()));
        exit;
    }
    
    /**
     * Affiche les champs de configuration dans l'admin
     */
    public function render_admin_fields() {
        $enabled = get_option('acc_tts_enabled', true);
        $auto_rotate = get_option('acc_tts_auto_rotate', true);
        $api_keys = $this->get_api_keys();
        
        // Messages de succès/erreur
        if (isset($_GET['acc_tts_success'])) {
            $messages = [
                'key_added' => 'Clé API ajoutée avec succès !',
                'usage_reset' => 'Compteurs réinitialisés avec succès !'
            ];
            $message = $messages[$_GET['acc_tts_success']] ?? 'Action réussie';
            echo '<div class="notice notice-success is-dismissible"><p><strong>✓</strong> ' . esc_html($message) . '</p></div>';
        }
        
        if (isset($_GET['acc_tts_error'])) {
            $errors = [
                'empty_key' => 'La clé API ne peut pas être vide',
                'duplicate_key' => 'Cette clé API existe déjà'
            ];
            $error = $errors[$_GET['acc_tts_error']] ?? 'Une erreur est survenue';
            echo '<div class="notice notice-error is-dismissible"><p><strong>✗</strong> ' . esc_html($error) . '</p></div>';
        }
        
        ?>
        <style>
        .acc-tts-admin {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .acc-tts-admin h2 {
            margin-top: 0;
            padding-bottom: 12px;
            border-bottom: 2px solid #2271b1;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .acc-api-keys-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: white;
            border: 1px solid #ddd;
            border-radius: 4px;
            overflow: hidden;
        }
        .acc-api-keys-table th {
            background: #f6f7f7;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid #ddd;
        }
        .acc-api-keys-table td {
            padding: 12px;
            border-bottom: 1px solid #f0f0f0;
        }
        .acc-api-keys-table tr:last-child td {
            border-bottom: none;
        }
        .acc-api-key-masked {
            font-family: monospace;
            background: #f6f7f7;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 12px;
        }
        .acc-progress-bar {
            width: 100%;
            height: 20px;
            background: #f0f0f0;
            border-radius: 10px;
            overflow: hidden;
            position: relative;
        }
        .acc-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #10b981 0%, #059669 100%);
            transition: width 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 11px;
            font-weight: 600;
        }
        .acc-progress-fill.warning {
            background: linear-gradient(90deg, #f59e0b 0%, #d97706 100%);
        }
        .acc-progress-fill.danger {
            background: linear-gradient(90deg, #ef4444 0%, #dc2626 100%);
        }
        .acc-status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .acc-status-badge.active {
            background: #d1fae5;
            color: #065f46;
        }
        .acc-status-badge.inactive {
            background: #fee2e2;
            color: #991b1b;
        }
        .acc-add-key-form {
            background: #f9fafb;
            padding: 20px;
            border-radius: 8px;
            border: 2px dashed #d1d5db;
            margin: 20px 0;
        }
        .acc-add-key-form h3 {
            margin-top: 0;
            color: #374151;
        }
        .acc-form-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }
        .acc-form-field label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #374151;
        }
        .acc-form-field input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            font-size: 14px;
        }
        .acc-form-field input:focus {
            outline: none;
            border-color: #2271b1;
            box-shadow: 0 0 0 3px rgba(34, 113, 177, 0.1);
        }
        .acc-info-box {
            background: #eff6ff;
            border-left: 4px solid #2271b1;
            padding: 16px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .acc-info-box h4 {
            margin-top: 0;
            color: #1e40af;
        }
        .acc-stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin: 20px 0;
        }
        .acc-stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            text-align: center;
        }
        .acc-stat-value {
            font-size: 32px;
            font-weight: bold;
            color: #2271b1;
            margin: 10px 0;
        }
        .acc-stat-label {
            color: #6b7280;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .acc-action-buttons {
            display: flex;
            gap: 10px;
        }
        </style>
        
        <div class="acc-tts-admin" id="acc-tts-settings">
            <h2>
                <span class="dashicons dashicons-controls-volumeon"></span>
                Configuration Text-to-Speech - Gestion des API
            </h2>
            
            <p class="description">
                Ce module utilise la <strong>Web Speech API native</strong> du navigateur (gratuit, sans limite).
                <br>Vous pouvez également configurer des clés API cloud pour une qualité vocale premium avec rotation automatique.
            </p>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="acc_tts_enabled">Activer le module</label>
                    </th>
                    <td>
                        <label class="acc-module-toggle">
                            <input
                                type="hidden"
                                name="acc_tts_enabled"
                                value="0"
                            />
                            <input
                                type="checkbox"
                                id="acc_tts_enabled"
                                name="acc_tts_enabled"
                                value="1"
                                <?php checked($enabled, true); ?>
                            />
                            <span class="acc-module-toggle-slider"></span>
                        </label>
                        <p class="description">
                            Activer/désactiver la fonctionnalité Text-to-Speech sur le site
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="acc_tts_auto_rotate">Rotation automatique</label>
                    </th>
                    <td>
                        <label class="acc-module-toggle">
                            <input
                                type="hidden"
                                name="acc_tts_auto_rotate"
                                value="0"
                            />
                            <input
                                type="checkbox"
                                id="acc_tts_auto_rotate"
                                name="acc_tts_auto_rotate"
                                value="1"
                                <?php checked($auto_rotate, true); ?>
                            />
                            <span class="acc-module-toggle-slider"></span>
                        </label>
                        <p class="description">
                            Réinitialiser automatiquement les compteurs lorsque toutes les clés ont atteint leur limite
                        </p>
                    </td>
                </tr>
            </table>
            
            <?php if (!empty($api_keys)): 
                // Calcul des statistiques
                $total_keys = count($api_keys);
                $active_keys = count(array_filter($api_keys, function($k) { return $k['active']; }));
                $total_usage = array_sum(array_column($api_keys, 'usage'));
                $total_limit = array_sum(array_column($api_keys, 'limit'));
            ?>
            
            <!-- Statistiques -->
            <div class="acc-stats-grid">
                <div class="acc-stat-card">
                    <div class="acc-stat-label">Clés totales</div>
                    <div class="acc-stat-value"><?php echo $total_keys; ?></div>
                </div>
                <div class="acc-stat-card">
                    <div class="acc-stat-label">Clés actives</div>
                    <div class="acc-stat-value" style="color: #10b981;"><?php echo $active_keys; ?></div>
                </div>
                <div class="acc-stat-card">
                    <div class="acc-stat-label">Usage total</div>
                    <div class="acc-stat-value" style="color: #f59e0b;"><?php echo number_format($total_usage); ?></div>
                </div>
                <div class="acc-stat-card">
                    <div class="acc-stat-label">Limite totale</div>
                    <div class="acc-stat-value" style="color: #6366f1;"><?php echo number_format($total_limit); ?></div>
                </div>
            </div>
            
            <!-- Liste des clés API -->
            <h3>📋 Liste des clés API</h3>
            <table class="acc-api-keys-table">
                <thead>
                    <tr>
                        <th>Label</th>
                        <th>Clé API</th>
                        <th>Statut</th>
                        <th style="width: 250px;">Utilisation</th>
                        <th>Dernière utilisation</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($api_keys as $index => $key): 
                        $percentage = ($key['limit'] > 0) ? ($key['usage'] / $key['limit']) * 100 : 0;
                        $progress_class = '';
                        if ($percentage >= 90) {
                            $progress_class = 'danger';
                        } elseif ($percentage >= 70) {
                            $progress_class = 'warning';
                        }
                    ?>
                    <tr>
                        <td><strong><?php echo esc_html($key['label']); ?></strong></td>
                        <td>
                            <span class="acc-api-key-masked">
                                <?php echo esc_html(substr($key['key'], 0, 8)) . '••••••••' . esc_html(substr($key['key'], -4)); ?>
                            </span>
                        </td>
                        <td>
                            <span class="acc-status-badge <?php echo $key['active'] ? 'active' : 'inactive'; ?>">
                                <?php echo $key['active'] ? '✓ Active' : '✗ Inactive'; ?>
                            </span>
                        </td>
                        <td>
                            <div class="acc-progress-bar">
                                <div class="acc-progress-fill <?php echo $progress_class; ?>" style="width: <?php echo min(100, $percentage); ?>%">
                                    <?php echo number_format($key['usage']); ?> / <?php echo number_format($key['limit']); ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php 
                            if ($key['last_used']) {
                                echo esc_html(human_time_diff(strtotime($key['last_used']), current_time('timestamp'))) . ' ago';
                            } else {
                                echo '<em style="color: #9ca3af;">Jamais utilisée</em>';
                            }
                            ?>
                        </td>
                        <td>
                            <div class="acc-action-buttons">
                                <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=acc_tts_remove_api_key&index=' . $index), 'acc_tts_remove_api_key'); ?>" 
                                   class="button button-small"
                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette clé API ?')">
                                    🗑️ Supprimer
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <p>
                <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=acc_tts_reset_usage'), 'acc_tts_reset_usage'); ?>" 
                   class="button button-secondary"
                   onclick="return confirm('Réinitialiser tous les compteurs d\'utilisation ?')">
                    🔄 Réinitialiser tous les compteurs
                </a>
            </p>
            
            <?php endif; ?>
            
            <!-- Formulaire d'ajout de clé -->
            <div class="acc-add-key-form">
                <h3>➕ Ajouter une nouvelle clé API</h3>
                <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                    <?php wp_nonce_field('acc_tts_add_api_key'); ?>
                    <input type="hidden" name="action" value="acc_tts_add_api_key">
                    
                    <div class="acc-form-grid">
                        <div class="acc-form-field">
                            <label for="api_key">Clé API *</label>
                            <input 
                                type="text" 
                                id="api_key" 
                                name="api_key" 
                                placeholder="sk-proj-xxxxxxxxxxxxxxxx"
                                required
                            />
                        </div>
                        
                        <div class="acc-form-field">
                            <label for="label">Label</label>
                            <input 
                                type="text" 
                                id="label" 
                                name="label" 
                                placeholder="Ex: API Production"
                                value="Clé API <?php echo count($api_keys) + 1; ?>"
                            />
                        </div>
                        
                        <div class="acc-form-field">
                            <label for="limit">Limite (requêtes) *</label>
                            <input 
                                type="number" 
                                id="limit" 
                                name="limit" 
                                placeholder="1000"
                                value="1000"
                                min="1"
                                required
                            />
                        </div>
                    </div>
                    
                    <button type="submit" class="button button-primary">
                        ➕ Ajouter la clé API
                    </button>
                </form>
            </div>
            
            <!-- Informations -->
            <div class="acc-info-box">
                <h4>ℹ️ Fonctionnement de la rotation automatique</h4>
                <ul style="margin: 10px 0; line-height: 1.8;">
                    <li><strong>Sans clé API :</strong> Le module utilise la Web Speech API native du navigateur (gratuit, illimité)</li>
                    <li><strong>Avec clés API :</strong> Le module utilisera les clés dans l'ordre jusqu'à atteindre leur limite</li>
                    <li><strong>Rotation automatique :</strong> Lorsque toutes les clés ont atteint leur limite, les compteurs se réinitialisent automatiquement</li>
                    <li><strong>Clé désactivée :</strong> Une clé devient automatiquement inactive quand elle atteint sa limite</li>
                </ul>
            </div>
            
            <details style="margin-top: 20px;">
                <summary style="cursor: pointer; color: #2271b1; font-weight: 600; font-size: 14px;">
                    📚 Comment obtenir une clé API ? (Google Cloud, Amazon, Azure)
                </summary>
                <div style="margin-top: 12px; padding: 12px; background: #f6f7f7; border-radius: 4px;">
                    <h4>Google Cloud Text-to-Speech</h4>
                    <ol style="margin-left: 20px; line-height: 1.8;">
                        <li>Aller sur <a href="https://console.cloud.google.com" target="_blank">Google Cloud Console</a></li>
                        <li>Créer un projet</li>
                        <li>Activer l'API "Cloud Text-to-Speech"</li>
                        <li>Créer des identifiants → Clé API</li>
                        <li>Copier et coller la clé ci-dessus</li>
                    </ol>
                    
                    <h4 style="margin-top: 20px;">Amazon Polly</h4>
                    <ol style="margin-left: 20px; line-height: 1.8;">
                        <li>Aller sur <a href="https://aws.amazon.com/polly/" target="_blank">AWS Polly</a></li>
                        <li>Créer un compte AWS</li>
                        <li>Générer des clés d'accès IAM</li>
                        <li>Configurer les permissions pour Polly</li>
                    </ol>
                    
                    <h4 style="margin-top: 20px;">Microsoft Azure Speech</h4>
                    <ol style="margin-left: 20px; line-height: 1.8;">
                        <li>Aller sur <a href="https://azure.microsoft.com/services/cognitive-services/text-to-speech/" target="_blank">Azure Portal</a></li>
                        <li>Créer une ressource "Speech Services"</li>
                        <li>Copier la clé API depuis les paramètres</li>
                    </ol>
                </div>
            </details>
        </div>
        <?php
    }
    
    /**
     * Vérifie si le module est actif
     */
    public function is_enabled() {
        return (bool) get_option('acc_tts_enabled', true);
    }
    
    /**
     * Récupère les informations du module
     */
    public function get_info() {
        $api_keys = $this->get_api_keys();
        return [
            'name' => $this->module_name,
            'title' => $this->config['title'] ?? 'Text-to-Speech',
            'description' => $this->config['description'] ?? '',
            'version' => $this->config['version'] ?? '1.1.0',
            'icon' => $this->config['icon'] ?? '🔊',
            'category' => $this->config['category'] ?? 'lecture',
            'enabled' => $this->is_enabled(),
            'api_keys_count' => count($api_keys),
            'active_api_key' => $this->get_active_api_key() !== null
        ];
    }
}

// Initialiser le module
new ACC_Module_Text_To_Speech();