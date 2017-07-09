<?php

if (!isset($_SESSION['userId'])) {
    header('Location: index.php?page=login');
    exit;
}

header('Content-Type: application/json');

// ALTER TABLE `Users` ADD FULLTEXT INDEX Search(`FirstName`, `LastName`, `Email`);

$searchQuery = isset($_POST['query']) ? trim($_POST['query']) : '';
$searchQuery = filter_var($searchQuery, FILTER_SANITIZE_STRING);

$prevLink = '<span class="disabled">&laquo;</span> <span class="disabled">&lsaquo;</span>';
$nextLink = '<span class="disabled">&rsaquo;</span> <span class="disabled">&raquo;</span>';
$paging = '<div id="paging-header"><p> No results found </p></div>';
$recordsTotal = 0;
$recordsFiltered = 0;
// How many items to list per page
$limit = 10;
$results = new stdClass();
$response = array();

if (!empty($searchQuery)) {

    // InnoDB full-text search does not support the use of the @ symbol in boolean full-text searches. The @ symbol is reserved for use by the @distance proximity search operator.
    // (IN NATURAL LANGUAGE MODE vs IN BOOLEAN MODE)
    try {
        $sql = "
        SELECT COUNT(`UserId`) AS `recordsTotal`,
        MATCH (`FirstName`, `LastName`, `Email`)
        AGAINST (:SearchQuery IN NATURAL LANGUAGE MODE) AS `Relevance`
        FROM `Users`
        WHERE MATCH (`FirstName`, `LastName`, `Email`)
        AGAINST (:SearchQuery IN NATURAL LANGUAGE MODE)
        AND `Users`.`IsActive` = 1";

        $stmt = $PDO->prepare($sql);
        $stmt->bindParam('SearchQuery', $searchQuery, PDO::PARAM_STR);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $recordsTotal = (int)$row['recordsTotal'];
        }

    } catch (PDOException $e) {
        trigger_error($e->getMessage(), E_USER_ERROR);
    }//end try

    if ($recordsTotal > 0) {

        // How many pages will there be
        $pages = ceil($recordsTotal / $limit);

        // What page are we currently on?
        $page = min($pages, filter_input(INPUT_POST, 'page', FILTER_VALIDATE_INT, array(
            'options' => array(
                'default' => 1,
                'min_range' => 1,
            ),
        )));

        // Calculate the offset for the query
        $offset = ($page - 1) * $limit;

        // Some information to display to the user
        $start = $offset + 1;
        $end = min(($offset + $limit), $recordsTotal);

        // The "back" link
        $prevLink = ($page > 1) ? '<a class="paging-link" href="1" title="First page">&laquo;</a> <a class="paging-link" href="' . ($page - 1) . '" title="Previous page">&lsaquo;</a>' : '<span class="disabled">&laquo;</span> <span class="disabled">&lsaquo;</span>';

        // The "forward" link
        $nextLink = ($page < $pages) ? '<a class="paging-link" href="' . ($page + 1) . '" title="Next page">&rsaquo;</a> <a class="paging-link" href="' . $pages . '" title="Last page">&raquo;</a>' : '<span class="disabled">&rsaquo;</span> <span class="disabled">&raquo;</span>';

        // Display the paging information
        $paging = '<div id="paging-header"><p> Page ' . $page . ' of ' . $pages . ' pages, displaying ' . $start . '-' . $end . ' of ' . $recordsTotal . ' results </p></div>';


        // determine when to stop searching
        $prevPage = ($page > 1) ? true : false;
        $nextPage = ($page < $pages) ? true : false;

        try {
            $sql = "
            SELECT `UserId`, `FirstName`, `LastName`, `Picture`,
            MATCH (`FirstName`, `LastName`, `Email`) AGAINST (:SearchQuery IN NATURAL LANGUAGE MODE) AS `Relevance`
            FROM `Users`
            WHERE MATCH (`FirstName`, `LastName`, `Email`) AGAINST (:SearchQuery IN NATURAL LANGUAGE MODE)
            AND `Users`.`IsActive` = 1
            ORDER BY `Relevance` DESC
            LIMIT :Limit OFFSET :Offset";

            $stmt = $PDO->prepare($sql);
            $stmt->bindParam('SearchQuery', $searchQuery, PDO::PARAM_STR);
            $stmt->bindParam('Limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam('Offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $recordsFiltered++;
                $user = new stdClass;
                $user->Id = $row['UserId'];
                $user->Name = trim($row['FirstName'] .' ' . $row['LastName']);
                if (!empty($row['Picture']) && file_exists(constant('UPLOADS_USERS') . $row['Picture'])) {
                    $user->Picture = constant('URL_UPLOADS_USERS') . $row['Picture'];
                } else {
                    if ($row['Gender'] == 0) {
                        $user->Picture = constant('URL_UPLOADS_USERS') . 'default-male.png';
                    } else {
                        $user->Picture = constant('URL_UPLOADS_USERS') . 'default-female.png';
                    }
                }

                $results->users[] = $user;
            }

        } catch (PDOException $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
        }//end try

        //echo 'Records Filtered: ' . $recordsFiltered;


        if ($recordsFiltered > 0) {
            $response['success'] = true;
            $response['results'] = $results;
            $response['recordsTotal'] = $recordsTotal;
            $response['recordsFiltered'] = $recordsFiltered;
            echo json_encode($response);
        } else {
            $response['error'] = 'No filtered results';
            $results->users = [];
            $response['results'] = $results;
            $response['recordsTotal'] = 0;
            $response['recordsFiltered'] = 0;
            echo json_encode($response);
        }
    } else {
        $response['error'] = 'No results';
        $results->users = [];
        $response['results'] = $results;
        $response['recordsTotal'] = 0;
        $response['recordsFiltered'] = 0;
        echo json_encode($response);
    }
} else {
    $response['error'] = 'Empty search query';
    $results->users = [];
    $response['results'] = $results;
    $response['recordsTotal'] = 0;
    $response['recordsFiltered'] = 0;
    echo json_encode($response);
}
