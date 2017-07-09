<?php

if (!isset($_SESSION['userId'])) {
    header('Location: index.php?page=login');
    exit;
}

header('Content-Type: application/json');

if (isset($_POST) && count($_POST) > 0) {

    $token = isset($_POST['token']) ? $_POST['token'] : '';

    $count = 0;
    $mediaTitle = '';
    $mediaDescription = '';
    try {
        $sql = "
        SELECT 
          `Media`.`Title`,
          `Media`.`Description`
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
            $mediaTitle = $row['Title'];
            $mediaDescription = $row['Description'];
        }

    } catch (PDOException $e) {
        trigger_error($e->getMessage(), E_USER_ERROR);
    }//end try

    if ($count > 0) {
        echo json_encode(array('success' => 'Message', 'title' => $mediaTitle, 'description' => $mediaDescription));
    } else {
        $msgBox = alertBox("Insufficient privilege", "<i class='fa fa-times'></i>", "danger");
        echo json_encode(array('error' => $msgBox));
    }
}





