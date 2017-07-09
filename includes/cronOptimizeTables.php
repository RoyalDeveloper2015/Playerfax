<?php

// This script will help reclaim disk space if run periodically

// This is the cron job command - run it every Monday at 3 am
// php /home/player/public_html/includes/cronOptimizeTables.php >/dev/null 2>&1

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

$tables = array();
try {
    $sql = "SHOW TABLES";

    $stmt = $PDO->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $tableName = str_replace('"', '', $row['Tables_in_player_main']); // remove quotes surrounding the table name
        array_push($tables, $tableName);
    }

} catch (PDOException $e) {
    trigger_error($e->getMessage(), E_USER_ERROR);
}//end try

if (count($tables) > 0) {

    foreach ($tables as $table) {
        try {
            $sql = "OPTIMIZE TABLE `:Table`";

            $stmt = $PDO->prepare($sql);
            $stmt->bindParam('Table', $table, PDO::PARAM_STR);
            $stmt->execute();

        } catch (PDOException $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
        }//end try
    }

    echo 'Complete';
}


