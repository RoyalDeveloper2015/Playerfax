$('document').ready(function () {
    
    //toggle menu js
    $('.safe_menu_button').click(function (){        
        if($(this).hasClass('safe_open')){
            $(this).removeClass('safe_open');
            $('.user_menu').slideUp('slow');
        }else{
            $(this).addClass('safe_open');
            $('.user_menu').slideDown('slow');
        }
    });
    $('.alertMsg .alert-close').each(function () {
        $(this).click(function (e) {
            e.preventDefault();
            $(this).parent().fadeOut("slow", function () {
                $(this).addClass('hidden');                
            });
        });
    });
    $(window).on('load resize', function (){
        if($(window).width() <= 767){
            var fixed_header_height = $('.user_home .header_mobile').outerHeight();
            var fixed_footer_height = $('.user_home .bottom_navigation').outerHeight();
            $('.user_profile_main').css('padding-top', fixed_header_height + 30);
            $('.user_profile_main').css('margin-bottom', fixed_footer_height - 30); 
        }            
        $('.bottom_navigation a[href*="#"]:not([href="#"])').click(function() {
            if (location.pathname.replace(/^\//,'') === this.pathname.replace(/^\//,'') && location.hostname === this.hostname) {
                var target = $(this.hash);
                target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
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

    $('.add_player').click(function (){
        if($(this).hasClass('open')){
            $(this).removeClass('open');
            $('#add_player_search').slideUp('slow').removeClass('open');
        }else{
            $(this).addClass('open');
            $('#add_player_search').slideDown('slow').addClass('open');
            $('#create_player').slideUp('slow').addClass('open');
        }                
    });
    $('.create_player').click(function (){   
        $('.add_player').removeClass('open');
        $('.add_players').slideUp('slow').removeClass('open');
        $('#create_player').slideDown('slow').addClass('open');                
    });    
    $('.find_player').click(function (){
        if($(this).hasClass('open')){
            $(this).removeClass('open');
            $('#add_create_player').fadeOut('slow').removeClass('open');
        }else{
            $(this).addClass('open');
            $('#add_create_player').fadeIn('slow').addClass('open');
        }        
    });
    $('.close_pop i').click(function (){
        if($('.find_player').hasClass('open')){
            $('.find_player').removeClass('open');
            $('#add_create_player').fadeOut('slow').removeClass('open');
        }
    });
    /***datepicker js****/
    $('.date').datepicker({
        dateFormat: 'dd-mm-yy'
    });
     /***accordion active js****/
    $('#accordion .panel-heading,#accordion1 .panel-heading').click(function () {
        if (!$(this).hasClass('active'))
        {
            // the element clicked was not active, but now is - 
            $('.panel-heading').removeClass('active');
            $(this).addClass('active');
            setIconOpened(this);
        }
        else
        {
            // active panel was reclicked
            if ($(this).hasClass('opened'))
            {
                setIconOpened(null);
            }
            else
            {
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
        $(this).closest('.wall_img').find('iframe')[0].src+= "?autoplay=1";
        ev.preventDefault();
    });
});
function setIconOpened(activePanel) {
    $('.panel-heading').addClass('closed').removeClass('opened');

    if (activePanel){
        $(activePanel).addClass('opened').removeClass('closed');
    }
}
$(window).on("load resize", function () {
    var before_login = $('.before_login').outerHeight();
    if ($(window).width() >= 768) {
        $('.home_slider').css('margin-top', before_login);
        $('.before_login a[href*="#"]:not([href="#"])').click(function() {
            if (location.pathname.replace(/^\//,'') === this.pathname.replace(/^\//,'') && location.hostname === this.hostname) {
                var target = $(this.hash);
                target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
                if (target.length) {
                    $('html, body').animate({
                    scrollTop: target.offset().top - before_login}, 'slow');
                    return false;
                }
            }
        });
    }
    else{
        $('.home_slider').css('margin-top', 0);
        $('.before_login a[href*="#"]:not([href="#"])').click(function() {
            if (location.pathname.replace(/^\//,'') === this.pathname.replace(/^\//,'') && location.hostname === this.hostname) {
                var target = $(this.hash);
                target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
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
$(".header_bottom .navbar-nav li a").click(function() {
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
$("#myModal4 .checkbox_section .checkbox input").click(function(){
    var group = "#myModal4 .checkbox_section .checkbox input[name='"+$(this).attr("name")+"']";
    $(group).attr("checked",false);
    $(group).next().removeClass('c_check');
    $(this).attr("checked",true);
    $(this).next().addClass('c_check');
});

if($(window).width() > 768){
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
if($(window).width() <= 767){
    $('.user_menu li a.events').click(function () {
        if($(this).hasClass('evnt_open')){
           $(this).removeClass('evnt_open'); 
           $('.sub_menu').slideUp('slow');
        }else{
            $(this).addClass('evnt_open'); 
           $('.sub_menu').slideDown('slow'); 
        }
    });
}

$('.green_btn, .grey_bg_btn, .add_event').click(function(e){
    e.preventDefault();
});
$('.blue_bg_btn, .grey_bg_btn, .add_event').click(function(){
    $('body').css('overflow','hidden');
    $("#myModal21, #myModal22, #myModal23, #myModal24").css('overflow','auto');
});
$('#myModal23 .blue_bg_btn, #myModal24 .blue_bg_btn, #myModal23 .grey_bg_btn, #myModal24 .grey_bg_btn').click(function(){
    $('body').css('overflow','auto');    
});

$(".add_row").click(function(){
    var row=$(".add_table_row.textbox_row:last").clone();
    $(row).find("input").each(function(i,item){
        $(item).val("");
    });
    
    $(".add_table").append(row);       
});

$('.tag_close').click(function(){
    $(this).parent().hide();
});
