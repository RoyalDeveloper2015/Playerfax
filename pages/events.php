<?php

if (!isset($_SESSION['userId'])) {
    header('Location: index.php?page=login');
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
        #pageLoadingSpinner {
            font-size: 14px;
            color: #555555;
            background-color: #ffd700;
        }
    </style>
</head>
<body class="user_home" id="portrait">
<?php require 'includes/header.php'; ?>
<section class="user_profile_main">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">

                <button class="btn btn-success" data-toggle="modal" data-target="#addEventStep1">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                    Add event
                </button>

                <span id="pageLoadingSpinner" class="label">Page is loading...</span>

                <!-- col-sm-10 -->
                <?php
                $count = 0;
                $editEventStep1 = ''; // modal
                $editEventStep2 = ''; // modal
                $addPlayerToEvent = ''; // modal

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
                          `Events`.`StartDate`,
                          `Events`.`EndDate`
                        FROM 
                          `Events` 
                        LEFT JOIN `EventAdmins` ON
                        `Events`.`EventId` = `EventAdmins`.`EventId`
                        WHERE
                          `EventAdmins`.`UserId` = :UserId";

                    $stmt_event = $PDO->prepare($sql_event);
                    $stmt_event->bindParam('UserId', $userId, PDO::PARAM_INT);
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

                        $eventStateOptions = '';
                        foreach ($states as $stateKey => $stateValue) {
                            if ($stateKey == $row_event['StateShort']) {
                                $eventStateOptions .= '<option value="' . $stateKey . '" selected="selected">' . $stateValue . '</option>';
                            } else {
                                $eventStateOptions .= '<option value="' . $stateKey . '">' . $stateValue . '</option>';
                            }
                        }

                        if ($row_event['Calendar'] == '0') {
                            $eventsCalendar = '<label class="radio-inline"><input type="radio" name="eventCalendar" value="yes"> Yes</label>
                                               <label class="radio-inline"><input type="radio" name="eventCalendar" value="no" checked="checked"> No</label>';
                        } else {
                            $eventsCalendar = '<label class="radio-inline"><input type="radio" name="eventCalendar" value="yes" checked="checked"> Yes</label>
                                               <label class="radio-inline"><input type="radio" name="eventCalendar" value="no"> No</label>';
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
                        $_startDate = '';
                        $_startTime = '';
                        if (!empty($row_event['StartDate']) && $row_event['StartDate'] != '0000-00-00 00:00:00') {
                            $eventStartDate = date('m/d/Y g:i:s', strtotime($row_event['StartDate']));

                            $_startDate = date('F jS, Y', strtotime($eventStartDate));
                            $_startTime = date('g:ia', strtotime($eventStartDate));

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
                        $eventEndDate = '';
                        $_endDate = '';
                        $_endTime = '';
                        if (!empty($row_event['EndDate']) && $row_event['EndDate'] != '0000-00-00 00:00:00') {
                            $eventEndDate = date('m/d/Y g:i:s', strtotime($row_event['EndDate']));

                            $_endDate = date('F jS, Y', strtotime($eventEndDate));
                            $_endTime = date('g:ia', strtotime($eventEndDate));
                        }

                        $eventRounds = $row_event['Rounds'];
                        $playerChartRows = '';

                        $editEventStep1 .= '
                            <!-- Edit Event Step 1 -->
                            <div id="editEvent' . $row_event['EventId'] . 'Step1" data-id="' . $row_event['EventId'] . '" class="modal forget_section" role="dialog">
                                <div class="modal-dialog">
                                    <!-- Modal content-->
                                    <div class="container">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <div class="close_img">
                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                </div>
                                                <div class="title_h2">
                                                    <h2>Event Details</h2>
                                                </div>
                                            </div>
                                            <div class="modal-body">
                                                <form id="editEvent' . $row_event['EventId'] . 'Step1Form" class="form-horizontal media_popup add_new_events">
                                                    <input name="eventName" type="text" placeholder="Event Name" required class="form-control input_box" value="' . $row_event['Name'] . '"/>
                                                    <input name="eventSport" class="form-control input_box" disabled readonly value="' . $sportName . '">
                                                    <input name="eventStartDate" type="text" placeholder="Start Date &amp; Time" required class="form-control input_box datetime" value="' . $eventStartDate . '"/>
                                                    <input name="eventEndDate" type="text" placeholder="End Date &amp; Time" required class="form-control input_box datetime" value="' . $eventEndDate . '"/>
                                                    <input name="eventAddress" type="text" placeholder="Event Address" required class="form-control input_box" value="' . $row_event['Address'] . '"/>
                                                    <input name="eventCity" type="text" placeholder="Event City" required class="form-control input_box" value="' . $row_event['City'] . '"/>
                                                    <select name="eventStateShort" class="form-control input_box">
                                                        <option value="">Select State</option>
                                                        ' . $eventStateOptions . '
                                                    </select>
                                                    <input name="eventZip" type="text" placeholder="Event Zip" required class="form-control input_box" value="' . $row_event['Zip'] . '"/>
                                                    <select name="eventTimezone" class="form-control input_box full_box">
                                                    <option value="">Select Timezone</option>
                                                    ' . $eventTimezoneOptions . '
                                                    </select>
                                                    <textarea name="eventDescription" class="form-control input_box textarea_box" placeholder="Details / Description">' . $row_event['Description'] . '</textarea>
                                                    <input name="eventCoordinator" type="text" placeholder="Coordinator Name" required class="form-control input_box full_box" value="' . $row_event['Coordinator'] . '"/>
                                                    <input name="eventPhone" type="text" placeholder="Contact Phone" required class="form-control input_box" value="' . $row_event['Phone'] . '"/>
                                                    <input name="eventEmail" type="text" placeholder="Contact Email" required class="form-control input_box" value="' . $row_event['Email'] . '"/>
                                                    <input name="eventWebsite" type="text" placeholder="Event Registration / Info Link" required class="form-control input_box full_box" value="' . $row_event['Website'] . '"/>
                                                    <h5>Showcase on PlayerFax Events Calendar</h5>
                                                    ' . $eventsCalendar . '
                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                    <div class="all_btn">
                                                        <div class="blue_btn">
                                                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#myModal25"><i class="fa fa-chevron-left"></i> Back</button>
                                                        </div>
                                                        <div class="blue_btn">
                                                            <button type="button" class="btn btn-success" data-dismiss="modal" data-toggle="modal" data-target="#editEvent' . $row_event['EventId'] . 'Step2">Next <i class="fa fa-chevron-right"></i></button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /Edit Event Step 1 -->';

                        $playerStateOptions = '';
                        foreach ($states as $stateKey => $stateValue) {
                            $playerStateOptions .= '<option value="' . $stateKey . '">' . $stateValue . '</option>';
                        }

                        // Baseball
                        if ($sportNameLowercase === 'baseball') {

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
                                $baseballPrimaryPositionOptions .= '<option value="' . getBaseballFieldPositionNumber($key) . '">' . $value . '</option>';
                            }

                            foreach ($baseballFieldPositions as $key => $value) {
                                $baseballSecondaryPositionOptions .= '<option value="' . getBaseballFieldPositionNumber($key) . '">' . $value . '</option>';
                            }

                            $addPlayerToEvent .= '
                            <!-- Edit Event Step 1 -->
                            <div id="addPlayerToEvent' . $row_event['EventId'] . '" data-id="' . $row_event['EventId'] . '" class="modal forget_section" role="dialog">
                                <div class="modal-dialog">
                                    <!-- Modal content-->
                                    <div class="container">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <div class="close_img">
                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                </div>
                                                <div class="title_h2">
                                                    <h2>Add Player To Event</h2>
                                                </div>
                                            </div>
                                            <div class="modal-body">
                                                <form id="addPlayerToEvent' . $row_event['EventId'] . 'Form" class="form-horizontal media_popup add_new_events">
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="playerTeamId' . $row_event['EventId'] . '">Team Id</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="playerTeamId' . $row_event['EventId'] . '" name="playerTeamId" class="form-control form-control input_box">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="playerGender' . $row_event['EventId'] . '">Gender</label>
                                                        <div class="col-sm-8">
                                                            <select id="playerGender' . $row_event['EventId'] . '" name="playerGender" class="form-control input_box">
                                                                <option value="male">Male</option>
                                                                <option value="female">Female</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="playerFirstName' . $row_event['EventId'] . '">First Name</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="playerFirstName' . $row_event['EventId'] . '" name="playerFirstName" class="form-control input_box">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="playerMiddleName' . $row_event['EventId'] . '">Middle Name (or initial)</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="playerMiddleName' . $row_event['EventId'] . '" name="playerMiddleName" class="form-control input_box">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="playerLastName' . $row_event['EventId'] . '">Last Name</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="playerLastName' . $row_event['EventId'] . '" name="playerLastName" class="form-control input_box">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="playerGradYear' . $row_event['EventId'] . '">Grad Year</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="playerGradYear' . $row_event['EventId'] . '" name="playerGradYear" class="form-control input_box">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="playerDOB' . $row_event['EventId'] . '">DOB</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="playerDOB' . $row_event['EventId'] . '" name="playerDOB" class="form-control input_box" placeholder="MM/DD/YYYY">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="pCity' . $row_event['EventId'] . '">City</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="playerCity' . $row_event['EventId'] . '" name="playerCity" class="form-control input_box">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="playerStateShort' . $row_event['EventId'] . '">State</label>
                                                        <div class="col-sm-8">
                                                            <select id="playerStateShort' . $row_event['EventId'] . '" name="playerStateShort" class="form-control input_box">
                                                                <option value="">Select State</option>
                                                                ' . $playerStateOptions . '
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="playerEmail' . $row_event['EventId'] . '">Email</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="playerEmail' . $row_event['EventId'] . '" name="playerEmail" class="form-control input_box">
                                                        </div>
                                                    </div>
                                                    <h4 class="text-center">Baseball Field Position</h4>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="baseball_PrimaryPosition' . $row_event['EventId'] . '">Primary</label>
                                                        <div class="col-sm-8">
                                                            <select id="baseball_PrimaryPosition' . $row_event['EventId'] . '" name="baseball_PrimaryPosition" class="form-control input_box">
                                                                <option value="">Select Position</option>
                                                                ' . $baseballPrimaryPositionOptions . '
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="baseball_SecondaryPosition' . $row_event['EventId'] . '">Secondary</label>
                                                        <div class="col-sm-8">
                                                            <select id="baseball_SecondaryPosition' . $row_event['EventId'] . '" name="baseball_SecondaryPosition" class="form-control input_box">
                                                                <option value="">Select Position</option>
                                                                ' . $baseballSecondaryPositionOptions . '
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="addPlayerToEventErrorBox"></div>
                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                    <div class="all_btn">
                                                        <div class="blue_btn">
                                                            <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-chevron-left"></i> Back</button>
                                                        </div>
                                                        <div class="blue_btn">
                                                            <button type="submit" name="submit" value="submit" class="btn btn-success addPlayerToEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /Edit Event Step 1 -->';
                        } // end Baseball

                        // Fast Pitch
                        if ($sportNameLowercase === 'fastpitch') {

                            $fastPitchPrimaryPositionOptions = '';
                            $fastPitchSecondaryPositionOptions = '';
                            $fastPitchFieldPositions = array(
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

                            foreach ($fastPitchFieldPositions as $key => $value) {
                                $fastPitchPrimaryPositionOptions .= '<option value="' . getBaseballFieldPositionNumber($key) . '">' . $value . '</option>';
                            }

                            foreach ($fastPitchFieldPositions as $key => $value) {
                                $fastPitchSecondaryPositionOptions .= '<option value="' . getBaseballFieldPositionNumber($key) . '">' . $value . '</option>';
                            }

                            $addPlayerToEvent .= '
                            <!-- Edit Event Step 1 -->
                            <div id="addPlayerToEvent' . $row_event['EventId'] . '" data-id="' . $row_event['EventId'] . '" class="modal forget_section" role="dialog">
                                <div class="modal-dialog">
                                    <!-- Modal content-->
                                    <div class="container">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <div class="close_img">
                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                </div>
                                                <div class="title_h2">
                                                    <h2>Add Player To Event</h2>
                                                </div>
                                            </div>
                                            <div class="modal-body">
                                                <form id="addPlayerToEvent' . $row_event['EventId'] . 'Form" class="form-horizontal media_popup add_new_events">
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="playerTeamId' . $row_event['EventId'] . '">Team Id</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="playerTeamId' . $row_event['EventId'] . '" name="playerTeamId" class="form-control input_box">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="pGender' . $row_event['EventId'] . '">Gender</label>
                                                        <div class="col-sm-8">
                                                            <select id="pGender' . $row_event['EventId'] . '" name="pGender" class="form-control input_box">
                                                                <option value="male">Male</option>
                                                                <option value="female">Female</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="playerFirstName' . $row_event['EventId'] . '">First Name</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="playerFirstName' . $row_event['EventId'] . '" name="playerFirstName" class="form-control input_box">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="playerMiddleName' . $row_event['EventId'] . '">Initial</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="playerMiddleName' . $row_event['EventId'] . '" name="playerMiddleName" class="form-control input_box">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="playerLastName' . $row_event['EventId'] . '">Last Name</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="playerLastName' . $row_event['EventId'] . '" name="playerLastName" class="form-control input_box">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="playerGradYear' . $row_event['EventId'] . '">Grad Year</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="playerGradYear' . $row_event['EventId'] . '" name="playerGradYear" class="form-control input_box">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="playerDOB' . $row_event['EventId'] . '">DOB</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="playerDOB' . $row_event['EventId'] . '" name="playerDOB" class="form-control input_box" placeholder="MM/DD/YYYY">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="playerCity' . $row_event['EventId'] . '">City</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="playerCity' . $row_event['EventId'] . '" name="playerCity" class="form-control input_box">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="playerStateShort' . $row_event['EventId'] . '">State</label>
                                                        <div class="col-sm-8">
                                                            <select id="playerStateShort' . $row_event['EventId'] . '" name="playerStateShort" class="form-control input_box">
                                                                <option value="">Select State</option>
                                                                ' . $playerStateOptions . '
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="playerEmail' . $row_event['EventId'] . '">Email</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="playerEmail' . $row_event['EventId'] . '" name="playerEmail" class="form-control input_box">
                                                        </div>
                                                    </div>
                                                    <h3 class="text-center">Fast Pitch Field Position</h3>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="fastPitch_PrimaryPosition' . $row_event['EventId'] . '">Primary</label>
                                                        <div class="col-sm-8">
                                                            <select id="fastPitch_PrimaryPosition' . $row_event['EventId'] . '" name="fastPitch_PrimaryPosition" class="form-control input_box">
                                                                <option value="">Select Position</option>
                                                                ' . $fastPitchPrimaryPositionOptions . '
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="fastPitch_SecondaryPosition' . $row_event['EventId'] . '">Secondary</label>
                                                        <div class="col-sm-8">
                                                            <select id="fastPitch_SecondaryPosition' . $row_event['EventId'] . '" name="fastPitch_SecondaryPosition" class="form-control input_box">
                                                                <option value="">Select Position</option>
                                                                ' . $fastPitchSecondaryPositionOptions . '
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="addPlayerToEventErrorBox"></div>
                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                    <div class="all_btn">
                                                        <div class="blue_btn">
                                                            <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-chevron-left"></i> Back</button>
                                                        </div>
                                                        <div class="blue_btn">
                                                            <button type="submit" name="submit" value="submit" class="btn btn-success addPlayerToEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /Edit Event Step 1 -->';
                        } // end Fast Pitch

                        // Lacrosse
                        if ($sportNameLowercase === 'lacrosse') {

                            $addPlayerToEvent .= '
                            <!-- Edit Event Step 1 -->
                            <div id="addPlayerToEvent' . $row_event['EventId'] . '" data-id="' . $row_event['EventId'] . '" class="modal forget_section" role="dialog">
                                <div class="modal-dialog">
                                    <!-- Modal content-->
                                    <div class="container">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <div class="close_img">
                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                </div>
                                                <div class="title_h2">
                                                    <h2>Add Player To Event</h2>
                                                </div>
                                            </div>
                                            <div class="modal-body">
                                                <form id="addPlayerToEvent' . $row_event['EventId'] . 'Form" class="form-horizontal media_popup add_new_events">
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="playerTeamId' . $row_event['EventId'] . '">Team Id</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="playerTeamId' . $row_event['EventId'] . '" name="playerTeamId" class="form-control input_box">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="playerGender' . $row_event['EventId'] . '">Gender</label>
                                                        <div class="col-sm-8">
                                                            <select id="playerGender' . $row_event['EventId'] . '" name="playerGender" class="form-control input_box">
                                                                <option value="male">Male</option>
                                                                <option value="female">Female</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="playerFirstName' . $row_event['EventId'] . '">First Name</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="playerFirstName' . $row_event['EventId'] . '" name="playerFirstName" class="form-control input_box">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="playerMiddleName' . $row_event['EventId'] . '">Initial</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="playerMiddleName' . $row_event['EventId'] . '" name="playerMiddleName" class="form-control input_box">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="playerLastName' . $row_event['EventId'] . '">Last Name</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="playerLastName' . $row_event['EventId'] . '" name="playerLastName" class="form-control input_box">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="playerGradYear' . $row_event['EventId'] . '">Grad Year</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="playerGradYear' . $row_event['EventId'] . '" name="playerGradYear" class="form-control input_box">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="playerDOB' . $row_event['EventId'] . '">DOB</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="playerDOB' . $row_event['EventId'] . '" name="playerDOB" class="form-control input_box" placeholder="MM/DD/YYYY">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="playerCity' . $row_event['EventId'] . '">City</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="playerCity' . $row_event['EventId'] . '" name="playerCity" class="form-control input_box">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="playerStateShort' . $row_event['EventId'] . '">State</label>
                                                        <div class="col-sm-8">
                                                            <select id="playerStateShort' . $row_event['EventId'] . '" name="playerStateShort" class="form-control input_box">
                                                                <option value="">Select State</option>
                                                                ' . $playerStateOptions . '
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="playerEmail' . $row_event['EventId'] . '">Email</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="playerEmail' . $row_event['EventId'] . '" name="playerEmail" class="form-control input_box">
                                                        </div>
                                                    </div>
                                                    <div class="addPlayerToEventErrorBox"></div>
                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                    <div class="all_btn">
                                                        <div class="blue_btn">
                                                            <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-chevron-left"></i> Back</button>
                                                        </div>
                                                        <div class="blue_btn">
                                                            <button type="submit" name="submit" value="submit" class="btn btn-success addPlayerToEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /Edit Event Step 1 -->';
                        } // end Lacrosse

                        // Cross Fit
                        if ($sportNameLowercase === 'crossfit') {

                            $addPlayerToEvent .= '
                            <!-- Edit Event Step 1 -->
                            <div id="addPlayerToEvent' . $row_event['EventId'] . '" data-id="' . $row_event['EventId'] . '" class="modal forget_section" role="dialog">
                                <div class="modal-dialog">
                                    <!-- Modal content-->
                                    <div class="container">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <div class="close_img">
                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                </div>
                                                <div class="title_h2">
                                                    <h2>Add Player To Event</h2>
                                                </div>
                                            </div>
                                            <div class="modal-body">
                                                <form id="addPlayerToEvent' . $row_event['EventId'] . 'Form" class="form-horizontal media_popup add_new_events">
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="playerTeamId' . $row_event['EventId'] . '">Team Id</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="playerTeamId' . $row_event['EventId'] . '" name="playerTeamId" class="form-control input_box">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="playerGender' . $row_event['EventId'] . '">Gender</label>
                                                        <div class="col-sm-8">
                                                            <select id="playerGender' . $row_event['EventId'] . '" name="playerGender" class="form-control input_box">
                                                                <option value="male">Male</option>
                                                                <option value="female">Female</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="playerFirstName' . $row_event['EventId'] . '">First Name</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="playerFirstName' . $row_event['EventId'] . '" name="playerFirstName" class="form-control input_box">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="playerMiddleName' . $row_event['EventId'] . '">Initial</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="playerMiddleName' . $row_event['EventId'] . '" name="playerMiddleName" class="form-control input_box">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="playerLastName' . $row_event['EventId'] . '">Last Name</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="playerLastName' . $row_event['EventId'] . '" name="playerLastName" class="form-control input_box">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="playerGradYear' . $row_event['EventId'] . '">Grad Year</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="playerGradYear' . $row_event['EventId'] . '" name="playerGradYear" class="form-control input_box">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="playerDOB' . $row_event['EventId'] . '">DOB</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="playerDOB' . $row_event['EventId'] . '" name="playerDOB" class="form-control input_box" placeholder="MM/DD/YYYY">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="playerCity' . $row_event['EventId'] . '">City</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="playerCity' . $row_event['EventId'] . '" name="playerCity" class="form-control input_box">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="playerStateShort' . $row_event['EventId'] . '">State</label>
                                                        <div class="col-sm-8">
                                                            <select id="playerStateShort' . $row_event['EventId'] . '" name="playerStateShort" class="form-control input_box">
                                                                <option value="">Select State</option>
                                                                ' . $playerStateOptions . '
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="playerEmail' . $row_event['EventId'] . '">Email</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="playerEmail' . $row_event['EventId'] . '" name="playerEmail" class="form-control input_box">
                                                        </div>
                                                    </div>
                                                    <div class="addPlayerToEventErrorBox"></div>
                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                    <div class="all_btn">
                                                        <div class="blue_btn">
                                                            <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-chevron-left"></i> Back</button>
                                                        </div>
                                                        <div class="blue_btn">
                                                            <button type="submit" name="submit" value="submit" class="btn btn-success addPlayerToEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /Edit Event Step 1 -->';
                        } // end Cross Fit

                        // Swimming
                        if ($sportNameLowercase === 'swimming') {

                            $addPlayerToEvent .= '
                            <!-- Edit Event Step 1 -->
                            <div id="addPlayerToEvent' . $row_event['EventId'] . '" data-id="' . $row_event['EventId'] . '" class="modal forget_section" role="dialog">
                                <div class="modal-dialog">
                                    <!-- Modal content-->
                                    <div class="container">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <div class="close_img">
                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                </div>
                                                <div class="title_h2">
                                                    <h2>Add Player To Event</h2>
                                                </div>
                                            </div>
                                            <div class="modal-body">
                                                <form id="addPlayerToEvent' . $row_event['EventId'] . 'Form" class="form-horizontal media_popup add_new_events">
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="playerTeamId' . $row_event['EventId'] . '">Team Id</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="playerTeamId' . $row_event['EventId'] . '" name="playerTeamId" class="form-control input_box">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="playerGender' . $row_event['EventId'] . '">Gender</label>
                                                        <div class="col-sm-8">
                                                            <select id="playerGender' . $row_event['EventId'] . '" name="playerGender" class="form-control input_box">
                                                                <option value="male">Male</option>
                                                                <option value="female">Female</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="playerFirstName' . $row_event['EventId'] . '">First Name</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="playerFirstName' . $row_event['EventId'] . '" name="playerFirstName" class="form-control input_box">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="playerMiddleName' . $row_event['EventId'] . '">Initial</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="playerMiddleName' . $row_event['EventId'] . '" name="playerMiddleName" class="form-control input_box">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="playerLastName' . $row_event['EventId'] . '">Last Name</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="playerLastName' . $row_event['EventId'] . '" name="playerLastName" class="form-control input_box">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="playerGradYear' . $row_event['EventId'] . '">Grad Year</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="playerGradYear' . $row_event['EventId'] . '" name="playerGradYear" class="form-control input_box">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="playerDOB' . $row_event['EventId'] . '">DOB</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="playerDOB' . $row_event['EventId'] . '" name="playerDOB" class="form-control input_box" placeholder="MM/DD/YYYY">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="playerCity' . $row_event['EventId'] . '">City</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="playerCity' . $row_event['EventId'] . '" name="playerCity" class="form-control input_box">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="playerStateShort' . $row_event['EventId'] . '">State</label>
                                                        <div class="col-sm-8">
                                                            <select id="playerStateShort' . $row_event['EventId'] . '" name="playerStateShort" class="form-control input_box">
                                                                <option value="">Select State</option>
                                                                ' . $playerStateOptions . '
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="playerEmail' . $row_event['EventId'] . '">Email</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="playerEmail' . $row_event['EventId'] . '" name="playerEmail" class="form-control input_box">
                                                        </div>
                                                    </div>
                                                    <div class="addPlayerToEventErrorBox"></div>
                                                    <input type="hidden" name="eventId" value="' . $row_event['EventId'] . '">
                                                    <div class="all_btn">
                                                        <div class="blue_btn">
                                                            <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-chevron-left"></i> Back</button>
                                                        </div>
                                                        <div class="blue_btn">
                                                            <button type="submit" name="submit" value="submit" class="btn btn-success addPlayerToEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /Edit Event Step 1 -->';
                        } // end Swimming

                        $sport_image = (!empty($sportName)) ? 'sport_' . $sportNameLowercase . '.png' : 'manage_event.png';
                        $sport_name = (!empty($sportName)) ? $sportName : '';

                        $cityState = (!empty($row_event['City'])) ? ' - ' . rtrim($row_event['City'] . ', ' . $row_event['StateShort'], ',') : '';

                        echo '<div id="accordion' . $row_event['EventId'] . '" class="panel-group manage_accordion" style="margin-top:20px;">';

                        echo '<div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a data-toggle="collapse" data-parent="#accordion' . $row_event['EventId'] . '" href="#m' . $row_event['EventId'] . '" aria-expanded="false" class="collapsed">
                                                ' . $row_event['Name'] . '
                                                <span>' . $row_event['Address'] . ' ' . ltrim($cityState, ' - ') . '</span>
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="m' . $row_event['EventId'] . '" class="panel-collapse collapse" aria-expanded="false">
                                        <div class="panel-body">
                                            <div class="event_description">
                                                <div class="event_time">
                                                    <p>' . $_time . '<span>' . $_date . '</span></p>
                                                    <div class="event_title">
                                                        <h5>Event Description</h5>
                                                        <p>' . $row_event['Description'] . '</p>
                                                    </div>
                                                </div>
                                            </div>';


                        echo '<div class="admin_listed"><div class="event_title"><h5>Admins Listed:</h5></div><div class="event-admin-tags">';

                        // list event admins
                        $adminTagsNonEditable = '';
                        $adminTagsEditable = '';
                        try {
                            $sql_admin = "
                                SELECT 
                                  `EventAdmins`.`UserId`,
                                  `Users`.`FirstName`,
                                  `Users`.`LastName`
                                FROM 
                                  `EventAdmins` 
                                LEFT JOIN 
                                  `Users`
                                ON 
                                  `EventAdmins`.`UserId` = `Users`.`UserId`
                                WHERE
                                  `EventAdmins`.`EventId` = :EventId";

                            $stmt_admin = $PDO->prepare($sql_admin);
                            $stmt_admin->bindParam('EventId', $row_event['EventId'], PDO::PARAM_INT);
                            $stmt_admin->execute();

                            while ($row_admin = $stmt_admin->fetch(PDO::FETCH_ASSOC)) {

                                $adminId = $row_admin['UserId'];
                                $adminFullName = trim($row_admin['FirstName'] . ' ' . $row_admin['LastName']);

                                $adminTagsNonEditable .= '<div class="grey_tag_box"><div class="text_box_tag">' . $adminFullName . '</div></div>';
                                $adminTagsEditable .= '<div class="grey_tag_box"><div class="text_box_tag">' . $adminFullName . '<input data-eventAdminId="' . $row_admin['UserId'] . '" type="hidden" name="eventAdminId[]" value="' . $row_admin['UserId'] . '"><img src="images/tag_close.png" class="tag_close"></div></div>';
                            }

                        } catch (PDOException $e) {
                            trigger_error($e->getMessage(), E_USER_ERROR);
                        }//end try

                        echo $adminTagsNonEditable;
                        echo '</div></div>'; // end event-admin-tags and admin_listed

                        $editEventStep2 .= '
                            
                            <!-- Edit Event Step 2 -->
                            <div id="editEvent' . $row_event['EventId'] . 'Step2" data-id="' . $row_event['EventId'] . '" class="modal forget_section" role="dialog">
                                <div class="modal-dialog">
                                    <!-- Modal content-->
                                    <div class="container">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <div class="close_img">
                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                </div>
                                                <div class="title_h2">
                                                    <h2>Add/Remove Admins of Event</h2>
                                                </div>
                                            </div>
                                            <div class="modal-body">
                                                <form id="editEvent' . $row_event['EventId'] . 'Step2Form">
                                                 <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="editEventErrorBox">
                                                            <div class="alertMsg info">
                                                                <span><i class="fa fa-check-square-o"></i></span> This step is optional. Press Submit when done. <a class="alert-close" href="#">x</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                    <div class="add_admins_full">
                                                        <div class="col-sm-offset-1 col-sm-10">
                                                            <div class="btns_bottom_text">
                                                                <span>Admin Search</span>
                                                                <p>Search by First Name, Last Name and Email Address</p>
                                                            </div>
                                                            <select class="searchEventAdminQuery form-control" style="width: 100%;">
                                                                <option></option>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-offset-1 col-sm-10">
                                                            <div class="admin_listed">
                                                                <div class="event_title"><h5>Admins Listed:</h5></div>
                                                                <div class="event-admin-tags">' . $adminTagsEditable . '</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="all_btn">
                                                        <div class="blue_btn">
                                                            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#editEvent' . $row_event['EventId'] . 'Step1"><i class="fa fa-chevron-left"></i> Back</button>
                                                        </div>
                                                        <div class="blue_btn">
                                                            <button type="button" class="btn btn-success editEvent" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /Edit Event Step 2 -->';


                        echo '<div class="admin_listed"><div class="event_title"><h5>Players Invited:</h5></div><div class="event-admin-tags">';

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

                                $playerFullName = trim($row_player['FirstName'] . ' ' . $row_player['LastName']);

                                $playerTagsNonEditable .= '<div class="grey_tag_box"><div class="text_box_tag">' . $playerFullName . '</div></div>';

                            } // end while

                        } catch (PDOException $e) {
                            trigger_error($e->getMessage(), E_USER_ERROR);
                        }//end try

                        echo $playerTagsNonEditable;
                        echo '</div></div>'; // end event-admin-tags and admin_listed

                        echo '<div class="btn-group pull-right" role="group" aria-label="...">
                                  <a class="btn btn-primary" href="index.php?page=event-results&id=' . $row_event['EventId'] . '"><i class="fa fa-external-link" aria-hidden="true"></i> View Results</a>
                                  <a class="btn btn-info" data-dismiss="modal" data-toggle="modal" data-target="#editEvent' . $row_event['EventId'] . 'Step1"><i class="fa fa-pencil" aria-hidden="true"></i> Edit Event</a>
                                  <a class="btn btn-info" data-dismiss="modal" data-toggle="modal" data-target="#addPlayerToEvent' . $row_event['EventId'] . '"><i class="fa fa-plus" aria-hidden="true"></i> Add Player</a>
                                  <a class="btn btn-primary" href="index.php?page=event-details&id=' . $row_event['EventId'] . '"><i class="fa fa-external-link" aria-hidden="true"></i> Enter Results</a>
                              </div>';

                        echo '</div></div></div></div>';

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

<!-- Add Event Step 1 -->
<div id="addEventStep1" class="modal forget_section" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="container">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="close_img">
                        <button type="button" class="close" data-dismiss="modal"></button>
                    </div>
                    <div class="title_h2">
                        <h2>Step 1 - Event Details</h2>
                    </div>
                </div>
                <div class="modal-body">
                    <form id="addEventStep1Form" class="media_popup add_new_events">
                        <input name="eventName" type="text" placeholder="Event Name" required class="form-control input_box"/>
                        <select name="eventSport" class="form-control input_box">
                            <option>Select Sport</option>
                            <option value="baseball">Baseball</option>
                            <option value="crossfit">Cross Fit</option>
                            <option value="fastpitch">Fast Pitch</option>
                            <option value="lacrosse">Lacrosse</option>
                            <option value="swimming">Swimming</option>
                        </select>
                        <input name="eventStartDate" type="text" placeholder="Event Date & Time" required class="form-control input_box datetime"/>
                        <input name="eventEndDate" type="text" placeholder="End Date &amp; Time" required class="form-control input_box datetime"/>
                        <select name="eventRounds" class="form-control input_box full_box">
                            <option value="1">One Round</option>
                            <option value="2">Two Rounds</option>
                            <option value="3">Three Rounds</option>
                            <option value="4">Four Rounds</option>
                            <option value="5">Five Rounds</option>
                            <option value="6" selected="selected">Six Rounds (Default)</option>
                            <option value="7">Seven Rounds</option>
                            <option value="8">Eight Rounds</option>
                            <option value="9">Nine Rounds</option>
                            <option value="10">Ten Rounds</option>
                        </select>
                        <input name="eventAddress" type="text" placeholder="Event Address" required class="form-control input_box"/>
                        <input name="eventCity" type="text" placeholder="Event City" required class="form-control input_box"/>
                        <select name="eventStateShort" class="form-control input_box">
                            <option value="">Select State</option>
                            <option value="AL">Alabama</option>
                            <option value="AK">Alaska</option>
                            <option value="AZ">Arizona</option>
                            <option value="AR">Arkansas</option>
                            <option value="CA">California</option>
                            <option value="CO">Colorado</option>
                            <option value="CT">Connecticut</option>
                            <option value="DE">Delaware</option>
                            <option value="DC">District Of Columbia</option>
                            <option value="FL">Florida</option>
                            <option value="GA">Georgia</option>
                            <option value="HI">Hawaii</option>
                            <option value="ID">Idaho</option>
                            <option value="IL">Illinois</option>
                            <option value="IN">Indiana</option>
                            <option value="IA">Iowa</option>
                            <option value="KS">Kansas</option>
                            <option value="KY">Kentucky</option>
                            <option value="LA">Louisiana</option>
                            <option value="ME">Maine</option>
                            <option value="MD">Maryland</option>
                            <option value="MA">Massachusetts</option>
                            <option value="MI">Michigan</option>
                            <option value="MN">Minnesota</option>
                            <option value="MS">Mississippi</option>
                            <option value="MO">Missouri</option>
                            <option value="MT">Montana</option>
                            <option value="NE">Nebraska</option>
                            <option value="NV">Nevada</option>
                            <option value="NH">New Hampshire</option>
                            <option value="NJ">New Jersey</option>
                            <option value="NM">New Mexico</option>
                            <option value="NY">New York</option>
                            <option value="NC">North Carolina</option>
                            <option value="ND">North Dakota</option>
                            <option value="OH">Ohio</option>
                            <option value="OK">Oklahoma</option>
                            <option value="OR">Oregon</option>
                            <option value="PA">Pennsylvania</option>
                            <option value="RI">Rhode Island</option>
                            <option value="SC">South Carolina</option>
                            <option value="SD">South Dakota</option>
                            <option value="TN">Tennessee</option>
                            <option value="TX">Texas</option>
                            <option value="UT">Utah</option>
                            <option value="VT">Vermont</option>
                            <option value="VA">Virginia</option>
                            <option value="WA">Washington</option>
                            <option value="WV">West Virginia</option>
                            <option value="WI">Wisconsin</option>
                            <option value="WY">Wyoming</option>
                            <option value="AS">American Samoa</option>
                            <option value="GU">Guam</option>
                            <option value="MP">Northern Mariana Islands</option>
                            <option value="PR">Puerto Rico</option>
                            <option value="UM">United States Minor Outlying Islands</option>
                            <option value="VI">Virgin Islands</option>
                            <option value="AA">Armed Forces Americas</option>
                            <option value="AP">Armed Forces Pacific</option>
                            <option value="AE">Armed Forces Others</option>
                        </select>
                        <input name="eventZip" type="text" placeholder="Event Zip" required class="form-control input_box"/>
                        <select name="eventTimezone" class="form-control input_box full_box">
                            <option value="">Select Timezone</option>
                            <?php
                            foreach ($zones as $tzName => $timezones) {
                                echo '<optgroup label="' . $tzName . '">';
                                foreach ($timezones as $timezone => $value) {
                                    $time = new DateTime(NULL, new DateTimeZone($timezone));
                                    $value = '(GMT' . $time->format('P') . ') ' . $timezones[$timezone] . ' (' . $timezone . ')';
                                    $value = str_replace('_', ' ', $value);
                                    echo '<option value="' . $timezone . '">' . $value . '</option>';
                                }
                                echo '</optgroup>';
                            }
                            ?>
                        </select>
                        <textarea name="eventDescription" class="form-control input_box textarea_box" placeholder="Details / Description"></textarea>
                        <input name="eventCoordinator" type="text" placeholder="Coordinator Name" required class="form-control input_box full_box"/>
                        <input name="eventPhone" type="text" placeholder="Contact Phone" required class="form-control input_box"/>
                        <input name="eventEmail" type="text" placeholder="Contact Email" required class="form-control input_box"/>
                        <input name="eventWebsite" type="text" placeholder="Event Registration / Info Link" required class="form-control input_box full_box"/>
                        <h5>Showcase on PlayerFax Events Calendar</h5>
                        <label class="radio-inline"><input type="radio" name="eventCalendar" value="yes" checked="checked"> Yes</label>
                        <label class="radio-inline"><input type="radio" name="eventCalendar" value="no"> No</label>
                        <div class="all_btn">
                            <div class="blue_btn">
                                <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-chevron-left"></i> Back</button>
                            </div>
                            <div class="blue_btn">
                                <button type="button" class="btn btn-success" data-dismiss="modal" data-toggle="modal" data-target="#addEventStep2">Next <i class="fa fa-chevron-right"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Add Event Step 1 -->
<!-- Add Event Step 2 -->
<div id="addEventStep2" class="modal forget_section" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="container">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="close_img">
                        <button type="button" class="close" data-dismiss="modal"></button>
                    </div>
                    <div class="title_h2">
                        <h2>Step 2 - Add Admins to Event</h2>
                    </div>
                </div>
                <div class="modal-body" style="overflow:hidden;">
                    <form id="addEventStep2Form">
                        <div class="row">
                            <div class="col-sm-12">
                                <div id="addEventErrorBox">
                                    <div class="alertMsg info">
                                        <span><i class="fa fa-check-square-o"></i></span> This step is optional. Press Submit when done. <a class="alert-close" href="#">x</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="add_admins_full">
                            <div class="col-sm-offset-1 col-sm-10">
                                <div class="btns_bottom_text">
                                    <span>Admin Search</span>
                                    <p>Search by First Name, Last Name and Email Address</p>
                                </div>
                                <select class="searchEventAdminQuery form-control" style="width: 100%;">
                                    <option></option>
                                </select>
                            </div>
                            <div class="col-sm-offset-1 col-sm-10">
                                <div class="event_title"><h5>Admins Listed:</h5></div>
                                <div class="event-admin-tags"></div>
                            </div>
                        </div>
                        <div class="all_btn">
                            <div class="blue_btn">
                                <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                            </div>
                            <div class="blue_btn">
                                <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#addEventStep1"><i class="fa fa-chevron-left"></i> Back</button>
                            </div>
                            <div class="blue_btn">
                                <button id="addEvent" type="submit" name="submit" value="submit" class="btn btn-success" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Add Event Step 2 -->

<?php
// generated modal dialogs
echo $editEventStep1;
echo $editEventStep2;
echo $addPlayerToEvent;
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

        $('button.addPlayerToEvent').each(function () {
            var myButton = $(this);
            var parent = $(this).closest('.modal');
            var myForm = parent.find('form');
            var modalId = parent.attr('id');
            var eventId = myForm.find('input[name="eventId"]').val();
            var myErrorBox = parent.find('.addPlayerToEventErrorBox');

            myForm.on('submit', function (e) {
                if (isComplete === false) {
                    myButton.button('loading');
                    isComplete = true;
                    $.ajax({
                        url: 'index.php?action=addPlayerToEvent',
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

        $('#addEvent').on('click', function (e) {

            if (isComplete === false) {
                $('#addEvent').button('loading');
                isComplete = true;
                $.ajax({
                    url: 'index.php?action=addEvent',
                    type: 'POST',
                    data: {'step1': $('#addEventStep1Form').MytoJson(), 'step2': $('#addEventStep2Form').MytoJson()},
                    dataType: 'json',
                    success: function (json) {

                        $('#addEventErrorBox').empty();

                        //data: return data from server
                        if (typeof json.success !== 'undefined') {
                            $('#addEventErrorBox').html(json.success);
                        }

                        if (typeof json.error !== 'undefined') {
                            $('#addEventErrorBox').html(json.error);
                        }
                        isComplete = false;
                        $('#addEvent').button('reset');
                    }
                });
            }
            e.preventDefault(); //STOP default action
            //e.unbind();
        });

        $('button.editEvent').each(function () {
            var parent = $(this).closest('.modal');
            var id = parent.data('id');

            $(this).on('click', function (e) {

                var step1 = $('#editEvent' + id + 'Step1Form');
                var step2 = $('#editEvent' + id + 'Step2Form');
                var myErrorBox = parent.find('.editEventErrorBox');
                var myButton = $(this);

                if (isComplete === false) {
                    myButton.button('loading');
                    isComplete = true;
                    $.ajax({
                        url: 'index.php?action=editEvent',
                        type: 'POST',
                        data: {'step1': step1.MytoJson(), 'step2': step2.MytoJson()},
                        dataType: 'json',
                        success: function (json) {

                            $(myErrorBox).empty();

                            if (typeof json.success !== 'undefined') {
                                $(myErrorBox).html(json.success);
                            }

                            if (typeof json.error !== 'undefined') {
                                $(myErrorBox).html(json.error);
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

    (function (jQuery) {

        jQuery.fn.MytoJson = function (options) {

            options = jQuery.extend({}, options);

            var self = this,
                json = {},
                push_counters = {},
                patterns = {
                    "validate": /^[a-zA-Z][a-zA-Z0-9_]*(?:\[(?:\d*|[a-zA-Z0-9_]+)\])*$/,
                    "key": /[a-zA-Z0-9_]+|(?=\[\])/g,
                    "push": /^$/,
                    "fixed": /^\d+$/,
                    "named": /^[a-zA-Z0-9_]+$/
                };


            this.build = function (base, key, value) {
                base[key] = value;
                return base;
            };

            this.push_counter = function (key) {
                if (push_counters[key] === undefined) {
                    push_counters[key] = 0;
                }
                return push_counters[key]++;
            };

            jQuery.each(jQuery(this).serializeArray(), function () {

                // skip invalid keys
                if (!patterns.validate.test(this.name)) {
                    return;
                }

                var k,
                    keys = this.name.match(patterns.key),
                    merge = this.value,
                    reverse_key = this.name;

                while ((k = keys.pop()) !== undefined) {

                    // adjust reverse_key
                    reverse_key = reverse_key.replace(new RegExp("\\[" + k + "\\]$"), '');

                    // push
                    if (k.match(patterns.push)) {
                        merge = self.build([], self.push_counter(reverse_key), merge);
                    }

                    // fixed
                    else if (k.match(patterns.fixed)) {
                        merge = self.build([], k, merge);
                    }

                    // named
                    else if (k.match(patterns.named)) {
                        merge = self.build({}, k, merge);
                    }
                }

                json = jQuery.extend(true, json, merge);
            });


            return json;
        }

    })(jQuery);
</script>
</body>
</html>