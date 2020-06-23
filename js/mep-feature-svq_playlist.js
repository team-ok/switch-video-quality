(function($) {
    $.extend(MediaElementPlayer.prototype, {
    buildsvqplaylist: function(player, controls, layers, media) {
        var t = this;

        if (!$(t.node).hasClass('svq')){
            return false;
        }
        //set playlist item thumbnail size to a 16:9 aspect ratio
        var thumbHeight = Math.round( $('.svq_playlist_item_poster').width() * 0.5625 );
        $('.svq_playlist_item_poster').find('img').height(thumbHeight);

        // add to list when playlist item is clicked
        var wrapper = $(t.container).closest('div.wp-video');
        wrapper.find('.svq_playlist_item_poster').on('click', function() {
            var curr_player = $('video.svq').index(t.node);
            var item = $(this).parent();
            var index = item.siblings().addBack().index( item );
            // get playlist data from json-object
            var svq_video = svq_playlist_data[curr_player][index].svq_video;
            if (svq_video === undefined){
                alert(_svqPlaylistSettings.svq_noVideo);
                return false;
            }
            var svq_title = svq_playlist_data[curr_player][index].svq_title,
            svq_title2 = svq_playlist_data[curr_player][index].svq_title2,
            svq_title3 = svq_playlist_data[curr_player][index].svq_title3,
            svq_ext_link = svq_playlist_data[curr_player][index].svq_ext_link || [],
            linkText = svq_ext_link['text'] || '',
            linkURL = svq_ext_link['url'] || '',
            svq_duration = svq_video[0].svq_length,
            svq_poster = ($.isEmptyObject(svq_playlist_data[curr_player][index].svq_poster)) ? '' : svq_playlist_data[curr_player][index].svq_poster,
            svq_subs = svq_playlist_data[curr_player][index].svq_subs || [];

            //remove old sources and subtitles from DOM
            $(t.node).removeAttr('src').find('source, track').remove();
            //add new sources to DOM
            for (var i = 0; i < svq_video.length; i++) {
                    $(t.node).append('<source src="' + svq_video[i].svq_url + '" title="' + ($.isEmptyObject(svq_video[i].svq_label) ? '' : svq_video[i].svq_label) + '" type="' + svq_video[i].svq_mime + '" data-order="' + ($.isEmptyObject(svq_video[i].svq_order) ? '' : svq_video[i].svq_order)  + '"></source>');
            }
            for (var i = 0; i < svq_subs.length; i++) {
                $(t.node).append('<track srclang="' + svq_subs[i].svq_lang + '" kind="subtitles" label="' + svq_subs[i].svq_label + '" src="' + svq_subs[i].svq_src + '">');
            }
            player.cleartracks(player);
            player.rebuildtracks();
            //scroll to player
            $('html, body').animate({scrollTop: t.container.offset().top}, 'fast');

            //set new poster image
            $(t.node).attr('poster', svq_poster);
            t.layers.find('.mejs-poster').css('background-image', 'url("' + svq_poster + '")').find('img').attr('src', svq_poster);
            
            // refresh text in info-overlay
            if (t.svq_infooverlay){
                var title = t.svq_infooverlay.find('div.svq-info-title');
                var title2 = t.svq_infooverlay.find('div.svq-info-title2');
                var title3 = t.svq_infooverlay.find('div.svq-info-title3');
                var extLink = t.svq_infooverlay.find('div.svq-info-ext-link');
                var duration = t.svq_infooverlay.find('div.svq-info-duration');
                ( ! $.isEmptyObject(svq_title) ? title.text(svq_title).css('display', 'block') : title.text('').css('display', 'none') );
                ( ! $.isEmptyObject(svq_title2) ? title2.text(svq_title2).css('display', 'block') : title2.text('').css('display', 'none') );
                ( ! $.isEmptyObject(svq_title3) ? title3.text(svq_title3).css('display', 'block') : title3.text('').css('display', 'none') );
                ( linkURL !== "" ? extLink.css('display', 'block').find('a').attr('href', linkURL).text( (linkText ? linkText : linkURL) ) : extLink.css('display', 'none').find('a').text('').attr('href', '') );
                duration.text(svq_duration).css('display', ($.isEmptyObject(svq_duration) ? 'none' : 'block'));
                ( ! $.isEmptyObject(svq_duration) ? duration.text(svq_duration).css('display', 'block') : duration.text('').css('display', 'none') );
            }
            //load and play video
            media.load();
            media.play();
            //add sources to the quality switch field
            t.refresh_source_list(player, media, index);
        });
    }
    });
})(mejs.$);