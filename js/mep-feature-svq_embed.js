(function($) {
    $.extend(MediaElementPlayer.prototype, {
    buildsvqembed: function(player, controls, layers, media) {
        var t = this;
        var curr_player = $('video.svq').index(t.node);
        var svqIndex = 0;
        
        if (!$(t.node).hasClass('svq') || !svq_options[curr_player].svq_embed_active) {
            return false;
        }
        player.svqEmbedLayer = $(
            '<div class="svq-embed-code-layer">' + 
            '<div class="svq-embed-code">' +
            '<label>' +
            '<span>' + _svqEmbedSettings.svq_embed_code_label + '</span>' +
            '<textarea title="' + _svqEmbedSettings.svq_embed_code_title + '" rows="4" spellcheck="false" readonly="true"></textarea>' +
            '</label>' +
            '<div class="svq_embed_custom_size">' +
            '<label>' + 
            '<span>' + _svqEmbedSettings.svq_embed_custom_size_label + '</span>' +
            '</label>' +
            '<form>' +
            '<input class="svq_embed_custom_width" type="text" maxlength="4">' +
            '&nbsp;x&nbsp;' + 
            '<input class="svq_embed_custom_height" type="text" maxlength="4">' +
            '</form>' +
            '</div>' +
            '</div>' + 
            '</div>'
        ).insertAfter(layers);
        player.svqEmbedToggle = $(
            '<div class="svq-embed-toggle">' +
            '<button id="' + t.id + '-embed" title="' + _svqEmbedSettings.svq_embed_title + '" aria-controls="' + t.id + '" type="button"></button>' +
            '</div>'
        ).insertAfter(player.svqEmbedLayer);

        //generate embed code at page load
        t.generateEmbedCode(t, curr_player, svqIndex);

        player.svqEmbedLayer
            .on('keydown', function(event){
                var key = event.which || event.keyCode;
                //close embed layer when escape key is pressed
                if (key === 27){
                    player.svqEmbedToggle.find('button').trigger('click');
                }
                //prevent keyboard events from bubbling to mejs keyboard control listener
                event.stopPropagation();
            })
            // refresh embed code when custom size is set
            .find('input').on('change', function(){
                //remove non-numeric characters
                var customSize = $(this).val().replace(/\D/g, '');
                var autoSize = 0;
                if ( $(this).hasClass('svq_embed_custom_width') ){
                    autoSize = Math.round(customSize * 9 / 16);
                    t.generateEmbedCode(t, curr_player, svqIndex, customSize, autoSize);
                } else if ( $(this).hasClass('svq_embed_custom_height') ){
                    autoSize = Math.round(customSize * 16 / 9);
                    t.generateEmbedCode(t, curr_player, svqIndex, autoSize, customSize);
                }
                $(this).siblings().val(autoSize);
            });
        // refresh embed code when playlist item is clicked
        var wrapper = $(t.container).closest('div.wp-video');
        wrapper.find('li.svq_playlist-item').on('click', function() {
            svqIndex = $(this).siblings().addBack().index(this);
            t.generateEmbedCode(t, curr_player, svqIndex);
            player.svqEmbedLayer.find('input').val('');
            if ( player.svqEmbedToggle.hasClass('active') ){
                player.svqEmbedToggle.find('button').trigger('click');
            }
        });
        player.container.hover(
            function() {
                if (!player.isFullScreen){
                    player.svqEmbedToggle.css('visibility', 'visible');
                }
            },
            function() {
                player.svqEmbedToggle.css('visibility', 'hidden');
            }
        );
        $(media).on('playing', function(){
            player.svqEmbedToggle.css('visibility', 'hidden');
        });
        $(document).on('webkitfullscreenchange mozfullscreenchange fullscreenchange MSFullscreenChange', function() {
            if (player.isFullScreen) {
                player.svqEmbedToggle.css('visibility', 'hidden');
                if ( player.svqEmbedToggle.hasClass('active') ){
                    player.svqEmbedToggle.find('button').trigger('click');
                }
            }
        });
        player.svqEmbedToggle.find('button').click(function(){
            var title = ( $(this).parent().hasClass('active') ? _svqEmbedSettings.svq_embed_title : _svqEmbedSettings.svq_close_embed_title );
            player.svqEmbedLayer.slideToggle('fast').find('textarea').select();
            $(this).attr('title', title).parent().toggleClass('active');
        });
    }, generateEmbedCode: function(player, svqInstance, svqIndex, svqWidth, svqHeight){
        if (!svqWidth || !svqHeight){
            svqWidth = $(player.media).width();
            svqHeight = $(player.media).height();
        }
        var svqEmbedCode = '<iframe src="' + svq_embed_urls[svqInstance][svqIndex] + '&svq_width=' + svqWidth + '" width="' + svqWidth + '" height="' + svqHeight + '" frameborder="0" allowfullscreen></iframe>';
        player.svqEmbedLayer.find('textarea').val(svqEmbedCode).select();
    }
    });
})(mejs.$);