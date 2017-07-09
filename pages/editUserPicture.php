<?php

if (!isset($_SESSION['userId'])) {
    header('Location: index.php?page=login');
    exit;
}

header('Content-Type: application/json');

$targetFolder = constant('ABS_PATH') . constant('UPLOADS_USERS');

$userToken = isset($_POST['token']) ? trim($_POST['token']) : ''; // not used
$userToken = filter_var($userToken, FILTER_SANITIZE_STRING);

$currentUserPicture = ''; // current file name with extension
$newUserPicture = '';  // new file name with extension

if (!empty($userToken)) {

    $count = 0;

    try {
        $sql = "
        SELECT
          `Users`.`Picture`
        FROM 
          `Users`
        WHERE
          `Users`.`UserId` = :UserId
        AND 
          `Users`.`IsActive` = 1";

        $stmt = $PDO->prepare($sql);
        $stmt->bindParam('UserId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $currentUserPicture = $row['Picture'];
            $count++;
        }

    } catch (PDOException $e) {
        trigger_error($e->getMessage(), E_USER_ERROR);
    }//end try

    if ($count > 0) {

        // Save cropped canvas data to server
        if (isset($_POST['Filedata'])) {

            $imageIsSaved = false;

            // PNG
            $newUserPicture = sha1($userIp . microseconds()) . '.png'; // new file name with extension
            $targetFile = rtrim($targetFolder, '/') . '/' . $newUserPicture; // absolute path to new file
            $currentTargetFile = rtrim($targetFolder, '/') . '/' . $currentUserPicture; // absolute path to current file

            // image options
            $pngQuality = 9; // PNG (quality range 0-9) 9 = maximum compression (quality doesn't change)
            $maxWidth = 650;

            $encodedData = str_replace(' ', '+', $_POST['Filedata']);
            $encodedData = str_replace('data:image/png;base64,', '', $encodedData);
            $decodedData = base64_decode($encodedData);

            $_srcImage = @imagecreatefromstring($decodedData);

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

            @imagealphablending($im, false);
            // Resize the uploaded image
            @imagecopyresampled($im, $_srcImage, 0, 0, 0, 0, $_dstWidth, $_dstHeight, $_srcWidth, $_srcHeight);
            // preserve the alpha channel
            @imagesavealpha($im, true);

            $imageIsSaved = @imagepng($im, $targetFile, $pngQuality, PNG_ALL_FILTERS);

            // Free the memory for this resource
            @imagedestroy($im);

            if ($imageIsSaved) {

                if (!empty($currentUserPicture) && file_exists($currentTargetFile)) {
                    unlink($currentTargetFile);
                }

                try {
                    $sql = "
                    UPDATE 
                      `Users` 
                    SET
                      `Users`.`Picture` = :Picture
                    WHERE 
                      `Users`.`UserId` = :UserId";

                    $stmt = $PDO->prepare($sql);
                    $stmt->bindParam('Picture', $newUserPicture, PDO::PARAM_STR);
                    $stmt->bindParam('UserId', $userId, PDO::PARAM_INT);
                    $stmt->execute();

                } catch (PDOException $e) {
                    trigger_error($e->getMessage(), E_USER_ERROR);
                }//end try

                $token = sha1($userIp . microseconds());

                $msgBox = alertBox("Picture edited successfully", "<i class='fa fa-check-square-o'></i>", "success");
                echo json_encode(array('success' => $msgBox, 'url' => constant('URL_UPLOADS_USERS') . $newUserPicture . '?token=' . $token));
            } else {
                $msgBox = alertBox("Unable to change picture", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
            }

        } // end if

        // save uploaded image data to server
        if (isset($_FILES['Filedata'])) {
            $tempFile = $_FILES['Filedata']['tmp_name'];

            // Validate the file type
            $fileTypes = array('jpg', 'jpeg', 'gif', 'png'); // File extensions
            $fileParts = pathinfo($_FILES['Filedata']['name']);
            if (!isset($fileParts['extension'])) {
                echo json_encode(array('error' => 'Invalid file type'));
                exit;
            }
            $fileExtension = strtolower($fileParts['extension']);
            $newUserPicture = sha1($userIp . microseconds()) . '.' . $fileExtension; // new file name with extension
            $targetFile = rtrim($targetFolder, '/') . '/' . $newUserPicture; // absolute path to new file
            $currentTargetFile = rtrim($targetFolder, '/') . '/' . $currentUserPicture; // absolute path to current file

            if (in_array($fileExtension, $fileTypes)) {

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

                    $imageIsSaved = @imagejpeg($im, $targetFile, $jpegQuality);
                    // Free the memory for this resource
                    @imagedestroy($im);
                    @unlink($tempFile);
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

                    $imageIsSaved = @imagegif($im, $targetFile);
                    // Free the memory for this resource
                    @imagedestroy($im);
                    @unlink($tempFile);
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

                    $imageIsSaved = @imagepng($im, $targetFile, $pngQuality, PNG_ALL_FILTERS);
                    // Free the memory for this resource
                    @imagedestroy($im);
                    @unlink($tempFile);
                }

                if ($imageIsSaved) {

                    if (!empty($currentUserPicture) && file_exists($currentTargetFile)) {
                        unlink($currentTargetFile);
                    }

                    try {
                        $sql = "
                        UPDATE 
                          `Users` 
                        SET
                          `Users`.`Picture` = :Picture
                        WHERE 
                          `Users`.`UserId` = :UserId";

                        $stmt = $PDO->prepare($sql);
                        $stmt->bindParam('Picture', $newUserPicture, PDO::PARAM_STR);
                        $stmt->bindParam('UserId', $userId, PDO::PARAM_INT);
                        $stmt->execute();

                    } catch (PDOException $e) {
                        trigger_error($e->getMessage(), E_USER_ERROR);
                    }//end try

                    $token = sha1($userIp . microseconds());

                    $msgBox = alertBox("Picture edited successfully", "<i class='fa fa-check-square-o'></i>", "success");
                    echo json_encode(array('success' => $msgBox, 'url' => constant('URL_UPLOADS_USERS') . $newUserPicture . '?token=' . $token));

                } else {
                    $msgBox = alertBox("Unable to change picture", "<i class='fa fa-times'></i>", "danger");
                    echo json_encode(array('error' => $msgBox));
                }

            } else {
                $msgBox = alertBox("Invalid file type", "<i class='fa fa-times'></i>", "danger");
                echo json_encode(array('error' => $msgBox));
            }
        } // end if
    }
}
