<?php


if (!isset($_SESSION['userId'])) {
    header('Location: index.php?page=login');
    exit;
}

header('Content-Type: application/json');


if (isset($_POST) && count($_POST) > 0) {

    $eventId = isset($_POST['eventId']) ? $_POST['eventId'] : '';
    $eventId = preg_replace("/[^0-9]/", "", $eventId);

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
          `Events`.`Rounds`
        FROM 
          `Events` 
        WHERE
          `Events`.`EventId` = :EventId";

        $stmt = $PDO->prepare($sql);
        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
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

    $playerToken = isset($_POST['token']) ? trim($_POST['token']) : '';
    $playerToken = filter_var($playerToken, FILTER_SANITIZE_STRING);

    $playerId = null;

    $statsUpdated = false;

    $created = date('Y-m-d H:i:s');
    $lastUpdated = date('Y-m-d H:i:s');
    $certified = '1'; // 0=un-certified, 1=certified

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
        WHERE
          `Players`.`Token` = :Token
        AND 
          `Players`.`EventId` = :EventId
        AND 
          `Players`.`IsActive` = 1";

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


    if ($count > 0) {

        // Baseball
        if (array_key_exists('baseball_VelocityMound', $_POST)) {
            $x = 0;
            $error = false;
            for ($i = 1; $i <= $eventRounds; $i++) {
                $baseball_VelocityMound = isset($_POST['baseball_VelocityMound'][$x]) ? preg_replace("/[^0-9]/", "", $_POST['baseball_VelocityMound'][$x]) : '0'; // MPH
                if (!empty($baseball_VelocityMound) && $baseball_VelocityMound > 255) {
                    $error = true;
                } else {

                    // UPDATE scoring

                    try {
                        $sql = "
                        UPDATE 
                          `GameBaseball` 
                        SET
                          `GameBaseball`.`LastUpdated` = :LastUpdated,
                          `GameBaseball`.`EntryDate` = :EntryDate,
                          `GameBaseball`.`VelocityMound` = :VelocityMound
                        WHERE
                          `GameBaseball`.`PlayerId` = :PlayerId
                        AND 
                          `GameBaseball`.`EventId` = :EventId
                        AND 
                          `GameBaseball`.`Round` = :Round";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                        $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                        $stmt->bindParam('VelocityMound', $baseball_VelocityMound, PDO::PARAM_INT);
                        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                        $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                        $stmt->execute();

                    } catch (PDOException $e) {
                        trigger_error($e->getMessage(), E_USER_ERROR);
                    }//end try
                }
                $x++;
            }
            if ($error) {
                $msgBox = alertBox("Velocity Mound (0-255) (Baseball)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            } else {
                $statsUpdated = true;
            }
        } // end if

        if (array_key_exists('baseball_VelocityOutfield', $_POST)) {
            $x = 0;
            $error = false;
            for ($i = 1; $i <= $eventRounds; $i++) {
                $baseball_VelocityOutfield = isset($_POST['baseball_VelocityOutfield'][$x]) ? preg_replace("/[^0-9]/", "", $_POST['baseball_VelocityOutfield'][$x]) : '0'; // MPH
                if (!empty($baseball_VelocityOutfield) && $baseball_VelocityOutfield > 255) {
                    $error = true;
                } else {

                    // UPDATE scoring

                    try {
                        $sql = "
                        UPDATE 
                          `GameBaseball` 
                        SET
                          `GameBaseball`.`LastUpdated` = :LastUpdated,
                          `GameBaseball`.`EntryDate` = :EntryDate,
                          `GameBaseball`.`VelocityOutfield` = :VelocityOutfield
                        WHERE
                          `GameBaseball`.`PlayerId` = :PlayerId
                        AND 
                          `GameBaseball`.`EventId` = :EventId
                        AND 
                          `GameBaseball`.`Round` = :Round";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                        $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                        $stmt->bindParam('VelocityOutfield', $baseball_VelocityOutfield, PDO::PARAM_INT);
                        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                        $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                        $stmt->execute();

                    } catch (PDOException $e) {
                        trigger_error($e->getMessage(), E_USER_ERROR);
                    }//end try
                }
                $x++;
            }
            if ($error) {
                $msgBox = alertBox("Velocity Outfield (0-255) (Baseball)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            } else {
                $statsUpdated = true;
            }
        } // end if

        if (array_key_exists('baseball_VelocityInfield', $_POST)) {
            $x = 0;
            $error = false;
            for ($i = 1; $i <= $eventRounds; $i++) {
                $baseball_VelocityInfield = isset($_POST['baseball_VelocityInfield'][$x]) ? preg_replace("/[^0-9]/", "", $_POST['baseball_VelocityInfield'][$x]) : '0'; // MPH
                if (!empty($baseball_VelocityInfield) && $baseball_VelocityInfield > 255) {
                    $error = true;
                } else {

                    // UPDATE scoring

                    try {
                        $sql = "
                            UPDATE 
                              `GameBaseball` 
                            SET
                              `GameBaseball`.`LastUpdated` = :LastUpdated,
                              `GameBaseball`.`EntryDate` = :EntryDate,
                              `GameBaseball`.`VelocityInfield` = :VelocityInfield
                            WHERE
                              `GameBaseball`.`PlayerId` = :PlayerId
                            AND 
                              `GameBaseball`.`EventId` = :EventId
                            AND 
                              `GameBaseball`.`Round` = :Round";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                        $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                        $stmt->bindParam('VelocityInfield', $baseball_VelocityInfield, PDO::PARAM_INT);
                        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                        $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                        $stmt->execute();

                    } catch (PDOException $e) {
                        trigger_error($e->getMessage(), E_USER_ERROR);
                    }//end try
                }
                $x++;
            }
            if ($error) {
                $msgBox = alertBox("Velocity Infield (0-255) (Baseball)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            } else {
                $statsUpdated = true;
            }
        } // end if

        if (array_key_exists('baseball_SwingVelocity', $_POST)) {
            $x = 0;
            $error = false;
            for ($i = 1; $i <= $eventRounds; $i++) {
                $baseball_SwingVelocity = isset($_POST['baseball_SwingVelocity'][$x]) ? preg_replace("/[^0-9]/", "", $_POST['baseball_SwingVelocity'][$x]) : '0'; // MPH
                if (!empty($baseball_SwingVelocity) && $baseball_SwingVelocity > 255) {
                    $error = true;
                } else {

                    // UPDATE scoring

                    try {
                        $sql = "
                            UPDATE 
                              `GameBaseball` 
                            SET
                              `GameBaseball`.`LastUpdated` = :LastUpdated,
                              `GameBaseball`.`EntryDate` = :EntryDate,
                              `GameBaseball`.`SwingVelocity` = :SwingVelocity
                            WHERE
                              `GameBaseball`.`PlayerId` = :PlayerId
                            AND 
                              `GameBaseball`.`EventId` = :EventId
                            AND 
                              `GameBaseball`.`Round` = :Round";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                        $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                        $stmt->bindParam('SwingVelocity', $baseball_SwingVelocity, PDO::PARAM_STR);
                        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                        $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                        $stmt->execute();

                    } catch (PDOException $e) {
                        trigger_error($e->getMessage(), E_USER_ERROR);
                    }//end try
                }
                $x++;
            }
            if ($error) {
                $msgBox = alertBox("Swing Velocity (0-255) (Baseball)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            } else {
                $statsUpdated = true;
            }
        } // end if

        if (array_key_exists('baseball_60YardDash', $_POST)) {
            $x = 0;
            $error = false;
            for ($i = 1; $i <= $eventRounds; $i++) {
                $baseball_60YardDash = isset($_POST['baseball_60YardDash'][$x]) ? hmsuToDecimal(trim($_POST['baseball_60YardDash'][$x])) : '0.0'; // Time
                if ($baseball_60YardDash === false) {
                    $error = true;
                } else {

                    // UPDATE scoring

                    try {
                        $sql = "
                            UPDATE 
                              `GameBaseball` 
                            SET
                              `GameBaseball`.`LastUpdated` = :LastUpdated,
                              `GameBaseball`.`EntryDate` = :EntryDate,
                              `GameBaseball`.`60YardDash` = :_60YardDash
                            WHERE
                              `GameBaseball`.`PlayerId` = :PlayerId
                            AND 
                              `GameBaseball`.`EventId` = :EventId
                            AND 
                              `GameBaseball`.`Round` = :Round";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                        $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                        $stmt->bindParam('_60YardDash', $baseball_60YardDash, PDO::PARAM_STR);
                        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                        $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                        $stmt->execute();

                    } catch (PDOException $e) {
                        trigger_error($e->getMessage(), E_USER_ERROR);
                    }//end try
                }
                $x++;
            }
            if ($error) {
                $msgBox = alertBox("Format invalid for 60 Yard Dash (Baseball)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            } else {
                $statsUpdated = true;
            }
        } // end if

        if (array_key_exists('baseball_CatcherPop', $_POST)) {
            $x = 0;
            $error = false;
            for ($i = 1; $i <= $eventRounds; $i++) {
                $baseball_CatcherPop = isset($_POST['baseball_CatcherPop'][$x]) ? hmsuToDecimal(trim($_POST['baseball_CatcherPop'][$x])) : '0.0'; // Time
                if ($baseball_CatcherPop === false) {
                    $error = true;
                } else {

                    // UPDATE scoring

                    try {
                        $sql = "
                            UPDATE 
                              `GameBaseball` 
                            SET
                              `GameBaseball`.`LastUpdated` = :LastUpdated,
                              `GameBaseball`.`EntryDate` = :EntryDate,
                              `GameBaseball`.`CatcherPop` = :CatcherPop
                            WHERE
                              `GameBaseball`.`PlayerId` = :PlayerId
                            AND 
                              `GameBaseball`.`EventId` = :EventId
                            AND 
                              `GameBaseball`.`Round` = :Round";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                        $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                        $stmt->bindParam('CatcherPop', $baseball_CatcherPop, PDO::PARAM_STR);
                        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                        $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                        $stmt->execute();

                    } catch (PDOException $e) {
                        trigger_error($e->getMessage(), E_USER_ERROR);
                    }//end try
                }
                $x++;
            }
            if ($error) {
                $msgBox = alertBox("Format invalid for Catcher Pop (Baseball)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            } else {
                $statsUpdated = true;
            }
        } // end if

        if (array_key_exists('baseball_CatcherRelease', $_POST)) {
            $x = 0;
            $error = false;
            for ($i = 1; $i <= $eventRounds; $i++) {
                $baseball_CatcherRelease = isset($_POST['baseball_CatcherRelease'][$x]) ? hmsuToDecimal(trim($_POST['baseball_CatcherRelease'][$x])) : '0.0'; // Time
                if ($baseball_CatcherRelease === false) {
                    $error = true;
                } else {

                    // UPDATE scoring

                    try {
                        $sql = "
                            UPDATE 
                              `GameBaseball` 
                            SET
                              `GameBaseball`.`LastUpdated` = :LastUpdated,
                              `GameBaseball`.`EntryDate` = :EntryDate,
                              `GameBaseball`.`CatcherRelease` = :CatcherRelease
                            WHERE
                              `GameBaseball`.`PlayerId` = :PlayerId
                            AND 
                              `GameBaseball`.`EventId` = :EventId
                            AND 
                              `GameBaseball`.`Round` = :Round";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                        $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                        $stmt->bindParam('CatcherRelease', $baseball_CatcherRelease, PDO::PARAM_STR);
                        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                        $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                        $stmt->execute();

                    } catch (PDOException $e) {
                        trigger_error($e->getMessage(), E_USER_ERROR);
                    }//end try
                }
                $x++;
            }
            if ($error) {
                $msgBox = alertBox("Format invalid for Catcher Release (Baseball)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            } else {
                $statsUpdated = true;
            }
        } // end if

        if (array_key_exists('baseball_TeeVelocity', $_POST)) {
            $x = 0;
            $error = false;
            for ($i = 1; $i <= $eventRounds; $i++) {
                $baseball_TeeVelocity = isset($_POST['baseball_TeeVelocity'][$x]) ? preg_replace("/[^0-9]/", "", $_POST['baseball_TeeVelocity'][$x]) : '0'; // MPH
                if (!empty($baseball_TeeVelocity) && $baseball_TeeVelocity > 255) {
                    $error = true;
                } else {

                    // UPDATE scoring

                    try {
                        $sql = "
                            UPDATE 
                              `GameBaseball` 
                            SET
                              `GameBaseball`.`LastUpdated` = :LastUpdated,
                              `GameBaseball`.`EntryDate` = :EntryDate,
                              `GameBaseball`.`TeeVelocity` = :TeeVelocity
                            WHERE
                              `GameBaseball`.`PlayerId` = :PlayerId
                            AND 
                              `GameBaseball`.`EventId` = :EventId
                            AND 
                              `GameBaseball`.`Round` = :Round";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                        $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                        $stmt->bindParam('TeeVelocity', $baseball_TeeVelocity, PDO::PARAM_INT);
                        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                        $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                        $stmt->execute();

                    } catch (PDOException $e) {
                        trigger_error($e->getMessage(), E_USER_ERROR);
                    }//end try
                }
                $x++;
            }
            if ($error) {
                $msgBox = alertBox("Tee Velocity (0-255) (Baseball)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            } else {
                $statsUpdated = true;
            }
        } // end if

        // Fast Pitch
        if (array_key_exists('fastPitch_VelocityMound', $_POST)) {
            $x = 0;
            $error = false;
            for ($i = 1; $i <= $eventRounds; $i++) {
                $fastPitch_VelocityMound = isset($_POST['fastPitch_VelocityMound'][$x]) ? preg_replace("/[^0-9]/", "", $_POST['fastPitch_VelocityMound'][$x]) : '0'; // MPH
                if (!empty($fastPitch_VelocityMound) && $fastPitch_VelocityMound > 255) {
                    $error = true;
                } else {

                    // UPDATE scoring

                    try {
                        $sql = "
                        UPDATE 
                          `GameFastPitch` 
                        SET
                          `GameFastPitch`.`LastUpdated` = :LastUpdated,
                          `GameFastPitch`.`EntryDate` = :EntryDate,
                          `GameFastPitch`.`VelocityMound` = :VelocityMound
                        WHERE
                          `GameFastPitch`.`PlayerId` = :PlayerId
                        AND 
                          `GameFastPitch`.`EventId` = :EventId
                        AND 
                          `GameFastPitch`.`Round` = :Round";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                        $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                        $stmt->bindParam('VelocityMound', $fastPitch_VelocityMound, PDO::PARAM_INT);
                        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                        $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                        $stmt->execute();

                    } catch (PDOException $e) {
                        trigger_error($e->getMessage(), E_USER_ERROR);
                    }//end try
                }
                $x++;
            }
            if ($error) {
                $msgBox = alertBox("Velocity Mound (0-255) (Fast Pitch)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            } else {
                $statsUpdated = true;
            }
        } // end if

        if (array_key_exists('fastPitch_VelocityOutfield', $_POST)) {
            $x = 0;
            $error = false;
            for ($i = 1; $i <= $eventRounds; $i++) {
                $fastPitch_VelocityOutfield = isset($_POST['fastPitch_VelocityOutfield'][$x]) ? preg_replace("/[^0-9]/", "", $_POST['fastPitch_VelocityOutfield'][$x]) : '0'; // MPH
                if (!empty($fastPitch_VelocityOutfield) && $fastPitch_VelocityOutfield > 255) {
                    $error = true;
                } else {

                    // UPDATE scoring

                    try {
                        $sql = "
                            UPDATE 
                              `GameFastPitch` 
                            SET
                              `GameFastPitch`.`LastUpdated` = :LastUpdated,
                              `GameFastPitch`.`EntryDate` = :EntryDate,
                              `GameFastPitch`.`VelocityOutfield` = :VelocityOutfield
                            WHERE
                              `GameFastPitch`.`PlayerId` = :PlayerId
                            AND 
                              `GameFastPitch`.`EventId` = :EventId
                            AND 
                              `GameFastPitch`.`Round` = :Round";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                        $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                        $stmt->bindParam('VelocityOutfield', $fastPitch_VelocityOutfield, PDO::PARAM_INT);
                        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                        $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                        $stmt->execute();

                    } catch (PDOException $e) {
                        trigger_error($e->getMessage(), E_USER_ERROR);
                    }//end try

                }
                $x++;
            }
            if ($error) {
                $msgBox = alertBox("Velocity Outfield (0-255) (Fast Pitch)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            } else {
                $statsUpdated = true;
            }
        } // end if

        if (array_key_exists('fastPitch_VelocityInfield', $_POST)) {
            $x = 0;
            $error = false;
            for ($i = 1; $i <= $eventRounds; $i++) {
                $fastPitch_VelocityInfield = isset($_POST['fastPitch_VelocityInfield'][$x]) ? preg_replace("/[^0-9]/", "", $_POST['fastPitch_VelocityInfield'][$x]) : '0'; // MPH
                if (!empty($fastPitch_VelocityInfield) && $fastPitch_VelocityInfield > 255) {
                    $error = true;
                } else {

                    // UPDATE scoring

                    try {
                        $sql = "
                            UPDATE 
                              `GameFastPitch` 
                            SET
                              `GameFastPitch`.`LastUpdated` = :LastUpdated,
                              `GameFastPitch`.`EntryDate` = :EntryDate,
                              `GameFastPitch`.`VelocityInfield` = :VelocityInfield
                            WHERE
                              `GameFastPitch`.`PlayerId` = :PlayerId
                            AND 
                              `GameFastPitch`.`EventId` = :EventId
                            AND 
                              `GameFastPitch`.`Round` = :Round";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                        $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                        $stmt->bindParam('VelocityInfield', $fastPitch_VelocityInfield, PDO::PARAM_INT);
                        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                        $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                        $stmt->execute();

                    } catch (PDOException $e) {
                        trigger_error($e->getMessage(), E_USER_ERROR);
                    }//end try
                }
                $x++;
            }
            if ($error) {
                $msgBox = alertBox("Velocity Infield (0-255) (Fast Pitch)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            } else {
                $statsUpdated = true;
            }
        } // end if

        if (array_key_exists('fastPitch_SwingVelocity', $_POST)) {
            $x = 0;
            $error = false;
            for ($i = 1; $i <= $eventRounds; $i++) {
                $fastPitch_SwingVelocity = isset($_POST['fastPitch_SwingVelocity'][$x]) ? preg_replace("/[^0-9]/", "", $_POST['fastPitch_SwingVelocity'][$x]) : '0'; // MPH
                if (!empty($fastPitch_SwingVelocity) && $fastPitch_SwingVelocity > 255) {
                    $error = true;
                } else {

                    // UPDATE scoring

                    try {
                        $sql = "
                            UPDATE 
                              `GameFastPitch` 
                            SET
                              `GameFastPitch`.`LastUpdated` = :LastUpdated,
                              `GameFastPitch`.`EntryDate` = :EntryDate,
                              `GameFastPitch`.`SwingVelocity` = :SwingVelocity
                            WHERE
                              `GameFastPitch`.`PlayerId` = :PlayerId
                            AND 
                              `GameFastPitch`.`EventId` = :EventId
                            AND 
                              `GameFastPitch`.`Round` = :Round";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                        $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                        $stmt->bindParam('SwingVelocity', $fastPitch_SwingVelocity, PDO::PARAM_STR);
                        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                        $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                        $stmt->execute();

                    } catch (PDOException $e) {
                        trigger_error($e->getMessage(), E_USER_ERROR);
                    }//end try
                }
                $x++;
            }
            if ($error) {
                $msgBox = alertBox("Swing Velocity (0-255) (Fast Pitch)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            } else {
                $statsUpdated = true;
            }
        } // end if

        if (array_key_exists('fastPitch_60YardDash', $_POST)) {
            $x = 0;
            $error = false;
            for ($i = 1; $i <= $eventRounds; $i++) {
                $fastPitch_60YardDash = isset($_POST['fastPitch_60YardDash'][$x]) ? hmsuToDecimal(trim($_POST['fastPitch_60YardDash'][$x])) : '0.0'; // Time
                if ($fastPitch_60YardDash === false) {
                    $error = true;
                } else {

                    // UPDATE scoring

                    try {
                        $sql = "
                            UPDATE 
                              `GameFastPitch` 
                            SET
                              `GameFastPitch`.`LastUpdated` = :LastUpdated,
                              `GameFastPitch`.`EntryDate` = :EntryDate,
                              `GameFastPitch`.`60YardDash` = :_60YardDash
                            WHERE
                              `GameFastPitch`.`PlayerId` = :PlayerId
                            AND 
                              `GameFastPitch`.`EventId` = :EventId
                            AND 
                              `GameFastPitch`.`Round` = :Round";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                        $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                        $stmt->bindParam('_60YardDash', $fastPitch_60YardDash, PDO::PARAM_STR);
                        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                        $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                        $stmt->execute();

                    } catch (PDOException $e) {
                        trigger_error($e->getMessage(), E_USER_ERROR);
                    }//end try
                }
                $x++;
            }
            if ($error) {
                $msgBox = alertBox("Format invalid for 60 Yard Dash (Fast Pitch)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            } else {
                $statsUpdated = true;
            }
        } // end if

        if (array_key_exists('fastPitch_CatcherPop', $_POST)) {
            $x = 0;
            $error = false;
            for ($i = 1; $i <= $eventRounds; $i++) {
                $fastPitch_CatcherPop = isset($_POST['fastPitch_CatcherPop'][$x]) ? hmsuToDecimal(trim($_POST['fastPitch_CatcherPop'][$x])) : '0.0'; // Time
                if ($fastPitch_CatcherPop === false) {
                    $error = true;
                } else {

                    // UPDATE scoring

                    try {
                        $sql = "
                            UPDATE 
                              `GameFastPitch` 
                            SET
                              `GameFastPitch`.`LastUpdated` = :LastUpdated,
                              `GameFastPitch`.`EntryDate` = :EntryDate,
                              `GameFastPitch`.`CatcherPop` = :CatcherPop
                            WHERE
                              `GameFastPitch`.`PlayerId` = :PlayerId
                            AND 
                              `GameFastPitch`.`EventId` = :EventId
                            AND 
                              `GameFastPitch`.`Round` = :Round";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                        $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                        $stmt->bindParam('CatcherPop', $fastPitch_CatcherPop, PDO::PARAM_STR);
                        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                        $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                        $stmt->execute();

                    } catch (PDOException $e) {
                        trigger_error($e->getMessage(), E_USER_ERROR);
                    }//end try
                }
                $x++;
            }
            if ($error) {
                $msgBox = alertBox("Format invalid for Catcher Pop (Fast Pitch)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            } else {
                $statsUpdated = true;
            }
        } // end if

        if (array_key_exists('fastPitch_CatcherRelease', $_POST)) {
            $x = 0;
            $error = false;
            for ($i = 1; $i <= $eventRounds; $i++) {
                $fastPitch_CatcherRelease = isset($_POST['fastPitch_CatcherRelease'][$x]) ? hmsuToDecimal(trim($_POST['fastPitch_CatcherRelease'][$x])) : '0.0'; // Time
                if ($fastPitch_CatcherRelease === false) {
                    $error = true;
                } else {

                    // UPDATE scoring

                    try {
                        $sql = "
                            UPDATE 
                              `GameFastPitch` 
                            SET
                              `GameFastPitch`.`LastUpdated` = :LastUpdated,
                              `GameFastPitch`.`EntryDate` = :EntryDate,
                              `GameFastPitch`.`CatcherRelease` = :CatcherRelease
                            WHERE
                              `GameFastPitch`.`PlayerId` = :PlayerId
                            AND 
                              `GameFastPitch`.`EventId` = :EventId
                            AND 
                              `GameFastPitch`.`Round` = :Round";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                        $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                        $stmt->bindParam('CatcherRelease', $fastPitch_CatcherRelease, PDO::PARAM_STR);
                        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                        $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                        $stmt->execute();

                    } catch (PDOException $e) {
                        trigger_error($e->getMessage(), E_USER_ERROR);
                    }//end try
                }
                $x++;
            }
            if ($error) {
                $msgBox = alertBox("Format invalid for Catcher Release Fast Pitch)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            } else {
                $statsUpdated = true;
            }
        } // end if

        if (array_key_exists('fastPitch_TeeVelocity', $_POST)) {
            $x = 0;
            $error = false;
            for ($i = 1; $i <= $eventRounds; $i++) {
                $fastPitch_TeeVelocity = isset($_POST['fastPitch_TeeVelocity'][$x]) ? preg_replace("/[^0-9]/", "", $_POST['fastPitch_TeeVelocity'][$x]) : '0'; // MPH
                if (!empty($fastPitch_TeeVelocity) && $fastPitch_TeeVelocity > 255) {
                    $error = true;
                } else {

                    // UPDATE scoring

                    try {
                        $sql = "
                            UPDATE 
                              `GameFastPitch` 
                            SET
                              `GameFastPitch`.`LastUpdated` = :LastUpdated,
                              `GameFastPitch`.`EntryDate` = :EntryDate,
                              `GameFastPitch`.`TeeVelocity` = :TeeVelocity
                            WHERE
                              `GameFastPitch`.`PlayerId` = :PlayerId
                            AND 
                              `GameFastPitch`.`EventId` = :EventId
                            AND 
                              `GameFastPitch`.`Round` = :Round";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                        $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                        $stmt->bindParam('TeeVelocity', $fastPitch_TeeVelocity, PDO::PARAM_INT);
                        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                        $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                        $stmt->execute();

                    } catch (PDOException $e) {
                        trigger_error($e->getMessage(), E_USER_ERROR);
                    }//end try
                }
                $x++;
            }
            if ($error) {
                $msgBox = alertBox("Tee Velocity (0-255) (Fast Pitch)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            } else {
                $statsUpdated = true;
            }
        } // end if


        // Lacrosse
        if (array_key_exists('lacrosse_60YardDash', $_POST)) {
            $x = 0;
            $error = false;
            for ($i = 1; $i <= $eventRounds; $i++) {
                $lacrosse_60YardDash = isset($_POST['lacrosse_60YardDash'][$x]) ? hmsuToDecimal(trim($_POST['lacrosse_60YardDash'][$x])) : '0.0'; // Time
                if ($lacrosse_60YardDash === false) {
                    $error = true;
                } else {

                    // UPDATE scoring

                    try {
                        $sql = "
                            UPDATE 
                              `GameLacrosse` 
                            SET
                              `GameLacrosse`.`LastUpdated` = :LastUpdated,
                              `GameLacrosse`.`EntryDate` = :EntryDate,
                              `GameLacrosse`.`60YardDash` = :_60YardDash
                            WHERE
                              `GameLacrosse`.`PlayerId` = :PlayerId
                            AND 
                              `GameLacrosse`.`EventId` = :EventId
                            AND 
                              `GameLacrosse`.`Round` = :Round";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                        $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                        $stmt->bindParam('_60YardDash', $lacrosse_60YardDash, PDO::PARAM_STR);
                        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                        $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                        $stmt->execute();

                    } catch (PDOException $e) {
                        trigger_error($e->getMessage(), E_USER_ERROR);
                    }//end try
                }
                $x++;
            }
            if ($error) {
                $msgBox = alertBox("Format invalid for 60 Yard Dash (Lacrosse)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            } else {
                $statsUpdated = true;
            }
        } // end if

        if (array_key_exists('lacrosse_5ConeFootwork', $_POST)) {
            $x = 0;
            $error = false;
            for ($i = 1; $i <= $eventRounds; $i++) {
                $lacrosse_5ConeFootwork = isset($_POST['lacrosse_5ConeFootwork'][$x]) ? hmsuToDecimal(trim($_POST['lacrosse_5ConeFootwork'][$x])) : '0.0'; // Time
                if ($lacrosse_5ConeFootwork === false) {
                    $error = true;
                } else {

                    // UPDATE scoring

                    try {
                        $sql = "
                            UPDATE 
                              `GameLacrosse` 
                            SET
                              `GameLacrosse`.`LastUpdated` = :LastUpdated,
                              `GameLacrosse`.`EntryDate` = :EntryDate,
                              `GameLacrosse`.`5ConeFootwork` = :_5ConeFootwork
                            WHERE
                              `GameLacrosse`.`PlayerId` = :PlayerId
                            AND 
                              `GameLacrosse`.`EventId` = :EventId
                            AND 
                              `GameLacrosse`.`Round` = :Round";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                        $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                        $stmt->bindParam('_5ConeFootwork', $lacrosse_5ConeFootwork, PDO::PARAM_STR);
                        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                        $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                        $stmt->execute();

                    } catch (PDOException $e) {
                        trigger_error($e->getMessage(), E_USER_ERROR);
                    }//end try
                }
                $x++;
            }
            if ($error) {
                $msgBox = alertBox("Format invalid for 5 Cone Footwork (Lacrosse)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            } else {
                $statsUpdated = true;
            }
        } // end if

        if (array_key_exists('lacrosse_ShuttleRun', $_POST)) {
            $x = 0;
            $error = false;
            for ($i = 1; $i <= $eventRounds; $i++) {
                $lacrosse_ShuttleRun = isset($_POST['lacrosse_ShuttleRun'][$x]) ? hmsuToDecimal(trim($_POST['lacrosse_ShuttleRun'][$x])) : '0.0'; // Time
                if ($lacrosse_ShuttleRun === false) {
                    $error = true;
                } else {

                    // UPDATE scoring

                    try {
                        $sql = "
                            UPDATE 
                              `GameLacrosse` 
                            SET
                              `GameLacrosse`.`LastUpdated` = :LastUpdated,
                              `GameLacrosse`.`EntryDate` = :EntryDate,
                              `GameLacrosse`.`ShuttleRun` = :ShuttleRun
                            WHERE
                              `GameLacrosse`.`PlayerId` = :PlayerId
                            AND 
                              `GameLacrosse`.`EventId` = :EventId
                            AND 
                              `GameLacrosse`.`Round` = :Round";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                        $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                        $stmt->bindParam('ShuttleRun', $lacrosse_ShuttleRun, PDO::PARAM_STR);
                        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                        $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                        $stmt->execute();

                    } catch (PDOException $e) {
                        trigger_error($e->getMessage(), E_USER_ERROR);
                    }//end try
                }
                $x++;
            }
            if ($error) {
                $msgBox = alertBox("Format invalid for Shuttle Run (Lacrosse)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            } else {
                $statsUpdated = true;
            }
        } // end if

        if (array_key_exists('lacrosse_Rebounder10', $_POST)) {
            $x = 0;
            $error = false;
            for ($i = 1; $i <= $eventRounds; $i++) {
                $lacrosse_Rebounder10 = isset($_POST['lacrosse_Rebounder10'][$x]) ? preg_replace("/[^0-9]/", "", $_POST['lacrosse_Rebounder10'][$x]) : '0'; // Count
                if (!empty($lacrosse_Rebounder10) && $lacrosse_Rebounder10 > 65535) {
                    $error = true;
                } else {

                    // UPDATE scoring

                    try {
                        $sql = "
                            UPDATE 
                              `GameLacrosse` 
                            SET
                              `GameLacrosse`.`LastUpdated` = :LastUpdated,
                              `GameLacrosse`.`EntryDate` = :EntryDate,
                              `GameLacrosse`.`Rebounder10` = :Rebounder10
                            WHERE
                              `GameLacrosse`.`PlayerId` = :PlayerId
                            AND 
                              `GameLacrosse`.`EventId` = :EventId
                            AND 
                              `GameLacrosse`.`Round` = :Round";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                        $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                        $stmt->bindParam('Rebounder10', $lacrosse_Rebounder10, PDO::PARAM_INT);
                        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                        $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                        $stmt->execute();

                    } catch (PDOException $e) {
                        trigger_error($e->getMessage(), E_USER_ERROR);
                    }//end try
                }
                $x++;
            }
            if ($error) {
                $msgBox = alertBox("Rebounder 10 (0-65535) (Lacrosse)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            } else {
                $statsUpdated = true;
            }
        } // end if

        if (array_key_exists('lacrosse_GoalShot10', $_POST)) {
            $x = 0;
            $error = false;
            for ($i = 1; $i <= $eventRounds; $i++) {
                $lacrosse_GoalShot10 = isset($_POST['lacrosse_GoalShot10'][$x]) ? preg_replace("/[^0-9]/", "", $_POST['lacrosse_GoalShot10'][$x]) : '0'; // Count
                if (!empty($lacrosse_GoalShot10) && $lacrosse_GoalShot10 > 65535) {
                    $error = true;
                } else {

                    // UPDATE scoring

                    try {
                        $sql = "
                            UPDATE 
                              `GameLacrosse` 
                            SET
                              `GameLacrosse`.`LastUpdated` = :LastUpdated,
                              `GameLacrosse`.`EntryDate` = :EntryDate,
                              `GameLacrosse`.`GoalShot10` = :GoalShot10
                            WHERE
                              `GameLacrosse`.`PlayerId` = :PlayerId
                            AND 
                              `GameLacrosse`.`EventId` = :EventId
                            AND 
                              `GameLacrosse`.`Round` = :Round";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                        $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                        $stmt->bindParam('GoalShot10', $lacrosse_GoalShot10, PDO::PARAM_INT);
                        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                        $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                        $stmt->execute();

                    } catch (PDOException $e) {
                        trigger_error($e->getMessage(), E_USER_ERROR);
                    }//end try
                }
                $x++;
            }
            if ($error) {
                $msgBox = alertBox("Goal Shot 10 (0-65535) (Lacrosse)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            } else {
                $statsUpdated = true;
            }
        } // end if

        if (array_key_exists('lacrosse_Accuracy50', $_POST)) {
            $x = 0;
            $error = false;
            for ($i = 1; $i <= $eventRounds; $i++) {
                $lacrosse_Accuracy50 = isset($_POST['lacrosse_Accuracy50'][$x]) ? preg_replace("/[^0-9]/", "", $_POST['lacrosse_Accuracy50'][$x]) : '0'; // Count
                if (!empty($lacrosse_Accuracy50) && $lacrosse_Accuracy50 > 65535) {
                    $error = true;
                } else {

                    // UPDATE scoring

                    try {
                        $sql = "
                            UPDATE 
                              `GameLacrosse` 
                            SET
                              `GameLacrosse`.`LastUpdated` = :LastUpdated,
                              `GameLacrosse`.`EntryDate` = :EntryDate,
                              `GameLacrosse`.`Accuracy50` = :Accuracy50
                            WHERE
                              `GameLacrosse`.`PlayerId` = :PlayerId
                            AND 
                              `GameLacrosse`.`EventId` = :EventId
                            AND 
                              `GameLacrosse`.`Round` = :Round";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                        $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                        $stmt->bindParam('Accuracy50', $lacrosse_Accuracy50, PDO::PARAM_INT);
                        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                        $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                        $stmt->execute();

                    } catch (PDOException $e) {
                        trigger_error($e->getMessage(), E_USER_ERROR);
                    }//end try
                }
                $x++;
            }
            if ($error) {
                $msgBox = alertBox("Accuracy 50 (0-65535) (Lacrosse)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            } else {
                $statsUpdated = true;
            }
        } // end if

        if (array_key_exists('lacrosse_VelocityThrow', $_POST)) {
            $x = 0;
            $error = false;
            for ($i = 1; $i <= $eventRounds; $i++) {
                $lacrosse_VelocityThrow = isset($_POST['lacrosse_VelocityThrow'][$x]) ? preg_replace("/[^0-9]/", "", $_POST['lacrosse_VelocityThrow'][$x]) : '0'; // MPH
                if (!empty($lacrosse_VelocityThrow) && $lacrosse_VelocityThrow > 255) {
                    $error = true;
                } else {

                    // UPDATE scoring

                    try {
                        $sql = "
                            UPDATE 
                              `GameLacrosse` 
                            SET
                              `GameLacrosse`.`LastUpdated` = :LastUpdated,
                              `GameLacrosse`.`EntryDate` = :EntryDate,
                              `GameLacrosse`.`VelocityThrow` = :VelocityThrow
                            WHERE
                              `GameLacrosse`.`PlayerId` = :PlayerId
                            AND 
                              `GameLacrosse`.`EventId` = :EventId
                            AND 
                              `GameLacrosse`.`Round` = :Round";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                        $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                        $stmt->bindParam('VelocityThrow', $lacrosse_VelocityThrow, PDO::PARAM_INT);
                        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                        $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                        $stmt->execute();

                    } catch (PDOException $e) {
                        trigger_error($e->getMessage(), E_USER_ERROR);
                    }//end try
                }
                $x++;
            }
            if ($error) {
                $msgBox = alertBox("Velocity Throw (0-255) (Lacrosse)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            } else {
                $statsUpdated = true;
            }
        } // end if


        // Cross Fit
        if (array_key_exists('crossFit_ShuttleRun', $_POST)) {
            $x = 0;
            $error = false;
            for ($i = 1; $i <= $eventRounds; $i++) {
                $crossFit_ShuttleRun = isset($_POST['crossFit_ShuttleRun'][$x]) ? hmsuToDecimal(trim($_POST['crossFit_ShuttleRun'][$x])) : '0.0'; // Time
                if ($crossFit_ShuttleRun === false) {
                    $error = true;
                } else {

                    // UPDATE scoring

                    try {
                        $sql = "
                            UPDATE 
                              `GameCrossFit` 
                            SET
                              `GameCrossFit`.`LastUpdated` = :LastUpdated,
                              `GameCrossFit`.`EntryDate` = :EntryDate,
                              `GameCrossFit`.`ShuttleRun` = :ShuttleRun
                            WHERE
                              `GameCrossFit`.`PlayerId` = :PlayerId
                            AND 
                              `GameCrossFit`.`EventId` = :EventId
                            AND 
                              `GameCrossFit`.`Round` = :Round";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                        $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                        $stmt->bindParam('ShuttleRun', $crossFit_ShuttleRun, PDO::PARAM_STR);
                        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                        $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                        $stmt->execute();

                    } catch (PDOException $e) {
                        trigger_error($e->getMessage(), E_USER_ERROR);
                    }//end try
                }
                $x++;
            }
            if ($error) {
                $msgBox = alertBox("Format invalid for Shuttle Run (Cross Fit)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            } else {
                $statsUpdated = true;
            }
        } // end if

        if (array_key_exists('crossFit_40YardDash', $_POST)) {
            $x = 0;
            $error = false;
            for ($i = 1; $i <= $eventRounds; $i++) {
                $crossFit_40YardDash = isset($_POST['crossFit_40YardDash'][$x]) ? hmsuToDecimal(trim($_POST['crossFit_40YardDash'][$x])) : '0.0'; // Time
                if ($crossFit_40YardDash === false) {
                    $error = true;
                } else {

                    // UPDATE scoring

                    try {
                        $sql = "
                            UPDATE 
                              `GameCrossFit` 
                            SET
                              `GameCrossFit`.`LastUpdated` = :LastUpdated,
                              `GameCrossFit`.`EntryDate` = :EntryDate,
                              `GameCrossFit`.`40YardDash` = :_40YardDash
                            WHERE
                              `GameCrossFit`.`PlayerId` = :PlayerId
                            AND 
                              `GameCrossFit`.`EventId` = :EventId
                            AND 
                              `GameCrossFit`.`Round` = :Round";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                        $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                        $stmt->bindParam('_40YardDash', $crossFit_40YardDash, PDO::PARAM_STR);
                        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                        $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                        $stmt->execute();

                    } catch (PDOException $e) {
                        trigger_error($e->getMessage(), E_USER_ERROR);
                    }//end try
                }
                $x++;
            }
            if ($error) {
                $msgBox = alertBox("Format invalid for 40 Yard Dash (Cross Fit)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            } else {
                $statsUpdated = true;
            }
        } // end if

        if (array_key_exists('crossFit_5105ConeDrill', $_POST)) {
            $x = 0;
            $error = false;
            for ($i = 1; $i <= $eventRounds; $i++) {
                $crossFit_5105ConeDrill = isset($_POST['crossFit_5105ConeDrill'][$x]) ? hmsuToDecimal(trim($_POST['crossFit_5105ConeDrill'][$x])) : '0.0'; // Time
                if ($crossFit_5105ConeDrill === false) {
                    $error = true;
                } else {

                    // UPDATE scoring

                    try {
                        $sql = "
                            UPDATE 
                              `GameCrossFit` 
                            SET
                              `GameCrossFit`.`LastUpdated` = :LastUpdated,
                              `GameCrossFit`.`EntryDate` = :EntryDate,
                              `GameCrossFit`.`5105ConeDrill` = :_5105ConeDrill
                            WHERE
                              `GameCrossFit`.`PlayerId` = :PlayerId
                            AND 
                              `GameCrossFit`.`EventId` = :EventId
                            AND 
                              `GameCrossFit`.`Round` = :Round";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                        $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                        $stmt->bindParam('_5105ConeDrill', $crossFit_5105ConeDrill, PDO::PARAM_STR);
                        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                        $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                        $stmt->execute();

                    } catch (PDOException $e) {
                        trigger_error($e->getMessage(), E_USER_ERROR);
                    }//end try
                }
                $x++;
            }
            if ($error) {
                $msgBox = alertBox("5-10-5 Cone Drill  (0-100) (Cross Fit)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            } else {
                $statsUpdated = true;
            }
        } // end if

        if (array_key_exists('crossFit_3RMHang', $_POST)) {
            $x = 0;
            $error = false;
            for ($i = 1; $i <= $eventRounds; $i++) {
                $crossFit_3RMHang = isset($_POST['crossFit_3RMHang'][$x]) ? preg_replace("/[^0-9]/", "", $_POST['crossFit_3RMHang'][$x]) : '0'; // Count
                if (!empty($crossFit_3RMHang) && $crossFit_3RMHang > 100) {
                    $error = true;
                } else {

                    // UPDATE scoring

                    try {
                        $sql = "
                            UPDATE 
                              `GameCrossFit` 
                            SET
                              `GameCrossFit`.`LastUpdated` = :LastUpdated,
                              `GameCrossFit`.`EntryDate` = :EntryDate,
                              `GameCrossFit`.`3RMHang` = :_3RMHang
                            WHERE
                              `GameCrossFit`.`PlayerId` = :PlayerId
                            AND 
                              `GameCrossFit`.`EventId` = :EventId
                            AND 
                              `GameCrossFit`.`Round` = :Round";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                        $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                        $stmt->bindParam('_3RMHang', $crossFit_3RMHang, PDO::PARAM_INT);
                        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                        $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                        $stmt->execute();

                    } catch (PDOException $e) {
                        trigger_error($e->getMessage(), E_USER_ERROR);
                    }//end try
                }
                $x++;
            }
            if ($error) {
                $msgBox = alertBox("3 RM Hang (0-100) (Cross Fit)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            } else {
                $statsUpdated = true;
            }
        } // end if

        if (array_key_exists('crossFit_VerticalJump', $_POST)) {
            $x = 0;
            $error = false;
            for ($i = 1; $i <= $eventRounds; $i++) {
                $crossFit_VerticalJump = isset($_POST['crossFit_VerticalJump'][$x]) ? preg_replace("/[^0-9.]/", "", $_POST['crossFit_VerticalJump'][$x]) : '0.0'; // Inch
                // fix this for inches
                if (!empty($crossFit_VerticalJump) && $crossFit_VerticalJump > 99.99) {
                    $error = true;
                } else {

                    // UPDATE scoring

                    try {
                        $sql = "
                            UPDATE 
                              `GameCrossFit` 
                            SET
                              `GameCrossFit`.`LastUpdated` = :LastUpdated,
                              `GameCrossFit`.`EntryDate` = :EntryDate,
                              `GameCrossFit`.`VerticalJump` = :VerticalJump
                            WHERE
                              `GameCrossFit`.`PlayerId` = :PlayerId
                            AND 
                              `GameCrossFit`.`EventId` = :EventId
                            AND 
                              `GameCrossFit`.`Round` = :Round";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                        $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                        $stmt->bindParam('VerticalJump', $crossFit_VerticalJump, PDO::PARAM_STR);
                        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                        $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                        $stmt->execute();

                    } catch (PDOException $e) {
                        trigger_error($e->getMessage(), E_USER_ERROR);
                    }//end try
                }
                $x++;
            }
            if ($error) {
                $msgBox = alertBox("Vertical Jump (0-99.99) (Cross Fit)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            } else {
                $statsUpdated = true;
            }
        } // end if

        if (array_key_exists('crossFit_BroadJump', $_POST)) {
            $x = 0;
            $error = false;
            for ($i = 1; $i <= $eventRounds; $i++) {
                $crossFit_BroadJump = isset($_POST['crossFit_BroadJump'][$x]) ? preg_replace("/[^0-9.]/", "", $_POST['crossFit_BroadJump'][$x]) : '0.0'; // Inch
                // fix this for inches
                if (!empty($crossFit_BroadJump) && $crossFit_BroadJump > 99.99) {
                    $error = true;
                } else {

                    // UPDATE scoring

                    try {
                        $sql = "
                            UPDATE 
                              `GameCrossFit` 
                            SET
                              `GameCrossFit`.`LastUpdated` = :LastUpdated,
                              `GameCrossFit`.`EntryDate` = :EntryDate,
                              `GameCrossFit`.`BroadJump` = :BroadJump
                            WHERE
                              `GameCrossFit`.`PlayerId` = :PlayerId
                            AND 
                              `GameCrossFit`.`EventId` = :EventId
                            AND 
                              `GameCrossFit`.`Round` = :Round";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                        $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                        $stmt->bindParam('BroadJump', $crossFit_BroadJump, PDO::PARAM_STR);
                        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                        $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                        $stmt->execute();

                    } catch (PDOException $e) {
                        trigger_error($e->getMessage(), E_USER_ERROR);
                    }//end try
                }
                $x++;
            }
            if ($error) {
                $msgBox = alertBox("Broad Jump (0-99.99) (Cross Fit)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            } else {
                $statsUpdated = true;
            }
        } // end if

        if (array_key_exists('crossFit_PowerClean', $_POST)) {
            $x = 0;
            $error = false;
            for ($i = 1; $i <= $eventRounds; $i++) {
                $crossFit_PowerClean = isset($_POST['crossFit_PowerClean'][$x]) ? preg_replace("/[^0-9]/", "", $_POST['crossFit_PowerClean'][$x]) : '0'; // Count
                if (!empty($crossFit_PowerClean) && $crossFit_PowerClean > 100) {
                    $error = true;
                } else {

                    // UPDATE scoring

                    try {
                        $sql = "
                            UPDATE 
                              `GameCrossFit` 
                            SET
                              `GameCrossFit`.`LastUpdated` = :LastUpdated,
                              `GameCrossFit`.`EntryDate` = :EntryDate,
                              `GameCrossFit`.`PowerClean` = :PowerClean
                            WHERE
                              `GameCrossFit`.`PlayerId` = :PlayerId
                            AND 
                              `GameCrossFit`.`EventId` = :EventId
                            AND 
                              `GameCrossFit`.`Round` = :Round";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                        $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                        $stmt->bindParam('PowerClean', $crossFit_PowerClean, PDO::PARAM_INT);
                        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                        $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                        $stmt->execute();

                    } catch (PDOException $e) {
                        trigger_error($e->getMessage(), E_USER_ERROR);
                    }//end try
                }
                $x++;
            }
            if ($error) {
                $msgBox = alertBox("Power Clean (0-100) (Cross Fit)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            } else {
                $statsUpdated = true;
            }
        } // end if

        if (array_key_exists('crossFit_PullUps', $_POST)) {
            $x = 0;
            $error = false;
            for ($i = 1; $i <= $eventRounds; $i++) {
                $crossFit_PullUps = isset($_POST['crossFit_PullUps'][$x]) ? preg_replace("/[^0-9]/", "", $_POST['crossFit_PullUps'][$x]) : '0'; // Count
                if (!empty($crossFit_PullUps) && $crossFit_PullUps > 65535) {
                    $error = true;
                } else {

                    // UPDATE scoring

                    try {
                        $sql = "
                            UPDATE 
                              `GameCrossFit` 
                            SET
                              `GameCrossFit`.`LastUpdated` = :LastUpdated,
                              `GameCrossFit`.`EntryDate` = :EntryDate,
                              `GameCrossFit`.`PullUps` = :PullUps
                            WHERE
                              `GameCrossFit`.`PlayerId` = :PlayerId
                            AND 
                              `GameCrossFit`.`EventId` = :EventId
                            AND 
                              `GameCrossFit`.`Round` = :Round";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                        $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                        $stmt->bindParam('PullUps', $crossFit_PullUps, PDO::PARAM_INT);
                        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                        $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                        $stmt->execute();

                    } catch (PDOException $e) {
                        trigger_error($e->getMessage(), E_USER_ERROR);
                    }//end try
                }
                $x++;
            }
            if ($error) {
                $msgBox = alertBox("Pull Ups (0-65535) (Cross Fit)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            } else {
                $statsUpdated = true;
            }
        } // end if

        if (array_key_exists('crossFit_PushUps', $_POST)) {
            $x = 0;
            $error = false;
            for ($i = 1; $i <= $eventRounds; $i++) {
                $crossFit_PushUps = isset($_POST['crossFit_PushUps'][$x]) ? preg_replace("/[^0-9]/", "", $_POST['crossFit_PushUps'][$x]) : '0'; // Count
                if (!empty($crossFit_PushUps) && $crossFit_PushUps > 65535) {
                    $error = true;
                } else {

                    // UPDATE scoring

                    try {
                        $sql = "
                            UPDATE 
                              `GameCrossFit` 
                            SET
                              `GameCrossFit`.`LastUpdated` = :LastUpdated,
                              `GameCrossFit`.`EntryDate` = :EntryDate,
                              `GameCrossFit`.`PushUps` = :PushUps
                            WHERE
                              `GameCrossFit`.`PlayerId` = :PlayerId
                            AND 
                              `GameCrossFit`.`EventId` = :EventId
                            AND 
                              `GameCrossFit`.`Round` = :Round";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                        $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                        $stmt->bindParam('PushUps', $crossFit_PushUps, PDO::PARAM_INT);
                        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                        $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                        $stmt->execute();

                    } catch (PDOException $e) {
                        trigger_error($e->getMessage(), E_USER_ERROR);
                    }//end try
                }
                $x++;
            }
            if ($error) {
                $msgBox = alertBox("Push Ups (0-65535) (Cross Fit)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            } else {
                $statsUpdated = true;
            }
        } // end if

        // Swimming
        if (array_key_exists('swimming_25MFreestyle', $_POST)) {
            $x = 0;
            $error = false;
            for ($i = 1; $i <= $eventRounds; $i++) {
                $swimming_25MFreestyle = isset($_POST['swimming_25MFreestyle'][$x]) ? hmsuToDecimal(trim($_POST['swimming_25MFreestyle'][$x])) : '0.0'; // Time
                if ($swimming_25MFreestyle === false) {
                    $error = true;
                } else {

                    // UPDATE scoring

                    try {
                        $sql = "
                            UPDATE 
                              `GameSwimming` 
                            SET
                              `GameSwimming`.`LastUpdated` = :LastUpdated,
                              `GameSwimming`.`EntryDate` = :EntryDate,
                              `GameSwimming`.`25MFreestyle` = :_25MFreestyle
                            WHERE
                              `GameSwimming`.`PlayerId` = :PlayerId
                            AND 
                              `GameSwimming`.`EventId` = :EventId
                            AND 
                              `GameSwimming`.`Round` = :Round";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                        $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                        $stmt->bindParam('_25MFreestyle', $swimming_25MFreestyle, PDO::PARAM_STR);
                        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                        $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                        $stmt->execute();

                    } catch (PDOException $e) {
                        trigger_error($e->getMessage(), E_USER_ERROR);
                    }//end try
                }
                $x++;
            }
            if ($error) {
                $msgBox = alertBox("Format invalid for 25 M. Freestyle (Swimming)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            } else {
                $statsUpdated = true;
            }
        } // end if

        if (array_key_exists('swimming_25MBackStroke', $_POST)) {
            $x = 0;
            $error = false;
            for ($i = 1; $i <= $eventRounds; $i++) {
                $swimming_25MBackStroke = isset($_POST['swimming_25MBackStroke'][$x]) ? hmsuToDecimal(trim($_POST['swimming_25MBackStroke'][$x])) : '0.0'; // Time
                if ($swimming_25MBackStroke === false) {
                    $error = true;
                } else {

                    // UPDATE scoring

                    try {
                        $sql = "
                            UPDATE 
                              `GameSwimming` 
                            SET
                              `GameSwimming`.`LastUpdated` = :LastUpdated,
                              `GameSwimming`.`EntryDate` = :EntryDate,
                              `GameSwimming`.`25MBackStroke` = :_25MBackStroke
                            WHERE
                              `GameSwimming`.`PlayerId` = :PlayerId
                            AND 
                              `GameSwimming`.`EventId` = :EventId
                            AND 
                              `GameSwimming`.`Round` = :Round";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                        $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                        $stmt->bindParam('_25MBackStroke', $swimming_25MBackStroke, PDO::PARAM_STR);
                        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                        $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                        $stmt->execute();

                    } catch (PDOException $e) {
                        trigger_error($e->getMessage(), E_USER_ERROR);
                    }//end try
                }
                $x++;
            }
            if ($error) {
                $msgBox = alertBox("Format invalid for 25 M. BackStroke (Swimming)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            } else {
                $statsUpdated = true;
            }
        } // end if

        if (array_key_exists('swimming_25MBreastStroke', $_POST)) {
            $x = 0;
            $error = false;
            for ($i = 1; $i <= $eventRounds; $i++) {
                $swimming_25MBreastStroke = isset($_POST['swimming_25MBreastStroke'][$x]) ? hmsuToDecimal(trim($_POST['swimming_25MBreastStroke'][$x])) : '0.0'; // Time
                if ($swimming_25MBreastStroke === false) {
                    $error = true;
                } else {

                    // UPDATE scoring

                    try {
                        $sql = "
                            UPDATE 
                              `GameSwimming` 
                            SET
                              `GameSwimming`.`LastUpdated` = :LastUpdated,
                              `GameSwimming`.`EntryDate` = :EntryDate,
                              `GameSwimming`.`25MBreastStroke` = :_25MBreastStroke
                            WHERE
                              `GameSwimming`.`PlayerId` = :PlayerId
                            AND 
                              `GameSwimming`.`EventId` = :EventId
                            AND 
                              `GameSwimming`.`Round` = :Round";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                        $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                        $stmt->bindParam('_25MBreastStroke', $swimming_25MBackStroke, PDO::PARAM_STR);
                        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                        $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                        $stmt->execute();

                    } catch (PDOException $e) {
                        trigger_error($e->getMessage(), E_USER_ERROR);
                    }//end try
                }
                $x++;
            }
            if ($error) {
                $msgBox = alertBox("Format invalid for 25 M. BreastStroke (Swimming)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            } else {
                $statsUpdated = true;
            }
        } // end if

        if (array_key_exists('swimming_25MButterfly', $_POST)) {
            $x = 0;
            $error = false;
            for ($i = 1; $i <= $eventRounds; $i++) {
                $swimming_25MButterfly = isset($_POST['swimming_25MButterfly'][$x]) ? hmsuToDecimal(trim($_POST['swimming_25MButterfly'][$x])) : '0.0'; // Time
                if ($swimming_25MButterfly === false) {
                    $error = true;
                } else {

                    // UPDATE scoring

                    try {
                        $sql = "
                            UPDATE 
                              `GameSwimming` 
                            SET
                              `GameSwimming`.`LastUpdated` = :LastUpdated,
                              `GameSwimming`.`EntryDate` = :EntryDate,
                              `GameSwimming`.`25MButterfly` = :_25MButterfly
                            WHERE
                              `GameSwimming`.`PlayerId` = :PlayerId
                            AND 
                              `GameSwimming`.`EventId` = :EventId
                            AND 
                              `GameSwimming`.`Round` = :Round";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                        $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                        $stmt->bindParam('_25MButterfly', $swimming_25MButterfly, PDO::PARAM_STR);
                        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                        $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                        $stmt->execute();

                    } catch (PDOException $e) {
                        trigger_error($e->getMessage(), E_USER_ERROR);
                    }//end try
                }
                $x++;
            }
            if ($error) {
                $msgBox = alertBox("Format invalid for 25 M. Butterfly (Swimming)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            } else {
                $statsUpdated = true;
            }
        } // end if


        /////
        if (array_key_exists('swimming_50MFreestyle', $_POST)) {
            $x = 0;
            $error = false;
            for ($i = 1; $i <= $eventRounds; $i++) {
                $swimming_50MFreestyle = isset($_POST['swimming_50MFreestyle'][$x]) ? hmsuToDecimal(trim($_POST['swimming_50MFreestyle'][$x])) : '0.0'; // Time
                if ($swimming_50MFreestyle === false) {
                    $error = true;
                } else {

                    // UPDATE scoring

                    try {
                        $sql = "
                            UPDATE 
                              `GameSwimming` 
                            SET
                              `GameSwimming`.`LastUpdated` = :LastUpdated,
                              `GameSwimming`.`EntryDate` = :EntryDate,
                              `GameSwimming`.`50MFreestyle` = :_50MFreestyle
                            WHERE
                              `GameSwimming`.`PlayerId` = :PlayerId
                            AND 
                              `GameSwimming`.`EventId` = :EventId
                            AND 
                              `GameSwimming`.`Round` = :Round";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                        $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                        $stmt->bindParam('_50MFreestyle', $swimming_50MFreestyle, PDO::PARAM_STR);
                        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                        $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                        $stmt->execute();

                    } catch (PDOException $e) {
                        trigger_error($e->getMessage(), E_USER_ERROR);
                    }//end try
                }
                $x++;
            }
            if ($error) {
                $msgBox = alertBox("Format invalid for 50 M. Freestyle (Swimming)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            } else {
                $statsUpdated = true;
            }
        } // end if

        if (array_key_exists('swimming_50MBackStroke', $_POST)) {
            $x = 0;
            $error = false;
            for ($i = 1; $i <= $eventRounds; $i++) {
                $swimming_50MBackStroke = isset($_POST['swimming_50MBackStroke'][$x]) ? hmsuToDecimal(trim($_POST['swimming_50MBackStroke'][$x])) : '0.0'; // Time
                if ($swimming_50MBackStroke === false) {
                    $error = true;
                } else {

                    // UPDATE scoring

                    try {
                        $sql = "
                            UPDATE 
                              `GameSwimming` 
                            SET
                              `GameSwimming`.`LastUpdated` = :LastUpdated,
                              `GameSwimming`.`EntryDate` = :EntryDate,
                              `GameSwimming`.`50MBackStroke` = :_50MBackStroke
                            WHERE
                              `GameSwimming`.`PlayerId` = :PlayerId
                            AND 
                              `GameSwimming`.`EventId` = :EventId
                            AND 
                              `GameSwimming`.`Round` = :Round";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                        $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                        $stmt->bindParam('_50MBackStroke', $swimming_50MBackStroke, PDO::PARAM_STR);
                        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                        $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                        $stmt->execute();

                    } catch (PDOException $e) {
                        trigger_error($e->getMessage(), E_USER_ERROR);
                    }//end try
                }
                $x++;
            }
            if ($error) {
                $msgBox = alertBox("Format invalid for 50 M. BackStroke (Swimming)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            } else {
                $statsUpdated = true;
            }
        } // end if

        if (array_key_exists('swimming_50MBreastStroke', $_POST)) {
            $x = 0;
            $error = false;
            for ($i = 1; $i <= $eventRounds; $i++) {
                $swimming_50MBreastStroke = isset($_POST['swimming_50MBreastStroke'][$x]) ? hmsuToDecimal(trim($_POST['swimming_50MBreastStroke'][$x])) : '0.0'; // Time
                if ($swimming_50MBreastStroke === false) {
                    $error = true;
                } else {

                    // UPDATE scoring

                    try {
                        $sql = "
                            UPDATE 
                              `GameSwimming` 
                            SET
                              `GameSwimming`.`LastUpdated` = :LastUpdated,
                              `GameSwimming`.`EntryDate` = :EntryDate,
                              `GameSwimming`.`50MBreastStroke` = :_50MBreastStroke
                            WHERE
                              `GameSwimming`.`PlayerId` = :PlayerId
                            AND 
                              `GameSwimming`.`EventId` = :EventId
                            AND 
                              `GameSwimming`.`Round` = :Round";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                        $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                        $stmt->bindParam('_50MBreastStroke', $swimming_50MBackStroke, PDO::PARAM_STR);
                        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                        $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                        $stmt->execute();

                    } catch (PDOException $e) {
                        trigger_error($e->getMessage(), E_USER_ERROR);
                    }//end try
                }
                $x++;
            }
            if ($error) {
                $msgBox = alertBox("Format invalid for 50 M. BreastStroke (Swimming)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            } else {
                $statsUpdated = true;
            }
        } // end if

        if (array_key_exists('swimming_50MButterfly', $_POST)) {
            $x = 0;
            $error = false;
            for ($i = 1; $i <= $eventRounds; $i++) {
                $swimming_50MButterfly = isset($_POST['swimming_50MButterfly'][$x]) ? hmsuToDecimal(trim($_POST['swimming_50MButterfly'][$x])) : '0.0'; // Time
                if ($swimming_50MButterfly === false) {
                    $error = true;
                } else {

                    // UPDATE scoring

                    try {
                        $sql = "
                            UPDATE 
                              `GameSwimming` 
                            SET
                              `GameSwimming`.`LastUpdated` = :LastUpdated,
                              `GameSwimming`.`EntryDate` = :EntryDate,
                              `GameSwimming`.`50MButterfly` = :_50MButterfly
                            WHERE
                              `GameSwimming`.`PlayerId` = :PlayerId
                            AND 
                              `GameSwimming`.`EventId` = :EventId
                            AND 
                              `GameSwimming`.`Round` = :Round";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                        $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                        $stmt->bindParam('_50MButterfly', $swimming_50MButterfly, PDO::PARAM_STR);
                        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                        $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                        $stmt->execute();

                    } catch (PDOException $e) {
                        trigger_error($e->getMessage(), E_USER_ERROR);
                    }//end try
                }
                $x++;
            }
            if ($error) {
                $msgBox = alertBox("Format invalid for 50 M. Butterfly (Swimming)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            } else {
                $statsUpdated = true;
            }
        } // end if

        ///
        if (array_key_exists('swimming_100MFreestyle', $_POST)) {
            $x = 0;
            $error = false;
            for ($i = 1; $i <= $eventRounds; $i++) {
                $swimming_100MFreestyle = isset($_POST['swimming_100MFreestyle'][$x]) ? hmsuToDecimal(trim($_POST['swimming_100MFreestyle'][$x])) : '0.0'; // Time
                if ($swimming_100MFreestyle === false) {
                    $error = true;
                } else {

                    // UPDATE scoring

                    try {
                        $sql = "
                            UPDATE 
                              `GameSwimming` 
                            SET
                              `GameSwimming`.`LastUpdated` = :LastUpdated,
                              `GameSwimming`.`EntryDate` = :EntryDate,
                              `GameSwimming`.`100MFreestyle` = :_100MFreestyle
                            WHERE
                              `GameSwimming`.`PlayerId` = :PlayerId
                            AND 
                              `GameSwimming`.`EventId` = :EventId
                            AND 
                              `GameSwimming`.`Round` = :Round";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                        $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                        $stmt->bindParam('_100MFreestyle', $swimming_100MFreestyle, PDO::PARAM_STR);
                        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                        $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                        $stmt->execute();

                    } catch (PDOException $e) {
                        trigger_error($e->getMessage(), E_USER_ERROR);
                    }//end try
                }
                $x++;
            }
            if ($error) {
                $msgBox = alertBox("Format invalid for 100 M. Freestyle (Swimming)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            } else {
                $statsUpdated = true;
            }
        } // end if

        if (array_key_exists('swimming_100MBackStroke', $_POST)) {
            $x = 0;
            $error = false;
            for ($i = 1; $i <= $eventRounds; $i++) {
                $swimming_100MBackStroke = isset($_POST['swimming_100MBackStroke'][$x]) ? hmsuToDecimal(trim($_POST['swimming1000MBackStroke'][$x])) : '0.0'; // Time
                if ($swimming_100MBackStroke === false) {
                    $error = true;
                } else {

                    // UPDATE scoring

                    try {
                        $sql = "
                            UPDATE 
                              `GameSwimming` 
                            SET
                              `GameSwimming`.`LastUpdated` = :LastUpdated,
                              `GameSwimming`.`EntryDate` = :EntryDate,
                              `GameSwimming`.`100MBackStroke` = :_100MBackStroke
                            WHERE
                              `GameSwimming`.`PlayerId` = :PlayerId
                            AND 
                              `GameSwimming`.`EventId` = :EventId
                            AND 
                              `GameSwimming`.`Round` = :Round";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                        $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                        $stmt->bindParam('_100MBackStroke', $swimming_100MBackStroke, PDO::PARAM_STR);
                        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                        $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                        $stmt->execute();

                    } catch (PDOException $e) {
                        trigger_error($e->getMessage(), E_USER_ERROR);
                    }//end try
                }
                $x++;
            }
            if ($error) {
                $msgBox = alertBox("Format invalid for 100 M. BackStroke (Swimming)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            } else {
                $statsUpdated = true;
            }
        } // end if

        if (array_key_exists('swimming_100MBreastStroke', $_POST)) {
            $x = 0;
            $error = false;
            for ($i = 1; $i <= $eventRounds; $i++) {
                $swimming_100MBreastStroke = isset($_POST['swimming_100MBreastStroke'][$x]) ? hmsuToDecimal(trim($_POST['swimming_100MBreastStroke'][$x])) : '0.0'; // Time
                if ($swimming_100MBreastStroke === false) {
                    $error = true;
                } else {

                    // UPDATE scoring

                    try {
                        $sql = "
                            UPDATE 
                              `GameSwimming` 
                            SET
                              `GameSwimming`.`LastUpdated` = :LastUpdated,
                              `GameSwimming`.`EntryDate` = :EntryDate,
                              `GameSwimming`.`100MBreastStroke` = :_100MBreastStroke
                            WHERE
                              `GameSwimming`.`PlayerId` = :PlayerId
                            AND 
                              `GameSwimming`.`EventId` = :EventId
                            AND 
                              `GameSwimming`.`Round` = :Round";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                        $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                        $stmt->bindParam('_100MBreastStroke', $swimming_100MBackStroke, PDO::PARAM_STR);
                        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                        $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                        $stmt->execute();

                    } catch (PDOException $e) {
                        trigger_error($e->getMessage(), E_USER_ERROR);
                    }//end try
                }
                $x++;
            }
            if ($error) {
                $msgBox = alertBox("Format invalid for 100 M. BreastStroke (Swimming)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            } else {
                $statsUpdated = true;
            }
        } // end if

        if (array_key_exists('swimming_100MButterfly', $_POST)) {
            $x = 0;
            $error = false;
            for ($i = 1; $i <= $eventRounds; $i++) {
                $swimming_100MButterfly = isset($_POST['swimming_100MButterfly'][$x]) ? hmsuToDecimal(trim($_POST['swimming_100MButterfly'][$x])) : '0.0'; // Time
                if ($swimming_100MButterfly === false) {
                    $error = true;
                } else {

                    // UPDATE scoring

                    try {
                        $sql = "
                            UPDATE 
                              `GameSwimming` 
                            SET
                              `GameSwimming`.`LastUpdated` = :LastUpdated,
                              `GameSwimming`.`EntryDate` = :EntryDate,
                              `GameSwimming`.`100MButterfly` = :_100MButterfly
                            WHERE
                              `GameSwimming`.`PlayerId` = :PlayerId
                            AND 
                              `GameSwimming`.`EventId` = :EventId
                            AND 
                              `GameSwimming`.`Round` = :Round";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                        $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                        $stmt->bindParam('_100MButterfly', $swimming_100MButterfly, PDO::PARAM_STR);
                        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                        $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                        $stmt->execute();

                    } catch (PDOException $e) {
                        trigger_error($e->getMessage(), E_USER_ERROR);
                    }//end try
                }
                $x++;
            }
            if ($error) {
                $msgBox = alertBox("Format invalid for 100 M. Butterfly (Swimming)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            } else {
                $statsUpdated = true;
            }
        } // end if

        if (array_key_exists('swimming_100MIndividualMedley', $_POST)) {
            $x = 0;
            $error = false;
            for ($i = 1; $i <= $eventRounds; $i++) {
                $swimming_100MIndividualMedley = isset($_POST['swimming_100MIndividualMedley'][$x]) ? hmsuToDecimal(trim($_POST['swimming_100MIndividualMedley'][$x])) : '0.0'; // Time
                if ($swimming_100MIndividualMedley === false) {
                    $error = true;
                } else {

                    // UPDATE scoring

                    try {
                        $sql = "
                            UPDATE 
                              `GameSwimming` 
                            SET
                              `GameSwimming`.`LastUpdated` = :LastUpdated,
                              `GameSwimming`.`EntryDate` = :EntryDate,
                              `GameSwimming`.`100MIndividualMedley` = :_100MIndividualMedley
                            WHERE
                              `GameSwimming`.`PlayerId` = :PlayerId
                            AND 
                              `GameSwimming`.`EventId` = :EventId
                            AND 
                              `GameSwimming`.`Round` = :Round";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                        $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                        $stmt->bindParam('_100MIndividualMedley', $swimming_100MIndividualMedley, PDO::PARAM_STR);
                        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                        $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                        $stmt->execute();

                    } catch (PDOException $e) {
                        trigger_error($e->getMessage(), E_USER_ERROR);
                    }//end try
                }
                $x++;
            }
            if ($error) {
                $msgBox = alertBox("Format invalid for 100 M. Individual Medley (Swimming)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            } else {
                $statsUpdated = true;
            }
        } // end if

        if (array_key_exists('swimming_200MIndividualMedley', $_POST)) {
            $x = 0;
            $error = false;
            for ($i = 1; $i <= $eventRounds; $i++) {
                $swimming_200MIndividualMedley = isset($_POST['swimming_200MIndividualMedley'][$x]) ? hmsuToDecimal(trim($_POST['swimming_200MIndividualMedley'][$x])) : '0.0'; // Time
                if ($swimming_200MIndividualMedley === false) {
                    $error = true;
                } else {

                    // UPDATE scoring

                    try {
                        $sql = "
                            UPDATE 
                              `GameSwimming` 
                            SET
                              `GameSwimming`.`LastUpdated` = :LastUpdated,
                              `GameSwimming`.`EntryDate` = :EntryDate,
                              `GameSwimming`.`200MIndividualMedley` = :_200MIndividualMedley
                            WHERE
                              `GameSwimming`.`PlayerId` = :PlayerId
                            AND 
                              `GameSwimming`.`EventId` = :EventId
                            AND 
                              `GameSwimming`.`Round` = :Round";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                        $stmt->bindParam('EntryDate', $entryDate, PDO::PARAM_STR);
                        $stmt->bindParam('_200MIndividualMedley', $swimming_200MIndividualMedley, PDO::PARAM_STR);
                        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                        $stmt->bindParam('EventId', $eventId, PDO::PARAM_INT);
                        $stmt->bindParam('Round', $i, PDO::PARAM_INT);
                        $stmt->execute();

                    } catch (PDOException $e) {
                        trigger_error($e->getMessage(), E_USER_ERROR);
                    }//end try
                }
                $x++;
            }
            if ($error) {
                $msgBox = alertBox("Format invalid for 200 M. Individual Medley (Swimming)", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
                exit;
            } else {
                $statsUpdated = true;
            }
        } // end if

    } // end if

    if (!$statsUpdated) {
        $msgBox = alertBox("Please enter player stats for at least one sport.", "<i class='fa fa-times'></i>", "danger");
        echo json_encode(array('error' => $msgBox));
    } else {
        $msgBox = alertBox("Player stats updated for $_entryDate", "<i class='fa fa-check-square-o'></i>", "success");
        echo json_encode(array('success' => $msgBox));
    }

}

