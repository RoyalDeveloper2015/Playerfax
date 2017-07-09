<?php

if (!isset($_SESSION['userId'])) {
    header('Location: index.php?page=login');
    exit;
}

header('Content-Type: application/json');

if (isset($_POST) && count($_POST) > 0) {

    $lastUpdated = date('Y-m-d H:i:s');

    $token = isset($_POST['token']) ? $_POST['token'] : '';

    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    $message = filter_var($message, FILTER_SANITIZE_STRING);

    if (!empty($message)) {
        $count = 0;
        $postCommentId = null;
        try {
            $sql = "
            SELECT 
              `PostComments`.`PostCommentId`
            FROM 
              `PostComments` 
            WHERE
              `PostComments`.`UserIdFrom` = :UserIdFrom
            AND 
              `PostComments`.`Token` = :Token";

            $stmt = $PDO->prepare($sql);
            $stmt->bindParam('Token', $token, PDO::PARAM_INT);
            $stmt->bindParam('UserIdFrom', $userId, PDO::PARAM_INT);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $count++;
                $postCommentId = $row['PostCommentId'];
            }

        } catch (PDOException $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
        }//end try

        if ($count > 0) {
            try {
                $sql = "
                UPDATE 
                  `PostComments`
                SET
                  `PostComments`.`Content` = :Content,
                  `PostComments`.`LastUpdated` = :LastUpdated
                WHERE
                  `PostComments`.`PostCommentId` = :PostCommentId
                AND 
                  `PostComments`.`UserIdFrom` = :UserIdFrom";

                $stmt = $PDO->prepare($sql);
                $stmt->bindParam('Content', $message, PDO::PARAM_STR);
                $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR); // Update date
                $stmt->bindParam('PostCommentId', $postCommentId, PDO::PARAM_INT);
                $stmt->bindParam('UserIdFrom', $userId, PDO::PARAM_INT); // My logged in userId
                $stmt->execute();

            } catch (PDOException $e) {
                trigger_error($e->getMessage(), E_USER_ERROR);
            }//end try

            $msgBox = alertBox("Updated successfully", "<i class='fa fa-check-square-o'></i>", "success");
            echo json_encode(array('success' => $msgBox, 'content' => $message));
        } else {
            $msgBox = alertBox("Insufficient privilege", "<i class='fa fa-times'></i>", "danger");
            echo json_encode(array('error' => $msgBox));
        }
    } else {
        $msgBox = alertBox("Message is required.", "<i class='fa fa-times'></i>", "danger");
        echo json_encode(array('error' => $msgBox));
    }
}

