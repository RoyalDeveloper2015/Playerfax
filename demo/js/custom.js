$('document').ready(function () {
    
    $('.alertMsg .alert-close').each(function () {
        $(this).click(function (e) {
            e.preventDefault();
            $(this).parent().fadeOut("slow", function () {
                $(this).addClass('hidden');
            });
        });
    });

    /**** Hide Show Search Box Start *****/
    $(".search").click(function (e) {
        $(".search_box").show();
        e.stopPropagation();
    });
    $(document).click(function (evnt) {
        $(".search_box").hide();
        $('.search').removeClass('open');
    });
    $('.search_box').click(function (evnt1) {
        $(".search").show();
        evnt1.preventDefault();
        evnt1.stopPropagation();
    });
    $('.search').click(function (event) {
        if ($(this).hasClass('open')) {
            $(this).removeClass('open');
            $(".search_box").hide();
        } else {
            $(this).addClass('open');
            $(".search").show();
            event.preventDefault();
        }
    });

    $('.add_player').click(function () {
        if ($(this).hasClass('open')) {
            $(this).removeClass('open');
            $('#add_player_search').slideUp('slow').removeClass('open');
        } else {
            $(this).addClass('open');
            $('#add_player_search').slideDown('slow').addClass('open');
            $('#create_player').slideUp('slow').addClass('open');
        }
    });
    $('.create_player').click(function () {
        $('.add_player').removeClass('open');
        $('.add_players').slideUp('slow').removeClass('open');
        $('#create_player').slideDown('slow').addClass('open');
    });
    /***datepicker js****/
    $('.date').datepicker({
        dateFormat: 'dd-mm-yy'
    });
    /***accordion active js****/
    $('#accordion .panel-heading').click(function () {
        if (!$(this).hasClass('active')) {
            // the element clicked was not active, but now is - 
            $('.panel-heading').removeClass('active');
            $(this).addClass('active');
            setIconOpened(this);
        }
        else {
            // active panel was reclicked
            if ($(this).hasClass('opened')) {
                setIconOpened(null);
            }
            else {
                setIconOpened(this);
            }
        }
    });

    /*** tab accordion active js****/
    $('.myTab').tabCollapse();
    $(window).resize(function (e) {
        $('.myTab').tabCollapse();
    });

    var model_width = $('.container').innerWidth();
    $('.modal-dialog').width(model_width);

    $(window).on("load resize", function () {
        var model_width = $('.container').innerWidth();
        $('.modal-dialog').width(model_width);
    });

    $('.play_overlay').on('click', function (ev) {
        $(this).hide();
        $(this).closest('.wall_img').find('iframe')[0].src += "?autoplay=1";
        ev.preventDefault();
    });
});
function setIconOpened(activePanel) {
    $('.panel-heading').addClass('closed').removeClass('opened');

    if (activePanel) {
        $(activePanel).addClass('opened').removeClass('closed');
    }
}
$(window).on("load resize", function () {
    var before_login = $('.before_login').outerHeight();
    if ($(window).width() >= 768) {
        $('.home_slider').css('margin-top', before_login);
        $('.before_login a[href*="#"]:not([href="#"])').click(function () {
            if (location.pathname.replace(/^\//, '') === this.pathname.replace(/^\//, '') && location.hostname === this.hostname) {
                var target = $(this.hash);
                target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
                if (target.length) {
                    $('html, body').animate({
                        scrollTop: target.offset().top - before_login
                    }, 'slow');
                    return false;
                }
            }
        });
    }
    else {
        $('.home_slider').css('margin-top', 0);
        $('.before_login a[href*="#"]:not([href="#"])').click(function () {
            if (location.pathname.replace(/^\//, '') === this.pathname.replace(/^\//, '') && location.hostname === this.hostname) {
                var target = $(this.hash);
                target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
                if (target.length) {
                    $('html, body').animate({
                        scrollTop: target.offset().top
                    }, 1000);
                    return;
                }
            }
        });
    }
});
$(".header_bottom .navbar-nav li a").click(function () {
    $(".header_bottom .navbar-collapse").toggleClass('in');
});
$(window).load(function () {
    $('.home_slider .flexslider').flexslider({
        animation: "slide"
    });
    $('.testimonial_slider.flexslider').flexslider({
        animation: "slide",
        controlNav: false
    });
    /***upload file js****/
//    document.getElementById("uploadBtn").onchange = function (e) {
//        e.preventDefault();
//        document.getElementById("uploadFile").value = this.value;
//    };
//    document.getElementById("uploadBtn1").onchange = function () {
//        document.getElementById("uploadFile1").value = this.value;
//    };
});