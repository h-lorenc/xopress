<?php
if (!defined('ABSPATH')) {
    exit;
}

global $post;

// Pobierz przypisania używając metod z klasy Product_Tab
$tab = new WC_GPSR_Product_Tab();
$manufacturer_id = $tab->get_product_manufacturer($post->ID);
$distributor_id = $tab->get_product_distributor($post->ID);
$safety_files = get_post_meta($post->ID, '_safety_files', true);
$safety_links = get_post_meta($post->ID, '_safety_links', true);

if ($manufacturer_id) {
    echo '<h3>Producent:</h3>';
    echo '<p>';
    echo esc_html(get_post_meta($manufacturer_id, '_company_name', true)) . '<br>';
    echo esc_html(get_post_meta($manufacturer_id, '_street', true)) . '<br>';
    echo esc_html(get_post_meta($manufacturer_id, '_postal_code', true)) . ' ';
    echo esc_html(get_post_meta($manufacturer_id, '_city', true)) . '<br>';
    echo esc_html(get_post_meta($manufacturer_id, '_country', true)) . '<br>';
    echo 'Email: ' . esc_html(get_post_meta($manufacturer_id, '_email', true));
    
    $phone = get_post_meta($manufacturer_id, '_phone', true);
    if ($phone) {
        echo '<br>Tel: ' . esc_html($phone);
    }
    
    echo '</p>';
}

if ($distributor_id) {
    echo '<h3>Dystrybutor:</h3>';
    echo '<p>';
    echo esc_html(get_post_meta($distributor_id, '_company_name', true)) . '<br>';
    echo esc_html(get_post_meta($distributor_id, '_street', true)) . '<br>';
    echo esc_html(get_post_meta($distributor_id, '_postal_code', true)) . ' ';
    echo esc_html(get_post_meta($distributor_id, '_city', true)) . '<br>';
    echo esc_html(get_post_meta($distributor_id, '_country', true)) . '<br>';
    echo 'Email: ' . esc_html(get_post_meta($distributor_id, '_email', true));
    
    $phone = get_post_meta($distributor_id, '_phone', true);
    if ($phone) {
        echo '<br>Tel: ' . esc_html($phone);
    }
    
    echo '</p>';
}

// Wyświetl pliki i linki do instrukcji
$has_files = !empty($safety_files) && is_array($safety_files);
$has_links = !empty($safety_links) && is_array($safety_links);

if ($has_files || $has_links) {
    echo '<h3>Dokumenty bezpieczeństwa:</h3>';
    echo '<ul class="safety-files-list">';
    
    if ($has_files) {
        foreach ($safety_files as $file) {
            $filename = basename($file);
            echo '<li><a href="' . esc_url($file) . '" target="_blank">' . esc_html($filename) . '</a></li>';
        }
    }
    
    if ($has_links) {
        foreach ($safety_links as $link) {
            $link_text = parse_url($link, PHP_URL_HOST) . parse_url($link, PHP_URL_PATH);
            echo '<li><a href="' . esc_url($link) . '" target="_blank">' . esc_html($link_text) . '</a></li>';
        }
    }
    
    echo '</ul>';
}
?>