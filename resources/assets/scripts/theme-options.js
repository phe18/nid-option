jQuery(document).ready(function($) {
  // Tabs navigation
  $('.options-tabs a').on('click', function(e) {
      e.preventDefault();

      var targetTab = $(this).attr('href');

      // Update active tab
      $('.options-tabs a').removeClass('active');
      $(this).addClass('active');

      // Show target tab content
      $('.tab-content').removeClass('active');
      $(targetTab).addClass('active');
  });

  // Media uploader for Logo and Favicon
  $('.upload-button').on('click', function(e) {
      e.preventDefault();

      var button = $(this);
      var uploaderContainer = button.closest('.media-uploader');
      var type = uploaderContainer.data('type');

      var mediaUploader = wp.media({
          title: type === 'logo' ? 'Select or Upload Logo' : 'Select or Upload Favicon',
          button: {
              text: 'Use this ' + (type === 'logo' ? 'Logo' : 'Favicon')
          },
          multiple: false
      });

      mediaUploader.on('select', function() {
          var attachment = mediaUploader.state().get('selection').first().toJSON();

          // Update hidden fields
          uploaderContainer.find('input[name="' + type + '_id"]').val(attachment.id);
          uploaderContainer.find('input[name="' + type + '_url"]').val(attachment.url);

          // Update preview
          uploaderContainer.find('.preview').html('<img src="' + attachment.url + '" alt="' + type + ' Preview">');

          // Show remove button
          uploaderContainer.find('.remove-button').show();
      });

      mediaUploader.open();
  });

  // Remove media button
  $('.remove-button').on('click', function(e) {
      e.preventDefault();

      var button = $(this);
      var uploaderContainer = button.closest('.media-uploader');
      var type = uploaderContainer.data('type');

      // Clear hidden fields
      uploaderContainer.find('input[name="' + type + '_id"]').val('');
      uploaderContainer.find('input[name="' + type + '_url"]').val('');

      // Clear preview
      uploaderContainer.find('.preview').html('');

      // Hide remove button
      button.hide();
  });

  // Save options with AJAX
  $('.save-options').on('click', function(e) {
      e.preventDefault();

      var $button = $(this);
      var $spinner = $('.spinner');
      var $form = $('#theme-options-form');

      // Show spinner
      $spinner.addClass('is-active');
      $button.prop('disabled', true);

      // Make sure WP editors update textareas
      if (typeof tinyMCE !== 'undefined') {
          tinyMCE.triggerSave();
      }

      // Prepare data
      var formData = $form.serialize();
      var logoId = $('input[name="logo_id"]').val();
      var logoUrl = $('input[name="logo_url"]').val();
      var faviconId = $('input[name="favicon_id"]').val();
      var faviconUrl = $('input[name="favicon_url"]').val();

      // Send AJAX request
      $.ajax({
          url: themeOptionsData.ajaxUrl,
          type: 'POST',
          data: {
              action: 'save_theme_options',
              nonce: themeOptionsData.nonce,
              form_data: formData,
              logo_id: logoId,
              logo_url: logoUrl,
              favicon_id: faviconId,
              favicon_url: faviconUrl
          },
          success: function(response) {
              // Hide spinner
              $spinner.removeClass('is-active');
              $button.prop('disabled', false);

              if (response.success) {
                  // Show success message
                  $('.notice-success').fadeIn().delay(2000).fadeOut();
              } else {
                  // Show error message
                  $('.settings-error').find('.error-message').text(response.data.message);
                  $('.settings-error').fadeIn().delay(3000).fadeOut();
              }
          },
          error: function() {
              // Hide spinner
              $spinner.removeClass('is-active');
              $button.prop('disabled', false);

              // Show error message
              $('.settings-error').find('.error-message').text('An error occurred. Please try again.');
              $('.settings-error').fadeIn().delay(3000).fadeOut();
          }
      });
  });
});
