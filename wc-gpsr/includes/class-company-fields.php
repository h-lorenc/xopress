<?php
if (!defined('ABSPATH')) {
    exit;
}

class WC_GPSR_Company_Fields {
    private $post_types = array('manufacturer', 'distributor');
    
    public function __construct() {
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_meta_box_data'));
    }
    
    public function add_meta_boxes() {
        foreach ($this->post_types as $post_type) {
            add_meta_box(
                'company_details',
                'Dane firmy',
                array($this, 'render_meta_box'),
                $post_type,
                'normal',
                'high'
            );
        }
    }
    
    public function render_meta_box($post) {
        wp_nonce_field('company_details_nonce', 'company_details_nonce');
        
        $fields = array(
            'company_name' => 'Pełna nazwa firmy',
            'street' => 'Ulica i numer budynku',
            'postal_code' => 'Kod pocztowy',
            'city' => 'Miasto',
            'country' => 'Kraj',
            'email' => 'Adres e-mail',
            'phone' => 'Numer telefonu (opcjonalnie)'
        );
        
        foreach ($fields as $field_id => $label) {
            $value = get_post_meta($post->ID, '_' . $field_id, true);
            ?>
            <p>
                <label for="<?php echo esc_attr($field_id); ?>"><strong><?php echo esc_html($label); ?>:</strong></label><br>
                <input type="text" 
                       id="<?php echo esc_attr($field_id); ?>" 
                       name="<?php echo esc_attr($field_id); ?>" 
                       value="<?php echo esc_attr($value); ?>" 
                       style="width: 100%;"
                       <?php echo ($field_id === 'email') ? 'type="email"' : 'type="text"'; ?>>
            </p>
            <?php
        }
    }
    
    public function save_meta_box_data($post_id) {
        if (!isset($_POST['company_details_nonce']) || 
            !wp_verify_nonce($_POST['company_details_nonce'], 'company_details_nonce')) {
            return;
        }
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        if (!in_array(get_post_type($post_id), $this->post_types)) {
            return;
        }
        
        $fields = array(
            'company_name',
            'street',
            'postal_code',
            'city',
            'country',
            'email',
            'phone'
        );
        
        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                $value = sanitize_text_field($_POST[$field]);
                update_post_meta($post_id, '_' . $field, $value);
            }
        }
    }
}