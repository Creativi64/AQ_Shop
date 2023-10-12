(function (a) {
    a.fn.slideshow = function (p) {
        var p = p || {}, autoplay = p && p.autoplay ? p.autoplay : "enable",
            time_Interval = p && p.time_Interval ? p.time_Interval : "4000";
        time_Interval = parseInt(time_Interval);
        var g = a(this), current = -1, y = g.children(".slideshow-products").children("div").length, v, w;
        if (y == 0) {
            g.append("Require content");
            return null
        }
        ;
        if (autoplay == "enable") {
            play()
        } else {
            current = 0;
            showpic()
        }
        ;g.find(".slideshow-controls").children("li").hover(function () {
            var index = g.find(".slideshow-controls").children("li").index($(this));
            if (index != current) {
                current = index;
                showpic()
            }
        }, function () {
        });
        g.hover(function () {
            if (autoplay == "enable") v = setTimeout(play, time_Interval)
        })

        function showpic() {
            clearTimeout(v);
            g.find(".slideshow-products").find(".product").fadeOut(100);
            g.find(".slideshow-products").find(".product").eq(current).fadeIn(100);
            g.find(".slideshow-controls").children("li").eq(current).addClass("active");
            g.find(".slideshow-controls").children("li").eq(w).removeClass();
            w = current
        }

        function play() {
            current++;
            if (current >= y) current = 0;
            showpic();
            v = setTimeout(play, time_Interval)
        }
    }
})(jQuery);

function resizeSlideshow(sliderId)
{
    console.log('resizeSlideshow ' + sliderId);
    var teaserElement = jQuery('#slideshow_' + sliderId);
    if (teaserElement.length != 0) {
        try {
            var totalHeight = teaserElement.height();
            teaserElement.css({'max-height': totalHeight + 'px'});

            var count = teaserElement.find('.slideshow-controls li').length;
            var controlsHeight = $(teaserElement.find('.slideshow-controls')[0]).height();

            if (controlsHeight > totalHeight) {
                teaserElement.find('.slideshow-controls li p').css({ 'margin-top': 0, 'margin-bottom': 0});
                teaserElement.find('.slideshow-controls li').css({'padding-top': 0,'padding-top': 0});
                teaserElement.find('.slideshow-controls li').css({'height': totalHeight / count + 'px'});
                controlsHeight = $(teaserElement.find('.slideshow-controls')[0]).height();
            }

            if (controlsHeight < totalHeight) {
                var pad = (totalHeight - controlsHeight) / (count * 2);
                teaserElement.find('.slideshow-controls li').css({'padding-top': pad + 'px','padding-bottom': pad + 'px'});
            }
        }
        catch (ex) {
            console.log('#slideshow_{$data.slider_id} - resize =>', ex);
        }
    }
    else{
        console.log('resizeSlideshow ' + sliderId + ' not found');
    }

}


