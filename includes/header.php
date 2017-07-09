<header class="after_login header_desk">
    <div class="header_top">
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-sm-6">
                    <select class="form-control" id="searchPlayerQuery" style="width: 100%;">
                        <option></option>
                    </select>
                </div>
                <div class="col-md-3 col-sm-4">
                    <div class="header_login">
                        <ul>
                            <!--<li><a href="javascript:void(0);" class="search"></a></li>-->
                            <li><a href="index.php?page=home" class="register"><?php echo $userFullName; ?></a></li>
                            <li><a href="index.php?page=logout" class="login">Logout</a></li>
                        </ul>
                        <!--<div class="search_box">
                            <form>
                                <input type="text" class="input_box" placeholder="Search">
                                <input type="submit" class="serch_img" value="">
                            </form>
                        </div>-->
                    </div>
                </div>
                <div class="col-md-1 col-sm-2">
                    <div class="header_social">
                        <ul>
                            <li><a href="https://www.facebook.com/PlayerFax/" class="fb"></a></li>
                            <!--<li><a href="#" class="tweet"></a></li>
                            <li><a href="#" class="gplus"></a></li>
                            <li><a href="#" class="linkdin"></a></li>
                            <li><a href="#" class="ytube"></a></li>-->
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="header_bottom">
        <div class="container">
            <div class="row">
                <div class="col-sm-3">
                    <div class="logo">
                        <a href="index.php?page=home"><img src="images/playerlogo_img.png" alt="img" class="img-responsive" draggable="false"/></a>
                    </div>
                </div>
                <div class="col-sm-9">
                    <div class="user_menu">
                        <ul>
                            <li><a href="index.php?page=home" class="home">Home</a></li>
                            <li><a href="index.php?page=profile" class="profile">Settings</a></li>
                            <li><a href="index.php?page=my-players" class="my_player">My Athletes</a></li>
                            <!--<li><a href="#" class="add_profile">Create Player</a></li>-->
                            <li><a href="index.php?page=events" class="events">Events</a></li>
                        </ul>
                        <!--
                        <div class="sub_menu">
                            <div class="sub_menu_box">
                                <a href="https://playerfax.com/index.php?page=events" class="eve_event">Manage Events</a>
                                <a href="#" class="add_event" data-toggle="modal" data-target="#myModal21">Add event</a>
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<div class="header_mobile">
    <div class="safe_menu_button">
        <span></span>
    </div>
    <div class="col-sm-9">
        <div class="user_profile_img">
           <a href="#" data-dismiss="modal" data-toggle="modal" data-target="#user_img_modal" data-view="mobile"> <img class="user_img_profile_picture" src="<?php echo $userPicture; ?>" alt="<?php echo $userFullName; ?>"/></a>
        </div>
        <div class="serach_form">
            <input type="text" class="txt_box" placeholder="player id #, or name search"/>
        </div>
        <div class="user_menu">
            <ul>
                <li><a href="index.php?page=home" class="home">Home</a></li>
                <li><a href="index.php?page=profile" class="profile">Settings</a></li>
                <li><a href="index.php?page=my-players" class="my_player">My Athletes</a></li>
                <!--<li><a href="#" class="add_profile">Create Player</a></li>-->
                <!-- Add new button -->
                <li><a href="index.php?page=events" class="events">Events</a></li>
                <li><a href="index.php?page=logout" class="logout">Logout</a></li>
                <!-- End new Button -->
            </ul>
            <!-- Add new sub menu button
            <div class="sub_menu">
                <div class="sub_menu_box">
                    <a href="https://playerfax.com/index.php?page=events" class="eve_event">Manage Events</a>
                    <a href="index.php?page=events" class="add_event">Add event</a>
                </div>
            </div> -->
            <!-- End new sub menu button -->
        </div>
    </div>
</div>

<style>
    #user_img_container {
        width: 650px;
        max-width: 650px;
        height: auto;
        margin-bottom: 20px;
    }

    #user_img_container img.user_img_profile_picture {
        width: 100%;
        max-width: 100%;
        border: 1px solid #ddd;
    }

    #user_img_link {
        position: relative;
        transform: translate(-50%, -50%);
    }

    #user_img_link label {
        cursor: pointer;
        font-size: 18px;
    }

    #user_img_link input {
        height: 1px;
        width: 1px;
        opacity: 0.0; /* Standard: FF gt 1.5, Opera, Safari */
        filter: alpha(opacity=0); /* IE lt 8 */
        -ms-filter: "alpha(opacity=0)"; /* IE 8 */
        -khtml-opacity: 0.0; /* Safari 1.x */
        -moz-opacity: 0.0; /* FF lt 1.5, Netscape */
    }

    #user_img_link a {
        color: #fff;
        -webkit-font-smoothing: antialiased;
    }

    #user_img_preview {
        border: 1px solid #ddd;
        margin-bottom: 20px;
    }
</style>

<!-- Change User Picture -->
<div id="user_img_modal" class="modal forget_section" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="container">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="close_img">
                        <button type="button" class="close" data-dismiss="modal"></button>
                    </div>
                    <div class="title_h2">
                        <h2>Change Profile Picture</h2>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="user_img_message"></div>
                            <a href="#" id="user_img_link">
                                <label><input id="user_img_input" type="file" data-token="<?php echo $userToken; ?>" accept="image/jpg,image/png,image/jpeg,image/gif"/>Upload Picture</label>
                            </a>
                        </div>
                    </div>
                    <div id="cropper" style="display:none;">
                        <div class="row">
                            <div class="col-md-8">
                                <div id="user_img_container">
                                    <img class="user_img_profile_picture" src="<?php echo $userPicture; ?>" alt="<?php echo $userFullName; ?>" data-token="<?php echo $userToken; ?>"/>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <img id="user_img_preview" src="about:blank">
                            </div>
                        </div>

                        <button id="user_img_crop" type="button" class="btn btn-primary">Crop and Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Change User Picture -->