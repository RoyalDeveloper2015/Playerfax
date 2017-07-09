<?php

if (!isset($_SESSION['userId'])) {
    header('Location: index.php?page=login');
    exit;
}

header('Content-Type: application/json');

$msgBox = alertBox("Shared successfully", "<i class='fa fa-check-square-o'></i>", "success");
echo json_encode(array('success' => $msgBox));

