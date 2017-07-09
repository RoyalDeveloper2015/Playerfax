<?php

// SELECT CONVERT_TZ('2004-01-01 12:00:00','GMT','MET')

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

$eventId = null;
$eventName = '';
$eventSport = '';
$eventStartDate = '';
$_eventStartDate = '';
$eventRounds = '3';
$eventAddress = '';
$eventCity = '';
$eventStateShort = '';
$eventStateLong = '';
$eventZip = '';
$eventDescription = '';
$eventCoordinator = '';
$eventPhone = '';
$eventEmail = '';
$eventWebsite = '';
$eventCalendar = 'yes';
$eventToken = sha1($userIp . microseconds());
$results = new stdClass();
$eventCreated = date('Y-m-d H:i:s');

if (isset($_POST) && count($_POST) > 0) {

    // STEP 1
    $eventName = isset($_POST['step1']['eventName']) ? trim($_POST['step1']['eventName']) : '';
    $eventName = filter_var($eventName, FILTER_SANITIZE_STRING);
    $eventName = preg_replace('/\s+/', ' ', $eventName);

    $eventSport = isset($_POST['step1']['eventSport']) ? trim($_POST['step1']['eventSport']) : '';
    $eventSport = filter_var($eventSport, FILTER_SANITIZE_STRING);
    $eventSport = getSportNumber($eventSport);

    $eventStartDate = isset($_POST['step1']['eventStartDate']) ? trim($_POST['step1']['eventStartDate']) : '';
    $eventStartDate = filter_var($eventStartDate, FILTER_SANITIZE_STRING);

    $eventEndDate = isset($_POST['step1']['eventEndDate']) ? trim($_POST['step1']['eventEndDate']) : '';
    $eventEndDate = filter_var($eventEndDate, FILTER_SANITIZE_STRING);

    $eventRounds = isset($_POST['step1']['eventRounds']) ? trim($_POST['step1']['eventRounds']) : '';
    $eventRounds = preg_replace("/[^0-9]/", "", $eventRounds);

    $eventAddress = isset($_POST['step1']['eventAddress']) ? trim($_POST['step1']['eventAddress']) : '';
    $eventAddress = filter_var($eventAddress, FILTER_SANITIZE_STRING);

    $eventCity = isset($_POST['step1']['eventCity']) ? trim($_POST['step1']['eventCity']) : '';
    $eventCity = filter_var($eventCity, FILTER_SANITIZE_STRING);

    $eventStateShort = isset($_POST['step1']['eventStateShort']) ? trim($_POST['step1']['eventStateShort']) : '';
    $eventStateShort = filter_var($eventStateShort, FILTER_SANITIZE_STRING);

    if (!array_key_exists($eventStateShort, $states)) {
        $eventStateShort = '';
    }
    $eventStateLong = '';
    if (isset($states[$eventStateShort])) {
        $eventStateLong = $states[$eventStateShort];
    }

    $eventZip = isset($_POST['step1']['eventZip']) ? trim($_POST['step1']['eventZip']) : '';
    $eventZip = filter_var($eventZip, FILTER_SANITIZE_STRING);

    $eventDescription = isset($_POST['step1']['eventDescription']) ? trim($_POST['step1']['eventDescription']) : '';
    $eventDescription = filter_var($eventDescription, FILTER_SANITIZE_STRING);

    $eventCoordinator = isset($_POST['step1']['eventCoordinator']) ? trim($_POST['step1']['eventCoordinator']) : '';
    $eventCoordinator = filter_var($eventCoordinator, FILTER_SANITIZE_STRING);

    $eventPhone = isset($_POST['step1']['eventPhone']) ? trim($_POST['step1']['eventPhone']) : '';
    $eventPhone = filter_var($eventPhone, FILTER_SANITIZE_STRING);

    $eventEmail = isset($_POST['step1']['eventEmail']) ? trim($_POST['step1']['eventEmail']) : '';
    $eventEmail = filter_var($eventEmail, FILTER_SANITIZE_EMAIL);

    $eventWebsite = isset($_POST['step1']['eventWebsite']) ? trim($_POST['step1']['eventWebsite']) : '';
    $eventWebsite = filter_var($eventWebsite, FILTER_SANITIZE_STRING);

    if (!empty($eventWebsite)) {
        // This uses a negative lookahead at the beginning of the string that looks for http:// or https://
        $eventWebsite = preg_replace('/^(?!https?:\/\/)/', 'http://', $eventWebsite);
    }

    $eventCalendar = isset($_POST['step1']['eventCalendar']) ? trim($_POST['step1']['eventCalendar']) : 'yes';
    $eventCalendar = filter_var($eventCalendar, FILTER_SANITIZE_STRING);

    if ($eventCalendar == 'yes') {
        $eventCalendar = '1';
    } else {
        $eventCalendar = '0';
    }

    // validate step 1 user input
    if (empty($eventName)) {
        $msgBox = alertBox("Step 1 - Event Name is required.", "<i class='fa fa-times'></i>", "danger");
        echo json_encode(array('error' => $msgBox));
        exit;
    }

    if (empty($eventSport)) {
        $msgBox = alertBox("Step 1 - Event Sport is required.", "<i class='fa fa-times'></i>", "danger");
        echo json_encode(array('error' => $msgBox));
        exit;
    }

    if (empty($eventStartDate)) {
        $msgBox = alertBox("Step 1 - Event Start Date is required.", "<i class='fa fa-times'></i>", "danger");
        echo json_encode(array('error' => $msgBox));
        exit;
    }

    if (empty($eventEndDate)) {
        $msgBox = alertBox("Step 1 - Event End Date is required.", "<i class='fa fa-times'></i>", "danger");
        echo json_encode(array('error' => $msgBox));
        exit;
    }

    if (!empty($eventStartDate)) {
        $eventStartDate = date('Y-m-d H:i:s', strtotime($eventStartDate));
        $_eventStartDate = date('M jS, Y g:i a', strtotime($eventStartDate));
        if (!validateDate($eventStartDate)) {
            $msgBox = alertBox("Step 1 - Start Date is invalid.", "<i class='fa fa-times'></i>", "danger");
            echo json_encode(array('error' => $msgBox));
            exit;
        }

        // convert time if user timezone exists
        // this way we can quickly select rows
        // un-convert only for display purposes
        //if (!empty($userTimezone)) {
        //    $tz = new DateTimeZone($userTimezone);
        //    $date = new DateTime($eventStartDate);
        //    $date->setTimezone($tz);
        //    $eventStartDate = $date->format('Y-m-d H:i:s');
        //}
    }

    if (!empty($eventEndDate)) {
        $eventEndDate = date('Y-m-d H:i:s', strtotime($eventEndDate));
        $_eventEndDate = date('M jS, Y g:i a', strtotime($eventEndDate));
        if (!validateDate($eventEndDate)) {
            $msgBox = alertBox("Step 1 - End Date is invalid.", "<i class='fa fa-times'></i>", "danger");
            echo json_encode(array('error' => $msgBox));
            exit;
        }
    }

    // STEP 2 (optional)
    $admins = array();
    // Add the owner of this event as an event admin
    array_push($admins, $userId);
    if (isset($_POST['step2']['eventAdminId'])) {

        foreach ($_POST['step2']['eventAdminId'] AS $eventAdminId) {

            $eventAdminId = preg_replace("/[^0-9]/", "", $eventAdminId);

            // make sure these admins are valid and active
            try {
                $sql = "
                SELECT 
                  `Users`.`UserId` 
                FROM 
                  `Users` 
                USE INDEX (`PRIMARY`)
                WHERE
                  `Users`.`UserId` = :UserId
                AND
                  `Users`.`IsActive` = 1";

                $stmt = $PDO->prepare($sql);
                $stmt->bindParam('UserId', $eventAdminId, PDO::PARAM_INT);
                $stmt->execute();

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    // make sure there are no duplicate admins
                    if (!in_array($eventAdminId, $admins)) {
                        array_push($admins, $eventAdminId);
                    }
                }

            } catch (PDOException $e) {
                trigger_error($e->getMessage(), E_USER_ERROR);
            }//end try
        }
    }

    // insert event into database
    try {
        $sql = "
            INSERT INTO `Events` 
            (
              `EventId`, 
              `Created`, 
              `LastUpdated`, 
              `UserId`, 
              `StartDate`, 
              `EndDate`, 
              `Sport`,
              `Rounds`, 
              `Description`, 
              `Name`, 
              `Address`, 
              `City`, 
              `StateShort`, 
              `StateLong`, 
              `Zip`, 
              `Coordinator`, 
              `Phone`, 
              `Email`, 
              `Website`, 
              `Calendar`,
              `Token`
            ) VALUES (
              NULL, 
              :Created, 
              :LastUpdated, 
              :UserId,
              :StartDate, 
              :EndDate,
              :Sport,
              :Rounds, 
              :Description, 
              :Name, 
              :Address, 
              :City, 
              :StateShort, 
              :StateLong, 
              :Zip, 
              :Coordinator, 
              :Phone, 
              :Email, 
              :Website, 
              :Calendar,
              :Token
            )";

        $stmt = $PDO->prepare($sql);
        $stmt->bindParam('Created', $eventCreated, PDO::PARAM_STR);
        $stmt->bindParam('LastUpdated', $eventCreated, PDO::PARAM_STR);
        $stmt->bindParam('UserId', $userId, PDO::PARAM_INT);
        $stmt->bindParam('StartDate', $eventStartDate, PDO::PARAM_STR);
        $stmt->bindParam('EndDate', $eventEndDate, PDO::PARAM_STR);
        $stmt->bindParam('Sport', $eventSport, PDO::PARAM_STR);
        $stmt->bindParam('Rounds', $eventRounds, PDO::PARAM_STR);
        $stmt->bindParam('Description', $eventDescription, PDO::PARAM_STR);
        $stmt->bindParam('Name', $eventName, PDO::PARAM_STR);
        $stmt->bindParam('Address', $eventAddress, PDO::PARAM_STR);
        $stmt->bindParam('City', $eventCity, PDO::PARAM_STR);
        $stmt->bindParam('StateShort', $eventStateShort, PDO::PARAM_STR);
        $stmt->bindParam('StateLong', $eventStateLong, PDO::PARAM_STR);
        $stmt->bindParam('Zip', $eventZip, PDO::PARAM_STR);
        $stmt->bindParam('Coordinator', $eventCoordinator, PDO::PARAM_STR);
        $stmt->bindParam('Phone', $eventPhone, PDO::PARAM_STR);
        $stmt->bindParam('Email', $eventEmail, PDO::PARAM_STR);
        $stmt->bindParam('Website', $eventWebsite, PDO::PARAM_STR);
        $stmt->bindParam('Calendar', $eventCalendar, PDO::PARAM_STR);
        $stmt->bindParam('Token', $eventToken, PDO::PARAM_STR);
        $stmt->execute();
    } catch (PDOException $e) {
        trigger_error($e->getMessage(), E_USER_ERROR);
    }//end try

    // get the event ID
    try {
        $sql = "
            SELECT 
              `Events`.`EventId` 
            FROM 
              `Events` 
            USE INDEX (`UserIdToken`)
            WHERE
              `Events`.`UserId` = :UserId
            AND
              `Events`.`Token` = :Token";

        $stmt = $PDO->prepare($sql);
        $stmt->bindParam('UserId', $userId, PDO::PARAM_INT);
        $stmt->bindParam('Token', $eventToken, PDO::PARAM_STR);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $eventId = $row['EventId'];
        }

    } catch (PDOException $e) {
        trigger_error($e->getMessage(), E_USER_ERROR);
    }//end try

    // insert all event admins into database
    if (count($admins) > 0) {

        foreach ($admins as $adminId) {

            $eventAdminToken = sha1($userIp . microseconds());

            try {
                $sql = "
                    INSERT INTO `EventAdmins` 
                    (
                      `EventAdminId`, 
                      `Created`, 
                      `UserId`, 
                      `EventId`, 
                      `Token`
                    ) VALUES (
                      NULL, 
                      :Created, 
                      :UserId, 
                      :EventId, 
                      :Token
                    )";

                $stmt = $PDO->prepare($sql);
                $stmt->bindParam('Created', $eventCreated, PDO::PARAM_STR);
                $stmt->bindParam('UserId', $adminId, PDO::PARAM_INT);
                $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                $stmt->bindParam('Token', $eventAdminToken, PDO::PARAM_STR);
                $stmt->execute();

            } catch (PDOException $e) {
                trigger_error($e->getMessage(), E_USER_ERROR);
            }//end try
        }
    }

    // insert wall EventId - this indicates a new event
    $wallToken = sha1($userIp . microseconds());
    try {
        $sql = "
                INSERT INTO `Wall` 
                (
                  `WallId`, 
                  `Created`, 
                  `UserId`, 
                  `UserIdFrom`, 
                  `EventId`,
                  `Token`
                ) VALUES (
                  NULL, 
                  :Created, 
                  :UserId, 
                  :UserIdFrom, 
                  :EventId,
                  :Token
                )";

        $stmt = $PDO->prepare($sql);
        $stmt->bindParam('Created', $eventCreated, PDO::PARAM_STR); // Creation date
        $stmt->bindParam('UserId', $userId, PDO::PARAM_INT); // My logged in userId
        $stmt->bindParam('UserIdFrom', $userId, PDO::PARAM_INT); // My logged in userId
        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
        $stmt->bindParam('Token', $wallToken, PDO::PARAM_STR);
        $stmt->execute();

    } catch (PDOException $e) {
        trigger_error($e->getMessage(), E_USER_ERROR);
    }//end try

    // success
    $msgBox = alertBox("Event created for $_eventStartDate", "<i class='fa fa-check-square-o'></i>", "success");
    echo json_encode(array('success' => $msgBox));


}

