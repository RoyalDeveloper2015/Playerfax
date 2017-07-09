var $userProfileImage, canvasData, cropBoxData;

$('document').ready(function () {

    $userProfileImage = $('#user_img_container img.user_img_profile_picture');

    $('#user_img_modal').on('shown.bs.modal', function (e) {
        var referrer = $(e.relatedTarget);
        var view = referrer.data('view');
        if (view === 'mobile') {
            if (typeof $userProfileImage !== 'undefined') {
                $userProfileImage.cropper('destroy');
            }
        } else {
            $('#cropper').show();
            $userProfileImage.cropper({
                aspectRatio: 1 / 1,
                autoCropArea: 0.6,
                ready: function () {
                    var croppedCanvas = $userProfileImage.cropper('getCroppedCanvas');
                    var png = croppedCanvas.toDataURL("image/png");
                    $('#user_img_preview').attr('src', png);
                    $userProfileImage.cropper('setCanvasData', canvasData);
                    $userProfileImage.cropper('setCropBoxData', cropBoxData);
                }
            }).on('cropend', function () {
                var croppedCanvas = $userProfileImage.cropper('getCroppedCanvas');
                var png = croppedCanvas.toDataURL("image/png");
                $('#user_img_preview').attr('src', png);
                $userProfileImage.cropper('setCanvasData', canvasData);
                $userProfileImage.cropper('setCropBoxData', cropBoxData);
            }).on('zoom', function () {
                var croppedCanvas = $userProfileImage.cropper('getCroppedCanvas');
                var png = croppedCanvas.toDataURL("image/png");
                $('#user_img_preview').attr('src', png);
                $userProfileImage.cropper('setCanvasData', canvasData);
                $userProfileImage.cropper('setCropBoxData', cropBoxData);
            });
        }

    }).on('hidden.bs.modal', function () {
        $userProfileImage.cropper('destroy');
        canvasData = '';
        cropBoxData = '';
        $('#user_img_preview').attr('src', 'about:blank');
    });

    $('#user_img_crop').on('click', function () {
        var cropBoxData = $userProfileImage.cropper('getCropBoxData');
        var croppedCanvas = $userProfileImage.cropper('getCroppedCanvas');
        var png = croppedCanvas.toDataURL("image/png");
        var imageData = png.replace(/^data:image\/(png|jpeg);base64,/, "");
        var token = $userProfileImage.data('token');
        var data = new FormData();
        data.append('Filedata', imageData);
        data.append('token', token);

        $.ajax({
            url: 'index.php?action=editUserPicture',
            data: data,
            dataType: 'json',
            processData: false,
            contentType: false,
            type: 'POST',
            success: function (json) {
                if (typeof json.success !== 'undefined') {
                    $('#user_img_message').html(json.success);

                    $userProfileImage.cropper('destroy');
                    canvasData = '';
                    cropBoxData = '';
                    $('#user_img_preview').attr('src', 'about:blank');

                    $('.user_img_profile_picture').attr('src', json.url);
                    $userProfileImage.cropper({
                        aspectRatio: 1 / 1,
                        autoCropArea: 0.6,
                        ready: function () {
                            var croppedCanvas = $userProfileImage.cropper('getCroppedCanvas');
                            var png = croppedCanvas.toDataURL("image/png");
                            $('#user_img_preview').attr('src', png);
                            $userProfileImage.cropper('setCanvasData', canvasData);
                            $userProfileImage.cropper('setCropBoxData', cropBoxData);
                        }
                    }).on('cropend', function () {
                        var croppedCanvas = $userProfileImage.cropper('getCroppedCanvas');
                        var png = croppedCanvas.toDataURL("image/png");
                        $('#user_img_preview').attr('src', png);
                        $userProfileImage.cropper('setCanvasData', canvasData);
                        $userProfileImage.cropper('setCropBoxData', cropBoxData);
                    }).on('zoom', function () {
                        var croppedCanvas = $userProfileImage.cropper('getCroppedCanvas');
                        var png = croppedCanvas.toDataURL("image/png");
                        $('#user_img_preview').attr('src', png);
                        $userProfileImage.cropper('setCanvasData', canvasData);
                        $userProfileImage.cropper('setCropBoxData', cropBoxData);
                    });
                }

                if (typeof json.error !== 'undefined') {
                    $('#user_img_message').html(json.error);
                }
            }
        });

    });

    $('#user_img_input').on('change', function () {
        var token = $(this).data('token');
        var file = this.files[0];
        // check file extension
        var ext = file.name.substring(file.name.lastIndexOf('.') + 1).toLowerCase();
        if (!(/(gif|jpe?g|png)$/i).test(ext)) {
            alert('You must select an image file only');
            return false;
        }
        // check file size
        if (file.size > 20971520) {
            alert('Max upload size is 20 MB');
            return false;
        } else {
            $('#user_img_link').hide();
            // upload new file
            var data = new FormData();
            data.append('Filedata', file);
            data.append('token', token);
            $.ajax({
                url: 'index.php?action=editUserPicture',
                data: data,
                dataType: 'json',
                processData: false,
                contentType: false,
                type: 'POST',
                success: function (json) {
                    if (typeof json.success !== 'undefined') {
                        $('.user_img_profile_picture').attr('src', json.url);
                        $('#user_img_message').html(json.success);
                        if ($('#cropper').is(':visible')) {
                            $userProfileImage.cropper('destroy');
                            canvasData = '';
                            cropBoxData = '';
                            $('#user_img_preview').attr('src', 'about:blank');
                            $userProfileImage.cropper({
                                aspectRatio: 1 / 1,
                                autoCropArea: 0.6,
                                ready: function () {
                                    var croppedCanvas = $userProfileImage.cropper('getCroppedCanvas');
                                    var png = croppedCanvas.toDataURL("image/png");
                                    $('#user_img_preview').attr('src', png);
                                    $userProfileImage.cropper('setCanvasData', canvasData);
                                    $userProfileImage.cropper('setCropBoxData', cropBoxData);
                                }
                            }).on('cropend', function () {
                                var croppedCanvas = $userProfileImage.cropper('getCroppedCanvas');
                                var png = croppedCanvas.toDataURL("image/png");
                                $('#user_img_preview').attr('src', png);
                                $userProfileImage.cropper('setCanvasData', canvasData);
                                $userProfileImage.cropper('setCropBoxData', cropBoxData);
                            }).on('zoom', function () {
                                var croppedCanvas = $userProfileImage.cropper('getCroppedCanvas');
                                var png = croppedCanvas.toDataURL("image/png");
                                $('#user_img_preview').attr('src', png);
                                $userProfileImage.cropper('setCanvasData', canvasData);
                                $userProfileImage.cropper('setCropBoxData', cropBoxData);
                            });
                        }
                    }

                    if (typeof json.error !== 'undefined') {
                        $('#user_img_message').html(json.error);
                    }

                    $('#user_img_link').show();
                }
            });
        }
    });

    $('.img-circle').hover(
        function () {
            $(this).find('.picture-caption').removeClass("hide");
        }, function () {
            $(this).find('.picture-caption').addClass("hide");
        }
    );

    //toggle menu js
    $('.safe_menu_button').click(function () {
        if ($(this).hasClass('safe_open')) {
            $(this).removeClass('safe_open');
            $('.user_menu').slideUp('slow');
        } else {
            $(this).addClass('safe_open');
            $('.user_menu').slideDown('slow');
        }
    });
    /* changed this to use event delegation */
    $(document).on('click', '.alertMsg .alert-close', function (e) {
        e.preventDefault();
        $(this).parent().fadeOut("slow", function () {
            $(this).addClass('hidden');
        });
    });
    $(window).on('load resize', function () {
        if ($(window).width() <= 767) {
            var fixed_header_height = $('.user_home .header_mobile').outerHeight();
            var fixed_footer_height = $('.user_home .bottom_navigation').outerHeight();
            $('.user_profile_main').css('padding-top', fixed_header_height + 30);
            $('.user_profile_main').css('margin-bottom', fixed_footer_height - 30);
        } else {
            $('.user_profile_main').removeAttr("style");
        }
        $('.bottom_navigation a[href*="#"]:not([href="#"])').click(function () {
            if (location.pathname.replace(/^\//, '') === this.pathname.replace(/^\//, '') && location.hostname === this.hostname) {
                var target = $(this.hash);
                target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
                if (target.length) {
                    $('html, body').animate({
                        scrollTop: target.offset().top - fixed_header_height - 15
                    }, 500);
                    return false;
                }
            }
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
    $('.find_player').click(function () {
        if ($(this).hasClass('open')) {
            $(this).removeClass('open');
            //$('#add_create_player').fadeOut('slow').removeClass('open');
            $('#add_player_search_mobile').fadeOut('slow').removeClass('open');
        } else {
            $(this).addClass('open');
            $('#add_create_player').fadeIn('slow').addClass('open');
            $('#add_player_search_mobile').fadeIn('slow').addClass('open');
        }
    });

    if ($(window).width() < 768) {
        $('.create_player').click(function () {
            $('#create_player').fadeOut('slow').removeClass('open');
            $('#create_player1').fadeIn('slow').addClass('open');
        });
    }
    $('.close_pop i').click(function () {
        if ($('.find_player').hasClass('open')) {
            $('.find_player').removeClass('open');
            $('#add_player_search_mobile').fadeOut('slow').removeClass('open');
        }
        $('#create_player1').fadeOut('slow').removeClass('open');
        $('#add_create_player').fadeOut('slow').removeClass('open');

    });
    /***datepicker js****/
    $('.date').datepicker({
        todayHighlight: true,
        dateFormat: 'dd-mm-yy'
    });

    /***accordion active js****/
    $('.manage_accordion .panel-heading').click(function () {
        if (!$(this).hasClass('active')) {
            // the element clicked was not active, but now is -
            $(this).find('.panel-heading').removeClass('active');
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
    $(window).scroll(function () {
        if ($(window).scrollTop() > 1) {
            $('.bottom_navigation').css('visibility', 'visible');
        }
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
$("#myModal4 .checkbox_section .checkbox input").click(function () {
    var $box = $(this);
    if ($box.is(":checked")) {
        var group = "input:checkbox[name='" + $box.attr("name") + "']";
        $(group).prop("checked", false).removeClass('c_check');
        $box.prop("checked", true).addClass('c_check');
    } else {
        $box.prop("checked", false).removeClass('c_check');
    }
});

/*
 if ($(window).width() > 768) {
 $('.user_menu li a.events').hover(function () {
 $('.sub_menu').show();
 });
 $('.header_bottom, .sub_menu').mouseleave(function () {
 $('.sub_menu').hide();
 });
 $('.header_bottom a.add_profile').mouseover(function () {
 $('.sub_menu').hide();
 });
 }
 if ($(window).width() <= 767) {
 $('.user_menu ul li ').on("click", 'a.events', function (e) {
 e.preventDefault();
 e.stopPropagation();
 if ($(this).hasClass('evnt_open')) {
 $(this).removeClass('evnt_open');
 $('.sub_menu').slideUp('slow');
 } else {
 $(this).addClass('evnt_open');
 $('.sub_menu').slideDown('slow');
 }
 });
 }
 */
//$('.green_btn, .grey_bg_btn, .add_event').click(function(e){
//e.preventDefault();
//});
$('.blue_bg_btn, .grey_bg_btn, .add_event, .eve_event, .btn').click(function () {
    //$('body').css('overflow', 'hidden');
    //$(".modal").css('overflow', 'auto');
});
$('.modal .blue_bg_btn').click(function () {
    //$('body').css('overflow', 'auto');
});

$(document).on('click', 'a.add_row', function (e) {
    e.preventDefault();
    var $add_players_table = $(this).closest('form').find('.add_players_table');
    var $add_table = $add_players_table.find('.add_table');
    var row = $add_table.find(".add_table_row.textbox_row:last").clone();
    $(row).find("input").each(function (i, item) {
        $(item).val("");
    });

    $add_table.append(row);
    // initialize datepicker
    /*
     $(row).find('input[name="pDOB[]"]').datepicker({
     todayHighlight: true,
     dateFormat: 'dd-mm-yy'
     });
     */
});

$('.tag_close').click(function () {
    $(this).closest('.grey_tag_box').remove();
});
$('.close_pop').click(function () {
    $('body').removeAttr("style");
});


