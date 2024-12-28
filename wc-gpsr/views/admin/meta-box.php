<?php
if (!defined('ABSPATH')) {
    exit;
}

wp_nonce_field('save_gpsr_data', 'gpsr_nonce');

$manufacturer_id = get_post_meta($post->ID, '_product_manufacturer', true);
$distributor_id = get_post_meta($post->ID, '_product_distributor', true);
$safety_files = get_post_meta($post->ID, '_safety_files', true);
$safety_links = get_post_meta($post->ID, '_safety_links', true);

$manufacturers = get_posts(array('post_type' => 'manufacturer', 'posts_per_page' => -1));
$distributors = get_posts(array('post_type' => 'distributor', 'posts_per_page' => -1));
?>

<div class="gpsr-meta-box">
    <p>
        <label><strong>Producent:</strong></label><br>
        <select name="product_manufacturer" style="width: 100%;">
            <option value="">-- Wybierz producenta --</option>
            <?php foreach ($manufacturers as $man): ?>
                <option value="<?php echo esc_attr($man->ID); ?>" <?php selected($manufacturer_id, $man->ID); ?>>
                    <?php echo esc_html($man->post_title); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>

    <p>
        <label><strong>Dystrybutor:</strong></label><br>
        <select name="product_distributor" style="width: 100%;">
            <option value="">-- Wybierz dystrybutora --</option>
            <?php foreach ($distributors as $dist): ?>
                <option value="<?php echo esc_attr($dist->ID); ?>" <?php selected($distributor_id, $dist->ID); ?>>
                    <?php echo esc_html($dist->post_title); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>

    <div class="safety-files">
        <p>
            <label><strong>Pliki bezpieczeństwa:</strong></label><br>
            <input type="button" class="button" value="Dodaj pliki" onclick="addSafetyFiles()" />
        </p>
        <div id="safety_files_container">
            <?php
            if (is_array($safety_files)) {
                foreach ($safety_files as $file) {
                    echo '<div>';
                    echo '<input type="text" name="safety_files[]" value="' . esc_attr($file) . '" readonly />';
                    echo '<button type="button" onclick="this.parentElement.remove()">Usuń</button>';
                    echo '</div>';
                }
            }
            ?>
        </div>
    </div>

    <div class="safety-links">
        <p>
            <label><strong>Linki do instrukcji bezpieczeństwa:</strong></label><br>
            <button type="button" class="button" onclick="addSafetyLink()">Dodaj link</button>
        </p>
        <div id="safety_links_container">
            <?php
            if (is_array($safety_links)) {
                foreach ($safety_links as $link) {
                    echo '<div class="safety-link-row">';
                    echo '<input type="text" name="safety_links[]" value="' . esc_attr($link) . '" placeholder="https://" />';
                    echo '<button type="button" onclick="this.parentElement.remove()">Usuń</button>';
                    echo '</div>';
                }
            }
            ?>
        </div>
    </div>
</div>

<script>
function addSafetyFiles() {
    var frame = wp.media({
        title: 'Wybierz pliki bezpieczeństwa',
        multiple: true
    });

    frame.on('select', function() {
        var attachments = frame.state().get('selection').toJSON();
        var container = document.getElementById('safety_files_container');
        
        attachments.forEach(function(attachment) {
            var div = document.createElement('div');
            div.innerHTML = '<input type="text" name="safety_files[]" value="' + attachment.url + '" readonly />' +
                          '<button type="button" onclick="this.parentElement.remove()">Usuń</button>';
            container.appendChild(div);
        });
    });

    frame.open();
}

function addSafetyLink() {
    var container = document.getElementById('safety_links_container');
    var div = document.createElement('div');
    div.className = 'safety-link-row';
    div.innerHTML = '<input type="text" name="safety_links[]" placeholder="https://" />' +
                   '<button type="button" onclick="this.parentElement.remove()">Usuń</button>';
    container.appendChild(div);
}</script>

<style>
.safety-link-row {
    margin-bottom: 10px;
}
.safety-link-row input[type="text"] {
    width: calc(100% - 70px);
    margin-right: 10px;
}
</style>