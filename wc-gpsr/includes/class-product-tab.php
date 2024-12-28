<?php
if (!defined('ABSPATH')) {
    exit;
}

class WC_GPSR_Product_Tab {
    public function __construct() {
        add_filter('woocommerce_product_tabs', array($this, 'add_gpsr_tab'));
    }

    public function add_gpsr_tab($tabs) {
        global $post;
        
        $manufacturer_id = $this->get_product_manufacturer($post->ID);
        $distributor_id = $this->get_product_distributor($post->ID);
        $safety_files = get_post_meta($post->ID, '_safety_files', true);
        $safety_links = get_post_meta($post->ID, '_safety_links', true);
        
        if ($manufacturer_id || $distributor_id || !empty($safety_files) || !empty($safety_links)) {
            $tabs['gpsr'] = array(
                'title' => 'Bezpieczeństwo',
                'priority' => 50,
                'callback' => array($this, 'render_tab_content')
            );
        }
        
        return $tabs;
    }

    public function get_product_manufacturer($product_id) {
        // Sprawdź najpierw ręczne przypisanie
        $manufacturer_id = get_post_meta($product_id, '_product_manufacturer', true);
        
        // Jeśli nie ma ręcznego przypisania, sprawdź kategorie
        if (!$manufacturer_id) {
            $terms = get_the_terms($product_id, 'product_cat');
            if ($terms && !is_wp_error($terms)) {
                foreach ($terms as $term) {
                    $category_manufacturer_id = get_term_meta($term->term_id, '_category_manufacturer', true);
                    if ($category_manufacturer_id) {
                        // Zapisz przypisanie z kategorii do produktu
                        update_post_meta($product_id, '_product_manufacturer', $category_manufacturer_id);
                        $manufacturer_id = $category_manufacturer_id;
                        break;
                    }
                }
            }
        }
        
        return $manufacturer_id;
    }

    public function get_product_distributor($product_id) {
        // Sprawdź najpierw ręczne przypisanie
        $distributor_id = get_post_meta($product_id, '_product_distributor', true);
        
        // Jeśli nie ma ręcznego przypisania, sprawdź kategorie
        if (!$distributor_id) {
            $terms = get_the_terms($product_id, 'product_cat');
            if ($terms && !is_wp_error($terms)) {
                foreach ($terms as $term) {
                    $category_distributor_id = get_term_meta($term->term_id, '_category_distributor', true);
                    if ($category_distributor_id) {
                        // Zapisz przypisanie z kategorii do produktu
                        update_post_meta($product_id, '_product_distributor', $category_distributor_id);
                        $distributor_id = $category_distributor_id;
                        break;
                    }
                }
            }
        }
        
        return $distributor_id;
    }

    public function render_tab_content() {
        include WC_GPSR_PLUGIN_DIR . 'views/frontend/tab-content.php';
    }
}