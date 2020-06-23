jQuery(document).ready(function($) {
      var cnt = $('.svq_metabox').length;
      var admin_bar = ($('#wpadminbar').css('position') == 'fixed') ? $('#wpadminbar').height() : 0;
      //hide the remove button when there's only one metabox
      if (cnt === 1) {
        $('span.remove_svq_item').css('display', 'none');
      }

      function update_playlist_position_number() {
         $('div.svq_metabox').each(function(index) {
              var playlist_number = index + 1;
              //set position number to text and data-attribute
              $(this).find('.svq_playlist_position').text( (playlist_number < 10 ? '0'+playlist_number : playlist_number) ).data('position', playlist_number);
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
            tobeCleared.find('span.svq_accordion_label').text('');
            tobeCleared.find('div.svq_poster_thumb').remove();
            tobeCleared.find('div.svq_subtitle').remove();
            tobeCleared.find('div.svq_video_qualities').remove();
      }

      function video_input_html(curr_position, input_cnt){
        var name_url = 'svq[' + curr_position + '][svq_video][' + input_cnt + '][svq_url]';
        var name_label = 'svq[' + curr_position + '][svq_video][' + input_cnt + '][svq_label]';
        var name_order = 'svq[' + curr_position + '][svq_video][' + input_cnt + '][svq_order]';
        var name_mime = 'svq[' + curr_position + '][svq_video][' + input_cnt + '][svq_mime]';
        var name_length = 'svq[' + curr_position + '][svq_video][' + input_cnt + '][svq_length]';
        var html = 
          '<div class="svq_video_qualities">' + 
          '<span class="svq_clear_input" title="' + svq_admin_l10n.removeFields + '"></span>' +
          '<div><label>' + svq_admin_l10n.url + '<input class="video_url_input" type="text" size="80" value="" name="' + name_url + '" /></label></div>' +
          '<div><label>' + svq_admin_l10n.label + '<input class="video_quality_label" type="text" size="5" value="" name="' + name_label  + '" /></label></div>' +
          '<div><label>' + svq_admin_l10n.duration + '<input class="video_quality_duration" type="text" size="8" value="" name="' + name_length + '" /></label></div>' +
          '<input class="video_quality_mime" type="hidden" name="' + name_mime  + '" value="" />' +
          '<input class="video_quality_order" type="hidden" name="' + name_order + '" value="" />' +
          '</div>';
        return html;
      }

      function subtitle_input_html(curr_position, input_cnt){
        var name_label = 'svq[' + curr_position + '][svq_subs][' + input_cnt + '][svq_label]';
        var name_lang = 'svq[' + curr_position + '][svq_subs][' + input_cnt + '][svq_lang]';
        var name_src = 'svq[' + curr_position + '][svq_subs][' + input_cnt + '][svq_src]';
        var html = 
          '<div class="svq_subtitle">' +
          	'<span class="svq_clear_input" title="' + svq_admin_l10n.removeFields + '"></span>' +
          	'<div>'+
          		'<label>' + svq_admin_l10n.label + '<input class="svq_subtitle_label" required type="text" size="15" value="" name="' + name_label  + '" /></label>' +
          	'</div>' +
          	'<div>' +
          		'<label>' + svq_admin_l10n.lang + '<input class="svq_subtitle_lang" required type="text" size="5" value="" name="' + name_lang  + '" />' +
          			'<a href="https://r12a.github.io/app-subtags/" rel="noopener noreferrer" target="_blank" title="'+svq_admin_l10n.languageTags+'"><span class="dashicons dashicons-info"></span></a>' +
          		'</label>' +
          	'</div>' +
          	'<div>' +
          		'<label>' + svq_admin_l10n.url + '<input class="svq_subtitle_src" type="text" size="80" value="" name="' + name_src  + '" /></label>' +
          	'</div>' +
          '</div>';

        return html;
      }

      // handle playlist item toolbar click events
      $('.add_svq_item').click(function(event) {
        event.stopPropagation();
        var toClone = $(this).closest('div.svq_metabox');
        var cloned = toClone.clone(true, true);
        clear_item(cloned);
        cloned.insertAfter(toClone);
        $('span.remove_svq_item').css('display', 'inline-block');
        update_playlist_position_number();
        var position = cloned.find('.svq_playlist_position').data('position') - 1;
        $('#svq_metabox_container').accordion('refresh').accordion('option', 'active', position).one('accordionactivate', function(){
          $('html, body').animate({scrollTop: cloned.offset().top - admin_bar }, 'slow');
        });
        cnt++;
      });

      $('.remove_svq_item').on('click', function(event) {
        event.stopPropagation();
        tobeRemoved = $(this).closest('.svq_metabox');
        tobeRemoved.hide('slow', function() { 
          tobeRemoved.remove();
          update_playlist_position_number();
        });
        cnt--;
        if (cnt == 1) {
          $('span.remove_svq_item').css('display', 'none');  
        }
      });

      $('.clear_svq_item').click(function(event) {
        event.stopPropagation();
        var tobeCleared = $(this).closest('.svq_metabox');
        clear_item(tobeCleared);
      });

      $('.svq_playlist_position').on('click', function(event){
          event.stopPropagation();
      });

      $('input.svq_title_1').on('change', function(){
        var labelText = $(this).val();
        $(this).closest('.svq_metabox').find('.svq_accordion_label').text(labelText);
      });

      // drag&drop and accordion functionality
      $('#svq_metabox_container').sortable({
        opacity: 0.6,
        revert: true,
        cursor: 'move',
        handle: '.svq_handle',
        update: function(event, ui) {
          update_playlist_position_number();
        }
      });

      $('#svq_metabox_container').accordion({
        header: '.svq_metabox_header',
        icons:  false,
        heightStyle: 'content',
        collapsible: true
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
            library: {
              type: ['video/mp4', 'video/webm', 'video/ogv']
            },
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
              var video_input = $(video_input_html(curr_position - 1, input_cnt));
              video_input.find('.video_url_input').val(this.url);
              video_input.find('.video_quality_label').val(this.height + 'p');
              video_input.find('.video_quality_duration').val(this.fileLength);
              video_input.find('.video_quality_mime').val(this.mime);
              video_input.find('.video_quality_order').val(this.height);
              video_input.appendTo(vid_mamapapa);
              input_cnt++;
            });
        });
        // Opens the media library frame
        meta_video_frame.open();
    });

  $('.svq_manual_entry').click(function() {
      var container = $(this).parent();
      var curr_position = $(this).closest('div.svq_metabox').find('span.svq_playlist_position').data('position') - 1;
      var input_cnt;
      var new_input = '';
      
      if ( container.hasClass('svq_video') ){
        input_cnt = container.find('.svq_video_qualities').length;
        new_input = $( video_input_html(curr_position, input_cnt) );
      } else if ( container.hasClass('svq_subtitles') ){
        input_cnt = container.find('.svq_subtitle').length;
        new_input = $( subtitle_input_html(curr_position, input_cnt) );
      }
      if ( new_input.length ) {
        new_input.appendTo(container);
      }
  });

// get height and duration of video from its metadata (video has to be partially loaded)
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
      case 'webm':
        return 'video/webm';
        break;
      case 'ogv':
        return 'video/ogg';
        break;
      default:
        return 'video/mp4';
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
      getVideoHeight(wrapper, url, function(h,d){
        wrapper.find('.video_quality_order').val(h);
        wrapper.find('.video_quality_label').val(h + 'p');
        var hours = Math.floor(d / 3600);
        var minutes = Math.floor(d % 3600 / 60);
        var seconds = Math.round(d % 60);
        var duration = (hours > 0 ? hours + ':' : '') + (minutes < 10 ? '0' + minutes : minutes) + ':' + (seconds < 10 ? '0' + seconds : seconds);
        wrapper.find('.video_quality_duration').val(duration);
      });
      wrapper.find('input.video_quality_mime').val(fileType);
    }
  });


// Clear video data input
    $('.svq_metabox').on('click', 'span.svq_clear_input', function() {
      var tobeCleared = $(this).parent();
      var vid_qual_boxes = tobeCleared.siblings('.svq_video_qualities');
      tobeCleared.remove();
      vid_qual_boxes.each(function(index) {
        var input = $(this).find('input');
        input.each(function(){
          var name = $(this).attr('name');
          var new_name = name.replace(/svq_video\]\[(.+?)\]/, 'svq_video][' + index + ']');
          $(this).attr('name', new_name);
        });
      });
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


// Subtitle upload
    var meta_subtitle_frame;
    var subtitle_url_input;

     // Runs when the subtitle button is clicked
    $('.svq_choose_subtitle').click(function(e){
        e.preventDefault();
        $container = $(this).closest('div.svq_subtitles');
        curr_position = $container.closest('.svq_metabox').find('span.svq_playlist_position').data('position');
        // If the frame already exists, re-open it
        if (meta_subtitle_frame) {
            meta_subtitle_frame.open();
            return;
        }
        // Sets up the media library frame
        meta_subtitle_frame = wp.media.frames.meta_subtitle_frame = wp.media({
            title: svq_admin_l10n.mmSubtitle,
            library: { type: 'text' },
            multiple: true
        });
        // Runs when a subtitle is selected
        meta_subtitle_frame.on('select', function(){
            // Grabs the attachment selection and creates a JSON representation of the model
            var media_attachment = meta_subtitle_frame.state().get('selection').toJSON();
            $.each(media_attachment, function(index){
                var subtitle_input = $( subtitle_input_html(curr_position - 1, index) );
                subtitle_input.find('.svq_subtitle_src').val(this.url);
                subtitle_input.appendTo($container);
            });
        });
        // Opens the media library frame
        meta_subtitle_frame.open();
    });
});