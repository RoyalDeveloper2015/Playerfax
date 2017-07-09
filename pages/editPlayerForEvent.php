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

    $playerToken = isset($_POST['token']) ? trim($_POST['token']) : '';
    $playerToken = filter_var($playerToken, FILTER_SANITIZE_STRING);

    $playerId = null;

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

    $count = 0;
    try {
        $sql = "
        SELECT
          `Players`.`PlayerId`
        FROM 
          `Players`
        WHERE
          `Players`.`Token` = :Token
        AND 
          `Players`.`IsActive` = 1
        AND 
          `Players`.`EventId` = :EventId";

        $stmt = $PDO->prepare($sql);
        $stmt->bindParam('Token', $playerToken, PDO::PARAM_STR);
        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $playerId = $row['PlayerId'];
            $count++;
        }

    } catch (PDOException $e) {
        trigger_error($e->getMessage(), E_USER_ERROR);
    }//end try

    // check if player is valid
    if ($count == 0) {
        $msgBox = alertBox("Invalid Player", "<i class='fa fa-times'></i>", "danger");
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

    $playerLastUpdated = date('Y-m-d H:i:s');
    $playerIsActive = '1';

    // UPDATE PLAYER
    try {
        $sql = "
            UPDATE 
              `Players` 
            SET
              `Players`.`LastUpdated` = :LastUpdated, 
              `Players`.`TeamId` = :TeamId, 
              `Players`.`Gender` = :Gender,
              `Players`.`FirstName` = :FirstName,
              `Players`.`MiddleName` = :MiddleName,
              `Players`.`LastName` = :LastName,
              `Players`.`Email` = :Email, 
              `Players`.`DOB` = :DOB, 
              `Players`.`GradYear` = :GradYear, 
              `Players`.`City` = :City,
              `Players`.`StateShort` = :StateShort,
              `Players`.`StateLong` = :StateLong
            WHERE
              `Players`.`PlayerId` = :PlayerId
            AND 
              `Players`.`EventId` = :EventId";

        $stmt = $PDO->prepare($sql);
        $stmt->bindParam('LastUpdated', $playerLastUpdated, PDO::PARAM_STR);
        $stmt->bindParam('TeamId', $playerTeamId, PDO::PARAM_STR);
        $stmt->bindParam('Gender', $playerGender, PDO::PARAM_INT);
        $stmt->bindParam('FirstName', $playerFirstName, PDO::PARAM_STR);
        $stmt->bindParam('MiddleName', $playerMiddleName, PDO::PARAM_STR);
        $stmt->bindParam('LastName', $playerLastName, PDO::PARAM_STR);
        $stmt->bindParam('Email', $playerEmail, PDO::PARAM_STR);
        $stmt->bindParam('DOB', $playerDOB, PDO::PARAM_STR);
        $stmt->bindParam('GradYear', $playerGradYear, PDO::PARAM_STR);
        $stmt->bindParam('City', $playerCity, PDO::PARAM_STR);
        $stmt->bindParam('StateShort', $playerStateShort, PDO::PARAM_STR);
        $stmt->bindParam('StateLong', $playerStateLong, PDO::PARAM_STR);
        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
        $stmt->execute();
    } catch (PDOException $e) {
        trigger_error($e->getMessage(), E_USER_ERROR);
    }//end try

    // Baseball
    if ($eventSportNameLowercase === 'baseball') {

        $baseball_PrimaryPosition = isset($_POST['baseball_PrimaryPosition']) ? preg_replace("/[^0-9]/", "", $_POST['baseball_PrimaryPosition']) : '0'; // Position Number
        $baseball_SecondaryPosition = isset($_POST['baseball_SecondaryPosition']) ? preg_replace("/[^0-9]/", "", $_POST['baseball_SecondaryPosition']) : '0'; // Position Number

        // insert empty player stats for each round of this event
        for ($i = 1; $i <= $eventRounds; $i++) {

            try {
                $sql = "
                UPDATE 
                  `GameBaseball` 
                SET
                  `GameBaseball`.`LastUpdated` = :LastUpdated, 
                  `GameBaseball`.`PrimaryPosition` = :PrimaryPosition, 
                  `GameBaseball`.`SecondaryPosition` = :SecondaryPosition
                WHERE
                  `GameBaseball`.`PlayerId` = :PlayerId
                AND 
                  `GameBaseball`.`EventId` = :EventId";

                $stmt = $PDO->prepare($sql);
                $stmt->bindParam('LastUpdated', $playerLastUpdated, PDO::PARAM_STR);
                $stmt->bindParam('PrimaryPosition', $baseball_PrimaryPosition, PDO::PARAM_INT);
                $stmt->bindParam('SecondaryPosition', $baseball_SecondaryPosition, PDO::PARAM_INT);
                $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
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
                UPDATE 
                  `GameFastPitch` 
                SET
                  `GameFastPitch`.`LastUpdated` = :LastUpdated, 
                  `GameFastPitch`.`PrimaryPosition` = :PrimaryPosition, 
                  `GameFastPitch`.`SecondaryPosition` = :SecondaryPosition
                WHERE
                  `GameFastPitch`.`PlayerId` = :PlayerId
                AND 
                  `GameFastPitch`.`EventId` = :EventId";

                $stmt = $PDO->prepare($sql);
                $stmt->bindParam('LastUpdated', $playerLastUpdated, PDO::PARAM_STR);
                $stmt->bindParam('PrimaryPosition', $fastPitch_PrimaryPosition, PDO::PARAM_INT);
                $stmt->bindParam('SecondaryPosition', $fastPitch_SecondaryPosition, PDO::PARAM_INT);
                $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
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
                UPDATE 
                  `GameLacrosse` 
                SET
                  `GameLacrosse`.`LastUpdated` = :LastUpdated
                WHERE
                  `GameLacrosse`.`PlayerId` = :PlayerId
                AND 
                  `GameLacrosse`.`EventId` = :EventId";

                $stmt = $PDO->prepare($sql);
                $stmt->bindParam('LastUpdated', $playerLastUpdated, PDO::PARAM_STR);
                $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
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
                UPDATE 
                  `GameCrossFit` 
                SET
                  `GameCrossFit`.`LastUpdated` = :LastUpdated
                WHERE
                  `GameCrossFit`.`PlayerId` = :PlayerId
                AND 
                  `GameCrossFit`.`EventId` = :EventId";

                $stmt = $PDO->prepare($sql);
                $stmt->bindParam('LastUpdated', $playerLastUpdated, PDO::PARAM_STR);
                $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
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
                UPDATE 
                  `GameSwimming` 
                SET
                  `GameSwimming`.`LastUpdated` = :LastUpdated
                WHERE
                  `GameSwimming`.`PlayerId` = :PlayerId
                AND 
                  `GameSwimming`.`EventId` = :EventId";

                $stmt = $PDO->prepare($sql);
                $stmt->bindParam('LastUpdated', $playerLastUpdated, PDO::PARAM_STR);
                $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                $stmt->execute();
            } catch (PDOException $e) {
                trigger_error($e->getMessage(), E_USER_ERROR);
            }//end try

        } // end for

    } // end Swimming

    if (!empty($playerFullName)) {
        $msgBox = alertBox("Player updated ($playerFullName)", "<i class='fa fa-check-square-o'></i>", "success");
    } else {
        $msgBox = alertBox("Player updated", "<i class='fa fa-check-square-o'></i>", "success");
    }

    echo json_encode(array('success' => $msgBox));

}

