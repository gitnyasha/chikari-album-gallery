(function ($) {
  "use strict";

  /**
   * All of the code for your public-facing JavaScript source
   * should reside in this file.
   *
   * Note: It has been assumed you will write jQuery code here, so the
   * $ function reference has been prepared for usage within the scope
   * of this function.
   *
   * This enables you to define handlers, for when the DOM is ready:
   *
   * $(function() {
   *
   * });
   *
   * When the window is loaded:
   *
   * $( window ).load(function() {
   *
   * });
   *
   * ...and/or other possibilities.
   *
   * Ideally, it is not considered best practise to attach more than a
   * single DOM-ready or window-load handler for a particular page.
   * Although scripts in the WordPress core, Plugins and Themes may be
   * practising this, we should strive to set a better example in our own work.
   */

  jQuery(document).ready(function ($) {
    $("#gallery-add-image-button").on("click", function (e) {
      e.preventDefault();

      // Create the media frame
      var mediaUploader = wp.media({
        title: "Select Images",
        button: {
          text: "Add to Gallery",
        },
        multiple: true, // Allow selecting multiple images
      });

      // Open the media uploader
      mediaUploader.open();

      // Handle selection of images
      mediaUploader.on("select", function () {
        var attachments = mediaUploader.state().get("selection").toJSON();

        if (attachments.length > 0) {
          var imageIds = $("#gallery-images")
            .val()
            .split(",")
            .filter(function (value) {
              return value !== ""; // Remove empty values
            });

          // Append newly selected image IDs to the hidden field value
          for (var i = 0; i < attachments.length; i++) {
            imageIds.push(attachments[i].id);
          }

          // Update the hidden field value with the updated image IDs
          $("#gallery-images").val(imageIds.join(","));

          // Display the selected images in the meta box
          for (var j = 0; j < attachments.length; j++) {
            var image = "<div>" + attachments[j].url + "</div>";
            $("#gallery-images-container").append(image);
          }
        }
      });
    });
  });
})(jQuery);
