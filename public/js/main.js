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

  // init carrossel
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
// init inputmast
  $(":input").inputmask();
  // handle symfony form submit

  $(".fetch-form").on("submit", async (e) => {
    e.preventDefault();
    let body = new FormData(e.target);
    let action = $(e.target).attr("action");
    let method = $(e.target).attr("method");
    let sucess_message = $(e.target).attr("success_message");

    const response = await fetch(action, {
      body: body,
      method: method,
      mode: "cors",
      cache: "default",
    });

    if (response.status === 200) {
      toastr.success("Obrigado! " + sucess_message)
    } else {
      toastr.error("Ocorreu um erro ao enviar seus dados, por favor envie um email para contato@pontonet.site")
    }
  })
  // handle selected plan
  $("*[data-selected-plan]").on("click", (e) => {
    $("#landing_page_lead_name").trigger("click");
    const selected_value = $(e.target).attr("data-selected-plan")
    $("#landing_page_lead_selectedPlan").val(selected_value).change();
  })
})(jQuery);
