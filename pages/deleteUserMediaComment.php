<?php

if (!isset($_SESSION['userId'])) {
    header('Location: index.php?page=login');
    exit;
}

header('Content-Type: application/json');

if (isset($_POST) && count($_POST) > 0) {

    $lastUpdated = date('Y-m-d H:i:s');

    $token = isset($_POST['token']) ? $_POST['token'] : '';

    $count = 0;
    $mediaCommentId = null;
    $mediaCommentUserIdFrom = null;
    try {
        $sql = "
        SELECT 
          `MediaComments`.`MediaCommentId`,
          `MediaComments`.`UserIdFrom`
        FROM 
          `MediaComments` 
        WHERE
          `MediaComments`.`UserId` = :UserId
        AND 
          `MediaComments`.`Token` = :Token";

        $stmt = $PDO->prepare($sql);
        $stmt->bindParam('Token', $token, PDO::PARAM_INT);
        $stmt->bindParam('UserId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $count++;
            $mediaCommentId = $row['MediaCommentId'];
            $mediaCommentUserIdFrom = $row['UserIdFrom'];
        }

    } catch (PDOException $e) {
        trigger_error($e->getMessage(), E_USER_ERROR);
    }//end try

    if ($count > 0) {

        if ($mediaCommentUserIdFrom == $userId) {

            try {
                $sql = "
                DELETE FROM `MediaComments`
                WHERE
                  `MediaComments`.`MediaCommentId` = :MediaCommentId
                AND 
                  `MediaComments`.`UserIdFrom` = :UserIdFrom";

                $stmt = $PDO->prepare($sql);
                $stmt->bindParam('MediaCommentId', $mediaCommentId, PDO::PARAM_INT);
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
                  `Wall`.`MediaCommentId` = :MediaCommentId
                AND 
                  `Wall`.`UserIdFrom` = :UserIdFrom";

                $stmt = $PDO->prepare($sql);
                $stmt->bindParam('MediaCommentId', $mediaCommentId, PDO::PARAM_INT);
                $stmt->bindParam('UserIdFrom', $userId, PDO::PARAM_INT);
                $stmt->execute();

            } catch (PDOException $e) {
                trigger_error($e->getMessage(), E_USER_ERROR);
            }//end try

            $msgBox = alertBox("Deleted successfully", "<i class='fa fa-check-square-o'></i>", "success");
            echo json_encode(array('success' => $msgBox));

        } else {
            $msgBox = alertBox("Insufficient privilege", "<i class='fa fa-times'></i>", "danger");
            echo json_encode(array('error' => $msgBox));
        }

    } else {
        $msgBox = alertBox("Insufficient privilege", "<i class='fa fa-times'></i>", "danger");
        echo json_encode(array('error' => $msgBox));
    }
}


