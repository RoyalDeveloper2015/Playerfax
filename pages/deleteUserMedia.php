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
    $mediaId = null;
    $fileKey = '';
    $fileType = '';
    $fileName = '';
    $mediaUserIdFrom = null;
    try {
        $sql = "
        SELECT 
          `Media`.`MediaId`,
          `Media`.`FileKey`,
          `Media`.`FileType`,
          `Media`.`FileName`,
          `Media`.`UserIdFrom`
        FROM 
          `Media` 
        WHERE
          `Media`.`UserId` = :UserId
        AND 
          `Media`.`Token` = :Token";

        $stmt = $PDO->prepare($sql);
        $stmt->bindParam('Token', $token, PDO::PARAM_INT);
        $stmt->bindParam('UserId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $count++;
            $mediaId = $row['MediaId'];
            $fileKey = $row['FileKey'];
            $fileType = $row['FileType'];
            $fileName = $row['FileName'];
            $mediaUserIdFrom = $row['UserIdFrom'];
        }

    } catch (PDOException $e) {
        trigger_error($e->getMessage(), E_USER_ERROR);
    }//end try

    if ($count > 0) {

        if ($mediaUserIdFrom == $userId) {

            // delete video from jwplatform API
            if ($fileType === 'video') {

                require_once 'includes/botr/api.php';

                $botr_api = new BotrAPI('vUrIVB6u', 'xu1vvGQNveo5RaL7bUtVm8T3');

                if (!empty($fileKey)) {

                    $params = [
                        'video_key' => $fileKey
                    ];

                    $delete = $botr_api->call("/videos/delete", $params); // outputs associative array
                }
            }

            // delete image from our server
            if ($fileType === 'image') {
                $targetFolder = constant('ABS_PATH') . constant('UPLOADS_MEDIA');
                @unlink($targetFolder . $fileName);
            }

            try {
                $sql = "
                DELETE FROM `Media`
                WHERE
                  `Media`.`UserId` = :UserId
                AND
                  `Media`.`MediaId` = :MediaId";

                $stmt = $PDO->prepare($sql);
                $stmt->bindParam('UserId', $userId, PDO::PARAM_INT);
                $stmt->bindParam('MediaId', $mediaId, PDO::PARAM_INT);
                $stmt->execute();

            } catch (PDOException $e) {
                trigger_error($e->getMessage(), E_USER_ERROR);
            }//end try

            try {
                $sql = "
                DELETE FROM `Wall`
                WHERE
                  `Wall`.`UserId` = :UserId
                AND
                  `Wall`.`MediaId` = :MediaId";

                $stmt = $PDO->prepare($sql);
                $stmt->bindParam('UserId', $userId, PDO::PARAM_INT);
                $stmt->bindParam('MediaId', $mediaId, PDO::PARAM_INT);
                $stmt->execute();

            } catch (PDOException $e) {
                trigger_error($e->getMessage(), E_USER_ERROR);
            }//end try

            try {
                $sql = "
                DELETE FROM `MediaComments`
                WHERE
                  `MediaComments`.`UserId` = :UserId
                AND
                  `MediaComments`.`MediaId` = :MediaId";

                $stmt = $PDO->prepare($sql);
                $stmt->bindParam('UserId', $userId, PDO::PARAM_INT);
                $stmt->bindParam('MediaId', $mediaId, PDO::PARAM_INT);
                $stmt->execute();

            } catch (PDOException $e) {
                trigger_error($e->getMessage(), E_USER_ERROR);
            }//end try

            try {
                $sql = "
                DELETE FROM `Likes`
                WHERE
                  `Likes`.`UserId` = :UserId
                AND
                  `Likes`.`MediaId` = :MediaId";

                $stmt = $PDO->prepare($sql);
                $stmt->bindParam('UserId', $userId, PDO::PARAM_INT);
                $stmt->bindParam('MediaId', $mediaId, PDO::PARAM_INT);
                $stmt->execute();

            } catch (PDOException $e) {
                trigger_error($e->getMessage(), E_USER_ERROR);
            }//end try

            try {
                $sql = "
                DELETE FROM `Shares`
                WHERE
                  `Shares`.`UserId` = :UserId
                AND
                  `Shares`.`MediaId` = :MediaId";

                $stmt = $PDO->prepare($sql);
                $stmt->bindParam('UserId', $userId, PDO::PARAM_INT);
                $stmt->bindParam('MediaId', $mediaId, PDO::PARAM_INT);
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


