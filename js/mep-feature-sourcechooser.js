// Source Chooser Plugin
(function($) {
    $.extend(mejs.MepDefaults, {
        sourcechooserText: _wpmejsSettings.svq_switch_hover
    });
    $.extend(MediaElementPlayer.prototype, {
        buildsourcechooser: function(player, controls, layers, media) {
            var t = this;
            if (!$(media).hasClass('svq')){
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
            .delegate('input[type=radio]', 'click', function() {
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
        // add to list at pageload     
        t.refresh_source_list();
    },
            refresh_source_list: function() {
            var t = this;
            var curr_player = $('video.svq').index($(t.media));
            t.sourcechooserButton.find('ul').empty();
            //get sources from DOM
            var sources = [].slice.call(t.media.children);
            $(t.media).one('loadedmetadata', function() {
                var curry = t.media.currentSrc;
                var playable;
                //sort labels
                sources.sort(function compare(a,b) {
                    if (svq_options[curr_player].svq_sort_qualities == 'asc'){
                        return parseInt(b.dataset.order, 10) - parseInt(a.dataset.order, 10);
                    } else {
                        return parseInt(a.dataset.order, 10) - parseInt(b.dataset.order, 10);
                    }
                });
                
                for (i in sources) {
                    src = sources[i];
                        if (src.nodeName === 'SOURCE' && t.media.canPlayType(src.type)) {
                        //add sources of the first playable type only
                            if (playable !== undefined && src.type !== playable) {
                            continue;
                            }
                            t.addSourceButton(src.src, src.title, src.dataset.order, curry == src.src);
                            playable = src.type;
                        }
                }
            });
    },
            addSourceButton: function(src, label, height, isCurrent) {
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
                        '<input type="radio" name="' + t.id + '_sourcechooser" id="' + t.id + '_sourcechooser_' + label + '" value="' + src + '" ' + (isCurrent ? 'checked="checked"' : '') + ' />' +
                        '<label for="' + t.id + '_sourcechooser_' + label + '">' + label + flag + '</label>' +
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