$(function () {

    $('.js-open-video').magnificPopup({
        type: 'iframe'
    });

    // JS-Scrolling Anchors
    jQuery('.js-scrollable-anchor').on('click', function (e) {
        e.preventDefault();

        jQuery('html,body').animate({
            scrollTop: jQuery(this.hash).offset().top
        }, 1000);
    });

    // Font Face Observing
    var font = new FontFaceObserver('Antonio-Regular', 'Antonio-Bold');

    font.load().then(function () {
        document.documentElement.className += " fonts-loaded";
    });

    // Form validation
    if ($('.validate').length) {
        $('.validate').parsley({
            excluded: 'input[type=button], input[type=submit], input[type=reset]',
            inputs: 'input, textarea, select, input[type=hidden], :hidden',
            errorClass: "parsley-error",
            classHandler: function (el) {
                return el.$element.closest('.form-group');
            }
        });
    }

    // Gallery
    $('.js-gallery').magnificPopup({
        delegate: 'a',
        type: 'image',
        gallery: {
            enabled: true
        }
    });
});