<?php

if (!isset($_SESSION['userId'])) {
    header('Location: index.php?page=login');
    exit;
}

$searchQuery = isset($_POST['query']) ? trim($_POST['query']) : '';
$searchQuery = filter_var($searchQuery, FILTER_SANITIZE_STRING);

$searchQuery = "%$searchQuery%"; // LIKE syntax

header('Content-Type: application/json');

$results = new stdClass();
$response = array();
$count = 0;

if (!empty($searchQuery)) {

    try {
        $sql = "
            SELECT 
              `GeoNames`.`Id`, 
              `GeoNames`.`City`
            FROM 
              `GeoNames`
            WHERE 
              `GeoNames`.`City` LIKE :SearchQuery";

        $stmt = $PDO->prepare($sql);
        $stmt->bindParam('SearchQuery', $searchQuery, PDO::PARAM_STR);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $count++;
            $city = new stdClass;
            $city->Id = $row['Id'];
            $city->Name = $row['City'];
            $results->cities[] = $city;
        }

    } catch (PDOException $e) {
        trigger_error($e->getMessage(), E_USER_ERROR);
    }//end try

    if ($count > 0) {
        $response['success'] = true;
        $response['results'] = $results;
        echo json_encode($response);
    } else {
        $response['error'] = 'No results';
        $results->cities = [];
        $response['results'] = $results;
        echo json_encode($response);
    }

} else {
    $response['error'] = 'Empty search query';
    $results->cities = [];
    $response['results'] = $results;
    echo json_encode($response);
}
