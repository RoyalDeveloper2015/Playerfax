<?php

if (!isset($_SESSION['userId'])) {
    header('Location: index.php?page=login');
    exit;
}

header('Content-Type: application/json');

if (isset($_POST) && count($_POST) > 0) {

    $token = isset($_POST['token']) ? $_POST['token'] : '';
    $token = filter_var($token, FILTER_SANITIZE_STRING);

    $reason = isset($_POST['reason']) ? $_POST['reason'] : '';
    $reason = preg_replace("/[^0-9]/", "", $reason);

    if (empty($reason)) {
        $msgBox = alertBox("Choose an option.", "<i class='fa fa-times'></i>", "danger");
        echo json_encode(array('error' => $msgBox));
        exit;
    }

    $count = 0;
    $postId = null;
    $postUserId = null;
    $postUserIdFrom = null;
    try {
        $sql = "
        SELECT 
          `Posts`.`PostId`,
          `Posts`.`UserId`,
          `Posts`.`UserIdFrom`
        FROM 
          `Posts` 
        WHERE
          `Posts`.`Token` = :Token";

        $stmt = $PDO->prepare($sql);
        $stmt->bindParam('Token', $token, PDO::PARAM_STR);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $count++;
            $postId = $row['PostId'];
            $postUserId = $row['UserId'];
            $postUserIdFrom = $row['UserIdFrom'];
        }

    } catch (PDOException $e) {
        trigger_error($e->getMessage(), E_USER_ERROR);
    }//end try

    if ($count > 0) {

        $created = date('Y-m-d H:i:s');
        $token = sha1($userIp . microseconds());
        try {
            $sql = "
            INSERT INTO `PostReports` 
            (
              `PostReportId`, 
              `Created`, 
              `UserId`, 
              `UserIdFrom`, 
              `PostId`, 
              `Reason`,
              `Token`
            ) VALUES (
              NULL, 
              :Created, 
              :UserId, 
              :UserIdFrom, 
              :PostId, 
              :Reason,
              :Token
            )";

            $stmt = $PDO->prepare($sql);
            $stmt->bindParam('Created', $created, PDO::PARAM_STR);
            $stmt->bindParam('UserId', $userId, PDO::PARAM_INT);
            $stmt->bindParam('UserIdFrom', $userId, PDO::PARAM_INT);
            $stmt->bindParam('PostId', $postId, PDO::PARAM_INT); // the postId to report
            $stmt->bindParam('Reason', $reason, PDO::PARAM_INT);
            $stmt->bindParam('Token', $token, PDO::PARAM_STR);
            $stmt->execute();

        } catch (PDOException $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
        }//end try

        try {
            $sql = "
            INSERT INTO `UserPostHidden` 
            (
              `UserPostHiddenId`, 
              `Created`, 
              `UserId`, 
              `UserIdFrom`, 
              `PostId`, 
              `Token`
            ) VALUES (
              NULL, 
              :Created, 
              :UserId, 
              :UserIdFrom, 
              :PostId, 
              :Token
            )";

            $stmt = $PDO->prepare($sql);
            $stmt->bindParam('Created', $created, PDO::PARAM_STR);
            $stmt->bindParam('UserId', $userId, PDO::PARAM_INT);
            $stmt->bindParam('UserIdFrom', $userId, PDO::PARAM_INT);
            $stmt->bindParam('PostId', $postId, PDO::PARAM_INT); // the postId to hide
            $stmt->bindParam('Token', $token, PDO::PARAM_STR);
            $stmt->execute();

        } catch (PDOException $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
        }//end try

        $msgBox = alertBox("Your complaint has been received.", "<i class='fa fa-check-square-o'></i>", "success");
        echo json_encode(array('success' => $msgBox));


    } else {
        $msgBox = alertBox("Insufficient privilege", "<i class='fa fa-times'></i>", "danger");
        echo json_encode(array('error' => $msgBox));
    }
}


