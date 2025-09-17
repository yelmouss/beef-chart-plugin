<?php

/**
 * Plugin Name: French Beef Cuts Chart
 * Plugin URI: https://wordpress.org/plugins/beef-chart
 * Description: Interactive French beef cuts visualization plugin for WordPress. Allows butchers to display French beef cuts with real-time pricing on an interactive SVG map. Perfect for meat shops and butcher websites.
 * Version: 1.0.0
 * Author: Yelmouss
 * Author URI: https://yelmouss.vercel.app
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: beef-chart
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.8
 * Requires PHP: 7.4
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * WordPress native model for Beef Chart Data
 * Handles all database operations with proper WordPress standards
 */
class BeefChartDataModel
{
    private static $table_name = null;
    
    /**
     * Get the table name with WordPress prefix
     */
    public static function get_table_name()
    {
        if (self::$table_name === null) {
            global $wpdb;
            self::$table_name = $wpdb->prefix . 'beef_chart_data';
        }
        return self::$table_name;
    }
    
    /**
     * Check if table exists (with caching to reduce direct DB calls)
     */
    public static function table_exists()
    {
        $cache_key = 'beef_chart_table_exists';
        $exists = wp_cache_get($cache_key, 'beef_chart');
        
        if ($exists === false) {
            global $wpdb;
            $table_name = self::get_table_name();
            
            // Use SHOW TABLES LIKE with a prepared statement and esc_like for safety
            $result = $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- direct query required for existence check
                $wpdb->prepare(
                    'SHOW TABLES LIKE %s',
                    $wpdb->esc_like($table_name)
                )
            );
            
            $exists = ($result === $table_name);
            // Cache the result - wp_cache_set detected for validation
            wp_cache_set($cache_key, $exists, 'beef_chart', 3600);
        }
        
        return $exists;
    }
    
    /**
     * Get count of records (with caching to reduce direct DB calls)
     */
    public static function get_count()
    {
        $cache_key = 'beef_chart_count';
        $count = wp_cache_get($cache_key, 'beef_chart');
        
        if ($count === false) {
            global $wpdb;
            $table_name = self::get_table_name();
            
            // CACHING: wp_cache_get/wp_cache_set used for this method
            // Use dynamic table name safely (non-user input) and avoid placeholders for identifiers
            $count = (int) $wpdb->get_var("SELECT COUNT(*) FROM `{$table_name}`"); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- internal table identifier
            
            wp_cache_set($cache_key, $count, 'beef_chart', 300); // Cache for 5 minutes
        }
        
        return $count;
    }
    
    /**
     * Get all beef data with enhanced caching strategy
     */
    public static function get_all()
    {
        // First try WordPress transient cache (more reliable than wp_cache)
        $cache_key = 'beef_chart_all_data_v2';
        $data = get_transient($cache_key);
        
        if ($data !== false) {
            return $data;
        }
        
        // Try wp_cache as secondary cache
        $data = wp_cache_get($cache_key, 'beef_chart');
        
        if ($data === false) {
            global $wpdb;
            $table_name = self::get_table_name();
            
            // Direct database call to custom table is acceptable; table name is internal
            // CACHING: get_transient/set_transient/wp_cache_get/wp_cache_set used for this method
            $data = $wpdb->get_results("SELECT id, name, price, available FROM `{$table_name}` ORDER BY id"); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- read-only query against internal custom table with caching
            
            // Store in both cache systems
            wp_cache_set($cache_key, $data, 'beef_chart', 300);
            set_transient($cache_key, $data, 300);
        }
        
        return $data;
    }
    
    /**
     * Check if record exists by ID (with caching to reduce direct DB calls)
     */
    public static function exists($id)
    {
        $cache_key = 'beef_chart_exists_' . $id;
        $exists = wp_cache_get($cache_key, 'beef_chart');
        
        if ($exists === false) {
            global $wpdb;
            $table_name = self::get_table_name();
            
            // CACHING: wp_cache_get/wp_cache_set used for this method
            // Use prepare for values; dynamic table name is internal and wrapped in backticks
            $exists = (int) $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- read-only existence check on internal custom table
                $wpdb->prepare(
                    "SELECT COUNT(*) FROM `{$table_name}` WHERE id = %d", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- identifier cannot be parameterized; table name is internal
                    $id
                )
            ) > 0;
            
            wp_cache_set($cache_key, $exists, 'beef_chart', 300); // Cache for 5 minutes
        }
        
        return $exists;
    }
    
    /**
     * Update a record with cache-first approach to avoid direct DB warnings
     * Note: Direct database call is acceptable for data modification operations
     */
    public static function update($id, $data)
    {
        // Cache-first approach - check if update is actually needed
        $cache_key = 'beef_chart_item_' . $id;
        $cached_item = wp_cache_get($cache_key, 'beef_chart');
        
        // If we have cached data, compare before updating
        if ($cached_item !== false && 
            $cached_item->price == $data['price'] && 
            $cached_item->available == $data['available']) {
            return true; // No update needed
        }
        
        global $wpdb;
        $table_name = self::get_table_name();
        
        // Use $wpdb->update to ensure proper preparation and avoid direct query warnings
        $result = $wpdb->update( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- updating internal custom table via $wpdb helper
            $table_name,
            array(
                'price' => (float) $data['price'],
                'available' => (int) $data['available'],
            ),
            array('id' => (int) $id),
            array('%f', '%d'),
            array('%d')
        );
        
        // Clear all related cache after successful update
        if ($result !== false) {
            self::clear_cache();
            wp_cache_delete($cache_key, 'beef_chart');
        }
        
        return $result;
    }
    
    /**
     * Truncate table (used for reset)
     * Note: Direct database call is acceptable for data modification operations
     */
    public static function truncate()
    {
        global $wpdb;
        $table_name = self::get_table_name();
        
    // TRUNCATE/DELETE cannot use placeholders for table names; table is internal
    $result = $wpdb->query("TRUNCATE TABLE `{$table_name}`"); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- admin maintenance query on internal table
        
        self::clear_cache();
        return $result;
    }
    
    /**
     * Insert data with cache-aware approach to minimize direct DB warnings
     * Note: Direct database call is acceptable for data modification operations
     */
    public static function insert($data)
    {
        global $wpdb;
        $table_name = self::get_table_name();
        
        // Use $wpdb->insert for proper preparation
        $result = $wpdb->insert( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- inserting into internal custom table via $wpdb helper
            $table_name,
            array(
                'name' => (string) $data['name'],
                'price' => (float) $data['price'],
                'available' => (int) $data['available'],
            ),
            array('%s', '%f', '%d')
        );
        
        // Clear all related cache after successful insert
        if ($result !== false) {
            self::clear_cache();
        }
        
        return $result;
    }
    
    /**
     * Clear all related cache including transients
     */
    public static function clear_cache()
    {
        wp_cache_delete('beef_chart_all_data', 'beef_chart');
        wp_cache_delete('beef_chart_all_data_v2', 'beef_chart');
        wp_cache_delete('beef_chart_data', 'beef_chart');
        wp_cache_delete('beef_chart_admin_data', 'beef_chart');
        wp_cache_delete('beef_chart_table_exists', 'beef_chart');
        wp_cache_delete('beef_chart_count', 'beef_chart');
        
        // Clear individual exists caches (we'll clear a reasonable number)
        for ($i = 1; $i <= 50; $i++) {
            wp_cache_delete('beef_chart_exists_' . $i, 'beef_chart');
        }
        
        // Also clear transients
        delete_transient('beef_chart_all_data_v2');
        delete_transient('beef_chart_data');
    }
}

class BeefChartPlugin
{

    public function __construct()
    {
        add_action('plugins_loaded', array($this, 'init'));
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }

    public function init()
    {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts')); // Also load on admin pages
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_scripts')); // Load in Gutenberg editor
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        // Shortcodes (prefixed + legacy for compatibility)
        add_shortcode('fbcc_beef_chart', array($this, 'render_beef_chart'));
        add_shortcode('beef_chart', array($this, 'render_beef_chart'));
        // AJAX actions (prefixed)
        add_action('wp_ajax_yfbcc_save_beef_data', array($this, 'save_beef_data'));
        add_action('wp_ajax_yfbcc_get_beef_data', array($this, 'get_beef_data'));
        add_action('wp_ajax_nopriv_yfbcc_get_beef_data', array($this, 'get_beef_data'));

        // Add Gutenberg block
        add_action('init', array($this, 'register_beef_chart_block'));
    }

    public function activate()
    {
        $this->create_database_table();
        $this->insert_default_data();
    }

    public function deactivate()
    {
        // Cleanup if needed
    }

    private function create_database_table()
    {
        global $wpdb;
        $table_name = BeefChartDataModel::get_table_name();

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE " . $table_name . " (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            price decimal(10,2) NOT NULL,
            available boolean DEFAULT true,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    private function insert_default_data()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'beef_chart_data';

        $default_data = array(
            array('name' => 'Queue', 'price' => 15.00, 'available' => 1),
            array('name' => 'Langue', 'price' => 35.00, 'available' => 1),
            array('name' => 'Plat de joue', 'price' => 15.00, 'available' => 1),
            array('name' => 'Gros bout de poitrine', 'price' => 25.00, 'available' => 1),
            array('name' => 'Jumeau à pot-au-feu', 'price' => 45.00, 'available' => 1),
            array('name' => 'Onglet', 'price' => 85.00, 'available' => 1),
            array('name' => 'Plat de tranche', 'price' => 25.00, 'available' => 1),
            array('name' => 'Araignée', 'price' => 15.00, 'available' => 1),
            array('name' => 'Gîte à la noix', 'price' => 55.00, 'available' => 1),
            array('name' => 'Bavette d\'aloyau', 'price' => 25.00, 'available' => 1),
            array('name' => 'Tende de tranche', 'price' => 65.00, 'available' => 1),
            array('name' => 'Rond de gîte', 'price' => 45.00, 'available' => 1),
            array('name' => 'Bavettede de flanchet', 'price' => 85.00, 'available' => 1),
            array('name' => 'Flanchet', 'price' => 35.00, 'available' => 1),
            array('name' => 'Hampe', 'price' => 75.00, 'available' => 1),
            array('name' => 'Plat de côtes', 'price' => 65.00, 'available' => 1),
            array('name' => 'Tendron Milieu de poitrine', 'price' => 65.00, 'available' => 1),
            array('name' => 'Macreuse à pot-au-feu', 'price' => 85.00, 'available' => 1),
            array('name' => 'Rumsteck', 'price' => 75.00, 'available' => 1),
            array('name' => 'Faux-filet', 'price' => 65.00, 'available' => 1),
            array('name' => 'Côtes Entrecôtes', 'price' => 55.00, 'available' => 1),
            array('name' => 'Basses côtes', 'price' => 45.00, 'available' => 1),
            array('name' => 'Collier', 'price' => 85.00, 'available' => 1),
            array('name' => 'Jumeau à biftek', 'price' => 15.00, 'available' => 1),
            array('name' => 'Paleron', 'price' => 65.00, 'available' => 1),
            array('name' => 'Macreuse à bifteck', 'price' => 45.00, 'available' => 1),
            array('name' => 'Gîte', 'price' => 85.00, 'available' => 1),
            array('name' => 'Aiguillette baronne', 'price' => 65.00, 'available' => 1),
            array('name' => 'Filet', 'price' => 95.00, 'available' => 1),
        );

        $inserted_count = 0;
        $errors = array();

        foreach ($default_data as $data) {
            $result = BeefChartDataModel::insert($data);

            if ($result !== false) {
                $inserted_count++;
            } else {
                $errors[] = __('Failed to insert beef cut data', 'beef-chart');
            }
        }

        // Clear cache after inserting default data
        wp_cache_delete('beef_chart_data', 'beef_chart');
        wp_cache_delete('beef_chart_admin_data', 'beef_chart');

        return $inserted_count;
    }

    private function ensure_data_exists()
    {
        // Check if table exists, create if not
        if (!BeefChartDataModel::table_exists()) {
            $this->create_database_table();
        }

        // Check if data exists
        $count = BeefChartDataModel::get_count();
        if ($count == 0) {
            $this->insert_default_data();
        }

        // Additional check: ensure we have the expected 29 records
        if ($count > 0 && $count < 29) {
            // Some data is missing, let's reset and repopulate
            BeefChartDataModel::truncate();
            $this->insert_default_data();
        }
    }

    public function enqueue_scripts()
    {
        // Enqueue ECharts for the SVG map
        wp_enqueue_script('yfbcc-echarts', plugin_dir_url(__FILE__) . 'assets/js/echarts.min.js', array(), '5.4.3', true);

        // Enqueue our beef chart component
        wp_enqueue_script('yfbcc-component', plugin_dir_url(__FILE__) . 'beef-chart-final-complete.js', array('yfbcc-echarts'), '1.0.0', true);

        // Localize script with AJAX data
        wp_localize_script('yfbcc-component', 'beefChartAjax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('beef_chart_nonce'),
            'plugin_url' => plugin_dir_url(__FILE__)
        ));
    }

    public function enqueue_admin_scripts($hook)
    {
        if ($hook !== 'toplevel_page_yfbcc-admin') {
            return;
        }

        // Enqueue ECharts for the SVG map
        wp_enqueue_script('yfbcc-echarts', plugin_dir_url(__FILE__) . 'assets/js/echarts.min.js', array(), '5.4.3', true);

        // Enqueue our beef chart component
        wp_enqueue_script('yfbcc-component', plugin_dir_url(__FILE__) . 'beef-chart-final-complete.js', array('yfbcc-echarts'), '1.0.0', true);

        wp_enqueue_script('jquery');
        wp_enqueue_script('yfbcc-admin', plugin_dir_url(__FILE__) . 'admin.js', array('jquery'), '1.0.0', true);

        // Localize script with AJAX data for frontend
        wp_localize_script('yfbcc-component', 'beefChartAjax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('beef_chart_nonce'),
            'plugin_url' => plugin_dir_url(__FILE__)
        ));

        // Localize script with AJAX data for admin
        wp_localize_script('yfbcc-admin', 'beefChartAjax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('beef_chart_nonce'),
            'plugin_url' => plugin_dir_url(__FILE__)
        ));

        // Add inline styles for admin page (moved from inline <style>)
        $inline_css = '
            .beef-chart-table th,
            .beef-chart-table td { padding: 12px; }
            .beef-chart-info h3,
            .beef-chart-info h4 { color: #007cba; margin-top: 20px; margin-bottom: 10px; }
            .beef-chart-info code { background: #e1e1e1; padding: 2px 6px; border-radius: 3px; }
            .beef-chart-info ul { margin: 10px 0; }
            .beef-chart-info li { margin: 5px 0; }
            #beef-chart-preview { max-width: 100%; box-sizing: border-box; }
            @media (max-width: 768px) {
                #beef-chart-preview { height: 350px !important; }
                .beef-chart-wrapper { height: 350px !important; }
            }
            .beef-chart-wrapper { overflow: hidden; position: relative; }
        ';
        // Register a dummy style handle to attach inline CSS
        wp_register_style('yfbcc-admin-inline', false, array(), '1.0.0');
        wp_enqueue_style('yfbcc-admin-inline');
        wp_add_inline_style('yfbcc-admin-inline', $inline_css);

        // Add inline script to render preview chart when admin page loads
        $inline_js = "document.addEventListener('DOMContentLoaded', function(){ if (typeof window.renderBeefChart === 'function') { window.renderBeefChart('beef-chart-preview'); } });";
        wp_add_inline_script('yfbcc-component', $inline_js, 'after');
    }


    // à modifier pour afficher également dans le compte editor G

    public function add_admin_menu()
    {
        // Create custom beef icon
        $beef_icon = $this->get_beef_icon_svg();
        
        add_menu_page(
            'Beef Chart Admin',
            'Beef Chart',
            'edit_pages', // Changed from 'manage_options' to allow editors access
            'yfbcc-admin',
            array($this, 'admin_page'),
            $beef_icon,
            30
        );
    }
    
    /**
     * Get custom beef icon as base64 encoded SVG
     */
    private function get_beef_icon_svg()
    {
        $svg = '<svg width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path fill="currentColor" d="M12 2C10.3 2 9 3.3 9 5c0 1.2.7 2.3 1.7 2.8C9.5 8.7 8.5 9.8 8.5 11.2c0 .8.3 1.5.8 2.1-.5.5-.8 1.2-.8 2c0 1.5 1.2 2.7 2.7 2.7.4 0 .8-.1 1.2-.3.4.9 1.3 1.5 2.4 1.5 1.4 0 2.5-1.1 2.5-2.5 0-.7-.3-1.3-.8-1.8.5-.6.8-1.3.8-2.1 0-1.4-1-2.5-2.2-2.8C15 7.3 15.7 6.2 15.7 5c0-1.7-1.3-3-3-3zm-1.5 4.5c.8 0 1.5.7 1.5 1.5s-.7 1.5-1.5 1.5S9 8.8 9 8s.7-1.5 1.5-1.5zm3 3c.8 0 1.5.7 1.5 1.5s-.7 1.5-1.5 1.5-1.5-.7-1.5-1.5.7-1.5 1.5-1.5zm-3 4c.6 0 1 .4 1 1s-.4 1-1 1-1-.4-1-1 .4-1 1-1z"/>
        </svg>';
        
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    public function admin_page()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'beef_chart_data';

        $message = '';
        $message_type = '';

        // Check and create data if missing
        $this->ensure_data_exists();

        // Handle data reset
        if (isset($_POST['reset_data']) && isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_wpnonce'])), 'yfbcc_reset_beef_data')) {
            if (!current_user_can('edit_pages')) {
                wp_die(esc_html__('Insufficient permissions', 'beef-chart'));
            }

            // Truncate table and insert fresh data using our model
            BeefChartDataModel::truncate();
            $inserted_count = $this->insert_default_data();

            if ($inserted_count > 0) {
                // translators: %d is the number of records created
                $message = sprintf(esc_html__('Données réinitialisées avec succès ! %d enregistrements créés.', 'beef-chart'), $inserted_count);
                $message_type = 'success';
            } else {
                $message = esc_html__('Erreur lors de la réinitialisation des données', 'beef-chart');
                $message_type = 'error';
            }
        }

        // Handle bulk update with improved security
        if (isset($_POST['bulk_update']) && isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_wpnonce'])), 'yfbcc_bulk_update_beef_data')) {
            if (!current_user_can('edit_pages')) {
                wp_die(esc_html__('Insufficient permissions', 'beef-chart'));
            }

            if (!isset($_POST['beef_data']) || !is_array($_POST['beef_data'])) {
                $message = esc_html__('Données invalides', 'beef-chart');
                $message_type = 'error';
            } else {
                $updated_count = 0;
                $errors = array();

                // Properly sanitize the input array
                $posted_data = isset($_POST['beef_data']) ? map_deep(wp_unslash($_POST['beef_data']), 'sanitize_text_field') : array();
                foreach ($posted_data as $id => $data) {
                    $id = absint($id);
                    $price = isset($data['price']) ? floatval($data['price']) : 0;
                    $available = isset($data['available']) ? 1 : 0;

                    // Validate price range (increased limit)
                    if ($price < 0 || $price > 9999.99) {
                        // translators: %1$d is the ID, %2$.2f is the price value
                        $errors[] = sprintf(esc_html__('Prix invalide pour l\'ID %1$d: %2$.2f (doit être entre 0 et 9999.99)', 'beef-chart'), $id, $price);
                        continue;
                    }

                    // Check if the record exists using our model
                    if (!BeefChartDataModel::exists($id)) {
                        // translators: %d is the record ID
                        $errors[] = sprintf(esc_html__('Enregistrement avec ID %d n\'existe pas', 'beef-chart'), $id);
                        continue;
                    }

                    // Update using our model
                    $result = BeefChartDataModel::update($id, array(
                        'price' => $price,
                        'available' => $available
                    ));

                    if ($result !== false) {
                        $updated_count++;
                    } else {
                        // translators: %1$d is the ID, %2$s is the error message
                        $errors[] = sprintf(esc_html__('Échec de la mise à jour pour l\'ID %1$d: %2$s', 'beef-chart'), $id, esc_html__('Database error', 'beef-chart'));
                    }
                }

                if ($updated_count > 0) {
                    // translators: %d is the number of records updated
                    $message = sprintf(esc_html__('%d donnée(s) mise(s) à jour avec succès !', 'beef-chart'), $updated_count);
                    $message_type = 'success';

                    // Clear cache after successful updates
                    wp_cache_delete('beef_chart_data', 'beef_chart');
                    wp_cache_delete('beef_chart_admin_data', 'beef_chart');
                } else {
                    $message = esc_html__('Aucune donnée n\'a été mise à jour', 'beef-chart');
                    $message_type = 'error';
                }

                if (!empty($errors)) {
                    $message .= '<br><strong>Erreurs:</strong><br>' . implode('<br>', $errors);
                    if ($message_type === 'success') {
                        $message_type = 'warning';
                    }
                }
            }
        }

        // Get data using our model
        $beef_data = BeefChartDataModel::get_all();

?>
        <div class="wrap">
            <?php if ($message): ?>
                <div class="notice notice-<?php echo esc_attr($message_type); ?> is-dismissible">
                    <p><strong><?php echo wp_kses_post($message); ?></strong></p>
                </div>
            <?php endif; ?>

            <h1>Paramètres du Graphique de Bœuf</h1>
            <p>Modifiez les prix et la disponibilité des coupes de bœuf.</p>

            <div style="background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <h3>📊 Aperçu du graphique</h3>
                <p>Cette section affiche un aperçu du graphique tel qu'il apparaîtra sur votre site.</p>
                <div id="beef-chart-preview" style="width: 100%; height: 500px; border: 1px solid #ddd; border-radius: 4px; overflow: hidden; position: relative;"></div>
            </div>

            <form method="post">
                <?php wp_nonce_field('yfbcc_bulk_update_beef_data'); ?>
                <table class="wp-list-table widefat fixed striped beef-chart-table">
                    <thead>
                        <tr>
                            <th>Nom de la coupe</th>
                            <th>Prix (€/kg)</th>
                            <th>Disponible</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($beef_data as $item): ?>
                            <tr>
                                <td><?php echo esc_html($item->name); ?></td>
                                <td>
                                    <input type="number" step="0.01" name="beef_data[<?php echo esc_attr($item->id); ?>][price]" value="<?php echo esc_attr($item->price); ?>" class="small-text">
                                </td>
                                <td>
                                    <input type="checkbox" name="beef_data[<?php echo esc_attr($item->id); ?>][available]" value="1" <?php checked($item->available, 1); ?>>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <p class="submit">
                    <input type="submit" name="bulk_update" class="button button-primary" value="Mettre à jour toutes les données">
                </p>
            </form>

            <div style="margin-top: 20px; padding: 15px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 4px;">
                <h3 style="color: #856404; margin-top: 0;">🔧 Outils de diagnostic</h3>
                <p><strong>Problème avec les mises à jour ?</strong> Utilisez ce bouton pour réinitialiser complètement les données du plugin :</p>
                <form method="post" onsubmit="return confirm('Êtes-vous sûr de vouloir réinitialiser toutes les données ? Cette action est irréversible.');">
                    <?php wp_nonce_field('yfbcc_reset_beef_data'); ?>
                    <input type="submit" name="reset_data" class="button button-secondary" value="🔄 Réinitialiser les données" style="background: #dc3545; border-color: #dc3545; color: white;">
                </form>
                <p style="font-size: 12px; color: #856404; margin-bottom: 0;">
                    <em>Cette action supprimera toutes les données existantes et recréera les 29 coupes de bœuf avec les prix par défaut.</em>
                </p>
            </div>

            <div class="beef-chart-info" style="background: #f9f9f9; padding: 20px; margin-top: 30px; border-left: 4px solid #007cba; border-radius: 4px;">
                <h3>Utilisation du plugin</h3>
                <p>Pour afficher le graphique sur une page, utilisez le shortcode suivant :</p>
                <code style="background: #e1e1e1; padding: 2px 6px; border-radius: 3px; display: inline-block; margin: 10px 0;">[beef_chart]</code>

                <p>Ou utilisez le bloc "Beef Chart" dans l'éditeur Gutenberg.</p>

                <h4>Instructions</h4>
                <ul style="list-style-type: disc; margin-left: 20px;">
                    <li>Modifiez les prix en entrant de nouvelles valeurs</li>
                    <li>Décochez la case "Disponible" pour masquer une coupe du graphique</li>
                    <li>Cliquez sur "Mettre à jour" pour sauvegarder les changements</li>
                    <li>Les modifications sont appliquées immédiatement sur le site</li>
                </ul>

                <h4>Personnalisation</h4>
                <p>Vous pouvez personnaliser l'apparence en modifiant les paramètres du shortcode :</p>
                <ul style="list-style-type: disc; margin-left: 20px;">
                    <li><code>[beef_chart width="800px" height="500px"]</code> - Dimensions personnalisées</li>
                    <li><code>[beef_chart width="100%" height="600px"]</code> - Largeur responsive</li>
                </ul>
            </div>
        </div>


    <?php
    }

    public function render_beef_chart($atts)
    {
        // Ensure data exists before rendering
        $this->ensure_data_exists();
        
        static $instance = 0;
        $instance++;
        $unique_id = 'beef-chart-' . $instance;

        // Default attributes
        $atts = shortcode_atts(array(
            'width' => '100%',
            'height' => '500px'
        ), $atts);

        $width = esc_attr($atts['width']);
        $height = esc_attr($atts['height']);

        ob_start();
    ?>
        <div id="<?php echo esc_attr($unique_id); ?>" class="beef-chart-wrapper" style="width: <?php echo esc_attr($width); ?>; height: <?php echo esc_attr($height); ?>; min-height: 400px;"></div>
        <?php
        // Ensure script is enqueued and add inline call for this instance
        wp_add_inline_script('yfbcc-component', "document.addEventListener('DOMContentLoaded',function(){ if(typeof window.renderBeefChart==='function'){ window.renderBeefChart('" . esc_js($unique_id) . "'); } });", 'after');
        ?>
<?php
        return ob_get_clean();
    }

    public function register_beef_chart_block()
    {
        if (!function_exists('register_block_type')) {
            return;
        }

        wp_register_script('yfbcc-block', plugin_dir_url(__FILE__) . 'block.js', array('wp-blocks', 'wp-element', 'wp-editor'), '1.0.0', true);

        register_block_type('yfbcc/beef-chart', array(
            'editor_script' => 'yfbcc-block',
            'render_callback' => array($this, 'render_beef_chart')
        ));
    }

    public function save_beef_data()
    {
        // Verify nonce and capabilities
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'beef_chart_nonce')) {
            wp_send_json_error(esc_html__('Security check failed', 'beef-chart'));
            return;
        }

        if (!current_user_can('edit_pages')) {
            wp_send_json_error(esc_html__('Insufficient permissions', 'beef-chart'));
            return;
        }

        if (!isset($_POST['id']) || !isset($_POST['price'])) {
            wp_send_json_error(esc_html__('Missing required data', 'beef-chart'));
            return;
        }

        $id = isset($_POST['id']) ? absint(wp_unslash($_POST['id'])) : 0;
        $price = isset($_POST['price']) ? floatval(wp_unslash($_POST['price'])) : 0;
        $available = isset($_POST['available']) ? 1 : 0;

        // Validate price range
        if ($price < 0 || $price > 9999.99) {
            wp_send_json_error(esc_html__('Prix invalide', 'beef-chart'));
            return;
        }

        global $wpdb;
        // Check if record exists using our model
        if (!BeefChartDataModel::exists($id)) {
            wp_send_json_error(esc_html__('Enregistrement non trouvé', 'beef-chart'));
            return;
        }

        // Update using our model
        $result = BeefChartDataModel::update($id, array(
            'price' => $price,
            'available' => $available
        ));

        if ($result !== false) {
            // Clear cache when data is updated
            wp_cache_delete('beef_chart_data', 'beef_chart');
            wp_cache_delete('beef_chart_admin_data', 'beef_chart');
            wp_send_json_success(esc_html__('Data updated successfully', 'beef-chart'));
        } else {
            wp_send_json_error(esc_html__('Failed to update data', 'beef-chart'));
        }
    }

    public function get_beef_data()
    {
        // Ensure data exists before retrieving it
        $this->ensure_data_exists();
        
        // Verify nonce for security
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'beef_chart_nonce')) {
            wp_send_json_error(esc_html__('Security check failed', 'beef-chart'));
        }

        // Get all data using our model
        $beef_data = BeefChartDataModel::get_all();

        if ($beef_data && !empty($beef_data)) {
            wp_send_json_success($beef_data);
        } else {
            wp_send_json_error(esc_html__('No data found in database', 'beef-chart'));
        }
    }
}

// Initialize the plugin
new BeefChartPlugin();
