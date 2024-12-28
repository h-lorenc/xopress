<?php
if (!defined('ABSPATH')) {
    exit;
}

$product_cats = get_terms('product_cat', array('hide_empty' => false));
$manufacturers = get_posts(array('post_type' => 'manufacturer', 'posts_per_page' => -1));
$distributors = get_posts(array('post_type' => 'distributor', 'posts_per_page' => -1));

if (isset($_POST['save_category_assignments'])) {
    if (check_admin_referer('gpsr_category_assignments')) {
        $manufacturer_assignments = isset($_POST['category_manufacturer']) ? array_map('sanitize_text_field', $_POST['category_manufacturer']) : array();
        $distributor_assignments = isset($_POST['category_distributor']) ? array_map('sanitize_text_field', $_POST['category_distributor']) : array();
        
        foreach ($manufacturer_assignments as $term_id => $manufacturer_id) {
            update_term_meta($term_id, '_category_manufacturer', $manufacturer_id);
        }
        
        foreach ($distributor_assignments as $term_id => $distributor_id) {
            update_term_meta($term_id, '_category_distributor', $distributor_id);
        }
        
        echo '<div class="updated"><p>Przypisania zostały zaktualizowane.</p></div>';
    }
}
?>

<div class="wrap">
    <h1>Przypisz po kategoriach</h1>
    <form method="post" action="">
        <?php wp_nonce_field('gpsr_category_assignments'); ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Kategoria</th>
                    <th>Producent</th>
                    <th>Dystrybutor</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($product_cats as $cat): ?>
                    <tr>
                        <td><?php echo esc_html($cat->name); ?></td>
                        <td>
                            <select name="category_manufacturer[<?php echo esc_attr($cat->term_id); ?>]">
                                <option value="">-- Wybierz producenta --</option>
                                <?php foreach ($manufacturers as $man): ?>
                                    <option value="<?php echo esc_attr($man->ID); ?>" 
                                        <?php selected(get_term_meta($cat->term_id, '_category_manufacturer', true), $man->ID); ?>>
                                        <?php echo esc_html($man->post_title); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td>
                            <select name="category_distributor[<?php echo esc_attr($cat->term_id); ?>]">
                                <option value="">-- Wybierz dystrybutora --</option>
                                <?php foreach ($distributors as $dist): ?>
                                    <option value="<?php echo esc_attr($dist->ID); ?>"
                                        <?php selected(get_term_meta($cat->term_id, '_category_distributor', true), $dist->ID); ?>>
                                        <?php echo esc_html($dist->post_title); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p class="submit">
            <input type="submit" name="save_category_assignments" class="button-primary" value="Zapisz przypisania">
        </p>
    </form>
</div>