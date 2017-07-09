<?php

// This is the cron job command
// php /home/player/public_html/includes/cronMediaStageTwo.php >/dev/null 2>&1

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
      `Media`.`Status` = 2
    AND 
      `Media`.`FileType` = 'video'
    AND 
      `Media`.`FileKey` != ''
    LIMIT 1";

    $stmt_media = $PDO->prepare($sql_media);
    $stmt_media->execute();

    while ($row_media = $stmt_media->fetch(PDO::FETCH_ASSOC)) {
        $count++;
        $mediaId = $row_media['MediaId'];
        $mediaUserId = $row_media['UserId'];
        $mediaPlayerId = $row_media['PlayerId'];
        $mediaUserIdFrom = $row_media['UserIdFrom'];
        $mediaCreated = $row_media['Created'];
        $mediaLastUpdated = $row_media['LastUpdated'];
        $mediaTitle = $row_media['Title'];
        $mediaDescription = $row_media['Description'];
        $mediaFileName = $row_media['FileName'];
        $mediaKey = $row_media['FileKey'];

        require_once 'botr/api.php';

        $botr_api = new BotrAPI('vUrIVB6u', 'xu1vvGQNveo5RaL7bUtVm8T3');

        // get video
        $params = [
            'video_key' => $mediaKey
        ];

        // list converted video
        $conversions = $botr_api->call("/videos/conversions/list", $params); // outputs associative array

        $videos = array();
        if (isset($conversions['conversions']) && is_array($conversions['conversions'])) {
            $conversionCount = count($conversions['conversions']);
            $i = 0;
            foreach ($conversions['conversions'] as $conversion) {
                if (isset($conversion['template']['format']['key'])) {
                    $format = $conversion['template']['format']['key'];
                    $status = isset($conversion['status']) ? $conversion['status'] : '';
                    $key = isset($conversion['key']) ? $conversion['key'] : '';
                    $width = isset($conversion['width']) ? $conversion['width'] : '';
                    $protocol = isset($conversion['link']['protocol']) ? $conversion['link']['protocol'] : '';
                    $address = isset($conversion['link']['address']) ? $conversion['link']['address'] : '';
                    $path = isset($conversion['link']['path']) ? $conversion['link']['path'] : '';
                    if ($format === 'mp4') {

                        $url = $protocol . '://' . $address . $path;
                        $videos['mp4'][$i]['status'] =  $status;
                        $videos['mp4'][$i]['width'] =  $width;
                        $videos['mp4'][$i]['key'] =  $key;
                        $videos['mp4'][$i]['url'] =  $url;

                        if ($i > 0) {
                            $p = ($i-1);
                            // remove previous item from array if width is smaller
                            if (isset($videos['mp4'][$p]['width'])) {
                                $previousWidth = $videos['mp4'][$p]['width'];
                                $currentWidth = $videos['mp4'][$i]['width'];
                                if ($previousWidth < $currentWidth) {
                                    unset($videos['mp4'][$p]);
                                }
                            }
                        }

                        $i++;
                    }
                }
            }
        }

        // priority to largest mp4 video available
        if (isset($videos['mp4'])) {
            // reindex mp4 array
            $videos['mp4'] = array_values($videos['mp4']);
            $status = isset($videos['mp4'][0]['status']) ? $videos['mp4'][0]['status'] : '';
            $key = isset($videos['mp4'][0]['key']) ? $videos['mp4'][0]['key'] : '';
            $url = isset($videos['mp4'][0]['url']) ? $videos['mp4'][0]['url'] : '';
        } else {
            // if mp4 not available, use the original video
            $status = isset($conversions['conversions'][0]['status']) ? $conversions['conversions'][0]['status'] : '';
            $key = isset($conversions['conversions'][0]['key']) ? $conversions['conversions'][0]['key'] : '';
            $protocol = isset($conversions['conversions'][0]['link']['protocol']) ? $conversions['conversions'][0]['link']['protocol'] : '';
            $address = isset($conversions['conversions'][0]['link']['address']) ? $conversions['conversions'][0]['link']['address'] : '';
            $path = isset($conversions['conversions'][0]['link']['path']) ? $conversions['conversions'][0]['link']['path'] : '';
            $url = $protocol . '://' . $address . $path;
        }

        if ($status === 'Ready') {

            $lastUpdated = date('Y-m-d H:i:s');
            $token = sha1(time());
            $status = '4'; // 0=Queue, 1=first stage, 2=second stage, 3=Error, 4=Done

            try {
                $sql_done = "
                    UPDATE `Media` 
                    SET
                      `Media`.`LastUpdated` = :LastUpdated, 
                      `Media`.`Status` = :Status,
                      `Media`.`Url` = :Url
                    WHERE
                      `Media`.`MediaId` = :MediaId";

                $stmt_done = $PDO->prepare($sql_done);
                $stmt_done->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR);
                $stmt_done->bindParam('Status', $status, PDO::PARAM_INT);
                $stmt_done->bindParam('MediaId', $mediaId, PDO::PARAM_INT);
                $stmt_done->bindParam('Url', $url, PDO::PARAM_STR);
                $stmt_done->execute();

            } catch (PDOException $e) {
                trigger_error($e->getMessage(), E_USER_ERROR);
            }//end try

            // post to wall
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
                $stmt_wall->bindParam('Created', $mediaCreated, PDO::PARAM_STR); // timestamp of when user was uploading
                $stmt_wall->bindParam('UserId', $mediaUserId, PDO::PARAM_INT);
                $stmt_wall->bindParam('UserIdFrom', $mediaUserIdFrom, PDO::PARAM_INT);
                if (empty($mediaPlayerId)) {
                    $mediaPlayerId = NULL;
                    $stmt_wall->bindParam('PlayerId', $mediaPlayerId, PDO::PARAM_NULL);
                } else {
                    $stmt_wall->bindParam('PlayerId', $mediaPlayerId, PDO::PARAM_INT);
                }
                $stmt_wall->bindParam('MediaId', $mediaId, PDO::PARAM_INT);
                $stmt_wall->bindParam('Token', $token, PDO::PARAM_STR);
                $stmt_wall->execute();

            } catch (PDOException $e) {
                trigger_error($e->getMessage(), E_USER_ERROR);
            }//end try

        }

    } // end while

} catch (PDOException $e) {
    trigger_error($e->getMessage(), E_USER_ERROR);
}//end try

if ($count == 0) {
// no videos to process
    echo json_encode(array('error' => 'no videos to process'));

}



