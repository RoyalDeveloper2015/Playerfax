<?php

if (!isset($_SESSION['userId'])) {
    header('Location: index.php?page=login');
    exit;
}

header('Content-Type: application/json');

if (isset($_POST) && count($_POST) > 0) {

    $comment = isset($_POST['message']) ? trim($_POST['message']) : '';
    $comment = filter_var($comment, FILTER_SANITIZE_STRING);

    $type = isset($_POST['type']) ? trim($_POST['type']) : 'post';
    $type = filter_var($type, FILTER_SANITIZE_STRING);

    $parentToken = isset($_POST['token']) ? trim($_POST['token']) : '';
    $parentToken = filter_var($parentToken, FILTER_SANITIZE_STRING);

    $created = date('Y-m-d H:i:s');
    $token = sha1($userIp . microseconds());

    if (!empty($comment)) {

        if ($type === 'post') {

            $postId = null;
            $postUserId = null;
            try {
                $sql = "
                SELECT 
                  `Posts`.`PostId`, 
                  `Posts`.`UserId`
                FROM 
                  `Posts` 
                WHERE
                  `Posts`.`Token` = :Token";

                $stmt = $PDO->prepare($sql);
                $stmt->bindParam('Token', $parentToken, PDO::PARAM_STR);
                $stmt->execute();

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $postId = $row['PostId'];
                    $postUserId = $row['UserId'];
                }

            } catch (PDOException $e) {
                trigger_error($e->getMessage(), E_USER_ERROR);
            }//end try

            // insert post
            try {
                $sql = "
                INSERT INTO `PostComments` 
                (
                  `PostCommentId`, 
                  `Created`, 
                  `UserId`, 
                  `UserIdFrom`, 
                  `PostId`, 
                  `Content`, 
                  `Token`
                ) VALUES (
                  NULL, 
                  :Created,
                  :UserId, 
                  :UserIdFrom, 
                  :PostId,
                  :Content, 
                  :Token
                )";

                $stmt = $PDO->prepare($sql);
                $stmt->bindParam('Created', $created, PDO::PARAM_STR);
                $stmt->bindParam('UserId', $userId, PDO::PARAM_INT);
                $stmt->bindParam('UserIdFrom', $userId, PDO::PARAM_INT);
                $stmt->bindParam('PostId', $postId, PDO::PARAM_INT);
                $stmt->bindParam('Content', $comment, PDO::PARAM_STR);
                $stmt->bindParam('Token', $token, PDO::PARAM_STR);
                $stmt->execute();
            } catch (PDOException $e) {
                trigger_error($e->getMessage(), E_USER_ERROR);
            }//end try

            // get PostCommentId
            $postCommentId = null;
            $postCommentUserIdFrom = null;
            try {
                $sql = "
                SELECT 
                  `PostComments`.`PostCommentId`,
                  `PostComments`.`UserIdFrom`
                FROM 
                  `PostComments` 
                WHERE
                  `PostComments`.`UserId` = :UserId
                AND
                  `PostComments`.`Token` = :Token";

                $stmt = $PDO->prepare($sql);
                $stmt->bindParam('UserId', $userId, PDO::PARAM_INT);
                $stmt->bindParam('Token', $token, PDO::PARAM_STR);
                $stmt->execute();

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $postCommentId = $row['PostCommentId'];
                    $postCommentUserIdFrom = $row['UserIdFrom'];
                }

            } catch (PDOException $e) {
                trigger_error($e->getMessage(), E_USER_ERROR);
            }//end try

            // insert wall PostCommentId
            $wallToken = sha1($userIp . microseconds());
            try {
                $sql_wall = "
                INSERT INTO `Wall` 
                (
                  `WallId`, 
                  `Created`, 
                  `UserId`, 
                  `UserIdFrom`, 
                  `PostId`, 
                  `PostCommentId`, 
                  `Token`
                ) VALUES (
                  NULL, 
                  :Created, 
                  :UserId, 
                  :UserIdFrom, 
                  :PostId,
                  :PostCommentId, 
                  :Token
                )";

                $stmt_wall = $PDO->prepare($sql_wall);
                $stmt_wall->bindParam('Created', $created, PDO::PARAM_STR); // timestamp of when user created a Like
                $stmt_wall->bindParam('UserId', $userId, PDO::PARAM_INT); // Owner of Like
                $stmt_wall->bindParam('UserIdFrom', $userId, PDO::PARAM_INT); // My logged in userId (one who created the Like)
                $stmt_wall->bindParam('PostId', $postId, PDO::PARAM_INT);
                $stmt_wall->bindParam('PostCommentId', $postCommentId, PDO::PARAM_INT);
                $stmt_wall->bindParam('Token', $wallToken, PDO::PARAM_STR);
                $stmt_wall->execute();

            } catch (PDOException $e) {
                trigger_error($e->getMessage(), E_USER_ERROR);
            }//end try

            $data_id = md5($userIp . $postCommentId);

            $data_relation = '';
            if ($postCommentUserIdFrom == $userId) {
                $data_relation = 'me';
            }

            $content = '<div id="comment_' . $data_id . '" class="wall_desc_main">
                <div class="like_share_main">
                    <div class="user_left">
                        <img src="' . $userPicture . '" alt="' . $userFullName . '" class="img-circle img-responsive user_img_profile_picture" style="max-width:34px;max-height:34px;">
                        <div class="msg_content">
                            <span>' . $comment . '</span>
                        </div>
                        <a href="#" class="dd-comment" data-token="' . $token . '" data-type="post-comment" data-id="' . $data_id . '" data-relation="' . $data_relation . '"></a>
                    </div>
                </div>
            </div>';

            $msgBox = alertBox("You have successfully posted to your Timeline", "<i class='fa fa-check-square-o'></i>", "success");
            echo json_encode(array('success' => $msgBox, 'content' => $content, 'id' => $data_id));

        } else if ($type === 'media') {

            $mediaId = null;
            $mediaUserId = null;
            try {
                $sql = "
                SELECT 
                  `Media`.`MediaId`, 
                  `Media`.`UserId`
                FROM 
                  `Media` 
                WHERE
                  `Media`.`Token` = :Token";

                $stmt = $PDO->prepare($sql);
                $stmt->bindParam('Token', $parentToken, PDO::PARAM_STR);
                $stmt->execute();

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $mediaId = $row['MediaId'];
                    $mediaUserId = $row['UserId'];
                }

            } catch (PDOException $e) {
                trigger_error($e->getMessage(), E_USER_ERROR);
            }//end try

            // insert post
            try {
                $sql = "
                INSERT INTO `MediaComments` 
                (
                  `MediaCommentId`, 
                  `Created`, 
                  `UserId`, 
                  `UserIdFrom`, 
                  `MediaId`, 
                  `Content`, 
                  `Token`
                ) VALUES (
                  NULL, 
                  :Created,
                  :UserId, 
                  :UserIdFrom, 
                  :MediaId,
                  :Content, 
                  :Token
                )";

                $stmt = $PDO->prepare($sql);
                $stmt->bindParam('Created', $created, PDO::PARAM_STR);
                $stmt->bindParam('UserId', $userId, PDO::PARAM_INT);
                $stmt->bindParam('UserIdFrom', $userId, PDO::PARAM_INT);
                $stmt->bindParam('MediaId', $mediaId, PDO::PARAM_INT);
                $stmt->bindParam('Content', $comment, PDO::PARAM_STR);
                $stmt->bindParam('Token', $token, PDO::PARAM_STR);
                $stmt->execute();
            } catch (PDOException $e) {
                trigger_error($e->getMessage(), E_USER_ERROR);
            }//end try

            // get MediaCommentId
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
                $stmt->bindParam('UserId', $userId, PDO::PARAM_INT);
                $stmt->bindParam('Token', $token, PDO::PARAM_STR);
                $stmt->execute();

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $mediaCommentId = $row['MediaCommentId'];
                    $mediaCommentUserIdFrom = $row['UserIdFrom'];
                }

            } catch (PDOException $e) {
                trigger_error($e->getMessage(), E_USER_ERROR);
            }//end try

            // insert wall MediaCommentId
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
                  `MediaCommentId`, 
                  `Token`
                ) VALUES (
                  NULL, 
                  :Created, 
                  :UserId, 
                  :UserIdFrom, 
                  :MediaId,
                  :MediaCommentId, 
                  :Token
                )";

                $stmt_wall = $PDO->prepare($sql_wall);
                $stmt_wall->bindParam('Created', $created, PDO::PARAM_STR); // timestamp of when user created a Like
                $stmt_wall->bindParam('UserId', $userId, PDO::PARAM_INT); // Owner of Like
                $stmt_wall->bindParam('UserIdFrom', $userId, PDO::PARAM_INT); // My logged in userId (one who created the Like)
                $stmt_wall->bindParam('MediaId', $mediaId, PDO::PARAM_INT);
                $stmt_wall->bindParam('MediaCommentId', $mediaCommentId, PDO::PARAM_INT);
                $stmt_wall->bindParam('Token', $wallToken, PDO::PARAM_STR);
                $stmt_wall->execute();

            } catch (PDOException $e) {
                trigger_error($e->getMessage(), E_USER_ERROR);
            }//end try

            $data_id = md5($userIp . $mediaCommentId);

            $data_relation = '';
            if ($mediaCommentUserIdFrom == $userId) {
                $data_relation = 'me';
            }

            $content = '<div id="comment_' . $data_id . '" class="wall_desc_main">
                <div class="like_share_main">
                    <div class="user_left">
                        <img src="' . $userPicture . '" alt="' . $userFullName . '" class="img-circle img-responsive user_img_profile_picture" style="max-width:34px;max-height:34px;">
                        <div class="msg_content">
                            <span>' . $comment . '</span>
                        </div>
                        <a href="#" class="dd-comment" data-token="' . $token . '" data-type="post-comment" data-id="' . $data_id . '" data-relation="' . $data_relation . '"></a>
                    </div>
                </div>
            </div>';

            $msgBox = alertBox("You have successfully posted to the Timeline", "<i class='fa fa-check-square-o'></i>", "success");
            echo json_encode(array('success' => $msgBox, 'content' => $content, 'id' => $data_id));

        } else {
            $msgBox = alertBox("Message is required.", "<i class='fa fa-times'></i>", "danger");
            echo json_encode(array('error' => $msgBox));
            exit;
        }

    } else {
        $msgBox = alertBox("Message is required.", "<i class='fa fa-times'></i>", "danger");
        echo json_encode(array('error' => $msgBox));
        exit;
    }

}



