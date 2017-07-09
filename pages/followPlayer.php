<?php

if (!isset($_SESSION['userId'])) {
    header('Location: index.php?page=login');
    exit;
}

header('Content-Type: application/json');

if (isset($_POST) && count($_POST) > 0) {

    $playerToken = isset($_POST['token']) ? trim($_POST['token']) : '';
    $playerToken = filter_var($playerToken, FILTER_SANITIZE_STRING);

    $userRelation = isset($_POST['relationship'][0]) ? trim($_POST['relationship'][0]) : '4';
    $requestPlayerAdmin = isset($_POST['requestPlayerAdmin']) ? trim($_POST['requestPlayerAdmin']) : 'no';

    $count = 0;
    $playerId = null;
    $playerFirstName = ''; // The player's name
    $playerFullName = ''; // The player's full name
    $adminId = null; // Owner of player
    $adminFullName = ''; // Owner of player
    $adminEmail = ''; // Owner of player
    $requestReason = '';

    $created = date('Y-m-d H:i:s');
    $adminToken = sha1($userIp . microseconds());
    $isAdmin = '0';

    // get player information from token
    try {
        $sql = "
        SELECT 
          `Players`.`PlayerId` AS `PlayerId`,
          `Players`.`FirstName` AS `PlayerFirstName`,
          `Players`.`LastName` AS `PlayerLastName`,
          `Players`.`UserId` AS `PlayerUserId`
        FROM `Players`
        LEFT JOIN `Wall` ON 
            `Players`.`PlayerId` = `Wall`.`PlayerId`
        WHERE
          `Players`.`Token` = :Token";

        $stmt = $PDO->prepare($sql);
        $stmt->bindParam('Token', $playerToken, PDO::PARAM_STR);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $count++;
            $playerId = $row['PlayerId']; // The player
            $playerFirstName = $row['PlayerFirstName']; // The player's name
            $playerFullName = trim($row['PlayerFirstName'] . ' ' . $row['PlayerLastName']); // The player's full name
            $adminId = $row['PlayerUserId']; // Owner of player
        }

    } catch (PDOException $e) {
        trigger_error($e->getMessage(), E_USER_ERROR);
    }//end try

    if ($count > 0) {

        // get admin user information
        try {
            $sql = "
            SELECT 
              `Users`.`UserId`, 
              `Users`.`FirstName`, 
              `Users`.`LastName`, 
              `Users`.`Email`
            FROM 
              `Users` 
            WHERE
              `Users`.`UserId` = :UserId";

            $stmt = $PDO->prepare($sql);
            $stmt->bindParam('UserId', $adminId, PDO::PARAM_STR);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $count++;
                $adminId = $row['UserId']; // Owner of player
                $adminFullName = trim($row['FirstName'] . ' ' . $row['LastName']); // Owner of player
                $adminEmail = $row['Email']; // Owner of player
            }

        } catch (PDOException $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
        }//end try


        $requestReason = getDesignation($userRelation, $playerFirstName);

        // check to see if I already made this request, to prevent duplicate requests
        $count = 0;
        try {
            $sql = "
            SELECT 
              `Follows`.`FollowId`, `Follows`.`UserIdFrom`
            FROM 
              `Follows` 
            WHERE
              `Follows`.`UserIdFrom` = :UserIdFrom
            AND 
              `Follows`.`UserId` != :UserId
            AND 
              `Follows`.`PlayerId` = :PlayerId";

            $stmt = $PDO->prepare($sql);
            $stmt->bindParam('UserIdFrom', $userId, PDO::PARAM_INT); // My logged in userId (one who is requesting admin access)
            $stmt->bindParam('UserId', $userId, PDO::PARAM_INT); // The player card
            $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT); // The player card
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $count++;
            }

        } catch (PDOException $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
        }//end try

        if ($count > 0) {
            $msgBox = alertBox("You have already added this player.", "<i class='fa fa-times'></i>", "danger");
            echo json_encode(array('error' => $msgBox));
            exit;
        }

        // check to see if I already made this request, to prevent duplicate requests
        $count = 0;
        try {
            $sql = "
            SELECT 
              `PlayerAdmins`.`PlayerAdminId`
            FROM 
              `PlayerAdmins` 
            WHERE
              `PlayerAdmins`.`UserIdFrom` = :UserIdFrom
            AND 
              `PlayerAdmins`.`PlayerId` = :PlayerId";

            $stmt = $PDO->prepare($sql);
            $stmt->bindParam('UserIdFrom', $userId, PDO::PARAM_INT); // My logged in userId (one who is requesting admin access)
            $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT); // The player card
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $count++;
            }

        } catch (PDOException $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
        }//end try

        if ($count > 0) {
            $msgBox = alertBox("You have already requested Admin rights.", "<i class='fa fa-times'></i>", "danger");
            echo json_encode(array('error' => $msgBox));
            exit;
        }

        // insert new Follow
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
            $stmt->bindParam('Created', $created, PDO::PARAM_STR); // Creation date
            $stmt->bindParam('UserId', $adminId, PDO::PARAM_INT); // Owner of player
            $stmt->bindParam('UserIdFrom', $userId, PDO::PARAM_INT); // My logged in userId (one who is requesting admin access)
            $stmt->bindParam('Designation', $userRelation, PDO::PARAM_INT); // Chosen designation
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
            $stmt->bindParam('UserId', $adminId, PDO::PARAM_INT);
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
            $stmt->bindParam('Created', $created, PDO::PARAM_STR); // Creation date
            $stmt->bindParam('UserId', $adminId, PDO::PARAM_INT); // Owner of player
            $stmt->bindParam('UserIdFrom', $userId, PDO::PARAM_INT); // My logged in userId (one who is requesting admin access)
            $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT); // The player card
            $stmt->bindParam('FollowId', $followId, PDO::PARAM_INT);
            $stmt->bindParam('Token', $wallToken, PDO::PARAM_STR);
            $stmt->execute();

        } catch (PDOException $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
        }//end try

        if ($requestPlayerAdmin == 'yes') {

            // insert new Player Admin Request
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
                $stmt->bindParam('Created', $created, PDO::PARAM_STR); // Creation date
                $stmt->bindParam('UserId', $adminId, PDO::PARAM_INT); // Owner of player
                $stmt->bindParam('UserIdFrom', $userId, PDO::PARAM_INT); // My logged in userId (one who is requesting admin access)
                $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT); // The player card
                $stmt->bindParam('IsAdmin', $isAdmin, PDO::PARAM_INT); // 0=No, 1=Yes
                $stmt->bindParam('Token', $adminToken, PDO::PARAM_STR); // If there is a token, then a request has been made
                $stmt->execute();

            } catch (PDOException $e) {
                trigger_error($e->getMessage(), E_USER_ERROR);
            }//end try

            // send email to the user who owns this player with a token that will update the record

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
            $mail->AddAddress($adminEmail, $adminFullName);  // Send to Owner of player
            $mail->Subject = 'Playerfax PlayerAdmin Request';

            // HTML version
            $htmlBody = file_get_contents('includes/email/email.requestPlayerAdmin.html');
            $htmlBody = str_replace('%ADMIN_NAME%', $adminFullName, $htmlBody);
            $htmlBody = str_replace('%USER_NAME%', $userFullName, $htmlBody);
            $htmlBody = str_replace('%PLAYER_NAME%', $playerFullName, $htmlBody);
            $htmlBody = str_replace('%REQUEST_REASON%', $requestReason, $htmlBody);
            $htmlBody = str_replace('%CONFIRM_TOKEN%', $adminToken, $htmlBody);

            $mail->Body = $htmlBody;

            // plain text version
            $plainBody = file_get_contents('includes/email/email.requestPlayerAdmin.txt');
            $plainBody = str_replace('%ADMIN_NAME%', $adminFullName, $plainBody);
            $plainBody = str_replace('%USER_NAME%', $userFullName, $plainBody);
            $plainBody = str_replace('%PLAYER_NAME%', $playerFullName, $plainBody);
            $plainBody = str_replace('%REQUEST_REASON%', $requestReason, $plainBody);
            $plainBody = str_replace('%CONFIRM_TOKEN%', $adminToken, $plainBody);

            $mail->AltBody = $plainBody;

            $mail->send();

            $msgBox = alertBox("Player added. Admin rights requested", "<i class='fa fa-check-square-o'></i>", "success");
            echo json_encode(array('success' => $msgBox));
        } else {
            $msgBox = alertBox("Player added", "<i class='fa fa-check-square-o'></i>", "success");
            echo json_encode(array('success' => $msgBox));
        }
    }
}

