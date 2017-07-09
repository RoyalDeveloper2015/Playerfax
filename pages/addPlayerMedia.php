<?php

ini_set('max_execution_time', '21600'); // 6 hours
set_time_limit(21600); // 6 hours

if (!isset($_SESSION['userId'])) {
    header('Location: index.php?page=login');
    exit;
}

header('Content-Type: application/json');

$targetFolder = constant('ABS_PATH') . constant('UPLOADS_MEDIA');

$playerToken = isset($_POST['token']) ? trim($_POST['token']) : '';
$playerToken = filter_var($playerToken, FILTER_SANITIZE_STRING);

$progressId = isset($_POST['progressId']) ? trim($_POST['progressId']) : '';
$progressId = filter_var($progressId, FILTER_SANITIZE_STRING);

$playerId = null;
$adminId = null;
$token = sha1($userIp . microseconds());

if (!empty($_FILES) && !empty($playerToken)) {

    $count = 0;

    try {
        $sql = "
        SELECT
          `Players`.`PlayerId`,
          `Players`.`UserId`
        FROM 
          `Players`
        USE INDEX (`TokenIsActive`)
        WHERE
          `Players`.`Token` = :Token
        AND 
          `Players`.`IsActive` = 1";

        $stmt = $PDO->prepare($sql);
        $stmt->bindParam('Token', $playerToken, PDO::PARAM_STR);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $playerId = $row['PlayerId'];
            $adminId = $row['UserId'];
            $count++;
        }

    } catch (PDOException $e) {
        trigger_error($e->getMessage(), E_USER_ERROR);
    }//end try

    if ($count > 0 && isset($_FILES['Filedata'])) {

        $tempFile = $_FILES['Filedata']['tmp_name'];
        $tempFileSize = filesize($tempFile);
        $tempFileMD5Hash = md5($tempFile);

        // Validate the file type
        $imageFileTypes = array('jpg', 'jpeg', 'gif', 'png'); // File extensions
        $videoFileTypes = array('mpg', 'mpeg', 'mp4', 'm4v', 'webm', 'wmv', '3gp', '3g2', 'flv', 'ogv', 'mov');
        $fileParts = pathinfo($_FILES['Filedata']['name']);
        if (!isset($fileParts['extension'])) {
            echo json_encode(array('error' => 'Invalid file type'));
            exit;
        }
        $fileExtension = strtolower($fileParts['extension']);
        $hash = sha1($userIp . microseconds());
        $mediaFileName = $hash . '.' . $fileExtension;
        $targetMediaFile = rtrim($targetFolder, '/') . '/' . $mediaFileName;
        $token = sha1($userIp . microseconds());

        if (in_array($fileExtension, $imageFileTypes)) {

            $imageIsSaved = false;

            // image options
            $jpegQuality = 99; // JPEG (quality range 0-99) 99 = minimum compression (maximum quality)
            $pngQuality = 9; // PNG (quality range 0-9) 9 = maximum compression (quality doesn't change)
            $maxWidth = 650;

            $contents = @fread(@fopen($tempFile, "rb"), filesize($tempFile));

            // Create a new image
            $_srcImage = @imagecreatefromstring($contents);

            // The image will now be scaled vertically when resizing the width.

            // Get the current height and width
            $_srcWidth = @imagesx($_srcImage);
            $_srcHeight = @imagesy($_srcImage);

            // get the ratio
            @$_srcRatio = $_srcHeight / $_srcWidth;

            // we now have the new dimensions
            $_dstHeight = @round($maxWidth * $_srcRatio);
            $_dstWidth = $maxWidth;

            // Create a new true color image based upon the destination height and width
            $im = @imagecreatetruecolor($_dstWidth, $_dstHeight);

            $exif = @exif_read_data($tempFile);

            if (($fileExtension == "jpeg") || ($fileExtension == "jpg")) {
                // Resize the uploaded image
                @imagecopyresized($im, $_srcImage, 0, 0, 0, 0, $_dstWidth, $_dstHeight, $_srcWidth, $_srcHeight);

                if (isset($exif['Orientation'])) {

                    // Fix Orientation
                    switch ($exif['Orientation']) {
                        case 3:
                            // 180 rotate left
                            $im = imagerotate($im, 180, 0);
                            break;

                        case 6:
                            // 90 rotate right
                            $im = imagerotate($im, -90, 0);
                            break;

                        case 8:
                            // 90 rotate left
                            $im = imagerotate($im, 90, 0);
                            break;
                    }

                    if ($_srcWidth > $_srcHeight) {
                        // Landscape orientation
                    } else {
                        // Portrait or Square orientation
                    }
                }

                $imageIsSaved = @imagejpeg($im, $targetMediaFile, $jpegQuality);
                // Free the memory for this resource
                @imagedestroy($im);
                if (file_exists($tempFile)) {
                    unlink($tempFile);
                }
            }

            if ($fileExtension == "gif") {
                // retain the gif transparency
                $colorTransparent = @imagecolortransparent($_srcImage);
                @imagepalettecopy($_srcImage, $im);
                @imagefill($im, 0, 0, $colorTransparent);
                @imagecolortransparent($im, $colorTransparent);
                @imagetruecolortopalette($im, true, 256);

                // Resize the uploaded image
                @imagecopyresized($im, $_srcImage, 0, 0, 0, 0, $_dstWidth, $_dstHeight, $_srcWidth, $_srcHeight);

                if (isset($exif['Orientation'])) {

                    // Fix Orientation
                    switch ($exif['Orientation']) {
                        case 3:
                            // 180 rotate left
                            $im = imagerotate($im, 180, 0);
                            break;

                        case 6:
                            // 90 rotate right
                            $im = imagerotate($im, -90, 0);
                            break;

                        case 8:
                            // 90 rotate left
                            $im = imagerotate($im, 90, 0);
                            break;
                    }

                    if ($_srcWidth > $_srcHeight) {
                        // Landscape orientation
                    } else {
                        // Portrait or Square orientation
                    }
                }

                $imageIsSaved = @imagegif($im, $targetMediaFile);
                // Free the memory for this resource
                @imagedestroy($im);
                if (file_exists($tempFile)) {
                    unlink($tempFile);
                }
            }

            if ($fileExtension == "png") {
                @imagealphablending($im, false);
                // Resize the uploaded image
                @imagecopyresampled($im, $_srcImage, 0, 0, 0, 0, $_dstWidth, $_dstHeight, $_srcWidth, $_srcHeight);
                // preserve the alpha channel
                @imagesavealpha($im, true);

                if (isset($exif['Orientation'])) {

                    // Fix Orientation
                    switch ($exif['Orientation']) {
                        case 3:
                            // 180 rotate left
                            $im = imagerotate($im, 180, 0);
                            break;

                        case 6:
                            // 90 rotate right
                            $im = imagerotate($im, -90, 0);
                            break;

                        case 8:
                            // 90 rotate left
                            $im = imagerotate($im, 90, 0);
                            break;
                    }

                    if ($_srcWidth > $_srcHeight) {
                        // Landscape orientation
                    } else {
                        // Portrait or Square orientation
                    }
                }

                $imageIsSaved = @imagepng($im, $targetMediaFile, $pngQuality);
                // Free the memory for this resource
                @imagedestroy($im);
                if (file_exists($tempFile)) {
                    unlink($tempFile);
                }
            }

            if ($imageIsSaved) {

                $created = date('Y-m-d H:i:s');
                $lastUpdated = date('Y-m-d H:i:s');
                $targetMediaFileSize = filesize($targetMediaFile);
                $targetMediaFileMD5Hash = md5($targetMediaFile);
                $fileType = 'image';
                $fileKey = '';
                $fileStatus = '4'; // 0=Queue, 1=first stage, 2=second stage, 3=Error, 4=Done
                $mediaToken = sha1($userIp . microseconds());

                try {
                    $sql = "
                    INSERT INTO `Media`
                    (
                      `MediaId`,
                      `Created`,
                      `LastUpdated`,
                      `Status`,
                      `UserId`,
                      `UserIdFrom`,
                      `PlayerId`,
                      `FileName`,
                      `FileType`,
                      `FileExtension`,
                      `FileSize`,
                      `FileMD5Hash`,
                      `FileKey`,
                      `Token`
                    ) VALUES (
                      NULL,
                      :Created,
                      :LastUpdated,
                      :Status,
                      :UserId,
                      :UserIdFrom,
                      :PlayerId,
                      :FileName,
                      :FileType,
                      :FileExtension,
                      :FileSize,
                      :FileMD5Hash,
                      :FileKey,
                      :Token
                    )";

                    $stmt = $PDO->prepare($sql);
                    $stmt->bindParam('Created', $created, PDO::PARAM_STR); // Creation date
                    $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                    $stmt->bindParam('Status', $fileStatus, PDO::PARAM_INT);
                    $stmt->bindParam('UserId', $adminId, PDO::PARAM_INT); // Owner of player
                    $stmt->bindParam('UserIdFrom', $userId, PDO::PARAM_INT); // My logged in userId (one who is posting video)
                    $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT); // The player card
                    $stmt->bindParam('FileName', $mediaFileName, PDO::PARAM_STR);
                    $stmt->bindParam('FileType', $fileType, PDO::PARAM_STR);
                    $stmt->bindParam('FileExtension', $fileExtension, PDO::PARAM_STR);
                    $stmt->bindParam('FileSize', $targetMediaFileSize, PDO::PARAM_STR);
                    $stmt->bindParam('FileMD5Hash', $targetMediaFileMD5Hash, PDO::PARAM_STR);
                    $stmt->bindParam('FileKey', $fileKey, PDO::PARAM_STR);
                    $stmt->bindParam('Token', $mediaToken, PDO::PARAM_STR);
                    $stmt->execute();

                } catch (PDOException $e) {
                    trigger_error($e->getMessage(), E_USER_ERROR);
                }//end try

                // get media information from token
                $mediaId = null;
                try {
                    $sql = "
                    SELECT 
                      `Media`.`MediaId`
                    FROM 
                      `Media` 
                    USE INDEX (`UserIdToken`)
                    WHERE
                      `Media`.`UserId` = :UserId
                    AND
                      `Media`.`Token` = :Token";

                    $stmt = $PDO->prepare($sql);
                    $stmt->bindParam('UserId', $adminId, PDO::PARAM_INT);
                    $stmt->bindParam('Token', $mediaToken, PDO::PARAM_STR);
                    $stmt->execute();

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $mediaId = $row['MediaId'];
                    }

                } catch (PDOException $e) {
                    trigger_error($e->getMessage(), E_USER_ERROR);
                }//end try

                // insert wall MediaId
                $wallToken = sha1($userIp . microseconds());
                try {
                    $sql_wall = "
                    INSERT INTO `Wall` 
                    (
                      `WallId`, 
                      `Created`, 
                      `UserId`, 
                      `UserIdFrom`, 
                      `PlayerId`, 
                      `MediaId`, 
                      `Token`
                    ) VALUES (
                      NULL, 
                      :Created, 
                      :UserId, 
                      :UserIdFrom, 
                      :PlayerId, 
                      :MediaId, 
                      :Token
                    )";

                    $stmt_wall = $PDO->prepare($sql_wall);
                    $stmt_wall->bindParam('Created', $created, PDO::PARAM_STR); // timestamp of when user was uploading
                    $stmt_wall->bindParam('UserId', $adminId, PDO::PARAM_INT); // Owner of player
                    $stmt_wall->bindParam('UserIdFrom', $userId, PDO::PARAM_INT); // My logged in userId (one who is posting video)
                    $stmt_wall->bindParam('PlayerId', $playerId, PDO::PARAM_INT); // The player card
                    $stmt_wall->bindParam('MediaId', $mediaId, PDO::PARAM_INT);
                    $stmt_wall->bindParam('Token', $wallToken, PDO::PARAM_STR);
                    $stmt_wall->execute();

                } catch (PDOException $e) {
                    trigger_error($e->getMessage(), E_USER_ERROR);
                }//end try

                echo json_encode(array('success' => 'Added successfully', 'url' => constant('URL_UPLOADS_MEDIA') . $mediaFileName . '?token=' . $token, 'mediaId' => $mediaId, 'progressId' => $progressId, 'type' => 'image'));
            } else {
                echo json_encode(array('error' => 'Upload Failed'));
            }

        } elseif (in_array($fileExtension, $videoFileTypes)) {

            if (is_dir($targetFolder) && is_writable($targetFolder)) {

                if (move_uploaded_file($tempFile, $targetMediaFile)) {

                    $created = date('Y-m-d H:i:s');
                    $lastUpdated = date('Y-m-d H:i:s');
                    $fileType = 'video';
                    $fileKey = '';
                    $fileStatus = '0'; // 0=Queue, 1=first stage, 2=second stage, 3=Error, 4=Done
                    $mediaToken = sha1($userIp . microseconds());

                    try {
                        $sql = "
                        INSERT INTO `Media`
                        (
                          `MediaId`,
                          `Created`,
                          `LastUpdated`,
                          `Status`,
                          `UserId`,
                          `UserIdFrom`,
                          `PlayerId`,
                          `FileName`,
                          `FileType`,
                          `FileExtension`,
                          `FileSize`,
                          `FileMD5Hash`,
                          `FileKey`,
                          `Token`
                        ) VALUES (
                          NULL,
                          :Created,
                          :LastUpdated,
                          :Status,
                          :UserId,
                          :UserIdFrom,
                          :PlayerId,
                          :FileName,
                          :FileType,
                          :FileExtension,
                          :FileSize,
                          :FileMD5Hash,
                          :FileKey,
                          :Token
                        )";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam('Created', $created, PDO::PARAM_STR); // Creation date
                        $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                        $stmt->bindParam('Status', $fileStatus, PDO::PARAM_INT);
                        $stmt->bindParam('UserId', $adminId, PDO::PARAM_INT); // Owner of player
                        $stmt->bindParam('UserIdFrom', $userId, PDO::PARAM_INT); // My logged in userId (one who is posting video)
                        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT); // The player card
                        $stmt->bindParam('FileName', $mediaFileName, PDO::PARAM_STR);
                        $stmt->bindParam('FileType', $fileType, PDO::PARAM_STR);
                        $stmt->bindParam('FileExtension', $fileExtension, PDO::PARAM_STR);
                        $stmt->bindParam('FileSize', $tempFileSize, PDO::PARAM_STR);
                        $stmt->bindParam('FileMD5Hash', $tempFileMD5Hash, PDO::PARAM_STR);
                        $stmt->bindParam('FileKey', $fileKey, PDO::PARAM_STR);
                        $stmt->bindParam('Token', $mediaToken, PDO::PARAM_STR);
                        $stmt->execute();

                    } catch (PDOException $e) {
                        trigger_error($e->getMessage(), E_USER_ERROR);
                    }//end try

                    // get media information from token
                    $mediaId = null;
                    try {
                        $sql = "
                        SELECT 
                          `Media`.`MediaId`
                        FROM 
                          `Media` 
                        USE INDEX (`UserIdToken`)
                        WHERE
                          `Media`.`UserId` = :UserId
                        AND
                          `Media`.`Token` = :Token";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam('UserId', $adminId, PDO::PARAM_INT);
                        $stmt->bindParam('Token', $mediaToken, PDO::PARAM_STR);
                        $stmt->execute();

                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $mediaId = $row['MediaId'];
                        }

                    } catch (PDOException $e) {
                        trigger_error($e->getMessage(), E_USER_ERROR);
                    }//end try

                    echo json_encode(array('success' => 'Added successfully', 'url' => constant('URL_UPLOADS_MEDIA') . 'processing-video.png' . '?token=' . $token, 'mediaId' => $mediaId, 'progressId' => $progressId, 'type' => 'video'));

                } else {
                    echo json_encode(array('error' => 'Upload Failed'));
                }

            } else {
                echo json_encode(array('error' => 'Directory not writable'));
            }

        } else {
            echo json_encode(array('error' => 'Invalid file type'));
        }
    }
}


