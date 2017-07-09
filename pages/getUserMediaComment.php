<?php

if (!isset($_SESSION['userId'])) {
    header('Location: index.php?page=login');
    exit;
}

header('Content-Type: application/json');

if (isset($_POST) && count($_POST) > 0) {

    $token = isset($_POST['token']) ? $_POST['token'] : '';

    $count = 0;
    $mediaContent = '';
    try {
        $sql = "
        SELECT 
          `MediaComments`.`Content`
        FROM 
          `MediaComments` 
        WHERE
          `MediaComments`.`UserIdFrom` = :UserIdFrom
        AND 
          `MediaComments`.`Token` = :Token";

        $stmt = $PDO->prepare($sql);
        $stmt->bindParam('Token', $token, PDO::PARAM_INT);
        $stmt->bindParam('UserIdFrom', $userId, PDO::PARAM_INT);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $count++;
            $mediaContent = $row['Content'];
        }

    } catch (PDOException $e) {
        trigger_error($e->getMessage(), E_USER_ERROR);
    }//end try

    if ($count > 0) {
        echo json_encode(array('success' => 'Message', 'content' => $mediaContent));
    } else {
        $msgBox = alertBox("Insufficient privilege", "<i class='fa fa-times'></i>", "danger");
        echo json_encode(array('error' => $msgBox));
    }
}





