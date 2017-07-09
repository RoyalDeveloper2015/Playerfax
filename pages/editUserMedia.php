<?php

if (!isset($_SESSION['userId'])) {
    header('Location: index.php?page=login');
    exit;
}

header('Content-Type: application/json');

if (isset($_POST) && count($_POST) > 0) {

    $lastUpdated = date('Y-m-d H:i:s');

    $token = isset($_POST['token']) ? $_POST['token'] : '';

    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $title = filter_var($title, FILTER_SANITIZE_STRING);

    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $description = filter_var($description, FILTER_SANITIZE_STRING);

    $count = 0;
    $mediaId = null;
    try {
        $sql = "
        SELECT 
          `Media`.`MediaId`
        FROM 
          `Media` 
        WHERE
          `Media`.`UserIdFrom` = :UserIdFrom
        AND 
          `Media`.`Token` = :Token";

        $stmt = $PDO->prepare($sql);
        $stmt->bindParam('Token', $token, PDO::PARAM_INT);
        $stmt->bindParam('UserIdFrom', $userId, PDO::PARAM_INT);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $count++;
            $mediaId = $row['MediaId'];
        }

    } catch (PDOException $e) {
        trigger_error($e->getMessage(), E_USER_ERROR);
    }//end try

    if ($count > 0) {
        try {
            $sql = "
            UPDATE 
              `Media`
            SET
              `Media`.`Title` = :Title,
              `Media`.`Description` = :Description,
              `Media`.`LastUpdated` = :LastUpdated
            WHERE
              `Media`.`MediaId` = :MediaId
            AND 
              `Media`.`UserIdFrom` = :UserIdFrom";

            $stmt = $PDO->prepare($sql);
            $stmt->bindParam('Title', $title, PDO::PARAM_STR);
            $stmt->bindParam('Description', $description, PDO::PARAM_STR);
            $stmt->bindParam('LastUpdated', $lastUpdated, PDO::PARAM_STR); // Update date
            $stmt->bindParam('MediaId', $mediaId, PDO::PARAM_INT);
            $stmt->bindParam('UserIdFrom', $userId, PDO::PARAM_INT); // My logged in userId
            $stmt->execute();

        } catch (PDOException $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
        }//end try

        echo json_encode(array('success' => 'Updated successfully', 'title' => $title, 'description' => $description));
    }
}


