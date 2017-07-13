<?php

 // print_r($_SERVER);

    require_once __DIR__ . '/../includes/Facebook/autoload.php';

    $fb = new Facebook\Facebook([
    'app_id' => FB_APP_ID, // Replace {app-id} with your app id
    'app_secret' => FB_APP_SECRET,
    'default_graph_version' => 'v2.9',
    ]);
    $helper = $fb->getRedirectLoginHelper();

    try {
        $accessToken = $helper->getAccessToken();
    } catch ( Facebook\Exceptions\FacebookResponseException $e) {
        echo 'Graph returned an error:' .$e->getMessage();
        exit;
    } catch ( Facebook\Exceptions\FacebookSDKException $e) {
        echo 'Facebook SDK returned an error:' . $e->getMessage();
        exit;
    }

    $fb->setDefaultAccessToken($accessToken);
    $response_user = $fb->get('/me?fields=first_name,last_name,gender,email');
    $user_info = $response_user->getGraphUser();

    try {
        $requestPicture = $fb->get('/me/picture?redirect=false&height=300');
        $picture = $requestPicture->getGraphUser();
    } catch ( Exception $e) {
        echo $e->getMessage();
        exit;
    }

    echo "<img style='display:none;' src='" . $picture['url']."'/>";

    $img = __DIR__.'/../uploads/users/'. $user_info['id']. '.jpg';

    if ( isset($_SESSION['facebook_access_token'])) {
        $fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
    } else {
        $_SESSION['facebook_access_token'] = (string)$accessToken;
    }
    $mail_addr = $user_info['email'];
    try {
        $sql = "SELECT COUNT(Email) as flag FROM Users WHERE Email = :Email";
        $stmp = $PDO->prepare($sql);
        $stmp->bindParam('Email',$mail_addr,PDO::PARAM_STR);
        $stmp->execute();
        $row = $stmp->fetch();
        $flag = $row[0];
        if ( (int)$flag == 0) {
            file_put_contents($img,file_get_contents($picture['url']));
            try {
                $sql = "
                INSERT INTO `Users`
                (
                  `UserId`,
                  `Created`,
                  `LastUpdated`,
                  `Gender`,
                  `FirstName`,
                  `LastName`,
                  `Email`,
                  `Password`,
                  `Token`,
                  `IsActive`,
                  `Picture`
                ) VALUES (
                  NULL,
                  :Created,
                  :LastUpdated,
                  :Gender,
                  :FirstName,
                  :LastName,
                  :Email,
                  :Password,
                  :Token,
                  :IsActive,
                  :Img
                )";

                $stmp = $PDO->prepare($sql);
                $userCreated = date('Y-m-d H:i:s');
                $stmp->bindParam('Created', $userCreated, PDO::PARAM_STR);
                $userLastUpdated = date('Y-m-d H:i:s');
                $stmp->bindParam('LastUpdated', $userLastUpdated, PDO::PARAM_STR);
                // print_r($user_info['gender'] == 'male');
                $gFlag = $user_info['gender'] == 'male' ? 0:1;
                $stmp->bindParam('Gender', $gFlag , PDO::PARAM_INT);

                $stmp->bindParam('FirstName', $user_info['first_name'], PDO::PARAM_STR);
                $stmp->bindParam('LastName', $user_info['last_name'], PDO::PARAM_STR);
                $stmp->bindParam('Email', $user_info['email'], PDO::PARAM_STR);
                $pwdString = '';
                $stmp->bindParam('Password', $pwdString, PDO::PARAM_STR);
                $accessString = (string)$accessToken;
                $stmp->bindParam('Token', $accessString, PDO::PARAM_STR);
                $activeFlag = 1;
                $stmp->bindParam('IsActive', $activeFlag, PDO::PARAM_INT);
                $imgUrl = $user_info['id']. '.jpg';
                $stmp->bindParam('Img',$imgUrl,PDO::PARAM_STR);
                $stmp->execute();
            } catch (PDOException $e) {
                trigger_error($e->getMessage(), E_USER_ERROR);
            }//end try

        }
        $sql = "SELECT UserId FROM Users WHERE Email = :Email";
        $stmp = $PDO->prepare($sql);
        $stmp->bindParam('Email',$mail_addr,PDO::PARAM_STR);
        $stmp->execute();
        $row = $stmp->fetch();
        $userId = $row[0];
        $_SESSION['userIp'] = getClientIp();
        $_SESSION['userId'] = $userId;
        $_SESSION['userEmail'] = $user_info['email'];
        $_SESSION['userGender'] = $user_info['gender'];
        $_SESSION['userTimezone'] = '';
        $_SESSION['userFullName'] = $user_info['name'];
        $_SESSION['userFirstName'] = $user_info['first_name'];
        $_SESSION['userLastName'] = $user_info['last_name'];
        $_SESSION['userToken'] = (string)$accessToken;
        $_SESSION['userPicture'] = $user_info['id'];
        // print_r($_SESSION);
        header('Location: http://localhost/playerfax/index.php?page=home');

    }  catch ( PDOException $e ) {
        echo $e->getMessage();
    }



?>
