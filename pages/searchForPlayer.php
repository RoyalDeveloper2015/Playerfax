<?php

if (!isset($_SESSION['userId'])) {
    header('Location: index.php?page=login');
    exit;
}

header('Content-Type: application/json');

// ALTER TABLE `Players` ADD FULLTEXT INDEX Search(`FirstName`, `MiddleName`, `LastName`, `Email`, `School`, `GradYear`, `City`, `StateShort`, `StateLong`);

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
        SELECT COUNT(`PlayerId`) AS `recordsTotal`,
        MATCH (`FirstName`, `MiddleName`, `LastName`, `Email`, `School`, `GradYear`, `City`, `StateShort`, `StateLong`)
        AGAINST (:SearchQuery IN NATURAL LANGUAGE MODE) AS `Relevance`
        FROM `Players`
        WHERE MATCH (`FirstName`, `MiddleName`, `LastName`, `Email`, `School`, `GradYear`, `City`, `StateShort`, `StateLong`)
        AGAINST (:SearchQuery IN NATURAL LANGUAGE MODE)
        AND `Players`.`IsActive` = 1";

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
            SELECT `Players`.`PlayerId`, `Picture`, `Gender`, `FirstName`, `MiddleName`, `LastName`, `School`, `GradYear`, `City`, `StateShort`, `StateLong`, `Token`, `Players`.`UserId` AS `PlayerUserId`,
            MATCH (`FirstName`, `MiddleName`, `LastName`, `Email`, `School`, `GradYear`, `City`, `StateShort`, `StateLong`) AGAINST (:SearchQuery IN NATURAL LANGUAGE MODE) AS `Relevance`
            FROM `Players`
            WHERE MATCH (`FirstName`, `MiddleName`, `LastName`, `Email`, `School`, `GradYear`, `City`, `StateShort`, `StateLong`) AGAINST (:SearchQuery IN NATURAL LANGUAGE MODE)
            AND `Players`.`IsActive` = 1
            ORDER BY `Relevance` DESC
            LIMIT :Limit OFFSET :Offset";

            $stmt = $PDO->prepare($sql);
            $stmt->bindParam('SearchQuery', $searchQuery, PDO::PARAM_STR);
            $stmt->bindParam('Limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam('Offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $recordsFiltered++;
                $player = new stdClass;
                //$player->Id = $row['PlayerId'];
                $player->Token = $row['Token'];
                $player->Gender = $row['Gender'];
                $player->Name = trim($row['FirstName'] . ' ' . $row['MiddleName'] . ' ' . $row['LastName']);
                $player->GradYear = $row['GradYear'];

                if (!empty($row['Picture']) && file_exists(constant('UPLOADS_PLAYERS') . $row['Picture'])) {
                    $player->Picture = constant('URL_UPLOADS_PLAYERS') . $row['Picture'];
                } else {
                    if ($row['Gender'] == 0) {
                        $player->Picture = constant('URL_UPLOADS_PLAYERS') . 'default-male.png';
                    } else {
                        $player->Picture = constant('URL_UPLOADS_PLAYERS') . 'default-female.png';
                    }
                }

                $player->School = $row['School'];
                $player->City = $row['City'];
                $player->StateShort = $row['StateShort'];
                $player->StateLong = $row['StateLong'];
                // Check if this player has a follower
                //

                $count = 0;
                $followUserId = null;
                try {
                    $sql1 = "
                    SELECT 
                      `Follows`.`UserId` AS `FollowUserId` 
                    FROM 
                      `Follows` 
                    WHERE
                      `Follows`.`PlayerId` = :PlayerId
                    AND
                      `Follows`.`UserIdFrom` = :UserIdFrom";

                    $stmt1 = $PDO->prepare($sql1);
                    $stmt1->bindParam('PlayerId', $row['PlayerId'], PDO::PARAM_STR);
                    $stmt1->bindParam('UserIdFrom', $userId, PDO::PARAM_STR);
                    $stmt1->execute();

                    while ($row1 = $stmt1->fetch(PDO::FETCH_ASSOC)) {
                        $count++;
                        $followUserId = $row1['FollowUserId'];
                    }

                } catch (PDOException $e) {
                    trigger_error($e->getMessage(), E_USER_ERROR);
                }//end try

                if ($count > 0) {
                    $player->IsFriend = true;
                } else {
                    $player->IsFriend = false;
                }
                $results->players[] = $player;
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
            $results->players = [];
            $response['results'] = $results;
            $response['recordsTotal'] = 0;
            $response['recordsFiltered'] = 0;
            echo json_encode($response);
        }
    } else {
        $response['error'] = 'No results';
        $results->players = [];
        $response['results'] = $results;
        $response['recordsTotal'] = 0;
        $response['recordsFiltered'] = 0;
        echo json_encode($response);
    }
} else {
    $response['error'] = 'Empty search query';
    $results->players = [];
    $response['results'] = $results;
    $response['recordsTotal'] = 0;
    $response['recordsFiltered'] = 0;
    echo json_encode($response);
}
