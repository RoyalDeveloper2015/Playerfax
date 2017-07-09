<?php

// This is the cron job command
// php /home/player/public_html/includes/cronMediaStageOne.php >/dev/null 2>&1

ini_set('max_execution_time', '21600'); // 6 hours
set_time_limit(21600); // 6 hours
error_reporting(E_ALL);

require_once '../config.php';

// DB Connection
try {
    // MySQL with PDO_MYSQL
    $attributes = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
    );
    $PDO = new PDO("mysql:host=$db_host_main;dbname=$db_name_main", $db_user_main, $db_pass_main, $attributes);
} catch (PDOException $e) {
    trigger_error('PDO connection failed: ', E_USER_ERROR);
}

// bootstrap the php-ffmpeg library
require_once 'vendor/autoload.php';

$targetFolder = constant('ABS_PATH') . constant('UPLOADS_MEDIA');
$mediaId = null;
$mediaUserId = null;
$mediaCreated = '';
$mediaLastUpdated = '';
$mediaTitle = '';
$mediaDescription = '';
$mediaFileName = '';
$userFullName = '';

// get media information
$count = 0;
try {
    $sql_media = "
    SELECT 
      `Media`.*
    FROM 
      `Media` 
    WHERE
      `Media`.`Status` = 0
    LIMIT 1";

    $stmt_media = $PDO->prepare($sql_media);
    $stmt_media->execute();

    while ($row_media = $stmt_media->fetch(PDO::FETCH_ASSOC)) {
        $count++;
        $mediaId = $row_media['MediaId'];
        $mediaUserId = $row_media['UserId'];
        $mediaCreated = $row_media['Created'];
        $mediaLastUpdated = $row_media['LastUpdated'];
        $mediaTitle = $row_media['Title'];
        $mediaDescription = $row_media['Description'];
        $mediaFileName = $row_media['FileName'];

        if (empty($mediaTitle)) {
            $mediaTitle = 'Default Title';
        }

        if (empty($mediaDescription)) {
            $mediaDescription = 'Default Description';
        }

        if (file_exists($targetFolder . $mediaFileName)) {

            // get author
            try {
                $sql_author = "
                SELECT 
                  `Users`.`FirstName`, `Users`.`LastName`
                FROM 
                  `Users` 
                WHERE
                  `Users`.`UserId` = :UserId";

                $stmt_author = $PDO->prepare($sql_author);
                $stmt_author->bindParam('UserId', $mediaUserId, PDO::PARAM_INT);
                $stmt_author->execute();

                while ($row_author = $stmt_author->fetch(PDO::FETCH_ASSOC)) {
                    $userFullName = trim($row_author['FirstName'] . ' ' . $row_author['LastName']);
                }

            } catch (PDOException $e) {
                trigger_error($e->getMessage(), E_USER_ERROR);
            }//end try

            try {

                $ffmpeg = FFMpeg\FFMpeg::create(array(
                    'ffmpeg.binaries' => '/home/player/public_html/includes/ffmpeg-git-20170417-64bit-static/ffmpeg',
                    'ffprobe.binaries' => '/home/player/public_html/includes/ffmpeg-git-20170417-64bit-static/ffprobe',
                    'timeout' => 3600, // The timeout for the underlying process
                    'ffmpeg.threads' => 12,   // The number of threads that FFMpeg should use
                ));

                $video = $ffmpeg->open($targetFolder . $mediaFileName);

                $ffprobe = FFMpeg\FFProbe::create(array(
                    'ffmpeg.binaries' => '/home/player/public_html/includes/ffmpeg-git-20170417-64bit-static/ffmpeg',
                    'ffprobe.binaries' => '/home/player/public_html/includes/ffmpeg-git-20170417-64bit-static/ffprobe',
                    'timeout' => 3600, // The timeout for the underlying process
                    'ffmpeg.threads' => 12,   // The number of threads that FFMpeg should use
                ));

                $dimension = $ffprobe
                    ->streams($targetFolder . $mediaFileName)// extracts streams information
                    ->videos()// filters video streams
                    ->first()// returns the first video stream
                    ->getDimensions(); // returns a FFMpeg\Coordinate\Dimension object

                $fullWidth = $dimension->getWidth(); //$dimension->getHeight();
                $fullDuration = floor($ffprobe->format($targetFolder . $mediaFileName)->get('duration')); // seconds

                $halfDuration = ceil(($fullDuration / 2));
                if ($halfDuration > 2) {
                    // get middle frame
                    $video->frame(FFMpeg\Coordinate\TimeCode::fromSeconds($halfDuration))->save($targetFolder . $mediaFileName . '.jpg');
                } else {
                    // get first frame
                    $video->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(1))->save($targetFolder . $mediaFileName . '.jpg');
                }

                // upload to jw-platform

                require_once 'botr/api.php';

                $botr_api = new BotrAPI('vUrIVB6u', 'xu1vvGQNveo5RaL7bUtVm8T3');

                // list all videos
                $list = $botr_api->call("/videos/list"); // outputs associative array
                $status = isset($list['status']) ? $list['status'] : '';
                $videos = isset($list['videos']) ? $list['videos'] : array();

                if ($status === 'ok') {

                    $params = [
                        'title' => $mediaTitle,
                        'description' => $mediaDescription,
                        'trim_in_point' => '01:00:00.000',
                        'author' => $userFullName,
                        'sourcetype' => 'file',
                        'upload_method' => 'single',
                        'custom.id' => $mediaId,
                        'custom.filename' => $mediaFileName
                    ];

                    // create upload link using parameters
                    $create = $botr_api->call('/videos/create', $params); // outputs associative array
                    $status = isset($create['status']) ? $create['status'] : '';
                    $linkPath = isset($create['link']['path']) ? $create['link']['path'] : '';
                    $linkKey = isset($create['link']['query']['key']) ? $create['link']['query']['key'] : '';
                    $linkToken = isset($create['link']['query']['token']) ? $create['link']['query']['token'] : '';
                    $linkProtocol = isset($create['link']['protocol']) ? $create['link']['protocol'] : '';
                    $linkAddress = isset($create['link']['address']) ? $create['link']['address'] : '';

                    if ($status === 'ok') {

                        // set this media item to processing first stage
                        try {
                            $sql_first = "
                            UPDATE 
                              `Media`
                            SET 
                              `Media`.`Status` = 1 ,
                              `Media`.`FileKey` = :FileKey
                            WHERE
                              `Media`.`MediaId` = :MediaId";

                            $stmt_first = $PDO->prepare($sql_first);
                            $stmt_first->bindParam('FileKey', $linkKey, PDO::PARAM_STR);
                            $stmt_first->bindParam('MediaId', $mediaId, PDO::PARAM_INT);
                            $stmt_first->execute();

                        } catch (PDOException $e) {
                            trigger_error($e->getMessage(), E_USER_ERROR);
                        }//end try

                        // upload video to jw-platform
                        $url = $linkProtocol . '://' . $linkAddress . $linkPath . '?api_format=json&key=' . $linkKey . '&token=' . $linkToken;
                        $url = filter_var($url, FILTER_SANITIZE_URL);
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_HEADER, false);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                        curl_setopt($ch, CURLOPT_TIMEOUT, 21600); // 6 hour timeout
                        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
                        curl_setopt($ch, CURLOPT_POST, true);
                        $args['file'] = new CurlFile($targetFolder . $mediaFileName, '', $mediaFileName);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $args);

                        $data = curl_exec($ch);
                        $content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
                        curl_close($ch);

                        $json = json_decode($data, true);

                        $status = isset($json['status']) ? $json['status'] : '';
                        $type = isset($json['media']['type']) ? $json['media']['type'] : '';
                        $key = isset($json['media']['key']) ? $json['media']['key'] : '';
                        $md5 = isset($json['file']['md5']) ? $json['file']['md5'] : '';
                        $size = isset($json['file']['size']) ? $json['file']['size'] : '';
                        $redirect_link = isset($json['redirect_link']) ? $json['redirect_link'] : '';

                        $lastUpdated = date('Y-m-d H:i:s');

                        if ($status === 'ok') {

                            // set this media item to second stage processing
                            // 0=Queue, 1=first stage, 2=second stage, 3=Error, 4=Done
                            try {
                                $sql_second = "
                                UPDATE 
                                  `Media`
                                SET 
                                  `Media`.`Status` = 2 
                                WHERE
                                  `Media`.`MediaId` = :MediaId";

                                $stmt_second = $PDO->prepare($sql_second);
                                $stmt_second->bindParam('MediaId', $mediaId, PDO::PARAM_INT);
                                $stmt_second->execute();

                            } catch (PDOException $e) {
                                trigger_error($e->getMessage(), E_USER_ERROR);
                            }//end try

                            // delete video when complete
                            if (file_exists($targetFolder . $mediaFileName)) {
                                unlink($targetFolder . $mediaFileName);
                            }

                            echo json_encode(array('success' => 'Media uploaded successfully'));

                        } else {

                            $status = '3'; // 0=Queue, 1=first stage, 2=second stage, 3=Error, 4=Done

                            // update media status to error
                            try {
                                $sql_error = "
                                UPDATE `Media` 
                                SET
                                  `LastUpdated` = :LastUpdated, 
                                  `Status` = :Status
                                WHERE
                                  `MediaId` = :MediaId";

                                $stmt_error = $PDO->prepare($sql_error);
                                $stmt_error->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                                $stmt_error->bindParam('Status', $status, PDO::PARAM_INT);
                                $stmt_error->bindParam('MediaId', $mediaId, PDO::PARAM_INT);
                                $stmt_error->execute();

                            } catch (PDOException $e) {
                                trigger_error($e->getMessage(), E_USER_ERROR);
                            }//end try

                            // delete video if there is an error
                            if (file_exists($targetFolder . $mediaFileName)) {
                                unlink($targetFolder . $mediaFileName);
                            }

                            if (file_exists($targetFolder . $mediaFileName . '.jpg')) {
                                unlink($targetFolder . $mediaFileName . '.jpg');
                            }

                            echo json_encode(array('error' => 'Unable to parse video file'));
                        }

                    } else {
                        // unable to do anything
                        echo json_encode(array('error' => 'API error. Try again later'));
                        print_r($create);
                    }

                } else {
                    echo json_encode(array('error' => 'Unable to parse video file'));
                    print_r($list);
                }

            } catch (Exception $e) {

                $status = '3'; // 0=Queue, 1=first stage, 2=second stage, 3=Error, 4=Done

                // update media status to error
                try {
                    $sql_error = "
                    UPDATE `Media` 
                    SET
                      `LastUpdated` = :LastUpdated, 
                      `Status` = :Status
                    WHERE
                      `MediaId` = :MediaId";

                    $stmt_error = $PDO->prepare($sql_error);
                    $stmt_error->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                    $stmt_error->bindParam('Status', $status, PDO::PARAM_INT);
                    $stmt_error->bindParam('MediaId', $mediaId, PDO::PARAM_INT);
                    $stmt_error->execute();

                } catch (PDOException $e) {
                    trigger_error($e->getMessage(), E_USER_ERROR);
                }//end try

                // delete file if there is an error
                if (file_exists($targetFolder . $mediaFileName)) {
                    unlink($targetFolder . $mediaFileName);
                }

                if (file_exists($targetFolder . $mediaFileName . '.jpg')) {
                    unlink($targetFolder . $mediaFileName . '.jpg');
                }

                trigger_error($e->getMessage(), E_USER_ERROR);
            }//end try

        } // end if file exists

    } // end while

} catch (PDOException $e) {
    trigger_error($e->getMessage(), E_USER_ERROR);
}//end try

if ($count == 0) {
// no videos to process
    echo json_encode(array('error' => 'no videos to process'));

}



