<?php

if (!isset($_SESSION['userId'])) {
    header('Location: index.php?page=login');
    exit;
}

header('Content-Type: application/json');


$prevLink = '<span class="disabled">&laquo;</span> <span class="disabled">&lsaquo;</span>';
$nextLink = '<span class="disabled">&rsaquo;</span> <span class="disabled">&raquo;</span>';
$paging = '<div id="paging-header"><p> No results found </p></div>';
$recordsTotal = 0;
$recordsFiltered = 0;
// How many items to list per page
$limit = 10;
$wallContent = '';

try {
    $sql_wall = "
    SELECT COUNT(`Result`.`WallId`) AS `recordsTotal`
      FROM (
        SELECT `Wall`.*
        FROM `Wall` 
        USE INDEX (`UserId`)
        WHERE
          (`Wall`.`UserId` = :UserId)
        UNION
        SELECT `Wall`.*
        FROM `Wall` 
        USE INDEX (`UserIdFrom`)
        WHERE
          (`Wall`.`UserIdFrom` = :UserIdFrom)
        ) AS `Result`
    WHERE 
      (`Result`.`MediaId` IS NOT NULL OR `Result`.`PostId` IS NOT NULL)
    AND 
      (`Result`.`MediaCommentId` IS NULL AND `Result`.`PostCommentId` IS NULL AND `Result`.`LikeId` IS NULL)";

    $stmt_wall = $PDO->prepare($sql_wall);
    $stmt_wall->bindParam('UserId', $userId, PDO::PARAM_INT);
    $stmt_wall->bindParam('UserIdFrom', $userId, PDO::PARAM_INT);
    $stmt_wall->execute();

    while ($row_wall = $stmt_wall->fetch(PDO::FETCH_ASSOC)) {
        $recordsTotal = (int)$row_wall['recordsTotal']; // 41
    }

} catch (PDOException $e) {
    trigger_error($e->getMessage(), E_USER_ERROR);
}//end try

if ($recordsTotal > 0) {

    // How many pages will there be
    $pages = ceil($recordsTotal / $limit); // 5

    // What page are we currently on?
    $page = filter_input(INPUT_POST, 'page', FILTER_VALIDATE_INT, array(
        'options' => array(
            'default' => 1,
            'min_range' => 1,
        ),
    )); // 5

    // Calculate the offset for the query
    $offset = ($page - 1) * $limit; // 40

    // Some information to display to the user
    $start = $offset + 1; // 41
    $end = min(($offset + $limit), $recordsTotal); // 41

    // The "back" link
    $prevLink = ($page > 1) ? '<a class="paging-link" href="1" title="First page">&laquo;</a> <a class="paging-link" href="' . ($page - 1) . '" title="Previous page">&lsaquo;</a>' : '<span class="disabled">&laquo;</span> <span class="disabled">&lsaquo;</span>';

    // The "forward" link
    $nextLink = ($page < $pages) ? '<a class="paging-link" href="' . ($page + 1) . '" title="Next page">&rsaquo;</a> <a class="paging-link" href="' . $pages . '" title="Last page">&raquo;</a>' : '<span class="disabled">&rsaquo;</span> <span class="disabled">&raquo;</span>';

    // Display the paging information
    $paging = '<div id="paging-header"><p> Page ' . $page . ' of ' . $pages . ' pages, displaying ' . $start . '-' . $end . ' of ' . $recordsTotal . ' results </p></div>';

    // determine when to stop searching
    $prevPage = ($page > 1) ? true : false;
    $nextPage = ($page < $pages) ? true : false;

    $likePostIds = array();
    $likeMediaIds = array();
    try {
        $sql = "
        SELECT 
          `Likes`.`PostId`,
          `Likes`.`MediaId`
        FROM 
          `Likes` 
        USE INDEX (`UserId`)
        WHERE
          `Likes`.`UserId` = :UserId";

        $stmt = $PDO->prepare($sql);
        $stmt->bindParam('UserId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (!empty($row['PostId'])) {
                array_push($likePostIds, $row['PostId']);
            }
            if (!empty($row['MediaId'])) {
                array_push($likeMediaIds, $row['MediaId']);
            }
        }

    } catch (PDOException $e) {
        trigger_error($e->getMessage(), E_USER_ERROR);
    }//end try

    $hidePostIds = array();
    try {
        $sql = "
        SELECT 
          `UserPostHidden`.`PostId`
        FROM 
          `UserPostHidden` 
        USE INDEX (`UserId`)
        WHERE
          `UserPostHidden`.`UserId` = :UserId";

        $stmt = $PDO->prepare($sql);
        $stmt->bindParam('UserId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            array_push($hidePostIds, $row['PostId']);
        }

    } catch (PDOException $e) {
        trigger_error($e->getMessage(), E_USER_ERROR);
    }//end try

    $hidePostCommentIds = array();
    try {
        $sql = "
        SELECT 
          `UserPostCommentHidden`.`PostCommentId`
        FROM 
          `UserPostCommentHidden` 
        USE INDEX (`UserId`)
        WHERE
          `UserPostCommentHidden`.`UserId` = :UserId";

        $stmt = $PDO->prepare($sql);
        $stmt->bindParam('UserId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            array_push($hidePostCommentIds, $row['PostCommentId']);
        }

    } catch (PDOException $e) {
        trigger_error($e->getMessage(), E_USER_ERROR);
    }//end try

    $hideMediaIds = array();
    try {
        $sql = "
        SELECT 
          `UserMediaHidden`.`MediaId`
        FROM 
          `UserMediaHidden` 
        USE INDEX (`UserId`)
        WHERE
          `UserMediaHidden`.`UserId` = :UserId";

        $stmt = $PDO->prepare($sql);
        $stmt->bindParam('UserId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            array_push($hideMediaIds, $row['MediaId']);
        }

    } catch (PDOException $e) {
        trigger_error($e->getMessage(), E_USER_ERROR);
    }//end try

    $hideMediaCommentIds = array();
    try {
        $sql = "
        SELECT 
          `UserMediaCommentHidden`.`MediaCommentId`
        FROM 
          `UserMediaCommentHidden` 
        USE INDEX (`UserId`)
        WHERE
          `UserMediaCommentHidden`.`UserId` = :UserId";

        $stmt = $PDO->prepare($sql);
        $stmt->bindParam('UserId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            array_push($hideMediaCommentIds, $row['MediaCommentId']);
        }

    } catch (PDOException $e) {
        trigger_error($e->getMessage(), E_USER_ERROR);
    }//end try

    $unfollowUserIds = array();
    try {
        $sql = "
        SELECT 
          `UserUnfollow`.`UserIdFrom`
        FROM 
          `UserUnfollow` 
        USE INDEX (`UserId`)
        WHERE
          `UserUnfollow`.`UserId` = :UserId";

        $stmt = $PDO->prepare($sql);
        $stmt->bindParam('UserId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            array_push($unfollowUserIds, $row['UserIdFrom']);
        }

    } catch (PDOException $e) {
        trigger_error($e->getMessage(), E_USER_ERROR);
    }//end try

    try {
        $sql_wall = "
        SELECT `Result`.*
          FROM (
            SELECT `Wall`.*
            FROM `Wall` 
            USE INDEX (`UserId`)
            WHERE
              (`Wall`.`UserId` = :UserId)
            UNION
            SELECT `Wall`.*
            FROM `Wall` 
            USE INDEX (`UserIdFrom`)
            WHERE
              (`Wall`.`UserIdFrom` = :UserIdFrom)
            ) AS `Result`
        WHERE 
          (`Result`.`MediaId` IS NOT NULL OR `Result`.`PostId` IS NOT NULL)
        AND 
          (`Result`.`MediaCommentId` IS NULL AND `Result`.`PostCommentId` IS NULL AND `Result`.`LikeId` IS NULL)
        ORDER BY `Result`.`Created` DESC
        LIMIT :Limit OFFSET :Offset";

        $stmt_wall = $PDO->prepare($sql_wall);
        $stmt_wall->bindParam('UserId', $userId, PDO::PARAM_INT);
        $stmt_wall->bindParam('UserIdFrom', $userId, PDO::PARAM_INT);
        $stmt_wall->bindParam('Limit', $limit, PDO::PARAM_INT);
        $stmt_wall->bindParam('Offset', $offset, PDO::PARAM_INT);
        $stmt_wall->execute();

        while ($row_wall = $stmt_wall->fetch(PDO::FETCH_ASSOC)) {
            $recordsFiltered++;

            $wallId = $row_wall['WallId'];
            $wallUserId = $row_wall['UserId'];
            $wallUserIdFrom = $row_wall['UserIdFrom'];
            $wallPlayerId = $row_wall['PlayerId'];
            $wallMediaId = $row_wall['MediaId'];
            $wallMediaCommentId = $row_wall['MediaCommentId'];
            $wallMessageId = $row_wall['MessageId'];
            $wallPostId = $row_wall['PostId'];
            $wallPostCommentId = $row_wall['PostCommentId'];
            $wallEventId = $row_wall['EventId'];
            $wallFollowId = $row_wall['FollowId'];
            $wallLikeId = $row_wall['LikeId'];
            $wallFanId = $row_wall['FanId'];
            $wallAlertId = $row_wall['AlertId'];
            $wallToken = $row_wall['Token'];

            // skip if user requested to unfollow all posts by this user
            if (in_array($wallUserIdFrom, $unfollowUserIds)) {
                continue;
            }

            // MEDIA
            if (!empty($wallMediaId)) {

                // skip if user requested to hide this post
                if (in_array($wallMediaId, $hideMediaIds)) {
                    continue;
                }

                try {
                    $sql_media = "
                    SELECT 
                      `Media`.*
                    FROM 
                      `Media` 
                    USE INDEX (`MediaIdStatus`)
                    WHERE
                      `Media`.`Status` = 4
                    AND 
                      `Media`.`MediaId` = :MediaId";

                    $stmt_media = $PDO->prepare($sql_media);
                    $stmt_media->bindParam('MediaId', $wallMediaId, PDO::PARAM_INT);
                    $stmt_media->execute();

                    while ($row_media = $stmt_media->fetch(PDO::FETCH_ASSOC)) {
                        $mediaId = $row_media['MediaId'];
                        $mediaUserId = $row_media['UserId'];
                        $mediaUserIdFrom = $row_media['UserIdFrom'];
                        $mediaCreated = $row_media['Created'];
                        $mediaCreatedTime = date('g:i a', strtotime($mediaCreated));
                        $mediaCreatedDate = date('M jS, Y', strtotime($mediaCreated));
                        $mediaLastUpdated = $row_media['LastUpdated'];
                        $mediaTitle = trim($row_media['Title']);
                        $mediaDescription = trim($row_media['Description']);
                        $mediaFileKey = $row_media['FileKey'];
                        $mediaFileName = $row_media['FileName'];
                        $mediaFileType = $row_media['FileType'];
                        $mediaToken = $row_media['Token'];
                        $mediaUrl = $row_media['Url'];
                        $mediaUserFullName = '';
                        $mediaUserPicture = '';

                        $wallMedia = '';
                        $targetFolder = constant('URL_UPLOADS_MEDIA');

                        if ($mediaFileType === 'image') {
                            $wallMedia = '<img src="' . $targetFolder . $mediaFileName . '" alt="image" class="img-thumbnail img-responsive"/>';
                        }
                        if ($mediaFileType === 'video') {
                            $wallMedia = '<video controls  poster="' . $targetFolder . $mediaFileName . '.jpg" style="width:100%;" class="img-thumbnail"><source src="' . $mediaUrl . '" type="video/mp4"></video>';
                        }

                        try {
                            $sql_user = "
                            SELECT 
                              `Users`.`FirstName`,  
                              `Users`.`LastName`,
                              `Users`.`Picture`,
                              `Users`.`Gender`
                            FROM 
                              `Users` 
                            USE INDEX (`PRIMARY`)
                            WHERE
                              `Users`.`UserId` = :UserId";

                            $stmt_user = $PDO->prepare($sql_user);
                            $stmt_user->bindParam('UserId', $mediaUserIdFrom, PDO::PARAM_INT);
                            $stmt_user->execute();

                            while ($row_user = $stmt_user->fetch(PDO::FETCH_ASSOC)) {

                                $mediaUserFullName = trim($row_user['FirstName'] . ' ' . $row_user['LastName']);

                                if (!empty($row_user['Picture']) && file_exists(constant('UPLOADS_USERS') . $row_user['Picture'])) {
                                    $mediaUserPicture = constant('URL_UPLOADS_USERS') . $row_user['Picture'];
                                } else {
                                    if ($row_user['Gender'] == 0) {
                                        $mediaUserPicture = constant('URL_UPLOADS_USERS') . 'default-male.png';
                                    } else {
                                        $mediaUserPicture = constant('URL_UPLOADS_USERS') . 'default-female.png';
                                    }
                                }
                            }

                        } catch (PDOException $e) {
                            trigger_error($e->getMessage(), E_USER_ERROR);
                        }//end try

                        // default state (share button will always be a default gray button)
                        $like_share = '<button href="#" class="like-button btn btn-gray" data-type="media" data-token="' . $mediaToken . '"> Like</button>
                                    <button href="#" class="share-button btn btn-gray" data-type="media" data-token="' . $mediaToken . '" data-toggle="modal" data-target="#share_media"><i class="fa fa-share"></i> Share</button>';
                        if (in_array($mediaId, $likeMediaIds)) {
                            // liked state (share button will always be a default gray button)
                            $like_share = '<button href="#" class="like-button btn btn-success" data-type="media" data-token="' . $mediaToken . '"><i class="fa fa-thumbs-o-up"></i> Like</button>
                                    <button href="#" class="share-button btn btn-gray" data-type="media" data-token="' . $mediaToken . '" data-toggle="modal" data-target="#share_media"><i class="fa fa-share"></i> Share</button>';
                        }

                        $comments = '';
                        try {
                            $sql_comment = "
                            SELECT 
                              `MediaComments`.`MediaCommentId` AS `CommentId`,
                              `MediaComments`.`Token` AS `CommentToken`,
                              `MediaComments`.`Created` AS `CommentCreated`,
                              `MediaComments`.`UserIdFrom` AS `CommentUserIdFrom`,
                              `MediaComments`.`Content` AS `CommentContent`,
                              `MediaComments`.`Token` AS `CommentToken`,
                              `Users`.`FirstName` AS `CommentFirstName`,
                              `Users`.`LastName` AS `CommentLastName`,
                              `Users`.`Gender` AS `CommentGender`,
                              `Users`.`Picture` AS `CommentPicture`
                            FROM 
                              `MediaComments` 
                            USE INDEX (`MediaId`)
                            LEFT JOIN `Users` USE INDEX FOR JOIN (`PRIMARY`) ON `MediaComments`.`UserId` = `Users`.`UserId`
                            WHERE
                              `MediaComments`.`MediaId` = :MediaId";

                            $stmt_comment = $PDO->prepare($sql_comment);
                            $stmt_comment->bindParam('MediaId', $mediaId, PDO::PARAM_INT);
                            $stmt_comment->execute();

                            while ($row_comment = $stmt_comment->fetch(PDO::FETCH_ASSOC)) {
                                $count++;

                                // skip if user requested to hide a comment
                                if (in_array($row_comment['CommentId'], $hideMediaCommentIds)) {
                                    continue;
                                }

                                $commentUserIdFrom = $row_comment['CommentUserIdFrom'];

                                $commentUserFullName = trim($row_comment['CommentFirstName'] . ' ' . $row_comment['CommentLastName']);

                                if (!empty($row_comment['CommentPicture']) && file_exists(constant('UPLOADS_USERS') . $row_comment['CommentPicture'])) {
                                    $commentUserPicture = constant('URL_UPLOADS_USERS') . $row_comment['CommentPicture'];
                                } else {
                                    if ($row_comment['Gender'] == 0) {
                                        $commentUserPicture = constant('URL_UPLOADS_USERS') . 'default-male.png';
                                    } else {
                                        $commentUserPicture = constant('URL_UPLOADS_USERS') . 'default-female.png';
                                    }
                                }

                                $comment_id = md5($userIp . $row_comment['CommentId']);

                                $data_relation = '';
                                if ($commentUserIdFrom == $userId) {
                                    $data_relation = 'me';
                                }

                                $comments .= '<div id="comment_' . $comment_id . '"  class="wall_desc_main">
                                    <div class="like_share_main">
                                        <div class="user_left">
                                            <img src="' . $commentUserPicture . '" alt="' . $commentUserFullName . '" class="img-circle img-responsive user_img_profile_picture" style="max-width:34px;max-height:34px;">
                                            <div class="msg_content">
                                                ' . $row_comment['CommentContent'] . '
                                            </div>
                                            <a href="#" class="dd-comment" data-token="' . $row_comment['CommentToken'] . '" data-type="media-comment" data-id="' . $comment_id . '" data-relation="' . $data_relation . '"></a>
                                        </div>
                                    </div>
                                </div>';
                            }

                        } catch (PDOException $e) {
                            trigger_error($e->getMessage(), E_USER_ERROR);
                        }//end try

                        $data_id = md5($userIp . $mediaId);

                        $data_relation = '';
                        if ($mediaUserIdFrom == $userId) {
                            $data_relation = 'me';
                        }

                        $wallContent .= '<div id="post_' . $data_id . '" class="single_wall">
                             <div class="wall_desc_main">
                                <div class="like_share_main">
                                    <div class="user_left">
                                        <img src="' . $mediaUserPicture . '" alt="' . $mediaUserFullName . '" class="img-circle img-responsive user_img_profile_picture">
                                        <div class="msg_content">
                                            <h6>' . $mediaUserFullName . '</h6>
                                            <p><span>' . $mediaCreatedTime . ' </span> ' . $mediaCreatedDate . '</p>
                                        </div>
                                        <a href="#" class="dd-post" data-token="' . $mediaToken . '" data-type="media" data-id="' . $data_id . '" data-relation="' . $data_relation . '"></a>
                                    </div>
                                </div>
                                <div class="wall_img center-block">
                                    ' . $wallMedia . '
                                </div>
                                <div class="wall_title"><h5>' . $mediaTitle . '</h5></div>
                                <div class="desc_content">' . $mediaDescription . '</div>
                                <hr style="margin:0;">
                                <div class="like_share">
                                   ' . $like_share . '
                                </div>
                            </div>
                            <div class="wall_desc_main">
                                <div class="like_share_main">
                                    <div class="user_left">
                                        <img src="' . $userPicture . '" alt="' . $userFullName . '" class="img-circle img-responsive user_img_profile_picture" style="max-width:34px;max-height:34px;">
                                        <div class="msg_content2">
                                            <form method="post" class="comment-form" data-type="media" data-token="' . $mediaToken . '">
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
                            ' . $comments . '
                        </div>';


                    }
                } catch (PDOException $e) {
                    trigger_error($e->getMessage(), E_USER_ERROR);
                }//end try

            } // end if MEDIA

            // POST
            if (!empty($wallPostId)) {

                // skip if user requested to hide this post
                if (in_array($wallPostId, $hidePostIds)) {
                    continue;
                }

                try {
                    $sql_post = "
                    SELECT 
                      `Posts`.*
                    FROM 
                      `Posts` 
                    USE INDEX (`PRIMARY`)
                    WHERE
                      `Posts`.`PostId` = :PostId";

                    $stmt_post = $PDO->prepare($sql_post);
                    $stmt_post->bindParam('PostId', $wallPostId, PDO::PARAM_INT);
                    $stmt_post->execute();

                    while ($row_post = $stmt_post->fetch(PDO::FETCH_ASSOC)) {
                        $postId = $row_post['PostId'];
                        $postUserId = $row_post['UserId'];
                        $postUserIdFrom = $row_post['UserIdFrom'];
                        $postCreated = $row_post['Created'];
                        $postCreatedTime = date('g:i a', strtotime($postCreated));
                        $postCreatedDate = date('M jS, Y', strtotime($postCreated));
                        $postLastUpdated = $row_post['LastUpdated'];
                        $postContent = $row_post['Content'];
                        $postToken = $row_post['Token'];
                        $postUserFullName = '';
                        $postUserPicture = '';

                        try {
                            $sql_user = "
                            SELECT 
                              `Users`.`FirstName`,  
                              `Users`.`LastName`,
                              `Users`.`Picture`,
                              `Users`.`Gender`
                            FROM 
                              `Users` 
                            USE INDEX (`PRIMARY`)
                            WHERE
                              `Users`.`UserId` = :UserId";

                            $stmt_user = $PDO->prepare($sql_user);
                            $stmt_user->bindParam('UserId', $postUserIdFrom, PDO::PARAM_INT);
                            $stmt_user->execute();

                            while ($row_user = $stmt_user->fetch(PDO::FETCH_ASSOC)) {

                                $postUserFullName = trim($row_user['FirstName'] . ' ' . $row_user['LastName']);

                                if (!empty($row_user['Picture']) && file_exists(constant('UPLOADS_USERS') . $row_user['Picture'])) {
                                    $postUserPicture = constant('URL_UPLOADS_USERS') . $row_user['Picture'];
                                } else {
                                    if ($row_user['Gender'] == 0) {
                                        $postUserPicture = constant('URL_UPLOADS_USERS') . 'default-male.png';
                                    } else {
                                        $postUserPicture = constant('URL_UPLOADS_USERS') . 'default-female.png';
                                    }
                                }
                            }

                        } catch (PDOException $e) {
                            trigger_error($e->getMessage(), E_USER_ERROR);
                        }//end try

                        // default state (share button will always be a default gray button)
                        $like_share = '<button href="#" class="like-button btn btn-gray" data-type="post" data-token="' . $postToken . '"> Like</button>
                                    <button href="#" class="share-button btn btn-gray" data-type="post" data-token="' . $postToken . '" data-toggle="modal" data-target="#share_post"><i class="fa fa-share"></i> Share</button>';
                        if (in_array($postId, $likePostIds)) {
                            // liked state (share button will always be a default gray button)
                            $like_share = '<button href="#" class="like-button btn btn-success" data-type="post" data-token="' . $postToken . '"><i class="fa fa-thumbs-o-up"></i> Like</button>
                                    <button href="#" class="share-button btn btn-gray" data-type="post" data-token="' . $postToken . '" data-toggle="modal" data-target="#share_post"><i class="fa fa-share"></i> Share</button>';
                        }

                        $comments = '';
                        try {
                            $sql_comment = "
                            SELECT 
                              `PostComments`.`PostCommentId` AS `CommentId`,
                              `PostComments`.`Token` AS `CommentToken`,
                              `PostComments`.`Created` AS `CommentCreated`,
                              `PostComments`.`UserIdFrom` AS `CommentUserIdFrom`,
                              `PostComments`.`Content` AS `CommentContent`,
                              `PostComments`.`Token` AS `CommentToken`,
                              `Users`.`FirstName` AS `CommentFirstName`,
                              `Users`.`LastName` AS `CommentLastName`,
                              `Users`.`Gender` AS `CommentGender`,
                              `Users`.`Picture` AS `CommentPicture`
                            FROM 
                              `PostComments` 
                            USE INDEX (`PostId`)
                            LEFT JOIN `Users` USE INDEX FOR JOIN (`PRIMARY`) ON `PostComments`.`UserId` = `Users`.`UserId`
                            WHERE 
                              `PostComments`.`PostId` = :PostId";

                            $stmt_comment = $PDO->prepare($sql_comment);
                            $stmt_comment->bindParam('PostId', $postId, PDO::PARAM_INT);
                            $stmt_comment->execute();

                            while ($row_comment = $stmt_comment->fetch(PDO::FETCH_ASSOC)) {
                                $count++;

                                // skip if user requested to hide a comment
                                if (in_array($row_comment['CommentId'], $hidePostCommentIds)) {
                                    continue;
                                }

                                $commentUserIdFrom = $row_comment['CommentUserIdFrom'];

                                $commentUserFullName = trim($row_comment['CommentFirstName'] . ' ' . $row_comment['CommentLastName']);

                                if (!empty($row_comment['CommentPicture']) && file_exists(constant('UPLOADS_USERS') . $row_comment['CommentPicture'])) {
                                    $commentUserPicture = constant('URL_UPLOADS_USERS') . $row_comment['CommentPicture'];
                                } else {
                                    if ($row_comment['Gender'] == 0) {
                                        $commentUserPicture = constant('URL_UPLOADS_USERS') . 'default-male.png';
                                    } else {
                                        $commentUserPicture = constant('URL_UPLOADS_USERS') . 'default-female.png';
                                    }
                                }

                                $comment_id = md5($userIp . $row_comment['CommentId']);

                                $data_relation = '';
                                if ($commentUserIdFrom == $userId) {
                                    $data_relation = 'me';
                                }

                                $comments .= '<div id="comment_' . $comment_id . '" class="wall_desc_main">
                                    <div class="like_share_main">
                                        <div class="user_left">
                                            <img src="' . $commentUserPicture . '" alt="' . $commentUserFullName . '" class="img-circle img-responsive user_img_profile_picture" style="max-width:34px;max-height:34px;">
                                            <div class="msg_content">
                                                ' . $row_comment['CommentContent'] . '
                                            </div>
                                            <a href="#" class="dd-comment" data-token="' . $row_comment['CommentToken'] . '" data-type="post-comment" data-id="' . $comment_id . '" data-relation="' . $data_relation . '"></a>
                                        </div>
                                    </div>
                                </div>';
                            }

                        } catch (PDOException $e) {
                            trigger_error($e->getMessage(), E_USER_ERROR);
                        }//end try

                        $data_id = md5($userIp . $postId);

                        $data_relation = '';
                        if ($postUserIdFrom == $userId) {
                            $data_relation = 'me';
                        }

                        $wallContent .= '<div id="post_' . $data_id . '" class="single_wall">
                            <div class="wall_desc_main">
                                <div class="like_share_main">
                                    <div class="user_left">
                                        <img src="' . $postUserPicture . '" alt="' . $postUserFullName . '" class="img-circle img-responsive user_img_profile_picture">
                                        <div class="msg_content">
                                            <h6>' . $postUserFullName . '</h6>
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
                                        <img src="' . $userPicture . '" alt="' . $userFullName . '" class="img-circle img-responsive user_img_profile_picture" style="max-width:34px;max-height:34px;">
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
                            ' . $comments . '
                        </div>';

                    }
                } catch (PDOException $e) {
                    trigger_error($e->getMessage(), E_USER_ERROR);
                }//end try

            } // end if POST

        } // end while row_wall

    } catch (PDOException $e) {
        trigger_error($e->getMessage(), E_USER_ERROR);
    }//end try

    //echo 'Records Filtered: ' . $recordsFiltered;

    if ($recordsFiltered > 0) {
        echo json_encode(array('success' => 'Results fetched successfully', 'content' => $wallContent));
    } else {
        echo json_encode(array('error' => 'No filtered results'));
    }

} else {
    echo json_encode(array('error' => 'No results'));
}

