<?php

if (!isset($_SESSION['userId'])) {
    header('Location: index.php?page=login');
    exit;
}

header('Content-Type: application/json');

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

if (isset($_POST) && count($_POST) > 0) {

    $eventId = isset($_POST['eventId']) ? $_POST['eventId'] : '';
    $eventId = preg_replace("/[^0-9]/", "", $eventId);

    $eventSportNameLowercase = '';
    $eventRounds = 1;

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
          `Events`.`Sport`,
          `Events`.`Rounds`
        FROM 
          `Events` 
        WHERE
          `Events`.`EventId` = :EventId";

        $stmt = $PDO->prepare($sql);
        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $eventSport = getSportName($row['Sport']);
            $eventSportNameLowercase = strtolower(str_replace(' ', '', $eventSport));
            $eventAdminId = $row['UserId'];
            $eventRounds = $row['Rounds'];
            $count++;
        }

    } catch (PDOException $e) {
        trigger_error($e->getMessage(), E_USER_ERROR);
    }//end try

    if ($count == 0) {
        $msgBox = alertBox("Invalid Event", "<i class='fa fa-times'></i>", "danger");
        echo json_encode(array('error' => $msgBox));
        exit;
    }

    // check if logged-in user is an event admin for this event
    if (!in_array($userId, $eventAdminIds)) {
        $msgBox = alertBox("You do not have permission to edit this event", "<i class='fa fa-times'></i>", "danger");
        echo json_encode(array('error' => $msgBox));
        exit;
    }

    // input sanitation
    $playerGender = isset($_POST['playerGender']) ? trim($_POST['playerGender']) : 'male';
    $playerGender = filter_var($playerGender, FILTER_SANITIZE_STRING);

    if ($playerGender == 'male') {
        // male
        $playerGender = '0';
    } else {
        // female
        $playerGender = '1';
    }

    $playerTeamId = isset($_POST['playerTeamId']) ? trim($_POST['playerTeamId']) : '';
    $playerTeamId = filter_var($playerTeamId, FILTER_SANITIZE_STRING);

    if (empty($playerTeamId)) {
        $msgBox = alertBox("Team ID is required", "<i class='fa fa-times'></i>", "danger");
        echo json_encode(array('error' => $msgBox));
        exit;
    }

    $playerDesignation = isset($_POST['playerDesignation']) ? trim($_POST['playerDesignation']) : '0';
    $playerDesignation = filter_var($playerDesignation, FILTER_SANITIZE_STRING);

    $playerFirstName = isset($_POST['playerFirstName']) ? trim($_POST['playerFirstName']) : '';
    $playerFirstName = filter_var($playerFirstName, FILTER_SANITIZE_STRING);
    $playerFirstName = ucfirst(strtolower($playerFirstName));
    $playerFirstName = preg_replace('/\s+/', ' ', $playerFirstName);

    $playerMiddleName = isset($_POST['playerMiddleName']) ? trim($_POST['playerMiddleName']) : '';
    $playerMiddleName = filter_var($playerMiddleName, FILTER_SANITIZE_STRING);
    $playerMiddleName = ucfirst(strtolower($playerMiddleName));
    $playerMiddleName = preg_replace('/\s+/', ' ', $playerMiddleName);

    $playerLastName = isset($_POST['playerLastName']) ? trim($_POST['playerLastName']) : '';
    $playerLastName = filter_var($playerLastName, FILTER_SANITIZE_STRING);
    $playerLastName = ucfirst(strtolower($playerLastName));
    $playerLastName = preg_replace('/\s+/', ' ', $playerLastName);

    $playerFullName = trim($playerFirstName . ' ' . $playerMiddleName . ' ' . $playerLastName);
    $playerFullName = preg_replace('/\s+/', ' ', $playerFullName);

    $playerEmail = isset($_POST['playerEmail']) ? trim($_POST['playerEmail']) : '';
    $playerEmail = filter_var($playerEmail, FILTER_SANITIZE_EMAIL);
    $playerEmail = strtolower($playerEmail);

    $playerDOB = isset($_POST['playerDOB']) ? trim($_POST['playerDOB']) : '0000-00-00';
    $playerDOB = filter_var($playerDOB, FILTER_SANITIZE_STRING);

    $playerSchool = isset($_POST['playerSchool']) ? trim($_POST['playerSchool']) : '';
    $playerSchool = filter_var($playerSchool, FILTER_SANITIZE_STRING);
    $playerSchool = ucwords(strtolower($playerSchool));
    $playerSchool = preg_replace('/\s+/', ' ', $playerSchool);

    $playerGradYear = isset($_POST['playerGradYear']) ? trim($_POST['playerGradYear']) : '';
    $playerGradYear = filter_var($playerGradYear, FILTER_SANITIZE_STRING);

    $playerCity = isset($_POST['playerCity']) ? trim($_POST['playerCity']) : '';
    $playerCity = filter_var($playerCity, FILTER_SANITIZE_STRING);
    $playerCity = ucwords(strtolower($playerCity));

    $playerStateShort = isset($_POST['playerStateShort']) ? trim($_POST['playerStateShort']) : '';
    $playerStateShort = filter_var($playerStateShort, FILTER_SANITIZE_STRING);
    $playerStateShort = strtoupper($playerStateShort);

    if (!array_key_exists($playerStateShort, $states)) {
        $playerStateShort = '';
    }

    $playerStateLong = '';
    if (isset($states[$playerStateShort])) {
        $playerStateLong = $states[$playerStateShort];
    }

    if (empty($playerFirstName)) {
        $msgBox = alertBox("First Name is required", "<i class='fa fa-times'></i>", "danger");
        echo json_encode(array('error' => $msgBox));
        exit;
    }

    if (!empty($playerDOB)) {
        $_m = date('n', strtotime($playerDOB));
        $_d = date('j', strtotime($playerDOB));
        $_y = date('Y', strtotime($playerDOB));
        $playerDOB = date('Y-m-d', strtotime($playerDOB));
        if (!checkdate($_m, $_d, $_y) || $playerDOB == '1969-12-31') {
            $msgBox = alertBox("DOB is invalid.", "<i class='fa fa-times'></i>", "danger");
            echo json_encode(array('error' => $msgBox));
            exit;
        }
    }

    $requestReason = getDesignation($playerDesignation, $playerFirstName);

    if (!empty($playerEmail)) {
        if (!filter_var($playerEmail, FILTER_VALIDATE_EMAIL)) {
            $msgBox = alertBox("Enter a valid email address", "<i class='fa fa-times'></i>", "danger");
            echo json_encode(array('error' => $msgBox));
            exit;
        }
    }

    $entryDate = date('Y-m-d');
    $playerCreated = date('Y-m-d H:i:s');
    $playerLastUpdated = date('Y-m-d H:i:s');
    $playerToken = sha1($userIp . microseconds());
    $playerIsActive = '1';
    $certified = '1'; // 0=un-certified, 1=certified

    // create player
    try {
        $sql = "
        INSERT INTO `Players` 
        (
          `PlayerId`, 
          `Created`, 
          `LastUpdated`, 
          `UserId`, 
          `EventId`,
          `TeamId`,
          `Gender`, 
          `FirstName`, 
          `MiddleName`, 
          `LastName`, 
          `Designation`, 
          `Email`, 
          `DOB`, 
          `School`, 
          `GradYear`, 
          `City`, 
          `StateShort`, 
          `StateLong`, 
          `Token`, 
          `IsActive`
        ) VALUES (
          NULL, 
          :Created, 
          :LastUpdated, 
          :UserId,  
          :EventId, 
          :TeamId,
          :Gender, 
          :FirstName, 
          :MiddleName, 
          :LastName, 
          :Designation, 
          :Email, 
          :DOB, 
          :School, 
          :GradYear, 
          :City, 
          :StateShort, 
          :StateLong, 
          :Token, 
          :IsActive
        )";

        $stmt = $PDO->prepare($sql);
        $stmt->bindParam('Created', $playerCreated, PDO::PARAM_STR);
        $stmt->bindParam('LastUpdated', $playerLastUpdated, PDO::PARAM_STR);
        $stmt->bindParam('UserId', $eventAdminId, PDO::PARAM_INT);
        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
        $stmt->bindParam('TeamId', $playerTeamId, PDO::PARAM_STR);
        $stmt->bindParam('Gender', $playerGender, PDO::PARAM_INT);
        $stmt->bindParam('FirstName', $playerFirstName, PDO::PARAM_STR);
        $stmt->bindParam('MiddleName', $playerMiddleName, PDO::PARAM_STR);
        $stmt->bindParam('LastName', $playerLastName, PDO::PARAM_STR);
        $stmt->bindParam('Designation', $playerDesignation, PDO::PARAM_STR);
        $stmt->bindParam('Email', $playerEmail, PDO::PARAM_STR);
        $stmt->bindParam('DOB', $playerDOB, PDO::PARAM_STR);
        $stmt->bindParam('School', $playerSchool, PDO::PARAM_STR);
        $stmt->bindParam('GradYear', $playerGradYear, PDO::PARAM_STR);
        $stmt->bindParam('City', $playerCity, PDO::PARAM_STR);
        $stmt->bindParam('StateShort', $playerStateShort, PDO::PARAM_STR);
        $stmt->bindParam('StateLong', $playerStateLong, PDO::PARAM_STR);
        $stmt->bindParam('Token', $playerToken, PDO::PARAM_STR);
        $stmt->bindParam('IsActive', $playerIsActive, PDO::PARAM_INT);
        $stmt->execute();
    } catch (PDOException $e) {
        trigger_error($e->getMessage(), E_USER_ERROR);
    }//end try

    // get PlayerId
    $playerId = null;
    try {
        $sql = "
        SELECT 
          `Players`.`PlayerId` 
        FROM 
          `Players` 
        WHERE
          `Players`.`UserId` = :UserId
        AND
          `Players`.`Token` = :Token
          ";

        $stmt = $PDO->prepare($sql);
        $stmt->bindParam('UserId', $eventAdminId, PDO::PARAM_INT);
        $stmt->bindParam('Token', $playerToken, PDO::PARAM_STR);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $playerId = $row['PlayerId'];
        }

    } catch (PDOException $e) {
        trigger_error($e->getMessage(), E_USER_ERROR);
    }//end try

    // make me playerAdmin
    $isAdmin = '1';
    $playerAdminToken = sha1($userIp . microseconds());
    try {
        $sql = "
        INSERT INTO `PlayerAdmins` 
        (
          `PlayerAdminId`, 
          `Created`, 
          `UserId`, 
          `UserIdFrom`, 
          `PlayerId`, 
          `IsAdmin`, 
          `Token`
        ) VALUES (
          NULL, 
          :Created, 
          :UserId, 
          :UserIdFrom, 
          :PlayerId,
          :IsAdmin, 
          :Token
        )";

        $stmt = $PDO->prepare($sql);
        $stmt->bindParam('Created', $playerCreated, PDO::PARAM_STR);
        $stmt->bindParam('UserId', $eventAdminId, PDO::PARAM_INT);
        $stmt->bindParam('UserIdFrom', $eventAdminId, PDO::PARAM_INT);
        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
        $stmt->bindParam('IsAdmin', $isAdmin, PDO::PARAM_INT);
        $stmt->bindParam('Token', $playerAdminToken, PDO::PARAM_STR);
        $stmt->execute();
    } catch (PDOException $e) {
        trigger_error($e->getMessage(), E_USER_ERROR);
    }//end try

    // get the PlayerAdminId
    $playerAdminId = null;
    try {
        $sql = "
        SELECT 
          `PlayerAdmins`.`PlayerAdminId`
        FROM 
          `PlayerAdmins` 
        WHERE
          `PlayerAdmins`.`UserId` = :UserId
        AND
          `PlayerAdmins`.`Token` = :Token";

        $stmt = $PDO->prepare($sql);
        $stmt->bindParam('UserId', $eventAdminId, PDO::PARAM_INT);
        $stmt->bindParam('Token', $playerAdminToken, PDO::PARAM_STR);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $playerAdminId = $row['PlayerAdminId'];
        }

    } catch (PDOException $e) {
        trigger_error($e->getMessage(), E_USER_ERROR);
    }//end try

    // insert wall PlayerAdminId - this indicates a new player admin
    $wallToken = sha1($userIp . microseconds());
    try {
        $sql = "
        INSERT INTO `Wall` 
        (
          `WallId`, 
          `Created`, 
          `UserId`, 
          `UserIdFrom`, 
          `PlayerId`,
          `PlayerAdminId`,
          `Token`
        ) VALUES (
          NULL, 
          :Created, 
          :UserId, 
          :UserIdFrom, 
          :PlayerId,
          :PlayerAdminId,
          :Token
        )";

        $stmt = $PDO->prepare($sql);
        $stmt->bindParam('Created', $playerCreated, PDO::PARAM_STR); // Creation date
        $stmt->bindParam('UserId', $eventAdminId, PDO::PARAM_INT); // Owner of player
        $stmt->bindParam('UserIdFrom', $eventAdminId, PDO::PARAM_INT); // Owner of player
        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT); // The player card
        $stmt->bindParam('PlayerAdminId', $playerAdminId, PDO::PARAM_INT);
        $stmt->bindParam('Token', $wallToken, PDO::PARAM_STR);
        $stmt->execute();

    } catch (PDOException $e) {
        trigger_error($e->getMessage(), E_USER_ERROR);
    }//end try

    // insert wall PlayerId -  this insert indicates a player was created
    $wallToken = sha1($userIp . microseconds());
    try {
        $sql = "
        INSERT INTO `Wall` 
        (
          `WallId`, 
          `Created`, 
          `UserId`, 
          `UserIdFrom`, 
          `PlayerId`,
          `Token`
        ) VALUES (
          NULL, 
          :Created, 
          :UserId, 
          :UserIdFrom, 
          :PlayerId,
          :Token
        )";

        $stmt = $PDO->prepare($sql);
        $stmt->bindParam('Created', $playerCreated, PDO::PARAM_STR); // Creation date
        $stmt->bindParam('UserId', $eventAdminId, PDO::PARAM_INT); // Owner of player
        $stmt->bindParam('UserIdFrom', $eventAdminId, PDO::PARAM_INT); // Owner of player
        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT); // The player card
        $stmt->bindParam('Token', $wallToken, PDO::PARAM_STR);
        $stmt->execute();

    } catch (PDOException $e) {
        trigger_error($e->getMessage(), E_USER_ERROR);
    }//end try

    // "follow" my own player
    $userDesignation = '0'; // I am
    $followToken = sha1($userIp . microseconds());
    try {
        $sql = "
        INSERT INTO `Follows` 
        (
          `FollowId`, 
          `Created`, 
          `UserId`, 
          `UserIdFrom`, 
          `Designation`,
          `PlayerId`,
          `Token`
        ) VALUES (
          NULL, 
          :Created, 
          :UserId, 
          :UserIdFrom, 
          :Designation,
          :PlayerId,
          :Token
        )";

        $stmt = $PDO->prepare($sql);
        $stmt->bindParam('Created', $playerCreated, PDO::PARAM_STR); // Creation date
        $stmt->bindParam('UserId', $eventAdminId, PDO::PARAM_INT); // Owner of player
        $stmt->bindParam('UserIdFrom', $eventAdminId, PDO::PARAM_INT); // Owner of player
        $stmt->bindParam('Designation', $userDesignation, PDO::PARAM_INT); // Player designation
        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT); // The player card
        $stmt->bindParam('Token', $followToken, PDO::PARAM_STR);
        $stmt->execute();

    } catch (PDOException $e) {
        trigger_error($e->getMessage(), E_USER_ERROR);
    }//end try

    // get the FollowId
    $followId = null;
    try {
        $sql = "
        SELECT 
          `Follows`.`FollowId`
        FROM 
          `Follows` 
        WHERE
          `Follows`.`UserId` = :UserId
        AND
          `Follows`.`Token` = :Token";

        $stmt = $PDO->prepare($sql);
        $stmt->bindParam('UserId', $eventAdminId, PDO::PARAM_INT);
        $stmt->bindParam('Token', $followToken, PDO::PARAM_STR);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $followId = $row['FollowId'];
        }

    } catch (PDOException $e) {
        trigger_error($e->getMessage(), E_USER_ERROR);
    }//end try

    // insert wall FollowId
    $wallToken = sha1($userIp . microseconds());
    try {
        $sql = "
        INSERT INTO `Wall` 
        (
          `WallId`, 
          `Created`, 
          `UserId`, 
          `UserIdFrom`, 
          `PlayerId`,
          `FollowId`,
          `Token`
        ) VALUES (
          NULL, 
          :Created, 
          :UserId, 
          :UserIdFrom, 
          :PlayerId,
          :FollowId,
          :Token
        )";

        $stmt = $PDO->prepare($sql);
        $stmt->bindParam('Created', $playerCreated, PDO::PARAM_STR); // Creation date
        $stmt->bindParam('UserId', $eventAdminId, PDO::PARAM_INT); // Owner of player
        $stmt->bindParam('UserIdFrom', $eventAdminId, PDO::PARAM_INT); // Owner of player
        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT); // The player card
        $stmt->bindParam('FollowId', $followId, PDO::PARAM_INT);
        $stmt->bindParam('Token', $wallToken, PDO::PARAM_STR);
        $stmt->execute();

    } catch (PDOException $e) {
        trigger_error($e->getMessage(), E_USER_ERROR);
    }//end try

    if (!empty($playerEmail)) {

        // Must login with SMTP to remove hourly sending limit
        require 'includes/phpmailer-5.2.22/PHPMailerAutoload.php';

        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = "retail.smtp.com"; // mail.playerfax.com
        $mail->Port = 25025; // 26
        $mail->SMTPAuth = true;
        //Username to use for SMTP authentication
        $mail->Username = "support@playerfax.com";
        //Password to use for SMTP authentication
        $mail->Password = "t4<oI]6O~5-GNk]:]P-D";
        $mail->setFrom('support@playerfax.com', 'Playerfax'); // must be from this domain
        $mail->AddAddress($playerEmail, $playerFullName);  // Send to registrant
        $mail->AddAddress('support@playerfax.com', 'Playerfax');  // Send to admin
        $mail->Subject = 'Playerfax Invitation';

        // HTML version
        $htmlBody = file_get_contents('includes/email/email.playerInvitation.html');
        $htmlBody = str_replace('%ADMIN_NAME%', $userFullName, $htmlBody);
        $htmlBody = str_replace('%NAME%', $playerFullName, $htmlBody);
        $htmlBody = str_replace('%EMAIL%', $playerEmail, $htmlBody);
        $htmlBody = str_replace('%CONFIRM_TOKEN%', $playerToken, $htmlBody);

        $mail->Body = $htmlBody;

        // plain text version
        $plainBody = file_get_contents('includes/email/email.playerInvitation.txt');
        $plainBody = str_replace('%ADMIN_NAME%', $userFullName, $plainBody);
        $plainBody = str_replace('%NAME%', $playerFullName, $plainBody);
        $plainBody = str_replace('%EMAIL%', $playerEmail, $plainBody);
        $plainBody = str_replace('%CONFIRM_TOKEN%', $playerToken, $plainBody);

        $mail->AltBody = $plainBody;

        $mail->send();
    }

    // Baseball
    if ($eventSportNameLowercase === 'baseball') {

        $baseball_PrimaryPosition = isset($_POST['baseball_PrimaryPosition']) ? preg_replace("/[^0-9]/", "", $_POST['baseball_PrimaryPosition']) : '0'; // Position Number
        $baseball_SecondaryPosition = isset($_POST['baseball_SecondaryPosition']) ? preg_replace("/[^0-9]/", "", $_POST['baseball_SecondaryPosition']) : '0'; // Position Number

        // insert empty player stats for each round of this event
        for ($i = 1; $i <= $eventRounds; $i++) {

            try {
                $sql = "
                INSERT INTO `GameBaseball` 
                (
                  `BaseballId`, 
                  `Created`, 
                  `LastUpdated`, 
                  `EntryDate`,
                  `UserId`, 
                  `EventId`,
                  `Round`,
                  `PlayerId`, 
                  `Certified`,  
                  `PrimaryPosition`, 
                  `SecondaryPosition`
                ) VALUES (
                  NULL, 
                  :Created, 
                  :LastUpdated, 
                  :EntryDate,
                  :UserId, 
                  :EventId,
                  :Round,
                  :PlayerId,
                  :Certified,
                  :PrimaryPosition, 
                  :SecondaryPosition
                )";

                $stmt = $PDO->prepare($sql);
                $stmt->bindParam('Created', $playerCreated, PDO::PARAM_STR);
                $stmt->bindParam('LastUpdated', $playerLastUpdated, PDO::PARAM_STR);
                $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                $stmt->bindParam('UserId', $eventAdminId, PDO::PARAM_INT);
                $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                $stmt->bindParam('Certified', $certified, PDO::PARAM_INT);
                $stmt->bindParam('PrimaryPosition', $baseball_PrimaryPosition, PDO::PARAM_INT);
                $stmt->bindParam('SecondaryPosition', $baseball_SecondaryPosition, PDO::PARAM_INT);
                $stmt->execute();

            } catch (PDOException $e) {
                trigger_error($e->getMessage(), E_USER_ERROR);
            }//end try

        } // end for

    } // end Baseball

    // Fast Pitch
    if ($eventSportNameLowercase === 'fastpitch') {

        $fastPitch_PrimaryPosition = isset($_POST['fastPitch_PrimaryPosition']) ? preg_replace("/[^0-9]/", "", $_POST['fastPitch_PrimaryPosition']) : '0'; // Position Number
        $fastPitch_SecondaryPosition = isset($_POST['fastPitch_SecondaryPosition']) ? preg_replace("/[^0-9]/", "", $_POST['fastPitch_SecondaryPosition']) : '0'; // Position Number

        // insert empty player stats for each round of this event
        for ($i = 1; $i <= $eventRounds; $i++) {

            try {
                $sql = "
                INSERT INTO `GameFastPitch` 
                (
                  `FastPitchId`, 
                  `Created`, 
                  `LastUpdated`, 
                  `EntryDate`,
                  `UserId`, 
                  `EventId`,
                  `Round`,
                  `PlayerId`, 
                  `Certified`,  
                  `PrimaryPosition`, 
                  `SecondaryPosition`
                ) VALUES (
                  NULL, 
                  :Created, 
                  :LastUpdated, 
                  :EntryDate,
                  :UserId, 
                  :EventId,
                  :Round,
                  :PlayerId,
                  :Certified,
                  :PrimaryPosition, 
                  :SecondaryPosition
                )";

                $stmt = $PDO->prepare($sql);
                $stmt->bindParam('Created', $playerCreated, PDO::PARAM_STR);
                $stmt->bindParam('LastUpdated', $playerLastUpdated, PDO::PARAM_STR);
                $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                $stmt->bindParam('UserId', $eventAdminId, PDO::PARAM_INT);
                $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                $stmt->bindParam('Certified', $certified, PDO::PARAM_INT);
                $stmt->bindParam('PrimaryPosition', $fastPitch_PrimaryPosition, PDO::PARAM_INT);
                $stmt->bindParam('SecondaryPosition', $fastPitch_SecondaryPosition, PDO::PARAM_INT);
                $stmt->execute();

            } catch (PDOException $e) {
                trigger_error($e->getMessage(), E_USER_ERROR);
            }//end try

        } // end for

    } // end Fast Pitch

    // Lacrosse
    if ($eventSportNameLowercase === 'lacrosse') {

        // insert empty player stats for each round of this event
        for ($i = 1; $i <= $eventRounds; $i++) {

            try {
                $sql = "
                INSERT INTO `GameLacrosse` 
                (
                  `LacrosseId`, 
                  `Created`, 
                  `LastUpdated`, 
                  `EntryDate`,
                  `UserId`, 
                  `EventId`,
                  `Round`,
                  `PlayerId`, 
                  `Certified`
                ) VALUES (
                  NULL, 
                  :Created, 
                  :LastUpdated, 
                  :EntryDate,
                  :UserId, 
                  :EventId,
                  :Round,
                  :PlayerId,
                  :Certified
                )";

                $stmt = $PDO->prepare($sql);
                $stmt->bindParam('Created', $playerCreated, PDO::PARAM_STR);
                $stmt->bindParam('LastUpdated', $playerLastUpdated, PDO::PARAM_STR);
                $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                $stmt->bindParam('UserId', $eventAdminId, PDO::PARAM_INT);
                $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                $stmt->bindParam('Certified', $certified, PDO::PARAM_INT);
                $stmt->execute();

            } catch (PDOException $e) {
                trigger_error($e->getMessage(), E_USER_ERROR);
            }//end try

        } // end for

    } // end Lacrosse

    // Cross Fit
    if ($eventSportNameLowercase === 'crossfit') {

        // insert empty player stats for each round of this event
        for ($i = 1; $i <= $eventRounds; $i++) {

            try {
                $sql = "
                INSERT INTO `GameCrossFit` 
                (
                  `CrossFitId`, 
                  `Created`, 
                  `LastUpdated`, 
                  `EntryDate`,
                  `UserId`, 
                  `EventId`,
                  `Round`,
                  `PlayerId`, 
                  `Certified`
                ) VALUES (
                  NULL, 
                  :Created, 
                  :LastUpdated, 
                  :EntryDate,
                  :UserId, 
                  :EventId,
                  :Round,
                  :PlayerId,
                  :Certified
                )";

                $stmt = $PDO->prepare($sql);
                $stmt->bindParam('Created', $playerCreated, PDO::PARAM_STR);
                $stmt->bindParam('LastUpdated', $playerLastUpdated, PDO::PARAM_STR);
                $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                $stmt->bindParam('UserId', $eventAdminId, PDO::PARAM_INT);
                $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                $stmt->bindParam('Certified', $certified, PDO::PARAM_INT);
                $stmt->execute();

            } catch (PDOException $e) {
                trigger_error($e->getMessage(), E_USER_ERROR);
            }//end try

        } // end for

    } // end Cross Fit

    // Swimming
    if ($eventSportNameLowercase === 'swimming') {

        // insert empty player stats for each round of this event
        for ($i = 1; $i <= $eventRounds; $i++) {

            try {
                $sql = "
                INSERT INTO `GameSwimming` 
                (
                  `SwimmingId`, 
                  `Created`, 
                  `LastUpdated`, 
                  `EntryDate`,
                  `UserId`, 
                  `EventId`,
                  `Round`,
                  `PlayerId`, 
                  `Certified`
                ) VALUES (
                  NULL, 
                  :Created, 
                  :LastUpdated, 
                  :EntryDate,
                  :UserId, 
                  :EventId,
                  :Round,
                  :PlayerId,
                  :Certified
                )";

                $stmt = $PDO->prepare($sql);
                $stmt->bindParam('Created', $playerCreated, PDO::PARAM_STR);
                $stmt->bindParam('LastUpdated', $playerLastUpdated, PDO::PARAM_STR);
                $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                $stmt->bindParam('UserId', $eventAdminId, PDO::PARAM_INT);
                $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                $stmt->bindParam('Certified', $certified, PDO::PARAM_INT);
                $stmt->execute();

            } catch (PDOException $e) {
                trigger_error($e->getMessage(), E_USER_ERROR);
            }//end try

        } // end for

    } // end Swimming

    if (!empty($playerFullName)) {
        $msgBox = alertBox("Player added ($playerFullName)", "<i class='fa fa-check-square-o'></i>", "success");
    } else {
        $msgBox = alertBox("Player added", "<i class='fa fa-check-square-o'></i>", "success");
    }

    echo json_encode(array('success' => $msgBox));

}

