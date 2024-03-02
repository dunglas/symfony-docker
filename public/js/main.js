(function ($) {
    "use strict";
    $(window).on('load', function () {
        $('body').addClass('loaded');
    });

    function headerHeight() {
        var height = $("#header").height();
        $('.header-height').css('height', height + 'px');
    }

    $(function () {
        var header = $("#header"), yOffset = 0, triggerPoint = 80;
        headerHeight();
        $(window).resize(headerHeight);
        $(window).on('scroll', function () {
            yOffset = $(window).scrollTop();
            if (yOffset >= triggerPoint) {
                header.addClass("navbar-fixed-top animated slideInDown");
            } else {
                header.removeClass("navbar-fixed-top animated slideInDown");
            }
        });
    });
    $('.menu-wrap ul.nav').slicknav({prependTo: '.header-section .navbar', label: '', allowParentLinks: true});
    $('.default-btn').on('mouseenter', function (e) {
        var parentOffset = $(this).offset(), relX = e.pageX - parentOffset.left, relY = e.pageY - parentOffset.top;
        $(this).find('span').css({top: relY, left: relX})
    }).on('mouseout', function (e) {
        var parentOffset = $(this).offset(), relX = e.pageX - parentOffset.left, relY = e.pageY - parentOffset.top;
        $(this).find('span').css({top: relY, left: relX})
    });
    smoothScroll.init({offset: 60});
    $(window).on('scroll', function () {
        if ($(this).scrollTop() > 100) {
            $('#scroll-to-top').fadeIn();
        } else {
            $('#scroll-to-top').fadeOut();
        }
    });
    new WOW().init();
    if ($('.subscribe_form').length > 0) {
        $('.subscribe_form').ajaxChimp({
            language: 'es',
            callback: mailchimpCallback,
            url: "//alexatheme.us14.list-manage.com/subscribe/post?u=48e55a88ece7641124b31a029&amp;id=361ec5b369"
        });
    }

    function mailchimpCallback(resp) {
        if (resp.result === 'success') {
            $('#subscribe-result').addClass('subs-result');
            $('.subscription-success').text(resp.msg).fadeIn();
            $('.subscription-error').fadeOut();
        } else if (resp.result === 'error') {
            $('#subscribe-result').addClass('subs-result');
            $('.subscription-error').text(resp.msg).fadeIn();
        }
    }

    $.ajaxChimp.translations.es = {
        'submit': 'Submitting...',
        0: 'We have sent you a confirmation email',
        1: 'Please enter your email',
        2: 'An email address must contain a single @',
        3: 'The domain portion of the email address is invalid (the portion after the @: )',
        4: 'The username portion of the email address is invalid (the portion before the @: )',
        5: 'This email address looks fake or invalid. Please enter a real email address'
    };

    const slider = tns({
        startIndex: 1,
        container: '.my-slider',
        items: 1,
        fixedWidth: 510,
        gutter: 20,
        controls: false,
        nav: true,
        navPosition: "bottom",
        navAsThumbnails: true,
        mouseDrag: true,
        autoplayButtonOutput: false,
        preventScrollOnTouch: "auto"
    });

    $(".slicknav_btn").click((e) => {
        e.preventDefault();
        $(".slicknav_nav").toggle();
    })

})(jQuery);
