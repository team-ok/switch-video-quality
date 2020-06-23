// Source Chooser Plugin
(function($) {
    $.extend(mejs.MepDefaults, {
        sourcechooserText: _svqSettings.svq_switch_hover
    });
    $.extend(MediaElementPlayer.prototype, {
        buildsourcechooser: function(player, controls, layers, media) {
            var t = this;

            if (!$(t.node).hasClass('svq')){
                return false;
            }

            player.sourcechooserButton =
                $('<div class="mejs-button mejs-sourcechooser-button">' +
                    '<button type="button" aria-controls="' + t.id + '" title="' + t.options.sourcechooserText + '"></button>' +
                    '<div class="mejs-sourcechooser-selector">' +
                    '<ul>' +
                    '</ul>' +
                    '</div>' +
                    '</div>')
                .appendTo(t.controls)

            // hover
            .hover(function() {
                $(this).find('.mejs-sourcechooser-selector').css('visibility', 'visible');
            }, function() {
                $(this).find('.mejs-sourcechooser-selector').css('visibility', 'hidden');
            })

            // handle clicks to the radio buttons
            .on('click', 'input[type=radio]', function() {
                src = this.value;
                if (media.currentSrc != src) {
                    currentTime = media.currentTime;
                    paused = media.paused;
                    media.setSrc(src);
                    media.load();
                    // setCurrentTime only works when the video's metadata is present
                    $(media).one('loadedmetadata', function() {
                        media.setCurrentTime(currentTime);
                        if (!paused) {
                            media.play();
                        }
                    });
                }
            });
            $(t.node).on('loadedmetadata', function(){
                var currentQuality = this.videoHeight;
                var buttonClass = 'mejs-button mejs-sourcechooser-button';
                if (currentQuality >= 2160){
                    buttonClass += ' uhd';
                } else if (currentQuality >= 720){
                        buttonClass += ' hd';
                }
                player.sourcechooserButton.removeClass().addClass(buttonClass);
            });
        // add to list at pageload     
        t.refresh_source_list(player, media, 0);
    },
            refresh_source_list: function(player, media, index) {
            var t = player;
            var curr_player = $('video.svq').index(t.node);
            var svq_video = svq_playlist_data[curr_player][index].svq_video;
            
            t.sourcechooserButton.find('ul').empty();

            if (svq_video.length <= 0) {
            	return;
            }
            $(media).one('loadedmetadata', function() {
                var curry = media.currentSrc;
                var playable;
                //sort labels
                svq_video.sort(function compare(a,b) {
                    if (svq_options[curr_player].svq_sort_qualities == 'asc'){
                        return parseInt(b.svq_order, 10) - parseInt(a.svq_order, 10);
                    } else {
                        return parseInt(a.svq_order, 10) - parseInt(b.svq_order, 10);
                    }
                });
                
                for (i in svq_video) {
                    var src = svq_video[i];
                    if (src.svq_mime !== undefined && typeof media.canPlayType === 'function') {
                    //add sources of the first playable type only
                        if (playable !== undefined && src.svq_mime !== playable) {
                        continue;
                        }
                        t.addSourceButton(src.svq_url, src.svq_label, src.svq_order, curry == src.svq_url, i);
                        playable = src.svq_mime;
                    }
                }
            });
    },
            addSourceButton: function(src, label, height, isCurrent, index) {
                var t = this;
                if (label === '') {
                    label = src.split('/').pop().split('.')[0];
                }
                var flag = '';
                if (height >= 720) {
                    flag = '<sup>HD</sup>';
                    if (height >= 2160) {
                        flag = '<sup>4K</sup>';
                    }
                }
                t.sourcechooserButton.find('ul').append(
                    $('<li>' +
                        '<input type="radio" name="' + t.id + '_sourcechooser" id="' + t.id + '_sourcechooser_' + label + '_' + index + '" value="' + src + '" ' + (isCurrent ? 'checked="checked"' : '') + ' />' +
                        '<label for="' + t.id + '_sourcechooser_' + label + '_' + index + '">' + label + flag + '</label>' +
                        '</li>')
                );
                t.adjustSourcechooserBox();
            },
            adjustSourcechooserBox: function() {
                var t = this;
                // adjust the size of the outer box
                t.sourcechooserButton.find('.mejs-sourcechooser-selector').height(
                    t.sourcechooserButton.find('.mejs-sourcechooser-selector ul').outerHeight(true)
                );
            }
    });
})(mejs.$);