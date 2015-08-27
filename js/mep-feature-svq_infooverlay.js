
(function($) {

    $.extend(MediaElementPlayer.prototype, {
        
        buildsvqinfooverlay: function(player, controls, layers, media) {
          var t = this;
          var curr_player = $('video.svq').index($(media));
          if (!$(media).hasClass('svq') || svq_options[curr_player].show_svq_infooverlay != 'on') {
                return false;
          }
          var svq_title = svq_playlist_data[curr_player][0].svq_title;
          var svq_title2 = svq_playlist_data[curr_player][0].svq_title2;
          var svq_title3 = svq_playlist_data[curr_player][0].svq_title3;
          var svq_duration = svq_playlist_data[curr_player][0].svq_video[0].svq_length; 
          
          player.svq_infooverlay = $('<div class="svq-info">' +
           	'<div class="svq-info-playpause"><button aria-label="' + _wpmejsSettings.svq_play + '" title="' + _wpmejsSettings.svq_play + '" aria-controls="mep_0" type="button"></button></div>' + 
            '<div class="svq-info-text">' +
            '<div class="svq-info-title"></div>' +
            '<div class="svq-info-title2"></div>' +
            '<div class="svq-info-title3"></div>' +
            '<div class="svq-info-duration"></div>' +
            '</div>' + 
           	'</div>').insertAfter(layers);

          (svq_title !== "" && svq_title !== undefined) ? t.svq_infooverlay.find('div.svq-info-title').text(svq_title).css('display', 'block'): t.svq_infooverlay.find('div.svq-info-title').css('display', 'none');
          (svq_title2 !== "" && svq_title2 !== undefined) ? t.svq_infooverlay.find('div.svq-info-title2').text(svq_title2).css('display', 'block'): t.svq_infooverlay.find('div.svq-info-title2').css('display', 'none');
          (svq_title3 !== "" && svq_title3 !== undefined) ? t.svq_infooverlay.find('div.svq-info-title3').text(svq_title3).css('display', 'block'): t.svq_infooverlay.find('div.svq-info-title3').css('display', 'none');
          (svq_duration !== "" && svq_duration !== undefined) ? t.svq_infooverlay.find('div.svq-info-duration').text(svq_duration).css('display', 'block'): t.svq_infooverlay.find('div.svq-info-duration').css('display', 'none');

        var svq_playpause = t.svq_infooverlay.find('.svq-info-playpause > button');
           /*player.svq_infooverlay.css({
              'background-color': controls.css('background-color'),
              'margin-bottom': controls.height()
            });*/
            svq_playpause.on('click', function() {
              ( media.paused ) ? media.play() : media.pause();
            });
            $(media).on('play', function() {
              svq_playpause.css('background-position', '100% 0').attr({'aria-label':_wpmejsSettings.svq_pause,'title':_wpmejsSettings.svq_pause});
            });
            $(media).on('pause', function() {
              svq_playpause.css('background-position', '0 0').attr({'aria-label':_wpmejsSettings.svq_play,'title':_wpmejsSettings.svq_play});
            });
           player.container.hover(
                function() {
                if (!player.isFullScreen) {
                player.svq_infooverlay.css('visibility', 'visible');
                }},
                function() {
                player.svq_infooverlay.css('visibility', 'hidden');
                });
            
           $(document).on('webkitfullscreenchange mozfullscreenchange fullscreenchange MSFullscreenChange', function() {
              if (player.isFullScreen) {
              	 player.svq_infooverlay.css('visibility', 'hidden');
              }
            });
        }
    });
})(mejs.$);
