<?php

if (!isset($_SESSION['userId'])) {
    header('Location: index.php?page=login');
    exit;
}

header('Content-Type: application/json');


if (isset($_POST) && count($_POST) > 0) {

    $playerToken = isset($_POST['token']) ? trim($_POST['token']) : '';
    $playerToken = filter_var($playerToken, FILTER_SANITIZE_STRING);

    $playerId = null;

    $statsAdded = false;

    $created = date('Y-m-d H:i:s');
    $certified = '0'; // 0=un-certified, 1=certified

    $lastUpdated = date('Y-m-d H:i:s');

    $_statsDate = isset($_POST['statsEntryDate']) ? trim($_POST['statsEntryDate']) : '';
    $_month = substr($_statsDate, 0, 2);
    $_day = substr($_statsDate, 3, 2);
    $_year = substr($_statsDate, 6, 4);
    if (!checkdate($_month, $_day, $_year)) {
        $msgBox = alertBox("Enter a valid date MM/DD/YYYY", "<i class='fa fa-times'></i>", "danger");
        echo json_encode(array('error' => $msgBox));
        exit;
    } else {
        $_entryDate = date('F jS, Y', strtotime("$_month/$_day/$_year"));
        $entryDate = date('Y-m-d', strtotime("$_month/$_day/$_year"));
    }

    $count = 0;

    try {
        $sql = "
        SELECT
          `Players`.`PlayerId`
        FROM 
          `Players`
        USE INDEX (`TokenIsActive`)
        WHERE
          `Players`.`Token` = :Token
        AND 
          `Players`.`IsActive` = 1";

        $stmt = $PDO->prepare($sql);
        $stmt->bindParam('Token', $playerToken, PDO::PARAM_STR);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $playerId = $row['PlayerId'];
            $count++;
        }

    } catch (PDOException $e) {
        trigger_error($e->getMessage(), E_USER_ERROR);
    }//end try


    if ($count > 0) {

        // baseball
        if (hasSportStat($_POST, 'baseball')) {

            $statsAdded = true;

            $baseball_VelocityMound = isset($_POST['baseball_VelocityMound']) ? preg_replace("/[^0-9]/", "", $_POST['baseball_VelocityMound']) : '0'; // MPH
            $baseball_VelocityOutfield = isset($_POST['baseball_VelocityOutfield']) ? preg_replace("/[^0-9]/", "", $_POST['baseball_VelocityOutfield']) : '0'; // MPH
            $baseball_VelocityInfield = isset($_POST['baseball_VelocityInfield']) ? preg_replace("/[^0-9]/", "", $_POST['baseball_VelocityInfield']) : '0'; // MPH
            $baseball_SwingVelocity = isset($_POST['baseball_SwingVelocity']) ? preg_replace("/[^0-9]/", "", $_POST['baseball_SwingVelocity']) : '0'; // MPH
            $baseball_60YardDash = isset($_POST['baseball_60YardDash']) ? hmsuToDecimal(trim($_POST['baseball_60YardDash'])) : '0.0'; // Time
            $baseball_CatcherPop = isset($_POST['baseball_CatcherPop']) ? hmsuToDecimal(trim($_POST['baseball_CatcherPop'])) : '0.0'; // Time
            $baseball_CatcherRelease = isset($_POST['baseball_CatcherRelease']) ? hmsuToDecimal(trim($_POST['baseball_CatcherRelease'])) : '0.0'; // Time
            $baseball_PrimaryPosition = isset($_POST['baseball_PrimaryPosition']) ? getBaseballFieldPositionNumber(preg_replace("/[^0-9]/", "", $_POST['baseball_PrimaryPosition'])) : '0'; // Position Number
            $baseball_SecondaryPosition = isset($_POST['baseball_SecondaryPosition']) ? getBaseballFieldPositionNumber(preg_replace("/[^0-9]/", "", $_POST['baseball_SecondaryPosition'])) : '0'; // Position Number
            $baseball_TeeVelocity = isset($_POST['baseball_TeeVelocity']) ? preg_replace("/[^0-9]/", "", $_POST['baseball_TeeVelocity']) : '0'; // MPH

            if (!empty($baseball_VelocityMound) && $baseball_VelocityMound > 255) {
                $msgBox = alertBox("Velocity Mound (0-255) (Baseball)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            }
            if (!empty($baseball_VelocityOutfield) && $baseball_VelocityOutfield > 255) {
                $msgBox = alertBox("Velocity Outfield (0-255) (Baseball)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            }
            if (!empty($baseball_VelocityInfield) && $baseball_VelocityInfield > 255) {
                $msgBox = alertBox("Velocity Infield (0-255) (Baseball)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            }
            if (!empty($baseball_SwingVelocity) && $baseball_SwingVelocity > 255) {
                $msgBox = alertBox("Swing Velocity (0-255) (Baseball)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            }
            if ($baseball_60YardDash === false) {
                $msgBox = alertBox("Format invalid for 60 Yard Dash (Baseball)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            }
            if ($baseball_CatcherPop === false) {
                $msgBox = alertBox("Format invalid for Catcher Pop (Baseball)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            }
            if ($baseball_CatcherRelease === false) {
                $msgBox = alertBox("Format invalid for Catcher Release (Baseball)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            }
            if (!empty($baseball_PrimaryPosition) && $baseball_PrimaryPosition > 9) {
                $msgBox = alertBox("Select a valid Primary Position (Baseball)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            }
            if (!empty($baseball_SecondaryPosition) && $baseball_SecondaryPosition > 9) {
                $msgBox = alertBox("Select a valid Secondary Position (Baseball)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            }

            // insert row for this sport
            try {
                $sql = "
                INSERT INTO `GameBaseball` 
                (
                  `BaseballId`, 
                  `Created`, 
                  `LastUpdated`, 
                  `EntryDate`,
                  `UserId`, 
                  `PlayerId`, 
                  `Certified`, 
                  `VelocityMound`, 
                  `VelocityOutfield`, 
                  `VelocityInfield`, 
                  `SwingVelocity`, 
                  `60YardDash`, 
                  `CatcherPop`, 
                  `CatcherRelease`, 
                  `PrimaryPosition`, 
                  `SecondaryPosition`,
                  `TeeVelocity`
                ) VALUES (
                  NULL, 
                  :Created, 
                  :LastUpdated, 
                  :EntryDate,
                  :UserId, 
                  :PlayerId,
                  :Certified, 
                  :VelocityMound, 
                  :VelocityOutfield, 
                  :VelocityInfield, 
                  :SwingVelocity, 
                  :_60YardDash, 
                  :CatcherPop, 
                  :CatcherRelease, 
                  :PrimaryPosition, 
                  :SecondaryPosition,
                  :TeeVelocity
                )";

                $stmt = $PDO->prepare($sql);
                $stmt->bindParam('Created', $created, PDO::PARAM_STR);
                $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                $stmt->bindParam('UserId', $userId, PDO::PARAM_INT);
                $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                $stmt->bindParam('Certified', $certified, PDO::PARAM_INT);
                $stmt->bindParam('VelocityMound', $baseball_VelocityMound, PDO::PARAM_INT);
                $stmt->bindParam('VelocityOutfield', $baseball_VelocityOutfield, PDO::PARAM_INT);
                $stmt->bindParam('VelocityInfield', $baseball_VelocityInfield, PDO::PARAM_INT);
                $stmt->bindParam('SwingVelocity', $baseball_SwingVelocity, PDO::PARAM_STR);
                $stmt->bindParam('_60YardDash', $baseball_60YardDash, PDO::PARAM_STR);
                $stmt->bindParam('CatcherPop', $baseball_CatcherPop, PDO::PARAM_STR);
                $stmt->bindParam('CatcherRelease', $baseball_CatcherRelease, PDO::PARAM_STR);
                $stmt->bindParam('PrimaryPosition', $baseball_PrimaryPosition, PDO::PARAM_INT);
                $stmt->bindParam('SecondaryPosition', $baseball_SecondaryPosition, PDO::PARAM_INT);
                $stmt->bindParam('TeeVelocity', $baseball_TeeVelocity, PDO::PARAM_INT);
                $stmt->execute();

            } catch (PDOException $e) {
                trigger_error($e->getMessage(), E_USER_ERROR);
            }//end try
        }


        // fast pitch
        if (hasSportStat($_POST, 'fastPitch')) {

            $statsAdded = true;

            $fastPitch_VelocityMound = isset($_POST['fastPitch_VelocityMound']) ? preg_replace("/[^0-9]/", "", $_POST['fastPitch_VelocityMound']) : '0'; // MPH
            $fastPitch_VelocityOutfield = isset($_POST['fastPitch_VelocityOutfield']) ? preg_replace("/[^0-9]/", "", $_POST['fastPitch_VelocityOutfield']) : '0'; // MPH
            $fastPitch_VelocityInfield = isset($_POST['fastPitch_VelocityInfield']) ? preg_replace("/[^0-9]/", "", $_POST['fastPitch_VelocityInfield']) : '0'; // MPH
            $fastPitch_SwingVelocity = isset($_POST['fastPitch_SwingVelocity']) ? preg_replace("/[^0-9]/", "", $_POST['fastPitch_SwingVelocity']) : '0'; // MPH
            $fastPitch_60YardDash = isset($_POST['fastPitch_60YardDash']) ? hmsuToDecimal(trim($_POST['fastPitch_60YardDash'])) : '0.0'; // Time
            $fastPitch_CatcherPop = isset($_POST['fastPitch_CatcherPop']) ? hmsuToDecimal(trim($_POST['fastPitch_CatcherPop'])) : '0.0'; // Time
            $fastPitch_CatcherRelease = isset($_POST['fastPitch_CatcherRelease']) ? hmsuToDecimal(trim($_POST['fastPitch_CatcherRelease'])) : '0.0'; // Time
            $fastPitch_PrimaryPosition = isset($_POST['fastPitch_PrimaryPosition']) ? getBaseballFieldPositionNumber(preg_replace("/[^0-9]/", "", $_POST['fastPitch_PrimaryPosition'])) : '0'; // Position Number
            $fastPitch_SecondaryPosition = isset($_POST['fastPitch_SecondaryPosition']) ? getBaseballFieldPositionNumber(preg_replace("/[^0-9]/", "", $_POST['fastPitch_SecondaryPosition'])) : '0'; // Position Number
            $fastPitch_TeeVelocity = isset($_POST['fastPitch_TeeVelocity']) ? preg_replace("/[^0-9]/", "", $_POST['fastPitch_TeeVelocity']) : '0'; // MPH

            if (!empty($fastPitch_VelocityMound) && $fastPitch_VelocityMound > 255) {
                $msgBox = alertBox("Velocity Mound (0-255) (Fast Pitch)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            }
            if (!empty($fastPitch_VelocityOutfield) && $fastPitch_VelocityOutfield > 255) {
                $msgBox = alertBox("Velocity Outfield (0-255) (Fast Pitch)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            }
            if (!empty($fastPitch_VelocityInfield) && $fastPitch_VelocityInfield > 255) {
                $msgBox = alertBox("Velocity Infield (0-255) (Fast Pitch)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            }
            if (!empty($fastPitch_SwingVelocity) && $fastPitch_SwingVelocity > 255) {
                $msgBox = alertBox("Swing Velocity (0-255) (Fast Pitch)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            }
            if ($fastPitch_60YardDash === false) {
                $msgBox = alertBox("Format invalid for 60 Yard Dash (Fast Pitch)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            }
            if ($fastPitch_CatcherPop === false) {
                $msgBox = alertBox("Format invalid for Catcher Pop (Fast Pitch)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            }
            if ($fastPitch_CatcherRelease === false) {
                $msgBox = alertBox("Format invalid for Catcher Release (Fast Pitch)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            }
            if (!empty($fastPitch_PrimaryPosition) && $fastPitch_PrimaryPosition > 9) {
                $msgBox = alertBox("Select a valid Primary Position (Fast Pitch)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            }
            if (!empty($fastPitch_SecondaryPosition) && $fastPitch_SecondaryPosition > 9) {
                $msgBox = alertBox("Select a valid Secondary Position (Fast Pitch)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            }
            if (!empty($fastPitch_TeeVelocity) && $fastPitch_TeeVelocity > 255) {
                $msgBox = alertBox("Tee Velocity (0-255) (Fast Pitch)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            }

            // insert row for this sport
            try {
                $sql = "
                INSERT INTO `GameFastPitch` 
                (
                  `FastPitchId`, 
                  `Created`, 
                  `LastUpdated`, 
                  `EntryDate`,
                  `UserId`, 
                  `PlayerId`,
                  `Certified`, 
                  `VelocityMound`, 
                  `VelocityOutfield`, 
                  `VelocityInfield`, 
                  `SwingVelocity`, 
                  `60YardDash`, 
                  `CatcherPop`, 
                  `CatcherRelease`, 
                  `PrimaryPosition`, 
                  `SecondaryPosition`,
                  `TeeVelocity`
                ) VALUES (
                  NULL, 
                  :Created, 
                  :LastUpdated, 
                  :EntryDate,
                  :UserId, 
                  :PlayerId,
                  :Certified, 
                  :VelocityMound, 
                  :VelocityOutfield, 
                  :VelocityInfield, 
                  :SwingVelocity, 
                  :_60YardDash, 
                  :CatcherPop, 
                  :CatcherRelease, 
                  :PrimaryPosition, 
                  :SecondaryPosition,
                  :TeeVelocity
                )";

                $stmt = $PDO->prepare($sql);
                $stmt->bindParam('Created', $created, PDO::PARAM_STR);
                $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                $stmt->bindParam('UserId', $userId, PDO::PARAM_INT);
                $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                $stmt->bindParam('Certified', $certified, PDO::PARAM_INT);
                $stmt->bindParam('VelocityMound', $fastPitch_VelocityMound, PDO::PARAM_INT);
                $stmt->bindParam('VelocityOutfield', $fastPitch_VelocityOutfield, PDO::PARAM_INT);
                $stmt->bindParam('VelocityInfield', $fastPitch_VelocityInfield, PDO::PARAM_INT);
                $stmt->bindParam('SwingVelocity', $fastPitch_SwingVelocity, PDO::PARAM_INT);
                $stmt->bindParam('_60YardDash', $fastPitch_60YardDash, PDO::PARAM_STR);
                $stmt->bindParam('CatcherPop', $fastPitch_CatcherPop, PDO::PARAM_STR);
                $stmt->bindParam('CatcherRelease', $fastPitch_CatcherRelease, PDO::PARAM_STR);
                $stmt->bindParam('PrimaryPosition', $fastPitch_PrimaryPosition, PDO::PARAM_INT);
                $stmt->bindParam('SecondaryPosition', $fastPitch_SecondaryPosition, PDO::PARAM_INT);
                $stmt->bindParam('TeeVelocity', $fastPitch_TeeVelocity, PDO::PARAM_INT);
                $stmt->execute();

            } catch (PDOException $e) {
                trigger_error($e->getMessage(), E_USER_ERROR);
            }//end try
        }


        // lacrosse
        if (hasSportStat($_POST, 'lacrosse')) {

            $statsAdded = true;

            $lacrosse_60YardDash = isset($_POST['lacrosse_60YardDash']) ? hmsuToDecimal(trim($_POST['lacrosse_60YardDash'])) : '0.0'; // Time
            $lacrosse_5ConeFootwork = isset($_POST['lacrosse_5ConeFootwork']) ? hmsuToDecimal(trim($_POST['lacrosse_5ConeFootwork'])) : '0.0'; // Time
            $lacrosse_ShuttleRun = isset($_POST['lacrosse_ShuttleRun']) ? hmsuToDecimal(trim($_POST['lacrosse_ShuttleRun'])) : '0.0'; // Time
            $lacrosse_Rebounder10 = isset($_POST['lacrosse_Rebounder10']) ? preg_replace("/[^0-9]/", "", $_POST['lacrosse_Rebounder10']) : '0'; // Count
            $lacrosse_GoalShot10 = isset($_POST['lacrosse_GoalShot10']) ? preg_replace("/[^0-9]/", "", $_POST['lacrosse_GoalShot10']) : '0'; // Count
            $lacrosse_Accuracy50 = isset($_POST['lacrosse_Accuracy50']) ? preg_replace("/[^0-9]/", "", $_POST['lacrosse_Accuracy50']) : '0'; // Count
            $lacrosse_VelocityThrow = isset($_POST['lacrosse_VelocityThrow']) ? preg_replace("/[^0-9]/", "", $_POST['lacrosse_VelocityThrow']) : '0'; // MPH

            if ($lacrosse_60YardDash === false) {
                $msgBox = alertBox("Format invalid for 60 Yard Dash (Lacrosse)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            }
            if ($lacrosse_5ConeFootwork === false) {
                $msgBox = alertBox("Format invalid for 5 Cone Footwork (Lacrosse)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            }
            if ($lacrosse_ShuttleRun === false) {
                $msgBox = alertBox("Format invalid for Shuttle Run (Lacrosse)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            }
            if (!empty($lacrosse_Rebounder10) && $lacrosse_Rebounder10 > 65535) {
                $msgBox = alertBox("Rebounder 10 (0-65535) (Lacrosse)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            }
            if (!empty($lacrosse_GoalShot10) && $lacrosse_GoalShot10 > 65535) {
                $msgBox = alertBox("Goal Shot 10 (0-65535) (Lacrosse)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            }
            if (!empty($lacrosse_Accuracy50) && $lacrosse_Accuracy50 > 65535) {
                $msgBox = alertBox("Accuracy 50 (0-65535) (Lacrosse)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            }

            // insert row for this sport
            try {
                $sql = "
                INSERT INTO `GameLacrosse` 
                (
                  `LacrosseId`, 
                  `Created`, 
                  `LastUpdated`, 
                  `EntryDate`,
                  `UserId`, 
                  `PlayerId`,
                  `Certified`, 
                  `60YardDash`, 
                  `5ConeFootwork`, 
                  `ShuttleRun`, 
                  `Rebounder10`, 
                  `GoalShot10`, 
                  `Accuracy50`, 
                  `VelocityThrow`
                ) VALUES (
                  NULL, 
                  :Created, 
                  :LastUpdated, 
                  :EntryDate,
                  :UserId, 
                  :PlayerId,
                  :Certified, 
                  :_60YardDash, 
                  :_5ConeFootwork, 
                  :ShuttleRun, 
                  :Rebounder10, 
                  :GoalShot10, 
                  :Accuracy50, 
                  :VelocityThrow
                )";

                $stmt = $PDO->prepare($sql);
                $stmt->bindParam('Created', $created, PDO::PARAM_STR);
                $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                $stmt->bindParam('UserId', $userId, PDO::PARAM_INT);
                $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                $stmt->bindParam('Certified', $certified, PDO::PARAM_INT);
                $stmt->bindParam('_60YardDash', $lacrosse_60YardDash, PDO::PARAM_STR);
                $stmt->bindParam('_5ConeFootwork', $lacrosse_5ConeFootwork, PDO::PARAM_STR);
                $stmt->bindParam('ShuttleRun', $lacrosse_ShuttleRun, PDO::PARAM_STR);
                $stmt->bindParam('Rebounder10', $lacrosse_Rebounder10, PDO::PARAM_INT);
                $stmt->bindParam('GoalShot10', $lacrosse_GoalShot10, PDO::PARAM_INT);
                $stmt->bindParam('Accuracy50', $lacrosse_Accuracy50, PDO::PARAM_INT);
                $stmt->bindParam('VelocityThrow', $lacrosse_VelocityThrow, PDO::PARAM_INT);
                $stmt->execute();

            } catch (PDOException $e) {
                trigger_error($e->getMessage(), E_USER_ERROR);
            }//end try
        }

        // crossFit
        if (hasSportStat($_POST, 'crossFit')) {

            $statsAdded = true;

            $crossFit_ShuttleRun = isset($_POST['crossFit_ShuttleRun']) ? hmsuToDecimal(trim($_POST['crossFit_ShuttleRun'])) : '0.0'; // Time
            $crossFit_40YardDash = isset($_POST['crossFit_40YardDash']) ? hmsuToDecimal(trim($_POST['crossFit_40YardDash'])) : '0.0'; // Time
            $crossFit_5105ConeDrill = isset($_POST['crossFit_5105ConeDrill']) ? hmsuToDecimal(trim($_POST['crossFit_5105ConeDrill'])) : '0.0'; // Time
            $crossFit_3RMHang = isset($_POST['crossFit_3RMHang']) ? preg_replace("/[^0-9]/", "", $_POST['crossFit_3RMHang']) : '0'; // Count
            $crossFit_VerticalJump = isset($_POST['crossFit_VerticalJump']) ? preg_replace("/[^0-9.]/", "", $_POST['crossFit_VerticalJump']) : '0.0'; // Inch
            $crossFit_BroadJump = isset($_POST['crossFit_BroadJump']) ? preg_replace("/[^0-9.]/", "", $_POST['crossFit_BroadJump']) : '0.0'; // Inch
            $crossFit_PowerClean = isset($_POST['crossFit_PowerClean']) ? preg_replace("/[^0-9]/", "", $_POST['crossFit_PowerClean']) : '0'; // Count
            $crossFit_PullUps = isset($_POST['crossFit_PullUps']) ? preg_replace("/[^0-9]/", "", $_POST['crossFit_PullUps']) : '0'; // Count
            $crossFit_PushUps = isset($_POST['crossFit_PushUps']) ? preg_replace("/[^0-9]/", "", $_POST['crossFit_PushUps']) : '0'; // Count

            if ($crossFit_ShuttleRun === false) {
                $msgBox = alertBox("Format invalid for Shuttle Run (Cross Fit)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            }
            if ($crossFit_40YardDash === false) {
                $msgBox = alertBox("Format invalid for 40 Yard Dash (Cross Fit)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            }
            if ($crossFit_5105ConeDrill === false) {
                $msgBox = alertBox("Format invalid for 5-10-5 Cone Drill (Cross Fit)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            }
            if (!empty($crossFit_3RMHang) && $crossFit_3RMHang > 100) {
                $msgBox = alertBox("3 RM Hang (0-100) (Cross Fit)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            }
            // fix this for inches
            if (!empty($crossFit_VerticalJump) && $crossFit_VerticalJump > 99.99) {
                $msgBox = alertBox("Vertical Jump (0-99.99) (Cross Fit)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            }
            // fix this for inches
            if (!empty($crossFit_BroadJump) && $crossFit_BroadJump > 99.99) {
                $msgBox = alertBox("Broad Jump (0-99.99) (Cross Fit)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            }
            if (!empty($crossFit_PowerClean) && $crossFit_PowerClean > 100) {
                $msgBox = alertBox("Power Clean (0-100) (Cross Fit)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            }
            if (!empty($crossFit_PullUps) && $crossFit_PullUps > 65535) {
                $msgBox = alertBox("Pull Ups (0-65535) (Cross Fit)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            }
            if (!empty($crossFit_PushUps) && $crossFit_PushUps > 65535) {
                $msgBox = alertBox("Push Ups (0-65535) (Cross Fit)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            }

            // insert row for this sport
            try {
                $sql = "
                INSERT INTO `GameCrossFit` 
                (
                  `CrossFitId`, 
                  `Created`, 
                  `LastUpdated`,
                  `EntryDate`,
                  `UserId`, 
                  `PlayerId`,
                  `Certified`, 
                  `ShuttleRun`, 
                  `40YardDash`, 
                  `5105ConeDrill`, 
                  `3RMHang`, 
                  `VerticalJump`, 
                  `BroadJump`, 
                  `PowerClean`,
                  `PullUps`,
                  `PushUps`
                ) VALUES (
                  NULL, 
                  :Created, 
                  :LastUpdated, 
                  :EntryDate,
                  :UserId, 
                  :PlayerId,
                  :Certified, 
                  :ShuttleRun, 
                  :_40YardDash, 
                  :_5105ConeDrill, 
                  :_3RMHang, 
                  :VerticalJump, 
                  :BroadJump, 
                  :PowerClean,
                  :PullUps,
                  :PushUps
                )";

                $stmt = $PDO->prepare($sql);
                $stmt->bindParam('Created', $created, PDO::PARAM_STR);
                $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                $stmt->bindParam('UserId', $userId, PDO::PARAM_INT);
                $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                $stmt->bindParam('Certified', $certified, PDO::PARAM_INT);
                $stmt->bindParam('ShuttleRun', $crossFit_ShuttleRun, PDO::PARAM_STR);
                $stmt->bindParam('_40YardDash', $crossFit_40YardDash, PDO::PARAM_STR);
                $stmt->bindParam('_5105ConeDrill', $crossFit_5105ConeDrill, PDO::PARAM_STR);
                $stmt->bindParam('_3RMHang', $crossFit_3RMHang, PDO::PARAM_INT);
                $stmt->bindParam('VerticalJump', $crossFit_VerticalJump, PDO::PARAM_INT);
                $stmt->bindParam('BroadJump', $crossFit_BroadJump, PDO::PARAM_INT);
                $stmt->bindParam('PowerClean', $crossFit_PowerClean, PDO::PARAM_INT);
                $stmt->bindParam('PullUps', $crossFit_PullUps, PDO::PARAM_INT);
                $stmt->bindParam('PushUps', $crossFit_PushUps, PDO::PARAM_INT);
                $stmt->execute();

            } catch (PDOException $e) {
                trigger_error($e->getMessage(), E_USER_ERROR);
            }//end try
        }

        // swimming
        if (hasSportStat($_POST, 'swimming')) {

            $statsAdded = true;

            $swimming_25MFreestyle = isset($_POST['swimming_25MFreestyle']) ? hmsuToDecimal(trim($_POST['swimming_25MFreestyle'])) : '0.0'; // Time
            $swimming_25MBackStroke = isset($_POST['swimming_25MBackStroke']) ? hmsuToDecimal(trim($_POST['swimming_25MBackStroke'])) : '0.0'; // Time
            $swimming_25MBreastStroke = isset($_POST['swimming_25MBreastStroke']) ? hmsuToDecimal(trim($_POST['swimming_25MBreastStroke'])) : '0.0'; // Time
            $swimming_25MButterfly = isset($_POST['swimming_25MButterfly']) ? hmsuToDecimal(trim($_POST['swimming_25MButterfly'])) : '0.0'; // Time
            $swimming_50MFreestyle = isset($_POST['swimming_50MFreestyle']) ? hmsuToDecimal(trim($_POST['swimming_50MFreestyle'])) : '0.0'; // Time
            $swimming_50MBackStroke = isset($_POST['swimming_50MBackStroke']) ? hmsuToDecimal(trim($_POST['swimming_50MBackStroke'])) : '0.0'; // Time
            $swimming_50MBreastStroke = isset($_POST['swimming_50MBreastStroke']) ? hmsuToDecimal(trim($_POST['swimming_50MBreastStroke'])) : '0.0'; // Time
            $swimming_50MButterfly = isset($_POST['swimming_50MButterfly']) ? hmsuToDecimal(trim($_POST['swimming_50MButterfly'])) : '0.0'; // Time
            $swimming_100MFreestyle = isset($_POST['swimming_100MFreestyle']) ? hmsuToDecimal(trim($_POST['swimming_100MFreestyle'])) : '0.0'; // Time
            $swimming_100MBackStroke = isset($_POST['swimming_100MBackStroke']) ? hmsuToDecimal(trim($_POST['swimming_100MBackStroke'])) : '0.0'; // Time
            $swimming_100MBreastStroke = isset($_POST['swimming_100MBreastStroke']) ? hmsuToDecimal(trim($_POST['swimming_100MBreastStroke'])) : '0.0'; // Time
            $swimming_100MButterfly = isset($_POST['swimming_100MButterfly']) ? hmsuToDecimal(trim($_POST['swimming_100MButterfly'])) : '0.0'; // Time
            $swimming_100MIndividualMedley = isset($_POST['swimming_100MIndividualMedley']) ? hmsuToDecimal(trim($_POST['swimming_100MIndividualMedley'])) : '0.0'; // Time
            $swimming_200MIndividualMedley = isset($_POST['swimming_200MIndividualMedley']) ? hmsuToDecimal(trim($_POST['swimming_200MIndividualMedley'])) : '0.0'; // Time

            if ($swimming_25MFreestyle === false) {
                $msgBox = alertBox("Format invalid for 25 M. Freestyle (Swimming)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            }
            if ($swimming_25MBackStroke === false) {
                $msgBox = alertBox("Format invalid for 25 M. Back Stroke (Swimming)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            }
            if ($swimming_25MBreastStroke === false) {
                $msgBox = alertBox("Format invalid for 25 M. Breast Stroke (Swimming)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            }
            if ($swimming_25MButterfly === false) {
                $msgBox = alertBox("Format invalid for 25 M. Butterfly (Swimming)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            }
            if ($swimming_50MFreestyle === false) {
                $msgBox = alertBox("Format invalid for 50 M. Freestyle (Swimming)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            }
            if ($swimming_50MBackStroke === false) {
                $msgBox = alertBox("Format invalid for 50 M. Back Stroke (Swimming)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            }
            if ($swimming_50MBreastStroke === false) {
                $msgBox = alertBox("Format invalid for 50 M. Breast Stroke (Swimming)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            }
            if ($swimming_50MButterfly === false) {
                $msgBox = alertBox("Format invalid for 50 M. Butterfly (Swimming)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            }
            if ($swimming_100MFreestyle === false) {
                $msgBox = alertBox("Format invalid for 100 M. Freestyle (Swimming)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            }
            if ($swimming_100MBackStroke === false) {
                $msgBox = alertBox("Format invalid for 100 M. Back Stroke (Swimming)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            }
            if ($swimming_100MBreastStroke === false) {
                $msgBox = alertBox("Format invalid for 100 M. Breast Stroke (Swimming)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            }
            if ($swimming_100MButterfly === false) {
                $msgBox = alertBox("Format invalid for 100 M. Butterfly (Swimming)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            }
            if ($swimming_100MIndividualMedley === false) {
                $msgBox = alertBox("Format invalid for 100 M. Individual Medley (Swimming)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            }
            if ($swimming_200MIndividualMedley === false) {
                $msgBox = alertBox("Format invalid for 200 M. Individual Medley (Swimming)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            }

            // insert row for this sport
            try {
                $sql = "
                INSERT INTO `GameSwimming` 
                (
                  `SwimmingId`, 
                  `Created`, 
                  `LastUpdated`, 
                  `EntryDate`,
                  `UserId`, 
                  `PlayerId`,
                  `Certified`, 
                  `25MFreestyle`, 
                  `25MBackStroke`, 
                  `25MBreastStroke`, 
                  `25MButterfly`, 
                  `50MFreestyle`, 
                  `50MBackStroke`, 
                  `50MBreastStroke`,
                  `50MButterfly`,
                  `100MFreestyle`,
                  `100MBackStroke`,
                  `100MBreastStroke`,
                  `100MButterfly`,
                  `100MIndividualMedley`,
                  `200MIndividualMedley`
                ) VALUES (
                  NULL, 
                  :Created, 
                  :LastUpdated, 
                  :EntryDate,
                  :UserId, 
                  :PlayerId,
                  :Certified, 
                  :_25MFreestyle, 
                  :_25MBackStroke, 
                  :_25MBreastStroke, 
                  :_25MButterfly, 
                  :_50MFreestyle, 
                  :_50MBackStroke, 
                  :_50MBreastStroke,
                  :_50MButterfly,
                  :_100MFreestyle,
                  :_100MBackStroke,
                  :_100MBreastStroke,
                  :_100MButterfly,
                  :_100MIndividualMedley,
                  :_200MIndividualMedley
                )";

                $stmt = $PDO->prepare($sql);
                $stmt->bindParam('Created', $created, PDO::PARAM_STR);
                $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                $stmt->bindParam('UserId', $userId, PDO::PARAM_INT);
                $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                $stmt->bindParam('Certified', $certified, PDO::PARAM_INT);
                $stmt->bindParam('_25MFreestyle', $swimming_25MFreestyle, PDO::PARAM_STR);
                $stmt->bindParam('_25MBackStroke', $swimming_25MBackStroke, PDO::PARAM_STR);
                $stmt->bindParam('_25MBreastStroke', $swimming_25MBreastStroke, PDO::PARAM_STR);
                $stmt->bindParam('_25MButterfly', $swimming_25MButterfly, PDO::PARAM_STR);
                $stmt->bindParam('_50MFreestyle', $swimming_50MFreestyle, PDO::PARAM_STR);
                $stmt->bindParam('_50MBackStroke', $swimming_50MBackStroke, PDO::PARAM_STR);
                $stmt->bindParam('_50MBreastStroke', $swimming_50MBreastStroke, PDO::PARAM_STR);
                $stmt->bindParam('_50MButterfly', $swimming_50MButterfly, PDO::PARAM_STR);
                $stmt->bindParam('_100MFreestyle', $swimming_100MFreestyle, PDO::PARAM_STR);
                $stmt->bindParam('_100MBackStroke', $swimming_100MBackStroke, PDO::PARAM_STR);
                $stmt->bindParam('_100MBreastStroke', $swimming_100MBreastStroke, PDO::PARAM_STR);
                $stmt->bindParam('_100MButterfly', $swimming_100MButterfly, PDO::PARAM_STR);
                $stmt->bindParam('_100MIndividualMedley', $swimming_100MIndividualMedley, PDO::PARAM_STR);
                $stmt->bindParam('_200MIndividualMedley', $swimming_200MIndividualMedley, PDO::PARAM_STR);
                $stmt->execute();

            } catch (PDOException $e) {
                trigger_error($e->getMessage(), E_USER_ERROR);
            }//end try
        }
    }

    if (!$statsAdded) {
        $msgBox = alertBox("Please enter player stats for at least one sport.", "<i class='fa fa-times'></i>", "danger");
        echo json_encode(array('error' => $msgBox));
    } else {
        $msgBox = alertBox("Player stats added for $_entryDate", "<i class='fa fa-check-square-o'></i>", "success");
        echo json_encode(array('success' => $msgBox));
    }

}

