<?php

if (!isset($_SESSION['userId'])) {
    header('Location: index.php?page=login');
    exit;
}

header('Content-Type: application/json');

if (isset($_POST) && count($_POST) > 0) {

    $token = isset($_POST['token']) ? $_POST['token'] : '';

    $count = 0;
    $mediaId = null;
    $mediaUserId = null;
    $mediaUserIdFrom = null;
    try {
        $sql = "
        SELECT 
          `Media`.`MediaId`,
          `Media`.`UserId`,
          `Media`.`UserIdFrom`
        FROM 
          `Media` 
        WHERE
          `Media`.`Token` = :Token";

        $stmt = $PDO->prepare($sql);
        $stmt->bindParam('Token', $token, PDO::PARAM_STR);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $count++;
            $mediaId = $row['MediaId'];
            $mediaUserId = $row['UserId'];
            $mediaUserIdFrom = $row['UserIdFrom'];
        }

    } catch (PDOException $e) {
        trigger_error($e->getMessage(), E_USER_ERROR);
    }//end try

    if ($count > 0) {

        $count = 0;
        $likeId = null;
        try {
            $sql = "
            SELECT 
              `Likes`.`LikeId`
            FROM 
              `Likes` 
            WHERE
              `Likes`.`MediaId` = :MediaId
            AND 
              `Likes`.`UserIdFrom` = :UserIdFrom";

            $stmt = $PDO->prepare($sql);
            $stmt->bindParam('MediaId', $mediaId, PDO::PARAM_INT);
            $stmt->bindParam('UserIdFrom', $userId, PDO::PARAM_INT);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $count++;
                $likeId = $row['LikeId'];
            }

        } catch (PDOException $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
        }//end try

        if ($count == 0) {
            $created = date('Y-m-d H:i:s');
            $token = sha1($userIp . microseconds());
            try {
                $sql = "
                INSERT INTO `Likes` 
                (
                  `LikeId`, 
                  `Created`, 
                  `UserId`, 
                  `UserIdFrom`, 
                  `MediaId`, 
                  `Token`
                ) VALUES (
                  NULL, 
                  :Created, 
                  :UserId, 
                  :UserIdFrom, 
                  :MediaId, 
                  :Token
                )";

                $stmt = $PDO->prepare($sql);
                $stmt->bindParam('Created', $created, PDO::PARAM_STR);
                $stmt->bindParam('UserId', $userId, PDO::PARAM_INT);
                $stmt->bindParam('UserIdFrom', $userId, PDO::PARAM_INT);
                $stmt->bindParam('MediaId', $mediaId, PDO::PARAM_INT); // the postId to like
                $stmt->bindParam('Token', $token, PDO::PARAM_STR);
                $stmt->execute();

            } catch (PDOException $e) {
                trigger_error($e->getMessage(), E_USER_ERROR);
            }//end try

            // get LikeId
            $likeId = null;
            try {
                $sql = "
                SELECT 
                  `Likes`.`LikeId`
                FROM 
                  `Likes` 
                WHERE
                  `Likes`.`UserId` = :UserId
                AND
                  `Likes`.`Token` = :Token";

                $stmt = $PDO->prepare($sql);
                $stmt->bindParam('UserId', $userId, PDO::PARAM_INT);
                $stmt->bindParam('Token', $token, PDO::PARAM_STR);
                $stmt->execute();

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $likeId = $row['LikeId'];
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
                  `MediaId`, 
                  `LikeId`, 
                  `Token`
                ) VALUES (
                  NULL, 
                  :Created, 
                  :UserId, 
                  :UserIdFrom, 
                  :MediaId,
                  :LikeId, 
                  :Token
                )";

                $stmt_wall = $PDO->prepare($sql_wall);
                $stmt_wall->bindParam('Created', $created, PDO::PARAM_STR); // timestamp of when user created a Like
                $stmt_wall->bindParam('UserId', $userId, PDO::PARAM_INT); // Owner of Like
                $stmt_wall->bindParam('UserIdFrom', $userId, PDO::PARAM_INT); // My logged in userId (one who created the Like)
                $stmt_wall->bindParam('MediaId', $mediaId, PDO::PARAM_INT);
                $stmt_wall->bindParam('LikeId', $likeId, PDO::PARAM_INT);
                $stmt_wall->bindParam('Token', $wallToken, PDO::PARAM_STR);
                $stmt_wall->execute();

            } catch (PDOException $e) {
                trigger_error($e->getMessage(), E_USER_ERROR);
            }//end try

            $msgBox = alertBox("Liked successfully", "<i class='fa fa-check-square-o'></i>", "success");
            echo json_encode(array('success' => $msgBox, 'like' => true));

        } else {

            try {
                $sql = "
                DELETE FROM 
                  `Likes` 
                WHERE
                  `Likes`.`MediaId` = :MediaId
                AND 
                  `Likes`.`UserIdFrom` = :UserIdFrom";

                $stmt = $PDO->prepare($sql);
                $stmt->bindParam('MediaId', $mediaId, PDO::PARAM_INT);
                $stmt->bindParam('UserIdFrom', $userId, PDO::PARAM_INT);
                $stmt->execute();

            } catch (PDOException $e) {
                trigger_error($e->getMessage(), E_USER_ERROR);
            }//end try

            try {
                $sql = "
                DELETE FROM 
                  `Wall` 
                WHERE
                  `Wall`.`LikeId` = :LikeId
                AND 
                  `Wall`.`UserIdFrom` = :UserIdFrom";

                $stmt = $PDO->prepare($sql);
                $stmt->bindParam('LikeId', $likeId, PDO::PARAM_INT);
                $stmt->bindParam('UserIdFrom', $userId, PDO::PARAM_INT);
                $stmt->execute();

            } catch (PDOException $e) {
                trigger_error($e->getMessage(), E_USER_ERROR);
            }//end try

            $msgBox = alertBox("Un-Liked successfully", "<i class='fa fa-check-square-o'></i>", "success");
            echo json_encode(array('success' => $msgBox, 'like' => false));
        }

    } else {
        $msgBox = alertBox("Insufficient privilege", "<i class='fa fa-times'></i>", "danger");
        echo json_encode(array('error' => $msgBox));
    }
}


