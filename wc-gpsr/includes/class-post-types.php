<?php
if (!defined('ABSPATH')) {
    exit;
}

class WC_GPSR_Post_Types {
    public function __construct() {
        add_action('init', array($this, 'register_post_types'));
    }

    public function register_post_types() {
        register_post_type('manufacturer', array(
            'labels' => array(
                'name' => 'Producenci',
                'singular_name' => 'Producent',
            ),
            'public' => false,
            'show_ui' => true,
            'supports' => array('title'),
            'menu_icon' => 'dashicons-building',
            'show_in_menu' => 'wc-gpsr'
        ));

        register_post_type('distributor', array(
            'labels' => array(
                'name' => 'Dystrybutorzy',
                'singular_name' => 'Dystrybutor',
            ),
            'public' => false,
            'show_ui' => true,
            'supports' => array('title'),
            'menu_icon' => 'dashicons-businessman',
            'show_in_menu' => 'wc-gpsr'
        ));
    }
}