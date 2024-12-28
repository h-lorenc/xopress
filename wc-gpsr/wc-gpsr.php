<?php
/**
 * Plugin Name: WooCommerce GPSR
 * Description: Zarządzanie producentami, dystrybutorami oraz dokumentacją bezpieczeństwa
 * Version: 1.0.0
 * Author: Your Name
 */

if (!defined('ABSPATH')) {
    exit;
}

define('WC_GPSR_PLUGIN_DIR', plugin_dir_path(__FILE__));

class WC_GPSR {
    public function __construct() {
        $this->includes();
        $this->init();
    }

    private function includes() {
        require_once WC_GPSR_PLUGIN_DIR . 'includes/class-admin-menu.php';
        require_once WC_GPSR_PLUGIN_DIR . 'includes/class-post-types.php';
        require_once WC_GPSR_PLUGIN_DIR . 'includes/class-product-meta.php';
        require_once WC_GPSR_PLUGIN_DIR . 'includes/class-product-tab.php';
        require_once WC_GPSR_PLUGIN_DIR . 'includes/class-company-fields.php';
    }

    private function init() {
        new WC_GPSR_Admin_Menu();
        new WC_GPSR_Post_Types();
        new WC_GPSR_Product_Meta();
        new WC_GPSR_Product_Tab();
        new WC_GPSR_Company_Fields();

        add_action('wp_ajax_get_attribute_terms', array($this, 'get_attribute_terms'));
    }

    public function get_attribute_terms() {
        check_ajax_referer('get_attribute_terms', 'nonce');
        
        $attribute_id = isset($_GET['attribute_id']) ? absint($_GET['attribute_id']) : 0;
        if (!$attribute_id) {
            wp_die();
        }
        
        $taxonomy = wc_attribute_taxonomy_name_by_id($attribute_id);
        $terms = get_terms(array(
            'taxonomy' => $taxonomy,
            'hide_empty' => false
        ));
        
        $output = '<option value="">-- Wybierz wartość --</option>';
        foreach ($terms as $term) {
            $output .= sprintf(
                '<option value="%s">%s</option>',
                esc_attr($term->term_id),
                esc_html($term->name)
            );
        }
        
        echo $output;
        wp_die();
    }
}

// Inicjalizacja pluginu
add_action('plugins_loaded', function() {
    if (class_exists('WooCommerce')) {
        new WC_GPSR();
    }
});