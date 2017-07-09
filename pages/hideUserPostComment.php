<?php

if (!isset($_SESSION['userId'])) {
    header('Location: index.php?page=login');
    exit;
}

header('Content-Type: application/json');

if (isset($_POST) && count($_POST) > 0) {

    $token = isset($_POST['token']) ? $_POST['token'] : '';

    $count = 0;
    $postCommentId = null;
    $postCommentUserId = null;
    $postCommentUserIdFrom = null;
    try {
        $sql = "
        SELECT 
          `PostComments`.`PostCommentId`,
          `PostComments`.`UserId`,
          `PostComments`.`UserIdFrom`
        FROM 
          `PostComments` 
        WHERE
          `PostComments`.`Token` = :Token";

        $stmt = $PDO->prepare($sql);
        $stmt->bindParam('Token', $token, PDO::PARAM_STR);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $count++;
            $postCommentId = $row['PostCommentId'];
            $postCommentUserId = $row['UserId'];
            $postCommentUserIdFrom = $row['UserIdFrom'];
        }

    } catch (PDOException $e) {
        trigger_error($e->getMessage(), E_USER_ERROR);
    }//end try

    if ($count > 0) {

        $created = date('Y-m-d H:i:s');
        $token = sha1($userIp . microseconds());
        try {
            $sql = "
            INSERT INTO `UserPostCommentHidden` 
            (
              `UserPostCommentHiddenId`, 
              `Created`, 
              `UserId`, 
              `UserIdFrom`, 
              `PostCommentId`, 
              `Token`
            ) VALUES (
              NULL, 
              :Created, 
              :UserId, 
              :UserIdFrom, 
              :PostCommentId, 
              :Token
            )";

            $stmt = $PDO->prepare($sql);
            $stmt->bindParam('Created', $created, PDO::PARAM_STR);
            $stmt->bindParam('UserId', $userId, PDO::PARAM_INT);
            $stmt->bindParam('UserIdFrom', $postCommentUserIdFrom, PDO::PARAM_INT);
            $stmt->bindParam('PostCommentId', $postCommentId, PDO::PARAM_INT); // the postCommentId to hide
            $stmt->bindParam('Token', $token, PDO::PARAM_STR);
            $stmt->execute();

        } catch (PDOException $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
        }//end try


        $msgBox = alertBox("Hidden successfully", "<i class='fa fa-check-square-o'></i>", "success");
        echo json_encode(array('success' => $msgBox));


    } else {
        $msgBox = alertBox("Insufficient privilege", "<i class='fa fa-times'></i>", "danger");
        echo json_encode(array('error' => $msgBox));
    }
}


