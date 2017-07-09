<?php

if (!isset($_SESSION['userId'])) {
    header('Location: index.php?page=login');
    exit;
}

$eventId = isset($_GET['id']) ? $_GET['id'] : '';
$eventId = preg_replace("/[^0-9]/", "", $eventId);
$eventAdminId = null;
$sportName = '';
$sportNameLowercase = '';

// get the PlayerAdminIds
$eventAdminIds = array();
try {
    $sql = "
    SELECT 
      `EventAdmins`.`UserId`
    FROM 
      `EventAdmins` 
    WHERE
      `EventAdmins`.`EventId` = :EventId";

    $stmt = $PDO->prepare($sql);
    $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        array_push($eventAdminIds, $row['UserId']);
    }

} catch (PDOException $e) {
    trigger_error($e->getMessage(), E_USER_ERROR);
}//end try

// make sure this eventId is valid
$count = 0;
try {
    $sql = "
    SELECT 
      `Events`.`EventId`, 
      `Events`.`UserId`,
      `Events`.`Sport`
    FROM 
      `Events` 
    WHERE
      `Events`.`EventId` = :EventId";

    $stmt = $PDO->prepare($sql);
    $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $eventAdminId = $row['UserId'];
        $sportName = getSportName($row['Sport']);
        $sportNameLowercase = strtolower(str_replace(' ', '', $sportName));
        $count++;
    }

} catch (PDOException $e) {
    trigger_error($e->getMessage(), E_USER_ERROR);
}//end try

if ($count == 0) {
    header('Location: index.php?page=404');
    exit;
}

if (!in_array($userId, $eventAdminIds)) {
    header('Location: index.php?page=404');
    exit;
}

$states = array(
    'AL' => 'Alabama',
    'AK' => 'Alaska',
    'AZ' => 'Arizona',
    'AR' => 'Arkansas',
    'CA' => 'California',
    'CO' => 'Colorado',
    'CT' => 'Connecticut',
    'DE' => 'Delaware',
    'DC' => 'District Of Columbia',
    'FL' => 'Florida',
    'GA' => 'Georgia',
    'HI' => 'Hawaii',
    'ID' => 'Idaho',
    'IL' => 'Illinois',
    'IN' => 'Indiana',
    'IA' => 'Iowa',
    'KS' => 'Kansas',
    'KY' => 'Kentucky',
    'LA' => 'Louisiana',
    'ME' => 'Maine',
    'MD' => 'Maryland',
    'MA' => 'Massachusetts',
    'MI' => 'Michigan',
    'MN' => 'Minnesota',
    'MS' => 'Mississippi',
    'MO' => 'Missouri',
    'MT' => 'Montana',
    'NE' => 'Nebraska',
    'NV' => 'Nevada',
    'NH' => 'New Hampshire',
    'NJ' => 'New Jersey',
    'NM' => 'New Mexico',
    'NY' => 'New York',
    'NC' => 'North Carolina',
    'ND' => 'North Dakota',
    'OH' => 'Ohio',
    'OK' => 'Oklahoma',
    'OR' => 'Oregon',
    'PA' => 'Pennsylvania',
    'RI' => 'Rhode Island',
    'SC' => 'South Carolina',
    'SD' => 'South Dakota',
    'TN' => 'Tennessee',
    'TX' => 'Texas',
    'UT' => 'Utah',
    'VT' => 'Vermont',
    'VA' => 'Virginia',
    'WA' => 'Washington',
    'WV' => 'West Virginia',
    'WI' => 'Wisconsin',
    'WY' => 'Wyoming',
    'AS' => 'American Samoa',
    'GU' => 'Guam',
    'MP' => 'Northern Mariana Islands',
    'PR' => 'Puerto Rico',
    'UM' => 'United States Minor Outlying Islands',
    'VI' => 'Virgin Islands',
    'AA' => 'Armed Forces Americas',
    'AP' => 'Armed Forces Pacific',
    'AE' => 'Armed Forces Others'
);

$zones = array(
    'USA' => array(
        'America/New_York' => 'Eastern Time',
        'America/Chicago' => 'Central Time',
        'America/Denver' => 'Mountain Time',
        'America/Phoenix' => 'Mountain Time no DST',
        'America/Los_Angeles' => 'Pacific Time',
        'America/Anchorage' => 'Alaska Time',
        'America/Adak' => 'Hawaii Time',
        'Pacific/Honolulu' => 'Hawaii Time no DST'
    ),
    'Chronological Order' => array(
        'Pacific/Kiritimati' => 'Line Is. Time',
        'Pacific/Enderbury' => 'Phoenix Is.Time',
        'Pacific/Tongatapu' => 'Tonga Time',
        'Pacific/Chatham' => 'Chatham Standard Time',
        'Pacific/Auckland' => 'New Zealand Standard Time',
        'Pacific/Fiji' => 'Fiji Time',
        'Asia/Kamchatka' => 'Petropavlovsk-Kamchatski Time',
        'Pacific/Norfolk' => 'Norfolk Time',
        'Australia/Lord_Howe' => 'Lord Howe Standard Time',
        'Pacific/Guadalcanal' => 'Solomon Is. Time',
        'Australia/Adelaide' => 'Australian Central Standard Time (South Australia)',
        'Australia/Sydney' => 'Australian Eastern StandardTime (New South Wales)',
        'Australia/Brisbane' => 'Australian Eastern Standard Time (Queensland)',
        'Australia/Darwin' => 'Australian Central Standard Time (Northern Territory)',
        'Asia/Seoul' => 'Korea Standard Time',
        'Asia/Tokyo' => 'Japan Standard Time',
        'Asia/Hong_Kong' => 'Hong Kong Time',
        'Asia/Kuala_Lumpur' => 'Malaysia Time',
        'Asia/Manila' => 'Philippines Time',
        'Asia/Shanghai' => 'China Standard Time',
        'Asia/Singapore' => 'Singapore Time',
        'Asia/Taipei' => 'China Standard Time',
        'Australia/Perth' => 'Australian Western Standard Time',
        'Asia/Bangkok' => 'Indochina Time',
        'Asia/Ho_Chi_Minh' => 'Indochina Time',
        'Asia/Jakarta' => 'West Indonesia Time',
        'Asia/Rangoon' => 'Myanmar Time',
        'Asia/Dhaka' => 'Bangladesh Time',
        'Asia/Kathmandu' => 'Nepal Time',
        'Asia/Colombo' => 'India Standard Time',
        'Asia/Kolkata' => 'India Standard Time',
        'Asia/Karachi' => 'Pakistan Time',
        'Asia/Tashkent' => 'Uzbekistan Time',
        'Asia/Yekaterinburg' => 'Yekaterinburg Time',
        'Asia/Kabul' => 'Afghanistan Time',
        'Asia/Baku' => 'Azerbaijan Summer Time',
        'Asia/Dubai' => 'Gulf Standard Time',
        'Asia/Tbilisi' => 'Georgia Time',
        'Asia/Yerevan' => 'Armenia Time',
        'Asia/Tehran' => 'Iran Daylight Time',
        'Africa/Nairobi' => 'East African Time',
        'Asia/Baghdad' => 'Arabia Standard Time',
        'Asia/Kuwait' => 'Arabia Standard Time',
        'Asia/Riyadh' => 'Arabia Standard Time',
        'Europe/Minsk' => 'Moscow Standard Time',
        'Europe/Moscow' => 'Moscow Standard Time',
        'Africa/Cairo' => 'Eastern European Summer Time',
        'Asia/Beirut' => 'Eastern European Summer Time',
        'Asia/Jerusalem' => 'Israel Daylight Time',
        'Europe/Athens' => 'Eastern European Summer Time',
        'Europe/Bucharest' => 'Eastern European Summer Time',
        'Europe/Helsinki' => 'Eastern European Summer Time',
        'Europe/Istanbul' => 'Eastern European Summer Time',
        'Africa/Johannesburg' => 'South Africa Standard Time',
        'Europe/Amsterdam' => 'Central European Summer Time',
        'Europe/Berlin' => 'Central European Summer Time',
        'Europe/Brussels' => 'Central European Summer Time',
        'Europe/Paris' => 'Central European Summer Time',
        'Europe/Prague' => 'Central European Summer Time',
        'Europe/Rome' => 'Central European Summer Time',
        'Europe/Lisbon' => 'Western European Summer Time',
        'Africa/Algiers' => 'Central European Time',
        'Europe/London' => 'British Summer Time',
        'Atlantic/Cape_Verde' => 'Cape Verde Time',
        'Africa/Casablanca' => 'Western European Time',
        'Europe/Dublin' => 'Irish Summer Time',
        'GMT' => 'Greenwich Mean Time',
        'America/Scoresbysund' => 'Eastern Greenland Summer Time',
        'Atlantic/Azores' => 'Azores Summer Time',
        'Atlantic/South_Georgia' => 'South Georgia Standard Time',
        'America/St_Johns' => 'Newfoundland Daylight Time',
        'America/Sao_Paulo' => 'Brasilia Summer Time',
        'America/Argentina/Buenos_Aires' => 'Argentina Time',
        'America/Santiago' => 'Chile Summer Time',
        'America/Halifax' => 'Atlantic Daylight Time',
        'America/Puerto_Rico' => 'Atlantic Standard Time',
        'Atlantic/Bermuda' => 'Atlantic Daylight Time',
        'America/Caracas' => 'Venezuela Time',
        'America/Indiana/Indianapolis' => 'Eastern Daylight Time',
        'America/New_York' => 'Eastern Daylight Time',
        'America/Bogota' => 'Colombia Time',
        'America/Lima' => 'Peru Time',
        'America/Panama' => 'Eastern Standard Time',
        'America/Mexico_City' => 'Central Daylight Time',
        'America/Chicago' => 'Central Daylight Time',
        'America/El_Salvador' => 'Central Standard Time',
        'America/Denver' => 'Mountain Daylight Time',
        'America/Mazatlan' => 'Mountain Standard Time',
        'America/Phoenix' => 'Mountain Standard Time',
        'America/Los_Angeles' => 'Pacific Daylight Time',
        'America/Tijuana' => 'Pacific Daylight Time',
        'Pacific/Pitcairn' => 'Pitcairn Standard Time',
        'America/Anchorage' => 'Alaska Daylight Time',
        'Pacific/Gambier' => 'Gambier Time',
        'America/Adak' => 'Hawaii-Aleutian Standard Time',
        'Pacific/Marquesas' => 'Marquesas Time',
        'Pacific/Honolulu' => 'Hawaii-Aleutian Standard Time',
        'Pacific/Niue' => 'Niue Time',
        'Pacific/Pago_Pago' => 'Samoa Standard Time'
    )
);

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
    <style>
        .dataTables_length {
            float: left;
        }

        .dataTables_filter {
            display: none;
        }

        .dt-buttons {
            float: left;
            margin-right: 10px;
        }

        .dt-buttons a {
            margin-right: 10px;
        }

        .dt-button-info {
            z-index: 99999 !important;
        }

        #pageLoadingSpinner {
            font-size: 14px;
            color: #555555;
            background-color: #ffd700;
        }

        .table-responsive {
            border: none !important;
        }
    </style>
</head>

<body class="user_home" id="portrait">
<?php require 'includes/header.php'; ?>
<section class="user_profile_main">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">

                <a href="index.php?page=events" class="btn btn-primary"> <i class="fa fa-chevron-left" aria-hidden="true"></i> Back To Events</a>

                <div id="options" class="dropdown pull-right">
                    <button class="btn btn-default dropdown-toggle" type="button" id="menu1" data-toggle="dropdown">Options
                        <span class="caret"></span></button>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
                        <li role="presentation" class="dropdown-header">Fast Switching</li>
                        <li id="fps" role="presentation"><a role="menuitem" tabindex="-1" href="#"><i class="fa fa-square-o text-muted" aria-hidden="true"></i> Player</a></li>
                        <li role="presentation" class="divider"></li>
                        <li role="presentation" class="dropdown-header">Activity</li>
                        <?php if ($sportNameLowercase == 'baseball'): ?>
                            <li class="fas" role="presentation" data-activity="VelocityMound"><a role="menuitem" tabindex="-1" href="#"><i class="fa fa-square-o text-muted" aria-hidden="true"></i> Velocity Mound</a></li>
                            <li class="fas" role="presentation" data-activity="VelocityOutfield"><a role="menuitem" tabindex="-1" href="#"><i class="fa fa-square-o text-muted" aria-hidden="true"></i> Velocity Outfield</a></li>
                            <li class="fas" role="presentation" data-activity="VelocityInfield"><a role="menuitem" tabindex="-1" href="#"><i class="fa fa-square-o text-muted" aria-hidden="true"></i> Velocity Infield</a></li>
                            <li class="fas" role="presentation" data-activity="SwingVelocity"><a role="menuitem" tabindex="-1" href="#"><i class="fa fa-square-o text-muted" aria-hidden="true"></i> Swing Velocity</a></li>
                            <li class="fas" role="presentation" data-activity="60YardDash"><a role="menuitem" tabindex="-1" href="#"><i class="fa fa-square-o text-muted" aria-hidden="true"></i> 60 Yard Dash</a></li>
                            <li class="fas" role="presentation" data-activity="CatcherPop"><a role="menuitem" tabindex="-1" href="#"><i class="fa fa-square-o text-muted" aria-hidden="true"></i> Catcher Pop</a></li>
                            <li class="fas" role="presentation" data-activity="CatcherRelease"><a role="menuitem" tabindex="-1" href="#"><i class="fa fa-square-o text-muted" aria-hidden="true"></i> Catcher Release</a></li>
                            <li class="fas" role="presentation" data-activity="TeeVelocity"><a role="menuitem" tabindex="-1" href="#"><i class="fa fa-square-o text-muted" aria-hidden="true"></i> Tee Velocity</a></li>
                        <?php endif; ?>
                        <?php if ($sportNameLowercase == 'fastpitch'): ?>
                            <li class="fas" role="presentation" data-activity="VelocityMound"><a role="menuitem" tabindex="-1" href="#"><i class="fa fa-square-o text-muted" aria-hidden="true"></i> Velocity Mound</a></li>
                            <li class="fas" role="presentation" data-activity="VelocityOutfield"><a role="menuitem" tabindex="-1" href="#"><i class="fa fa-square-o text-muted" aria-hidden="true"></i> Velocity Outfield</a></li>
                            <li class="fas" role="presentation" data-activity="VelocityInfield"><a role="menuitem" tabindex="-1" href="#"><i class="fa fa-square-o text-muted" aria-hidden="true"></i> Velocity Infield</a></li>
                            <li class="fas" role="presentation" data-activity="SwingVelocity"><a role="menuitem" tabindex="-1" href="#"><i class="fa fa-square-o text-muted" aria-hidden="true"></i> Swing Velocity</a></li>
                            <li class="fas" role="presentation" data-activity="60YardDash"><a role="menuitem" tabindex="-1" href="#"><i class="fa fa-square-o text-muted" aria-hidden="true"></i> 60 Yard Dash</a></li>
                            <li class="fas" role="presentation" data-activity="CatcherPop"><a role="menuitem" tabindex="-1" href="#"><i class="fa fa-square-o text-muted" aria-hidden="true"></i> Catcher Pop</a></li>
                            <li class="fas" role="presentation" data-activity="CatcherRelease"><a role="menuitem" tabindex="-1" href="#"><i class="fa fa-square-o text-muted" aria-hidden="true"></i> Catcher Release</a></li>
                            <li class="fas" role="presentation" data-activity="TeeVelocity"><a role="menuitem" tabindex="-1" href="#"><i class="fa fa-square-o text-muted" aria-hidden="true"></i> Tee Velocity</a></li>
                        <?php endif; ?>
                        <?php if ($sportNameLowercase == 'crossfit'): ?>
                            <li class="fas" role="presentation" data-activity="ShuttleRun"><a role="menuitem" tabindex="-1" href="#"><i class="fa fa-square-o text-muted" aria-hidden="true"></i> Shuttle Run</a></li>
                            <li class="fas" role="presentation" data-activity="40YardDash"><a role="menuitem" tabindex="-1" href="#"><i class="fa fa-square-o text-muted" aria-hidden="true"></i> 40 Yard Dash</a></li>
                            <li class="fas" role="presentation" data-activity="5105ConeDrill"><a role="menuitem" tabindex="-1" href="#"><i class="fa fa-square-o text-muted" aria-hidden="true"></i> 5-10-5 Cone Drill</a></li>
                            <li class="fas" role="presentation" data-activity="3RMHang"><a role="menuitem" tabindex="-1" href="#"><i class="fa fa-square-o text-muted" aria-hidden="true"></i> 3 RM Hang</a></li>
                            <li class="fas" role="presentation" data-activity="VerticalJump"><a role="menuitem" tabindex="-1" href="#"><i class="fa fa-square-o text-muted" aria-hidden="true"></i> Vertical Jump</a></li>
                            <li class="fas" role="presentation" data-activity="BroadJump"><a role="menuitem" tabindex="-1" href="#"><i class="fa fa-square-o text-muted" aria-hidden="true"></i> Broad Jump</a></li>
                            <li class="fas" role="presentation" data-activity="PowerClean"><a role="menuitem" tabindex="-1" href="#"><i class="fa fa-square-o text-muted" aria-hidden="true"></i> Power Clean</a></li>
                            <li class="fas" role="presentation" data-activity="PullUps"><a role="menuitem" tabindex="-1" href="#"><i class="fa fa-square-o text-muted" aria-hidden="true"></i> Pull Ups</a></li>
                            <li class="fas" role="presentation" data-activity="PushUps"><a role="menuitem" tabindex="-1" href="#"><i class="fa fa-square-o text-muted" aria-hidden="true"></i> Push Ups</a></li>
                        <?php endif; ?>
                        <?php if ($sportNameLowercase == 'lacrosse'): ?>
                            <li class="fas" role="presentation" data-activity="60YardDash"><a role="menuitem" tabindex="-1" href="#"><i class="fa fa-square-o text-muted" aria-hidden="true"></i> 60 Yard Dash</a></li>
                            <li class="fas" role="presentation" data-activity="5ConeFootwork"><a role="menuitem" tabindex="-1" href="#"><i class="fa fa-square-o text-muted" aria-hidden="true"></i> 5 Cone Footwork</a></li>
                            <li class="fas" role="presentation" data-activity="ShuttleRun"><a role="menuitem" tabindex="-1" href="#"><i class="fa fa-square-o text-muted" aria-hidden="true"></i> Shuttle Run</a></li>
                            <li class="fas" role="presentation" data-activity="Rebounder10"><a role="menuitem" tabindex="-1" href="#"><i class="fa fa-square-o text-muted" aria-hidden="true"></i> Rebounder 10</a></li>
                            <li class="fas" role="presentation" data-activity="GoalShot10"><a role="menuitem" tabindex="-1" href="#"><i class="fa fa-square-o text-muted" aria-hidden="true"></i> Goal Shot 10</a></li>
                            <li class="fas" role="presentation" data-activity="Accuracy50"><a role="menuitem" tabindex="-1" href="#"><i class="fa fa-square-o text-muted" aria-hidden="true"></i> Accuracy 50</a></li>
                            <li class="fas" role="presentation" data-activity="VelocityThrow"><a role="menuitem" tabindex="-1" href="#"><i class="fa fa-square-o text-muted" aria-hidden="true"></i> Velocity Throw</a></li>
                        <?php endif; ?>
                        <?php if ($sportNameLowercase == 'swimming'): ?>
                            <li class="fas" role="presentation" data-activity="25MFreestyle"><a role="menuitem" tabindex="-1" href="#"><i class="fa fa-square-o text-muted" aria-hidden="true"></i> 25M Freestyle</a></li>
                            <li class="fas" role="presentation" data-activity="25MBackStroke"><a role="menuitem" tabindex="-1" href="#"><i class="fa fa-square-o text-muted" aria-hidden="true"></i> 25M BackStroke</a></li>
                            <li class="fas" role="presentation" data-activity="25MBreastStroke"><a role="menuitem" tabindex="-1" href="#"><i class="fa fa-square-o text-muted" aria-hidden="true"></i> 25M BreastStroke</a></li>
                            <li class="fas" role="presentation" data-activity="25MButterfly"><a role="menuitem" tabindex="-1" href="#"><i class="fa fa-square-o text-muted" aria-hidden="true"></i> 25M Butterfly</a></li>
                            <li class="fas" role="presentation" data-activity="50MFreestyle"><a role="menuitem" tabindex="-1" href="#"><i class="fa fa-square-o text-muted" aria-hidden="true"></i> 50M Freestyle</a></li>
                            <li class="fas" role="presentation" data-activity="50MBackStroke"><a role="menuitem" tabindex="-1" href="#"><i class="fa fa-square-o text-muted" aria-hidden="true"></i> 50M BackStroke</a></li>
                            <li class="fas" role="presentation" data-activity="50MBreastStroke"><a role="menuitem" tabindex="-1" href="#"><i class="fa fa-square-o text-muted" aria-hidden="true"></i> 50M BreastStroke</a></li>
                            <li class="fas" role="presentation" data-activity="50MButterfly"><a role="menuitem" tabindex="-1" href="#"><i class="fa fa-square-o text-muted" aria-hidden="true"></i> 50M Butterfly</a></li>
                            <li class="fas" role="presentation" data-activity="100MFreestyle"><a role="menuitem" tabindex="-1" href="#"><i class="fa fa-square-o text-muted" aria-hidden="true"></i> 100M Freestyle</a></li>
                            <li class="fas" role="presentation" data-activity="100MBackStroke"><a role="menuitem" tabindex="-1" href="#"><i class="fa fa-square-o text-muted" aria-hidden="true"></i> 100M BackStroke</a></li>
                            <li class="fas" role="presentation" data-activity="100MBreastStroke"><a role="menuitem" tabindex="-1" href="#"><i class="fa fa-square-o text-muted" aria-hidden="true"></i> 100M BreastStroke</a></li>
                            <li class="fas" role="presentation" data-activity="100MButterfly"><a role="menuitem" tabindex="-1" href="#"><i class="fa fa-square-o text-muted" aria-hidden="true"></i> 100M Butterfly</a></li>
                            <li class="fas" role="presentation" data-activity="100MIndividualMedley"><a role="menuitem" tabindex="-1" href="#"><i class="fa fa-square-o text-muted" aria-hidden="true"></i> 100M Individual Medley</a></li>
                            <li class="fas" role="presentation" data-activity="200MIndividualMedley"><a role="menuitem" tabindex="-1" href="#"><i class="fa fa-square-o text-muted" aria-hidden="true"></i> 200M Individual Medley</a></li>
                        <?php endif; ?>
                    </ul>
                </div>

                <span id="pageLoadingSpinner" class="label">Page is loading...</span>

                <!-- col-sm-10 -->
                <?php
                $count = 0;
                $editPlayerForEvent = ''; // modal
                $playerStatsForEvent = ''; // modal
                $playerStatItemsForEvent = ''; // modal

                // list events
                try {
                    $sql_event = "
                        SELECT 
                          `Events`.`EventId`,
                          `Events`.`UserId`,
                          `Events`.`Sport`,
                          `Events`.`Rounds`,
                          `Events`.`Name`,
                          `Events`.`Address`,
                          `Events`.`City`,
                          `Events`.`StateShort`,
                          `Events`.`Zip`, 
                          `Events`.`Timezone`,
                          `Events`.`Phone`, 
                          `Events`.`Email`, 
                          `Events`.`Website`, 
                          `Events`.`Coordinator`,
                          `Events`.`Description`,
                          `Events`.`Calendar`,
                          `Events`.`StartDate`
                        FROM 
                          `Events` 
                        LEFT JOIN `EventAdmins` ON
                        `Events`.`EventId` = `EventAdmins`.`EventId`
                        WHERE
                          `EventAdmins`.`UserId` = :UserId
                        AND 
                          `EventAdmins`.`EventId` = :EventId";

                    $stmt_event = $PDO->prepare($sql_event);
                    $stmt_event->bindParam('UserId', $eventAdminId, PDO::PARAM_INT);
                    $stmt_event->bindParam('EventId', $eventId, PDO::PARAM_INT);
                    $stmt_event->execute();

                    while ($row_event = $stmt_event->fetch(PDO::FETCH_ASSOC)) {

                        $eventAdminId = $row_event['UserId'];
                        $count++;

                        $eventSportOptions = '';
                        $sportName = getSportName($row_event['Sport']);
                        $sportNameLowercase = strtolower(str_replace(' ', '', $sportName));
                        $sports = array(
                            'Baseball',
                            'Cross Fit',
                            'Fast Pitch',
                            'Lacrosse',
                            'Swimming'
                        );

                        foreach ($sports as $sport) {
                            $sportLowercase = strtolower(str_replace(' ', '', $sport));
                            if ($sportLowercase == $sportNameLowercase) {
                                $eventSportOptions .= '<option value="' . $sportLowercase . '" selected="selected">' . $sport . '</option>';
                            } else {
                                $eventSportOptions .= '<option value="' . $sportLowercase . '">' . $sport . '</option>';
                            }
                        }

                        $eventRoundsOptions = '';
                        $rounds = array(
                            '1' => 'One Round',
                            '2' => 'Two Rounds',
                            '3' => 'Three Rounds (Default)',
                            '4' => 'Four Rounds',
                            '5' => 'Five Rounds'
                        );

                        foreach ($rounds as $eventRoundKey => $eventRoundValue) {
                            if ($eventRoundKey == $row_event['Rounds']) {
                                $eventRoundsOptions .= '<option value="' . $eventRoundKey . '" selected="selected">' . $eventRoundValue . '</option>';
                            } else {
                                $eventRoundsOptions .= '<option value="' . $eventRoundKey . '">' . $eventRoundValue . '</option>';
                            }
                        }

                        $eventStateOptions = '';
                        foreach ($states as $stateKey => $stateValue) {
                            if ($stateKey == $row_event['StateShort']) {
                                $eventStateOptions .= '<option value="' . $stateKey . '" selected="selected">' . $stateValue . '</option>';
                            } else {
                                $eventStateOptions .= '<option value="' . $stateKey . '">' . $stateValue . '</option>';
                            }
                        }

                        if ($row_event['Calendar'] == '0') {
                            $eventsCalendar = '<input type="radio" name="eventCalendar" value="yes"> Yes
                                                        <input type="radio" name="eventCalendar" value="no" checked="checked"> No';
                        } else {
                            $eventsCalendar = '<input type="radio" name="eventCalendar" value="yes" checked="checked"> Yes
                                                        <input type="radio" name="eventCalendar" value="no"> No';
                        }

                        $eventTimezoneOptions = '';
                        foreach ($zones as $tzName => $timezones) {
                            $eventTimezoneOptions .= '<optgroup label="' . $tzName . '">';
                            foreach ($timezones as $timezone => $value) {
                                $time = new DateTime(NULL, new DateTimeZone($timezone));
                                $value = '(GMT' . $time->format('P') . ') ' . $timezones[$timezone] . ' (' . $timezone . ')';
                                $value = str_replace('_', ' ', $value);
                                if ($row_event['Timezone'] == $timezone) {
                                    $eventTimezoneOptions .= '<option value="' . $timezone . '" selected="selected">' . $value . '</option>';
                                } else {
                                    $eventTimezoneOptions .= '<option value="' . $timezone . '">' . $value . '</option>';
                                }
                            }
                            $eventTimezoneOptions .= '</optgroup>';
                        }

                        $eventStartDate = '';
                        $_time = '';
                        $_date = '';
                        if (!empty($row_event['StartDate'])) {
                            $eventStartDate = date('m/d/Y g:i:s', strtotime($row_event['StartDate']));

                            $_time = date('g:ia', strtotime($eventStartDate));
                            $_date = date('F jS, Y', strtotime($eventStartDate));

                            /*
                            if (!empty($userTimezone)) {
                                $tz = new DateTimeZone($userTimezone);
                                $date = new DateTime($eventStartDate);
                                $date->setTimezone($tz);
                                $eventStartDate = $date->format('m/d/Y g:i:s');

                                $tz = new DateTimeZone($userTimezone);
                                $date = new DateTime($eventStartDate);
                                $date->setTimezone($tz);
                                $_time = $date->format('g:ia');

                                $tz = new DateTimeZone($userTimezone);
                                $date = new DateTime($eventStartDate);
                                $date->setTimezone($tz);
                                $_date = $date->format('F jS, Y');

                            }
                            */
                        }

                        $eventRounds = $row_event['Rounds'];
                        $playerChartRows = '';

                        $cityState = (!empty($row_event['City'])) ? ' - ' . rtrim($row_event['City'] . ', ' . $row_event['StateShort'], ',') : '';

                        // list event players
                        $playerTagsNonEditable = '';

                        try {
                            $sql_player = "
                            SELECT 
                              `Players`.`PlayerId`,
                              `Players`.`TeamId`,
                              `Players`.`Gender`,
                              `Players`.`FirstName`,
                              `Players`.`MiddleName`,
                              `Players`.`LastName`,
                              `Players`.`GradYear`,
                              `Players`.`DOB`,
                              `Players`.`City`,
                              `Players`.`StateShort`,
                              `Players`.`Picture`,
                              `Players`.`Email`,
                              `Players`.`Token`
                            FROM 
                              `Players` 
                            WHERE
                              `Players`.`UserId` = :UserId
                            AND 
                              `Players`.`EventId` = :EventId";

                            $stmt_player = $PDO->prepare($sql_player);
                            $stmt_player->bindParam('UserId', $eventAdminId, PDO::PARAM_STR);
                            $stmt_player->bindParam('EventId', $row_event['EventId'], PDO::PARAM_INT);
                            $stmt_player->execute();

                            while ($row_player = $stmt_player->fetch(PDO::FETCH_ASSOC)) {

                                $playerId = $row_player['PlayerId'];
                                $playerToken = $row_player['Token'];
                                $playerFullName = trim($row_player['FirstName'] . ' ' . $row_player['LastName']);
                                $playerFullNameReverse = trim($row_player['LastName'] . ', ' . $row_player['FirstName']);
                                $playerFullNameReverse = ltrim($playerFullNameReverse, ',');

                                $playerPicture = '';
                                if (!empty($row_player['Picture']) && file_exists(constant('UPLOADS_PLAYERS') . $row_player['Picture'])) {
                                    $playerPicture = constant('UPLOADS_PLAYERS') . $row_player['Picture'];
                                } else {
                                    if ($row_player['Gender'] == 0) {
                                        $playerPicture = constant('UPLOADS_PLAYERS') . 'default-male.png';
                                    } else {
                                        $playerPicture = constant('UPLOADS_PLAYERS') . 'default-female.png';
                                    }
                                }

                                $genders = array('Male', 'Female');
                                $playerGender = $row_player['Gender'];

                                $playerGender2 = 'female';

                                if ($playerGender == 1) {
                                    $playerGender2 = 'female';
                                } else {
                                    $playerGender2 = 'male';
                                }

                                $playerGenderOptions = '';
                                foreach ($genders as $gender) {
                                    if ($playerGender2 == strtolower($gender)) {
                                        $playerGenderOptions .= '<option value="' . strtolower($gender) . '" selected="selected">' . $gender . '</option>';
                                    } else {
                                        $playerGenderOptions .= '<option value="' . strtolower($gender) . '">' . $gender . '</option>';
                                    }
                                }

                                $playerStateOptions = '';
                                foreach ($states as $stateKey => $stateValue) {
                                    if ($stateKey == $row_event['StateShort']) {
                                        $playerStateOptions .= '<option value="' . $stateKey . '" selected="selected">' . $stateValue . '</option>';
                                    } else {
                                        $playerStateOptions .= '<option value="' . $stateKey . '">' . $stateValue . '</option>';
                                    }
                                }

                                $playerDOB = '';
                                if (!empty($row_player['DOB'])) {
                                    $playerDOB = date('m/d/Y', strtotime($row_player['DOB']));
                                }

                                // reset for each player
                                $stat_Baseball_VelocityMound = ''; // MPH
                                $stat_Baseball_VelocityOutfield = ''; // MPH
                                $stat_Baseball_VelocityInfield = ''; // MPH
                                $stat_Baseball_SwingVelocity = ''; // MPH
                                $stat_Baseball_60YardDash = ''; // Time
                                $stat_Baseball_CatcherPop = ''; // Time
                                $stat_Baseball_CatcherRelease = ''; // Time
                                $stat_Baseball_TeeVelocity = ''; // MPH

                                $stat_CrossFit_ShuttleRun = ''; // Time
                                $stat_CrossFit_40YardDash = ''; // Time
                                $stat_CrossFit_5105ConeDrill = ''; // Time
                                $stat_CrossFit_3RMHang = ''; // Count
                                $stat_CrossFit_VerticalJump = ''; // Inch
                                $stat_CrossFit_BroadJump = ''; // Inch
                                $stat_CrossFit_PowerClean = ''; // Count
                                $stat_CrossFit_PullUps = ''; // Count
                                $stat_CrossFit_PushUps = ''; // Count

                                $stat_FastPitch_VelocityMound = ''; // MPH
                                $stat_FastPitch_VelocityOutfield = ''; // MPH
                                $stat_FastPitch_VelocityInfield = ''; // MPH
                                $stat_FastPitch_SwingVelocity = ''; // MPH
                                $stat_FastPitch_60YardDash = ''; // Time
                                $stat_FastPitch_CatcherPop = ''; // Time
                                $stat_FastPitch_CatcherRelease = ''; // Time
                                $stat_FastPitch_TeeVelocity = ''; // MPH

                                $stat_Lacrosse_60YardDash = ''; // Time
                                $stat_Lacrosse_5ConeFootwork = ''; // Time
                                $stat_Lacrosse_ShuttleRun = ''; // Time
                                $stat_Lacrosse_Rebounder10 = ''; // Count
                                $stat_Lacrosse_GoalShot10 = ''; // Count
                                $stat_Lacrosse_Accuracy50 = ''; // Count
                                $stat_Lacrosse_VelocityThrow = ''; // MPH

                                $stat_Swimming_25MFreestyle = ''; // Time
                                $stat_Swimming_25MBackStroke = ''; // Time
                                $stat_Swimming_25MBreastStroke = ''; // Time
                                $stat_Swimming_25MButterfly = ''; // Time
                                $stat_Swimming_50MFreestyle = ''; // Time
                                $stat_Swimming_50MBackStroke = ''; // Time
                                $stat_Swimming_50MBreastStroke = ''; // Time
                                $stat_Swimming_50MButterfly = ''; // Time
                                $stat_Swimming_100MFreestyle = ''; // Time
                                $stat_Swimming_100MBackStroke = ''; // Time
                                $stat_Swimming_100MBreastStroke = ''; // Time
                                $stat_Swimming_100MButterfly = ''; // Time
                                $stat_Swimming_100MIndividualMedley = ''; // Time
                                $stat_Swimming_200MIndividualMedley = ''; // Time

                                for ($i = 1; $i <= $eventRounds; $i++) {

                                    // Baseball
                                    if ($sportNameLowercase === 'baseball') {
                                        $baseball_VelocityMound = ''; // MPH
                                        $baseball_VelocityOutfield = ''; // MPH
                                        $baseball_VelocityInfield = ''; // MPH
                                        $baseball_SwingVelocity = ''; // MPH
                                        $baseball_60YardDash = ''; // Time
                                        $baseball_CatcherPop = ''; // Time
                                        $baseball_CatcherRelease = ''; // Time
                                        $baseball_PrimaryPosition = ''; // Position Number
                                        $baseball_SecondaryPosition = ''; // Position Number
                                        $baseball_TeeVelocity = ''; // MPH

                                        try {
                                            $sql = "
                                            SELECT
                                              `GameBaseball`.*
                                            FROM 
                                              `GameBaseball`
                                            WHERE
                                              `GameBaseball`.`PlayerId` = :PlayerId
                                            AND 
                                              `GameBaseball`.`EventId` = :EventId
                                            AND 
                                              `GameBaseball`.`Round` = :Round";

                                            $stmt = $PDO->prepare($sql);
                                            $stmt->bindParam('PlayerId', $row_player['PlayerId'], PDO::PARAM_INT);
                                            $stmt->bindParam('EventId', $row_event['EventId'], PDO::PARAM_INT);
                                            $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                                            $stmt->execute();

                                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                $baseball_VelocityMound = ($row['VelocityMound'] > 0) ? $row['VelocityMound'] : ''; // MPH
                                                $baseball_VelocityOutfield = ($row['VelocityOutfield'] > 0) ? $row['VelocityOutfield'] : ''; // MPH
                                                $baseball_VelocityInfield = ($row['VelocityInfield'] > 0) ? $row['VelocityInfield'] : ''; // MPH
                                                $baseball_SwingVelocity = ($row['SwingVelocity'] > 0) ? $row['SwingVelocity'] : ''; // MPH
                                                $baseball_60YardDash = ($row['60YardDash'] > 0) ? secondsToHMSU($row['60YardDash']) : ''; // Time
                                                $baseball_CatcherPop = ($row['CatcherPop'] > 0) ? secondsToHMSU($row['CatcherPop']) : ''; // Time
                                                $baseball_CatcherRelease = ($row['CatcherRelease'] > 0) ? secondsToHMSU($row['CatcherRelease']) : ''; // Time
                                                $baseball_PrimaryPosition = $row['PrimaryPosition']; // Position Number
                                                $baseball_SecondaryPosition = $row['SecondaryPosition']; // Position Number
                                                $baseball_TeeVelocity = ($row['TeeVelocity'] > 0) ? $row['TeeVelocity'] : ''; // MPH
                                            }

                                        } catch (PDOException $e) {
                                            trigger_error($e->getMessage(), E_USER_ERROR);
                                        }//end try

                                        $baseballPrimaryPositionOptions = '';
                                        $baseballSecondaryPositionOptions = '';
                                        $baseballFieldPositions = array(
                                            'P' => 'Pitcher',
                                            'C' => 'Catcher',
                                            '1B' => '1st Baseman',
                                            '2B' => '2nd Baseman',
                                            '3B' => '3rd Baseman',
                                            'SS' => 'Shortstop',
                                            'LF' => 'Left Fielder',
                                            'CF' => 'Center Fielder',
                                            'RF' => 'Right Fielder'
                                        );

                                        foreach ($baseballFieldPositions as $key => $value) {
                                            if (getBaseballFieldPositionNumber($key) == $baseball_PrimaryPosition) {
                                                $baseballPrimaryPositionOptions .= '<option value="' . getBaseballFieldPositionNumber($key) . '" selected="selected">' . $value . '</option>';
                                            } else {
                                                $baseballPrimaryPositionOptions .= '<option value="' . getBaseballFieldPositionNumber($key) . '">' . $value . '</option>';
                                            }
                                        }

                                        foreach ($baseballFieldPositions as $key => $value) {
                                            if (getBaseballFieldPositionNumber($key) == $baseball_SecondaryPosition) {
                                                $baseballSecondaryPositionOptions .= '<option value="' . getBaseballFieldPositionNumber($key) . '" selected="selected">' . $value . '</option>';
                                            } else {
                                                $baseballSecondaryPositionOptions .= '<option value="' . getBaseballFieldPositionNumber($key) . '">' . $value . '</option>';
                                            }
                                        }

                                        $stat_Baseball_VelocityMound .= '
                                        <div class="tr">
                                            <div class="td"></div>
                                            <div class="td">Round ' . $i . '</div>
                                            <div class="td">
                                                <input name="baseball_VelocityMound[]" type="tel" placeholder="0" class="input_box format_mph" value="' . $baseball_VelocityMound . '"/>
                                            </div>
                                            <div class="td"></div>
                                        </div>';
                                        $stat_Baseball_VelocityOutfield .= '
                                        <div class="tr">
                                            <div class="td"></div>
                                            <div class="td">Round ' . $i . '</div>
                                            <div class="td">
                                                <input name="baseball_VelocityOutfield[]" type="tel" placeholder="0" class="input_box format_mph" value="' . $baseball_VelocityOutfield . '"/>
                                            </div>
                                            <div class="td"></div>
                                        </div>';
                                        $stat_Baseball_VelocityInfield .= '
                                        <div class="tr">
                                            <div class="td"></div>
                                            <div class="td">Round ' . $i . '</div>
                                            <div class="td">
                                                <input name="baseball_VelocityInfield[]" type="tel" placeholder="0" class="input_box format_mph" value="' . $baseball_VelocityInfield . '"/>
                                            </div>
                                            <div class="td"></div>
                                        </div>';
                                        $stat_Baseball_SwingVelocity .= '
                                        <div class="tr">
                                            <div class="td"></div>
                                            <div class="td">Round ' . $i . '</div>
                                            <div class="td">
                                                <input name="baseball_SwingVelocity[]" type="tel" placeholder="0" class="input_box format_mph" value="' . $baseball_SwingVelocity . '"/>
                                            </div>
                                            <div class="td"></div>
                                        </div>';
                                        $stat_Baseball_60YardDash .= '
                                        <div class="tr">
                                            <div class="td"></div>
                                            <div class="td">Round ' . $i . '</div>
                                            <div class="td">
                                                <input name="baseball_60YardDash[]" type="text" placeholder="M:S.U" class="input_box format_time" value="' . $baseball_60YardDash . '"/>
                                            </div>
                                            <div class="td"></div>
                                        </div>';
                                        $stat_Baseball_CatcherPop .= '
                                        <div class="tr">
                                            <div class="td"></div>
                                            <div class="td">Round ' . $i . '</div>
                                            <div class="td">
                                                <input name="baseball_CatcherPop[]" type="text" placeholder="M:S.U" class="input_box format_time" value="' . $baseball_CatcherPop . '"/>
                                            </div>
                                            <div class="td"></div>
                                        </div>';
                                        $stat_Baseball_CatcherRelease .= '
                                        <div class="tr">
                                            <div class="td"></div>
                                            <div class="td">Round ' . $i . '</div>
                                            <div class="td">
                                                <input name="baseball_CatcherRelease[]" type="text" placeholder="M:S.U" class="input_box format_time" value="' . $baseball_CatcherRelease . '"/>
                                            </div>
                                            <div class="td"></div>
                                        </div>';
                                        $stat_Baseball_TeeVelocity .= '
                                        <div class="tr">
                                            <div class="td"></div>
                                            <div class="td"> ' . $i . '</div>
                                            <div class="td">
                                                <input name="baseball_TeeVelocity[]" type="tel" placeholder="0" class="input_box format_mph" value="' . $baseball_TeeVelocity . '"/>
                                            </div>
                                            <div class="td"></div>
                                        </div>';

                                        if ($i == 1) {
                                            $playerChartRows .= '
                                            <tr data-id="' . $playerId . '" data-event="' . $row_event['EventId'] . '">
                                                <td><button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'Baseball">Go</button></td>
                                                <td>
                                                    <div class="score_table_img text-center">
                                                        <a href="index.php?page=player&token=' . $playerToken . '" target="_blank"><img src="' . $playerPicture . '" alt="' . $playerFullName . '" class="img-responsive img-circle"></a>
                                                        ' . $row_player['GradYear'] . '
                                                    </div>
                                                </td>
                                                <td>' . $row_player['TeamId'] . '</td>
                                                <td><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#editPlayer' . $playerId . 'ForEvent' . $row_event['EventId'] . '">' . $playerFullNameReverse . '</td>
                                                <td><span class="visible-xs"><a href="#" data-toggle="tooltip" title="' . getBaseballFieldPositionName($baseball_PrimaryPosition) . '">' . getBaseballFieldPositionId($baseball_PrimaryPosition) . '</a>, <a href="#" data-toggle="tooltip" title="' . getBaseballFieldPositionName($baseball_SecondaryPosition) . '">' . getBaseballFieldPositionId($baseball_SecondaryPosition) . '</a></span><span class="hidden-xs">' . getBaseballFieldPositionName($baseball_PrimaryPosition) . ', ' . getBaseballFieldPositionName($baseball_SecondaryPosition) . '</span></td>
                                                <td><img src="images/rating.png" alt="Rating" class="img-responsive"></td>
                                            </tr>';
                                            $editPlayerForEvent .= '
                                                <!-- Edit Event Step 1 -->
                                                <div id="editPlayer' . $playerId . 'ForEvent' . $row_event['EventId'] . '" data-id="' . $row_event['EventId'] . '" class="modal forget_section" role="dialog">
                                                    <div class="modal-dialog">
                                                        <!-- Modal content-->
                                                        <div class="container">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <div class="close_img">
                                                                        <button type="button" class="close" data-dismiss="modal"></button>
                                                                    </div>
                                                                    <div class="title_h2">
                                                                        <h2>Edit Player</h2>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <form id="editPlayer' . $playerId . 'ForEvent' . $row_event['EventId'] . 'Form" class="form-horizontal media_popup add_new_events">
                                                                        <div class="form-group">
                                                                            <label class="control-label col-sm-4" for="playerTeamId' . $playerId . '">Team Id</label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text" id="playerTeamId' . $playerId . '" name="playerTeamId" class="form-control form-control input_box" value="' . $row_player['TeamId'] . '">
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="control-label col-sm-4" for="playerGender' . $playerId . '">Gender</label>
                                                                            <div class="col-sm-8">
                                                                                <select id="playerGender' . $playerId . '" name="playerGender" class="form-control input_box">
                                                                                    ' . $playerGenderOptions . '
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="control-label col-sm-4" for="playerFirstName' . $playerId . '">First Name</label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text" id="playerFirstName' . $playerId . '" name="playerFirstName" class="form-control input_box" value="' . $row_player['FirstName'] . '">
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="control-label col-sm-4" for="playerMiddleName' . $playerId . '">Middle Name (or initial)</label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text" id="playerMiddleName' . $playerId . '" name="playerMiddleName" class="form-control input_box" value="' . $row_player['MiddleName'] . '">
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="control-label col-sm-4" for="playerLastName' . $playerId . '">Last Name</label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text" id="playerLastName' . $playerId . '" name="playerLastName" class="form-control input_box" value="' . $row_player['LastName'] . '">
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="control-label col-sm-4" for="playerGradYear' . $playerId . '">Grad Year</label>
                                                                            <div class="col-sm-8">
                                                                                <input type="tel" id="playerGradYear' . $playerId . '" name="playerGradYear" class="form-control input_box" value="' . $row_player['GradYear'] . '">
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="control-label col-sm-4" for="playerDOB' . $playerId . '">DOB</label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text" id="playerDOB' . $playerId . '" name="playerDOB" class="form-control input_box" placeholder="MM/DD/YYYY" value="' . $row_player['DOB'] . '">
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="control-label col-sm-4" for="pCity' . $playerId . '">City</label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text" id="playerCity' . $playerId . '" name="playerCity" class="form-control input_box" value="' . $row_player['City'] . '">
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="control-label col-sm-4" for="playerStateShort' . $playerId . '">State</label>
                                                                            <div class="col-sm-8">
                                                                                <select id="playerStateShort' . $playerId . '" name="playerStateShort" class="form-control input_box">
                                                                                    <option value="">Select State</option>
                                                                                    ' . $playerStateOptions . '
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="control-label col-sm-4" for="playerEmail' . $playerId . '">Email</label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text" id="playerEmail' . $playerId . '" name="playerEmail" class="form-control input_box" value="' . $row_player['Email'] . '">
                                                                            </div>
                                                                        </div>
                                                                        <h4 class="text-center">Baseball Field Position</h4>
                                                                        <div class="form-group">
                                                                            <label class="control-label col-sm-4" for="baseball_PrimaryPosition' . $playerId . '">Primary</label>
                                                                            <div class="col-sm-8">
                                                                                <select id="baseball_PrimaryPosition' . $playerId . '" name="baseball_PrimaryPosition" class="form-control input_box">
                                                                                    <option value="">Select Position</option>
                                                                                    ' . $baseballPrimaryPositionOptions . '
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="control-label col-sm-4" for="baseball_SecondaryPosition' . $playerId . '">Secondary</label>
                                                                            <div class="col-sm-8">
                                                                                <select id="baseball_SecondaryPosition' . $playerId . '" name="baseball_SecondaryPosition" class="form-control input_box">
                                                                                    <option value="">Select Position</option>
                                                                                    ' . $baseballSecondaryPositionOptions . '
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="editPlayerForEventErrorBox"></div>
                                                                        <div class="all_btn">
                                                                            <div class="blue_btn">
                                                                                <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#editEvent' . $row_event['EventId'] . 'Results"><i class="fa fa-chevron-left"></i> Back</button>
                                                                            </div>
                                                                            <div class="blue_btn">
                                                                                <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerForEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                                            </div>
                                                                        </div>
                                                                        <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                                        <input type="hidden" name="token" value="' . $row_player['Token'] . '">
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- /editPlayerForEvent Baseball -->';

                                            $playerStatItemsForEvent .= '<!-- Add Player Stats For Event Baseball -->
                                            <div id="player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'Baseball" class="modal forget_section player_p_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>Player Stats For ' . $row_event['Name'] . '</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h2>' . $playerFullName . '</h2>
                                                                <div class="player_table">
                                                                    <div class="table">
                                                                        <div class="tbody">
                                                                            <div class="tr">
                                                                                <div class="td text-center"><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'BaseballVelocityMound">Velocity Mound</a></div>
                                                                            </div>
                                                                            <div class="tr">
                                                                                <div class="td text-center"><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'BaseballVelocityOutfield">Velocity Outfield</a></div>
                                                                            </div>
                                                                            <div class="tr">
                                                                                <div class="td text-center"><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'BaseballVelocityInfield">Velocity Infield</a></div>
                                                                            </div>
                                                                            <div class="tr">
                                                                                <div class="td text-center"><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'BaseballSwingVelocity">Swing Velocity</a></div>
                                                                            </div>
                                                                            <div class="tr">
                                                                                <div class="td text-center"><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Baseball60YardDash">60 Yard Dash</a></div>
                                                                            </div>
                                                                            <div class="tr">
                                                                                <div class="td text-center"><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'BaseballCatcherPop">Catcher Pop</a></div>
                                                                            </div>
                                                                            <div class="tr">
                                                                                <div class="td text-center"><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'BaseballCatcherRelease">Catcher Release</a></div>
                                                                            </div>
                                                                            <div class="tr">
                                                                                <div class="td text-center"><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'BaseballTeeVelocity">Tee Velocity</a></div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="all_btn">
                                                                    <div class="blue_btn">
                                                                        <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#editEvent' . $row_event['EventId'] . 'Results"><i class="fa fa-chevron-left"></i> Back</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div><!-- /playerStatItemsForEvent Baseball -->';
                                        } // end if

                                        if ($i == $eventRounds) {
                                            $playerStatsForEvent .= '<!-- Edit Player Stats For Event Baseball Velocity Mound -->
                                            <div id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'BaseballVelocityMound" class="modal forget_section player_p_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>Velocity Mound</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h2>' . $playerFullName . ' / ' . $row_event['Name'] . '</h2>
                                                                <form id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'BaseballVelocityMoundForm" class="player_accordion">
                                                                    <div class="player_table">
                                                                        <div class="table">
                                                                            <div class="thead">
                                                                                <div class="tr">
                                                                                    <div class="th"></div>
                                                                                    <div class="th">Item</div>
                                                                                    <div class="th">MPH</div>
                                                                                    <div class="th"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tbody">
                                                                                ' . $stat_Baseball_VelocityMound . '
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="playerId" value="' . $playerId . '">
                                                                    <input type="hidden" name="sport" value="Baseball">
                                                                    <input type="hidden" name="activity" value="VelocityMound">
                                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                                    <input type="hidden" name="token" value="' . $row_player['Token'] . '">
                                                                    <div class="editPlayerStatsForEventErrorBox"></div>
                                                                    <input type="text" name="statsEntryDate" class="input_box" placeholder="Date" value="' . date('m/d/Y') . '">
                                                                    <div class="all_btn">
                                                                        <div class="blue_btn">
                                                                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'Baseball"><i class="fa fa-chevron-left"></i> Back</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-savepause="true" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait.."><i class="fa fa-pause"></i> Save/Pause</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Edit Player Stats For Event Baseball Velocity Mound -->';
                                            $playerStatsForEvent .= '<!-- Edit Player Stats For Event Baseball Velocity Outfield -->
                                            <div id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'BaseballVelocityOutfield" class="modal forget_section player_p_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>Velocity Outfield</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h2>' . $playerFullName . ' / ' . $row_event['Name'] . '</h2>
                                                                <form id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'BaseballVelocityOutfieldForm" class="player_accordion">
                                                                    <div class="player_table">
                                                                        <div class="table">
                                                                            <div class="thead">
                                                                                <div class="tr">
                                                                                    <div class="th"></div>
                                                                                    <div class="th">Item</div>
                                                                                    <div class="th">MPH</div>
                                                                                    <div class="th"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tbody">
                                                                                ' . $stat_Baseball_VelocityOutfield . '
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="playerId" value="' . $playerId . '">
                                                                    <input type="hidden" name="sport" value="Baseball">
                                                                    <input type="hidden" name="activity" value="VelocityOutfield">
                                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                                    <input type="hidden" name="token" value="' . $row_player['Token'] . '">
                                                                    <div class="editPlayerStatsForEventErrorBox"></div>
                                                                    <input type="text" name="statsEntryDate" class="input_box" placeholder="Date" value="' . date('m/d/Y') . '">
                                                                    <div class="all_btn">
                                                                        <div class="blue_btn">
                                                                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'Baseball"><i class="fa fa-chevron-left"></i> Back</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-savepause="true" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait.."><i class="fa fa-pause"></i> Save/Pause</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Edit Player Stats For Event Baseball Velocity Outfield -->';
                                            $playerStatsForEvent .= '<!-- Edit Player Stats For Event Baseball Velocity Infield -->
                                            <div id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'BaseballVelocityInfield" class="modal forget_section player_p_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>Velocity Infield</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h2>' . $playerFullName . ' / ' . $row_event['Name'] . '</h2>
                                                                <form id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'BaseballVelocityMoundForm" class="player_accordion">
                                                                    <div class="player_table">
                                                                        <div class="table">
                                                                            <div class="thead">
                                                                                <div class="tr">
                                                                                    <div class="th"></div>
                                                                                    <div class="th">Item</div>
                                                                                    <div class="th">MPH</div>
                                                                                    <div class="th"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tbody">
                                                                                ' . $stat_Baseball_VelocityInfield . '
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="playerId" value="' . $playerId . '">
                                                                    <input type="hidden" name="sport" value="Baseball">
                                                                    <input type="hidden" name="activity" value="VelocityMound">
                                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                                    <input type="hidden" name="token" value="' . $row_player['Token'] . '">
                                                                    <div class="editPlayerStatsForEventErrorBox"></div>
                                                                    <input type="text" name="statsEntryDate" class="input_box" placeholder="Date" value="' . date('m/d/Y') . '">
                                                                    <div class="all_btn">
                                                                        <div class="blue_btn">
                                                                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'Baseball"><i class="fa fa-chevron-left"></i> Back</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-savepause="true" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait.."><i class="fa fa-pause"></i> Save/Pause</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Edit Player Stats For Event Baseball Velocity Infield -->';
                                            $playerStatsForEvent .= '<!-- Edit Player Stats For Event Baseball Swing Velocity -->
                                            <div id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'BaseballSwingVelocity" class="modal forget_section player_p_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>Swing Velocity</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h2>' . $playerFullName . ' / ' . $row_event['Name'] . '</h2>
                                                                <form id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'BaseballSwingVelocityForm" class="player_accordion">
                                                                    <div class="player_table">
                                                                        <div class="table">
                                                                            <div class="thead">
                                                                                <div class="tr">
                                                                                    <div class="th"></div>
                                                                                    <div class="th">Item</div>
                                                                                    <div class="th">MPH</div>
                                                                                    <div class="th"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tbody">
                                                                                ' . $stat_Baseball_SwingVelocity . '
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="playerId" value="' . $playerId . '">
                                                                    <input type="hidden" name="sport" value="Baseball">
                                                                    <input type="hidden" name="activity" value="SwingVelocity">
                                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                                    <input type="hidden" name="token" value="' . $row_player['Token'] . '">
                                                                    <div class="editPlayerStatsForEventErrorBox"></div>
                                                                    <input type="text" name="statsEntryDate" class="input_box" placeholder="Date" value="' . date('m/d/Y') . '">
                                                                    <div class="all_btn">
                                                                        <div class="blue_btn">
                                                                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'Baseball"><i class="fa fa-chevron-left"></i> Back</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-savepause="true" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait.."><i class="fa fa-pause"></i> Save/Pause</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Edit Player Stats For Event Baseball Swing Velocity -->';
                                            $playerStatsForEvent .= '<!-- Edit Player Stats For Event Baseball 60 Yard Dash -->
                                            <div id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Baseball60YardDash" class="modal forget_section player_p_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>60 Yard Dash</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h2>' . $playerFullName . ' / ' . $row_event['Name'] . '</h2>
                                                                <form id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Baseball60YardDashForm" class="player_accordion">
                                                                    <div class="player_table">
                                                                        <div class="table">
                                                                            <div class="thead">
                                                                                <div class="tr">
                                                                                    <div class="th"></div>
                                                                                    <div class="th">Item</div>
                                                                                    <div class="th">Time</div>
                                                                                    <div class="th"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tbody">
                                                                                ' . $stat_Baseball_60YardDash . '
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="playerId" value="' . $playerId . '">
                                                                    <input type="hidden" name="sport" value="Baseball">
                                                                    <input type="hidden" name="activity" value="60YardDash">
                                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                                    <input type="hidden" name="token" value="' . $row_player['Token'] . '">
                                                                    <div class="editPlayerStatsForEventErrorBox"></div>
                                                                    <input type="text" name="statsEntryDate" class="input_box" placeholder="Date" value="' . date('m/d/Y') . '">
                                                                    <div class="all_btn">
                                                                        <div class="blue_btn">
                                                                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'Baseball"><i class="fa fa-chevron-left"></i> Back</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-savepause="true" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait.."><i class="fa fa-pause"></i> Save/Pause</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Edit Player Stats For Event Baseball 60 Yard Dash -->';
                                            $playerStatsForEvent .= '<!-- Edit Player Stats For Event Baseball Catcher Pop -->
                                            <div id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'BaseballCatcherPop" class="modal forget_section player_p_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>Catcher Pop</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h2>' . $playerFullName . ' / ' . $row_event['Name'] . '</h2>
                                                                <form id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'BaseballCatcherPopForm" class="player_accordion">
                                                                    <div class="player_table">
                                                                        <div class="table">
                                                                            <div class="thead">
                                                                                <div class="tr">
                                                                                    <div class="th"></div>
                                                                                    <div class="th">Item</div>
                                                                                    <div class="th">Time</div>
                                                                                    <div class="th"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tbody">
                                                                                ' . $stat_Baseball_CatcherPop . '
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="playerId" value="' . $playerId . '">
                                                                    <input type="hidden" name="sport" value="Baseball">
                                                                    <input type="hidden" name="activity" value="CatcherPop">
                                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                                    <input type="hidden" name="token" value="' . $row_player['Token'] . '">
                                                                    <div class="editPlayerStatsForEventErrorBox"></div>
                                                                    <input type="text" name="statsEntryDate" class="input_box" placeholder="Date" value="' . date('m/d/Y') . '">
                                                                    <div class="all_btn">
                                                                        <div class="blue_btn">
                                                                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'Baseball"><i class="fa fa-chevron-left"></i> Back</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-savepause="true" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait.."><i class="fa fa-pause"></i> Save/Pause</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Edit Player Stats For Event Baseball Catcher Pop -->';
                                            $playerStatsForEvent .= '<!-- Edit Player Stats For Event Baseball Catcher Release -->
                                            <div id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'BaseballCatcherRelease" class="modal forget_section player_p_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>Catcher Release</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h2>' . $playerFullName . ' / ' . $row_event['Name'] . '</h2>
                                                                <form id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'BaseballCatcherReleaseForm" class="player_accordion">
                                                                    <div class="player_table">
                                                                        <div class="table">
                                                                            <div class="thead">
                                                                                <div class="tr">
                                                                                    <div class="th"></div>
                                                                                    <div class="th">Item</div>
                                                                                    <div class="th">Time</div>
                                                                                    <div class="th"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tbody">
                                                                                ' . $stat_Baseball_CatcherRelease . '
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="playerId" value="' . $playerId . '">
                                                                    <input type="hidden" name="sport" value="Baseball">
                                                                    <input type="hidden" name="activity" value="CatcherRelease">
                                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                                    <input type="hidden" name="token" value="' . $row_player['Token'] . '">
                                                                    <div class="editPlayerStatsForEventErrorBox"></div>
                                                                    <input type="text" name="statsEntryDate" class="input_box" placeholder="Date" value="' . date('m/d/Y') . '">
                                                                    <div class="all_btn">
                                                                        <div class="blue_btn">
                                                                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'Baseball"><i class="fa fa-chevron-left"></i> Back</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-savepause="true" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait.."><i class="fa fa-pause"></i> Save/Pause</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Edit Player Stats For Event Baseball Catcher Release -->';
                                            $playerStatsForEvent .= '<!-- Edit Player Stats For Event Baseball Tee Velocity -->
                                            <div id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'BaseballTeeVelocity" class="modal forget_section player_p_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>Tee Velocity</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h2>' . $playerFullName . ' / ' . $row_event['Name'] . '</h2>
                                                                <form id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'BaseballTeeVelocityForm" class="player_accordion">
                                                                    <div class="player_table">
                                                                        <div class="table">
                                                                            <div class="thead">
                                                                                <div class="tr">
                                                                                    <div class="th"></div>
                                                                                    <div class="th">Item</div>
                                                                                    <div class="th">MPH</div>
                                                                                    <div class="th"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tbody">
                                                                                ' . $stat_Baseball_TeeVelocity . '
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="playerId" value="' . $playerId . '">
                                                                    <input type="hidden" name="sport" value="Baseball">
                                                                    <input type="hidden" name="activity" value="TeeVelocity">
                                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                                    <input type="hidden" name="token" value="' . $row_player['Token'] . '">
                                                                    <div class="editPlayerStatsForEventErrorBox"></div>
                                                                    <input type="text" name="statsEntryDate" class="input_box" placeholder="Date" value="' . date('m/d/Y') . '">
                                                                    <div class="all_btn">
                                                                        <div class="blue_btn">
                                                                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'Baseball"><i class="fa fa-chevron-left"></i> Back</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-savepause="true" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait.."><i class="fa fa-pause"></i> Save/Pause</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Edit Player Stats For Event Baseball Tee Velocity -->';
                                        } // end if

                                    } // end Baseball

                                    // Cross Fit
                                    if ($sportNameLowercase === 'crossfit') {
                                        $crossFit_ShuttleRun = ''; // Time
                                        $crossFit_40YardDash = ''; // Time
                                        $crossFit_5105ConeDrill = ''; // Time
                                        $crossFit_3RMHang = ''; // Count
                                        $crossFit_VerticalJump = ''; // Inch
                                        $crossFit_BroadJump = ''; // Inch
                                        $crossFit_PowerClean = ''; // Count
                                        $crossFit_PullUps = ''; // Count
                                        $crossFit_PushUps = ''; // Count

                                        try {
                                            $sql = "
                                            SELECT
                                              `GameCrossFit`.*
                                            FROM 
                                              `GameCrossFit`
                                            WHERE
                                              `GameCrossFit`.`PlayerId` = :PlayerId
                                            AND 
                                              `GameCrossFit`.`EventId` = :EventId
                                            AND 
                                              `GameCrossFit`.`Round` = :Round";

                                            $stmt = $PDO->prepare($sql);
                                            $stmt->bindParam('PlayerId', $row_player['PlayerId'], PDO::PARAM_INT);
                                            $stmt->bindParam('EventId', $row_event['EventId'], PDO::PARAM_INT);
                                            $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                                            $stmt->execute();

                                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                $crossFit_ShuttleRun = ($row['ShuttleRun'] > 0) ? secondsToHMSU($row['ShuttleRun']) : ''; // Time
                                                $crossFit_40YardDash = ($row['40YardDash'] > 0) ? secondsToHMSU($row['40YardDash']) : ''; // Time
                                                $crossFit_5105ConeDrill = ($row['5105ConeDrill'] > 0) ? secondsToHMSU($row['5105ConeDrill']) : ''; // Time
                                                $crossFit_3RMHang = ($row['3RMHang'] > 0) ? $row['3RMHang'] : ''; // Count
                                                $crossFit_VerticalJump = ($row['VerticalJump'] > 0) ? $row['VerticalJump'] : ''; // Inch
                                                $crossFit_BroadJump = ($row['BroadJump'] > 0) ? $row['BroadJump'] : ''; // Inch
                                                $crossFit_PowerClean = ($row['PowerClean'] > 0) ? $row['PowerClean'] : ''; // Count
                                                $crossFit_PullUps = ($row['PullUps'] > 0) ? $row['PullUps'] : ''; // Count
                                                $crossFit_PushUps = ($row['PullUps'] > 0) ? $row['PushUps'] : ''; // Count
                                            }

                                        } catch (PDOException $e) {
                                            trigger_error($e->getMessage(), E_USER_ERROR);
                                        }//end try

                                        $stat_CrossFit_ShuttleRun .= '
                                        <div class="tr">
                                            <div class="td"></div>
                                            <div class="td">Round ' . $i . '</div>
                                            <div class="td">
                                                <input name="crossFit_ShuttleRun[]" type="text" placeholder="M:S.U" class="input_box format_time" value="' . $crossFit_ShuttleRun . '"/>
                                            </div>
                                            <div class="td"></div>
                                        </div>';
                                        $stat_CrossFit_40YardDash .= '
                                        <div class="tr">
                                            <div class="td"></div>
                                            <div class="td">Round ' . $i . '</div>
                                            <div class="td">
                                                <input name="crossFit_40YardDash[]" type="text" placeholder="M:S.U" class="input_box format_time" value="' . $crossFit_40YardDash . '"/>
                                            </div>
                                            <div class="td"></div>
                                        </div>';
                                        $stat_CrossFit_5105ConeDrill .= '
                                        <div class="tr">
                                            <div class="td"></div>
                                            <div class="td">Round ' . $i . '</div>
                                            <div class="td">
                                                <input name="crossFit_5105ConeDrill[]" type="text" placeholder="M:S.U" class="input_box format_time" value="' . $crossFit_5105ConeDrill . '"/>
                                            </div>
                                            <div class="td"></div>
                                        </div>';
                                        $stat_CrossFit_3RMHang .= '
                                        <div class="tr">
                                            <div class="td"></div>
                                            <div class="td">Round ' . $i . '</div>
                                            <div class="td">
                                                <input name="crossFit_3RMHang[]" type="tel" placeholder="0" class="input_box format_count" value="' . $crossFit_3RMHang . '"/>
                                            </div>
                                            <div class="td"></div>
                                        </div>';
                                        $stat_CrossFit_VerticalJump .= '
                                        <div class="tr">
                                            <div class="td"></div>
                                            <div class="td">Round ' . $i . '</div>
                                            <div class="td">
                                                <input name="crossFit_VerticalJump[]" type="tel" placeholder="0.00" class="input_box format_inch" value="' . $crossFit_VerticalJump . '"/>
                                            </div>
                                            <div class="td"></div>
                                        </div>';
                                        $stat_CrossFit_BroadJump .= '
                                        <div class="tr">
                                            <div class="td"></div>
                                            <div class="td">Round ' . $i . '</div>
                                            <div class="td">
                                                <input name="crossFit_BroadJump[]" type="tel" placeholder="0.00" class="input_box format_inch" value="' . $crossFit_BroadJump . '"/>
                                            </div>
                                            <div class="td"></div>
                                        </div>';
                                        $stat_CrossFit_PowerClean .= '
                                        <div class="tr">
                                            <div class="td"></div>
                                            <div class="td">Round ' . $i . '</div>
                                            <div class="td">
                                                <input name="crossFit_PowerClean[]" type="tel" placeholder="0" class="input_box format_count" value="' . $crossFit_PowerClean . '"/>
                                            </div>
                                            <div class="td"></div>
                                        </div>';
                                        $stat_CrossFit_PullUps .= '
                                        <div class="tr">
                                            <div class="td"></div>
                                            <div class="td">Round ' . $i . '</div>
                                            <div class="td">
                                                <input name="crossFit_PullUps[]" type="tel" placeholder="0" class="input_box format_count" value="' . $crossFit_PullUps . '"/>
                                            </div>
                                            <div class="td"></div>
                                        </div>';
                                        $stat_CrossFit_PushUps .= '
                                        <div class="tr">
                                            <div class="td"></div>
                                            <div class="td">Round ' . $i . '</div>
                                            <div class="td">
                                                <input name="crossFit_PushUps[]" type="tel" placeholder="0" class="input_box format_count" value="' . $crossFit_PushUps . '"/>
                                            </div>
                                            <div class="td"></div>
                                        </div>';

                                        if ($i == 1) {
                                            $playerChartRows .= '
                                            <tr data-id="' . $playerId . '" data-event="' . $row_event['EventId'] . '">
                                                <td><button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'CrossFit">Go</button></td>
                                                <td>
                                                    <div class="score_table_img text-center">
                                                        <a href="index.php?page=player&token=' . $playerToken . '" target="_blank"><img src="' . $playerPicture . '" alt="' . $playerFullName . '" class="img-responsive img-circle"></a>
                                                        ' . $row_player['GradYear'] . '
                                                    </div>
                                                </td>
                                                <td>' . $row_player['TeamId'] . '</td>
                                                <td><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#editPlayer' . $playerId . 'ForEvent' . $row_event['EventId'] . '">' . $playerFullNameReverse . '</td>
                                                <td><img src="images/rating.png" alt="Rating" class="img-responsive"></td>
                                            </tr>';
                                            $editPlayerForEvent .= '
                                            <!-- Edit Event Step 1 -->
                                            <div id="editPlayer' . $playerId . 'ForEvent' . $row_event['EventId'] . '" data-id="' . $row_event['EventId'] . '" class="modal forget_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>Edit Player</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form id="editPlayer' . $playerId . 'ForEvent' . $row_event['EventId'] . 'Form" class="form-horizontal media_popup add_new_events">
                                                                    <div class="form-group">
                                                                        <label class="control-label col-sm-4" for="playerTeamId' . $playerId . '">Team Id</label>
                                                                        <div class="col-sm-8">
                                                                            <input type="text" id="playerTeamId' . $playerId . '" name="playerTeamId" class="form-control input_box" value="' . $row_player['TeamId'] . '">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="control-label col-sm-4" for="playerGender' . $playerId . '">Gender</label>
                                                                        <div class="col-sm-8">
                                                                            <select id="playerGender' . $playerId . '" name="playerGender" class="form-control input_box">
                                                                                ' . $playerGenderOptions . '
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="control-label col-sm-4" for="playerFirstName' . $playerId . '">First Name</label>
                                                                        <div class="col-sm-8">
                                                                            <input type="text" id="playerFirstName' . $playerId . '" name="playerFirstName" class="form-control input_box" value="' . $row_player['FirstName'] . '">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="control-label col-sm-4" for="playerMiddleName' . $playerId . '">Initial</label>
                                                                        <div class="col-sm-8">
                                                                            <input type="text" id="playerMiddleName' . $playerId . '" name="playerMiddleName" class="form-control input_box" value="' . $row_player['MiddleName'] . '">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="control-label col-sm-4" for="playerLastName' . $playerId . '">Last Name</label>
                                                                        <div class="col-sm-8">
                                                                            <input type="text" id="playerLastName' . $playerId . '" name="playerLastName" class="form-control input_box" value="' . $row_player['LastName'] . '">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="control-label col-sm-4" for="playerGradYear' . $playerId . '">Grad Year</label>
                                                                        <div class="col-sm-8">
                                                                            <input type="tel" id="playerGradYear' . $playerId . '" name="playerGradYear" class="form-control input_box" value="' . $row_player['GradYear'] . '">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="control-label col-sm-4" for="playerDOB' . $playerId . '">DOB</label>
                                                                        <div class="col-sm-8">
                                                                            <input type="text" id="playerDOB' . $playerId . '" name="playerDOB" class="form-control input_box" placeholder="MM/DD/YYYY" value="' . $row_player['DOB'] . '">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="control-label col-sm-4" for="playerCity' . $playerId . '">City</label>
                                                                        <div class="col-sm-8">
                                                                            <input type="text" id="playerCity' . $playerId . '" name="playerCity" class="form-control input_box" value="' . $row_player['City'] . '">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="control-label col-sm-4" for="playerStateShort' . $playerId . '">State</label>
                                                                        <div class="col-sm-8">
                                                                            <select id="playerStateShort' . $playerId . '" name="playerStateShort" class="form-control input_box">
                                                                                <option value="">Select State</option>
                                                                                ' . $playerStateOptions . '
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="control-label col-sm-4" for="playerEmail' . $playerId . '">Email</label>
                                                                        <div class="col-sm-8">
                                                                            <input type="text" id="playerEmail' . $playerId . '" name="playerEmail" class="form-control input_box" value="' . $row_player['Email'] . '">
                                                                        </div>
                                                                    </div>
                                                                    <div class="editPlayerForEventErrorBox"></div>
                                                                    <div class="all_btn">
                                                                        <div class="blue_btn">
                                                                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#editEvent' . $row_event['EventId'] . 'Results"><i class="fa fa-chevron-left"></i> Back</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerForEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                                    <input type="hidden" name="token" value="' . $row_player['Token'] . '">
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /editPlayerForEvent Cross Fit -->';

                                            $playerStatItemsForEvent .= '<!-- Add Player Stats For Event Cross Fit -->
                                            <div id="player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'CrossFit" class="modal forget_section player_p_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>Player Stats For ' . $row_event['Name'] . '</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h2>' . $playerFullName . '</h2>
                                                                <div class="player_table">
                                                                    <div class="table">
                                                                        <div class="tbody">
                                                                            <div class="tr">
                                                                                <div class="td text-center"><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'CrossFitShuttleRun">Shuttle Run</a></div>
                                                                            </div>
                                                                            <div class="tr">
                                                                                <div class="td text-center"><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'CrossFit40YardDash">40 Yard Dash</a></div>
                                                                            </div>
                                                                            <div class="tr">
                                                                                <div class="td text-center"><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'CrossFit5105ConeDrill">5-10-5 Cone Drill</a></div>
                                                                            </div>
                                                                            <div class="tr">
                                                                                <div class="td text-center"><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'CrossFit3RMHang">3 RM Hang</a></div>
                                                                            </div>
                                                                            <div class="tr">
                                                                                <div class="td text-center"><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'CrossFitVerticalJump">Vertical Jump</a></div>
                                                                            </div>
                                                                            <div class="tr">
                                                                                <div class="td text-center"><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'CrossFitBroadJump">Broad Jump</a></div>
                                                                            </div>
                                                                            <div class="tr">
                                                                                <div class="td text-center"><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'CrossFitPowerClean">Power Clean</a></div>
                                                                            </div>
                                                                            <div class="tr">
                                                                                <div class="td text-center"><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'CrossFitPullUps">Pull Ups</a></div>
                                                                            </div>
                                                                            <div class="tr">
                                                                                <div class="td text-center"><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'CrossFitPushUps">Push Ups</a></div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="all_btn">
                                                                    <div class="blue_btn">
                                                                        <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#editEvent' . $row_event['EventId'] . 'Results"><i class="fa fa-chevron-left"></i> Back</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div><!-- /playerStatItemsForEvent Cross Fit -->';
                                        } // end if

                                        if ($i == $eventRounds) {
                                            $playerStatsForEvent .= '<!-- Edit Player Stats For Event Cross Fit Shuttle Run -->
                                            <div id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'CrossFitShuttleRun" class="modal forget_section player_p_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>Shuttle Run</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h2>' . $playerFullName . ' / ' . $row_event['Name'] . '</h2>
                                                                <form id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'CrossFitShuttleRunForm" class="player_accordion">
                                                                    <div class="player_table">
                                                                        <div class="table">
                                                                            <div class="thead">
                                                                                <div class="tr">
                                                                                    <div class="th"></div>
                                                                                    <div class="th">Item</div>
                                                                                    <div class="th">Time</div>
                                                                                    <div class="th"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tbody">
                                                                                ' . $stat_CrossFit_ShuttleRun . '
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="playerId" value="' . $playerId . '">
                                                                    <input type="hidden" name="sport" value="CrossFit">
                                                                    <input type="hidden" name="activity" value="ShuttleRun">
                                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                                    <input type="hidden" name="token" value="' . $row_player['Token'] . '">
                                                                    <div class="editPlayerStatsForEventErrorBox"></div>
                                                                    <input type="text" name="statsEntryDate" class="input_box" placeholder="Date" value="' . date('m/d/Y') . '">
                                                                    <div class="all_btn">
                                                                        <div class="blue_btn">
                                                                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'CrossFit"><i class="fa fa-chevron-left"></i> Back</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-savepause="true" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait.."><i class="fa fa-pause"></i> Save/Pause</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Edit Player Stats For Event Cross Fit Shuttle Run -->';
                                            $playerStatsForEvent .= '<!-- Edit Player Stats For Event Cross Fit 40 Yard Dash -->
                                            <div id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'CrossFit40YardDash" class="modal forget_section player_p_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>40 Yard Dash</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h2>' . $playerFullName . ' / ' . $row_event['Name'] . '</h2>
                                                                <form id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'CrossFit40YardDashForm" class="player_accordion">
                                                                    <div class="player_table">
                                                                        <div class="table">
                                                                            <div class="thead">
                                                                                <div class="tr">
                                                                                    <div class="th"></div>
                                                                                    <div class="th">Item</div>
                                                                                    <div class="th">Time</div>
                                                                                    <div class="th"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tbody">
                                                                                ' . $stat_CrossFit_40YardDash . '
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="playerId" value="' . $playerId . '">
                                                                    <input type="hidden" name="sport" value="CrossFit">
                                                                    <input type="hidden" name="activity" value="40YardDash">
                                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                                    <input type="hidden" name="token" value="' . $row_player['Token'] . '">
                                                                    <div class="editPlayerStatsForEventErrorBox"></div>
                                                                    <input type="text" name="statsEntryDate" class="input_box" placeholder="Date" value="' . date('m/d/Y') . '">
                                                                    <div class="all_btn">
                                                                        <div class="blue_btn">
                                                                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'CrossFit"><i class="fa fa-chevron-left"></i> Back</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-savepause="true" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait.."><i class="fa fa-pause"></i> Save/Pause</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Edit Player Stats For Event Cross Fit 40 Yard Dash -->';
                                            $playerStatsForEvent .= '<!-- Edit Player Stats For Event Cross Fit 5-10-5 Cone Drill -->
                                            <div id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'CrossFit5105ConeDrill" class="modal forget_section player_p_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>5-10-5 Cone Drill</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h2>' . $playerFullName . ' / ' . $row_event['Name'] . '</h2>
                                                                <form id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'CrossFit5105ConeDrillForm" class="player_accordion">
                                                                    <div class="player_table">
                                                                        <div class="table">
                                                                            <div class="thead">
                                                                                <div class="tr">
                                                                                    <div class="th"></div>
                                                                                    <div class="th">Item</div>
                                                                                    <div class="th">Time</div>
                                                                                    <div class="th"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tbody">
                                                                                ' . $stat_CrossFit_5105ConeDrill . '
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="playerId" value="' . $playerId . '">
                                                                    <input type="hidden" name="sport" value="CrossFit">
                                                                    <input type="hidden" name="activity" value="5105ConeDrill">
                                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                                    <input type="hidden" name="token" value="' . $row_player['Token'] . '">
                                                                    <div class="editPlayerStatsForEventErrorBox"></div>
                                                                    <input type="text" name="statsEntryDate" class="input_box" placeholder="Date" value="' . date('m/d/Y') . '">
                                                                    <div class="all_btn">
                                                                        <div class="blue_btn">
                                                                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'CrossFit"><i class="fa fa-chevron-left"></i> Back</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-savepause="true" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait.."><i class="fa fa-pause"></i> Save/Pause</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Edit Player Stats For Event Cross Fit 5-10-5 Cone Drill -->';
                                            $playerStatsForEvent .= '<!-- Edit Player Stats For Event Cross Fit 3 RM Hang -->
                                            <div id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'CrossFit3RMHang" class="modal forget_section player_p_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>3 RM Hang</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h2>' . $playerFullName . ' / ' . $row_event['Name'] . '</h2>
                                                                <form id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'CrossFit3RMHangForm" class="player_accordion">
                                                                    <div class="player_table">
                                                                        <div class="table">
                                                                            <div class="thead">
                                                                                <div class="tr">
                                                                                    <div class="th"></div>
                                                                                    <div class="th">Item</div>
                                                                                    <div class="th">Count</div>
                                                                                    <div class="th"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tbody">
                                                                                ' . $stat_CrossFit_3RMHang . '
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="playerId" value="' . $playerId . '">
                                                                    <input type="hidden" name="sport" value="CrossFit">
                                                                    <input type="hidden" name="activity" value="3RMHang">
                                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                                    <input type="hidden" name="token" value="' . $row_player['Token'] . '">
                                                                    <div class="editPlayerStatsForEventErrorBox"></div>
                                                                    <input type="text" name="statsEntryDate" class="input_box" placeholder="Date" value="' . date('m/d/Y') . '">
                                                                    <div class="all_btn">
                                                                        <div class="blue_btn">
                                                                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'CrossFit"><i class="fa fa-chevron-left"></i> Back</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-savepause="true" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait.."><i class="fa fa-pause"></i> Save/Pause</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Edit Player Stats For Event Cross Fit 3 RM Hang -->';
                                            $playerStatsForEvent .= '<!-- Edit Player Stats For Event Cross Fit Vertical Jump -->
                                            <div id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'CrossFitVerticalJump" class="modal forget_section player_p_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>Vertical Jump</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h2>' . $playerFullName . ' / ' . $row_event['Name'] . '</h2>
                                                                <form id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'CrossFitVerticalJumpForm" class="player_accordion">
                                                                    <div class="player_table">
                                                                        <div class="table">
                                                                            <div class="thead">
                                                                                <div class="tr">
                                                                                    <div class="th"></div>
                                                                                    <div class="th">Item</div>
                                                                                    <div class="th">Inch</div>
                                                                                    <div class="th"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tbody">
                                                                                ' . $stat_CrossFit_VerticalJump . '
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="playerId" value="' . $playerId . '">
                                                                    <input type="hidden" name="sport" value="CrossFit">
                                                                    <input type="hidden" name="activity" value="VerticalJump">
                                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                                    <input type="hidden" name="token" value="' . $row_player['Token'] . '">
                                                                    <div class="editPlayerStatsForEventErrorBox"></div>
                                                                    <input type="text" name="statsEntryDate" class="input_box" placeholder="Date" value="' . date('m/d/Y') . '">
                                                                    <div class="all_btn">
                                                                        <div class="blue_btn">
                                                                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'CrossFit"><i class="fa fa-chevron-left"></i> Back</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-savepause="true" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait.."><i class="fa fa-pause"></i> Save/Pause</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Edit Player Stats For Event Cross Fit Vertical Jump -->';
                                            $playerStatsForEvent .= '<!-- Edit Player Stats For Event Cross Fit Broad Jump -->
                                            <div id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'CrossFitBroadJump" class="modal forget_section player_p_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>Broad Jump</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h2>' . $playerFullName . ' / ' . $row_event['Name'] . '</h2>
                                                                <form id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'CrossFitBroadJumpForm" class="player_accordion">
                                                                    <div class="player_table">
                                                                        <div class="table">
                                                                            <div class="thead">
                                                                                <div class="tr">
                                                                                    <div class="th"></div>
                                                                                    <div class="th">Item</div>
                                                                                    <div class="th">Inch</div>
                                                                                    <div class="th"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tbody">
                                                                                ' . $stat_CrossFit_BroadJump . '
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="playerId" value="' . $playerId . '">
                                                                    <input type="hidden" name="sport" value="CrossFit">
                                                                    <input type="hidden" name="activity" value="BroadJump">
                                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                                    <input type="hidden" name="token" value="' . $row_player['Token'] . '">
                                                                    <div class="editPlayerStatsForEventErrorBox"></div>
                                                                    <input type="text" name="statsEntryDate" class="input_box" placeholder="Date" value="' . date('m/d/Y') . '">
                                                                    <div class="all_btn">
                                                                        <div class="blue_btn">
                                                                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'CrossFit"><i class="fa fa-chevron-left"></i> Back</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-savepause="true" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait.."><i class="fa fa-pause"></i> Save/Pause</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Edit Player Stats For Event Cross Fit Broad Jump -->';
                                            $playerStatsForEvent .= '<!-- Edit Player Stats For Event Cross Fit Power Clean -->
                                            <div id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'CrossFitPowerClean" class="modal forget_section player_p_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>Power Clean</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h2>' . $playerFullName . ' / ' . $row_event['Name'] . '</h2>
                                                                <form id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'CrossFitPowerCleanForm" class="player_accordion">
                                                                    <div class="player_table">
                                                                        <div class="table">
                                                                            <div class="thead">
                                                                                <div class="tr">
                                                                                    <div class="th"></div>
                                                                                    <div class="th">Item</div>
                                                                                    <div class="th">Count</div>
                                                                                    <div class="th"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tbody">
                                                                                ' . $stat_CrossFit_PowerClean . '
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="playerId" value="' . $playerId . '">
                                                                    <input type="hidden" name="sport" value="CrossFit">
                                                                    <input type="hidden" name="activity" value="PowerClean">
                                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                                    <input type="hidden" name="token" value="' . $row_player['Token'] . '">
                                                                    <div class="editPlayerStatsForEventErrorBox"></div>
                                                                    <input type="text" name="statsEntryDate" class="input_box" placeholder="Date" value="' . date('m/d/Y') . '">
                                                                    <div class="all_btn">
                                                                        <div class="blue_btn">
                                                                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'CrossFit"><i class="fa fa-chevron-left"></i> Back</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-savepause="true" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait.."><i class="fa fa-pause"></i> Save/Pause</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Edit Player Stats For Event Cross Fit Power Clean -->';
                                            $playerStatsForEvent .= '<!-- Edit Player Stats For Event Cross Fit Pull Ups -->
                                            <div id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'CrossFitPullUps" class="modal forget_section player_p_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>Pull Ups</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h2>' . $playerFullName . ' / ' . $row_event['Name'] . '</h2>
                                                                <form id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'CrossFitPullUpsForm" class="player_accordion">
                                                                    <div class="player_table">
                                                                        <div class="table">
                                                                            <div class="thead">
                                                                                <div class="tr">
                                                                                    <div class="th"></div>
                                                                                    <div class="th">Item</div>
                                                                                    <div class="th">Count</div>
                                                                                    <div class="th"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tbody">
                                                                                ' . $stat_CrossFit_PullUps . '
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="playerId" value="' . $playerId . '">
                                                                    <input type="hidden" name="sport" value="CrossFit">
                                                                    <input type="hidden" name="activity" value="PullUps">
                                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                                    <input type="hidden" name="token" value="' . $row_player['Token'] . '">
                                                                    <div class="editPlayerStatsForEventErrorBox"></div>
                                                                    <input type="text" name="statsEntryDate" class="input_box" placeholder="Date" value="' . date('m/d/Y') . '">
                                                                    <div class="all_btn">
                                                                        <div class="blue_btn">
                                                                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'CrossFit"><i class="fa fa-chevron-left"></i> Back</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-savepause="true" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait.."><i class="fa fa-pause"></i> Save/Pause</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Edit Player Stats For Event Cross Fit Pull Ups -->';
                                            $playerStatsForEvent .= '<!-- Edit Player Stats For Event Cross Fit Push Ups -->
                                            <div id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'CrossFitPushUps" class="modal forget_section player_p_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>Push Ups</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h2>' . $playerFullName . ' / ' . $row_event['Name'] . '</h2>
                                                                <form id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'CrossFitPushUpsForm" class="player_accordion">
                                                                    <div class="player_table">
                                                                        <div class="table">
                                                                            <div class="thead">
                                                                                <div class="tr">
                                                                                    <div class="th"></div>
                                                                                    <div class="th">Item</div>
                                                                                    <div class="th">Count</div>
                                                                                    <div class="th"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tbody">
                                                                                ' . $stat_CrossFit_PushUps . '
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="playerId" value="' . $playerId . '">
                                                                    <input type="hidden" name="sport" value="CrossFit">
                                                                    <input type="hidden" name="activity" value="PushUps">
                                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                                    <input type="hidden" name="token" value="' . $row_player['Token'] . '">
                                                                    <div class="editPlayerStatsForEventErrorBox"></div>
                                                                    <input type="text" name="statsEntryDate" class="input_box" placeholder="Date" value="' . date('m/d/Y') . '">
                                                                    <div class="all_btn">
                                                                        <div class="blue_btn">
                                                                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'CrossFit"><i class="fa fa-chevron-left"></i> Back</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-savepause="true" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait.."><i class="fa fa-pause"></i> Save/Pause</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Edit Player Stats For Event Cross Fit Push Ups -->';
                                        }// end if

                                    } // end Cross Fit

                                    // Fast Pitch
                                    if ($sportNameLowercase === 'fastpitch') {
                                        $fastPitch_VelocityMound = ''; // MPH
                                        $fastPitch_VelocityOutfield = ''; // MPH
                                        $fastPitch_VelocityInfield = ''; // MPH
                                        $fastPitch_SwingVelocity = ''; // MPH
                                        $fastPitch_60YardDash = ''; // Time
                                        $fastPitch_CatcherPop = ''; // Time
                                        $fastPitch_CatcherRelease = ''; // Time
                                        $fastPitch_PrimaryPosition = ''; // Position Number
                                        $fastPitch_SecondaryPosition = ''; // Position Number
                                        $fastPitch_TeeVelocity = ''; // MPH

                                        try {
                                            $sql = "
                                            SELECT
                                              `GameFastPitch`.*
                                            FROM 
                                              `GameFastPitch`
                                            WHERE
                                              `GameFastPitch`.`PlayerId` = :PlayerId
                                            AND 
                                              `GameFastPitch`.`EventId` = :EventId
                                            AND 
                                              `GameFastPitch`.`Round` = :Round";

                                            $stmt = $PDO->prepare($sql);
                                            $stmt->bindParam('PlayerId', $row_player['PlayerId'], PDO::PARAM_INT);
                                            $stmt->bindParam('EventId', $row_event['EventId'], PDO::PARAM_INT);
                                            $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                                            $stmt->execute();

                                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                $fastPitch_VelocityMound = ($row['VelocityMound'] > 0) ? $row['VelocityMound'] : ''; // MPH
                                                $fastPitch_VelocityOutfield = ($row['VelocityOutfield'] > 0) ? $row['VelocityOutfield'] : ''; // MPH
                                                $fastPitch_VelocityInfield = ($row['VelocityInfield'] > 0) ? $row['VelocityInfield'] : ''; // MPH
                                                $fastPitch_SwingVelocity = ($row['SwingVelocity'] > 0) ? $row['SwingVelocity'] : ''; // MPH
                                                $fastPitch_60YardDash = ($row['60YardDash'] > 0) ? secondsToHMSU($row['60YardDash']) : ''; // Time
                                                $fastPitch_CatcherPop = ($row['CatcherPop'] > 0) ? secondsToHMSU($row['CatcherPop']) : ''; // Time
                                                $fastPitch_CatcherRelease = ($row['CatcherRelease'] > 0) ? secondsToHMSU($row['CatcherRelease']) : ''; // Time
                                                $fastPitch_PrimaryPosition = ($row['PrimaryPosition'] > 0) ? $row['PrimaryPosition'] : ''; // Position Number
                                                $fastPitch_SecondaryPosition = ($row['SecondaryPosition'] > 0) ? $row['SecondaryPosition'] : ''; // Position Number
                                                $fastPitch_TeeVelocity = ($row['TeeVelocity'] > 0) ? $row['TeeVelocity'] : ''; // MPH
                                            }

                                        } catch (PDOException $e) {
                                            trigger_error($e->getMessage(), E_USER_ERROR);
                                        }//end try

                                        $fastPitchPrimaryPositionOptions = '';
                                        $fastPitchSecondaryPositionOptions = '';
                                        $baseballFieldPositions = array(
                                            'P' => 'Pitcher',
                                            'C' => 'Catcher',
                                            '1B' => '1st Baseman',
                                            '2B' => '2nd Baseman',
                                            '3B' => '3rd Baseman',
                                            'SS' => 'Shortstop',
                                            'LF' => 'Left Fielder',
                                            'CF' => 'Center Fielder',
                                            'RF' => 'Right Fielder'
                                        );

                                        foreach ($baseballFieldPositions as $key => $value) {
                                            if (getBaseballFieldPositionNumber($key) == $fastPitch_PrimaryPosition) {
                                                $fastPitchPrimaryPositionOptions .= '<option value="' . getBaseballFieldPositionNumber($key) . '" selected="selected">' . $value . '</option>';
                                            } else {
                                                $fastPitchPrimaryPositionOptions .= '<option value="' . getBaseballFieldPositionNumber($key) . '">' . $value . '</option>';
                                            }
                                        }

                                        foreach ($baseballFieldPositions as $key => $value) {
                                            if (getBaseballFieldPositionNumber($key) == $fastPitch_SecondaryPosition) {
                                                $fastPitchSecondaryPositionOptions .= '<option value="' . getBaseballFieldPositionNumber($key) . '" selected="selected">' . $value . '</option>';
                                            } else {
                                                $fastPitchSecondaryPositionOptions .= '<option value="' . getBaseballFieldPositionNumber($key) . '">' . $value . '</option>';
                                            }
                                        }

                                        $stat_FastPitch_VelocityMound .= '
                                        <div class="tr">
                                            <div class="td"></div>
                                            <div class="td">Round ' . $i . '</div>
                                            <div class="td">
                                                <input name="fastPitch_VelocityMound[]" type="tel" placeholder="0" class="input_box format_mph" value="' . $fastPitch_VelocityMound . '"/>
                                            </div>
                                            <div class="td"></div>
                                        </div>';
                                        $stat_FastPitch_VelocityOutfield .= '
                                        <div class="tr">
                                            <div class="td"></div>
                                            <div class="td">Round ' . $i . '</div>
                                            <div class="td">
                                                <input name="fastPitch_VelocityOutfield[]" type="tel" placeholder="0" class="input_box format_mph" value="' . $fastPitch_VelocityOutfield . '"/>
                                            </div>
                                            <div class="td"></div>
                                        </div>';
                                        $stat_FastPitch_VelocityInfield .= '
                                        <div class="tr">
                                            <div class="td"></div>
                                            <div class="td">Round ' . $i . '</div>
                                            <div class="td">
                                                <input name="fastPitch_VelocityInfield[]" type="tel" placeholder="0" class="input_box format_mph" value="' . $fastPitch_VelocityInfield . '"/>
                                            </div>
                                            <div class="td"></div>
                                        </div>';
                                        $stat_FastPitch_SwingVelocity .= '
                                        <div class="tr">
                                            <div class="td"></div>
                                            <div class="td">Round ' . $i . '</div>
                                            <div class="td">
                                                <input name="fastPitch_SwingVelocity[]" type="tel" placeholder="0" class="input_box format_mph" value="' . $fastPitch_SwingVelocity . '"/>
                                            </div>
                                            <div class="td"></div>
                                        </div>';
                                        $stat_FastPitch_60YardDash .= '
                                        <div class="tr">
                                            <div class="td"></div>
                                            <div class="td">Round ' . $i . '</div>
                                            <div class="td">
                                                <input name="fastPitch_60YardDash[]" type="text" placeholder="M:S.U" class="input_box format_time" value="' . $fastPitch_60YardDash . '"/>
                                            </div>
                                            <div class="td"></div>
                                        </div>';
                                        $stat_FastPitch_CatcherPop .= '
                                        <div class="tr">
                                            <div class="td"></div>
                                            <div class="td">Round ' . $i . '</div>
                                            <div class="td">
                                                <input name="fastPitch_CatcherPop[]" type="text" placeholder="M:S.U" class="input_box format_time" value="' . $fastPitch_CatcherPop . '"/>
                                            </div>
                                            <div class="td"></div>
                                        </div>';
                                        $stat_FastPitch_CatcherRelease .= '
                                        <div class="tr">
                                            <div class="td"></div>
                                            <div class="td">Round ' . $i . '</div>
                                            <div class="td">
                                                <input name="fastPitch_CatcherRelease[]" type="text" placeholder="M:S.U" class="input_box format_time" value="' . $fastPitch_CatcherRelease . '"/>
                                            </div>
                                            <div class="td"></div>
                                        </div>';
                                        $stat_FastPitch_TeeVelocity .= '
                                        <div class="tr">
                                            <div class="td"></div>
                                            <div class="td">Round ' . $i . '</div>
                                            <div class="td">
                                                <input name="fastPitch_TeeVelocity[]" type="tel" placeholder="0" class="input_box format_mph" value="' . $fastPitch_TeeVelocity . '"/>
                                            </div>
                                            <div class="td"></div>
                                        </div>';

                                        if ($i == 1) {
                                            $playerChartRows .= '
                                            <tr data-id="' . $playerId . '" data-event="' . $row_event['EventId'] . '">
                                                <td><button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'FastPitch">Go</button></td>
                                                <td>
                                                    <div class="score_table_img text-center">
                                                        <a href="index.php?page=player&token=' . $playerToken . '" target="_blank"><img src="' . $playerPicture . '" alt="' . $playerFullName . '" class="img-responsive img-circle"></a>
                                                        ' . $row_player['GradYear'] . '
                                                    </div>
                                                </td>
                                                <td>' . $row_player['TeamId'] . '</td>
                                                <td><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#editPlayer' . $playerId . 'ForEvent' . $row_event['EventId'] . '">' . $playerFullNameReverse . '</td>
                                                <td><span class="visible-xs"><a href="#" data-toggle="tooltip" title="' . getBaseballFieldPositionName($fastPitch_PrimaryPosition) . '">' . getBaseballFieldPositionId($fastPitch_PrimaryPosition) . '</a>, <a href="#" data-toggle="tooltip" title="' . getBaseballFieldPositionName($fastPitch_SecondaryPosition) . '">' . getBaseballFieldPositionId($fastPitch_SecondaryPosition) . '</a></span><span class="hidden-xs">' . getBaseballFieldPositionName($fastPitch_PrimaryPosition) . ', ' . getBaseballFieldPositionName($fastPitch_SecondaryPosition) . '</span></td>
                                                <td><img src="images/rating.png" alt="Rating" class="img-responsive"></td>
                                            </tr>';
                                            $editPlayerForEvent .= '
                                                <!-- Edit Event Step 1 -->
                                                <div id="editPlayer' . $playerId . 'ForEvent' . $row_event['EventId'] . '" data-id="' . $row_event['EventId'] . '" class="modal forget_section" role="dialog">
                                                    <div class="modal-dialog">
                                                        <!-- Modal content-->
                                                        <div class="container">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <div class="close_img">
                                                                        <button type="button" class="close" data-dismiss="modal"></button>
                                                                    </div>
                                                                    <div class="title_h2">
                                                                        <h2>Edit Player</h2>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <form id="editPlayer' . $playerId . 'ForEvent' . $row_event['EventId'] . 'Form" class="form-horizontal media_popup add_new_events">
                                                                        <div class="form-group">
                                                                            <label class="control-label col-sm-4" for="playerTeamId' . $playerId . '">Team Id</label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text" id="playerTeamId' . $playerId . '" name="playerTeamId" class="form-control input_box" value="' . $row_player['TeamId'] . '">
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="control-label col-sm-4" for="pGender' . $playerId . '">Gender</label>
                                                                            <div class="col-sm-8">
                                                                                <select id="pGender' . $playerId . '" name="pGender" class="form-control input_box">
                                                                                    ' . $playerGenderOptions . '
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="control-label col-sm-4" for="playerFirstName' . $playerId . '">First Name</label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text" id="playerFirstName' . $playerId . '" name="playerFirstName" class="form-control input_box" value="' . $row_player['FirstName'] . '">
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="control-label col-sm-4" for="playerMiddleName' . $playerId . '">Initial</label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text" id="playerMiddleName' . $playerId . '" name="playerMiddleName" class="form-control input_box" value="' . $row_player['MiddleName'] . '">
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="control-label col-sm-4" for="playerLastName' . $playerId . '">Last Name</label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text" id="playerLastName' . $playerId . '" name="playerLastName" class="form-control input_box" value="' . $row_player['LastName'] . '">
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="control-label col-sm-4" for="playerGradYear' . $playerId . '">Grad Year</label>
                                                                            <div class="col-sm-8">
                                                                                <input type="tel" id="playerGradYear' . $playerId . '" name="playerGradYear" class="form-control input_box" value="' . $row_player['GradYear'] . '">
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="control-label col-sm-4" for="playerDOB' . $playerId . '">DOB</label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text" id="playerDOB' . $playerId . '" name="playerDOB" class="form-control input_box" placeholder="MM/DD/YYYY" value="' . $row_player['DOB'] . '">
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="control-label col-sm-4" for="playerCity' . $playerId . '">City</label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text" id="playerCity' . $playerId . '" name="playerCity" class="form-control input_box" value="' . $row_player['City'] . '">
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="control-label col-sm-4" for="playerStateShort' . $playerId . '">State</label>
                                                                            <div class="col-sm-8">
                                                                                <select id="playerStateShort' . $playerId . '" name="playerStateShort" class="form-control input_box">
                                                                                    <option value="">Select State</option>
                                                                                    ' . $playerStateOptions . '
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="control-label col-sm-4" for="playerEmail' . $playerId . '">Email</label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text" id="playerEmail' . $playerId . '" name="playerEmail" class="form-control input_box" value="' . $row_player['Email'] . '">
                                                                            </div>
                                                                        </div>
                                                                        <h3 class="text-center">Fast Pitch Field Position</h3>
                                                                        <div class="form-group">
                                                                            <label class="control-label col-sm-4" for="fastPitch_PrimaryPosition' . $playerId . '">Primary</label>
                                                                            <div class="col-sm-8">
                                                                                <select id="fastPitch_PrimaryPosition' . $playerId . '" name="fastPitch_PrimaryPosition" class="form-control input_box">
                                                                                    <option value="">Select Position</option>
                                                                                    ' . $fastPitchPrimaryPositionOptions . '
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="control-label col-sm-4" for="fastPitch_SecondaryPosition' . $playerId . '">Secondary</label>
                                                                            <div class="col-sm-8">
                                                                                <select id="fastPitch_SecondaryPosition' . $playerId . '" name="fastPitch_SecondaryPosition" class="form-control input_box">
                                                                                    <option value="">Select Position</option>
                                                                                    ' . $fastPitchSecondaryPositionOptions . '
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="editPlayerForEventErrorBox"></div>
                                                                        <div class="all_btn">
                                                                            <div class="blue_btn">
                                                                                <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#editEvent' . $row_event['EventId'] . 'Results"><i class="fa fa-chevron-left"></i> Back</button>
                                                                            </div>
                                                                            <div class="blue_btn">
                                                                                <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerForEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                                            </div>
                                                                        </div>
                                                                        <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                                        <input type="hidden" name="token" value="' . $row_player['Token'] . '">
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- /editPlayerForEvent Fast Pitch -->';

                                            $playerStatItemsForEvent .= '<!-- Add Player Stats For Event Fast Pitch -->
                                            <div id="player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'FastPitch" class="modal forget_section player_p_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>Player Stats For ' . $row_event['Name'] . '</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h2>' . $playerFullName . '</h2>
                                                                <div class="player_table">
                                                                    <div class="table">
                                                                        <div class="tbody">
                                                                            <div class="tr">
                                                                                <div class="td text-center"><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'FastPitchVelocityMound">Velocity Mound</a></div>
                                                                            </div>
                                                                            <div class="tr">
                                                                                <div class="td text-center"><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'FastPitchVelocityOutfield">Velocity Outfield</a></div>
                                                                            </div>
                                                                            <div class="tr">
                                                                                <div class="td text-center"><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'FastPitchVelocityInfield">Velocity Infield</a></div>
                                                                            </div>
                                                                            <div class="tr">
                                                                                <div class="td text-center"><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'FastPitchSwingVelocity">Swing Velocity</a></div>
                                                                            </div>
                                                                            <div class="tr">
                                                                                <div class="td text-center"><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'FastPitch60YardDash">60 Yard Dash</a></div>
                                                                            </div>
                                                                            <div class="tr">
                                                                                <div class="td text-center"><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'FastPitchCatcherPop">Catcher Pop</a></div>
                                                                            </div>
                                                                            <div class="tr">
                                                                                <div class="td text-center"><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'FastPitchCatcherRelease">Catcher Release</a></div>
                                                                            </div>
                                                                            <div class="tr">
                                                                                <div class="td text-center"><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'FastPitchTeeVelocity">Tee Velocity</a></div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="all_btn">
                                                                    <div class="blue_btn">
                                                                        <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#editEvent' . $row_event['EventId'] . 'Results"><i class="fa fa-chevron-left"></i> Back</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div><!-- /playerStatItemsForEvent Fast Pitch -->';
                                        } // end if

                                        if ($i == $eventRounds) {
                                            $playerStatsForEvent .= '<!-- Edit Player Stats For Event Fast Pitch Velocity Mound -->
                                            <div id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'FastPitchVelocityMound" class="modal forget_section player_p_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>Velocity Mound</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h2>' . $playerFullName . ' / ' . $row_event['Name'] . '</h2>
                                                                <form id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'FastPitchVelocityMoundForm" class="player_accordion">
                                                                    <div class="player_table">
                                                                        <div class="table">
                                                                            <div class="thead">
                                                                                <div class="tr">
                                                                                    <div class="th"></div>
                                                                                    <div class="th">Item</div>
                                                                                    <div class="th">MPH</div>
                                                                                    <div class="th"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tbody">
                                                                                ' . $stat_FastPitch_VelocityMound . '
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="playerId" value="' . $playerId . '">
                                                                    <input type="hidden" name="sport" value="FastPitch">
                                                                    <input type="hidden" name="activity" value="VelocityMound">
                                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                                    <input type="hidden" name="token" value="' . $row_player['Token'] . '">
                                                                    <div class="editPlayerStatsForEventErrorBox"></div>
                                                                    <input type="text" name="statsEntryDate" class="input_box" placeholder="Date" value="' . date('m/d/Y') . '">
                                                                    <div class="all_btn">
                                                                        <div class="blue_btn">
                                                                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'FastPitch"><i class="fa fa-chevron-left"></i> Back</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-savepause="true" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait.."><i class="fa fa-pause"></i> Save/Pause</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Edit Player Stats For Event Fast Pitch Velocity Mound -->';
                                            $playerStatsForEvent .= '<!-- Edit Player Stats For Event Fast Pitch Velocity Outfield -->
                                            <div id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'FastPitchVelocityOutfield" class="modal forget_section player_p_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>Velocity Outfield</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h2>' . $playerFullName . ' / ' . $row_event['Name'] . '</h2>
                                                                <form id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'FastPitchVelocityOutfieldForm" class="player_accordion">
                                                                    <div class="player_table">
                                                                        <div class="table">
                                                                            <div class="thead">
                                                                                <div class="tr">
                                                                                    <div class="th"></div>
                                                                                    <div class="th">Item</div>
                                                                                    <div class="th">MPH</div>
                                                                                    <div class="th"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tbody">
                                                                                ' . $stat_FastPitch_VelocityOutfield . '
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="playerId" value="' . $playerId . '">
                                                                    <input type="hidden" name="sport" value="FastPitch">
                                                                    <input type="hidden" name="activity" value="VelocityOutfield">
                                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                                    <input type="hidden" name="token" value="' . $row_player['Token'] . '">
                                                                    <div class="editPlayerStatsForEventErrorBox"></div>
                                                                    <input type="text" name="statsEntryDate" class="input_box" placeholder="Date" value="' . date('m/d/Y') . '">
                                                                    <div class="all_btn">
                                                                        <div class="blue_btn">
                                                                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'FastPitch"><i class="fa fa-chevron-left"></i> Back</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-savepause="true" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait.."><i class="fa fa-pause"></i> Save/Pause</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Edit Player Stats For Event Fast Pitch Velocity Outfield -->';
                                            $playerStatsForEvent .= '<!-- Edit Player Stats For Event Fast Pitch Velocity Infield -->
                                            <div id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'FastPitchVelocityInfield" class="modal forget_section player_p_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>Velocity Infield</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h2>' . $playerFullName . ' / ' . $row_event['Name'] . '</h2>
                                                                <form id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'FastPitchVelocityMoundForm" class="player_accordion">
                                                                    <div class="player_table">
                                                                        <div class="table">
                                                                            <div class="thead">
                                                                                <div class="tr">
                                                                                    <div class="th"></div>
                                                                                    <div class="th">Item</div>
                                                                                    <div class="th">MPH</div>
                                                                                    <div class="th"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tbody">
                                                                                ' . $stat_FastPitch_VelocityInfield . '
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="playerId" value="' . $playerId . '">
                                                                    <input type="hidden" name="sport" value="FastPitch">
                                                                    <input type="hidden" name="activity" value="VelocityMound">
                                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                                    <input type="hidden" name="token" value="' . $row_player['Token'] . '">
                                                                    <div class="editPlayerStatsForEventErrorBox"></div>
                                                                    <input type="text" name="statsEntryDate" class="input_box" placeholder="Date" value="' . date('m/d/Y') . '">
                                                                    <div class="all_btn">
                                                                        <div class="blue_btn">
                                                                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'FastPitch"><i class="fa fa-chevron-left"></i> Back</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-savepause="true" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait.."><i class="fa fa-pause"></i> Save/Pause</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Edit Player Stats For Event Fast Pitch Velocity Infield -->';
                                            $playerStatsForEvent .= '<!-- Edit Player Stats For Event Fast Pitch Swing Velocity -->
                                            <div id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'FastPitchSwingVelocity" class="modal forget_section player_p_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>Swing Velocity</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h2>' . $playerFullName . ' / ' . $row_event['Name'] . '</h2>
                                                                <form id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'FastPitchSwingVelocityForm" class="player_accordion">
                                                                    <div class="player_table">
                                                                        <div class="table">
                                                                            <div class="thead">
                                                                                <div class="tr">
                                                                                    <div class="th"></div>
                                                                                    <div class="th">Item</div>
                                                                                    <div class="th">MPH</div>
                                                                                    <div class="th"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tbody">
                                                                                ' . $stat_FastPitch_SwingVelocity . '
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="playerId" value="' . $playerId . '">
                                                                    <input type="hidden" name="sport" value="FastPitch">
                                                                    <input type="hidden" name="activity" value="SwingVelocity">
                                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                                    <input type="hidden" name="token" value="' . $row_player['Token'] . '">
                                                                    <div class="editPlayerStatsForEventErrorBox"></div>
                                                                    <input type="text" name="statsEntryDate" class="input_box" placeholder="Date" value="' . date('m/d/Y') . '">
                                                                    <div class="all_btn">
                                                                        <div class="blue_btn">
                                                                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'FastPitch"><i class="fa fa-chevron-left"></i> Back</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-savepause="true" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait.."><i class="fa fa-pause"></i> Save/Pause</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Edit Player Stats For Event Fast Pitch Swing Velocity -->';
                                            $playerStatsForEvent .= '<!-- Edit Player Stats For Event Fast Pitch 60 Yard Dash -->
                                            <div id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'FastPitch60YardDash" class="modal forget_section player_p_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>60 Yard Dash</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h2>' . $playerFullName . ' / ' . $row_event['Name'] . '</h2>
                                                                <form id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'FastPitch60YardDashForm" class="player_accordion">
                                                                    <div class="player_table">
                                                                        <div class="table">
                                                                            <div class="thead">
                                                                                <div class="tr">
                                                                                    <div class="th"></div>
                                                                                    <div class="th">Item</div>
                                                                                    <div class="th">Time</div>
                                                                                    <div class="th"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tbody">
                                                                                ' . $stat_FastPitch_60YardDash . '
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="playerId" value="' . $playerId . '">
                                                                    <input type="hidden" name="sport" value="FastPitch">
                                                                    <input type="hidden" name="activity" value="60YardDash">
                                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                                    <input type="hidden" name="token" value="' . $row_player['Token'] . '">
                                                                    <div class="editPlayerStatsForEventErrorBox"></div>
                                                                    <input type="text" name="statsEntryDate" class="input_box" placeholder="Date" value="' . date('m/d/Y') . '">
                                                                    <div class="all_btn">
                                                                        <div class="blue_btn">
                                                                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'FastPitch"><i class="fa fa-chevron-left"></i> Back</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-savepause="true" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait.."><i class="fa fa-pause"></i> Save/Pause</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Edit Player Stats For Event Fast Pitch 60 Yard Dash -->';
                                            $playerStatsForEvent .= '<!-- Edit Player Stats For Event Fast Pitch Catcher Pop -->
                                            <div id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'FastPitchCatcherPop" class="modal forget_section player_p_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>Catcher Pop</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h2>' . $playerFullName . ' / ' . $row_event['Name'] . '</h2>
                                                                <form id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'FastPitchCatcherPopForm" class="player_accordion">
                                                                    <div class="player_table">
                                                                        <div class="table">
                                                                            <div class="thead">
                                                                                <div class="tr">
                                                                                    <div class="th"></div>
                                                                                    <div class="th">Item</div>
                                                                                    <div class="th">Time</div>
                                                                                    <div class="th"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tbody">
                                                                                ' . $stat_FastPitch_CatcherPop . '
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="playerId" value="' . $playerId . '">
                                                                    <input type="hidden" name="sport" value="FastPitch">
                                                                    <input type="hidden" name="activity" value="CatcherPop">
                                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                                    <input type="hidden" name="token" value="' . $row_player['Token'] . '">
                                                                    <div class="editPlayerStatsForEventErrorBox"></div>
                                                                    <input type="text" name="statsEntryDate" class="input_box" placeholder="Date" value="' . date('m/d/Y') . '">
                                                                    <div class="all_btn">
                                                                        <div class="blue_btn">
                                                                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'FastPitch"><i class="fa fa-chevron-left"></i> Back</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-savepause="true" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait.."><i class="fa fa-pause"></i> Save/Pause</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Edit Player Stats For Event Fast Pitch Catcher Pop -->';
                                            $playerStatsForEvent .= '<!-- Edit Player Stats For Event Fast Pitch Catcher Release -->
                                            <div id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'FastPitchCatcherRelease" class="modal forget_section player_p_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>Catcher Release</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h2>' . $playerFullName . ' / ' . $row_event['Name'] . '</h2>
                                                                <form id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'FastPitchCatcherReleaseForm" class="player_accordion">
                                                                    <div class="player_table">
                                                                        <div class="table">
                                                                            <div class="thead">
                                                                                <div class="tr">
                                                                                    <div class="th"></div>
                                                                                    <div class="th">Item</div>
                                                                                    <div class="th">Time</div>
                                                                                    <div class="th"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tbody">
                                                                                ' . $stat_FastPitch_CatcherRelease . '
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="playerId" value="' . $playerId . '">
                                                                    <input type="hidden" name="sport" value="FastPitch">
                                                                    <input type="hidden" name="activity" value="CatcherRelease">
                                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                                    <input type="hidden" name="token" value="' . $row_player['Token'] . '">
                                                                    <div class="editPlayerStatsForEventErrorBox"></div>
                                                                    <input type="text" name="statsEntryDate" class="input_box" placeholder="Date" value="' . date('m/d/Y') . '">
                                                                    <div class="all_btn">
                                                                        <div class="blue_btn">
                                                                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'FastPitch"><i class="fa fa-chevron-left"></i> Back</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-savepause="true" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait.."><i class="fa fa-pause"></i> Save/Pause</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Edit Player Stats For Event Fast Pitch Catcher Release -->';
                                            $playerStatsForEvent .= '<!-- Edit Player Stats For Event Fast Pitch Tee Velocity -->
                                            <div id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'FastPitchTeeVelocity" class="modal forget_section player_p_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>Tee Velocity</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h2>' . $playerFullName . ' / ' . $row_event['Name'] . '</h2>
                                                                <form id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'FastPitchTeeVelocityForm" class="player_accordion">
                                                                    <div class="player_table">
                                                                        <div class="table">
                                                                            <div class="thead">
                                                                                <div class="tr">
                                                                                    <div class="th"></div>
                                                                                    <div class="th">Item</div>
                                                                                    <div class="th">MPH</div>
                                                                                    <div class="th"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tbody">
                                                                                ' . $stat_FastPitch_TeeVelocity . '
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="playerId" value="' . $playerId . '">
                                                                    <input type="hidden" name="sport" value="FastPitch">
                                                                    <input type="hidden" name="activity" value="TeeVelocity">
                                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                                    <input type="hidden" name="token" value="' . $row_player['Token'] . '">
                                                                    <div class="editPlayerStatsForEventErrorBox"></div>
                                                                    <input type="text" name="statsEntryDate" class="input_box" placeholder="Date" value="' . date('m/d/Y') . '">
                                                                    <div class="all_btn">
                                                                        <div class="blue_btn">
                                                                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'FastPitch"><i class="fa fa-chevron-left"></i> Back</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-savepause="true" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait.."><i class="fa fa-pause"></i> Save/Pause</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Edit Player Stats For Event Fast Pitch Tee Velocity -->';
                                        } // end if

                                    } // end Fast Pitch

                                    // Lacrosse
                                    if ($sportNameLowercase === 'lacrosse') {
                                        $lacrosse_60YardDash = ''; // Time
                                        $lacrosse_5ConeFootwork = ''; // Time
                                        $lacrosse_ShuttleRun = ''; // Time
                                        $lacrosse_Rebounder10 = ''; // Count
                                        $lacrosse_GoalShot10 = ''; // Count
                                        $lacrosse_Accuracy50 = ''; // Count
                                        $lacrosse_VelocityThrow = ''; // MPH

                                        try {
                                            $sql = "
                                            SELECT
                                              `GameLacrosse`.*
                                            FROM 
                                              `GameLacrosse`
                                            WHERE
                                              `GameLacrosse`.`PlayerId` = :PlayerId
                                            AND 
                                              `GameLacrosse`.`EventId` = :EventId
                                            AND 
                                              `GameLacrosse`.`Round` = :Round";

                                            $stmt = $PDO->prepare($sql);
                                            $stmt->bindParam('PlayerId', $row_player['PlayerId'], PDO::PARAM_INT);
                                            $stmt->bindParam('EventId', $row_event['EventId'], PDO::PARAM_INT);
                                            $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                                            $stmt->execute();

                                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                $lacrosse_60YardDash = ($row['60YardDash'] > 0) ? secondsToHMSU($row['60YardDash']) : ''; // Time
                                                $lacrosse_5ConeFootwork = ($row['5ConeFootwork'] > 0) ? secondsToHMSU($row['5ConeFootwork']) : ''; // Time
                                                $lacrosse_ShuttleRun = ($row['ShuttleRun'] > 0) ? secondsToHMSU($row['ShuttleRun']) : ''; // Time
                                                $lacrosse_Rebounder10 = ($row['Rebounder10'] > 0) ? $row['Rebounder10'] : ''; // Count
                                                $lacrosse_GoalShot10 = ($row['GoalShot10'] > 0) ? $row['GoalShot10'] : ''; // Count
                                                $lacrosse_Accuracy50 = ($row['Accuracy50'] > 0) ? $row['Accuracy50'] : ''; // Count
                                                $lacrosse_VelocityThrow = ($row['VelocityThrow'] > 0) ? $row['VelocityThrow'] : ''; // MPH
                                            }

                                        } catch (PDOException $e) {
                                            trigger_error($e->getMessage(), E_USER_ERROR);
                                        }//end try

                                        $stat_Lacrosse_60YardDash .= '
                                        <div class="tr">
                                            <div class="td"></div>
                                            <div class="td">Round ' . $i . '</div>
                                            <div class="td">
                                                <input name="lacrosse_60YardDash[]" type="text" placeholder="M:S.U" class="input_box format_time" value="' . $lacrosse_60YardDash . '"/>
                                            </div>
                                            <div class="td"></div>
                                        </div>';
                                        $stat_Lacrosse_5ConeFootwork .= '
                                        <div class="tr">
                                            <div class="td"></div>
                                            <div class="td">Round ' . $i . '</div>
                                            <div class="td">
                                                <input name="lacrosse_5ConeFootwork[]" type="text" placeholder="M:S.U" class="input_box format_time" value="' . $lacrosse_5ConeFootwork . '"/>
                                            </div>
                                            <div class="td"></div>
                                        </div>';
                                        $stat_Lacrosse_ShuttleRun .= '
                                        <div class="tr">
                                            <div class="td"></div>
                                            <div class="td">Round ' . $i . '</div>
                                            <div class="td">
                                                <input name="lacrosse_ShuttleRun[]" type="text" placeholder="M:S.U" class="input_box format_time" value="' . $lacrosse_ShuttleRun . '"/>
                                            </div>
                                            <div class="td"></div>
                                        </div>';
                                        $stat_Lacrosse_Rebounder10 .= '
                                        <div class="tr">
                                            <div class="td"></div>
                                            <div class="td">Round ' . $i . '</div>
                                            <div class="td">
                                                <input name="lacrosse_Rebounder10[]" type="tel" placeholder="0" class="input_box format_count" value="' . $lacrosse_Rebounder10 . '"/>
                                            </div>
                                            <div class="td"></div>
                                        </div>';
                                        $stat_Lacrosse_GoalShot10 .= '
                                        <div class="tr">
                                            <div class="td"></div>
                                            <div class="td">Round ' . $i . '</div>
                                            <div class="td">
                                                <input name="lacrosse_GoalShot10[]" type="tel" placeholder="0" class="input_box format_count" value="' . $lacrosse_GoalShot10 . '"/>
                                            </div>
                                            <div class="td"></div>
                                        </div>';
                                        $stat_Lacrosse_Accuracy50 .= '
                                        <div class="tr">
                                            <div class="td"></div>
                                            <div class="td">Round ' . $i . '</div>
                                            <div class="td">
                                                <input name="lacrosse_Accuracy50[]" type="tel" placeholder="0" class="input_box format_count" value="' . $lacrosse_Accuracy50 . '"/>
                                            </div>
                                            <div class="td"></div>
                                        </div>';
                                        $stat_Lacrosse_VelocityThrow .= '
                                        <div class="tr">
                                            <div class="td"></div>
                                            <div class="td">Round ' . $i . '</div>
                                            <div class="td">
                                                <input name="lacrosse_VelocityThrow[]" type="tel" placeholder="0" class="input_box format_mph" value="' . $lacrosse_VelocityThrow . '"/>
                                            </div>
                                            <div class="td"></div>
                                        </div>';

                                        if ($i == 1) {
                                            $playerChartRows .= '
                                            <tr data-id="' . $playerId . '" data-event="' . $row_event['EventId'] . '">
                                                <td><button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'Lacrosse">Go</button></td>
                                                <td>
                                                    <div class="score_table_img text-center">
                                                        <a href="index.php?page=player&token=' . $playerToken . '" target="_blank"><img src="' . $playerPicture . '" alt="' . $playerFullName . '" class="img-responsive img-circle"></a>
                                                        ' . $row_player['GradYear'] . '
                                                    </div>
                                                </td>
                                                <td>' . $row_player['TeamId'] . '</td>
                                                <td><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#editPlayer' . $playerId . 'ForEvent' . $row_event['EventId'] . '">' . $playerFullNameReverse . '</td>
                                                <td><img src="images/rating.png" alt="Rating" class="img-responsive"></td>
                                            </tr>';
                                            $editPlayerForEvent .= '
                                            <!-- Edit Event Step 1 -->
                                            <div id="editPlayer' . $playerId . 'ForEvent' . $row_event['EventId'] . '" data-id="' . $row_event['EventId'] . '" class="modal forget_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>Edit Player</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form id="editPlayer' . $playerId . 'ForEvent' . $row_event['EventId'] . 'Form" class="form-horizontal media_popup add_new_events">
                                                                    <div class="form-group">
                                                                        <label class="control-label col-sm-4" for="playerTeamId' . $playerId . '">Team Id</label>
                                                                        <div class="col-sm-8">
                                                                            <input type="text" id="playerTeamId' . $playerId . '" name="playerTeamId" class="form-control input_box" value="' . $row_player['TeamId'] . '">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="control-label col-sm-4" for="playerGender' . $playerId . '">Gender</label>
                                                                        <div class="col-sm-8">
                                                                            <select id="playerGender' . $playerId . '" name="playerGender" class="form-control input_box">
                                                                                ' . $playerGenderOptions . '
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="control-label col-sm-4" for="playerFirstName' . $playerId . '">First Name</label>
                                                                        <div class="col-sm-8">
                                                                            <input type="text" id="playerFirstName' . $playerId . '" name="playerFirstName" class="form-control input_box" value="' . $row_player['FirstName'] . '">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="control-label col-sm-4" for="playerMiddleName' . $playerId . '">Initial</label>
                                                                        <div class="col-sm-8">
                                                                            <input type="text" id="playerMiddleName' . $playerId . '" name="playerMiddleName" class="form-control input_box" value="' . $row_player['MiddleName'] . '">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="control-label col-sm-4" for="playerLastName' . $playerId . '">Last Name</label>
                                                                        <div class="col-sm-8">
                                                                            <input type="text" id="playerLastName' . $playerId . '" name="playerLastName" class="form-control input_box" value="' . $row_player['LastName'] . '">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="control-label col-sm-4" for="playerGradYear' . $playerId . '">Grad Year</label>
                                                                        <div class="col-sm-8">
                                                                            <input type="tel" id="playerGradYear' . $playerId . '" name="playerGradYear" class="form-control input_box" value="' . $row_player['GradYear'] . '">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="control-label col-sm-4" for="playerDOB' . $playerId . '">DOB</label>
                                                                        <div class="col-sm-8">
                                                                            <input type="text" id="playerDOB' . $playerId . '" name="playerDOB" class="form-control input_box" placeholder="MM/DD/YYYY" value="' . $row_player['DOB'] . '">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="control-label col-sm-4" for="playerCity' . $playerId . '">City</label>
                                                                        <div class="col-sm-8">
                                                                            <input type="text" id="playerCity' . $playerId . '" name="playerCity" class="form-control input_box" value="' . $row_player['City'] . '">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="control-label col-sm-4" for="playerStateShort' . $playerId . '">State</label>
                                                                        <div class="col-sm-8">
                                                                            <select id="playerStateShort' . $playerId . '" name="playerStateShort" class="form-control input_box">
                                                                                <option value="">Select State</option>
                                                                                ' . $playerStateOptions . '
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="control-label col-sm-4" for="playerEmail' . $playerId . '">Email</label>
                                                                        <div class="col-sm-8">
                                                                            <input type="text" id="playerEmail' . $playerId . '" name="playerEmail" class="form-control input_box" value="' . $row_player['Email'] . '">
                                                                        </div>
                                                                    </div>
                                                                    <div class="editPlayerForEventErrorBox"></div>
                                                                    <div class="all_btn">
                                                                        <div class="blue_btn">
                                                                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#editEvent' . $row_event['EventId'] . 'Results"><i class="fa fa-chevron-left"></i> Back</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerForEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                                   <input type="hidden" name="token" value="' . $row_player['Token'] . '">
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /editPlayerForEvent Lacrosse -->';

                                            $playerStatItemsForEvent .= '<!-- Add Player Stats For Event Lacrosse -->
                                            <div id="player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'Lacrosse" class="modal forget_section player_p_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>Player Stats For ' . $row_event['Name'] . '</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h2>' . $playerFullName . '</h2>
                                                                <div class="player_table">
                                                                    <div class="table">
                                                                        <div class="tbody">
                                                                            <div class="tr">
                                                                                <div class="td text-center"><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Lacrosse60YardDash">60 Yard Dash</a></div>
                                                                            </div>
                                                                            <div class="tr">
                                                                                <div class="td text-center"><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Lacrosse5ConeFootwork">5 Cone Footwork</a></div>
                                                                            </div>
                                                                            <div class="tr">
                                                                                <div class="td text-center"><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'LacrosseShuttleRun">Shuttle Run</a></div>
                                                                            </div>
                                                                            <div class="tr">
                                                                                <div class="td text-center"><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'LacrosseRebounder10">Rebounder 10</a></div>
                                                                            </div>
                                                                            <div class="tr">
                                                                                <div class="td text-center"><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'LacrosseGoalShot10">Goal Shot 10</a></div>
                                                                            </div>
                                                                            <div class="tr">
                                                                                <div class="td text-center"><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'LacrosseAccuracy50">Accuracy 50</a></div>
                                                                            </div>
                                                                            <div class="tr">
                                                                                <div class="td text-center"><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'LacrosseVelocityThrow">Velocity Throw</a></div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="all_btn">
                                                                    <div class="blue_btn">
                                                                        <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#editEvent' . $row_event['EventId'] . 'Results"><i class="fa fa-chevron-left"></i> Back</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div><!-- /playerStatItemsForEvent Lacrosse -->';
                                        } // end if

                                        if ($i == $eventRounds) {
                                            $playerStatsForEvent .= '<!-- Edit Player Stats For Event Lacrosse 60 Yard Dash -->
                                            <div id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Lacrosse60YardDash" class="modal forget_section player_p_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>60 Yard Dash</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h2>' . $playerFullName . ' / ' . $row_event['Name'] . '</h2>
                                                                <form id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Lacrosse60YardDashForm" class="player_accordion">
                                                                    <div class="player_table">
                                                                        <div class="table">
                                                                            <div class="thead">
                                                                                <div class="tr">
                                                                                    <div class="th"></div>
                                                                                    <div class="th">Item</div>
                                                                                    <div class="th">Time</div>
                                                                                    <div class="th"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tbody">
                                                                                ' . $stat_Lacrosse_60YardDash . '
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="playerId" value="' . $playerId . '">
                                                                    <input type="hidden" name="sport" value="Lacrosse">
                                                                    <input type="hidden" name="activity" value="60YardDash">
                                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                                    <input type="hidden" name="token" value="' . $row_player['Token'] . '">
                                                                    <div class="editPlayerStatsForEventErrorBox"></div>
                                                                    <input type="text" name="statsEntryDate" class="input_box" placeholder="Date" value="' . date('m/d/Y') . '">
                                                                    <div class="all_btn">
                                                                        <div class="blue_btn">
                                                                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'Lacrosse"><i class="fa fa-chevron-left"></i> Back</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-savepause="true" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait.."><i class="fa fa-pause"></i> Save/Pause</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Edit Player Stats For Event Lacrosse 60 Yard Dash -->';
                                            $playerStatsForEvent .= '<!-- Edit Player Stats For Event Lacrosse 5 Cone Footwork -->
                                            <div id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Lacrosse5ConeFootwork" class="modal forget_section player_p_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>5 Cone Footwork</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h2>' . $playerFullName . ' / ' . $row_event['Name'] . '</h2>
                                                                <form id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Lacrosse5ConeFootworkForm" class="player_accordion">
                                                                    <div class="player_table">
                                                                        <div class="table">
                                                                            <div class="thead">
                                                                                <div class="tr">
                                                                                    <div class="th"></div>
                                                                                    <div class="th">Item</div>
                                                                                    <div class="th">Time</div>
                                                                                    <div class="th"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tbody">
                                                                                ' . $stat_Lacrosse_5ConeFootwork . '
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="playerId" value="' . $playerId . '">
                                                                    <input type="hidden" name="sport" value="Lacrosse">
                                                                    <input type="hidden" name="activity" value="5ConeFootwork">
                                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                                    <input type="hidden" name="token" value="' . $row_player['Token'] . '">
                                                                    <div class="editPlayerStatsForEventErrorBox"></div>
                                                                    <input type="text" name="statsEntryDate" class="input_box" placeholder="Date" value="' . date('m/d/Y') . '">
                                                                    <div class="all_btn">
                                                                        <div class="blue_btn">
                                                                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'Lacrosse"><i class="fa fa-chevron-left"></i> Back</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-savepause="true" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait.."><i class="fa fa-pause"></i> Save/Pause</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Edit Player Stats For Event Lacrosse 5 Cone Footwork -->';
                                            $playerStatsForEvent .= '<!-- Edit Player Stats For Event Lacrosse Shuttle Run -->
                                            <div id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'LacrosseShuttleRun" class="modal forget_section player_p_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>Shuttle Run</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h2>' . $playerFullName . ' / ' . $row_event['Name'] . '</h2>
                                                                <form id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'LacrosseShuttleRunForm" class="player_accordion">
                                                                    <div class="player_table">
                                                                        <div class="table">
                                                                            <div class="thead">
                                                                                <div class="tr">
                                                                                    <div class="th"></div>
                                                                                    <div class="th">Item</div>
                                                                                    <div class="th">Time</div>
                                                                                    <div class="th"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tbody">
                                                                                ' . $stat_Lacrosse_ShuttleRun . '
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="playerId" value="' . $playerId . '">
                                                                    <input type="hidden" name="sport" value="Lacrosse">
                                                                    <input type="hidden" name="activity" value="ShuttleRun">
                                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                                    <input type="hidden" name="token" value="' . $row_player['Token'] . '">
                                                                    <div class="editPlayerStatsForEventErrorBox"></div>
                                                                    <input type="text" name="statsEntryDate" class="input_box" placeholder="Date" value="' . date('m/d/Y') . '">
                                                                    <div class="all_btn">
                                                                        <div class="blue_btn">
                                                                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'Lacrosse"><i class="fa fa-chevron-left"></i> Back</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-savepause="true" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait.."><i class="fa fa-pause"></i> Save/Pause</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Edit Player Stats For Event Lacrosse Shuttle Run -->';
                                            $playerStatsForEvent .= '<!-- Edit Player Stats For Event Lacrosse Rebounder 10 -->
                                            <div id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'LacrosseRebounder10" class="modal forget_section player_p_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>Rebounder 10</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h2>' . $playerFullName . ' / ' . $row_event['Name'] . '</h2>
                                                                <form id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'LacrosseRebounder10Form" class="player_accordion">
                                                                    <div class="player_table">
                                                                        <div class="table">
                                                                            <div class="thead">
                                                                                <div class="tr">
                                                                                    <div class="th"></div>
                                                                                    <div class="th">Item</div>
                                                                                    <div class="th">Count</div>
                                                                                    <div class="th"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tbody">
                                                                                ' . $stat_Lacrosse_Rebounder10 . '
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="playerId" value="' . $playerId . '">
                                                                    <input type="hidden" name="sport" value="Lacrosse">
                                                                    <input type="hidden" name="activity" value="Rebounder10">
                                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                                    <input type="hidden" name="token" value="' . $row_player['Token'] . '">
                                                                    <div class="editPlayerStatsForEventErrorBox"></div>
                                                                    <input type="text" name="statsEntryDate" class="input_box" placeholder="Date" value="' . date('m/d/Y') . '">
                                                                    <div class="all_btn">
                                                                        <div class="blue_btn">
                                                                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'Lacrosse"><i class="fa fa-chevron-left"></i> Back</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-savepause="true" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait.."><i class="fa fa-pause"></i> Save/Pause</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Edit Player Stats For Event Lacrosse Rebounder 10 -->';
                                            $playerStatsForEvent .= '<!-- Edit Player Stats For Event Lacrosse Goal Shot 10 -->
                                            <div id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'LacrosseGoalShot10" class="modal forget_section player_p_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>Goal Shot 10</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h2>' . $playerFullName . ' / ' . $row_event['Name'] . '</h2>
                                                                <form id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'LacrosseGoalShot10Form" class="player_accordion">
                                                                    <div class="player_table">
                                                                        <div class="table">
                                                                            <div class="thead">
                                                                                <div class="tr">
                                                                                    <div class="th"></div>
                                                                                    <div class="th">Item</div>
                                                                                    <div class="th">Count</div>
                                                                                    <div class="th"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tbody">
                                                                                ' . $stat_Lacrosse_GoalShot10 . '
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="playerId" value="' . $playerId . '">
                                                                    <input type="hidden" name="sport" value="Lacrosse">
                                                                    <input type="hidden" name="activity" value="GoalShot10">
                                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                                    <input type="hidden" name="token" value="' . $row_player['Token'] . '">
                                                                    <div class="editPlayerStatsForEventErrorBox"></div>
                                                                    <input type="text" name="statsEntryDate" class="input_box" placeholder="Date" value="' . date('m/d/Y') . '">
                                                                    <div class="all_btn">
                                                                        <div class="blue_btn">
                                                                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'Lacrosse"><i class="fa fa-chevron-left"></i> Back</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-savepause="true" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait.."><i class="fa fa-pause"></i> Save/Pause</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Edit Player Stats For Event Lacrosse Goal Shot 10 -->';
                                            $playerStatsForEvent .= '<!-- Edit Player Stats For Event Lacrosse Accuracy 50 -->
                                            <div id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'LacrosseAccuracy50" class="modal forget_section player_p_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>Accuracy 50</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h2>' . $playerFullName . ' / ' . $row_event['Name'] . '</h2>
                                                                <form id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'LacrosseAccuracy50Form" class="player_accordion">
                                                                    <div class="player_table">
                                                                        <div class="table">
                                                                            <div class="thead">
                                                                                <div class="tr">
                                                                                    <div class="th"></div>
                                                                                    <div class="th">Item</div>
                                                                                    <div class="th">Count</div>
                                                                                    <div class="th"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tbody">
                                                                                ' . $stat_Lacrosse_Accuracy50 . '
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="playerId" value="' . $playerId . '">
                                                                    <input type="hidden" name="sport" value="Lacrosse">
                                                                    <input type="hidden" name="activity" value="Accuracy50">
                                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                                    <input type="hidden" name="token" value="' . $row_player['Token'] . '">
                                                                    <div class="editPlayerStatsForEventErrorBox"></div>
                                                                    <input type="text" name="statsEntryDate" class="input_box" placeholder="Date" value="' . date('m/d/Y') . '">
                                                                    <div class="all_btn">
                                                                        <div class="blue_btn">
                                                                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'Lacrosse"><i class="fa fa-chevron-left"></i> Back</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-savepause="true" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait.."><i class="fa fa-pause"></i> Save/Pause</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Edit Player Stats For Event Lacrosse Accuracy 50 -->';
                                            $playerStatsForEvent .= '<!-- Edit Player Stats For Event Lacrosse Velocity Throw -->
                                            <div id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'LacrosseVelocityThrow" class="modal forget_section player_p_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>Velocity Throw</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h2>' . $playerFullName . ' / ' . $row_event['Name'] . '</h2>
                                                                <form id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'LacrosseVelocityThrowForm" class="player_accordion">
                                                                    <div class="player_table">
                                                                        <div class="table">
                                                                            <div class="thead">
                                                                                <div class="tr">
                                                                                    <div class="th"></div>
                                                                                    <div class="th">Item</div>
                                                                                    <div class="th">MPH</div>
                                                                                    <div class="th"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tbody">
                                                                                ' . $stat_Lacrosse_VelocityThrow . '
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="playerId" value="' . $playerId . '">
                                                                    <input type="hidden" name="sport" value="Lacrosse">
                                                                    <input type="hidden" name="activity" value="VelocityThrow">
                                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                                    <input type="hidden" name="token" value="' . $row_player['Token'] . '">
                                                                    <div class="editPlayerStatsForEventErrorBox"></div>
                                                                    <input type="text" name="statsEntryDate" class="input_box" placeholder="Date" value="' . date('m/d/Y') . '">
                                                                    <div class="all_btn">
                                                                        <div class="blue_btn">
                                                                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'Lacrosse"><i class="fa fa-chevron-left"></i> Back</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-savepause="true" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait.."><i class="fa fa-pause"></i> Save/Pause</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Edit Player Stats For Event Lacrosse Velocity Throw -->';
                                        } // end if
                                    } // end Lacrosse

                                    // Swimming
                                    if ($sportNameLowercase === 'swimming') {
                                        $swimming_25MFreestyle = ''; // Time
                                        $swimming_25MBackStroke = ''; // Time
                                        $swimming_25MBreastStroke = ''; // Time
                                        $swimming_25MButterfly = ''; // Time
                                        $swimming_50MFreestyle = ''; // Time
                                        $swimming_50MBackStroke = ''; // Time
                                        $swimming_50MBreastStroke = ''; // Time
                                        $swimming_50MButterfly = ''; // Time
                                        $swimming_100MFreestyle = ''; // Time
                                        $swimming_100MBackStroke = ''; // Time
                                        $swimming_100MBreastStroke = ''; // Time
                                        $swimming_100MButterfly = ''; // Time
                                        $swimming_100MIndividualMedley = ''; // Time
                                        $swimming_200MIndividualMedley = ''; // Time

                                        try {
                                            $sql = "
                                            SELECT
                                              `GameSwimming`.*
                                            FROM 
                                              `GameSwimming`
                                            WHERE
                                              `GameSwimming`.`PlayerId` = :PlayerId
                                            AND 
                                              `GameSwimming`.`EventId` = :EventId
                                            AND 
                                              `GameSwimming`.`Round` = :Round";

                                            $stmt = $PDO->prepare($sql);
                                            $stmt->bindParam('PlayerId', $row_player['PlayerId'], PDO::PARAM_INT);
                                            $stmt->bindParam('EventId', $row_event['EventId'], PDO::PARAM_INT);
                                            $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                                            $stmt->execute();

                                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                $swimming_25MFreestyle = ($row['25MFreestyle'] > 0) ? secondsToHMSU($row['25MFreestyle']) : ''; // Time
                                                $swimming_25MBackStroke = ($row['25MBackStroke'] > 0) ? secondsToHMSU($row['25MBackStroke']) : ''; // Time
                                                $swimming_25MBreastStroke = ($row['25MBreastStroke'] > 0) ? secondsToHMSU($row['25MBreastStroke']) : ''; // Time
                                                $swimming_25MButterfly = ($row['25MButterfly'] > 0) ? secondsToHMSU($row['25MButterfly']) : ''; // Time
                                                $swimming_50MFreestyle = ($row['50MFreestyle'] > 0) ? secondsToHMSU($row['50MFreestyle']) : ''; // Time
                                                $swimming_50MBackStroke = ($row['50MBackStroke'] > 0) ? secondsToHMSU($row['50MBackStroke']) : ''; // Time
                                                $swimming_50MBreastStroke = ($row['50MBreastStroke'] > 0) ? secondsToHMSU($row['50MBreastStroke']) : ''; // Time
                                                $swimming_50MButterfly = ($row['50MButterfly'] > 0) ? secondsToHMSU($row['50MButterfly']) : ''; // Time
                                                $swimming_100MFreestyle = ($row['100MFreestyle'] > 0) ? secondsToHMSU($row['100MFreestyle']) : ''; // Time
                                                $swimming_100MBackStroke = ($row['100MBackStroke'] > 0) ? secondsToHMSU($row['100MBackStroke']) : ''; // Time
                                                $swimming_100MBreastStroke = ($row['100MBreastStroke'] > 0) ? secondsToHMSU($row['100MBreastStroke']) : ''; // Time
                                                $swimming_100MButterfly = ($row['100MButterfly'] > 0) ? secondsToHMSU($row['100MButterfly']) : ''; // Time
                                                $swimming_100MIndividualMedley = ($row['100MIndividualMedley'] > 0) ? secondsToHMSU($row['100MIndividualMedley']) : ''; // Time
                                                $swimming_200MIndividualMedley = ($row['200MIndividualMedley'] > 0) ? secondsToHMSU($row['200MIndividualMedley']) : ''; // Time
                                            }

                                        } catch (PDOException $e) {
                                            trigger_error($e->getMessage(), E_USER_ERROR);
                                        }//end try

                                        $stat_Swimming_25MFreestyle .= '
                                        <div class="tr">
                                            <div class="td"></div>
                                            <div class="td">Round ' . $i . '</div>
                                            <div class="td">
                                                <input name="swimming_25MFreestyle[]" type="text" placeholder="M:S.U" class="input_box format_time" value="' . $swimming_25MFreestyle . '"/>
                                            </div>
                                            <div class="td"></div>
                                        </div>';
                                        $stat_Swimming_25MBackStroke .= '
                                        <div class="tr">
                                            <div class="td"></div>
                                            <div class="td">Round ' . $i . '</div>
                                            <div class="td">
                                                <input name="swimming_25MBackStroke[]" type="text" placeholder="M:S.U" class="input_box format_time" value="' . $swimming_25MBackStroke . '"/>
                                            </div>
                                            <div class="td"></div>
                                        </div>';
                                        $stat_Swimming_25MBreastStroke .= '
                                        <div class="tr">
                                            <div class="td"></div>
                                            <div class="td">Round ' . $i . '</div>
                                            <div class="td">
                                                <input name="swimming_25MBreastStroke[]" type="text" placeholder="M:S.U" class="input_box format_time" value="' . $swimming_25MBreastStroke . '"/>
                                            </div>
                                            <div class="td"></div>
                                        </div>';
                                        $stat_Swimming_25MButterfly .= '
                                        <div class="tr">
                                            <div class="td"></div>
                                            <div class="td">Round ' . $i . '</div>
                                            <div class="td">
                                                <input name="swimming_25MButterfly[]" type="text" placeholder="M:S.U" class="input_box format_time" value="' . $swimming_25MButterfly . '"/>
                                            </div>
                                            <div class="td"></div>
                                        </div>';
                                        $stat_Swimming_50MBackStroke .= '
                                        <div class="tr">
                                            <div class="td"></div>
                                            <div class="td">Round ' . $i . '</div>
                                            <div class="td">
                                                <input name="swimming_50MBackStroke[]" type="text" placeholder="M:S.U" class="input_box format_time" value="' . $swimming_50MBackStroke . '"/>
                                            </div>
                                            <div class="td"></div>
                                        </div>';
                                        $stat_Swimming_50MBreastStroke .= '
                                        <div class="tr">
                                            <div class="td"></div>
                                            <div class="td">Round ' . $i . '</div>
                                            <div class="td">
                                                <input name="swimming_50MBreastStroke[]" type="text" placeholder="M:S.U" class="input_box format_time" value="' . $swimming_50MBreastStroke . '"/>
                                            </div>
                                            <div class="td"></div>
                                        </div>';
                                        $stat_Swimming_50MButterfly .= '
                                        <div class="tr">
                                            <div class="td"></div>
                                            <div class="td">Round ' . $i . '</div>
                                            <div class="td">
                                                <input name="swimming_50MButterfly[]" type="text" placeholder="M:S.U" class="input_box format_time" value="' . $swimming_50MButterfly . '"/>
                                            </div>
                                            <div class="td"></div>
                                        </div>';
                                        $stat_Swimming_100MBackStroke .= '
                                        <div class="tr">
                                            <div class="td"></div>
                                            <div class="td">Round ' . $i . '</div>
                                            <div class="td">
                                                <input name="swimming_100MBackStroke[]" type="text" placeholder="M:S.U" class="input_box format_time" value="' . $swimming_100MBackStroke . '"/>
                                            </div>
                                            <div class="td"></div>
                                        </div>';
                                        $stat_Swimming_100MBreastStroke .= '
                                        <div class="tr">
                                            <div class="td"></div>
                                            <div class="td">Round ' . $i . '</div>
                                            <div class="td">
                                                <input name="swimming_100MBreastStroke[]" type="text" placeholder="M:S.U" class="input_box format_time" value="' . $swimming_100MBreastStroke . '"/>
                                            </div>
                                            <div class="td"></div>
                                        </div>';
                                        $stat_Swimming_100MButterfly .= '
                                        <div class="tr">
                                            <div class="td"></div>
                                            <div class="td">Round ' . $i . '</div>
                                            <div class="td">
                                                <input name="swimming_100MButterfly[]" type="text" placeholder="M:S.U" class="input_box format_time" value="' . $swimming_100MButterfly . '"/>
                                            </div>
                                            <div class="td"></div>
                                        </div>';
                                        $stat_Swimming_100MIndividualMedley .= '
                                        <div class="tr">
                                            <div class="td"></div>
                                            <div class="td">Round ' . $i . '</div>
                                            <div class="td">
                                                <input name="swimming_100MIndividualMedley[]" type="text" placeholder="M:S.U" class="input_box format_time" value="' . $swimming_100MIndividualMedley . '"/>
                                            </div>
                                            <div class="td"></div>
                                        </div>';
                                        $stat_Swimming_200MIndividualMedley .= '
                                        <div class="tr">
                                            <div class="td"></div>
                                            <div class="td">Round ' . $i . '</div>
                                            <div class="td">
                                                <input name="swimming_200MIndividualMedley[]" type="text" placeholder="M:S.U" class="input_box format_time" value="' . $swimming_200MIndividualMedley . '"/>
                                            </div>
                                            <div class="td"></div>
                                        </div>';

                                        if ($i == 1) {
                                            $playerChartRows .= '
                                            <tr data-id="' . $playerId . '" data-event="' . $row_event['EventId'] . '">
                                                <td><button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'Swimming">Go</button></td>
                                                <td>
                                                    <div class="score_table_img text-center">
                                                        <a href="index.php?page=player&token=' . $playerToken . '" target="_blank"><img src="' . $playerPicture . '" alt="' . $playerFullName . '" class="img-responsive img-circle"></a>
                                                        ' . $row_player['GradYear'] . '
                                                    </div>
                                                </td>
                                                <td>' . $row_player['TeamId'] . '</td>
                                                <td><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#editPlayer' . $playerId . 'ForEvent' . $row_event['EventId'] . '">' . $playerFullNameReverse . '</td>
                                                <td><img src="images/rating.png" alt="Rating" class="img-responsive"></td>
                                            </tr>';
                                            $editPlayerForEvent .= '
                                            <!-- Edit Event Step 1 -->
                                            <div id="editPlayer' . $playerId . 'ForEvent' . $row_event['EventId'] . '" data-id="' . $row_event['EventId'] . '" class="modal forget_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>Edit Player</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form id="editPlayer' . $playerId . 'ForEvent' . $row_event['EventId'] . 'Form" class="form-horizontal media_popup add_new_events">
                                                                    <div class="form-group">
                                                                        <label class="control-label col-sm-4" for="playerTeamId' . $playerId . '">Team Id</label>
                                                                        <div class="col-sm-8">
                                                                            <input type="text" id="playerTeamId' . $playerId . '" name="playerTeamId" class="form-control input_box" value="' . $row_player['TeamId'] . '">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="control-label col-sm-4" for="playerGender' . $playerId . '">Gender</label>
                                                                        <div class="col-sm-8">
                                                                            <select id="playerGender' . $playerId . '" name="playerGender" class="form-control input_box">
                                                                                ' . $playerGenderOptions . '
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="control-label col-sm-4" for="playerFirstName' . $playerId . '">First Name</label>
                                                                        <div class="col-sm-8">
                                                                            <input type="text" id="playerFirstName' . $playerId . '" name="playerFirstName" class="form-control input_box" value="' . $row_player['FirstName'] . '">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="control-label col-sm-4" for="playerMiddleName' . $playerId . '">Initial</label>
                                                                        <div class="col-sm-8">
                                                                            <input type="text" id="playerMiddleName' . $playerId . '" name="playerMiddleName" class="form-control input_box" value="' . $row_player['MiddleName'] . '">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="control-label col-sm-4" for="playerLastName' . $playerId . '">Last Name</label>
                                                                        <div class="col-sm-8">
                                                                            <input type="text" id="playerLastName' . $playerId . '" name="playerLastName" class="form-control input_box" value="' . $row_player['LastName'] . '">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="control-label col-sm-4" for="playerGradYear' . $playerId . '">Grad Year</label>
                                                                        <div class="col-sm-8">
                                                                            <input type="tel" id="playerGradYear' . $playerId . '" name="playerGradYear" class="form-control input_box" value="' . $row_player['GradYear'] . '">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="control-label col-sm-4" for="playerDOB' . $playerId . '">DOB</label>
                                                                        <div class="col-sm-8">
                                                                            <input type="text" id="playerDOB' . $playerId . '" name="playerDOB" class="form-control input_box" placeholder="MM/DD/YYYY" value="' . $row_player['DOB'] . '">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="control-label col-sm-4" for="playerCity' . $playerId . '">City</label>
                                                                        <div class="col-sm-8">
                                                                            <input type="text" id="playerCity' . $playerId . '" name="playerCity" class="form-control input_box">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="control-label col-sm-4" for="playerStateShort' . $playerId . '">State</label>
                                                                        <div class="col-sm-8">
                                                                            <select id="playerStateShort' . $playerId . '" name="playerStateShort" class="form-control input_box">
                                                                                <option value="">Select State</option>
                                                                                ' . $playerStateOptions . '
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="control-label col-sm-4" for="playerEmail' . $playerId . '">Email</label>
                                                                        <div class="col-sm-8">
                                                                            <input type="text" id="playerEmail' . $playerId . '" name="playerEmail" class="form-control input_box" value="' . $row_player['Email'] . '">
                                                                        </div>
                                                                    </div>
                                                                    <div class="editPlayerForEventErrorBox"></div>
                                                                    <div class="all_btn">
                                                                        <div class="blue_btn">
                                                                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#editEvent' . $row_event['EventId'] . 'Results"><i class="fa fa-chevron-left"></i> Back</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerForEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                                    <input type="hidden" name="token" value="' . $row_player['Token'] . '">
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /editPlayerForEvent Swimming -->';

                                            $playerStatItemsForEvent .= '<!-- Add Player Stats For Event Swimming -->
                                            <div id="player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'Swimming" class="modal forget_section player_p_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>Player Stats For ' . $row_event['Name'] . '</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h2>' . $playerFullName . '</h2>
                                                                <div class="player_table">
                                                                    <div class="table">
                                                                        <div class="tbody">
                                                                            <div class="tr">
                                                                                <div class="td text-center"><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Swimming25MFreestyle">25M Freestyle</a></div>
                                                                            </div>
                                                                            <div class="tr">
                                                                                <div class="td text-center"><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Swimming25MBackStroke">25M BackStroke</a></div>
                                                                            </div>
                                                                            <div class="tr">
                                                                                <div class="td text-center"><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Swimming25MBreastStroke">25M BreastStroke</a></div>
                                                                            </div>
                                                                            <div class="tr">
                                                                                <div class="td text-center"><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Swimming25MButterfly">25M Butterfly</a></div>
                                                                            </div>
                                                                            <div class="tr">
                                                                                <div class="td text-center"><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Swimming50MFreestyle">50M Freestyle</a></div>
                                                                            </div>
                                                                            <div class="tr">
                                                                                <div class="td text-center"><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Swimming50MBackStroke">50M BackStroke</a></div>
                                                                            </div>
                                                                            <div class="tr">
                                                                                <div class="td text-center"><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Swimming50MBreastStroke">50M BreastStroke</a></div>
                                                                            </div>
                                                                            <div class="tr">
                                                                                <div class="td text-center"><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Swimming50MButterfly">50M Butterfly</a></div>
                                                                            </div>
                                                                            <div class="tr">
                                                                                <div class="td text-center"><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Swimming100MFreestyle">100M Freestyle</a></div>
                                                                            </div>
                                                                            <div class="tr">
                                                                                <div class="td text-center"><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Swimming100MBackStroke">100M BackStroke</a></div>
                                                                            </div>
                                                                            <div class="tr">
                                                                                <div class="td text-center"><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Swimming100MBreastStroke">100M BreastStroke</a></div>
                                                                            </div>
                                                                            <div class="tr">
                                                                                <div class="td text-center"><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Swimming100MButterfly">100M Butterfly</a></div>
                                                                            </div>
                                                                            <div class="tr">
                                                                                <div class="td text-center"><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Swimming100MIndividualMedley">100M Individual Medley</a></div>
                                                                            </div>
                                                                            <div class="tr">
                                                                                <div class="td text-center"><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Swimming200MIndividualMedley">200M Individual Medley</a></div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="all_btn">
                                                                    <div class="blue_btn">
                                                                        <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#editEvent' . $row_event['EventId'] . 'Results"><i class="fa fa-chevron-left"></i> Back</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div><!-- /playerStatItemsForEvent Swimming -->';
                                        } // end if

                                        if ($i == $eventRounds) {
                                            $playerStatsForEvent .= '<!-- Edit Player Stats For Event Swimming 25M Freestyle -->
                                            <div id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Swimming25MFreestyle" class="modal forget_section player_p_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>25M Freestyle</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h2>' . $playerFullName . ' / ' . $row_event['Name'] . '</h2>
                                                                <form id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Swimming25MFreestyleForm" class="player_accordion">
                                                                    <div class="player_table">
                                                                        <div class="table">
                                                                            <div class="thead">
                                                                                <div class="tr">
                                                                                    <div class="th"></div>
                                                                                    <div class="th">Item</div>
                                                                                    <div class="th">Time</div>
                                                                                    <div class="th"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tbody">
                                                                                ' . $stat_Swimming_25MFreestyle . '
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="playerId" value="' . $playerId . '">
                                                                    <input type="hidden" name="sport" value="Swimming">
                                                                    <input type="hidden" name="activity" value="25MFreestyle">
                                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                                    <input type="hidden" name="token" value="' . $row_player['Token'] . '">
                                                                    <div class="editPlayerStatsForEventErrorBox"></div>
                                                                    <input type="text" name="statsEntryDate" class="input_box" placeholder="Date" value="' . date('m/d/Y') . '">
                                                                    <div class="all_btn">
                                                                        <div class="blue_btn">
                                                                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'Swimming"><i class="fa fa-chevron-left"></i> Back</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-savepause="true" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait.."><i class="fa fa-pause"></i> Save/Pause</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Edit Player Stats For Event Swimming 25M BackStroke -->';
                                            $playerStatsForEvent .= '<!-- Edit Player Stats For Event Swimming 25M BackStroke -->
                                            <div id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Swimming25MBackStroke" class="modal forget_section player_p_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>25M BackStroke</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h2>' . $playerFullName . ' / ' . $row_event['Name'] . '</h2>
                                                                <form id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Swimming25MBackStrokeForm" class="player_accordion">
                                                                    <div class="player_table">
                                                                        <div class="table">
                                                                            <div class="thead">
                                                                                <div class="tr">
                                                                                    <div class="th"></div>
                                                                                    <div class="th">Item</div>
                                                                                    <div class="th">Time</div>
                                                                                    <div class="th"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tbody">
                                                                                ' . $stat_Swimming_25MBackStroke . '
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="playerId" value="' . $playerId . '">
                                                                    <input type="hidden" name="sport" value="Swimming">
                                                                    <input type="hidden" name="activity" value="25MBackStroke">
                                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                                    <input type="hidden" name="token" value="' . $row_player['Token'] . '">
                                                                    <div class="editPlayerStatsForEventErrorBox"></div>
                                                                    <input type="text" name="statsEntryDate" class="input_box" placeholder="Date" value="' . date('m/d/Y') . '">
                                                                    <div class="all_btn">
                                                                        <div class="blue_btn">
                                                                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'Swimming"><i class="fa fa-chevron-left"></i> Back</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-savepause="true" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait.."><i class="fa fa-pause"></i> Save/Pause</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Edit Player Stats For Event Swimming 25M BackStroke -->';
                                            $playerStatsForEvent .= '<!-- Edit Player Stats For Event Swimming 25M BreastStroke -->
                                            <div id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Swimming25MBreastStroke" class="modal forget_section player_p_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>25M BreastStroke</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h2>' . $playerFullName . ' / ' . $row_event['Name'] . '</h2>
                                                                <form id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Swimming25MBreastStrokeForm" class="player_accordion">
                                                                    <div class="player_table">
                                                                        <div class="table">
                                                                            <div class="thead">
                                                                                <div class="tr">
                                                                                    <div class="th"></div>
                                                                                    <div class="th">Item</div>
                                                                                    <div class="th">Time</div>
                                                                                    <div class="th"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tbody">
                                                                                ' . $stat_Swimming_25MBreastStroke . '
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="playerId" value="' . $playerId . '">
                                                                    <input type="hidden" name="sport" value="Swimming">
                                                                    <input type="hidden" name="activity" value="25MBreastStroke">
                                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                                    <input type="hidden" name="token" value="' . $row_player['Token'] . '">
                                                                    <div class="editPlayerStatsForEventErrorBox"></div>
                                                                    <input type="text" name="statsEntryDate" class="input_box" placeholder="Date" value="' . date('m/d/Y') . '">
                                                                    <div class="all_btn">
                                                                        <div class="blue_btn">
                                                                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'Swimming"><i class="fa fa-chevron-left"></i> Back</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-savepause="true" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait.."><i class="fa fa-pause"></i> Save/Pause</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Edit Player Stats For Event Swimming 25M BreastStroke -->';
                                            $playerStatsForEvent .= '<!-- Edit Player Stats For Event Swimming 25M Butterfly -->
                                            <div id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Swimming25MButterfly" class="modal forget_section player_p_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>25M Butterfly</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h2>' . $playerFullName . ' / ' . $row_event['Name'] . '</h2>
                                                                <form id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Swimming25MButterflyForm" class="player_accordion">
                                                                    <div class="player_table">
                                                                        <div class="table">
                                                                            <div class="thead">
                                                                                <div class="tr">
                                                                                    <div class="th"></div>
                                                                                    <div class="th">Item</div>
                                                                                    <div class="th">Time</div>
                                                                                    <div class="th"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tbody">
                                                                                ' . $stat_Swimming_25MButterfly . '
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="playerId" value="' . $playerId . '">
                                                                    <input type="hidden" name="sport" value="Swimming">
                                                                    <input type="hidden" name="activity" value="25MButterfly">
                                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                                    <input type="hidden" name="token" value="' . $row_player['Token'] . '">
                                                                    <div class="editPlayerStatsForEventErrorBox"></div>
                                                                    <input type="text" name="statsEntryDate" class="input_box" placeholder="Date" value="' . date('m/d/Y') . '">
                                                                    <div class="all_btn">
                                                                        <div class="blue_btn">
                                                                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'Swimming"><i class="fa fa-chevron-left"></i> Back</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-savepause="true" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait.."><i class="fa fa-pause"></i> Save/Pause</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Edit Player Stats For Event Swimming 25M Butterfly -->';
                                            $playerStatsForEvent .= '<!-- Edit Player Stats For Event Swimming 50M Freestyle -->
                                            <div id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Swimming50MFreestyle" class="modal forget_section player_p_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>50M Freestyle</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h2>' . $playerFullName . ' / ' . $row_event['Name'] . '</h2>
                                                                <form id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Swimming50MFreestyleForm" class="player_accordion">
                                                                    <div class="player_table">
                                                                        <div class="table">
                                                                            <div class="thead">
                                                                                <div class="tr">
                                                                                    <div class="th"></div>
                                                                                    <div class="th">Item</div>
                                                                                    <div class="th">Time</div>
                                                                                    <div class="th"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tbody">
                                                                                ' . $stat_Swimming_50MFreestyle . '
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="playerId" value="' . $playerId . '">
                                                                    <input type="hidden" name="sport" value="Swimming">
                                                                    <input type="hidden" name="activity" value="50MFreestyle">
                                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                                    <input type="hidden" name="token" value="' . $row_player['Token'] . '">
                                                                    <div class="editPlayerStatsForEventErrorBox"></div>
                                                                    <input type="text" name="statsEntryDate" class="input_box" placeholder="Date" value="' . date('m/d/Y') . '">
                                                                    <div class="all_btn">
                                                                        <div class="blue_btn">
                                                                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'Swimming"><i class="fa fa-chevron-left"></i> Back</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-savepause="true" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait.."><i class="fa fa-pause"></i> Save/Pause</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Edit Player Stats For Event Swimming 50M Freestyle -->';
                                            $playerStatsForEvent .= '<!-- Edit Player Stats For Event Swimming 50M BackStroke -->
                                            <div id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Swimming50MBackStroke" class="modal forget_section player_p_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>50M BackStroke</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h2>' . $playerFullName . ' / ' . $row_event['Name'] . '</h2>
                                                                <form id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Swimming50MBackStrokeForm" class="player_accordion">
                                                                    <div class="player_table">
                                                                        <div class="table">
                                                                            <div class="thead">
                                                                                <div class="tr">
                                                                                    <div class="th"></div>
                                                                                    <div class="th">Item</div>
                                                                                    <div class="th">Time</div>
                                                                                    <div class="th"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tbody">
                                                                                ' . $stat_Swimming_50MBackStroke . '
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="playerId" value="' . $playerId . '">
                                                                    <input type="hidden" name="sport" value="Swimming">
                                                                    <input type="hidden" name="activity" value="50MBackStroke">
                                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                                    <input type="hidden" name="token" value="' . $row_player['Token'] . '">
                                                                    <div class="editPlayerStatsForEventErrorBox"></div>
                                                                    <input type="text" name="statsEntryDate" class="input_box" placeholder="Date" value="' . date('m/d/Y') . '">
                                                                    <div class="all_btn">
                                                                        <div class="blue_btn">
                                                                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'Swimming"><i class="fa fa-chevron-left"></i> Back</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-savepause="true" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait.."><i class="fa fa-pause"></i> Save/Pause</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Edit Player Stats For Event Swimming 50M BackStroke -->';
                                            $playerStatsForEvent .= '<!-- Edit Player Stats For Event Swimming 50M BreastStroke -->
                                            <div id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Swimming50MBreastStroke" class="modal forget_section player_p_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>50M BreastStroke</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h2>' . $playerFullName . ' / ' . $row_event['Name'] . '</h2>
                                                                <form id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Swimming50MBreastStrokeForm" class="player_accordion">
                                                                    <div class="player_table">
                                                                        <div class="table">
                                                                            <div class="thead">
                                                                                <div class="tr">
                                                                                    <div class="th"></div>
                                                                                    <div class="th">Item</div>
                                                                                    <div class="th">Time</div>
                                                                                    <div class="th"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tbody">
                                                                                ' . $stat_Swimming_50MBreastStroke . '
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="playerId" value="' . $playerId . '">
                                                                    <input type="hidden" name="sport" value="Swimming">
                                                                    <input type="hidden" name="activity" value="50MBreastStroke">
                                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                                    <input type="hidden" name="token" value="' . $row_player['Token'] . '">
                                                                    <div class="editPlayerStatsForEventErrorBox"></div>
                                                                    <input type="text" name="statsEntryDate" class="input_box" placeholder="Date" value="' . date('m/d/Y') . '">
                                                                    <div class="all_btn">
                                                                        <div class="blue_btn">
                                                                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'Swimming"><i class="fa fa-chevron-left"></i> Back</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-savepause="true" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait.."><i class="fa fa-pause"></i> Save/Pause</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Edit Player Stats For Event Swimming 50M BreastStroke -->';
                                            $playerStatsForEvent .= '<!-- Edit Player Stats For Event Swimming 50M Butterfly -->
                                            <div id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Swimming50MButterfly" class="modal forget_section player_p_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>50M Butterfly</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h2>' . $playerFullName . ' / ' . $row_event['Name'] . '</h2>
                                                                <form id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Swimming50MButterflyForm" class="player_accordion">
                                                                    <div class="player_table">
                                                                        <div class="table">
                                                                            <div class="thead">
                                                                                <div class="tr">
                                                                                    <div class="th"></div>
                                                                                    <div class="th">Item</div>
                                                                                    <div class="th">Time</div>
                                                                                    <div class="th"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tbody">
                                                                                ' . $stat_Swimming_50MButterfly . '
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="playerId" value="' . $playerId . '">
                                                                    <input type="hidden" name="sport" value="Swimming">
                                                                    <input type="hidden" name="activity" value="50MButterfly">
                                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                                    <input type="hidden" name="token" value="' . $row_player['Token'] . '">
                                                                    <div class="editPlayerStatsForEventErrorBox"></div>
                                                                    <input type="text" name="statsEntryDate" class="input_box" placeholder="Date" value="' . date('m/d/Y') . '">
                                                                    <div class="all_btn">
                                                                        <div class="blue_btn">
                                                                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'Swimming"><i class="fa fa-chevron-left"></i> Back</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-savepause="true" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait.."><i class="fa fa-pause"></i> Save/Pause</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Edit Player Stats For Event Swimming 50M Butterfly -->';
                                            $playerStatsForEvent .= '<!-- Edit Player Stats For Event Swimming 100M Freestyle -->
                                            <div id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Swimming100MFreestyle" class="modal forget_section player_p_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>100M Freestyle</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h2>' . $playerFullName . ' / ' . $row_event['Name'] . '</h2>
                                                                <form id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Swimming100MFreestyleForm" class="player_accordion">
                                                                    <div class="player_table">
                                                                        <div class="table">
                                                                            <div class="thead">
                                                                                <div class="tr">
                                                                                    <div class="th"></div>
                                                                                    <div class="th">Item</div>
                                                                                    <div class="th">Time</div>
                                                                                    <div class="th"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tbody">
                                                                                ' . $stat_Swimming_100MFreestyle . '
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="playerId" value="' . $playerId . '">
                                                                    <input type="hidden" name="sport" value="Swimming">
                                                                    <input type="hidden" name="activity" value="100MFreestyle">
                                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                                    <input type="hidden" name="token" value="' . $row_player['Token'] . '">
                                                                    <div class="editPlayerStatsForEventErrorBox"></div>
                                                                    <input type="text" name="statsEntryDate" class="input_box" placeholder="Date" value="' . date('m/d/Y') . '">
                                                                    <div class="all_btn">
                                                                        <div class="blue_btn">
                                                                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'Swimming"><i class="fa fa-chevron-left"></i> Back</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-savepause="true" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait.."><i class="fa fa-pause"></i> Save/Pause</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Edit Player Stats For Event Swimming 100M Freestyle -->';
                                            $playerStatsForEvent .= '<!-- Edit Player Stats For Event Swimming 100M BackStroke -->
                                            <div id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Swimming100MBackStroke" class="modal forget_section player_p_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>100M BackStroke</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h2>' . $playerFullName . ' / ' . $row_event['Name'] . '</h2>
                                                                <form id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Swimming100MBackStrokeForm" class="player_accordion">
                                                                    <div class="player_table">
                                                                        <div class="table">
                                                                            <div class="thead">
                                                                                <div class="tr">
                                                                                    <div class="th"></div>
                                                                                    <div class="th">Item</div>
                                                                                    <div class="th">Time</div>
                                                                                    <div class="th"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tbody">
                                                                                ' . $stat_Swimming_100MBackStroke . '
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="playerId" value="' . $playerId . '">
                                                                    <input type="hidden" name="sport" value="Swimming">
                                                                    <input type="hidden" name="activity" value="100MBackStroke">
                                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                                    <input type="hidden" name="token" value="' . $row_player['Token'] . '">
                                                                    <div class="editPlayerStatsForEventErrorBox"></div>
                                                                    <input type="text" name="statsEntryDate" class="input_box" placeholder="Date" value="' . date('m/d/Y') . '">
                                                                    <div class="all_btn">
                                                                        <div class="blue_btn">
                                                                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'Swimming"><i class="fa fa-chevron-left"></i> Back</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-savepause="true" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait.."><i class="fa fa-pause"></i> Save/Pause</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Edit Player Stats For Event Swimming 100M BackStroke -->';
                                            $playerStatsForEvent .= '<!-- Edit Player Stats For Event Swimming 100M BreastStroke -->
                                            <div id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Swimming100MBreastStroke" class="modal forget_section player_p_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>100M BreastStroke</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h2>' . $playerFullName . ' / ' . $row_event['Name'] . '</h2>
                                                                <form id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Swimming100MBreastStrokeForm" class="player_accordion">
                                                                    <div class="player_table">
                                                                        <div class="table">
                                                                            <div class="thead">
                                                                                <div class="tr">
                                                                                    <div class="th"></div>
                                                                                    <div class="th">Item</div>
                                                                                    <div class="th">Time</div>
                                                                                    <div class="th"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tbody">
                                                                                ' . $stat_Swimming_100MBreastStroke . '
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="playerId" value="' . $playerId . '">
                                                                    <input type="hidden" name="sport" value="Swimming">
                                                                    <input type="hidden" name="activity" value="100MBreastStroke">
                                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                                    <input type="hidden" name="token" value="' . $row_player['Token'] . '">
                                                                    <div class="editPlayerStatsForEventErrorBox"></div>
                                                                    <input type="text" name="statsEntryDate" class="input_box" placeholder="Date" value="' . date('m/d/Y') . '">
                                                                    <div class="all_btn">
                                                                        <div class="blue_btn">
                                                                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'Swimming"><i class="fa fa-chevron-left"></i> Back</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-savepause="true" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait.."><i class="fa fa-pause"></i> Save/Pause</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Edit Player Stats For Event Swimming 100M BreastStroke -->';
                                            $playerStatsForEvent .= '<!-- Edit Player Stats For Event Swimming 100M Butterfly -->
                                            <div id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Swimming100MButterfly" class="modal forget_section player_p_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>100M Butterfly</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h2>' . $playerFullName . ' / ' . $row_event['Name'] . '</h2>
                                                                <form id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Swimming100MButterflyForm" class="player_accordion">
                                                                    <div class="player_table">
                                                                        <div class="table">
                                                                            <div class="thead">
                                                                                <div class="tr">
                                                                                    <div class="th"></div>
                                                                                    <div class="th">Item</div>
                                                                                    <div class="th">Time</div>
                                                                                    <div class="th"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tbody">
                                                                                ' . $stat_Swimming_100MButterfly . '
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="playerId" value="' . $playerId . '">
                                                                    <input type="hidden" name="sport" value="Swimming">
                                                                    <input type="hidden" name="activity" value="100MButterfly">
                                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                                    <input type="hidden" name="token" value="' . $row_player['Token'] . '">
                                                                    <div class="editPlayerStatsForEventErrorBox"></div>
                                                                    <input type="text" name="statsEntryDate" class="input_box" placeholder="Date" value="' . date('m/d/Y') . '">
                                                                    <div class="all_btn">
                                                                        <div class="blue_btn">
                                                                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'Swimming"><i class="fa fa-chevron-left"></i> Back</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-savepause="true" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait.."><i class="fa fa-pause"></i> Save/Pause</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Edit Player Stats For Event Swimming 100M Butterfly -->';
                                            $playerStatsForEvent .= '<!-- Edit Player Stats For Event Swimming 100M Individual Medley -->
                                            <div id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Swimming100MIndividualMedley" class="modal forget_section player_p_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>100M Individual Medley</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h2>' . $playerFullName . ' / ' . $row_event['Name'] . '</h2>
                                                                <form id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Swimming100MIndividualMedleyForm" class="player_accordion">
                                                                    <div class="player_table">
                                                                        <div class="table">
                                                                            <div class="thead">
                                                                                <div class="tr">
                                                                                    <div class="th"></div>
                                                                                    <div class="th">Item</div>
                                                                                    <div class="th">Time</div>
                                                                                    <div class="th"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tbody">
                                                                                ' . $stat_Swimming_100MIndividualMedley . '
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="playerId" value="' . $playerId . '">
                                                                    <input type="hidden" name="sport" value="Swimming">
                                                                    <input type="hidden" name="activity" value="100MIndividualMedley">
                                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                                    <input type="hidden" name="token" value="' . $row_player['Token'] . '">
                                                                    <div class="editPlayerStatsForEventErrorBox"></div>
                                                                    <input type="text" name="statsEntryDate" class="input_box" placeholder="Date" value="' . date('m/d/Y') . '">
                                                                    <div class="all_btn">
                                                                        <div class="blue_btn">
                                                                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'Swimming"><i class="fa fa-chevron-left"></i> Back</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-savepause="true" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait.."><i class="fa fa-pause"></i> Save/Pause</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Edit Player Stats For Event Swimming 100M Individual Medley -->';
                                            $playerStatsForEvent .= '<!-- Edit Player Stats For Event Swimming 200M Individual Medley -->
                                            <div id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Swimming200MIndividualMedley" class="modal forget_section player_p_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>200M Individual Medley</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h2>' . $playerFullName . ' / ' . $row_event['Name'] . '</h2>
                                                                <form id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Swimming200MIndividualMedleyForm" class="player_accordion">
                                                                    <div class="player_table">
                                                                        <div class="table">
                                                                            <div class="thead">
                                                                                <div class="tr">
                                                                                    <div class="th"></div>
                                                                                    <div class="th">Item</div>
                                                                                    <div class="th">Time</div>
                                                                                    <div class="th"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tbody">
                                                                                ' . $stat_Swimming_200MIndividualMedley . '
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="playerId" value="' . $playerId . '">
                                                                    <input type="hidden" name="sport" value="Swimming">
                                                                    <input type="hidden" name="activity" value="200MIndividualMedley">
                                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                                    <input type="hidden" name="token" value="' . $row_player['Token'] . '">
                                                                    <div class="editPlayerStatsForEventErrorBox"></div>
                                                                    <input type="text" name="statsEntryDate" class="input_box" placeholder="Date" value="' . date('m/d/Y') . '">
                                                                    <div class="all_btn">
                                                                        <div class="blue_btn">
                                                                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatItemsForEvent' . $row_event['EventId'] . 'Swimming"><i class="fa fa-chevron-left"></i> Back</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                                        </div>
                                                                        <div class="blue_btn">
                                                                            <button type="submit" name="submit" value="submit" class="btn btn-success editPlayerStatsForEvent" data-savepause="true" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait.."><i class="fa fa-pause"></i> Save/Pause</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Edit Player Stats For Event Swimming 200M Individual Medley -->';
                                        } // end if

                                    } // end Swimming

                                } //end for $eventRounds

                            } // end while

                        } catch (PDOException $e) {
                            trigger_error($e->getMessage(), E_USER_ERROR);
                        }//end try

                        // Baseball
                        if ($sportNameLowercase === 'baseball') {
                            echo '
                            <!-- Event Results -->
                            <div class="title_h2" style="margin-top:10px;margin-bottom:10px;"><h2>Enter Results For ' . $row_event['Name'] . ' </h2></div>
                            <div class="row">
                                    <div id="editEvent' . $row_event['EventId'] . 'Toolbar" class="col-md-4 hidden-xs pull-left"></div>
                                    <div class="col-md-4 hidden-xs pull-right"><input id="editEvent' . $row_event['EventId'] . 'Search" class="form-control" placeholder="Search" aria-controls="editEvent' . $row_event['EventId'] . 'Table" type="search"></div>
                                </div> 
                            <div class="table-responsive">
                                <table id="editEvent' . $row_event['EventId'] . 'Table" data-event="' . $row_event['EventId'] . '" class="table table-striped display responsive no-wrap datatable">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Player</th>
                                            <th>Team ID</span></th>
                                            <th>Last, First</th>
                                            <th>Position</th>
                                            <th>Overall</th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                    ' . $playerChartRows . '
                                    </tbody>
                                </table>
                            </div>
                            <!-- Event Results -->';
                        }

                        // Fast Pitch
                        if ($sportNameLowercase === 'fastpitch') {
                            echo '
                            <!-- Event Results -->
                            <div class="title_h2" style="margin-top:10px;margin-bottom:10px;"><h2>Enter Results For ' . $row_event['Name'] . ' </h2></div>
                            <div class="row">
                                    <div id="editEvent' . $row_event['EventId'] . 'Toolbar" class="col-md-4 hidden-xs pull-left"></div>
                                    <div class="col-md-4 hidden-xs pull-right"><input id="editEvent' . $row_event['EventId'] . 'Search" class="form-control" placeholder="Search" aria-controls="editEvent' . $row_event['EventId'] . 'Table" type="search"></div>
                                </div> 
                            <div class="table-responsive">
                                <table id="editEvent' . $row_event['EventId'] . 'Table" data-event="' . $row_event['EventId'] . '" class="table table-striped display responsive no-wrap datatable">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Player</th>
                                            <th>Team ID</th>
                                            <th>Last, First</th>
                                            <th>Position</th>
                                            <th>Overall</th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                    ' . $playerChartRows . '
                                    </tbody>
                                </table>
                            </div>
                            <!-- Event Results -->';
                        }

                        // Lacrosse
                        if ($sportNameLowercase === 'lacrosse') {
                            echo '
                            <!-- Event Results -->
                            <div class="title_h2" style="margin-top:10px;margin-bottom:10px;"><h2>Enter Results For ' . $row_event['Name'] . ' </h2></div>
                            <div class="row">
                                    <div id="editEvent' . $row_event['EventId'] . 'Toolbar" class="col-md-4 hidden-xs pull-left"></div>
                                    <div class="col-md-4 hidden-xs pull-right"><input id="editEvent' . $row_event['EventId'] . 'Search" class="form-control" placeholder="Search" aria-controls="editEvent' . $row_event['EventId'] . 'Table" type="search"></div>
                                </div> 
                            <div class="table-responsive">
                                <table id="editEvent' . $row_event['EventId'] . 'Table" data-event="' . $row_event['EventId'] . '" class="table table-striped display responsive no-wrap datatable">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Player</th>
                                            <th>Team ID</th>
                                            <th>Last, First</th>
                                            <th>Overall</th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                    ' . $playerChartRows . '
                                    </tbody>
                                </table>
                            </div>       
                            <!-- Event Results -->';
                        }

                        // Cross Fit
                        if ($sportNameLowercase === 'crossfit') {
                            echo '
                            <!-- Event Results -->
                            <div class="title_h2" style="margin-top:10px;margin-bottom:10px;"><h2>Enter Results For ' . $row_event['Name'] . ' </h2></div>
                            <div class="row">
                                    <div id="editEvent' . $row_event['EventId'] . 'Toolbar" class="col-md-4 hidden-xs pull-left"></div>
                                    <div class="col-md-4 hidden-xs pull-right"><input id="editEvent' . $row_event['EventId'] . 'Search" class="form-control" placeholder="Search" aria-controls="editEvent' . $row_event['EventId'] . 'Table" type="search"></div>
                                </div> 
                            <div class="table-responsive">
                                <table id="editEvent' . $row_event['EventId'] . 'Table" data-event="' . $row_event['EventId'] . '" class="table table-striped display responsive no-wrap datatable">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Player</th>
                                            <th>Team ID</th>
                                            <th>Last, First</th>
                                            <th>Overall</th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                    ' . $playerChartRows . '
                                    </tbody>
                                </table>
                            </div>        
                            <!-- Event Results -->';
                        }

                        // Swimming
                        if ($sportNameLowercase === 'swimming') {
                            echo '
                            <!-- Event Results -->
                                <div class="title_h2" style="margin-top:10px;margin-bottom:10px;"><h2>Enter Results For ' . $row_event['Name'] . ' </h2></div>
                                <div class="row">
                                    <div id="editEvent' . $row_event['EventId'] . 'Toolbar" class="col-md-4 hidden-xs pull-left"></div>
                                    <div class="col-md-4 hidden-xs pull-right"><input id="editEvent' . $row_event['EventId'] . 'Search" class="form-control" placeholder="Search" aria-controls="editEvent' . $row_event['EventId'] . 'Table" type="search"></div>
                                </div> 
                                <div class="table-responsive">
                                <table id="editEvent' . $row_event['EventId'] . 'Table" data-event="' . $row_event['EventId'] . '" class="table table-striped display responsive no-wrap datatable">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Player</th>
                                            <th>Team ID</th>
                                            <th>Last, First</th>
                                            <th>Overall</th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                    ' . $playerChartRows . '
                                    </tbody>
                                </table>
                            </div>           
                            <!-- Event Results -->';
                        }

                    } // end fetch event

                } catch (PDOException $e) {
                    trigger_error($e->getMessage(), E_USER_ERROR);
                }//end try

                if ($count == 0) {
                    echo '<h3>You have no events</h3>';
                }

                ?>
                <!-- end col-sm-10 -->
            </div>
        </div>
    </div>
</section>

<?php require 'includes/footer.php'; ?>

<?php
// generated modal dialogs
echo $editPlayerForEvent;
echo $playerStatsForEvent;
echo $playerStatItemsForEvent;
?>

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
<script type="text/javascript" src="js/jquery.storage.js"></script>
<script type="text/javascript" src="js/cropper-min.js"></script>
<script type="text/javascript" src="js/custom.js"></script>


<script>

    var isComplete = false;
    var ls = $.localStorage();
    var fpsTargets = [];
    var myActivity = '';

    $(document).ready(function () {

        $('[data-toggle="tooltip"]').tooltip();

        $('input.format_mph').each(function() {
            var input = $(this);
            input.popover({
                title : 'MPH',
                content : 'Whole number (example: 150)',
                placement : 'left',
                trigger: 'focus'
            });
        });

        $('input.format_time').each(function() {
            var input = $(this);
            input.popover({
                title : 'Time',
                content : 'Minutes:Seconds.Microseconds',
                placement : 'left',
                trigger: 'focus'
            });
        });

        $('input.format_count').each(function() {
            var input = $(this);
            input.popover({
                title : 'Count',
                content : 'Whole number (example: 35)',
                placement : 'left',
                trigger: 'focus'
            });
        });

        $('input.format_inch').each(function() {
            var input = $(this);
            input.popover({
                title : 'Inches',
                content : 'Whole or Decimal (35 or 35.2)',
                placement : 'left',
                trigger: 'focus'
            });
        });

        var fps = $('#fps');
        var fps_icon = fps.find('i');

        // create local storage item on page load
        if (ls.getItem('fps') === null) {
            ls.setItem('fps', 'no');
            $('button[data-savepause]').hide();
        }

        // show/hide button on page load
        if (ls.getItem('fps') === 'yes') {
            fps_icon.removeClass('text-muted').addClass('text-success');
            fps_icon.removeClass('fa-square-o').addClass('fa-check-square');
            $('button[data-savepause]').show();
        } else {
            fps_icon.removeClass('text-success').addClass('text-muted');
            fps_icon.removeClass('fa-check-square').addClass('fa-square-o');
            ls.setItem('fps', 'no');
            $('button[data-savepause]').hide();
        }

        // show/hide button with click handler
        fps.on('click', function (e) {
            e.preventDefault();
            if (ls.getItem('fps') === 'no') {
                // Enable FPS
                fps_icon.removeClass('text-muted').addClass('text-success');
                fps_icon.removeClass('fa-square-o').addClass('fa-check-square');
                ls.setItem('fps', 'yes');
                $('button[data-savepause]').show();
            } else {
                // Disable FPS
                fps_icon.removeClass('text-success').addClass('text-muted');
                fps_icon.removeClass('fa-check-square').addClass('fa-square-o');
                ls.setItem('fps', 'no');
                $('button[data-savepause]').hide();
            }
        });

        var fas = $('.fas');
        fas.on('click', function (e) {
            e.preventDefault();
            var fps_icon = $(this).find('i');
            if (fps_icon.hasClass('fa-square-o')) {
                // Enable FAS
                var selectedActivity = $(this).data('activity');
                var all_icons = fas.find('i');
                all_icons.removeClass('text-success').addClass('text-muted');
                all_icons.removeClass('fa-check-square').addClass('fa-square-o');
                fps_icon.removeClass('text-muted').addClass('text-success');
                fps_icon.removeClass('fa-square-o').addClass('fa-check-square');
                $('table.datatable').each(function () {
                    var tableId = $(this).attr('id');
                    var table = $('#' + tableId).DataTable();
                    table.rows().iterator('row', function (context, index) {
                        var row = $(this.row(index).node());
                        var button = row.find('td:first button');
                        var target = button.attr('data-target');
                        target = target.replace(myActivity, "");
                        //console.log('Target: ' + target);
                        var new_target = target.replace("StatItemsForEvent", "StatsForEvent");
                        //console.log('New Target: ' + new_target + selectedActivity);
                        button.attr('data-target', new_target + selectedActivity);
                        this.row(index).invalidate().draw();
                    });
                });
                myActivity = selectedActivity;
            } else {
                // Disable FAS
                fps_icon.removeClass('text-success').addClass('text-muted');
                fps_icon.removeClass('fa-check-square').addClass('fa-square-o');
                $('table.datatable').each(function () {
                    var tableId = $(this).attr('id');
                    var table = $('#' + tableId).DataTable();
                    table.rows().iterator('row', function (context, index) {
                        var row = $(this.row(index).node());
                        var button = row.find('td:first button');
                        var target = button.attr('data-target');
                        //console.log('Target: ' + target);
                        var new_target = target.replace("StatsForEvent", "StatItemsForEvent");
                        new_target = new_target.replace(myActivity, "");
                        //console.log('New Target: ' + new_target);
                        button.attr('data-target', new_target);
                        this.row(index).invalidate().draw();
                    });
                });
                /*
                 // Non-Datatables version
                 var row = $('table.datatable tbody tr');
                 row.each(function () {
                 var a = $(this).find('td:last a');
                 var target = a.data('target');
                 console.log('Target: ' + target);
                 // head directly to next modal
                 var new_target = target.replace("StatsForEvent", "StatItemsForEvent");
                 console.log('New Target: ' + new_target + myActivity);
                 a.attr('data-target', new_target + myActivity);
                 });
                 */
                myActivity = '';
            }
        });


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

        $('table.datatable').each(function () {
            var tableId = $(this).attr('id');
            var eventId = $(this).data('event');
            var table = $('#' + tableId);

            var dataTable = table.DataTable({
                dom: 'Rfrtlip',
                paging: false,
                responsive: true,
                columnDefs: [{
                    targets: '_all',
                    render: function (data, type, full, meta) {
                        return type == 'filter' ? $(data).text() : data;
                    }
                }],
                order: [[1, 'asc']],
                drawCallback: function (settings) {
                    // clear and repopulate after sorting
                    fpsTargets = [];
                    var tr = table.find('tbody tr');
                    tr.each(function () {
                        var row = $(this);
                        var target = {};
                        target.eventId = row.data('event');
                        target.playerId = row.data('id');
                        fpsTargets.push(target);
                    });
                },
                initComplete: function (settings, json) {
                    $(this).DataTable().buttons().container().appendTo($('#editEvent' + eventId + 'Toolbar'));
                },
                buttons: {
                    name: 'commands',
                    buttons: [{
                        extend: 'print',
                        text: '<i class="fa fa-print"></i> Print',
                        exportOptions: {
                            columns: ':not(.no-print)'
                        },
                        footer: true,
                        autoPrint: true
                    }, {
                        extend: 'collection',
                        text: 'Export',
                        exportOptions: {
                            columns: ':not(.no-print)'
                        },
                        buttons: ['copy', 'csv'],
                        footer: true
                    }],
                    dom: {
                        container: {
                            className: 'dt-buttons'
                        },
                        button: {
                            className: 'btn btn-default'
                        }
                    }
                }
            });

            $('#editEvent' + eventId + 'Search').on("keyup search input paste cut", function () {
                var value = $(this).val();
                dataTable.search(value).draw();
            });

        });

        $('button.editPlayerStatsForEvent').each(function () {
            $(this).on('click', function (e) {
                var myButton = $(this);
                var savePause = (typeof myButton.data('savepause') !== 'undefined');
                var parent = myButton.closest('.modal');
                var myForm = parent.find('form');
                var modalId = parent.attr('id');
                var eventId = myForm.find('input[name="eventId"]').val();
                var playerId = myForm.find('input[name="playerId"]').val();
                var sport = myForm.find('input[name="sport"]').val();
                var activity = myForm.find('input[name="activity"]').val();
                var myErrorBox = parent.find('.editPlayerStatsForEventErrorBox');

                if (isComplete === false) {
                    myButton.button('loading');
                    isComplete = true;
                    $.ajax({
                        url: 'index.php?action=editPlayerStatsForEvent',
                        type: 'POST',
                        data: myForm.serializeArray(),
                        dataType: 'json',
                        success: function (json) {

                            myErrorBox.empty();

                            //data: return data from server
                            if (typeof json.success !== 'undefined') {
                                myErrorBox.html(json.success);
                                if (ls.getItem('fps') === 'yes') {
                                    for (var i = 0; i < fpsTargets.length; i++) {
                                        if (fpsTargets[i].playerId == playerId) {
                                            var x = i + 1;
                                            if (typeof fpsTargets[x] !== 'undefined') {
                                                var _modal = '#player' + fpsTargets[x].playerId + 'StatsForEvent' + fpsTargets[x].eventId + sport + activity;
                                                if (!savePause) {
                                                    $('#' + modalId).modal('hide');
                                                    $(_modal).modal('show');
                                                }
                                                break;
                                            } else {
                                                if (!savePause) {
                                                    $('#' + modalId).modal('hide');
                                                    $('#editEvent' + eventId + 'Results').modal('show');
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    $('#' + modalId).modal('hide');
                                    $('#editEvent' + eventId + 'Results').modal('show');
                                    //$('#player' + playerId + 'StatItemsForEvent' + eventId + sport).modal('show');
                                }
                            }

                            if (typeof json.error !== 'undefined') {
                                myErrorBox.html(json.error);
                            }
                            isComplete = false;
                            myButton.button('reset');
                        }
                    });
                }
                e.preventDefault(); //STOP default action
                //e.unbind();
            });
        });

        $('button.editPlayerForEvent').each(function () {
            var myButton = $(this);
            var parent = $(this).closest('.modal');
            var myForm = parent.find('form');
            var modalId = parent.attr('id');
            var eventId = myForm.find('input[name="eventId"]').val();
            var myErrorBox = parent.find('.editPlayerForEventErrorBox');

            myForm.on('submit', function (e) {
                if (isComplete === false) {
                    myButton.button('loading');
                    isComplete = true;
                    $.ajax({
                        url: 'index.php?action=editPlayerForEvent',
                        type: 'POST',
                        data: $(this).serializeArray(),
                        dataType: 'json',
                        success: function (json) {

                            myErrorBox.empty();

                            //data: return data from server
                            if (typeof json.success !== 'undefined') {
                                myErrorBox.html(json.success);
                            }

                            if (typeof json.error !== 'undefined') {
                                myErrorBox.html(json.error);
                            }
                            isComplete = false;
                            myButton.button('reset');
                        }
                    });
                }
                e.preventDefault(); //STOP default action
                //e.unbind();
            });
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

        $('.searchEventAdminQuery').each(function () {
            var opts = {
                theme: "bootstrap",
                placeholder: "John Doe jdoe@harvard.edu",
                allowClear: true,
                minimumInputLength: 3,
                closeOnSelect: false,
                ajax: {
                    url: "index.php?action=searchForEventAdmin",
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
                            results: data.results.users.map(function (user) {
                                return {
                                    id: user.Id,
                                    userGender: user.Gender,
                                    userName: user.Name,
                                    userPicture: user.Picture
                                };
                            }),
                            pagination: {
                                // If there are 10 matches, there's at least another page
                                more: data.results.users.length === 10
                                //more: (params.page * 10) < data.recordsTotal
                            }
                        };
                    },
                    cache: true
                },
                templateResult: formatEventAdmin,
                templateSelection: formatEventAdminSelection
            };
            var parent = $(this).closest('.modal');
            if (parent.length) {
                opts.dropdownParent = parent;
            }
            $(this).select2(opts);

            $(this).on('select2:select', function (e) {
                var selectedElement = $(e.currentTarget);
                var userId = selectedElement.val();
                var $img = parent.find('img[data-id="' + userId + '"]');
                var tag = '<div class="grey_tag_box"><div class="text_box_tag">' + $img.data('name') + '</div><input data-eventAdminId="' + $img.data('id') + '" type="hidden" name="eventAdminId[]" value="' + $img.data('id') + '"><img src="images/tag_close.png" class="tag_close"></div>';
                if (!parent.find('input[data-eventAdminId="' + userId + '"]').length) {
                    parent.find(".event-admin-tags").append(tag);
                    parent.find('.tag_close').click(function () {
                        $(this).parent().hide();
                    });
                }
            });
        });

    }); // end doc.ready

    $(window).load(function () {
        $('#pageLoadingSpinner').fadeOut("slow");
    }); // end window.load

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

    function formatEventAdmin(user) {
        if (!user.id) {
            return user.userName;
        }

        var suggest = '';
        suggest += '<div class="row">';
        suggest += '<div class="col-sm-3 col-md-3 col-lg-2">';
        suggest += '<img src="' + user.userPicture + '" alt="' + user.userName + '" data-id="' + user.id + '" data-name="' + user.userName + '" class="img-responsive" style="max-width:80px;">';
        suggest += '</div>';
        suggest += '<div class="col-sm-9 col-md-9 col-lg-10">';
        suggest += '<span>' + user.userName + '</span><br />';
        suggest += '</div></div>';
        suggest += '</div>';

        var $user = $(
            suggest
        );
        return $user;
    }

    function formatEventAdminSelection(user) {
        // adjust for custom placeholder values
        if (!user.id) {
            return 'John Doe jdoe@harvard.edu';
        }
        return user.userName;
    }

</script>
</body>
</html>