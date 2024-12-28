<?php
if (!defined('ABSPATH')) {
    exit;
}

if (isset($_POST['assign_files_by_sku'])) {
    if (check_admin_referer('gpsr_files_assignment')) {
        $sku_list = sanitize_textarea_field($_POST['sku_list']);
        $safety_files = isset($_POST['safety_files']) ? array_map('esc_url_raw', $_POST['safety_files']) : array();
        $safety_links = isset($_POST['safety_links']) ? array_map('esc_url_raw', $_POST['safety_links']) : array();
        
        $skus = array_map('trim', explode(';', $sku_list));
        $updated = 0;
        
        foreach ($skus as $sku) {
            if (empty($sku)) continue;
            
            $product_id = wc_get_product_id_by_sku($sku);
            if ($product_id) {
                if (!empty($safety_files)) {
                    update_post_meta($product_id, '_safety_files', $safety_files);
                }
                if (!empty($safety_links)) {
                    update_post_meta($product_id, '_safety_links', $safety_links);
                }
                $updated++;
            }
        }
        
        echo '<div class="updated"><p>Zaktualizowano pliki i linki bezpieczeństwa dla ' . $updated . ' produktów.</p></div>';
    }
}
?>

<div class="wrap">
    <h1>Przypisz pliki bezpieczeństwa po SKU</h1>
    <form method="post" action="">
        <?php wp_nonce_field('gpsr_files_assignment'); ?>
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
                <th scope="row">
                    <label>Pliki bezpieczeństwa:</label>
                </th>
                <td>
                    <div id="safety_files_container">
                        <input type="button" class="button" value="Dodaj pliki" onclick="addSafetyFiles()" />
                        <div id="files_list"></div>
                    </div>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label>Linki do instrukcji:</label>
                </th>
                <td>
                    <div id="safety_links_container">
                        <button type="button" class="button" onclick="addSafetyLink()">Dodaj link</button>
                        <div id="links_list"></div>
                    </div>
                </td>
            </tr>
        </table>
        <p class="submit">
            <input type="submit" name="assign_files_by_sku" class="button-primary" value="Przypisz">
        </p>
    </form>
</div>

<script>
function addSafetyFiles() {
    var frame = wp.media({
        title: 'Wybierz pliki bezpieczeństwa',
        multiple: true
    });

    frame.on('select', function() {
        var attachments = frame.state().get('selection').toJSON();
        var container = document.getElementById('files_list');
        
        attachments.forEach(function(attachment) {
            var div = document.createElement('div');
            div.innerHTML = '<input type="text" name="safety_files[]" value="' . attachment.url + '" readonly />' +
                          '<button type="button" onclick="this.parentElement.remove()">Usuń</button>';
            container.appendChild(div);
        });
    });

    frame.open();
}

function addSafetyLink() {
    var container = document.getElementById('links_list');
    var div = document.createElement('div');
    div.className = 'safety-link-row';
    div.innerHTML = '<input type="text" name="safety_links[]" placeholder="https://" />' +
                   '<button type="button" onclick="this.parentElement.remove()">Usuń</button>';
    container.appendChild(div);
}
</script>

<style>
.safety-link-row {
    margin-bottom: 10px;
}
.safety-link-row input[type="text"] {
    width: calc(100% - 70px);
    margin-right: 10px;
}
</style>