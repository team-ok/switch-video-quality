(function($) {
    $.extend(MediaElementPlayer.prototype, {
    buildsvqplaylist: function(player, controls, layers, media) {
        var t = this;
        if (!$(media).hasClass('svq')){
            return false;
        }

        // add to list when playlist item is clicked
        var wrapper = $(t.container).closest('div.wp-video');
        wrapper.find('li.svq_playlist-item').on('click', function() {
            var curr_player = $('video.svq').index($(media));
            var index = $(this).siblings().addBack().index(this);
            // get playlist data from json-object
            var svq_video = svq_playlist_data[curr_player][index].svq_video,
            svq_title = svq_playlist_data[curr_player][index].svq_title,
            svq_title2 = svq_playlist_data[curr_player][index].svq_title2,
            svq_title3 = svq_playlist_data[curr_player][index].svq_title3,
            svq_duration = svq_video[0].svq_length,
            svq_poster = ($.isEmptyObject(svq_playlist_data[curr_player][index].svq_poster)) ? '' : svq_playlist_data[curr_player][index].svq_poster;

            //remove old sources from DOM
            $(media).removeAttr('src').find('source').remove();
            //add new sources to DOM
            for (var i = 0; i < svq_video.length; i++) {
                    $(media).append('<source src="' + svq_video[i].svq_url + '" title="' + ($.isEmptyObject(svq_video[i].svq_label) ? '' : svq_video[i].svq_label) + '" type="' + svq_video[i].svq_mime + '" data-order="' + ($.isEmptyObject(svq_video[i].svq_order) ? '' : svq_video[i].svq_order)  + '"></source>');
            };
            //scroll to player
            $('html, body').animate({scrollTop: t.container.offset().top}, 'fast');

            //set new poster image
            $(media).attr('poster', svq_poster);
            t.layers.find('.mejs-poster').css('background-image', 'url("' + svq_poster + '")').find('img').attr('src', svq_poster);
            
            // refresh text in info-overlay
            if (t.svq_infooverlay){
                t.svq_infooverlay.find('div.svq-info-title').text(svq_title).css('display', ($.isEmptyObject(svq_title) ? 'none' : 'block'));
                t.svq_infooverlay.find('div.svq-info-title2').text(svq_title2).css('display', ($.isEmptyObject(svq_title2) ? 'none' : 'block'));
                t.svq_infooverlay.find('div.svq-info-title3').text(svq_title3).css('display', ($.isEmptyObject(svq_title3) ? 'none' : 'block'));
                t.svq_infooverlay.find('div.svq-info-duration').text(svq_duration).css('display', ($.isEmptyObject(svq_duration) ? 'none' : 'block'));
            }
            //load and play video
            media.load();
            media.play();
            //add sources to the quality switch field
            t.refresh_source_list();
        });
    }
    });
})(mejs.$);