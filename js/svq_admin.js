jQuery(document).ready(function($) {
      var cnt = $('.svq_metabox').length;
      var admin_bar = ($('#wpadminbar').css('position') == 'fixed') ? $('#wpadminbar').height() : 0;
      //hide the remove button when there's only one metabox
      if (cnt == 1) {
        $('span.remove_svq_item').css('display', 'none');
      }

      function update_playlist_position_number() {
         $('div.svq_metabox').each(function(index) {
              var playlist_number = index + 1;
              //set position number to text and data-attribute
              $(this).find('.svq_playlist_position').text(svq_admin_l10n.playlistPosition + ' ' + playlist_number).data('position', playlist_number);
                //update name attributes for correct saving
                var input = $(this).find('input, select').not('input[type="button"]');
                input.each(function() {
                    var name = $(this).attr('name');
                    var new_name = name.replace(/svq\[(.+?)\]/, 'svq[' + index + ']');
                    $(this).attr('name', new_name);
                });
          });
      }
        function clear_item(tobeCleared) {
              tobeCleared.find('input[type="checkbox"]').prop('checked', false);
              tobeCleared.find('input[type="text"]').val('');
              tobeCleared.find('div.svq_poster_thumb').remove();
              tobeCleared.find('div.svq_video_qualities').remove();
        }  
        $('.add_svq_item').click(function() {
            var toClone = $(this).closest('div.svq_metabox');
            var cloned = toClone.clone(true);
            clear_item(cloned);
            cloned.insertAfter(toClone);
            $('span.remove_svq_item').css('display', 'inline-block');
            update_playlist_position_number();
            $('html, body').animate({scrollTop: cloned.offset().top - admin_bar }, 'slow');
            cnt++;
        });
        $('span.remove_svq_item').on('click', function() {
                tobeRemoved = $(this).closest('.svq_metabox');
                tobeRemoved.hide('slow', function() { tobeRemoved.remove(); update_playlist_position_number(); });
                cnt--;
                if (cnt == 1) {
                  $('span.remove_svq_item').css('display', 'none');  
                }
        });
        $('span.clear_svq_item').click(function() {
              var tobeCleared = $(this).closest('.svq_metabox');
              clear_item(tobeCleared);
        });
        $('.svq_sortable').sortable({
         opacity: 0.6,
         revert: true,
         cursor: 'move',
         handle: '.svq_handle',
         update: function(event, ui) {
                   update_playlist_position_number();
                 }
        });
        $('.svq_scrolltotop').on('click', function(){
          $('html, body').animate({scrollTop: 0}, 'fast');
        });
// Video upload
    //variables have to be defined outside of the click-function for correct assignment when reopening the meta frame
    var meta_video_frame;
    var vid_mamapapa;
    var curr_position;
     // Runs when the choose video button is clicked
    $('.svq_choose_vid').click(function(e){
        // Prevents the default action from occuring
        e.preventDefault();
        vid_mamapapa = $(this).closest('div.svq_video');
        // get position number from data-attribute
        curr_position = vid_mamapapa.closest('.svq_metabox').find('span.svq_playlist_position').data('position');
        // If the frame already exists and the playlist item didn't change, re-open it
        if (meta_video_frame) {
            meta_video_frame.open();
            return;
        }
        // Sets up the media library frame
            meta_video_frame = wp.media.frames.meta_video_frame = wp.media({
            title: svq_admin_l10n.mmVideo,
            library: { type: 'video' },
            multiple: true
        });
        // Runs when a video is selected
        meta_video_frame.on('select', function(){
            // Grabs the attachment selection and creates a JSON representation of the model
            var media_attachment = meta_video_frame.state().get('selection').toJSON();
            // Counts the existing input fields (to be used as array index)
            var input_cnt = vid_mamapapa.find('.video_url_input').length;
            // Creates video input fields and sends the attachment URL, height (as label and order number) and mime types to them
            $.each(media_attachment, function(index) {
              var name_url = 'svq[' + (curr_position - 1) + '][svq_video][' + input_cnt + '][svq_url]';
              var name_label = 'svq[' + (curr_position - 1) + '][svq_video][' + input_cnt + '][svq_label]';
              var name_order = 'svq[' + (curr_position - 1) + '][svq_video][' + input_cnt + '][svq_order]';
              var name_mime = 'svq[' + (curr_position - 1) + '][svq_video][' + input_cnt + '][svq_mime]';
              var name_length = 'svq[' + (curr_position - 1) + '][svq_video][' + input_cnt + '][svq_length]';
              var duration = this.fileLength;
              $('<div class="svq_video_qualities">' +
                '<span class="clear_video_input" title="' + svq_admin_l10n.removeFields + '"></span>' +
                '<div><label>&nbsp;' + svq_admin_l10n.url + '&nbsp;<input class="video_url_input" type="text" size="80" value="' + this.url + '" name="' + name_url + '" /></label></div>' +
                '<div><label>&nbsp;' + svq_admin_l10n.label + '&nbsp;<input class="video_quality_label" type="text" size="5" value="' + this.height + 'p" name="' + name_label + '" /></label></div>' +
                '<div><label>&nbsp;' + svq_admin_l10n.mimeType + '&nbsp;<select class="video_quality_mime" required name="' + name_mime  + '">' +
                  '<option value="">' + svq_admin_l10n.select + '</option>' +
                  '<option value="video/mp4">video/mp4</option>' +
                  '<option value="video/webm">video/webm</option>' +
                  '<option value="video/ogg">video/ogg</option>' +
                '</select></label></div>' +
                '<div><label>&nbsp;' + svq_admin_l10n.duration + '&nbsp;<input class="video_quality_duration" type="text" size="8" value="' + duration + '" name="' + name_length + '" /></label></div>' +
                '<div><label>&nbsp;' + svq_admin_l10n.order + '&nbsp;<input class="video_quality_order" type="text" size="5" value="' + this.height + '" name="' + name_order + '" /></label></div>' +
                '</div>')
              .appendTo(vid_mamapapa).find('select option[value="' + this.mime + '"]').attr('selected', 'selected');

              input_cnt++;
            });
        });
        // Opens the media library frame
        meta_video_frame.open();
    });
  $('.svq_vid_manual').click(function() {
      var curr_position = $(this).closest('div.svq_metabox').find('span.svq_playlist_position').data('position');
      var input_cnt = $(this).parent().find('.video_url_input').length;
      var name_url = 'svq[' + (curr_position - 1) + '][svq_video][' + input_cnt + '][svq_url]';
      var name_label = 'svq[' + (curr_position - 1) + '][svq_video][' + input_cnt + '][svq_label]';
      var name_order = 'svq[' + (curr_position - 1) + '][svq_video][' + input_cnt + '][svq_order]';
      var name_mime = 'svq[' + (curr_position - 1) + '][svq_video][' + input_cnt + '][svq_mime]';
      var name_length = 'svq[' + (curr_position - 1) + '][svq_video][' + input_cnt + '][svq_length]';
      $('<div class="svq_video_qualities">' + 
        '<span class="clear_video_input" title="' + svq_admin_l10n.removeFields + '"></span>' +
        '<div><label>&nbsp;' + svq_admin_l10n.url + '&nbsp;<input class="video_url_input" type="text" size="80" value="" name="' + name_url + '" /></label></div>' +
        '<div><label>&nbsp;' + svq_admin_l10n.label + '&nbsp;<input class="video_quality_label" type="text" size="5" value="" name="' + name_label  + '" /></label></div>' +
        '<div><label>&nbsp;' + svq_admin_l10n.mimeType + '&nbsp;<select class="video_quality_mime" required name="' + name_mime  + '">' +
            '<option value="">' + svq_admin_l10n.select + '</option>' +
            '<option value="video/mp4">video/mp4</option>' +
            '<option value="video/webm">video/webm</option>' +
            '<option value="video/ogg">video/ogg</option>' +
          '</select></label></div>' +
        '<div><label>&nbsp;' + svq_admin_l10n.duration + '&nbsp;<input class="video_quality_duration" type="text" size="8" value="" name="' + name_length + '" /></label></div>' +
        '<div><label>&nbsp;' + svq_admin_l10n.order + '&nbsp;<input class="video_quality_order" type="text" size="5" value="" name="' + name_order + '" /></label></div>' +
        '</div>')
        .appendTo( $(this).parent() );
  });

// get height of video from its metadata (video has to be partially loaded)
  function getVideoHeight(wrapper, url, callback){
    var video=document.createElement("video");
    video.style.display = "none";
      video.onloadedmetadata=function(){
         callback(this.videoHeight,this.duration);
         document.body.removeChild(video);   
      };
      document.body.appendChild(video).preload="metadata";
      video.src=url;
      video.onerror=function(){
        document.body.removeChild(video);
        wrapper.append('<p class="svq_error">'+svq_admin_l10n.urlError+'</p>');
      }
  }
// get filetype from url
  function getFileType(url){
    var fileType = url.split('.').pop();
    switch (fileType) {
      case 'mp4':
        return 'video/mp4';
        break;
      case 'webm':
        return 'video/webm';
        break;
      case 'ogv':
        return 'video/ogg';
        break;
      default:
        return 'invalid';
    }
  }
// autofill fields when url is entered manually
  $('.svq_video').on('change', '.video_url_input', function(){
    var t = $(this);
    var url = t.val().trim();
    var wrapper = t.closest('.svq_video_qualities');
    wrapper.find('.svq_error').remove();
    if ($.trim(url).length > 0) {
      var fileType = getFileType(url);
      if (fileType == 'invalid'){
        wrapper.append('<p class="svq_error">'+svq_admin_l10n.urlError+'</p>');
      } else {
        getVideoHeight(wrapper, url, function(h,d){
          wrapper.find('.video_quality_order').val(h);
          wrapper.find('.video_quality_label').val(h + 'p');
          var minutes = Math.floor(d / 60);
          var seconds = Math.round(d % 60);
          var duration = minutes + ':' + (seconds < 10 ? '0' + seconds : seconds);
          wrapper.find('.video_quality_duration').val(duration);
        });
        wrapper.find('select option[value="' + fileType + '"]').attr('selected', 'selected');
      }
    }
  });


// Clear video data input
    $('.svq_metabox').on('click', 'span.clear_video_input', function() {
      $(this).parent().remove();
    });


// Image upload
    var meta_image_frame;
    var img_mamapapa;
    var image_url_input;
     // Runs when the image button is clicked
    $('.svq_button_image').click(function(e){
        e.preventDefault();
        img_mamapapa = $(this).closest('div.svq_poster');
        image_url_input = img_mamapapa.find('input.img_url_input');
        // If the frame already exists, re-open it
        if (meta_image_frame) {
            meta_image_frame.open();
            return;
        }
        // Sets up the media library frame
        meta_image_frame = wp.media.frames.meta_image_frame = wp.media({
            title: svq_admin_l10n.mmImage,
            library: { type: 'image' }
        });
        // Runs when an image is selected
        meta_image_frame.on('select', function(){
            // Grabs the attachment selection and creates a JSON representation of the model
            var media_attachment = meta_image_frame.state().get('selection').first().toJSON();
            var image_url = media_attachment.url;
            // Sends the attachment URL to the image input field
            image_url_input.val(image_url);
            // Displays the image
            var curr_poster_image = img_mamapapa.find('div.svq_poster_thumb > img');
            if (curr_poster_image.length > 0) { 
                curr_poster_image.attr('src', image_url);
            } else {
                img_mamapapa.find('input.svq_button_image').after('<div class="svq_poster_thumb"><br /><img src="' + image_url + '"></div>');
            }
        });
        // Opens the media library frame
        meta_image_frame.open();
    });
});