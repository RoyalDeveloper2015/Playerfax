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
$eventName = '';

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
      `Events`.`Name`
    FROM 
      `Events` 
    WHERE
      `Events`.`EventId` = :EventId";

    $stmt = $PDO->prepare($sql);
    $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $eventAdminId = $row['UserId'];;
        $sportName = getSportName($row['Sport']);
        $sportNameLowercase = strtolower(str_replace(' ', '', $sportName));
        $eventName = $row['Name'];
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
                <!-- col-sm-10 -->
                <a href="index.php?page=events" class="btn btn-primary"> <i class="fa fa-chevron-left" aria-hidden="true"></i> Back To Events</a>
                <span id="pageLoadingSpinner" class="label">Page is loading...</span>
                <div class="title_h2" style="margin-top:10px;margin-bottom:10px;"><h2>Players Scoring Chart For <?php echo $eventName; ?></h2></div>
                <div class="row">
                    <div id="scoringChartTableToolbar" class="col-md-4 hidden-xs pull-left"></div>
                    <div class="col-md-4 hidden-xs pull-right"><input id="scoringChartTableSearch" class="form-control" placeholder="Search" aria-controls="scoringChartTable" type="search"></div>
                </div>
                <div class="table-responsive">
                    <table id="scoringChartTable" class="table table-striped display responsive no-wrap datatable">
                        <thead>
                        <tr>
                            <th></th>
                            <th>Team ID</th>
                            <th>Last, First</th>
                            <th>Overall</th>
                            <th>Player</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php

                        $playerStatsForEvent = ''; // modal

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
                                WHERE
                                  `Events`.`UserId` = :UserId
                                AND
                                  `Events`.`EventId` = :EventId";

                            $stmt_event = $PDO->prepare($sql_event);
                            $stmt_event->bindParam('UserId', $eventAdminId, PDO::PARAM_INT);
                            $stmt_event->bindParam('EventId', $eventId, PDO::PARAM_INT);
                            $stmt_event->execute();

                            while ($row_event = $stmt_event->fetch(PDO::FETCH_ASSOC)) {

                                $eventAdminId = $row_event['UserId'];
                                $eventRounds = $row_event['Rounds'];

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

                                        $table_header = '';
                                        $table_rows = '';

                                        $playerId = $row_player['PlayerId'];
                                        $playerToken = $row_player['Token'];
                                        $playerFullName = trim($row_player['FirstName'] . ' ' . $row_player['LastName']);
                                        $playerFullName = trim($row_player['FirstName'] . ' ' . $row_player['LastName']);
                                        $playerFullNameReverse = trim($row_player['LastName'] . ', ' . $row_player['FirstName']);
                                        $playerFullNameReverse = ltrim($playerFullNameReverse, ',');

                                        $playerPicture = '';
                                        if (!empty($row_player['Picture']) && file_exists(constant('UPLOADS_PLAYERS') . $row_player['Picture'])) {
                                            $playerPicture = constant('UPLOADS_PLAYERS') . $row_player['Picture'];
                                        } else {
                                            if ($row_player['Gender'] == 0) {
                                                $playerPicture .= constant('UPLOADS_PLAYERS') . 'default-male.png';
                                            } else {
                                                $playerPicture .= constant('UPLOADS_PLAYERS') . 'default-female.png';
                                            }
                                        }

                                        echo '
                                            <tr>
                                                <td><button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . '">Go</button></td>
                                                <td>' . $row_player['TeamId'] . '</td>
                                                <td>' . $playerFullNameReverse . '</td>
                                                <td><img src="images/rating.png" alt="User Image" class="img-responsive"></td>
                                                <td>
                                                     <div class="score_table_img">
                                                        <a href="index.php?page=player&token=' . $playerToken . '" target="_blank"><img src="' . $playerPicture . '" alt="' . $playerFullName . '" class="img-responsive img-circle"></a>
                                                    </div>
                                                </td>
                                            </tr>';

                                        if ($row_event['Sport'] == '1') {

                                            // Baseball
                                            // baseball_VelocityMound // MPH
                                            // baseball_VelocityOutfield // MPH
                                            // baseball_VelocityInfield // MPH
                                            // baseball_SwingVelocity // MPH
                                            // baseball_60YardDash // Time
                                            // baseball_CatcherPop // Time
                                            // baseball_CatcherRelease // Time
                                            // baseball_PrimaryPosition // Position Number
                                            // baseball_SecondaryPosition // Position Number
                                            // baseball_TeeVelocity // MPH


                                            for ($i = 1; $i <= $eventRounds; $i++) {

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

                                                        $table_rows .= '
                                                        <tr>
                                                            <td>' . $i . '</td>
                                                            <td>' . $row['VelocityMound'] . ' mph</td>
                                                            <td>' . $row['VelocityOutfield'] . ' mph</td>
                                                            <td>' . $row['VelocityInfield'] . ' mph</td>
                                                            <td>' . $row['SwingVelocity'] . ' mph</td>
                                                            <td>' . secondsToHMSU($row['60YardDash']) . '</td>
                                                            <td>' . secondsToHMSU($row['CatcherPop']) . '</td>
                                                            <td>' . secondsToHMSU($row['CatcherRelease']) . '</td>
                                                            <td>' . getBaseballFieldPositionName($row['PrimaryPosition']) . '</td>
                                                            <td>' . getBaseballFieldPositionName($row['SecondaryPosition']) . '</td>
                                                            <td>' . $row['TeeVelocity'] . ' mph</td>
                                                        </tr>';
                                                    }

                                                } catch (PDOException $e) {
                                                    trigger_error($e->getMessage(), E_USER_ERROR);
                                                }//end try


                                            } // end event rounds

                                            $table_header .= '
                                            <tr>
                                                <th>Round</th>
                                                <th>Velocity Mound</th>
                                                <th>Velocity Outfield</th>
                                                <th>Velocity Infield</th>
                                                <th>Swing Velocity</th>
                                                <th>60 Yard Dash</th>
                                                <th>Catcher Pop</th>
                                                <th>Catcher Release</th>
                                                <th>Primary Position</th>
                                                <th>Secondary Position</th>
                                                <th>Tee Velocity</th>
                                            </tr>';

                                        } // end baseball

                                        if ($row_event['Sport'] == '2') {

                                            // Cross Fit
                                            // crossFit_ShuttleRun // Time
                                            // crossFit_40YardDash // Time
                                            // crossFit_5105ConeDrill // Time
                                            // crossFit_3RMHang // Count
                                            // crossFit_VerticalJump // Inch
                                            // crossFit_BroadJump // Inch
                                            // crossFit_PowerClean // Count
                                            // crossFit_PullUps // Count
                                            // crossFit_PushUps // Count

                                            for ($i = 1; $i <= $eventRounds; $i++) {

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
                                                        $table_rows .= '
                                                        <tr>
                                                            <td>' . $i . '</td>
                                                            <td>' . secondsToHMSU($row['ShuttleRun']) . '</td>
                                                            <td>' . secondsToHMSU($row['40YardDash']) . '</td>
                                                            <td>' . secondsToHMSU($row['5105ConeDrill']) . '</td>
                                                            <td>' . $row['3RMHang'] . '</td>
                                                            <td>' . $row['VerticalJump'] . ' in.</td>
                                                            <td>' . $row['BroadJump'] . ' in.</td>
                                                            <td>' . $row['PowerClean'] . '</td>
                                                            <td>' . $row['PullUps'] . '</td>
                                                            <td>' . $row['PushUps'] . '</td>
                                                        </tr>';
                                                    }

                                                } catch (PDOException $e) {
                                                    trigger_error($e->getMessage(), E_USER_ERROR);
                                                }//end try

                                            } // end event rounds

                                            $table_header .= '
                                            <tr>
                                                <th>Round</th>
                                                <th>Shuttle Run</th>
                                                <th>40 Yard Dash</th>
                                                <th>5-10-5 Cone Drill</th>
                                                <th>3 RM Hang</th>
                                                <th>Vertical Jump</th>
                                                <th>Broad Jump</th>
                                                <th>Power Clean</th>
                                                <th>Pull Ups</th>
                                                <th>Push Ups</th>
                                            </tr>';

                                        } // end cross fit

                                        if ($row_event['Sport'] == '3') {

                                            // Fast Pitch
                                            // fastPitch_VelocityMound // MPH
                                            // fastPitch_VelocityOutfield // MPH
                                            // fastPitch_VelocityInfield // MPH
                                            // fastPitch_SwingVelocity // MPH
                                            // fastPitch_60YardDash // Time
                                            // fastPitch_CatcherPop // Time
                                            // fastPitch_CatcherRelease // Time
                                            // fastPitch_PrimaryPosition // Position Number
                                            // fastPitch_SecondaryPosition // Position Number
                                            // fastPitch_TeeVelocity // MPH

                                            for ($i = 1; $i <= $eventRounds; $i++) {

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
                                                        $table_rows .= '
                                                        <tr>
                                                            <td>' . $i . '</td>
                                                            <td>' . $row['VelocityMound'] . ' mph</td>
                                                            <td>' . $row['VelocityOutfield'] . ' mph</td>
                                                            <td>' . $row['VelocityInfield'] . ' mph</td>
                                                            <td>' . $row['SwingVelocity'] . ' mph</td>
                                                            <td>' . secondsToHMSU($row['60YardDash']) . '</td>
                                                            <td>' . secondsToHMSU($row['CatcherPop']) . '</td>
                                                            <td>' . secondsToHMSU($row['CatcherRelease']) . '</td>
                                                            <td>' . getBaseballFieldPositionName($row['PrimaryPosition']) . '</td>
                                                            <td>' . getBaseballFieldPositionName($row['SecondaryPosition']) . '</td>
                                                            <td>' . $row['TeeVelocity'] . ' mph</td>
                                                        </tr>';
                                                    }

                                                } catch (PDOException $e) {
                                                    trigger_error($e->getMessage(), E_USER_ERROR);
                                                }//end try

                                            } // end event rounds

                                            $table_header .= '
                                            <tr>
                                                <th>Round</th>
                                                <th>Velocity Mound</th>
                                                <th>Velocity Outfield</th>
                                                <th>Velocity Infield</th>
                                                <th>Swing Velocity</th>
                                                <th>60 Yard Dash</th>
                                                <th>Catcher Pop</th>
                                                <th>Catcher Release</th>
                                                <th>Primary Position</th>
                                                <th>Secondary Position</th>
                                                <th>Tee Velocity</th>
                                            </tr>';

                                        } // end fast pitch

                                        if ($row_event['Sport'] == '4') {

                                            // Lacrosse
                                            // lacrosse_60YardDash // Time
                                            // lacrosse_5ConeFootwork // Time
                                            // lacrosse_ShuttleRun // Time
                                            // lacrosse_Rebounder10 // Count
                                            // lacrosse_GoalShot10 // Count
                                            // lacrosse_Accuracy50 // Count
                                            // lacrosse_VelocityThrow // MPH

                                            for ($i = 1; $i <= $eventRounds; $i++) {

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
                                                        $table_rows .= '
                                                        <tr>
                                                            <td>' . $i . '</td>
                                                            <td>' . secondsToHMSU($row['60YardDash']) . '</td>
                                                            <td>' . secondsToHMSU($row['5ConeFootwork']) . '</td>
                                                            <td>' . secondsToHMSU($row['ShuttleRun']) . '</td>
                                                            <td>' . $row['SwingVelocity'] . '</td>
                                                            <td>' . $row['Rebounder10'] . '</td>
                                                            <td>' . $row['GoalShot10'] . '</td>
                                                            <td>' . $row['Accuracy50'] . '</td>
                                                            <td>' . $row['VelocityThrow'] . ' mph</td>
                                                        </tr>';
                                                    }

                                                } catch (PDOException $e) {
                                                    trigger_error($e->getMessage(), E_USER_ERROR);
                                                }//end try

                                            } // end event rounds

                                            $table_header .= '
                                            <tr>
                                                <th>Round</th>
                                                <th>60 Yard Dash</th>
                                                <th>5 Cone Footwork</th>
                                                <th>Shuttle Run</th>
                                                <th>Rebounder 10</th>
                                                <th>Goal Shot 10</th>
                                                <th>Accuracy 50</th>
                                                <th>Velocity Throw</th>
                                            </tr>';

                                        } // end lacrosse

                                        if ($row_event['Sport'] == '5') {

                                            // Swimming
                                            // swimming_25MFreestyle // Time
                                            // swimming_25MBackStroke // Time
                                            // swimming_25MBreastStroke // Time
                                            // swimming_25MButterfly // Time
                                            // swimming_50MFreestyle // Time
                                            // swimming_50MBackStroke // Time
                                            // swimming_50MBreastStroke // Time
                                            // swimming_50MButterfly // Time
                                            // swimming_100MFreestyle // Time
                                            // swimming_100MBackStroke // Time
                                            // swimming_100MBreastStroke // Time
                                            // swimming_100MButterfly // Time
                                            // swimming_100MIndividualMedley // Time
                                            // swimming_200MIndividualMedley // Time

                                            for ($i = 1; $i <= $eventRounds; $i++) {

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
                                                        $table_rows .= '
                                                        <tr>
                                                            <td>' . $i . '</td>
                                                            <td>' . secondsToHMSU($row['25MFreestyle']) . '</td>
                                                            <td>' . secondsToHMSU($row['25MBackStroke']) . '</td>
                                                            <td>' . secondsToHMSU($row['25MBreastStroke']) . '</td>
                                                            <td>' . secondsToHMSU($row['25MButterfly']) . '</td>
                                                            <td>' . secondsToHMSU($row['50MFreestyle']) . '</td>
                                                            <td>' . secondsToHMSU($row['50MBackStroke']) . '</td>
                                                            <td>' . secondsToHMSU($row['50MBreastStroke']) . '</td>
                                                            <td>' . secondsToHMSU($row['50MButterfly']) . '</td>
                                                            <td>' . secondsToHMSU($row['100MFreestyle']) . '</td>
                                                            <td>' . secondsToHMSU($row['100MBackStroke']) . '</td>
                                                            <td>' . secondsToHMSU($row['100MBreastStroke']) . '</td>
                                                            <td>' . secondsToHMSU($row['100MButterfly']) . '</td>
                                                            <td>' . secondsToHMSU($row['100MIndividualMedley']) . '</td>
                                                            <td>' . secondsToHMSU($row['200MIndividualMedley']) . '</td>
                                                        </tr>';
                                                    }

                                                } catch (PDOException $e) {
                                                    trigger_error($e->getMessage(), E_USER_ERROR);
                                                }//end try

                                            } // end event rounds

                                            $table_header .= '
                                            <tr>
                                                <th>Round</th>
                                                <th>25M Freestyle</th>
                                                <th>25M Back Stroke</th>
                                                <th>25M Breast Stroke</th>
                                                <th>25M Butterfly</th>
                                                <th>50M Freestyle</th>
                                                <th>50M Back Stroke</th>
                                                <th>50M Breast Stroke</th>
                                                <th>50M Butterfly</th>
                                                <th>100M Freestyle</th>
                                                <th>100M Back Stroke</th>
                                                <th>100M Breast Stroke</th>
                                                <th>100M Butterfly</th>
                                                <th>100M Individual Medley</th>
                                                <th>200M Individual Medley</th>
                                            </tr>';

                                        } // end swimming

                                        $playerStatsForEvent .= '<!-- Modal -->
                                            <div id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . '" class="modal fade forget_section" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="container">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div class="close_img">
                                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                                </div>
                                                                <div class="title_h2">
                                                                    <h2>Results for ' . $row_event['Name'] . '</h2>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <!-- Modal body-->
                                                                <div class="user_result">
                                                                    <div class="player_title">
                                                                        <div class="score_table_img">
                                                                            <a href="index.php?page=player&token=' . $playerToken . '" target="_blank"><img src="' . $playerPicture . '" alt="User Image" class="img-responsive img-circle"></a>
                                                                        </div>
                                                                        <h4>' . $playerFullName . '</h4>
                                                                    </div>
                                                                    <div class="camp_grade">
                                                                        <label>Team ID</label><span>' . $row_player['TeamId'] . '</span>
                                                                    </div>
                                                                    <div class="camp_grade">
                                                                        <label>Grad Year</label><span>' . $row_player['GradYear'] . '</span>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'TableToolbar" class="col-md-4 hidden-xs pull-left"></div>
                                                                    <div class="col-md-4 hidden-xs pull-right"><input id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'TableSearch" class="form-control" placeholder="Search" aria-controls="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Table" type="search"></div>
                                                                </div>
                                                                <div class="table-responsive">
                                                                    <table id="player' . $playerId . 'StatsForEvent' . $row_event['EventId'] . 'Table" class="table table-striped display responsive no-wrap datatable">
                                                                        <thead>
                                                                            ' . $table_header . '
                                                                        </thead>
                                                                        <tbody>
                                                                            ' . $table_rows . '
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                                <!-- end Modal body-->
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--Modal-->';

                                    } // end while for players
                                } catch (PDOException $e) {
                                    trigger_error($e->getMessage(), E_USER_ERROR);
                                }//end try

                            } // end while for events
                        } catch (PDOException $e) {
                            trigger_error($e->getMessage(), E_USER_ERROR);
                        }//end try
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<?php

echo $playerStatsForEvent;

?>
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

        $('table.datatable').each(function () {
            var tableId = $(this).attr('id');
            var table = $('#' + tableId);

            var dataTable = table.DataTable({
                dom: 'Rfrtlip',
                paging: false,
                responsive: true,
                initComplete: function (settings, json) {
                    if (($('#' + tableId + 'Toolbar')).length) {
                        $(this).DataTable().buttons().container().appendTo($('#' + tableId + 'Toolbar'));
                    }
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

            if (($('#' + tableId + 'Search')).length) {
                $('#' + tableId + 'Search').on("keyup search input paste cut", function () {
                    var value = $(this).val();
                    dataTable.search(value).draw();
                });
            }

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

</script>
</body>
</html>