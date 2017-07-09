<?php

session_start();

// Logout
if (isset($_GET['page'])) {
    $page = $_GET['page'];
    if ($page == 'logout') {
        $cookieParams = session_get_cookie_params();
        setcookie(session_name(), '', 0, $cookieParams['path'], $cookieParams['domain'], $cookieParams['secure'], isset($cookieParams['httponly']));
        session_destroy();
        session_write_close();
        header('Location: index.php?page=login');
    }
}

// Config
require 'config.php';
// Functions
require 'includes/functions.php';

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

// Keep some User data available
$userId = null;
$userIp = '';
$userEmail = '';
$userGender = '';
$userTimezone = '';
$userFullName = '';
$userFirstName = '';
$userLastName = '';
$userToken = '';
$userPicture = '';
$userPermissions = '';

if (isset($_SESSION['userId'])) {

    try {
        $sql = "
                SELECT
                  `Users`.`UserId`,
                  `Users`.`Email`,
                  `Users`.`Gender`,
                  `Users`.`Timezone`,
                  `Users`.`FirstName`,
                  `Users`.`LastName`,
                  `Users`.`Token`,
                  `Users`.`Picture`,
                  `Users`.`Password`,
                  `UserOptions`.`Permissions` AS `UserPermissions`
                FROM 
                  `Users` 
                USE INDEX (`UserIdIsActive`)
                LEFT JOIN 
                  `UserOptions`
                USE INDEX FOR JOIN (`UserId`)
                ON
                  `UserOptions`.`UserId` = `Users`.`UserId`
                WHERE
                  `Users`.`UserId` = :UserId
                AND 
                  `Users`.`IsActive` = 1";

        $stmt = $PDO->prepare($sql);
        $stmt->bindParam('UserId', $_SESSION['userId'], PDO::PARAM_STR);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $userIp = getClientIp();
            $userId = $row['UserId'];
            $userEmail = $row['Email'];
            $userGender = $row['Gender'];
            $userTimezone = $row['Timezone'];
            $userFullName = trim($row['FirstName'] . ' ' . $row['LastName']);
            $userFullName = preg_replace('/\s+/', ' ', $userFullName);
            $userFirstName = $row['FirstName'];
            $userLastName = $row['LastName'];
            $userToken = $row['Token'];
            $userPasswordFromDb = $row['Password'];

            if (!empty($row['Picture']) && file_exists(constant('UPLOADS_USERS') .$row['Picture'])) {
                $userPicture = constant('URL_UPLOADS_USERS') . $row['Picture'];
            } else {
                if ($userGender == 0) {
                    $userPicture = constant('URL_UPLOADS_USERS') . 'default-male.png';
                } else {
                    $userPicture = constant('URL_UPLOADS_USERS') . 'default-female.png';
                }
            }

            if (!empty($row['UserPermissions'])) {
                $userPermissions = $row['UserPermissions']; // json string
            }
        }

    } catch (PDOException $e) {
        trigger_error($e->getMessage(), E_USER_ERROR);
    }//end try

    $_SESSION['userIp'] = $userIp;
    $_SESSION['userId'] = $userId;
    $_SESSION['userEmail'] = $userEmail;
    $_SESSION['userGender'] = $userGender;
    $_SESSION['userTimezone'] = $userTimezone;
    $_SESSION['userFullName'] = filter_var($userFullName, FILTER_SANITIZE_STRING);
    $_SESSION['userFirstName'] = filter_var($userFirstName, FILTER_SANITIZE_STRING);
    $_SESSION['userLastName'] = filter_var($userLastName, FILTER_SANITIZE_STRING);
    $_SESSION['userToken'] = $userToken;
    $_SESSION['userPicture'] = $userPicture;
    $_SESSION['userPermissions'] = $userPermissions;

    /*
    $userIp = $_SESSION['userIp'];
    $userId = $_SESSION['userId'];
    $userEmail = $_SESSION['userEmail'];
    $userGender = $_SESSION['userGender'];
    $userTimezone = $_SESSION['userTimezone'];
    $userFullName = $_SESSION['userFullName'];
    $userFirstName = $_SESSION['userFirstName'];
    $userLastName = $_SESSION['userLastName'];
    $userToken = $_SESSION['userToken'];
    $userPicture = $_SESSION['userPicture'];
    $userPermissions = $_SESSION['userPermissions'];
    */
}

// Link to the Page
$page = 'index';
$pageName = 'Index';

if (isset($_GET['page']) && $_GET['page'] == 'registration') {
    $page = 'registration';
} else if (isset($_GET['page']) && $_GET['page'] == 'login') {
    $page = 'login';
} else if (isset($_GET['page']) && $_GET['page'] == 'home') {
    $page = 'home';
} else if (isset($_GET['page']) && $_GET['page'] == 'player') {
    $page = 'player';
} else if (isset($_GET['action']) && $_GET['action'] == 'addPlayer') {
    $page = 'addPlayer';
} else if (isset($_GET['action']) && $_GET['action'] == 'confirm') {
    $page = 'confirm';
} else if (isset($_GET['action']) && $_GET['action'] == 'resetPassword') {
    $page = 'resetPassword';
} else if (isset($_GET['action']) && $_GET['action'] == 'editUserPicture') {
    $page = 'editUserPicture';
} else if (isset($_GET['action']) && $_GET['action'] == 'editPlayerPicture') {
    $page = 'editPlayerPicture';
} else if (isset($_GET['action']) && $_GET['action'] == 'searchForPlayer') {
    $page = 'searchForPlayer';
} else if (isset($_GET['action']) && $_GET['action'] == 'searchForCity') {
    $page = 'searchForCity';
} else if (isset($_GET['action']) && $_GET['action'] == 'followPlayer') {
    $page = 'followPlayer';
} else if (isset($_GET['action']) && $_GET['action'] == 'requestPlayerAdmin') {
    $page = 'requestPlayerAdmin';
} else if (isset($_GET['action']) && $_GET['action'] == 'addUserMessage') {
    $page = 'addUserMessage';
} else if (isset($_GET['action']) && $_GET['action'] == 'addPlayerMessage') {
    $page = 'addPlayerMessage';
} else if (isset($_GET['action']) && $_GET['action'] == 'addPlayerPost') {
    $page = 'addPlayerPost';
} else if (isset($_GET['action']) && $_GET['action'] == 'addUserPost') {
    $page = 'addUserPost';
} else if (isset($_GET['action']) && $_GET['action'] == 'addPlayerStats') {
    $page = 'addPlayerStats';
} else if (isset($_GET['action']) && $_GET['action'] == 'editPlayerStatsForEvent') {
    $page = 'editPlayerStatsForEvent';
} else if (isset($_GET['action']) && $_GET['action'] == 'searchForEventAdmin') {
    $page = 'searchForEventAdmin';
} else if (isset($_GET['action']) && $_GET['action'] == 'addEvent') {
    $page = 'addEvent';
} else if (isset($_GET['action']) && $_GET['action'] == 'editEvent') {
    $page = 'editEvent';
} else if (isset($_GET['page']) && $_GET['page'] == 'event-results') {
    $page = 'event-results';
} else if (isset($_GET['page']) && $_GET['page'] == 'event-details') {
    $page = 'event-details';
} else if (isset($_GET['page']) && $_GET['page'] == 'events') {
    $page = 'events';
} else if (isset($_GET['page']) && $_GET['page'] == 'profile') {
    $page = 'profile';
} else if (isset($_GET['page']) && $_GET['page'] == 'my-players') {
    $page = 'my-players';
} else if (isset($_GET['action']) && $_GET['action'] == 'addPlayerMedia') {
    $page = 'addPlayerMedia';
} else if (isset($_GET['action']) && $_GET['action'] == 'addUserMedia') {
    $page = 'addUserMedia';
} else if (isset($_GET['action']) && $_GET['action'] == 'editPlayerMedia') {
    $page = 'editPlayerMedia';
} else if (isset($_GET['action']) && $_GET['action'] == 'editUserMedia') {
    $page = 'editUserMedia';
} else if (isset($_GET['action']) && $_GET['action'] == 'searchPlayerTimeline') {
    $page = 'searchPlayerTimeline';
} else if (isset($_GET['action']) && $_GET['action'] == 'searchUserTimeline') {
    $page = 'searchUserTimeline';
} else if (isset($_GET['action']) && $_GET['action'] == 'addPlayerToEvent') {
    $page = 'addPlayerToEvent';
} else if (isset($_GET['action']) && $_GET['action'] == 'editPlayerForEvent') {
    $page = 'editPlayerForEvent';
} else if (isset($_GET['action']) && $_GET['action'] == 'addUserComment') {
    $page = 'addUserComment';
} else if (isset($_GET['action']) && $_GET['action'] == 'editUserPost') {
    $page = 'editUserPost';
} else if (isset($_GET['action']) && $_GET['action'] == 'getUserPost') {
    $page = 'getUserPost';
} else if (isset($_GET['action']) && $_GET['action'] == 'editUserMedia') {
    $page = 'editUserMedia';
} else if (isset($_GET['action']) && $_GET['action'] == 'getUserMedia') {
    $page = 'getUserMedia';
} else if (isset($_GET['action']) && $_GET['action'] == 'deleteUserPost') {
    $page = 'deleteUserPost';
} else if (isset($_GET['action']) && $_GET['action'] == 'deleteUserMedia') {
    $page = 'deleteUserMedia';
} else if (isset($_GET['action']) && $_GET['action'] == 'hideUserPost') {
    $page = 'hideUserPost';
} else if (isset($_GET['action']) && $_GET['action'] == 'hideUserMedia') {
    $page = 'hideUserMedia';
} else if (isset($_GET['action']) && $_GET['action'] == 'unfollowUserPost') {
    $page = 'unfollowUserPost';
} else if (isset($_GET['action']) && $_GET['action'] == 'unfollowUserMedia') {
    $page = 'unfollowUserMedia';
} else if (isset($_GET['action']) && $_GET['action'] == 'reportUserPost') {
    $page = 'reportUserPost';
} else if (isset($_GET['action']) && $_GET['action'] == 'reportUserMedia') {
    $page = 'reportUserMedia';
} else if (isset($_GET['action']) && $_GET['action'] == 'editUserPostComment') {
    $page = 'editUserPostComment';
} else if (isset($_GET['action']) && $_GET['action'] == 'editUserMediaComment') {
    $page = 'editUserMediaComment';
} else if (isset($_GET['action']) && $_GET['action'] == 'deleteUserPostComment') {
    $page = 'deleteUserPostComment';
} else if (isset($_GET['action']) && $_GET['action'] == 'deleteUserMediaComment') {
    $page = 'deleteUserMediaComment';
} else if (isset($_GET['action']) && $_GET['action'] == 'hideUserPostComment') {
    $page = 'hideUserPostComment';
} else if (isset($_GET['action']) && $_GET['action'] == 'hideUserMediaComment') {
    $page = 'hideUserMediaComment';
} else if (isset($_GET['action']) && $_GET['action'] == 'getUserPostComment') {
    $page = 'getUserPostComment';
} else if (isset($_GET['action']) && $_GET['action'] == 'getUserMediaComment') {
    $page = 'getUserMediaComment';
} else if (isset($_GET['action']) && $_GET['action'] == 'likeUserPost') {
    $page = 'likeUserPost';
} else if (isset($_GET['action']) && $_GET['action'] == 'likeUserMedia') {
    $page = 'likeUserMedia';
} else if (isset($_GET['action']) && $_GET['action'] == 'shareUserPost') {
    $page = 'shareUserPost';
} else if (isset($_GET['action']) && $_GET['action'] == 'shareUserMedia') {
    $page = 'shareUserMedia';
} else {
    if (!empty($_GET['page'])) {
        // Load the Page
        require 'pages/404.php';
        exit;
    }
}

if (file_exists('pages/' . $page . '.php')) {
    // Load the Page
    require 'pages/' . $page . '.php';
}

