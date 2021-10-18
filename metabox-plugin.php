<?php
/**
 * Plugin Name: Metabox Plugin
 * Plugin URI: https://www.example.com
 * Description: To create a metabox with a input field and a checkbox
 * Version: 1.0
 * Author: Jamsheed
 * Author URI: https://www.example.com
 */
if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }
if( ! class_exists( 'MetaClass' ) ) {    
    class MetaClass {

        public function __construct()
    	{
    		add_action('add_meta_boxes', array($this, 'add_custom_meta_box'));
    		add_action('save_post', array($this, 'save_custom_meta_box'),10,3);
    		add_action( 'the_content', array($this,'meta_message' ));
    	}
        // To create html fields for custom metaboxe	
        public function custom_meta_box_markup($object)
        {
            wp_nonce_field(basename(__FILE__), "meta-box-nonce");

            ?>
            <div>
                <label for="meta-box-text">Text</label>
                <input name="meta-box-text" placeholder="test to show/hide" type="text" value="<?php echo get_post_meta($object->ID, 'meta-box-text', true);
                ?>">
                <br>
                <label for="meta-box-checkbox">SHOW</label>
                <?php
                $checkbox_value = get_post_meta($object->ID, 'meta-box-checkbox', true);
                if($checkbox_value == "true")
                {
                    ?>
                        <input name="meta-box-checkbox" type="checkbox" value="true" checked>
                    <?php
                }
                else 
                {
                    ?>  
                        <input name="meta-box-checkbox" type="checkbox" value="true">
                    <?php
                }
                ?>
            </div>
            <?php  
        }
        /**
        * Sanitize checkbox
        * @param int | $post_id the Post ID
        * @param WP_Post | $post Post object
        * @param bool | $update Whether this is an existing post being updated.
        */
        public function save_custom_meta_box($post_id, $post, $update)
        {
            if (!isset($_POST["meta-box-nonce"]) || !wp_verify_nonce($_POST["meta-box-nonce"], basename(__FILE__)))
                return $post_id;

            if(!current_user_can("edit_post", $post_id))
                return $post_id;

            if(defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
                return $post_id;

            $slug = "post";
            if($slug != $post->post_type)
                return $post_id;

            $meta_box_text_value = "";
            $meta_box_checkbox_value = "";
            $meta_box_text_value = isset($_POST["meta-box-text"]) ? sanitize_text_field($_POST["meta-box-text"]) : ‘’;
            update_post_meta($post_id, "meta-box-text", $meta_box_text_value);
            $meta_box_checkbox_value = isset($_POST["meta-box-checkbox"]) ? sanitize_text_field($_POST["meta-box-checkbox"]) : ‘’;
            update_post_meta($post_id, "meta-box-checkbox", $meta_box_checkbox_value);
        }
        // To display metabox input value on frontend by appending on content.
        public function meta_message( $content ) 
        {
        	global $post;
        	$data = get_post_meta($post -> ID, 'meta-box-text', true);
        	$check = get_post_meta($post -> ID, 'meta-box-checkbox', true); 
        	if (!empty($data) && $check == "true" ) {
    			$custom_message = "<div style='font-weight:bold;text-align:center'>";
    			$custom_message .= $data;
    			$custom_message .= "</div>";
    			$content = $custom_message . $content;
        	}
    		return $content;
        }
        // Function to create a metabox
        public function add_custom_meta_box()
        {
            add_meta_box("demo-meta-box", "Custom Meta Box", array($this, "custom_meta_box_markup"), "post", "side", "high", null);
        }

    }
    $metaclassobject = new MetaClass();
}
else {
    exit();
}
?>
