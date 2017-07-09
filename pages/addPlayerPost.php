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

    $postContent = isset($_POST['message']) ? trim($_POST['message']) : '';
    $postContent = filter_var($postContent, FILTER_SANITIZE_STRING);

    $playerToken = isset($_POST['token']) ? trim($_POST['token']) : '';
    $playerToken = filter_var($playerToken, FILTER_SANITIZE_STRING);

    if (!empty($postContent)) {

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

            $postCreated = date('Y-m-d H:i:s');
            $postToken = sha1($userIp . microseconds());

            // insert post
            try {
                $sql = "
                INSERT INTO `Posts` 
                (
                  `PostId`, 
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
                  :Content, 
                  :Token
                )";

                $stmt = $PDO->prepare($sql);
                $stmt->bindParam('Created', $postCreated, PDO::PARAM_STR);
                $stmt->bindParam('UserId', $adminId, PDO::PARAM_INT);
                $stmt->bindParam('UserIdFrom', $userId, PDO::PARAM_INT);
                $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
                $stmt->bindParam('Content', $postContent, PDO::PARAM_STR);
                $stmt->bindParam('Token', $postToken, PDO::PARAM_STR);
                $stmt->execute();
            } catch (PDOException $e) {
                trigger_error($e->getMessage(), E_USER_ERROR);
            }//end try

            // get PostId
            $postId = null;
            try {
                $sql = "
                SELECT 
                  `Posts`.`PostId`
                FROM 
                  `Posts` 
                USE INDEX (`UserIdToken`)
                WHERE
                  `Posts`.`UserId` = :UserId
                AND
                  `Posts`.`Token` = :Token";

                $stmt = $PDO->prepare($sql);
                $stmt->bindParam('UserId', $adminId, PDO::PARAM_INT);
                $stmt->bindParam('Token', $postToken, PDO::PARAM_STR);
                $stmt->execute();

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $postId = $row['PostId'];
                }

            } catch (PDOException $e) {
                trigger_error($e->getMessage(), E_USER_ERROR);
            }//end try

            // insert wall PostId
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
                  `PostId`, 
                  `Token`
                ) VALUES (
                  NULL, 
                  :Created, 
                  :UserId, 
                  :UserIdFrom, 
                  :PlayerId, 
                  :PostId, 
                  :Token
                )";

                $stmt_wall = $PDO->prepare($sql_wall);
                $stmt_wall->bindParam('Created', $postCreated, PDO::PARAM_STR); // timestamp of when user was posting
                $stmt_wall->bindParam('UserId', $adminId, PDO::PARAM_INT); // Owner of player
                $stmt_wall->bindParam('UserIdFrom', $userId, PDO::PARAM_INT); // My logged in userId (one who is posting video)
                $stmt_wall->bindParam('PlayerId', $playerId, PDO::PARAM_INT); // The player card
                $stmt_wall->bindParam('PostId', $postId, PDO::PARAM_INT);
                $stmt_wall->bindParam('Token', $wallToken, PDO::PARAM_STR);
                $stmt_wall->execute();

            } catch (PDOException $e) {
                trigger_error($e->getMessage(), E_USER_ERROR);
            }//end try


            $msgBox = alertBox("You have successfully posted to $adminFullName's Timeline", "<i class='fa fa-check-square-o'></i>", "success");
            echo json_encode(array('success' => $msgBox));
        }

    } else {
        $msgBox = alertBox("Message is required.", "<i class='fa fa-times'></i>", "danger");
        echo json_encode(array('error' => $msgBox));
        exit;
    }

}



