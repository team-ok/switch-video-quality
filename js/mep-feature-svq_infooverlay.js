
(function($) {

    $.extend(MediaElementPlayer.prototype, {
        
        buildsvqinfooverlay: function(player, controls, layers, media) {
          var t = this;
          var curr_player = $('video.svq').index(t.node);
          if (!$(t.node).hasClass('svq') || svq_options[curr_player].show_svq_infooverlay != 'on') {
                return false;
          }
          var index = _svqInfoSettings.svqIndex;

          if (svq_playlist_data[curr_player][index]){
            var svq_title = svq_playlist_data[curr_player][index].svq_title;
            var svq_title2 = svq_playlist_data[curr_player][index].svq_title2;
            var svq_title3 = svq_playlist_data[curr_player][index].svq_title3;
            var svq_ext_link = svq_playlist_data[curr_player][index].svq_ext_link || [];
            var linkText = svq_ext_link['text'] || '';
            var linkURL = svq_ext_link['url'] || '';
            var svq_duration = '';
            if (svq_playlist_data[curr_player][index].svq_video !== undefined){
              svq_duration = svq_playlist_data[curr_player][index].svq_video[0].svq_length;
            }
          }

          player.svq_infooverlay = $('<div class="svq-info">' +
           	'<div class="svq-info-playpause"><button aria-label="' + _svqInfoSettings.svq_play + '" title="' + _svqInfoSettings.svq_play + '" aria-controls="' + t.id + '" type="button"></button></div>' + 
            '<div class="svq-info-text">' +
            '<div class="svq-info-title"></div>' +
            '<div class="svq-info-title2"></div>' +
            '<div class="svq-info-title3"></div>' +
            '<div class="svq-info-ext-link"><a href="" target="_blank"></a></div>' +
            '<div class="svq-info-duration"></div>' +
            '</div>' + 
           	'</div>').insertAfter(layers);

          (svq_title !== "" && svq_title !== undefined) ? t.svq_infooverlay.find('div.svq-info-title').text(svq_title).css('display', 'block'): t.svq_infooverlay.find('div.svq-info-title').css('display', 'none');
          (svq_title2 !== "" && svq_title2 !== undefined) ? t.svq_infooverlay.find('div.svq-info-title2').text(svq_title2).css('display', 'block'): t.svq_infooverlay.find('div.svq-info-title2').css('display', 'none');
          (svq_title3 !== "" && svq_title3 !== undefined) ? t.svq_infooverlay.find('div.svq-info-title3').text(svq_title3).css('display', 'block'): t.svq_infooverlay.find('div.svq-info-title3').css('display', 'none');
          ( linkURL !== "" ? t.svq_infooverlay.find('div.svq-info-ext-link a').attr('href', linkURL).text( (linkText ? linkText : linkURL) ).parent().css('display', 'block'): t.svq_infooverlay.find('div.svq-info-ext-link').css('display', 'none') );
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
              svq_playpause.addClass('svq_playing').attr({'aria-label':_svqInfoSettings.svq_pause,'title':_svqInfoSettings.svq_pause});
            });
            $(media).on('pause', function() {
              svq_playpause.removeClass('svq_playing').attr({'aria-label':_svqInfoSettings.svq_play,'title':_svqInfoSettings.svq_play});
            });
            player.container.hover(
              function() {
                if (!player.isFullScreen) {
                  player.svq_infooverlay.css('visibility', 'visible');
                }
              },
                function() {
                  player.svq_infooverlay.css('visibility', 'hidden');
                }
            );
            
           $(document).on('webkitfullscreenchange mozfullscreenchange fullscreenchange MSFullscreenChange', function() {
              if (player.isFullScreen) {
              	 player.svq_infooverlay.css('visibility', 'hidden');
              }
            });
        }
    });
})(mejs.$);
