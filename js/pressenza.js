jQuery(document).ready(function ($) {

    // Make Youtube and Vimeo responsive
    // http://css-tricks.com/NetMag/FluidWidthVideo/Article-FluidWidthVideo.php
    var $allVideos = $("iframe[src^='//player.vimeo.com'], iframe[src^='https://www.youtube.com'], iframe[src^='http://www.youtube.com']"),
        $fluidEl = $(".article-view-content");
    $allVideos.each(function () {
        $(this).data('aspectRatio', this.height / this.width)
            .removeAttr('height')
            .removeAttr('width');
    });
    $(window).resize(function () {
        var newWidth = $fluidEl.width();
        $allVideos.each(function () {
            var $el = $(this);
            $el
                .width(newWidth)
                .height(newWidth * $el.data('aspectRatio'));
        });
    }).resize();

    $(".wp-caption").removeAttr('style');

    // Newsletter subscription
    function isEmail(email) {
        var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return regex.test(email);
    }

    $("#nlmail").focus(function () {
        $("#nlconsent").show(200);
    });

    $("#nlreg").submit(function (event) {
        event.preventDefault();
        var la = $('#nllang').val();
        var em = $('#nlmail').val();

        if ($('#doconsent').prop('checked')) {
            if (isEmail(em)) {
                var jqxhr = $.ajax({
                    url: "https://www.pressenza.net/sub.php?la=" + la + "&em=" + em,
                    type: "GET"
                })
                    .done(function (responseData, textStatus, jqXHR) {
                        $('#nlinfo').html(responseData);
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        $('#nlinfo').html('Error. Please try again');
                    });
            } else {
                $('#nlinfo').html($('#nlmail').data('error'));
            }
        } else {
            $("#nlconsent").show(200);
            $('#nlinfo').html($('#doconsent').data('error'));
        }
    });

});