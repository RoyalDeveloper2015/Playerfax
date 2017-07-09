<?php

if (!isset($_SESSION['userId'])) {
    header('Location: index.php?page=login');
    exit;
}

header('Content-Type: application/json');

$playerId = null;
$adminId = null; // Owner of player
$adminFullName = ''; // Owner of player
$adminEmail = ''; // Owner of player
$playerFullName = ''; // Player name

if (isset($_POST) && count($_POST) > 0) {

    $messageContent = isset($_POST['message']) ? trim($_POST['message']) : '';
    $messageContent = filter_var($messageContent, FILTER_SANITIZE_STRING);

    $playerToken = isset($_POST['token']) ? trim($_POST['token']) : '';
    $playerToken = filter_var($playerToken, FILTER_SANITIZE_STRING);

    if (!empty($messageContent)) {

        $count = 0;
        // get the admin information for this player using the player token
        try {
            $sql = "
            SELECT 
              `Players`.`PlayerId`, 
              `Players`.`FirstName` AS `PlayerFirstName`, 
              `Players`.`LastName` AS `PlayerLastName`, 
              `Users`.`UserId` AS `AdminUserId`, 
              `Users`.`FirstName` AS `AdminFirstName`, 
              `Users`.`LastName` AS `AdminLastName`, 
              `Users`.`Email` AS `AdminEmail`
            FROM 
              `Players` 
            USE INDEX (`TokenIsActive`)
            LEFT JOIN `Users`
            USE INDEX FOR JOIN (`PRIMARY`)
            ON 
              `Players`.`UserId` = `Users`.`UserId`
            WHERE
              `Players`.`Token` = :Token
            AND
              `Players`.`IsActive` = 1";

            $stmt = $PDO->prepare($sql);
            $stmt->bindParam('Token', $playerToken, PDO::PARAM_STR);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $count++;
                $playerId = $row['PlayerId'];
                $adminId = $row['AdminUserId'];
                $adminFullName = trim($row['AdminFirstName'] . ' ' . $row['AdminLastName']);
                $adminEmail = $row['AdminEmail'];
                $playerFullName = trim($row['PlayerFirstName'] . ' ' . $row['PlayerLastName']);
            }

        } catch (PDOException $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
        }//end try

        if ($count > 0) {

            $messageCreated = date('Y-m-d H:i:s');
            $messageToken = sha1($userIp . microseconds());

            // insert message
            $messageAESKey = substr($messageToken, 3, 16);
            try {
                $sql = "
                INSERT INTO `Messages` 
                (
                  `MessageId`, 
                  `Created`, 
                  `UserId`, 
                  `UserIdFrom`, 
                  `PlayerId`,
                  `Content`, 
                  `Token`
                ) VALUES (
                  NULL, 
                  :Created,
                  :UserId, 
                  :UserIdFrom, 
                  :PlayerId,
                  AES_ENCRYPT(:Content, :AESKey), 
                  :Token
                )";

                $stmt = $PDO->prepare($sql);
                $stmt->bindParam('Created', $messageCreated, PDO::PARAM_STR);
                $stmt->bindParam('UserId', $adminId, PDO::PARAM_INT);
                $stmt->bindParam('UserIdFrom', $userId, PDO::PARAM_INT);
                $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                $stmt->bindParam('Content', $messageContent, PDO::PARAM_STR);
                $stmt->bindParam('AESKey', $messageAESKey, PDO::PARAM_STR);
                $stmt->bindParam('Token', $messageToken, PDO::PARAM_STR);
                $stmt->execute();
            } catch (PDOException $e) {
                trigger_error($e->getMessage(), E_USER_ERROR);
            }//end try

            // get MessageId
            $messageId = null;
            try {
                $sql = "
                SELECT 
                  `Messages`.`MessageId`
                FROM 
                  `Messages` 
                USE INDEX (`UserIdToken`)
                WHERE
                  `Messages`.`UserId` = :UserId
                AND
                  `Messages`.`Token` = :Token";

                $stmt = $PDO->prepare($sql);
                $stmt->bindParam('UserId', $adminId, PDO::PARAM_INT);
                $stmt->bindParam('Token', $messageToken, PDO::PARAM_STR);
                $stmt->execute();

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $messageId = $row['MessageId'];
                }

            } catch (PDOException $e) {
                trigger_error($e->getMessage(), E_USER_ERROR);
            }//end try

            // insert wall MediaId
            $wallToken = sha1($userIp . microseconds());
            try {
                $sql_wall = "
                INSERT INTO `Wall` 
                (
                  `WallId`, 
                  `Created`, 
                  `UserId`, 
                  `UserIdFrom`, 
                  `PlayerId`, 
                  `MessageId`, 
                  `Token`
                ) VALUES (
                  NULL, 
                  :Created, 
                  :UserId, 
                  :UserIdFrom, 
                  :PlayerId, 
                  :MessageId, 
                  :Token
                )";

                $stmt_wall = $PDO->prepare($sql_wall);
                $stmt_wall->bindParam('Created', $messageCreated, PDO::PARAM_STR); // timestamp of when user was uploading
                $stmt_wall->bindParam('UserId', $adminId, PDO::PARAM_INT); // Owner of player
                $stmt_wall->bindParam('UserIdFrom', $userId, PDO::PARAM_INT); // My logged in userId (one who is posting video)
                $stmt_wall->bindParam('PlayerId', $playerId, PDO::PARAM_INT); // The player card
                $stmt_wall->bindParam('MessageId', $messageId, PDO::PARAM_INT);
                $stmt_wall->bindParam('Token', $wallToken, PDO::PARAM_STR);
                $stmt_wall->execute();

            } catch (PDOException $e) {
                trigger_error($e->getMessage(), E_USER_ERROR);
            }//end try

            // send email to admin about this message and player
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
            $mail->Subject = 'Playerfax Private Message';

            // HTML version
            $htmlBody = file_get_contents('includes/email/email.messageFromUser.html');
            $htmlBody = str_replace('%ADMIN_NAME%', $adminFullName, $htmlBody);
            $htmlBody = str_replace('%USER_NAME%', $userFullName, $htmlBody);
            $htmlBody = str_replace('%PLAYER_NAME%', $playerFullName, $htmlBody);
            //$htmlBody = str_replace('%MESSAGE_CONTENT%', $messageContent, $htmlBody);

            $mail->Body = $htmlBody;

            // plain text version
            $plainBody = file_get_contents('includes/email/email.messageFromUser.txt');
            $plainBody = str_replace('%ADMIN_NAME%', $adminFullName, $plainBody);
            $plainBody = str_replace('%USER_NAME%', $userFullName, $plainBody);
            $plainBody = str_replace('%PLAYER_NAME%', $playerFullName, $plainBody);
            //$plainBody = str_replace('%MESSAGE_CONTENT%', $messageContent, $plainBody);

            $mail->AltBody = $plainBody;

            $mail->send();

            $msgBox = alertBox("Message was sent to the owner of this player.", "<i class='fa fa-check-square-o'></i>", "success");
            echo json_encode(array('success' => $msgBox));
        }

    } else {
        $msgBox = alertBox("Message is required.", "<i class='fa fa-times'></i>", "danger");
        echo json_encode(array('error' => $msgBox));
        exit;
    }

}



