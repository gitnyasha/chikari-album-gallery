<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://marshallchikari.co.zw
 * @since      1.0.0
 *
 * @package    Chikari_Album_Gallery
 * @subpackage Chikari_Album_Gallery/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Chikari_Album_Gallery
 * @subpackage Chikari_Album_Gallery/admin
 * @author     Marshall Chikari <hello@marshallchikari.co.zw>
 */
class Chikari_Album_Gallery_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

        add_action('add_meta_boxes', array($this, 'chikari_gallery_meta_box'));
        add_action('init', array($this, 'chikari_gallery_register_post_type'));
        add_action('publish_gallery_album', array($this, 'chikari_gallery_save_attachments'));
        add_action('edit_gallery_album', array($this, 'chikari_gallery_save_attachments'));

    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Chikari_Album_Gallery_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Chikari_Album_Gallery_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/chikari-album-gallery-admin.css', array(), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Chikari_Album_Gallery_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Chikari_Album_Gallery_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/chikari-album-gallery-admin.js', array('jquery'), $this->version, false);

    }

    public function chikari_gallery_register_post_type()
    {
        $labels = array(
            'name' => 'Gallery Albums',
            'singular_name' => 'Gallery Album',
            'menu_name' => 'Gallery Albums',
            'name_admin_bar' => 'Gallery Album',
            'add_new' => 'Add New',
            'add_new_item' => 'Add New Gallery Album',
            'new_item' => 'New Gallery Album',
            'edit_item' => 'Edit Gallery Album',
            'view_item' => 'View Gallery Album',
            'all_items' => 'All Gallery Albums',
            'search_items' => 'Search Gallery Albums',
            'parent_item_colon' => 'Parent Gallery Albums:',
            'not_found' => 'No gallery albums found.',
            'not_found_in_trash' => 'No gallery albums found in Trash.',
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'gallery-album'),
            'capability_type' => 'post',
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => null,
            'supports' => array('title', 'thumbnail'),
        );

        register_post_type('gallery_album', $args);
    }

// Add meta box to gallery album post type for adding images
    public function chikari_gallery_meta_box()
    {
        add_meta_box('gallery_images', 'Gallery Images', array($this, 'chikari_gallery_image_box'), 'gallery_album', 'normal', 'default');
    }

    public function chikari_gallery_image_box($post)
    {
        // Display existing images
        $gallery_image_ids = get_post_meta($post->ID, 'gallery_images', true);
        $gallery_images = !empty($gallery_image_ids) ? explode(',', $gallery_image_ids) : array();

        if (!empty($gallery_images)) {
            echo '<div class="gallery-images-container">';
            foreach ($gallery_images as $image_id) {
                $image = wp_get_attachment_image($image_id, 'thumbnail');
                echo '<div class="gallery-image">' . $image . '</div>';
            }
            echo '</div>';
        }

        // Display 'Add Image' button
        echo '<input type="button" class="button" value="Add Image" id="gallery-add-image-button">';

        // Hidden field to store the image IDs
        echo '<input type="hidden" name="gallery_images" id="gallery-images" value="' . esc_attr(implode(',', $gallery_images)) . '">';

        // Display saved attachments on the frame
        $saved_attachments = get_post_meta($post->ID, 'gallery_attachments', true);
        $saved_attachments = !empty($saved_attachments) ? (is_array($saved_attachments) ? $saved_attachments : explode(',', $saved_attachments)) : array();
        if (!empty($saved_attachments)) {
            echo '<div class="gallery-images-container">';
            foreach ($saved_attachments as $attachment_id) {
                $image = wp_get_attachment_image($attachment_id, 'thumbnail');
                echo '<div class="gallery-image">' . $image . '</div>';
            }
            echo '</div>';
        }

        // Image uploader script
        ?>
<style>
.gallery-images-container {
    display: flex;
    flex-wrap: wrap;
}

.gallery-image {
    flex: 0 0 25%;
    padding: 10px;
}
</style>

<script>
jQuery(document).ready(function($) {
    var frame;

    // Run when the 'Add Image' button is clicked
    $('#gallery-add-image-button').on('click', function(e) {
        e.preventDefault();

        // If the media frame already exists, reopen it
        if (frame) {
            frame.open();
            return;
        }

        // Create a new media frame
        frame = wp.media({
            title: 'Select Images',
            multiple: true,
            library: {
                type: 'image'
            },
            button: {
                text: 'Add Image(s)'
            }
        });

        // Run when an image is selected
        frame.on('select', function() {
            var attachments = frame.state().get('selection').toJSON();

            // Append selected image IDs to the hidden field
            var imageIds = attachments.map(function(attachment) {
                return attachment.id;
            });

            var existingImageIds = $('#gallery-images').val();
            if (existingImageIds) {
                // Combine existing and newly selected image IDs
                var allImageIds = existingImageIds.split(',').concat(imageIds);
                imageIds = allImageIds.filter((value, index, self) => {
                    return self.indexOf(value) === index;
                });
            }

            $('#gallery-images').val(imageIds.join(','));

            // Display selected images
            attachments.forEach(function(attachment) {
                var image = '<img src="' + attachment.sizes.thumbnail.url + '">';
                $('.gallery-images-container').append('<div class="gallery-image">' +
                    image + '</div>');
            });
        });

        // Open the media frame
        frame.open();
    });
});
</script>

<?php
}

    public function chikari_gallery_save_attachments($post_id)
    {
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Check if attachments were submitted
        if (isset($_POST['gallery_images'])) {
            $new_attachments = explode(',', $_POST['gallery_images']);

            // Get the existing attachments
            $existing_attachments = get_post_meta($post_id, 'gallery_attachments', true);
            $existing_attachments = !empty($existing_attachments) ? (is_array($existing_attachments) ? implode(',', $existing_attachments) : $existing_attachments) : '';

            // Merge the existing and new attachments
            $merged_attachments = array_unique(array_merge(explode(',', $existing_attachments), $new_attachments));

            // Save the attachments
            update_post_meta($post_id, 'gallery_attachments', implode(',', $merged_attachments));
        }
    }

}