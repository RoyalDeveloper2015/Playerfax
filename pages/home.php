<?php

if (! (isset($_SESSION['userId']) || isset($_SESSION['facebook_access_token'] ) )) {
    header('Location: index.php?page=login');
    exit;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Playerfax</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1, maximum-scale=1">
    <link rel="apple-touch-icon" sizes="180x180" href="http://www.playerfax.com/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="http://www.playerfax.com/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="http://www.playerfax.com/favicon-16x16.png">
    <link rel="manifest" href="http://www.playerfax.com/manifest.json">
    <link rel="mask-icon" href="http://www.playerfax.com/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="theme-color" content="#123456">
    <link type="text/css" rel="stylesheet" href="css/bootstrap.min.css"/>
    <link type="text/css" rel="stylesheet" href="css/font-awesome.css"/>
    <link type="text/css" rel="stylesheet" href="css/flexslider.css"/>
    <link type="text/css" rel="stylesheet" href="css/bootstrap-datepicker.css"/>
    <link type="text/css" rel="stylesheet" href="css/bootstrap-datetimepicker.css"/>
    <link type="text/css" rel="stylesheet" href="css/select2.css"/>
    <link type="text/css" rel="stylesheet" href="css/select2-bootstrap.css"/>
    <link type="text/css" rel="stylesheet" href="js/DataTables-1.10.15/media/css/dataTables.bootstrap.min.css"/>
    <link type="text/css" rel="stylesheet" href="js/DataTables-1.10.15/extensions/Buttons/css/buttons.bootstrap.min.css"/>
    <link type="text/css" rel="stylesheet" href="css/dataTables.fontAwesome.css"/>
    <link type="text/css" rel="stylesheet" href="css/cropper-min.css"/>
    <link type="text/css" rel="stylesheet" href="css/style.css"/>
    <link type="text/css" rel="stylesheet" href="css/responsive.css"/>
    <link rel="stylesheet" type="text/css" href="css/addtohomescreen.css">
    <style>
        .upload-progress {
            display: block;
            text-align: center;
            width: 0;
            height: 3px;
            background: red;
            transition: width .3s;
            -webkit-transition: width .3s; /* Safari */
        }

        .upload-progress.hide {
            opacity: 0;
            transition: opacity 1.3s;
        }

        .wordwrap {
            white-space: pre-wrap; /* CSS3 */
            white-space: -moz-pre-wrap; /* Firefox */
            white-space: -pre-wrap; /* Opera <7 */
            white-space: -o-pre-wrap; /* Opera 7 */
            word-wrap: break-word; /* IE */
        }

        .mediaFile {
            height: auto;
            border: 1px solid #D2D2D2;
            padding: 10px;
            background-color: #fff;
            margin: 20px 10px;
        }

        .mediaFile p img {
            width: 300px;
        }

        .hidden {
            visibility: hidden;
            opacity: 0;
            -moz-transition: opacity 1s, visibility 1.3s;
            -webkit-transition: opacity 1s, visibility 1.3s;
            -o-transition: opacity 1s, visibility 1.3s;
            transition: opacity 1s, visibility 1.3s;
        }

        .shown {
            visibility: visible;
            opacity: 1;
            -moz-transition: opacity 1s, visibility 1.3s;
            -webkit-transition: opacity 1s, visibility 1.3s;
            -o-transition: opacity 1s, visibility 1.3s;
            transition: opacity 1s, visibility 1.3s;
        }

        .timeline_loader {
            left: 50%;
            margin: 0 auto;
            position: relative;
            top: 20px;
        }

        .dd-post {
            position: relative;
            float: right;
            padding-left: 20px;
            width: 20px;
            height: 20px;
            margin-left: -6px;
            margin-top: -10px;
            background: url(images/btn-down-arrow.png) 0 0 no-repeat;
        }

        .dd-comment {
            position: relative;
            float: right;
            padding-left: 20px;
            width: 20px;
            height: 20px;
            margin-left: -6px;
            margin-top: -10px;
            background: url(images/btn-pencil-edit.png) 0 0 no-repeat;
        }

        .dd-menu-post, .dd-menu-media, .dd-menu-post-comment, .dd-menu-media-comment {
            display: none;
            position: absolute;
            background-clip: padding-box;
            background-color: #fff;
            border: 1px solid rgba(0, 0, 0, 0.15);
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.3);
            border-bottom-right-radius: 0;
            color: #1d2129;
            max-width: 220px;
            width: 100%;
            z-index: 9999;
        }

        .dd-menu-post ul, .dd-menu-media ul, .dd-menu-post-comment ul, .dd-menu-media-comment ul {
            padding: 5px 0;
            list-style-type: none;
            margin: 0;
        }

        .dd-menu-post li, .dd-menu-media li, .dd-menu-post-comment li, .dd-menu-media-comment li {
            overflow: hidden;
            white-space: nowrap;
        }

        .dd-menu-post a, .dd-menu-media a, .dd-menu-post-comment a, .dd-menu-media-comment a {
            cursor: pointer;
            display: block;
            outline: medium none;
            text-decoration: none;
            -moz-border-bottom-colors: none;
            -moz-border-left-colors: none;
            -moz-border-right-colors: none;
            -moz-border-top-colors: none;
            border-color: #fff;
            border-image: none;
            border-style: solid;
            border-width: 1px 0;
            color: #1d2129;
            font-size: 12px;
            font-weight: normal;
            line-height: 22px;
            padding: 0 12px;
        }

        .dd-menu-post a:hover, .dd-menu-media a:hover, .dd-menu-post-comment a:hover, .dd-menu-media-comment a:hover, .dd-menu-post a:active, .dd-menu-media a:active, .dd-menu-post-comment a:active, .dd-menu-media-comment a:active, .dd-menu-post a:focus, .dd-menu-media a:focus, .dd-menu-post-comment a:focus, .dd-menu-media-comment a:focus {
            background-color: #005ca6;
            border-color: #005ca6;
            color: #fff;
        }

        .dd-menu-post i, .dd-menu-media i, .dd-menu-post-comment i, .dd-menu-media-comment i {
            background-size: 18px 36px;
            display: inline-block;
            position: absolute;
            top: 4px;
            left: 2px;
            font-size: 16px !important;
        }

        .dd-item {
            max-width: 300px;
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            padding-bottom: 3px;
            padding-top: 3px;
            position: relative;
        }

        .dd-item-action {
            padding-left: 24px;
        }

        .dd-separator {
            border-bottom: 1px solid #e9ebee;
            margin: 5px 7px 6px;
            padding-top: 1px;
        }

        .dd-item-title {
            font-size: 14px;
            font-weight: bold;
            line-height: 18px;
            white-space: normal;
        }

        .dd-item-desc {
            font-size: 11px;
            font-weight: normal;
            line-height: 16px;
            white-space: normal;
        }

        .btn-gray {
            color: #ffffff;
            background-color: #878787;
            border-color: #949494;
        }

        .btn-gray:hover,
        .btn-gray:focus,
        .btn-gray:active,
        .btn-gray.active,
        .open .dropdown-toggle.btn-gray {
            color: #ffffff;
            background-color: #6E6E6E;
            border-color: #949494;
        }

        .btn-gray:active,
        .btn-gray.active,
        .open .dropdown-toggle.btn-gray {
            background-image: none;
        }

        .btn-gray.disabled,
        .btn-gray[disabled],
        fieldset[disabled] .btn-gray,
        .btn-gray.disabled:hover,
        .btn-gray[disabled]:hover,
        fieldset[disabled] .btn-gray:hover,
        .btn-gray.disabled:focus,
        .btn-gray[disabled]:focus,
        fieldset[disabled] .btn-gray:focus,
        .btn-gray.disabled:active,
        .btn-gray[disabled]:active,
        fieldset[disabled] .btn-gray:active,
        .btn-gray.disabled.active,
        .btn-gray[disabled].active,
        fieldset[disabled] .btn-gray.active {
            background-color: #878787;
            border-color: #949494;
        }

        .btn-gray .badge {
            color: #878787;
            background-color: #ffffff;
        }
    </style>
    <script src="//content.jwplatform.com/libraries/OWJFiyrv.js"></script>
</head>
<body class="user_home" id="portrait">
<?php require 'includes/header.php'; ?>
<section class="user_profile_main">
    <div class="container">
        <div class="row">
            <div class="col-sm-2">
                <div class="img_preview">
                    <div class="img-circle">
                        <img class="user_img_profile_picture" src="<?php echo $userPicture; ?>" alt="<?php echo $userFullName; ?>" data-token="<?php echo $userToken; ?>" width="300" height="300"/>
                    </div>
                </div>
                <div class="text-center">
                    <a href="#" data-dismiss="modal" data-toggle="modal" data-target="#user_img_modal" data-view="desktop">Change Picture</a>
                </div>
                <div class="user_name_upload">
                    <h6><?php echo $userFullName; ?></h6>
                </div>
            </div>
            <div class="col-sm-10">
                <form id="addUserPostForm" method="post" class="form-horizontal">
                    <div id="addUserPostErrorBox"></div>
                    <textarea title="What's on your mind, <?php echo $userFirstName; ?>?" class="input_box form-control" name="message" placeholder="What's on your mind, <?php echo $userFirstName; ?>?" style="height:100px;"></textarea>
                    <div class="text-right" style="margin-bottom:10px;">
                        <div class="pull-left">
                            <label>
                                <span class="btn btn-default">
                                    <i class="fa fa-picture-o text-success" aria-hidden="true"></i> Photo/Video
                                    <input id="file_video_upload" type="file"
                                           accept="video/*, video/x-m4v, video/webm, video/x-ms-wmv, video/x-msvideo, video/3gpp, video/flv, video/x-flv, video/mp4, video/quicktime, video/mpeg, video/ogv, image/*" style="display: none;" multiple>
                                </span>
                            </label>
                            <input type="text" class="form-control" readonly disabled style="display: none;">
                        </div>
                        <button type="submit" id="addUserPost" value="addUserPost" class="btn btn-primary" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Please wait..."> Post</button>
                    </div>
                </form>
                <div id="mediaContainer"></div>
                <div class="clearfix"></div>
                <div class="title  hidden-xs">
                    <h4>Your Top Players By Recent Activity</h4>
                </div>
                <div class="team_section  hidden-xs">
                    <div class="team_members">
                        <ul>
                            <?php
                            $count = 0;
                            $topPlayers = '';
                            // Get four most followed players
                            try {
                                $sql_follow = "
                                SELECT
                                  COUNT(`Follows`.`FollowId`) AS `totalFollows`,
                                  `Follows`.`FollowId`,
                                  `Follows`.`Designation` AS `FollowDesignation`,
                                  `Follows`.`UserId` AS `FollowUserId`,
                                  `Follows`.`UserIdFrom` AS `FollowUserIdFrom`,
                                  `Follows`.`PlayerId` AS `FollowPlayerId`
                                FROM
                                  `Follows`
                                WHERE
                                  `Follows`.`UserId` = :UserId
                                GROUP BY `Follows`.`PlayerId`
                                HAVING `totalFollows` > 0
                                ORDER BY `totalFollows` DESC
                                LIMIT 4";

                                $stmt_follow = $PDO->prepare($sql_follow);
                                $stmt_follow->bindParam('UserId', $userId, PDO::PARAM_INT); // logged-in user
                                $stmt_follow->execute();

                                while ($row_follow = $stmt_follow->fetch(PDO::FETCH_ASSOC)) {

                                    // Select Player data
                                    try {
                                        $sql_player = "
                                        SELECT
                                          `Players`.`PlayerId`,
                                          `Players`.`UserId` AS `PlayerUserId`,
                                          `Players`.`LastUpdated` AS `PlayerLastUpdated`,
                                          `Players`.`Picture` AS `PlayerPicture`,
                                          `Players`.`Gender` AS `PlayerGender`,
                                          `Players`.`FirstName` AS `PlayerFirstName`,
                                          `Players`.`LastName` AS `PlayerLastName`,
                                          `Players`.`Designation` AS `PlayerDesignation`,
                                          `Players`.`Token` AS `PlayerToken`
                                        FROM
                                          `Players`
                                        WHERE
                                          `Players`.`PlayerId` = :PlayerId";

                                        $stmt_player = $PDO->prepare($sql_player);
                                        $stmt_player->bindParam('PlayerId', $row_follow['FollowPlayerId'], PDO::PARAM_INT); // logged-in user
                                        $stmt_player->execute();

                                        while ($row_player = $stmt_player->fetch(PDO::FETCH_ASSOC)) {
                                            $count++;
                                            $topPlayers .= '<li>';
                                            if ($row_follow['FollowUserIdFrom'] == $userId) {
                                                // if I "followed" my own player
                                                $topPlayers .= '<div class="designation"><p>' . getSimpleDesignation($row_follow['FollowDesignation']) . '</p></div>';
                                            } else {
                                                // if I "followed" someone else's player
                                                $topPlayers .= '<div class="designation"><p>' . getSimpleDesignation($row_follow['FollowDesignation']) . '</p></div>';
                                            }

                                            if (!empty($row_player['PlayerPicture']) && file_exists(constant('UPLOADS_PLAYERS') . $row_player['PlayerPicture'])) {
                                                $topPlayers .= '<div class="img_preview"><div class="img-circle"><img src="' . constant('URL_UPLOADS_PLAYERS') . $row_player['PlayerPicture'] . '" alt="' . trim($row_player['PlayerFirstName'] . ' ' . $row_player['PlayerLastName']) . '"/></div></div>';
                                            } else {
                                                if ($row_player['PlayerGender'] == 0) {
                                                    $topPlayers .= '<div class="img_preview"><div class="img-circle"><img src="' . constant('URL_UPLOADS_PLAYERS') . 'default-male.png" alt="' . trim($row_player['PlayerFirstName'] . ' ' . $row_player['PlayerLastName']) . '"/></div></div>';
                                                } else {
                                                    $topPlayers .= '<div class="img_preview"><div class="img-circle"><img src="' . constant('URL_UPLOADS_PLAYERS') . 'default-female.png" alt="' . trim($row_player['PlayerFirstName'] . ' ' . $row_player['PlayerLastName']) . '"/></div></div>';
                                                }
                                            }

                                            $topPlayers .= '<div class="member_name"><a href="index.php?page=player&token=' . $row_player['PlayerToken'] . '">' . trim($row_player['PlayerFirstName'] . ' ' . $row_player['PlayerLastName']) . '</a></div>';
                                            $topPlayers .= '</li>';

                                        }

                                    } catch (PDOException $e) {
                                        trigger_error($e->getMessage(), E_USER_ERROR);
                                    }//end try
                                }

                            } catch (PDOException $e) {
                                trigger_error($e->getMessage(), E_USER_ERROR);
                            }//end try

                            echo $topPlayers;
                            ?>
                        </ul>
                    </div>
                </div>
                <div class="row">
                    <?php
                    // if you have no players or have not "followed" any players, then say so.
                    if ($count == 0) {
                        echo alertBox("You have no players, <a href=\"index.php?page=my-players\">please here to add an athlete.</a>", "<i class='fa fa-times'></i>", "info");
                    }

                    ?>
                    <div class="col-sm-5 pull-right  hidden-xs">
                        <div class="notification_main" id="alerts">
                            <div class="noti_title">
                                <h5>Alerts & Notices</h5>
                            </div>
                            <div class="noti_content">
                                <ul>
                                    <?php
                                    // pull alerts from WALL
                                    /*
                                    $userAlert = '';
                                    $count = 0;
                                    try {
                                        $sql_alert = "
                                        SELECT
                                          `UserId`,
                                          `UserIdFrom`,
                                          `PlayerId`,
                                          `Created`,
                                          `Type`
                                        FROM
                                          `Alerts`
                                        WHERE
                                          `UserId` = :UserId";

                                        $stmt_alert = $PDO->prepare($sql_alert);
                                        $stmt_alert->bindParam('UserId', $userId, PDO::PARAM_INT);
                                        $stmt_alert->execute();

                                        while ($row_alert = $stmt_alert->fetch(PDO::FETCH_ASSOC)) {
                                            $count++;

                                            $_today = new DateTime();
                                            $_date_of_alert = new DateTime($row_alert['Created']);
                                            $_diff = $_today->diff($_date_of_alert);

                                            $_days_since_alert = $_diff->format("%r%a");
                                            $_year_of_alert = $_date_of_alert->format('Y');
                                            $_minutes_since_alert = $_diff->format('%i');
                                            $_this_year = $_today->format('Y');

                                            $alert_created = '';

                                            if ($_days_since_alert < -0) {
                                                if ($_year_of_alert < $_this_year) {
                                                    // Apr 2016
                                                    $alert_created = date('M Y', strtotime($row_alert['Created']));
                                                } else {
                                                    // Apr 20th
                                                    $alert_created = date('M jS', strtotime($row_alert['Created']));
                                                }
                                            } else {
                                                // show time for today
                                                if ($_minutes_since_alert < 1) {
                                                    // seconds ago
                                                    $alert_created = 'seconds ago';
                                                } else {
                                                    $alert_created = date('g:ia', strtotime($row_alert['Created']));
                                                }
                                            }

                                            $userFromName = '';
                                            try {
                                                $sql_user = "
                                                SELECT
                                                  `Users`.`UserId`,
                                                  `Users`.`Email`,
                                                  `Users`.`FirstName`,
                                                  `Users`.`LastName`,
                                                  `Users`.`Token`,
                                                  `Users`.`Picture`
                                                FROM
                                                  `Users`
                                                WHERE
                                                  `UserId` = :UserIdFrom";

                                                $stmt_user = $PDO->prepare($sql_user);
                                                $stmt_user->bindParam('UserIdFrom', $row_alert['UserIdFrom'], PDO::PARAM_STR);
                                                $stmt_user->execute();

                                                while ($row_user = $stmt_user->fetch(PDO::FETCH_ASSOC)) {

                                                    $userFromName = trim($row_user['FirstName'] . ' ' . $row_user['LastName']);
                                                }

                                            } catch (PDOException $e) {
                                                trigger_error($e->getMessage(), E_USER_ERROR);
                                            }//end try

                                            $playerName = '';
                                            if (!empty($row_alert['PlayerId'])) {
                                                try {
                                                    $sql_player = "
                                                    SELECT
                                                      `Players`.`FirstName`,
                                                      `Players`.`LastName`
                                                    FROM
                                                      `Players`
                                                    WHERE
                                                      `PlayerId` = :PlayerId";

                                                    $stmt_player = $PDO->prepare($sql_player);
                                                    $stmt_player->bindParam('PlayerId', $row_alert['PlayerId'], PDO::PARAM_INT);
                                                    $stmt_player->execute();

                                                    while ($row_player = $stmt_player->fetch(PDO::FETCH_ASSOC)) {
                                                        $playerName = trim($row_player['FirstName'] . ' ' . $row_player['LastName']);
                                                    }

                                                } catch (PDOException $e) {
                                                    trigger_error($e->getMessage(), E_USER_ERROR);
                                                }//end try
                                            }

                                            $alert_message = getAlertMessage($row_alert['Type']);

                                            if ($row_alert['Type'] == 0) {
                                                $alert_message = str_replace('%NAME1%', $userFromName, $alert_message);
                                            }

                                            if ($row_alert['Type'] == 1) {
                                                $alert_message = str_replace('%NAME1%', $userFromName, $alert_message);
                                                $alert_message = str_replace('%NAME2%', $playerName, $alert_message);
                                            }

                                            if ($row_alert['Type'] == 2) {
                                                $alert_message = str_replace('%NAME1%', $userFromName, $alert_message);
                                                $alert_message = str_replace('%PLAYER1%', $playerName, $alert_message);
                                            }

                                            if ($row_alert['Type'] == 3) {
                                                $alert_message = str_replace('%NAME1%', $userFromName, $alert_message);
                                                $alert_message = str_replace('%PLAYER1%', $playerName, $alert_message);
                                            }

                                            $userAlert .= '<li>';
                                            $userAlert .= '<p>' . $alert_message . '</p>';
                                            $userAlert .= '<span>' . $alert_created . '</span>';
                                            $userAlert .= '</li>';


                                        }

                                    } catch (PDOException $e) {
                                        trigger_error($e->getMessage(), E_USER_ERROR);
                                    }//end try

                                    if ($count > 0) {
                                        echo $userAlert;
                                    } else {
                                        echo '<li><p>You have no alerts</p></li>';
                                    }
                                    */
                                    ?>
                                </ul>
                            </div>
                        </div>
                        <div class="message_main" id="messages">
                            <div class="noti_title msg">
                                <h5>Recent Messages</h5>
                            </div>
                            <div class="noti_content msg">
                                <ul>
                                    <?php
                                    $userMessage = '';
                                    $count = 0;
                                    try {
                                        $sql_message = "
                                        SELECT
                                          AES_DECRYPT(`Content`, SUBSTRING(`Token`, 4, 16)) AS `MessageContent`,
                                          `UserIdFrom`,
                                          `Created`
                                        FROM
                                          `Messages`
                                        WHERE
                                          `UserId` = :UserId";

                                        $stmt_message = $PDO->prepare($sql_message);
                                        $stmt_message->bindParam('UserId', $userId, PDO::PARAM_INT);
                                        $stmt_message->execute();

                                        while ($row_message = $stmt_message->fetch(PDO::FETCH_ASSOC)) {
                                            $count++;

                                            try {
                                                $sql_user = "
                                                SELECT
                                                  `Users`.`UserId`,
                                                  `Users`.`Email`,
                                                  `Users`.`Gender`,
                                                  `Users`.`FirstName`,
                                                  `Users`.`LastName`,
                                                  `Users`.`Token`,
                                                  `Users`.`Picture`
                                                FROM
                                                  `Users`
                                                WHERE
                                                  `UserId` = :UserIdFrom";

                                                $stmt_user = $PDO->prepare($sql_user);
                                                $stmt_user->bindParam('UserIdFrom', $row_message['UserIdFrom'], PDO::PARAM_STR);
                                                $stmt_user->execute();

                                                while ($row_user = $stmt_user->fetch(PDO::FETCH_ASSOC)) {

                                                    $_today = new DateTime();
                                                    $_date_of_message = new DateTime($row_message['Created']);
                                                    $_diff = $_today->diff($_date_of_message);

                                                    $_days_since_message = $_diff->format("%r%a");
                                                    $_year_of_message = $_date_of_message->format('Y');
                                                    $_this_year = $_today->format('Y');

                                                    $message_created = '';
                                                    if ($_days_since_message < -0) {

                                                        if ($_year_of_message < $_this_year) {
                                                            // Apr 2016
                                                            $message_created = date('M Y', strtotime($row_message['Created']));
                                                        } else {
                                                            // Apr 20th
                                                            $message_created = date('M jS', strtotime($row_message['Created']));
                                                        }

                                                    } else {
                                                        // show time for today
                                                        $message_created = date('g:ia', strtotime($row_message['Created']));
                                                    }

                                                    $_userPicture = '';

                                                    if (!empty($row_user['Picture']) && file_exists(constant('UPLOADS_USERS') . $row_user['Picture'])) {
                                                        $_userPicture = constant('URL_UPLOADS_USERS') . $row_user['Picture'];
                                                    } else {
                                                        if ($row_user['Gender'] == 0) {
                                                            $_userPicture = constant('URL_UPLOADS_USERS') . 'default-male.png';
                                                        } else {
                                                            $_userPicture = constant('URL_UPLOADS_USERS') . 'default-female.png';
                                                        }
                                                    }

                                                    $userMessage .= '<li>';
                                                    $userMessage .= '<img src="' . $_userPicture . '" alt="image" class="user_img_profile_picture img-circle img-responsive"/>';
                                                    $userMessage .= '<div class="msg_content"><h6>' . $row_user['FirstName'] . ' ' . $row_user['LastName'] . '</h6><p><span>' . (($userId == $row_user['UserId']) ? 'you' : 'them') . ':</span> ' . $row_message['MessageContent'] . '</p></div>';
                                                    $userMessage .= '<span class="time">' . $message_created . '</span>';
                                                    $userMessage .= '</li>';
                                                }

                                            } catch (PDOException $e) {
                                                trigger_error($e->getMessage(), E_USER_ERROR);
                                            }//end try

                                        }

                                    } catch (PDOException $e) {
                                        trigger_error($e->getMessage(), E_USER_ERROR);
                                    }//end try

                                    if ($count > 0) {
                                        echo $userMessage;
                                    } else {
                                        echo '<li><p>You have no messages</p></li>';
                                    }

                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-7">
                        <div class="user_content">
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#tab1" data-toggle="tab" aria-expanded="true"><?php echo $userFirstName; ?></a></li>
                                <!--<li><a href="#tab2" data-toggle="tab" aria-expanded="false" class="activity">Activity Log</a></li>-->
                            </ul>
                        </div>
                        <div class="tab-content">
                            <div class="tab-pane active" id="tab1">

                            </div>
                            <div class="tab-pane " id="tab2">
                                <div class="single_wall activity">
                                    <ul>
                                        <li>
                                            <p>Kaden Blankenship<span> became</span></p>
                                            <p><span>friends with</span> Jhon White.</p>
                                            <a href="#" class="edit_activity"></a>
                                        </li>
                                        <li>
                                            <p>Kaden Blankenship<span> became</span></p>
                                            <p><span>friends with</span> Jhon White.</p>
                                            <a href="#" class="edit_activity"></a>
                                        </li>
                                        <li>
                                            <p>Kaden Blankenship<span> became</span></p>
                                            <p><span>friends with</span> Jhon White.</p>
                                            <a href="#" class="edit_activity"></a>
                                        </li>
                                        <li>
                                            <p>Kaden Blankenship<span> became</span></p>
                                            <p><span>friends with</span> Jhon White.</p>
                                            <a href="#" class="edit_activity"></a>
                                        </li>
                                        <li>
                                            <p>Kaden Blankenship<span> became</span></p>
                                            <p><span>friends with</span> Jhon White.</p>
                                            <a href="#" class="edit_activity"></a>
                                        </li>
                                        <li>
                                            <p>Kaden Blankenship<span> became</span></p>
                                            <p><span>friends with</span> Jhon White.</p>
                                            <a href="#" class="edit_activity"></a>
                                        </li>
                                        <li>
                                            <p>Kaden Blankenship<span> became</span></p>
                                            <p><span>friends with</span> Jhon White.</p>
                                            <a href="#" class="edit_activity"></a>
                                        </li>
                                        <li>
                                            <p>Kaden Blankenship<span> became</span></p>
                                            <p><span>friends with</span> Jhon White.</p>
                                            <a href="#" class="edit_activity"></a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="bottom_navigation">
        <ul>
            <li><a href="index.php?page=home" class="home"><span></span>Home</a></li>
            <li><a href="#alerts" class="alerts"><span></span>Notifications</a></li>
            <li><a href="#messages" class="msgs"><span></span>Messages</a></li>
            <li><a href="#messages" class="msgs"><span></span>Post</a></li>
            <li><a href="javascript:void(0);" class="find_player"><span></span>Find Player</a></li>
        </ul>
    </div>
</section>

<div class="dd-menu-post">
    <ul role="menu">
        <li role="presentation">
            <a role="menuitem" class="dd-item-link" href="#" data-action="edit" data-toggle="modal" data-target="#edit_post">
                <div class="dd-item">
                    <i class="fa fa-pencil" aria-hidden="true"></i>
                    <div class="dd-item-action">
                        <div class="dd-item-title">Edit post</div>
                        <div class="dd-item-desc">Edit the post content</div>
                    </div>
                </div>
            </a>
        </li>
        <li role="presentation">
            <a role="menuitem" class="dd-item-link" href="#" data-action="delete" data-toggle="modal" data-target="#delete_post">
                <div class="dd-item">
                    <i class="fa fa-trash" aria-hidden="true"></i>
                    <div class="dd-item-action">
                        <div class="dd-item-title">Delete post</div>
                        <div class="dd-item-desc">Permanently delete this post</div>
                    </div>
                </div>
            </a>
        </li>
        <li role="presentation">
            <a role="menuitem" class="dd-item-link" href="#" data-action="hide" data-toggle="modal" data-target="#hide_post">
                <div class="dd-item">
                    <i class="fa fa-times-circle" aria-hidden="true"></i>
                    <div class="dd-item-action">
                        <div class="dd-item-title">Hide post</div>
                        <div class="dd-item-desc">See fewer posts like this</div>
                    </div>
                </div>
            </a>
        </li>
        <li role="presentation">
            <a role="menuitem" class="dd-item-link" href="#" data-action="unfollow" data-toggle="modal" data-target="#unfollow_post">
                <div class="dd-item">
                    <i class="fa fa-sign-out" aria-hidden="true"></i>
                    <div class="dd-item-action">
                        <div class="dd-item-title">Unfollow</div>
                        <div class="dd-item-desc">Stop seeing posts but stay friends</div>
                    </div>
                </div>
            </a>
        </li>
        <li role="presentation">
            <a role="menuitem" class="dd-item-link" href="#" data-action="report" data-toggle="modal" data-target="#report_post">
                <div class="dd-item">
                    <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                    <div class="dd-item-action">
                        <div class="dd-item-title">Report post</div>
                    </div>
                </div>
            </a>
        </li>
    </ul>
</div>


<div class="dd-menu-media">
    <ul role="menu">
        <li role="presentation">
            <a role="menuitem" class="dd-item-link" href="#" data-action="edit" data-toggle="modal" data-target="#edit_media">
                <div class="dd-item">
                    <i class="fa fa-pencil" aria-hidden="true"></i>
                    <div class="dd-item-action">
                        <div class="dd-item-title">Edit post</div>
                        <div class="dd-item-desc">Edit the post content</div>
                    </div>
                </div>
            </a>
        </li>
        <li role="presentation">
            <a role="menuitem" class="dd-item-link" href="#" data-action="delete" data-toggle="modal" data-target="#delete_media">
                <div class="dd-item">
                    <i class="fa fa-trash" aria-hidden="true"></i>
                    <div class="dd-item-action">
                        <div class="dd-item-title">Delete post</div>
                        <div class="dd-item-desc">Permanently delete this post</div>
                    </div>
                </div>
            </a>
        </li>
        <li role="presentation">
            <a role="menuitem" class="dd-item-link" href="#" data-action="hide" data-toggle="modal" data-target="#hide_media">
                <div class="dd-item">
                    <i class="fa fa-times-circle" aria-hidden="true"></i>
                    <div class="dd-item-action">
                        <div class="dd-item-title">Hide post</div>
                        <div class="dd-item-desc">See fewer posts like this</div>
                    </div>
                </div>
            </a>
        </li>
        <li role="presentation">
            <a role="menuitem" class="dd-item-link" href="#" data-action="unfollow" data-toggle="modal" data-target="#unfollow_media">
                <div class="dd-item">
                    <i class="fa fa-sign-out" aria-hidden="true"></i>
                    <div class="dd-item-action">
                        <div class="dd-item-title">Unfollow</div>
                        <div class="dd-item-desc">Stop seeing posts but stay friends</div>
                    </div>
                </div>
            </a>
        </li>
        <li role="presentation">
            <a role="menuitem" class="dd-item-link" href="#" data-action="report" data-toggle="modal" data-target="#report_media">
                <div class="dd-item">
                    <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                    <div class="dd-item-action">
                        <div class="dd-item-title">Report post</div>
                    </div>
                </div>
            </a>
        </li>
    </ul>
</div>

<div class="dd-menu-post-comment">
    <ul role="menu">
        <li role="presentation">
            <a role="menuitem" class="dd-item-link" href="#" data-action="edit" data-toggle="modal" data-target="#edit_post_comment">
                <div class="dd-item">
                    <i class="fa fa-pencil" aria-hidden="true"></i>
                    <div class="dd-item-action">
                        <div class="dd-item-title">Edit</div>
                        <div class="dd-item-desc">Edit the comment</div>
                    </div>
                </div>
            </a>
        </li>
        <li role="presentation">
            <a role="menuitem" class="dd-item-link" href="#" data-action="delete" data-toggle="modal" data-target="#delete_post_comment">
                <div class="dd-item">
                    <i class="fa fa-trash" aria-hidden="true"></i>
                    <div class="dd-item-action">
                        <div class="dd-item-title">Delete</div>
                        <div class="dd-item-desc">Delete the comment</div>
                    </div>
                </div>
            </a>
        </li>
        <li role="presentation">
            <a role="menuitem" class="dd-item-link" href="#" data-action="hide" data-toggle="modal" data-target="#hide_post_comment">
                <div class="dd-item">
                    <i class="fa fa-times-circle" aria-hidden="true"></i>
                    <div class="dd-item-action">
                        <div class="dd-item-title">Hide</div>
                        <div class="dd-item-desc">Hide the comment</div>
                    </div>
                </div>
            </a>
        </li>
    </ul>
</div>

<div class="dd-menu-media-comment">
    <ul role="menu">
        <li role="presentation">
            <a role="menuitem" class="dd-item-link" href="#" data-action="edit" data-toggle="modal" data-target="#edit_media_comment">
                <div class="dd-item">
                    <i class="fa fa-pencil" aria-hidden="true"></i>
                    <div class="dd-item-action">
                        <div class="dd-item-title">Edit</div>
                        <div class="dd-item-desc">Edit the comment</div>
                    </div>
                </div>
            </a>
        </li>
        <li role="presentation">
            <a role="menuitem" class="dd-item-link" href="#" data-action="delete" data-toggle="modal" data-target="#delete_media_comment">
                <div class="dd-item">
                    <i class="fa fa-trash" aria-hidden="true"></i>
                    <div class="dd-item-action">
                        <div class="dd-item-title">Delete</div>
                        <div class="dd-item-desc">Delete the comment</div>
                    </div>
                </div>
            </a>
        </li>
        <li role="presentation">
            <a role="menuitem" class="dd-item-link" href="#" data-action="hide" data-toggle="modal" data-target="#hide_media_comment">
                <div class="dd-item">
                    <i class="fa fa-times-circle" aria-hidden="true"></i>
                    <div class="dd-item-action">
                        <div class="dd-item-title">Hide</div>
                        <div class="dd-item-desc">Hide the comment</div>
                    </div>
                </div>
            </a>
        </li>
    </ul>
</div>

<!-- Edit Post Comment -->
<div id="edit_post_comment" class="modal forget_section" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="container">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="close_img">
                        <button type="button" class="close" data-dismiss="modal"></button>
                    </div>
                    <div class="title_h2">
                        <h2>Edit Comment</h2>
                    </div>
                </div>
                <div class="modal-body">
                    <form id="editUserPostCommentForm" action="index.php?action=editUserPostComment" method="post" class="form-horizontal">
                        <div class="form-group">
                            <label for="message" class="col-sm-2 control-label">Message</label>
                            <div class="col-sm-10">
                                <textarea id="message" title="What's on your mind, <?php echo $userFirstName; ?>?" class="input_box form-control" name="message" placeholder="What's on your mind, <?php echo $userFirstName; ?>?" style="height:100px;"></textarea>
                            </div>
                        </div>
                        <div class="all_btn text-center">
                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal"><i class="fa fa-chevron-left"></i> Back</button>
                            <button type="submit" class="btn btn-success" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                        </div>
                        <input type="hidden" name="token">
                        <input type="hidden" name="id">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Edit Post Comment -->

<!-- Edit Media Comment -->
<div id="edit_media_comment" class="modal forget_section" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="container">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="close_img">
                        <button type="button" class="close" data-dismiss="modal"></button>
                    </div>
                    <div class="title_h2">
                        <h2>Edit Comment</h2>
                    </div>
                </div>
                <div class="modal-body">
                    <form id="editUserMediaCommentForm" action="index.php?action=editUserMediaComment" method="post" class="form-horizontal">
                        <div class="form-group">
                            <label for="message" class="col-sm-2 control-label">Message</label>
                            <div class="col-sm-10">
                                <textarea id="message" title="What's on your mind, <?php echo $userFirstName; ?>?" class="input_box form-control" name="message" placeholder="What's on your mind, <?php echo $userFirstName; ?>?" style="height:100px;"></textarea>
                            </div>
                        </div>
                        <div class="all_btn text-center">
                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal"><i class="fa fa-chevron-left"></i> Back</button>
                            <button type="submit" class="btn btn-success" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                        </div>
                        <input type="hidden" name="token">
                        <input type="hidden" name="id">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Edit Media Comment -->

<!-- Delete Post Comment -->
<div id="delete_post_comment" class="modal forget_section" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="container">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="close_img">
                        <button type="button" class="close" data-dismiss="modal"></button>
                    </div>
                    <div class="title_h2">
                        <h2>Delete Comment</h2>
                    </div>
                </div>
                <div class="modal-body">
                    <form id="deleteUserPostCommentForm" action="index.php?action=deleteUserPostComment" method="post" class="form-horizontal">
                        <p class="text-center">Are you sure you want to delete this comment?</p>
                        <div class="all_btn text-center">
                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal"><i class="fa fa-chevron-left"></i> Back</button>
                            <button type="submit" class="btn btn-success" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                        </div>
                        <input type="hidden" name="token">
                        <input type="hidden" name="id">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Delete Post Comment -->

<!-- Delete Media Comment -->
<div id="delete_media_comment" class="modal forget_section" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="container">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="close_img">
                        <button type="button" class="close" data-dismiss="modal"></button>
                    </div>
                    <div class="title_h2">
                        <h2>Delete Comment</h2>
                    </div>
                </div>
                <div class="modal-body">
                    <form id="deleteUserMediaCommentForm" action="index.php?action=deleteUserMediaComment" method="post" class="form-horizontal">
                        <p class="text-center">Are you sure you want to delete this comment?</p>
                        <div class="all_btn text-center">
                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal"><i class="fa fa-chevron-left"></i> Back</button>
                            <button type="submit" class="btn btn-success" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                        </div>
                        <input type="hidden" name="token">
                        <input type="hidden" name="id">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Delete Media Comment -->

<!-- Hide Post Comment -->
<div id="hide_post_comment" class="modal forget_section" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="container">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="close_img">
                        <button type="button" class="close" data-dismiss="modal"></button>
                    </div>
                    <div class="title_h2">
                        <h2>Hide Comment</h2>
                    </div>
                </div>
                <div class="modal-body">
                    <form id="hideUserPostCommentForm" action="index.php?action=hideUserPostComment" method="post" class="form-horizontal">
                        <p class="text-center">Are you sure you want to hide this comment?</p>
                        <div class="all_btn text-center">
                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal"><i class="fa fa-chevron-left"></i> Back</button>
                            <button type="submit" class="btn btn-success" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                        </div>
                        <input type="hidden" name="token">
                        <input type="hidden" name="id">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Hide Post Comment -->

<!-- Hide Media Comment -->
<div id="hide_media_comment" class="modal forget_section" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="container">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="close_img">
                        <button type="button" class="close" data-dismiss="modal"></button>
                    </div>
                    <div class="title_h2">
                        <h2>Hide Comment</h2>
                    </div>
                </div>
                <div class="modal-body">
                    <form id="hideUserMediaCommentForm" action="index.php?action=hideUserMediaComment" method="post" class="form-horizontal">
                        <p class="text-center">Are you sure you want to hide this comment?</p>
                        <div class="all_btn text-center">
                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal"><i class="fa fa-chevron-left"></i> Back</button>
                            <button type="submit" class="btn btn-success" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                        </div>
                        <input type="hidden" name="token">
                        <input type="hidden" name="id">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Hide Media Comment -->

<!-- Edit Post -->
<div id="edit_post" class="modal forget_section" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="container">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="close_img">
                        <button type="button" class="close" data-dismiss="modal"></button>
                    </div>
                    <div class="title_h2">
                        <h2>Edit Post</h2>
                    </div>
                </div>
                <div class="modal-body">
                    <form id="editUserPostForm" action="index.php?action=editUserPost" method="post" class="form-horizontal">
                        <div class="form-group">
                            <label for="message" class="col-sm-2 control-label">Message</label>
                            <div class="col-sm-10">
                                <textarea id="message" title="What's on your mind, <?php echo $userFirstName; ?>?" class="input_box form-control" name="message" placeholder="What's on your mind, <?php echo $userFirstName; ?>?" style="height:100px;"></textarea>
                            </div>
                        </div>
                        <div class="all_btn text-center">
                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal"><i class="fa fa-chevron-left"></i> Back</button>
                            <button type="submit" class="btn btn-success" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                        </div>
                        <input type="hidden" name="token">
                        <input type="hidden" name="id">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Edit Post -->

<!-- Edit Media -->
<div id="edit_media" class="modal forget_section" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="container">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="close_img">
                        <button type="button" class="close" data-dismiss="modal"></button>
                    </div>
                    <div class="title_h2">
                        <h2>Edit Post</h2>
                    </div>
                </div>
                <div class="modal-body">
                    <form id="editUserMediaForm" action="index.php?action=editUserMedia" method="post" class="form-horizontal">
                        <div class="form-group">
                            <label for="media_title" class="col-sm-2 control-label">Title</label>
                            <div class="col-sm-10">
                                <input id="media_title" class="input_box form-control" name="media_title" placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="media_description" class="col-sm-2 control-label">Description</label>
                            <div class="col-sm-10">
                                <textarea id="media_description" class="input_box form-control" name="media_description" placeholder="" style="height:100px;"></textarea>
                            </div>
                        </div>
                        <div class="all_btn text-center">
                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal"><i class="fa fa-chevron-left"></i> Back</button>
                            <button type="submit" class="btn btn-success" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                        </div>
                        <input type="hidden" name="token">
                        <input type="hidden" name="id">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Edit Media -->

<!-- Delete Post -->
<div id="delete_post" class="modal forget_section" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="container">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="close_img">
                        <button type="button" class="close" data-dismiss="modal"></button>
                    </div>
                    <div class="title_h2">
                        <h2>Delete Post</h2>
                    </div>
                </div>
                <div class="modal-body">
                    <p class="text-center">Are you sure you want to delete this post?</p>
                    <form id="deleteUserPostForm" action="index.php?action=deleteUserPost" method="post" class="form-horizontal">
                        <div class="all_btn text-center">
                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal"><i class="fa fa-chevron-left"></i> Back</button>
                            <button type="submit" class="btn btn-success" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Please wait..."><i class="fa fa-check"></i> Yes, Delete!</button>
                        </div>
                        <input type="hidden" name="token">
                        <input type="hidden" name="id">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Delete Post -->

<!-- Delete Media -->
<div id="delete_media" class="modal forget_section" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="container">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="close_img">
                        <button type="button" class="close" data-dismiss="modal"></button>
                    </div>
                    <div class="title_h2">
                        <h2>Delete Post</h2>
                    </div>
                </div>
                <div class="modal-body">
                    <p class="text-center">Are you sure you want to delete this post?</p>
                    <form id="deleteUserMediaForm" action="index.php?action=deleteUserMedia" method="post" class="form-horizontal">
                        <div class="all_btn text-center">
                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal"><i class="fa fa-chevron-left"></i> Back</button>
                            <button type="submit" class="btn btn-success" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Please wait..."><i class="fa fa-check"></i> Yes, Delete!</button>
                        </div>
                        <input type="hidden" name="token">
                        <input type="hidden" name="id">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Delete Media -->

<!-- Hide Post -->
<div id="hide_post" class="modal forget_section" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="container">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="close_img">
                        <button type="button" class="close" data-dismiss="modal"></button>
                    </div>
                    <div class="title_h2">
                        <h2>Hide Post</h2>
                    </div>
                </div>
                <div class="modal-body">
                    <p class="text-center">Are you sure you want to hide this post?</p>
                    <form id="hideUserPostForm" action="index.php?action=hideUserPost" method="post" class="form-horizontal">
                        <div class="all_btn text-center">
                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal"><i class="fa fa-chevron-left"></i> Back</button>
                            <button type="submit" class="btn btn-success" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Please wait..."><i class="fa fa-check"></i> Yes, Be Gone!</button>
                        </div>
                        <input type="hidden" name="token">
                        <input type="hidden" name="id">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Hide Post -->

<!-- Hide Media -->
<div id="hide_media" class="modal forget_section" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="container">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="close_img">
                        <button type="button" class="close" data-dismiss="modal"></button>
                    </div>
                    <div class="title_h2">
                        <h2>Hide Post</h2>
                    </div>
                </div>
                <div class="modal-body">
                    <p class="text-center">Are you sure you want to hide this post?</p>
                    <form id="hideUserMediaForm" action="index.php?action=hideUserMedia" method="post" class="form-horizontal">
                        <div class="all_btn text-center">
                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal"><i class="fa fa-chevron-left"></i> Back</button>
                            <button type="submit" class="btn btn-success" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Please wait..."><i class="fa fa-check"></i> Yes, Be Gone!</button>
                        </div>
                        <input type="hidden" name="token">
                        <input type="hidden" name="id">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Hide Media -->

<!-- Unfollow Post -->
<div id="unfollow_post" class="modal forget_section" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="container">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="close_img">
                        <button type="button" class="close" data-dismiss="modal"></button>
                    </div>
                    <div class="title_h2">
                        <h2>Unfollow User Posts</h2>
                    </div>
                </div>
                <div class="modal-body">
                    <p class="text-center">Are you sure you want to stop seeing all posts by this user?</p>
                    <form id="unfollowUserPostForm" action="index.php?action=unfollowUserPost" method="post" class="form-horizontal">
                        <div class="all_btn text-center">
                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal"><i class="fa fa-chevron-left"></i> Back</button>
                            <button type="submit" class="btn btn-success" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Please wait..."><i class="fa fa-check"></i> Yes, Be Gone!</button>
                        </div>
                        <input type="hidden" name="token">
                        <input type="hidden" name="id">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Unfollow Post -->

<!-- Unfollow Media -->
<div id="unfollow_media" class="modal forget_section" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="container">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="close_img">
                        <button type="button" class="close" data-dismiss="modal"></button>
                    </div>
                    <div class="title_h2">
                        <h2>Unfollow User Posts</h2>
                    </div>
                </div>
                <div class="modal-body">
                    <p class="text-center">Are you sure you want to stop seeing all posts by this user?</p>
                    <form id="unfollowUserMediaForm" action="index.php?action=unfollowUserMedia" method="post" class="form-horizontal">
                        <div class="all_btn text-center">
                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal"><i class="fa fa-chevron-left"></i> Back</button>
                            <button type="submit" class="btn btn-success" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Please wait..."><i class="fa fa-check"></i> Yes, Be Gone!</button>
                        </div>
                        <input type="hidden" name="token">
                        <input type="hidden" name="id">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Unfollow Media -->

<!-- Report Post -->
<div id="report_post" class="modal forget_section" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="container">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="close_img">
                        <button type="button" class="close" data-dismiss="modal"></button>
                    </div>
                    <div class="title_h2">
                        <h2>Report Post</h2>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row center-block">
                        <div class="col-md-5 col-md-offset-4 col-sm-6 col-sm-offset-4 col-xs-12">
                            <form id="reportUserPostForm" action="index.php?action=reportUserPost" method="post" class="form-horizontal">
                                <div class="control-group">
                                    <label class="control-label">What's going on?</label>
                                    <div class="controls">
                                        <label class="radio">
                                            <input type="radio" name="reason" value="0">
                                            It's annoying or not interesting
                                        </label>
                                        <label class="radio">
                                            <input type="radio" name="reason" value="2">
                                            I think it shouldn't be on PlayerFax
                                        </label>
                                        <label class="radio">
                                            <input type="radio" name="reason" value="3">
                                            It's spam
                                        </label>
                                    </div>
                                </div>
                                <div class="all_btn text-center">
                                    <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal"><i class="fa fa-chevron-left"></i> Back</button>
                                    <button type="submit" class="btn btn-success" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                </div>
                                <input type="hidden" name="token">
                                <input type="hidden" name="id">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Report Post -->

<!-- Report Media -->
<div id="report_media" class="modal forget_section" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="container">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="close_img">
                        <button type="button" class="close" data-dismiss="modal"></button>
                    </div>
                    <div class="title_h2">
                        <h2>Report Post</h2>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row center-block">
                        <div class="col-md-5 col-md-offset-4 col-sm-6 col-sm-offset-4 col-xs-12">
                            <form id="reportUserMediaForm" action="index.php?action=reportUserMedia" method="post" class="form-horizontal">
                                <div class="control-group">
                                    <label class="control-label">What's going on?</label>
                                    <div class="controls">
                                        <label class="radio">
                                            <input type="radio" name="reason" value="0">
                                            It's annoying or not interesting
                                        </label>
                                        <label class="radio">
                                            <input type="radio" name="reason" value="1">
                                            I'm in this photo/video and I don't like it
                                        </label>
                                        <label class="radio">
                                            <input type="radio" name="reason" value="2">
                                            I think it shouldn't be on PlayerFax
                                        </label>
                                        <label class="radio">
                                            <input type="radio" name="reason" value="3">
                                            It's spam
                                        </label>
                                    </div>
                                </div>
                                <div class="all_btn text-center">
                                    <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal"><i class="fa fa-chevron-left"></i> Back</button>
                                    <button type="submit" class="btn btn-success" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                </div>
                                <input type="hidden" name="token">
                                <input type="hidden" name="id">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Report Media -->

<!-- Share Post -->
<div id="share_post" class="modal forget_section" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="container">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="close_img">
                        <button type="button" class="close" data-dismiss="modal"></button>
                    </div>
                    <div class="title_h2">
                        <h2>Share Post</h2>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row center-block">
                        <div class="col-md-5 col-md-offset-4 col-sm-6 col-sm-offset-4 col-xs-12">
                            <form id="shareUserPostForm" action="index.php?action=shareUserPost" method="post" class="form-horizontal">

                                <div class="all_btn text-center">
                                    <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal"><i class="fa fa-chevron-left"></i> Back</button>
                                    <button type="submit" class="btn btn-success" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                </div>
                                <input type="hidden" name="token">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Share Post -->

<!-- Share Media -->
<div id="share_media" class="modal forget_section" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="container">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="close_img">
                        <button type="button" class="close" data-dismiss="modal"></button>
                    </div>
                    <div class="title_h2">
                        <h2>Share Media</h2>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row center-block">
                        <div class="col-md-5 col-md-offset-4 col-sm-6 col-sm-offset-4 col-xs-12">
                            <form id="shareUserMediaForm" action="index.php?action=shareUserMedia" method="post" class="form-horizontal">

                                <div class="all_btn text-center">
                                    <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal"><i class="fa fa-chevron-left"></i> Back</button>
                                    <button type="submit" class="btn btn-success" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                </div>
                                <input type="hidden" name="token">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Share Media -->


<?php require 'includes/footer.php'; ?>

<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/jquery.flexslider-min.js"></script>
<script type="text/javascript" src="js/bootstrap-tabcollapse.js"></script>
<script type="text/javascript" src="js/SmoothScroll.js"></script>
<script type="text/javascript" src="js/moment.min.js"></script>
<script type="text/javascript" src="js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="js/bootstrap-datetimepicker.js"></script>
<script type="text/javascript" src="js/select2.js"></script>
<script type="text/javascript" src="js/DataTables-1.10.15/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="js/DataTables-1.10.15/media/js/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="js/DataTables-1.10.15/extensions/Buttons/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="js/DataTables-1.10.15/extensions/Buttons/js/buttons.bootstrap.min.js"></script>
<script type="text/javascript" src="js/DataTables-1.10.15/extensions/Buttons/js/buttons.print.min.js"></script>
<script type="text/javascript" src="js/DataTables-1.10.15/extensions/Buttons/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="js/jquery.storage.js"></script>
<script type="text/javascript" src="js/cropper-min.js"></script>
<script type="text/javascript" src="js/jq-ajax-progress.min.js"></script>
<script type="text/javascript" src="js/custom.js"></script>
<script type="text/javascript" src="js/addtohomescreen.min.js"></script>
<script>
    addToHomescreen();
</script>

<script>

    var isComplete = false;

    $(document).ready(function () {

        $('input.date').each(function () {
            var opts = {};
            opts.format = 'mm/dd/yyyy';
            $(this).datepicker(opts);
        });

        $('input.datetime').each(function () {
            var opts = {};
            var currentValue = moment($(this).val());
            if (currentValue.isValid()) {
                opts.defaultDate = currentValue;
            }
            $(this).datetimepicker(opts);
        });

        $('#addUserPostForm').on('submit', function (e) {
            var form = $(this);
            var textarea = form.find('textarea:eq(0)');
            if (isComplete === false) {
                $('#addUserPost').button('loading');
                isComplete = true;
                $.ajax({
                    url: 'index.php?action=addUserPost',
                    type: 'POST',
                    data: $(this).serializeArray(),
                    dataType: 'json',
                    success: function (json) {

                        $('#addUserPostErrorBox').empty();

                        //data: return data from server
                        if (typeof json.success !== 'undefined') {
                            textarea.val('');
                            $('#addUserPostErrorBox').html(json.success);
                            $('#tab1').prepend(json.content);
                            createPostDropDownOptions();
                            createCommentDropDownOptions();
                        }

                        if (typeof json.error !== 'undefined') {
                            $('#addUserPostErrorBox').html(json.error);
                        }
                        isComplete = false;
                        $('#addUserPost').button('reset');
                    }
                });
            }
            e.preventDefault();
        });

        $('#searchPlayerQuery').select2({
            theme: "bootstrap",
            placeholder: "Search by Name, School, Grad Year, City, State and Email Address",
            allowClear: true,
            minimumInputLength: 3,
            closeOnSelect: false,
            ajax: {
                url: "index.php?action=searchForPlayer",
                dataType: "json",
                width: 'style',
                delay: 800,
                type: 'POST',
                data: function (params) {
                    return {
                        query: params.term,
                        page: params.page,
                        per_page: 10
                    };
                },
                processResults: function (data, params) {

                    //params.page = params.page || 1;
                    return {
                        results: data.results.players.map(function (player) {
                            return {
                                id: player.Token,
                                playerGender: player.Gender,
                                playerName: player.Name,
                                playerPicture: player.Picture,
                                playerSchool: player.School,
                                playerCity: player.City,
                                playerStateLong: player.StateLong,
                                playerGradYear: player.GradYear,
                                playerIsFriend: player.IsFriend
                            };
                        }),
                        pagination: {
                            // If there are 10 matches, there's at least another page
                            more: data.results.players.length === 10
                            //more: (params.page * 10) < data.recordsTotal
                        }
                    };
                },
                cache: true
            },
            templateResult: formatPlayer,
            templateSelection: formatPlayerSelection
        }).on('select2:select', function (e) {
            var selectedElement = $(e.currentTarget);
            var selectedValue = selectedElement.val();
            window.open('index.php?page=player&token=' + selectedValue, 'PlayerFaxWindow');
        });

        $('#file_video_upload').on('change', function () {
            var token = $(this).data('token');
            var input = $(this);
            var numFiles = this.files.length;
            var fileType = '';
            var label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
            input.trigger('fileselect', [numFiles, label]);

            for (var i = 0; i < numFiles; i++) {
                var file = this.files[i];
                var filePath = file.name.replace(/\\/g, '/');
                var fileName = filePath.substring(filePath.lastIndexOf('/') + 1, filePath.lastIndexOf('.'));
                // check file extension
                var ext = file.name.substring(file.name.lastIndexOf('.') + 1).toLowerCase();
                if (!(/(gif|jpe?g|png|mpe?g|mp4|m4v|webm|wmv|3gp|3g2|flv|ogv|mov)$/i).test(ext)) {
                    alert('You must select an image/video file only ' + file.name);
                    return false;
                }
                if ((/(gif|jpe?g|png)$/i).test(ext)) {
                    fileType = '<i class="fa fa-picture-o" aria-hidden="true"></i> Image';
                } else {
                    fileType = '<i class="fa fa-film" aria-hidden="true"></i> Video';
                }
                // check file size
                if (file.size > 2147483648) {
                    alert('Max upload size is 2 GB');
                    return false;
                }
                var id = "progress_" + Math.floor((Math.random() * 100000));
                $('#tab1').prepend('<div class="mediaFile"><span>' + fileType + '</span><p class="wordwrap">' + fileName + '</p><form><input class="input_box" name="mediaTitle" placeholder="Title"><textarea name="mediaDescription" class="input_box textarea_box" placeholder="Description"></textarea></form><div class="progress"><div id="' + id + '" class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">0%</div></div></div>');

                var data = new FormData();
                data.append('Filedata', file);
                data.append('token', token);
                data.append('progressId', id);

                // upload and display progress bar for each file
                $.when(uploadMedia(data, id)).done(function (json) {
                    //updateMedia(json);
                });

            } // end for

        }).on('fileselect', function (event, numFiles, label) {
            var input = $(this).parents('.input-group').find(':text');
            var log = numFiles > 1 ? numFiles + ' files selected' : label;
            if (input.length) {
                input.val(log);
            }
        });

    }); // end doc.ready

    function uploadMedia(data, id) {
        var progressBar = $('#' + id);
        var parent = progressBar.parent().parent();
        var progressBarContainer = parent.find('.progress');
        var form = parent.find('form');
        var mediaFileContainer = form.closest('.mediaFile');
        $.ajax({
            url: 'index.php?action=addUserMedia',
            data: data,
            dataType: 'json',
            processData: false,
            contentType: false,
            type: 'POST',
            timeout: 0,
            success: function (json) {

                $(progressBarContainer).remove();

                if (typeof json.success !== 'undefined') {
                    form.append('<button type="button" class="btn btn-success" data-progress="' + id + '" data-loading-text="<i class=\'fa fa-circle-o-notch fa-spin\'></i> Saving...">Update </button>');
                    if (json.url !== '') {
                        mediaFileContainer.find('p.wordwrap').html('<img class="img-thumbnail center-block" src="' + json.url + '">');
                    }
                    updateMedia(json);
                }

                if (typeof json.error !== 'undefined') {
                    // remove contents of block and display error
                    form.find('input[name="mediaTitle"]').remove();
                    form.find('input[name="mediaDate"]').remove();
                    form.find('textarea[name="mediaDescription"]').remove();
                    progressBarContainer.remove();
                    form.append('<h3>Error</h3><p>' + json.error + '</p>');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                form.find('input[name="mediaTitle"]').remove();
                form.find('input[name="mediaDate"]').remove();
                form.find('textarea[name="mediaDescription"]').remove();
                progressBarContainer.remove();
                form.append('<h3>Error</h3><p>' + textStatus + ': ' + errorThrown + '</p>');
            }
        }).uploadProgress(function (evt) {
            if (evt.lengthComputable) {
                var percentComplete = ((evt.loaded / evt.total) * 100);
                //console.log('Loaded: ' + percentComplete * 100);
                progressBar.attr('aria-valuenow', percentComplete);
                progressBar.text(
                    Math.floor(percentComplete) + '%'
                );
                progressBar.css({
                    width: percentComplete + '%'
                });
            }
        });
    }

    function updateMedia(json) {
        var button = $('button[data-progress="' + json.progressId + '"]');
        var form = button.closest('form');
        if (typeof json.success !== 'undefined') {
            button.click(function (e) {
                e.preventDefault();
                button.button('loading');
                var title = form.find('input[name="mediaTitle"]').val();
                var date = form.find('input[name="mediaDate"]').val();
                var description = form.find('textarea[name="mediaDescription"]').val();
                $.ajax({
                    url: 'index.php?action=editUserMedia',
                    data: {'title': title, 'date': date, 'description': description, 'token': json.token},
                    dataType: 'json',
                    type: 'POST',
                    success: function (response) {
                        if (typeof response.success !== 'undefined') {
                            button.button('reset');
                        }

                        if (typeof response.error !== 'undefined') {
                            alert(response.error);
                        }
                    }
                });
            });
        }

        if (typeof json.error !== 'undefined') {
            alert(json.error);
        }
    }

    function formatPlayer(player) {
        if (!player.id) {
            return player.playerName;
        }

        var suggest = '';
        var isFriend = (player.playerIsFriend === true) ? '<span class="isFriendColor"> <span class="glyphicon glyphicon-star"></span> Friend</span>' : '';
        suggest += '<div class="row">';
        suggest += '<div class="col-sm-3 col-md-3 col-lg-2">';
        suggest += '<img src="' + player.playerPicture + '" alt="' + player.playerName + '" class="img-responsive" style="max-width:80px;">';
        suggest += '</div>';
        suggest += '<div class="col-sm-9 col-md-9 col-lg-10">';
        suggest += '<span>' + player.playerName + '</span>' + isFriend + '<br />';
        suggest += '<span>';
        if (player.playerSchool !== '') {
            suggest += ' ' + player.playerSchool;
        }
        if (player.playerCity !== '') {
            if (player.playerStateLong !== '') {
                suggest += ' <b>(' + player.playerCity + ',  ' + player.playerStateLong + ')</b>';
            } else {
                suggest += ' <b>(' + player.playerCity + ')</b>';
            }
        }
        suggest += '</span><br />';

        if (player.playerGradYear !== '') {
            suggest += '<span>Class of ' + player.playerGradYear + '</span><br />';
        }


        suggest += '</div></div>';
        suggest += '</div>';

        var $player = $(
            suggest
        );
        return $player;
    }

    function formatPlayerSelection(player) {
        // adjust for custom placeholder values
        if (!player.id) {
            return 'Search by Name, School, Grad Year, City, State and Email Address';
        }
        return player.playerName;
    }

    var win = $(window),
        doc = $(document),
        tab1 = $('#tab1'),
        page = 1,
        load = $('<img/>').attr('src', 'images/ajax-loader.gif').addClass('timeline_loader').css({'margin-bottom' : '20px'});

    function getUserTimeline() {

        dettachScrollEvent();

        $.ajax({
            url: 'index.php?action=searchUserTimeline',
            data: {'page': page},
            dataType: 'json',
            type: 'POST',
            beforeSend: function () {
                tab1.append(load);
            },
            success: function (json) {
                if (typeof json.success !== 'undefined') {
                    load.remove();
                    tab1.append(json.content);
                    createPostDropDownOptions();
                    createCommentDropDownOptions();
                    page++;
                }

                if (typeof json.error !== 'undefined') {
                    tab1.find('.no-more-results').remove();
                    if (tab1.find('.single_wall').length > 0) {
                        tab1.append('<h4 class="no-more-results" style="text-align: center; margin-top:20px;">No more activity found</h4>');
                    } else {
                        tab1.append('<h4 class="no-more-results" style="text-align: center; margin-top:20px;">No activity found</h4>');
                    }

                    load.remove();
                }

                attachScrollEvent();
            }
        });
    }

    function attachScrollEvent() {
        win.scroll(function () {
            if (win.scrollTop() + win.height() > doc.height() - 300) {
                getUserTimeline();
            }
        });
    }

    function dettachScrollEvent() {
        win.unbind('scroll');
    }

    getUserTimeline();

    function createCommentDropDownOptions() {
        $('a.dd-comment').not('.active').each(function () {
            $(this).addClass('active');
            $(this).on('click', function (e) {
                e.preventDefault();
                var dd = $(this);
                var token = dd.data('token');
                var type = dd.data('type');
                var id = dd.data('id');
                var relation = dd.data('relation');
                var position = dd.offset();
                if (type === 'post-comment') {
                    var menu = $('.dd-menu-post-comment');
                    menu.css({'left': (position.left - 215) + 'px', 'top': (position.top + 20) + 'px'});
                    menu.show();
                    menu.find('.dd-item-link').each(function () {
                        var a = $(this);
                        var action = a.data('action');
                        a.data('token', token);
                        a.data('type', type);
                        a.data('id', id);
                        a.data('relation', relation);
                        var li = a.parent();
                        if (relation !== 'me' && (action === 'edit' || action === 'delete')) {
                            li.hide();
                        } else if (relation === 'me' && (action === 'hide')) {
                            li.hide();
                        } else {
                            li.show();
                        }
                    });
                }
                if (type === 'media-comment') {
                    var menu = $('.dd-menu-media-comment');
                    menu.css({'left': (position.left - 215) + 'px', 'top': (position.top + 20) + 'px'});
                    menu.show();
                    menu.find('.dd-item-link').each(function () {
                        var a = $(this);
                        var action = a.data('action');
                        a.data('token', token);
                        a.data('type', type);
                        a.data('id', id);
                        a.data('relation', relation);
                        var li = a.parent();
                        if (relation !== 'me' && (action === 'edit' || action === 'delete')) {
                            li.hide();
                        } else if (relation === 'me' && (action === 'hide')) {
                            li.hide();
                        } else {
                            li.show();
                        }
                    });
                }
            });
        });
    }

    function createPostDropDownOptions() {
        $('a.dd-post').not('.active').each(function () {
            $(this).addClass('active');
            $(this).on('click', function (e) {
                e.preventDefault();
                var dd = $(this);
                var token = dd.data('token');
                var type = dd.data('type');
                var id = dd.data('id');
                var relation = dd.data('relation');
                var position = dd.offset();
                if (type === 'post') {
                    var menu = $('.dd-menu-post');
                    menu.css({'left': (position.left - 215) + 'px', 'top': (position.top + 20) + 'px'});
                    menu.show();
                    menu.find('.dd-item-link').each(function () {
                        var a = $(this);
                        var action = a.data('action');
                        a.data('token', token);
                        a.data('type', type);
                        a.data('id', id);
                        a.data('relation', relation);
                        var li = a.parent();
                        if (relation !== 'me' && (action === 'edit' || action === 'delete')) {
                            li.hide();
                        } else if (relation === 'me' && (action === 'unfollow' || action === 'report')) {
                            li.hide();
                        } else {
                            li.show();
                        }
                    });
                }
                if (type === 'media') {
                    var menu = $('.dd-menu-media');
                    menu.css({'left': (position.left - 215) + 'px', 'top': (position.top + 20) + 'px'});
                    menu.show();
                    menu.find('.dd-item-link').each(function () {
                        var a = $(this);
                        var action = a.data('action');
                        a.data('token', token);
                        a.data('type', type);
                        a.data('id', id);
                        a.data('relation', relation);
                        var li = a.parent();
                        if (relation !== 'me' && (action === 'edit' || action === 'delete')) {
                            li.hide();
                        } else if (relation === 'me' && (action === 'unfollow' || action === 'report')) {
                            li.hide();
                        } else {
                            li.show();
                        }
                    });
                }
            });
        });
    }

    $(document).mouseup(function (e) {
        var container = $(".dd-menu-post");
        if (!container.is(e.target) && container.has(e.target).length === 0 && container.is(':visible')) {
            container.hide();
        }
    });

    $(document).mouseup(function (e) {
        var container = $(".dd-menu-media");
        if (!container.is(e.target) && container.has(e.target).length === 0 && container.is(':visible')) {
            container.hide();
        }
    });

    $(document).mouseup(function (e) {
        var container = $(".dd-menu-post-comment");
        if (!container.is(e.target) && container.has(e.target).length === 0 && container.is(':visible')) {
            container.hide();
        }
    });

    $(document).mouseup(function (e) {
        var container = $(".dd-menu-media-comment");
        if (!container.is(e.target) && container.has(e.target).length === 0 && container.is(':visible')) {
            container.hide();
        }
    });

    $(document).on('submit', 'form.comment-form', function (e) {
        e.preventDefault();
        var form = $(this);
        var token = form.data('token');
        var type = form.data('type');
        var textarea = form.find('textarea:eq(0)');
        var message = textarea.val();
        var myButton = form.find('button[type="submit"]');
        var main = form.closest('.like_share_main');
        var comments = form.closest('.single_wall').find('.wall_desc_main:last');
        main.find('.alertMsg').remove();
        myButton.button('loading');
        if (isComplete === false) {
            isComplete = true;
            $.ajax({
                url: 'index.php?action=addUserComment',
                type: 'POST',
                data: {'message': message, 'type': type, 'token': token},
                dataType: 'json',
                success: function (json) {
                    if (typeof json.success !== 'undefined') {
                        comments.after(json.content);
                        textarea.val('');
                        createCommentDropDownOptions();
                    }
                    if (typeof json.error !== 'undefined') {
                        main.prepend(json.error);
                    }
                    isComplete = false;
                    myButton.button('reset');
                }
            });
        }
    });

    $(document).on('click', 'button.like-button', function (e) {
        e.preventDefault();
        var myButton = $(this);
        var token = myButton.data('token');
        var type = myButton.data('type');
        if (type === 'post') {
            $.ajax({
                url: 'index.php?action=likeUserPost',
                type: 'POST',
                data: {'token': token},
                dataType: 'json',
                success: function (json) {
                    if (typeof json.success !== 'undefined') {
                        if (json.like) {
                            myButton.removeClass('btn-gray').addClass('btn-success');
                            myButton.html("<i class='fa fa-thumbs-o-up'></i> Liked");
                        } else {
                            myButton.removeClass('btn-success').addClass('btn-gray');
                            myButton.html(' Like');
                        }
                    }
                    if (typeof json.error !== 'undefined') {
                        myButton.removeClass('btn-success').addClass('btn-gray');
                        myButton.html(' Like');
                    }
                }
            });
        }
        if (type === 'media') {
            $.ajax({
                url: 'index.php?action=likeUserMedia',
                type: 'POST',
                data: {'token': token},
                dataType: 'json',
                success: function (json) {
                    if (typeof json.success !== 'undefined') {
                        if (json.like) {
                            myButton.removeClass('btn-gray').addClass('btn-success');
                            myButton.html("<i class='fa fa-thumbs-o-up'></i> Liked");
                        } else {
                            myButton.removeClass('btn-success').addClass('btn-gray');
                            myButton.html(' Like');
                        }
                    }
                    if (typeof json.error !== 'undefined') {
                        myButton.removeClass('btn-success').addClass('btn-gray');
                        myButton.html(' Like');
                    }
                }
            });
        }
    });

    $('#share_post').on('shown.bs.modal', function (e) {
        var parent = $(this);
        parent.find('.alertMsg').remove();
        var target = $(e.relatedTarget);
        if (target != null) {
            var token = target.data('token');
            parent.find('input[name="token"]').val(token);
        }
    });

    $('#share_media').on('shown.bs.modal', function (e) {
        var parent = $(this);
        parent.find('.alertMsg').remove();
        var target = $(e.relatedTarget);
        if (target != null) {
            var token = target.data('token');
            parent.find('input[name="token"]').val(token);
        }
    });

    $('#report_post').on('shown.bs.modal', function (e) {
        var parent = $(this);
        var target = $(e.relatedTarget);
        if (target != null) {
            var token = target.data('token');
            var id = target.data('id');
            parent.find('input[name="token"]').val(token);
            parent.find('input[name="id"]').val(id);
            $('.dd-menu-post').hide();
        }
    });
    $('#report_media').on('shown.bs.modal', function (e) {
        var parent = $(this);
        var target = $(e.relatedTarget);
        if (target != null) {
            var token = target.data('token');
            var id = target.data('id');
            parent.find('input[name="token"]').val(token);
            parent.find('input[name="id"]').val(id);
            $('.dd-menu-media').hide();
        }
    });

    $('#unfollow_post').on('shown.bs.modal', function (e) {
        var parent = $(this);
        var target = $(e.relatedTarget);
        if (target != null) {
            var token = target.data('token');
            var id = target.data('id');
            parent.find('input[name="token"]').val(token);
            parent.find('input[name="id"]').val(id);
            $('.dd-menu-post').hide();
        }
    });

    $('#unfollow_media').on('shown.bs.modal', function (e) {
        var parent = $(this);
        var target = $(e.relatedTarget);
        if (target != null) {
            var token = target.data('token');
            var id = target.data('id');
            parent.find('input[name="token"]').val(token);
            parent.find('input[name="id"]').val(id);
            $('.dd-menu-media').hide();
        }
    });

    $('#hide_post').on('shown.bs.modal', function (e) {
        var parent = $(this);
        var target = $(e.relatedTarget);
        if (target != null) {
            var token = target.data('token');
            var id = target.data('id');
            parent.find('input[name="token"]').val(token);
            parent.find('input[name="id"]').val(id);
            $('.dd-menu-post').hide();
        }
    });

    $('#hide_post_comment').on('shown.bs.modal', function (e) {
        var parent = $(this);
        var target = $(e.relatedTarget);
        if (target != null) {
            var token = target.data('token');
            var id = target.data('id');
            parent.find('input[name="token"]').val(token);
            parent.find('input[name="id"]').val(id);
            $('.dd-menu-post-comment').hide();
        }
    });

    $('#hide_media').on('shown.bs.modal', function (e) {
        var parent = $(this);
        var target = $(e.relatedTarget);
        if (target != null) {
            var token = target.data('token');
            var id = target.data('id');
            parent.find('input[name="token"]').val(token);
            parent.find('input[name="id"]').val(id);
            $('.dd-menu-media').hide();
        }
    });

    $('#hide_media_comment').on('shown.bs.modal', function (e) {
        var parent = $(this);
        var target = $(e.relatedTarget);
        if (target != null) {
            var token = target.data('token');
            var id = target.data('id');
            parent.find('input[name="token"]').val(token);
            parent.find('input[name="id"]').val(id);
            $('.dd-menu-media-comment').hide();
        }
    });

    $('#edit_post').on('shown.bs.modal', function (e) {
        var parent = $(this);
        parent.find('.alertMsg').remove();
        var target = $(e.relatedTarget);
        if (target != null) {
            var token = target.data('token');
            var id = target.data('id');
            parent.find('input[name="token"]').val(token);
            parent.find('input[name="id"]').val(id);
            getUserPost(token);
            $('.dd-menu-post').hide();
        }
    });

    $('#edit_post_comment').on('shown.bs.modal', function (e) {
        var parent = $(this);
        parent.find('.alertMsg').remove();
        var target = $(e.relatedTarget);
        if (target != null) {
            var token = target.data('token');
            var id = target.data('id');
            parent.find('input[name="token"]').val(token);
            parent.find('input[name="id"]').val(id);
            getUserPostComment(token);
            $('.dd-menu-post-comment').hide();
        }
    });

    $('#edit_media').on('shown.bs.modal', function (e) {
        var parent = $(this);
        parent.find('.alertMsg').remove();
        var target = $(e.relatedTarget);
        if (target != null) {
            var token = target.data('token');
            var id = target.data('id');
            parent.find('input[name="token"]').val(token);
            parent.find('input[name="id"]').val(id);
            getUserMedia(token);
            $('.dd-menu-media').hide();
        }
    });

    $('#edit_media_comment').on('shown.bs.modal', function (e) {
        var parent = $(this);
        parent.find('.alertMsg').remove();
        var target = $(e.relatedTarget);
        if (target != null) {
            var token = target.data('token');
            var id = target.data('id');
            parent.find('input[name="token"]').val(token);
            parent.find('input[name="id"]').val(id);
            getUserMediaComment(token);
            $('.dd-menu-media-comment').hide();
        }
    });

    $('#delete_post').on('shown.bs.modal', function (e) {
        var parent = $(this);
        parent.find('.alertMsg').remove();
        var target = $(e.relatedTarget);
        if (target != null) {
            var token = target.data('token');
            var id = target.data('id');
            parent.find('input[name="token"]').val(token);
            parent.find('input[name="id"]').val(id);
            $('.dd-menu-post').hide();
        }
    });

    $('#delete_post_comment').on('shown.bs.modal', function (e) {
        var parent = $(this);
        parent.find('.alertMsg').remove();
        var target = $(e.relatedTarget);
        if (target != null) {
            var token = target.data('token');
            var id = target.data('id');
            parent.find('input[name="token"]').val(token);
            parent.find('input[name="id"]').val(id);
            $('.dd-menu-post-comment').hide();
        }
    });

    $('#delete_media').on('shown.bs.modal', function (e) {
        var parent = $(this);
        parent.find('.alertMsg').remove();
        var target = $(e.relatedTarget);
        if (target != null) {
            var token = target.data('token');
            var id = target.data('id');
            parent.find('input[name="token"]').val(token);
            parent.find('input[name="id"]').val(id);
            $('.dd-menu-media').hide();
        }
    });

    $('#delete_media_comment').on('shown.bs.modal', function (e) {
        var parent = $(this);
        parent.find('.alertMsg').remove();
        var target = $(e.relatedTarget);
        if (target != null) {
            var token = target.data('token');
            var id = target.data('id');
            parent.find('input[name="token"]').val(token);
            parent.find('input[name="id"]').val(id);
            $('.dd-menu-media-comment').hide();
        }
    });

    function getUserPost(token) {
        var form = $('#editUserPostForm');
        var textarea = form.find('textarea:eq(0)');
        textarea.val('');
        $.ajax({
            url: 'index.php?action=getUserPost',
            type: 'POST',
            data: {'token': token},
            dataType: 'json',
            success: function (json) {
                if (typeof json.success !== 'undefined') {
                    textarea.val(json.content);
                }
                if (typeof json.error !== 'undefined') {
                    form.prepend(json.error);
                }
            }
        });
    }

    function getUserPostComment(token) {
        var form = $('#editUserPostCommentForm');
        var textarea = form.find('textarea:eq(0)');
        textarea.val('');
        $.ajax({
            url: 'index.php?action=getUserPostComment',
            type: 'POST',
            data: {'token': token},
            dataType: 'json',
            success: function (json) {
                if (typeof json.success !== 'undefined') {
                    textarea.val(json.content);
                }
                if (typeof json.error !== 'undefined') {
                    form.prepend(json.error);
                }
            }
        });
    }

    function getUserMedia(token) {
        var form = $('#editUserMediaForm');
        var title = form.find('input[name="media_title"]');
        var description = form.find('textarea[name="media_description"]');
        title.val('');
        description.val('');
        $.ajax({
            url: 'index.php?action=getUserMedia',
            type: 'POST',
            data: {'token': token},
            dataType: 'json',
            success: function (json) {
                if (typeof json.success !== 'undefined') {
                    title.val(json.title);
                    description.val(json.description);
                }
                if (typeof json.error !== 'undefined') {
                    form.prepend(json.error);
                }
            }
        });
    }

    function getUserMediaComment(token) {
        var form = $('#editUserMediaCommentForm');
        var textarea = form.find('textarea:eq(0)');
        textarea.val('');
        $.ajax({
            url: 'index.php?action=getUserMediaComment',
            type: 'POST',
            data: {'token': token},
            dataType: 'json',
            success: function (json) {
                if (typeof json.success !== 'undefined') {
                    textarea.val(json.content);
                }
                if (typeof json.error !== 'undefined') {
                    form.prepend(json.error);
                }
            }
        });
    }

    $('#editUserPostForm').on('submit', function (e) {
        e.preventDefault();
        var form = $(this);
        var token = form.find('input[name="token"]').val();
        var id = form.find('input[name="id"]').val();
        var content = $('#post_' + id).find('.wall_desc_main:first').find('.desc_content');
        var textarea = form.find('textarea:eq(0)');
        var message = textarea.val();
        var myButton = form.find('button[type="submit"]');
        form.find('.alertMsg').remove();
        myButton.button('loading');
        if (isComplete === false) {
            isComplete = true;
            $.ajax({
                url: 'index.php?action=editUserPost',
                type: 'POST',
                data: {'message': message, 'token': token},
                dataType: 'json',
                success: function (json) {
                    if (typeof json.success !== 'undefined') {
                        $('#edit_post').modal('hide');
                        content.html(json.content);
                    }
                    if (typeof json.error !== 'undefined') {
                        form.prepend(json.error);
                    }
                    isComplete = false;
                    myButton.button('reset');
                }
            });
        }
    });

    $('#editUserPostCommentForm').on('submit', function (e) {
        e.preventDefault();
        var form = $(this);
        var token = form.find('input[name="token"]').val();
        var id = form.find('input[name="id"]').val();
        var content = $('#comment_' + id).find('.msg_content');
        var textarea = form.find('textarea:eq(0)');
        var message = textarea.val();
        var myButton = form.find('button[type="submit"]');
        form.find('.alertMsg').remove();
        myButton.button('loading');
        if (isComplete === false) {
            isComplete = true;
            $.ajax({
                url: 'index.php?action=editUserPostComment',
                type: 'POST',
                data: {'message': message, 'token': token},
                dataType: 'json',
                success: function (json) {
                    if (typeof json.success !== 'undefined') {
                        $('#edit_post_comment').modal('hide');
                        content.html(json.content);
                    }
                    if (typeof json.error !== 'undefined') {
                        form.prepend(json.error);
                    }
                    isComplete = false;
                    myButton.button('reset');
                }
            });
        }
    });

    $('#editUserMediaForm').on('submit', function (e) {
        e.preventDefault();
        var form = $(this);
        var token = form.find('input[name="token"]').val();
        var id = form.find('input[name="id"]').val();
        var parent = $('#post_' + id);
        var title = parent.find('.wall_desc_main:first').find('.wall_title');
        var description = parent.find('.wall_desc_main:first').find('.desc_content');
        var media_title = form.find('input[name="media_title"]').val();
        var media_description = form.find('textarea[name="media_description"]').val();
        var myButton = form.find('button[type="submit"]');
        form.find('.alertMsg').remove();
        myButton.button('loading');
        if (isComplete === false) {
            isComplete = true;
            $.ajax({
                url: 'index.php?action=editUserMedia',
                type: 'POST',
                data: {'title': media_title, 'description': media_description, 'token': token},
                dataType: 'json',
                success: function (json) {
                    if (typeof json.success !== 'undefined') {
                        $('#edit_media').modal('hide');
                        title.html('<h5>' + json.title + '</h5>');
                        description.html(json.description);
                    }
                    if (typeof json.error !== 'undefined') {
                        form.prepend(json.error);
                    }
                    isComplete = false;
                    myButton.button('reset');
                }
            });
        }
    });

    $('#editUserMediaCommentForm').on('submit', function (e) {
        e.preventDefault();
        var form = $(this);
        var token = form.find('input[name="token"]').val();
        var id = form.find('input[name="id"]').val();
        var content = $('#comment_' + id).find('.msg_content');
        var textarea = form.find('textarea:eq(0)');
        var message = textarea.val();
        var myButton = form.find('button[type="submit"]');
        form.find('.alertMsg').remove();
        myButton.button('loading');
        if (isComplete === false) {
            isComplete = true;
            $.ajax({
                url: 'index.php?action=editUserMediaComment',
                type: 'POST',
                data: {'message': message, 'token': token},
                dataType: 'json',
                success: function (json) {
                    if (typeof json.success !== 'undefined') {
                        $('#edit_media_comment').modal('hide');
                        content.html(json.content);
                    }
                    if (typeof json.error !== 'undefined') {
                        form.prepend(json.error);
                    }
                    isComplete = false;
                    myButton.button('reset');
                }
            });
        }
    });

    $('#deleteUserPostForm').on('submit', function (e) {
        e.preventDefault();
        var form = $(this);
        var token = form.find('input[name="token"]').val();
        var id = form.find('input[name="id"]').val();
        var parent = $('#post_' + id);
        var myButton = form.find('button[type="submit"]');
        form.find('.alertMsg').remove();
        myButton.button('loading');
        if (isComplete === false) {
            isComplete = true;
            $.ajax({
                url: 'index.php?action=deleteUserPost',
                type: 'POST',
                data: {'token': token},
                dataType: 'json',
                success: function (json) {
                    if (typeof json.success !== 'undefined') {
                        $('#delete_post').modal('hide');
                        parent.remove();
                    }
                    if (typeof json.error !== 'undefined') {
                        form.prepend(json.error);
                    }
                    isComplete = false;
                    myButton.button('reset');
                }
            });
        }
    });

    $('#deleteUserPostCommentForm').on('submit', function (e) {
        e.preventDefault();
        var form = $(this);
        var token = form.find('input[name="token"]').val();
        var id = form.find('input[name="id"]').val();
        var parent = $('#comment_' + id);
        var myButton = form.find('button[type="submit"]');
        form.find('.alertMsg').remove();
        myButton.button('loading');
        if (isComplete === false) {
            isComplete = true;
            $.ajax({
                url: 'index.php?action=deleteUserPostComment',
                type: 'POST',
                data: {'token': token},
                dataType: 'json',
                success: function (json) {
                    if (typeof json.success !== 'undefined') {
                        $('#delete_post_comment').modal('hide');
                        parent.remove();
                    }
                    if (typeof json.error !== 'undefined') {
                        form.prepend(json.error);
                    }
                    isComplete = false;
                    myButton.button('reset');
                }
            });
        }
    });

    $('#deleteUserMediaForm').on('submit', function (e) {
        e.preventDefault();
        var form = $(this);
        var token = form.find('input[name="token"]').val();
        var id = form.find('input[name="id"]').val();
        var parent = $('#post_' + id);
        var myButton = form.find('button[type="submit"]');
        form.find('.alertMsg').remove();
        myButton.button('loading');
        if (isComplete === false) {
            isComplete = true;
            $.ajax({
                url: 'index.php?action=deleteUserMedia',
                type: 'POST',
                data: {'token': token},
                dataType: 'json',
                success: function (json) {
                    if (typeof json.success !== 'undefined') {
                        $('#delete_media').modal('hide');
                        parent.remove();
                    }
                    if (typeof json.error !== 'undefined') {
                        form.prepend(json.error);
                    }
                    isComplete = false;
                    myButton.button('reset');
                }
            });
        }
    });

    $('#deleteUserMediaCommentForm').on('submit', function (e) {
        e.preventDefault();
        var form = $(this);
        var token = form.find('input[name="token"]').val();
        var id = form.find('input[name="id"]').val();
        var parent = $('#comment_' + id);
        var myButton = form.find('button[type="submit"]');
        form.find('.alertMsg').remove();
        myButton.button('loading');
        if (isComplete === false) {
            isComplete = true;
            $.ajax({
                url: 'index.php?action=deleteUserMediaComment',
                type: 'POST',
                data: {'token': token},
                dataType: 'json',
                success: function (json) {
                    if (typeof json.success !== 'undefined') {
                        $('#delete_media_comment').modal('hide');
                        parent.remove();
                    }
                    if (typeof json.error !== 'undefined') {
                        form.prepend(json.error);
                    }
                    isComplete = false;
                    myButton.button('reset');
                }
            });
        }
    });

    $('#hideUserPostForm').on('submit', function (e) {
        e.preventDefault();
        var form = $(this);
        var token = form.find('input[name="token"]').val();
        var id = form.find('input[name="id"]').val();
        var parent = $('#post_' + id);
        var myButton = form.find('button[type="submit"]');
        form.find('.alertMsg').remove();
        myButton.button('loading');
        if (isComplete === false) {
            isComplete = true;
            $.ajax({
                url: 'index.php?action=hideUserPost',
                type: 'POST',
                data: {'token': token},
                dataType: 'json',
                success: function (json) {
                    if (typeof json.success !== 'undefined') {
                        $('#hide_post').modal('hide');
                        parent.remove();
                    }
                    if (typeof json.error !== 'undefined') {
                        form.prepend(json.error);
                    }
                    isComplete = false;
                    myButton.button('reset');
                }
            });
        }
    });

    $('#hideUserPostCommentForm').on('submit', function (e) {
        e.preventDefault();
        var form = $(this);
        var token = form.find('input[name="token"]').val();
        var id = form.find('input[name="id"]').val();
        var parent = $('#comment_' + id);
        var myButton = form.find('button[type="submit"]');
        form.find('.alertMsg').remove();
        myButton.button('loading');
        if (isComplete === false) {
            isComplete = true;
            $.ajax({
                url: 'index.php?action=hideUserPostComment',
                type: 'POST',
                data: {'token': token},
                dataType: 'json',
                success: function (json) {
                    if (typeof json.success !== 'undefined') {
                        $('#hide_post_comment').modal('hide');
                        parent.remove();
                    }
                    if (typeof json.error !== 'undefined') {
                        form.prepend(json.error);
                    }
                    isComplete = false;
                    myButton.button('reset');
                }
            });
        }
    });

    $('#hideUserMediaForm').on('submit', function (e) {
        e.preventDefault();
        var form = $(this);
        var token = form.find('input[name="token"]').val();
        var id = form.find('input[name="id"]').val();
        var parent = $('#post_' + id);
        var myButton = form.find('button[type="submit"]');
        form.find('.alertMsg').remove();
        myButton.button('loading');
        if (isComplete === false) {
            isComplete = true;
            $.ajax({
                url: 'index.php?action=hideUserMedia',
                type: 'POST',
                data: {'token': token},
                dataType: 'json',
                success: function (json) {
                    if (typeof json.success !== 'undefined') {
                        $('#hide_media').modal('hide');
                        parent.remove();
                    }
                    if (typeof json.error !== 'undefined') {
                        form.prepend(json.error);
                    }
                    isComplete = false;
                    myButton.button('reset');
                }
            });
        }
    });

    $('#hideUserMediaCommentForm').on('submit', function (e) {
        e.preventDefault();
        var form = $(this);
        var token = form.find('input[name="token"]').val();
        var id = form.find('input[name="id"]').val();
        var parent = $('#comment_' + id);
        var myButton = form.find('button[type="submit"]');
        form.find('.alertMsg').remove();
        myButton.button('loading');
        if (isComplete === false) {
            isComplete = true;
            $.ajax({
                url: 'index.php?action=hideUserMediaComment',
                type: 'POST',
                data: {'token': token},
                dataType: 'json',
                success: function (json) {
                    if (typeof json.success !== 'undefined') {
                        $('#hide_media_comment').modal('hide');
                        parent.remove();
                    }
                    if (typeof json.error !== 'undefined') {
                        form.prepend(json.error);
                    }
                    isComplete = false;
                    myButton.button('reset');
                }
            });
        }
    });

    $('#unfollowUserPostForm').on('submit', function (e) {
        e.preventDefault();
        var form = $(this);
        var token = form.find('input[name="token"]').val();
        var id = form.find('input[name="id"]').val();
        var parent = $('#post_' + id);
        var myButton = form.find('button[type="submit"]');
        form.find('.alertMsg').remove();
        myButton.button('loading');
        if (isComplete === false) {
            isComplete = true;
            $.ajax({
                url: 'index.php?action=unfollowUserPost',
                type: 'POST',
                data: {'token': token},
                dataType: 'json',
                success: function (json) {
                    if (typeof json.success !== 'undefined') {
                        $('#unfollow_post').modal('hide');
                        parent.remove();
                    }
                    if (typeof json.error !== 'undefined') {
                        form.prepend(json.error);
                    }
                    isComplete = false;
                    myButton.button('reset');
                }
            });
        }
    });

    $('#unfollowUserMediaForm').on('submit', function (e) {
        e.preventDefault();
        var form = $(this);
        var token = form.find('input[name="token"]').val();
        var id = form.find('input[name="id"]').val();
        var parent = $('#post_' + id);
        var myButton = form.find('button[type="submit"]');
        form.find('.alertMsg').remove();
        myButton.button('loading');
        if (isComplete === false) {
            isComplete = true;
            $.ajax({
                url: 'index.php?action=unfollowUserMedia',
                type: 'POST',
                data: {'token': token},
                dataType: 'json',
                success: function (json) {
                    if (typeof json.success !== 'undefined') {
                        $('#unfollow_media').modal('hide');
                        parent.remove();
                    }
                    if (typeof json.error !== 'undefined') {
                        form.prepend(json.error);
                    }
                    isComplete = false;
                    myButton.button('reset');
                }
            });
        }
    });

    $('#reportUserPostForm').on('submit', function (e) {
        e.preventDefault();
        var form = $(this);
        var token = form.find('input[name="token"]').val();
        var id = form.find('input[name="id"]').val();
        var parent = $('#post_' + id);
        var myButton = form.find('button[type="submit"]');
        form.find('.alertMsg').remove();
        myButton.button('loading');
        if (isComplete === false) {
            isComplete = true;
            $.ajax({
                url: 'index.php?action=reportUserPost',
                type: 'POST',
                data: form.serializeArray(),
                dataType: 'json',
                success: function (json) {
                    if (typeof json.success !== 'undefined') {
                        form.prepend(json.success);
                        parent.remove();
                    }
                    if (typeof json.error !== 'undefined') {
                        form.prepend(json.error);
                    }
                    isComplete = false;
                    myButton.button('reset');
                }
            });
        }
    });

    $('#reportUserMediaForm').on('submit', function (e) {
        e.preventDefault();
        var form = $(this);
        var token = form.find('input[name="token"]').val();
        var id = form.find('input[name="id"]').val();
        var parent = $('#post_' + id);
        var myButton = form.find('button[type="submit"]');
        form.find('.alertMsg').remove();
        myButton.button('loading');
        if (isComplete === false) {
            isComplete = true;
            $.ajax({
                url: 'index.php?action=reportUserMedia',
                type: 'POST',
                data: form.serializeArray(),
                dataType: 'json',
                success: function (json) {
                    if (typeof json.success !== 'undefined') {
                        form.prepend(json.success);
                        parent.remove();
                    }
                    if (typeof json.error !== 'undefined') {
                        form.prepend(json.error);
                    }
                    isComplete = false;
                    myButton.button('reset');
                }
            });
        }
    });

    $('#shareUserPostForm').on('submit', function (e) {
        e.preventDefault();
        var form = $(this);
        var token = form.find('input[name="token"]').val();
        var myButton = form.find('button[type="submit"]');
        form.find('.alertMsg').remove();
        myButton.button('loading');
        if (isComplete === false) {
            isComplete = true;
            $.ajax({
                url: 'index.php?action=shareUserPost',
                type: 'POST',
                data: form.serializeArray(),
                dataType: 'json',
                success: function (json) {
                    if (typeof json.success !== 'undefined') {
                        form.prepend(json.success);
                        //$('#share_post').hide();
                    }
                    if (typeof json.error !== 'undefined') {
                        form.prepend(json.error);
                    }
                    isComplete = false;
                    myButton.button('reset');
                }
            });
        }
    });

    $('#shareUserMediaForm').on('submit', function (e) {
        e.preventDefault();
        var form = $(this);
        var token = form.find('input[name="token"]').val();
        var myButton = form.find('button[type="submit"]');
        form.find('.alertMsg').remove();
        myButton.button('loading');
        if (isComplete === false) {
            isComplete = true;
            $.ajax({
                url: 'index.php?action=shareUserMedia',
                type: 'POST',
                data: form.serializeArray(),
                dataType: 'json',
                success: function (json) {
                    if (typeof json.success !== 'undefined') {
                        form.prepend(json.success);
                        //$('#share_media').hide();
                    }
                    if (typeof json.error !== 'undefined') {
                        form.prepend(json.error);
                    }
                    isComplete = false;
                    myButton.button('reset');
                }
            });
        }
    });

</script>
</body>
</html>
