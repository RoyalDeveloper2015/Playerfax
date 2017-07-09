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
$eventAdminId = null;
$eventLastUpdated = date('Y-m-d H:i:s');
$eventName = '';
$sportName = '';
$sportNameLowercase = '';
$eventStartDate = '';
$_eventStartDate = '';
$eventEndDate = '';
$_eventEndDate = '';
$eventAddress = '';
$eventCity = '';
$eventStateShort = '';
$eventStateLong = '';
$eventZip = '';
$eventTimezone = '';
$eventDescription = '';
$eventCoordinator = '';
$eventPhone = '';
$eventEmail = '';
$eventWebsite = '';
$eventCalendar = 'yes';
$eventToken = sha1($userIp . microseconds());
$results = new stdClass();

if (isset($_POST) && count($_POST) > 0) {

    // STEP 1
    $eventId = isset($_POST['step1']['eventId']) ? $_POST['step1']['eventId'] : '';
    $eventId = preg_replace("/[^0-9]/", "", $eventId);

    $eventTimezone = isset($_POST['step1']['eventTimezone']) ? $_POST['step1']['eventTimezone'] : '';
    $eventTimezone = filter_var($eventTimezone, FILTER_SANITIZE_STRING);

    // get the EventAdmins
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
        $msgBox = alertBox("Invalid Event ID - unable to update event", "<i class='fa fa-times'></i>", "danger");
        echo json_encode(array('error' => $msgBox));
        exit;
    }

    if (!in_array($eventAdminId, $eventAdminIds)) {
        $msgBox = alertBox("You must be an Admin to edit this Event", "<i class='fa fa-times'></i>", "danger");
        echo json_encode(array('error' => $msgBox));
        exit;
    }

    $eventName = isset($_POST['step1']['eventName']) ? trim($_POST['step1']['eventName']) : '';
    $eventName = filter_var($eventName, FILTER_SANITIZE_STRING);
    $eventName = preg_replace('/\s+/', ' ', $eventName);

    $eventStartDate = isset($_POST['step1']['eventStartDate']) ? trim($_POST['step1']['eventStartDate']) : '';
    $eventStartDate = filter_var($eventStartDate, FILTER_SANITIZE_STRING);

    $eventEndDate = isset($_POST['step1']['eventEndDate']) ? trim($_POST['step1']['eventEndDate']) : '';
    $eventEndDate = filter_var($eventEndDate, FILTER_SANITIZE_STRING);

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

        foreach ($_POST['step2']['eventAdminId'] AS $_eventAdminId) {

            $_eventAdminId = preg_replace("/[^0-9]/", "", $_eventAdminId);

            // make sure these admins are valid and active
            try {
                $sql = "
                SELECT 
                  `Users`.`UserId` 
                FROM 
                  `Users` 
                WHERE
                  `Users`.`UserId` = :UserId
                AND
                  `Users`.`IsActive` = 1";

                $stmt = $PDO->prepare($sql);
                $stmt->bindParam('UserId', $_eventAdminId, PDO::PARAM_INT);
                $stmt->execute();

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    // make sure there are no duplicate admins
                    if (!in_array($_eventAdminId, $admins)) {
                        array_push($admins, $_eventAdminId);
                    }
                }

            } catch (PDOException $e) {
                trigger_error($e->getMessage(), E_USER_ERROR);
            }//end try
        }
    }


    // UPDATE EVENT
    try {
        $sql = "
            UPDATE 
              `Events` 
            SET
              `Events`.`LastUpdated` = :LastUpdated, 
              `Events`.`StartDate` = :StartDate, 
              `Events`.`EndDate` = :EndDate, 
              `Events`.`Description` = :Description,
              `Events`.`Name` = :Name,
              `Events`.`Address` = :Address, 
              `Events`.`City` = :City, 
              `Events`.`StateShort` = :StateShort,
              `Events`.`StateLong` = :StateLong, 
              `Events`.`Zip` = :Zip,
              `Events`.`Timezone` = :Timezone,
              `Events`.`Coordinator` = :Coordinator, 
              `Events`.`Phone` = :Phone,
              `Events`.`Email` = :Email, 
              `Events`.`Website` = :Website,
              `Events`.`Calendar` = :Calendar
            WHERE
              `Events`.`UserId` = :UserId
            AND 
              `Events`.`EventId` = :EventId";

        $stmt = $PDO->prepare($sql);
        $stmt->bindParam('LastUpdated', $eventLastUpdated, PDO::PARAM_STR);
        $stmt->bindParam('StartDate', $eventStartDate, PDO::PARAM_STR);
        $stmt->bindParam('EndDate', $eventEndDate, PDO::PARAM_STR);
        $stmt->bindParam('Description', $eventDescription, PDO::PARAM_STR);
        $stmt->bindParam('Name', $eventName, PDO::PARAM_STR);
        $stmt->bindParam('Address', $eventAddress, PDO::PARAM_STR);
        $stmt->bindParam('City', $eventCity, PDO::PARAM_STR);
        $stmt->bindParam('StateShort', $eventStateShort, PDO::PARAM_STR);
        $stmt->bindParam('StateLong', $eventStateLong, PDO::PARAM_STR);
        $stmt->bindParam('Zip', $eventZip, PDO::PARAM_STR);
        $stmt->bindParam('Timezone', $eventTimezone, PDO::PARAM_STR);
        $stmt->bindParam('Coordinator', $eventCoordinator, PDO::PARAM_STR);
        $stmt->bindParam('Phone', $eventPhone, PDO::PARAM_STR);
        $stmt->bindParam('Email', $eventEmail, PDO::PARAM_STR);
        $stmt->bindParam('Website', $eventWebsite, PDO::PARAM_STR);
        $stmt->bindParam('Calendar', $eventCalendar, PDO::PARAM_STR);
        $stmt->bindParam('UserId', $eventAdminId, PDO::PARAM_INT);
        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
        $stmt->execute();
    } catch (PDOException $e) {
        trigger_error($e->getMessage(), E_USER_ERROR);
    }//end try

    // insert all event admins into database
    if (count($admins) > 0) {

        // DELETE ALL ADMINS
        try {
            $sql = "
                DELETE FROM 
                  `EventAdmins` 
                WHERE
                  `EventAdmins`.`EventId` = :EventId";

            $stmt = $PDO->prepare($sql);
            $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
            $stmt->execute();

        } catch (PDOException $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
        }//end try

        foreach ($admins as $adminId) {

            $eventAdminToken = sha1($userIp . microseconds());
            $adminCreated = date('Y-m-d H:i:s');

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
                $stmt->bindParam('Created', $adminCreated, PDO::PARAM_STR);
                $stmt->bindParam('UserId', $adminId, PDO::PARAM_INT);
                $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                $stmt->bindParam('Token', $eventAdminToken, PDO::PARAM_STR);
                $stmt->execute();

            } catch (PDOException $e) {
                trigger_error($e->getMessage(), E_USER_ERROR);
            }//end try
        }
    }

    // success (refresh home page immediately)
    $msgBox = alertBox("Event updated for $_eventStartDate.", "<i class='fa fa-check-square-o'></i>", "success");
    echo json_encode(array('success' => $msgBox));

}

