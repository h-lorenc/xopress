<?php
if (!defined('ABSPATH')) {
    exit;
}

class WC_GPSR_Admin_Menu {
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'handle_category_assignments'));
    }

    public function add_admin_menu() {
        add_menu_page(
            'GPSR',
            'GPSR',
            'manage_options',
            'wc-gpsr',
            array($this, 'render_main_page'),
            'dashicons-shield',
            56
        );
        
        add_submenu_page(
            'wc-gpsr',
            'Przypisz po kategoriach',
            'Przypisz po kategoriach',
            'manage_options',
            'wc-gpsr-categories',
            array($this, 'render_categories_page')
        );
        
        add_submenu_page(
            'wc-gpsr',
            'Przypisz po SKU',
            'Przypisz po SKU',
            'manage_options',
            'wc-gpsr-sku',
            array($this, 'render_sku_page')
        );
        
        add_submenu_page(
            'wc-gpsr',
            'Przypisz po atrybutach',
            'Przypisz po atrybutach',
            'manage_options',
            'wc-gpsr-attributes',
            array($this, 'render_attributes_page')
        );

        add_submenu_page(
            'wc-gpsr',
            'Pliki bezpieczeństwa',
            'Pliki bezpieczeństwa',
            'manage_options',
            'wc-gpsr-files',
            array($this, 'render_files_page')
        );
    }

    public function render_main_page() {
        include WC_GPSR_PLUGIN_DIR . 'views/admin/main-page.php';
    }

    public function render_categories_page() {
        include WC_GPSR_PLUGIN_DIR . 'views/admin/categories-page.php';
    }

    public function render_sku_page() {
        include WC_GPSR_PLUGIN_DIR . 'views/admin/sku-page.php';
    }

    public function render_attributes_page() {
        include WC_GPSR_PLUGIN_DIR . 'views/admin/attributes-page.php';
    }

    public function render_files_page() {
        include WC_GPSR_PLUGIN_DIR . 'views/admin/files-page.php';
    }

    public function handle_category_assignments() {
        if (isset($_POST['save_category_assignments']) && check_admin_referer('gpsr_category_assignments')) {
            $manufacturer_assignments = isset($_POST['category_manufacturer']) ? array_map('sanitize_text_field', $_POST['category_manufacturer']) : array();
            $distributor_assignments = isset($_POST['category_distributor']) ? array_map('sanitize_text_field', $_POST['category_distributor']) : array();
            
            foreach ($manufacturer_assignments as $term_id => $manufacturer_id) {
                update_term_meta($term_id, '_category_manufacturer', $manufacturer_id);
                if ($manufacturer_id) {
                    $this->update_category_products($term_id, $manufacturer_id, 'manufacturer');
                }
            }
            
            foreach ($distributor_assignments as $term_id => $distributor_id) {
                update_term_meta($term_id, '_category_distributor', $distributor_id);
                if ($distributor_id) {
                    $this->update_category_products($term_id, $distributor_id, 'distributor');
                }
            }
        }
    }

    private function update_category_products($term_id, $assigned_id, $type) {
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => -1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms' => $term_id
                )
            )
        );
        
        $products = get_posts($args);
        
        foreach ($products as $product) {
            // Sprawdź czy produkt nie ma już ręcznego przypisania
            $manual_assignment = get_post_meta($product->ID, '_product_' . $type, true);
            if (!$manual_assignment) {
                // Używamy tego samego klucza meta co dla ręcznych przypisań
                update_post_meta($product->ID, '_product_' . $type, $assigned_id);
            }
        }
    }
}