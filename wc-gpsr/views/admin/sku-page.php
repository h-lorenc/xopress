<?php
if (!defined('ABSPATH')) {
    exit;
}

$manufacturers = get_posts(array('post_type' => 'manufacturer', 'posts_per_page' => -1));
$distributors = get_posts(array('post_type' => 'distributor', 'posts_per_page' => -1));

if (isset($_POST['assign_by_sku'])) {
    if (check_admin_referer('gpsr_sku_assignment')) {
        $sku_list = sanitize_textarea_field($_POST['sku_list']);
        $manufacturer_id = isset($_POST['manufacturer']) ? sanitize_text_field($_POST['manufacturer']) : '';
        $distributor_id = isset($_POST['distributor']) ? sanitize_text_field($_POST['distributor']) : '';
        
        $skus = array_map('trim', explode(';', $sku_list));
        $updated = 0;
        
        foreach ($skus as $sku) {
            if (empty($sku)) continue;
            
            $product_id = wc_get_product_id_by_sku($sku);
            if ($product_id) {
                if ($manufacturer_id) {
                    update_post_meta($product_id, '_product_manufacturer', $manufacturer_id);
                }
                if ($distributor_id) {
                    update_post_meta($product_id, '_product_distributor', $distributor_id);
                }
                $updated++;
            }
        }
        
        echo '<div class="updated"><p>Zaktualizowano ' . $updated . ' produktów.</p></div>';
    }
}
?>

<div class="wrap">
    <h1>Przypisz po SKU</h1>
    <form method="post" action="">
        <?php wp_nonce_field('gpsr_sku_assignment'); ?>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="sku_list">Numery SKU (oddzielone średnikiem):</label>
                </th>
                <td>
                    <textarea name="sku_list" id="sku_list" rows="5" cols="50"></textarea>
                    <p class="description">Wprowadź numery SKU oddzielone średnikiem (;)</p>
                </td>
            </tr>
            <tr>
                <th scope="row">Producent:</th>
                <td>
                    <select name="manufacturer">
                        <option value="">-- Wybierz producenta --</option>
                        <?php foreach ($manufacturers as $man): ?>
                            <option value="<?php echo esc_attr($man->ID); ?>">
                                <?php echo esc_html($man->post_title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row">Dystrybutor:</th>
                <td>
                    <select name="distributor">
                        <option value="">-- Wybierz dystrybutora --</option>
                        <?php foreach ($distributors as $dist): ?>
                            <option value="<?php echo esc_attr($dist->ID); ?>">
                                <?php echo esc_html($dist->post_title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </table>
        <p class="submit">
            <input type="submit" name="assign_by_sku" class="button-primary" value="Przypisz">
        </p>
    </form>
</div>