<?php

if (!isset($_SESSION['userId'])) {
    header('Location: index.php?page=login');
    exit;
}

header('Content-Type: application/json');

$playerToken = isset($_POST['token']) ? trim($_POST['token']) : '';
$playerToken = filter_var($playerToken, FILTER_SANITIZE_STRING);

$prevLink = '<span class="disabled">&laquo;</span> <span class="disabled">&lsaquo;</span>';
$nextLink = '<span class="disabled">&rsaquo;</span> <span class="disabled">&raquo;</span>';
$paging = '<div id="paging-header"><p> No results found </p></div>';
$recordsTotal = 0;
$recordsFiltered = 0;
// How many items to list per page
$limit = 10;
$wallContent = '';

$playerId = null;
$playerFirstName = '';
$playerLastName = '';
$playerFullName = '';
try {
    $sql = "
    SELECT
      `Players`.`PlayerId`,
      `Players`.`Picture`,
      `Players`.`FirstName`,  
      `Players`.`LastName`
    FROM 
      `Players`
    WHERE
      `Players`.`Token` = :Token
    AND 
      `Players`.`IsActive` = 1";

    $stmt = $PDO->prepare($sql);
    $stmt->bindParam('Token', $playerToken, PDO::PARAM_STR);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $playerFirstName = trim($row['FirstName']);
        $playerLastName = trim($row['LastName']);
        $playerFullName = trim($row['FirstName'] . ' ' . $row['LastName']);
        $playerId = $row['PlayerId'];
    }

} catch (PDOException $e) {
    trigger_error($e->getMessage(), E_USER_ERROR);
}//end try

if (!is_null($playerId)) {
    try {
        $sql_wall = "
        SELECT 
          COUNT(`WallId`) AS `recordsTotal`
        FROM 
          `Wall` 
        WHERE
          `Wall`.`PlayerId` = :PlayerId
        AND 
          (`Wall`.`MediaId` IS NOT NULL OR `Wall`.`PostId` IS NOT NULL)
        ORDER BY `Created` DESC";

        $stmt_wall = $PDO->prepare($sql_wall);
        $stmt_wall->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
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


        try {
            $sql_wall = "
            SELECT 
              `Wall`.*
            FROM 
              `Wall` 
            WHERE
              `Wall`.`PlayerId` = :PlayerId
            AND 
              (`Wall`.`MediaId` IS NOT NULL OR `Wall`.`PostId` IS NOT NULL)
            ORDER BY `Created` DESC
            LIMIT :Limit OFFSET :Offset";

            $stmt_wall = $PDO->prepare($sql_wall);
            $stmt_wall->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
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
                $wallMessageId = $row_wall['MessageId'];
                $wallPostId = $row_wall['PostId'];
                $wallEventId = $row_wall['EventId'];
                $wallFollowId = $row_wall['FollowId'];
                $wallLikeId = $row_wall['LikeId'];
                $wallFanId = $row_wall['FanId'];
                $wallAlertId = $row_wall['AlertId'];
                $wallToken = $row_wall['Token'];

                // MEDIA
                if (!empty($wallMediaId)) {

                    try {
                        $sql_media = "
                        SELECT 
                          `Media`.*
                        FROM 
                          `Media` 
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
                            $mediaUrl = $row_media['Url'];
                            $mediaUserFullName = '';
                            $mediaUserPicture = '';

                            $wallMedia = '';
                            $targetFolder = constant('URL_UPLOADS_MEDIA');

                            if ($mediaFileType === 'image') {
                                $wallMedia = '<img src="' . $targetFolder . $mediaFileName . '" alt="image" class="img-responsive"/>';
                            }
                            if ($mediaFileType === 'video') {
                                $wallMedia = '<video controls style="width:100%;"><source src="' . $mediaUrl . '" type="video/mp4"></video>';
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


                            /*
                            $wallContent .= '<div class="timeline_section">
                                <div class="edit_icon">
                                    <a href="javascript:void(0);"></a>
                                </div>
                                <p>Kaden Blankenship <span>liked</span></p>
                                <p>John White’s <span>photo.</span></p>
                                <div class="timline_img_contain">
                                    <div class="timline_img">
                                        <iframe src="https://www.youtube.com/embed/BGMDkrJq3tE" allowfullscreen></iframe>
                                    </div>
                                    <div class="timline_img_name">
                                        <p><span>12:25am</span>17-Feb-17</p>
                                    </div>
                                </div>
                            </div>';
                            */

                            if (!empty($mediaTitle)) {
                                $mediaTitle = '<div class="wall_title"><h5>' . $mediaTitle . '</h5></div>';
                            }

                            if (!empty($mediaDescription)) {
                                $mediaDescription = '<div class="desc_content"><p>' . $mediaDescription . '</p></div>';
                            }

                            $wallContent .= '<div class="single_wall">
                                <div class="wall_title">
                                    <h5>' . $mediaTitle . '</h5>
                                </div>
                                <div class="wall_img">
                                    ' . $wallMedia . '
                                </div>
                                <div class="wall_desc_main">
                                    <div class="like_share_main">
                                        <div class="user_left">
                                            <img src="' . $mediaUserPicture . '" alt="image" class="img-circle img-responsive">
                                            <div class="msg_content">
                                                <h6>' . $mediaUserFullName . '</h6>
                                                <p><span>' . $mediaCreatedTime . ' </span> ' . $mediaCreatedDate . '</p>
                                            </div>
                                        </div>
                                        <div class="like_share">
                                            <a href="#">like</a>
                                            <a href="#">share</a>
                                        </div>
                                    </div>
                                    ' . $mediaTitle . '
                                    ' . $mediaDescription . '
                                </div>
                            </div>';

                        }
                    } catch (PDOException $e) {
                        trigger_error($e->getMessage(), E_USER_ERROR);
                    }//end try

                } // end if MEDIA

                // POST
                if (!empty($wallPostId)) {

                    try {
                        $sql_post = "
                        SELECT 
                          `Posts`.*
                        FROM 
                          `Posts` 
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


                            $wallContent .= '<div class="single_wall">
                                <div class="wall_img">
                                    ' . $postContent . '
                                </div>
                                <div class="wall_desc_main">
                                    <div class="like_share_main">
                                        <div class="user_left">
                                            <img src="' . $postUserPicture . '" alt="image" class="img-circle img-responsive">
                                            <div class="msg_content">
                                                <h6>' . $postUserFullName . '</h6>
                                                <p><span>' . $postCreatedTime . ' </span> ' . $postCreatedDate . '</p>
                                            </div>
                                        </div>
                                        <div class="like_share">
                                            <a href="#">like</a>
                                            <a href="#">share</a>
                                        </div>
                                    </div>
                                </div>
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

} else {
    echo json_encode(array('error' => 'Request is invalid'));
}



