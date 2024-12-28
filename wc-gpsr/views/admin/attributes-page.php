<?php
if (!defined('ABSPATH')) {
    exit;
}

$attributes = wc_get_attribute_taxonomies();
$manufacturers = get_posts(array('post_type' => 'manufacturer', 'posts_per_page' => -1));
$distributors = get_posts(array('post_type' => 'distributor', 'posts_per_page' => -1));

if (isset($_POST['assign_by_attributes'])) {
    if (check_admin_referer('gpsr_attributes_assignment')) {
        $attribute_id = isset($_POST['attribute']) ? absint($_POST['attribute']) : 0;
        $term_id = isset($_POST['term']) ? absint($_POST['term']) : 0;
        $manufacturer_id = isset($_POST['manufacturer']) ? sanitize_text_field($_POST['manufacturer']) : '';
        $distributor_id = isset($_POST['distributor']) ? sanitize_text_field($_POST['distributor']) : '';
        
        if ($attribute_id && $term_id) {
            $args = array(
                'post_type' => 'product',
                'posts_per_page' => -1,
                'tax_query' => array(
                    array(
                        'taxonomy' => wc_attribute_taxonomy_name_by_id($attribute_id),
                        'field' => 'term_id',
                        'terms' => $term_id
                    )
                )
            );
            
            $products = get_posts($args);
            $updated = 0;
            
            foreach ($products as $product) {
                if ($manufacturer_id) {
                    update_post_meta($product->ID, '_product_manufacturer', $manufacturer_id);
                }
                if ($distributor_id) {
                    update_post_meta($product->ID, '_product_distributor', $distributor_id);
                }
                $updated++;
            }
            
            echo '<div class="updated"><p>Zaktualizowano ' . $updated . ' produktów.</p></div>';
        }
    }
}
?>

<div class="wrap">
    <h1>Przypisz po atrybutach</h1>
    <form method="post" action="">
        <?php wp_nonce_field('gpsr_attributes_assignment'); ?>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="attribute">Wybierz atrybut:</label>
                </th>
                <td>
                    <select name="attribute" id="attribute">
                        <option value="">-- Wybierz atrybut --</option>
                        <?php foreach ($attributes as $attribute): ?>
                            <option value="<?php echo esc_attr($attribute->attribute_id); ?>">
                                <?php echo esc_html($attribute->attribute_label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="term">Wartość atrybutu:</label>
                </th>
                <td>
                    <select name="term" id="term">
                        <option value="">-- Najpierw wybierz atrybut --</option>
                    </select>
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
            <input type="submit" name="assign_by_attributes" class="button-primary" value="Przypisz">
        </p>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    $('#attribute').on('change', function() {
        var attribute_id = $(this).val();
        if (attribute_id) {
            $.ajax({
                url: ajaxurl,
                data: {
                    action: 'get_attribute_terms',
                    attribute_id: attribute_id,
                    nonce: '<?php echo wp_create_nonce('get_attribute_terms'); ?>'
                },
                success: function(response) {
                    $('#term').html(response);
                }
            });
        } else {
            $('#term').html('<option value="">-- Najpierw wybierz atrybut --</option>');
        }
    });
});</script>