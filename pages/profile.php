<?php

if (!isset($_SESSION['userId'])) {
    header('Location: index.php?page=login');
    exit;
}

$msgBox = '';
$error = false;

$gender = '';
$timezone = '';
$fullName = '';
$firstName = '';
$lastName = '';
$email = '';

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

if (isset($_POST['submit']) && $_POST['submit'] == 'update') {

    $timezone = isset($_POST['timezone']) ? trim($_POST['timezone']) : 'America/Phoenix';

    $gender = isset($_POST['gender']) ? trim($_POST['gender']) : 'male';
    $gender = filter_var($gender, FILTER_SANITIZE_STRING);

    if ($gender == 'male') {
        // male
        $gender = '0';
    } else {
        // female
        $gender = '1';
    }

    $firstName = isset($_POST['firstName']) ? trim($_POST['firstName']) : '';
    $firstName = filter_var($firstName, FILTER_SANITIZE_STRING);
    $firstName = ucfirst(strtolower($firstName));

    $lastName = isset($_POST['lastName']) ? trim($_POST['lastName']) : '';
    $lastName = filter_var($lastName, FILTER_SANITIZE_STRING);
    $lastName = ucfirst(strtolower($lastName));

    $fullName = trim($firstName . ' ' . $lastName);
    $fullName = preg_replace('/\s+/', ' ', $fullName);

    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $email = strtolower($email);

    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = true;
        $msgBox = alertBox("Enter a valid email address", "<i class='fa fa-times'></i>", "danger");
    }

    $foundTimezone = false;
    foreach ($zones as $tzName => $timezones) {
        foreach ($timezones as $zone => $value) {
            if ($zone === $timezone) {
                $foundTimezone = true;
            }
        }
    }
    if (!$foundTimezone) {
        $error = true;
        $msgBox = alertBox("Selected Timezone is invalid", "<i class='fa fa-times'></i>", "danger");
    }

    $lastUpdated = date('Y-m-d H:i:s');

    // check if email address exists
    $count = 0;
    try {
        $sql = "
        SELECT 
          `Users`.`UserId`
        FROM 
          `Users` 
        WHERE
          `Users`.`Email` = :Email
        AND 
          `Users`.`UserId` != :UserId";

        $stmt = $PDO->prepare($sql);
        $stmt->bindParam('Email', $email, PDO::PARAM_STR);
        $stmt->bindParam('UserId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $count++;
        }

    } catch (PDOException $e) {
        trigger_error($e->getMessage(), E_USER_ERROR);
    }//end try

    if ($count > 0) {
        $error = true;
        $msgBox = alertBox("This email is already registered", "<i class='fa fa-times'></i>", "danger");
    }

    if (!$error) {

        // update session variables
        $_SESSION['userIp'] = getClientIp();
        $_SESSION['userEmail'] = $email;
        $_SESSION['userGender'] = $gender;
        $_SESSION['userTimezone'] = $timezone;
        $_SESSION['userFullName'] = filter_var($fullName, FILTER_SANITIZE_STRING);
        $_SESSION['userFirstName'] = filter_var($firstName, FILTER_SANITIZE_STRING);
        $_SESSION['userLastName'] = filter_var($lastName, FILTER_SANITIZE_STRING);

        // update table
        try {
            $sql = "
            UPDATE `Users` 
            SET
              `Users`.`LastUpdated` = :LastUpdated,
              `Users`.`Gender` = :Gender,
              `Users`.`Timezone` = :Timezone,
              `Users`.`FirstName` = :FirstName,
              `Users`.`LastName` = :LastName,
              `Users`.`Email` = :Email
            WHERE 
              `Users`.`UserId` = :UserId";

            $stmt = $PDO->prepare($sql);

            $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
            $stmt->bindParam('Gender', $gender, PDO::PARAM_STR);
            $stmt->bindParam('FirstName', $firstName, PDO::PARAM_STR);
            $stmt->bindParam('LastName', $lastName, PDO::PARAM_STR);
            $stmt->bindParam('Email', $email, PDO::PARAM_STR);
            $stmt->bindParam('Timezone', $timezone, PDO::PARAM_STR);
            $stmt->bindParam('UserId', $userId, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
        }//end try
        
        // give user new password
        if (!empty($password)) {

            $options = [
                'cost' => 12,
            ];

            $passwordHash = password_hash($password, PASSWORD_DEFAULT, $options);

            try {
                $sql = "
                UPDATE `Users` 
                SET
                  `Users`.`LastUpdated` = :LastUpdated,
                  `Users`.`Password` = :_Password
                WHERE 
                  `Users`.`UserId` = :UserId";

                $stmt = $PDO->prepare($sql);

                $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                $stmt->bindParam('_Password', $passwordHash, PDO::PARAM_STR);
                $stmt->bindParam('UserId', $userId, PDO::PARAM_INT);
                $stmt->execute();
            } catch (PDOException $e) {
                trigger_error($e->getMessage(), E_USER_ERROR);
            }//end try
        }

        $msgBox = alertBox("Profile updated successfully", "<i class='fa fa-check-square-o'></i>", "success");

    }

}

// Get User data for the form fields below
try {
    $sql = "
        SELECT
          `Users`.`UserId`,
          `Users`.`Email`,
          `Users`.`Gender`,
          `Users`.`Timezone`,
          `Users`.`FirstName`,
          `Users`.`LastName`
        FROM 
          `Users` 
        USE INDEX (`UserIdIsActive`)
        WHERE
          `Users`.`UserId` = :UserId
        AND 
          `Users`.`IsActive` = 1";

    $stmt = $PDO->prepare($sql);
    $stmt->bindParam('UserId', $_SESSION['userId'], PDO::PARAM_STR);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $userEmail = $row['Email'];
        $userGender = $row['Gender'];
        $userTimezone = $row['Timezone'];
        $userFullName = trim($row['FirstName'] . ' ' . $row['LastName']);
        $userFullName = preg_replace('/\s+/', ' ', $userFullName);
        $userFirstName = $row['FirstName'];
        $userLastName = $row['LastName'];
    }

} catch (PDOException $e) {
    trigger_error($e->getMessage(), E_USER_ERROR);
}//end try

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

</head>
<body class="user_home" id="portrait">
<?php require 'includes/header.php'; ?>
<section class="user_profile_main">
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <!-- col-sm-10 -->
                <?php if ($msgBox) {
                    echo $msgBox;
                } ?>
                <form action="index.php?page=profile" method="post" class="form-horizontal">
                    <div class="form-group">
                        <label for="gender" class="col-sm-2 control-label">Gender</label>
                        <div class="col-sm-10">
                            <select name="gender" class="form-control input_box" id="gender">
                                <option value="">Select Gender</option>
                                <?php
                                $genderOptions = array('Male', 'Female');
                                foreach ($genderOptions as $key => $gender) {
                                    if ($userGender == $key) {
                                        echo '<option value="' . strtolower($gender) . '" selected="selected">' . $gender . '</option>';
                                    } else {
                                        echo '<option value="' . strtolower($gender) . '">' . $gender . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="timezone" class="col-sm-2 control-label">Timezone</label>
                        <div class="col-sm-10">
                            <select name="timezone" class="form-control input_box" id="timezone">
                                <option value="">Select Timezone</option>
                                <?php
                                foreach ($zones as $tzName => $timezones) {
                                    echo '<optgroup label="' . $tzName . '">';
                                    foreach ($timezones as $timezone => $value) {
                                        $time = new DateTime(NULL, new DateTimeZone($timezone));
                                        $value = '(GMT' . $time->format('P') . ') ' . $timezones[$timezone] . ' (' . $timezone . ')';
                                        $value = str_replace('_', ' ', $value);
                                        if ($userTimezone == $timezone) {
                                            echo '<option value="' . $timezone . '" selected="selected">' . $value . '</option>';
                                        } else {
                                            echo '<option value="' . $timezone . '">' . $value . '</option>';
                                        }
                                    }
                                    echo '</optgroup>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="firstName" class="col-sm-2 control-label">First Name</label>
                        <div class="col-sm-10">
                            <input name="firstName" type="text" class="form-control input_box" id="firstName" placeholder="First Name" value="<?php echo $userFirstName; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="lastName" class="col-sm-2 control-label">Last Name</label>
                        <div class="col-sm-10">
                            <input name="lastName" type="text" class="form-control input_box" id="lastName" placeholder="Last Name" value="<?php echo $userLastName; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email" class="col-sm-2 control-label">Email</label>
                        <div class="col-sm-10">
                            <input name="email" type="email" class="form-control input_box" id="email" placeholder="Email" value="<?php echo $userEmail; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password" class="col-sm-2 control-label">Password</label>
                        <div class="col-sm-10">
                            <input name="password" type="password" class="form-control input_box" id="password" placeholder="Password" autocomplete="off">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button name="submit" value="update" type="submit" class="btn btn_blue">Submit</button>
                        </div>
                    </div>
                </form>
                <!-- end col-sm-10 -->
            </div>
        </div>
    </div>
</section>

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
<script type="text/javascript" src="js/jquery.storage.js"></script>
<script type="text/javascript" src="js/cropper-min.js"></script>
<script type="text/javascript" src="js/custom.js"></script>

<script>

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

    }); // end doc.ready

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
</script>
</body>
</html>