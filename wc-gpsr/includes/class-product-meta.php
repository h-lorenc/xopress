<?php
if (!defined('ABSPATH')) {
    exit;
}

class WC_GPSR_Product_Meta {
    public function __construct() {
        add_action('add_meta_boxes', array($this, 'add_product_meta_boxes'));
        add_action('save_post_product', array($this, 'save_product_meta'));
    }

    public function add_product_meta_boxes() {
        add_meta_box(
            'gpsr_details',
            'GPSR - Producent/Dystrybutor',
            array($this, 'render_meta_box'),
            'product',
            'normal',
            'high'
        );
    }

    public function render_meta_box($post) {
        include WC_GPSR_PLUGIN_DIR . 'views/admin/meta-box.php';
    }

    public function save_product_meta($post_id) {
        if (!isset($_POST['gpsr_nonce']) || !wp_verify_nonce($_POST['gpsr_nonce'], 'save_gpsr_data')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        $fields = array(
            'product_manufacturer',
            'product_distributor'
        );

        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
            }
        }

        // Zapisz pliki bezpieczeństwa
        if (isset($_POST['safety_files'])) {
            $safety_files = array_map('esc_url_raw', $_POST['safety_files']);
            update_post_meta($post_id, '_safety_files', $safety_files);
        }

        // Zapisz linki do instrukcji
        if (isset($_POST['safety_links'])) {
            $safety_links = array_map('esc_url_raw', $_POST['safety_links']);
            update_post_meta($post_id, '_safety_links', $safety_links);
        }
    }
}