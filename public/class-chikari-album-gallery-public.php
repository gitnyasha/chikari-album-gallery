<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://marshallchikari.co.zw
 * @since      1.0.0
 *
 * @package    Chikari_Album_Gallery
 * @subpackage Chikari_Album_Gallery/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Chikari_Album_Gallery
 * @subpackage Chikari_Album_Gallery/public
 * @author     Marshall Chikari <hello@marshallchikari.co.zw>
 */
class Chikari_Album_Gallery_Public
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
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

        add_shortcode('chikari_gallery', array($this, 'chikari_gallery_shortcode'));

    }

    /**
     * Register the stylesheets for the public-facing side of the site.
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

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/chikari-album-gallery-public.css', array(), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the public-facing side of the site.
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

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/chikari-album-gallery-public.js', array('jquery'), $this->version, false);

    }

    public function chikari_gallery_shortcode($atts)
    {
        $atts = shortcode_atts(array(
            'columns' => 3, // Default number of columns for album display
        ), $atts);

        $output = '';

        // Query albums
        $albums = new WP_Query(array(
            'post_type' => 'gallery_album',
            'posts_per_page' => -1,
        ));

        if ($albums->have_posts()) {
            // Output album list
            $output .= '<div class="gallery-albums">';
            while ($albums->have_posts()) {
                $albums->the_post();
                $album_id = get_the_ID();
                $thumbnail = get_the_post_thumbnail($album_id, 'medium');
                $album_title = get_the_title();
                $output .= '<div class="gallery-album">';
                $output .= '<a href="#" data-album-id="' . esc_attr($album_id) . '">' . $thumbnail . '</a>';
                $output .= '<div class="gallery-album-title"><h5>' . $album_title . '</h5></div>';
                $output .= '</div>';

                // Get gallery attachments for the album
                $gallery_attachments = get_post_meta($album_id, 'gallery_attachments', true);

                // Ensure the gallery attachments field is set
                $gallery_attachments = !empty($gallery_attachments) ? $gallery_attachments : '';

                // Hidden field to store the attachment URLs
                $attachment_urls = array();
                if (!empty($gallery_attachments)) {
                    $attachment_ids = explode(',', $gallery_attachments);
                    foreach ($attachment_ids as $attachment_id) {
                        $attachment_url = wp_get_attachment_url($attachment_id);
                        if ($attachment_url) {
                            $attachment_urls[] = $attachment_url;
                        }
                    }
                }
                $attachment_urls_string = implode(',', $attachment_urls);
                $output .= '<input type="hidden" id="gallery-attachments-' . esc_attr($album_id) . '" value="' . esc_attr($attachment_urls_string) . '">';
            }
            $output .= '</div>';

            // Output modal
            $output .= '<div id="gallery-modal" class="gallery-modal">';
            $output .= '<span class="gallery-modal-close">&times;</span>';
            $output .= '<div class="gallery-modal-content">';
            $output .= '<div class="gallery-slideshow"></div>';
            $output .= '</div>';
            $output .= '<a class="gallery-prev">&#10094;</a>';
            $output .= '<a class="gallery-next">&#10095;</a>';
            $output .= '</div>';

            // Output JavaScript for modal functionality
            $output .= '
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var albumLinks = document.querySelectorAll(".gallery-album a");
        var slideshow = document.querySelector(".gallery-slideshow");
        var prevButton = document.querySelector(".gallery-prev");
        var nextButton = document.querySelector(".gallery-next");
        var closeButton = document.querySelector(".gallery-modal-close");
        var modal = document.getElementById("gallery-modal");
        var currentSlide = 0;
        var autoSlideInterval = null; // Variable to hold the auto slide interval

        // Attach click event listeners to album links
        albumLinks.forEach(function(link) {
            link.addEventListener("click", function(e) {
                e.preventDefault();
                var albumId = this.getAttribute("data-album-id");
                var attachmentUrls = document.getElementById("gallery-attachments-" + albumId).value.split(",");
                var slideHtml = "";

                // Generate HTML for each attachment URL
                attachmentUrls.forEach(function(url) {
                    slideHtml += "<div class=\\"gallery-slide\\"><img src=\\"" + url + "\\" /></div>";
                });

                // Display the slideshow in the modal
                slideshow.innerHTML = slideHtml;
                currentSlide = 0;
                showSlide(currentSlide);
                modal.style.display = "block";
                startAutoSlide(); // Start the auto slide feature
            });
        });

        // Close the modal when the close button is clicked
        closeButton.addEventListener("click", function() {
            modal.style.display = "none";
            stopAutoSlide(); // Stop the auto slide feature when the modal is closed
        });

        // Add event listeners for previous and next buttons
        prevButton.addEventListener("click", function() {
            navigateSlides(-1);
            stopAutoSlide(); // Stop the auto slide when manual navigation is used
        });

        nextButton.addEventListener("click", function() {
            navigateSlides(1);
            stopAutoSlide(); // Stop the auto slide when manual navigation is used
        });

        // Function to navigate between slides
        function navigateSlides(direction) {
            currentSlide = (currentSlide + direction + slideshow.children.length) % slideshow.children.length;
            showSlide(currentSlide);
        }

        // Function to show the specified slide
        function showSlide(slideIndex) {
            for (var i = 0; i < slideshow.children.length; i++) {
                slideshow.children[i].style.display = "none";
            }
            slideshow.children[slideIndex].style.display = "block";
        }

        // Function to start the auto slide feature
        function startAutoSlide() {
            autoSlideInterval = setInterval(function() {
                navigateSlides(1);
            }, 3000); // Change slide every 3 seconds (adjust the time interval as needed)
        }

        // Function to stop the auto slide feature
        function stopAutoSlide() {
            clearInterval(autoSlideInterval);
        }
    });
</script>
';
        }

        wp_reset_postdata();

        return $output;
    }

}