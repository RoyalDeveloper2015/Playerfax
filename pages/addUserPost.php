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

if (isset($_POST) && count($_POST) > 0) {

    $postContent = isset($_POST['message']) ? trim($_POST['message']) : '';
    $postContent = filter_var($postContent, FILTER_SANITIZE_STRING);

    if (!empty($postContent)) {

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
                  `Content`, 
                  `Token`
                ) VALUES (
                  NULL, 
                  :Created,
                  :UserId, 
                  :UserIdFrom, 
                  :Content, 
                  :Token
                )";

            $stmt = $PDO->prepare($sql);
            $stmt->bindParam('Created', $postCreated, PDO::PARAM_STR);
            $stmt->bindParam('UserId', $userId, PDO::PARAM_INT);
            $stmt->bindParam('UserIdFrom', $userId, PDO::PARAM_INT);
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
            $stmt->bindParam('UserId', $userId, PDO::PARAM_INT);
            $stmt->bindParam('Token', $postToken, PDO::PARAM_STR);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $postId = $row['PostId'];
            }

        } catch (PDOException $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
        }//end try

        $likePostIds = array();
        try {
            $sql = "
            SELECT 
              `Likes`.`PostId`
            FROM 
              `Likes` 
            USE INDEX (`UserIdPostId`)
            WHERE
              `Likes`.`UserId` = :UserId
            AND 
              `Likes`.`PostId` = :PostId";

            $stmt = $PDO->prepare($sql);
            $stmt->bindParam('UserId', $userId, PDO::PARAM_INT);
            $stmt->bindParam('PostId', $postId, PDO::PARAM_INT);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                array_push($likePostIds, $row['PostId']);
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

            $stmt_wall = $PDO->prepare($sql_wall);
            $stmt_wall->bindParam('Created', $postCreated, PDO::PARAM_STR); // timestamp of when user was posting
            $stmt_wall->bindParam('UserId', $userId, PDO::PARAM_INT); // Owner of player
            $stmt_wall->bindParam('UserIdFrom', $userId, PDO::PARAM_INT); // My logged in userId (one who is posting video)
            $stmt_wall->bindParam('PostId', $postId, PDO::PARAM_INT);
            $stmt_wall->bindParam('Token', $wallToken, PDO::PARAM_STR);
            $stmt_wall->execute();

        } catch (PDOException $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
        }//end try

        $postCreatedTime = date('g:i a', strtotime($postCreated));
        $postCreatedDate = date('M jS, Y', strtotime($postCreated));

        // default state (share button will always be a default gray button)
        $like_share = '<button href="#" class="like-button btn btn-gray" data-type="post" data-token="' . $postToken . '"> Like</button>
                                    <button href="#" class="share-button btn btn-gray" data-type="post" data-token="' . $postToken . '" data-toggle="modal" data-target="#share_media"><i class="fa fa-share"></i> Share</button>';
        if (in_array($postId, $likePostIds)) {
            // liked state (share button will always be a default gray button)
            $like_share = '<button href="#" class="like-button btn btn-success" data-type="post" data-token="' . $postToken . '"><i class="fa fa-thumbs-o-up"></i> Like</button>
                                    <button href="#" class="share-button btn btn-gray" data-type="post" data-token="' . $postToken . '" data-toggle="modal" data-target="#share_media"><i class="fa fa-share"></i> Share</button>';
        }

        $data_id = md5($userIp . $postId);

        $data_relation = 'me';

        $wallContent .= '<div id="post_' . $data_id . '" class="single_wall">
            <div class="wall_desc_main">
                <div class="like_share_main">
                    <div class="user_left">
                        <img src="' . $userPicture . '" alt="image" class="img-circle img-responsive user_img_profile_picture">
                        <div class="msg_content">
                            <h6>' . $userFullName . '</h6>
                            <p><span>' . $postCreatedTime . ' </span> ' . $postCreatedDate . '</p>
                        </div>
                        <a href="#" class="dd-post" data-token="' . $postToken . '" data-type="post" data-id="' . $data_id . '" data-relation="' . $data_relation . '"></a>
                    </div>
                </div>
                <div class="desc_content">
                    ' . $postContent . '
                </div>
                <hr style="margin:0;">
                <div class="like_share">
                    ' . $like_share . '
                </div>
            </div>
            <div class="wall_desc_main">
                <div class="like_share_main">
                    <div class="user_left">
                        <img src="' . $userPicture . '" alt="image" class="img-circle img-responsive user_img_profile_picture" style="max-width:34px;max-height:34px;">
                        <div class="msg_content2">
                            <form method="post" class="comment-form" data-type="post" data-token="' . $postToken . '">
                                <div class="form-group">
                                    <textarea style="width:100%;" class="form-control input_box" placeholder="Write a comment..."></textarea>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-success" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>';

        $msgBox = alertBox("You have successfully posted to your Timeline", "<i class='fa fa-check-square-o'></i>", "success");
        echo json_encode(array('success' => $msgBox, 'content' => $wallContent));

    } else {
        $msgBox = alertBox("Message is required.", "<i class='fa fa-times'></i>", "danger");
        echo json_encode(array('error' => $msgBox));
        exit;
    }

}



