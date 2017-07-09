<?php

if (!isset($_SESSION['userId'])) {
    header('Location: index.php?page=login');
    exit;
}


$msgBox = '';
$grantToken = isset($_GET['token']) ? trim($_GET['token']) : '';
$grantToken = filter_var($grantToken, FILTER_SANITIZE_STRING);

$count = 0;
$adminId = null;
$playerAdminId = null;
$userIdFrom = null;
$playerId = null;
$playerFullName = '';

try {
    $sql = "
    SELECT 
      `PlayerAdmins`.`PlayerAdminId`, 
      `PlayerAdmins`.`UserId`, 
      `PlayerAdmins`.`UserIdFrom`, 
      `PlayerAdmins`.`PlayerId`
    FROM 
      `PlayerAdmins` 
    WHERE
       `PlayerAdmins`.`UserId` = :UserId
    AND 
       `PlayerAdmins`.`Token` = :Token";

    $stmt = $PDO->prepare($sql);
    $stmt->bindParam('UserId', $userId, PDO::PARAM_INT);
    $stmt->bindParam('Token', $grantToken, PDO::PARAM_STR);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $count++;
        $playerAdminId = $row['PlayerAdminId'];
        $adminId = $row['UserId'];
        $userIdFrom = $row['UserIdFrom'];
        $playerId = $row['PlayerId'];
    }

} catch (PDOException $e) {
    trigger_error($e->getMessage(), E_USER_ERROR);
}//end try

// Make sure this request belongs to the correct logged-in user
if ($adminId === $userId) {

    try {
        $sql = "
        UPDATE 
          `PlayerAdmins` 
        SET
          `PlayerAdmins`.`Token` = NULL,
          `PlayerAdmins`.`IsAdmin` = 1
        WHERE
          `PlayerAdmins`.`UserId` = :UserId
        AND 
          `PlayerAdmins`.`Token` = :Token";

        $stmt = $PDO->prepare($sql);
        $stmt->bindParam('UserId', $userId, PDO::PARAM_INT);
        $stmt->bindParam('Token', $grantToken, PDO::PARAM_STR);
        $stmt->execute();

    } catch (PDOException $e) {
        trigger_error($e->getMessage(), E_USER_ERROR);
    }//end try

    try {
        $sql = "
        SELECT 
          `Players`.`FirstName`, 
          `Players`.`LastName`
        FROM 
          `Players` 
        WHERE
          `Players`.`PlayerId` = :PlayerId";

        $stmt = $PDO->prepare($sql);
        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $count++;
            $playerFullName = trim($row['FirstName'] . ' ' . $row['LastName']);
        }

    } catch (PDOException $e) {
        trigger_error($e->getMessage(), E_USER_ERROR);
    }//end try

    // Send message to grantee
    $granteeName = '';
    $granteeEmail = '';
    try {
        $sql = "
        SELECT 
          `Users`.`FirstName`, 
          `Users`.`LastName`, 
          `Users`.`Email`
        FROM 
          `Users` 
        WHERE
          `Users`.`UserId` = :UserId";

        $stmt = $PDO->prepare($sql);
        $stmt->bindParam('UserId', $userIdFrom, PDO::PARAM_INT);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $count++;
            $granteeName = trim($row['FirstName'] . ' ' . $row['LastName']);
            $granteeEmail = $row['Email'];
        }

    } catch (PDOException $e) {
        trigger_error($e->getMessage(), E_USER_ERROR);
    }//end try

    // insert wall PlayerAdminId
    $wallToken = sha1($userIp . microseconds());
    $created = date('Y-m-d H:i:s');
    try {
        $sql = "
        INSERT INTO `Wall` 
        (
          `WallId`, 
          `Created`, 
          `UserId`, 
          `UserIdFrom`, 
          `PlayerId`,
          `PlayerAdminId`,
          `Token`
        ) VALUES (
          NULL, 
          :Created, 
          :UserId, 
          :UserIdFrom, 
          :PlayerId,
          :PlayerAdminId,
          :Token
        )";

        $stmt = $PDO->prepare($sql);
        $stmt->bindParam('Created', $created, PDO::PARAM_STR); // Creation date
        $stmt->bindParam('UserId', $userId, PDO::PARAM_INT); // Owner of player
        $stmt->bindParam('UserIdFrom', $userIdFrom, PDO::PARAM_INT); // My logged in userId (one who is requesting admin access)
        $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT); // The player card
        $stmt->bindParam('PlayerAdminId', $playerAdminId, PDO::PARAM_INT);
        $stmt->bindParam('Token', $wallToken, PDO::PARAM_STR);
        $stmt->execute();

    } catch (PDOException $e) {
        trigger_error($e->getMessage(), E_USER_ERROR);
    }//end try

    if (!empty($granteeEmail)) {
        // Must login with SMTP to remove hourly sending limit
        require 'includes/phpmailer-5.2.22/PHPMailerAutoload.php';

        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = "retail.smtp.com"; // mail.playerfax.com
        $mail->Port = 25025; // 26
        $mail->SMTPAuth = true;
        //Username to use for SMTP authentication
        $mail->Username = "support@playerfax.com";
        //Password to use for SMTP authentication
        $mail->Password = "t4<oI]6O~5-GNk]:]P-D";
        $mail->setFrom('support@playerfax.com', 'Playerfax'); // must be from this domain
        $mail->AddAddress($granteeEmail, $granteeName);  // Send to registrant
        $mail->Subject = 'Playerfax PlayerAdmin Granted!';

        // HTML version
        $htmlBody = file_get_contents('includes/email/email.grantedPlayerAdmin.html');
        $htmlBody = str_replace('%USER_NAME%', $granteeName, $htmlBody);
        $htmlBody = str_replace('%ADMIN_NAME%', $userFullName, $htmlBody);
        $htmlBody = str_replace('%PLAYER_NAME%', $playerFullName, $htmlBody);

        $mail->Body = $htmlBody;

        // plain text version
        $plainBody = file_get_contents('includes/email/email.grantedPlayerAdmin.txt');
        $plainBody = str_replace('%USER_NAME%', $granteeName, $plainBody);
        $plainBody = str_replace('%ADMIN_NAME%', $userFullName, $plainBody);
        $plainBody = str_replace('%PLAYER_NAME%', $playerFullName, $plainBody);

        $mail->AltBody = $plainBody;

        $mail->send();
    }

    $msgBox = 'Admin rights granted for this request';

} else {
    $msgBox = 'The requested link is expired or invalid';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Playerfax | Request Player Admin Rights</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1, maximum-scale=1">
    <link type="image/png" rel="icon" sizes="32x32" href="favicon.png"/>
    <link type="text/css" rel="stylesheet" href="css/bootstrap.min.css"/>
    <link type="text/css" rel="stylesheet" href="css/bootstrap-datepicker.css"/>
    <link type="text/css" rel="stylesheet" href="css/flexslider.css"/>
    <link type="text/css" rel="stylesheet" href="css/style.css"/>
    <link type="text/css" rel="stylesheet" href="css/responsive.css"/>
</head>
<body class="login_bg">
<section class="login_section register_section">
    <div class="container">
        <div class="login_content">
            <div class="men_img">
                <div class="home_logo">
                    <a href="/">
                        <img src="images/playerlogo_img.png" class="img-responsive" alt="logo"/>
                    </a>
                </div>
                <img src="images/rmen_img.png" class="img-responsive imgmen" alt="men_img"/>
            </div>

            <div class="login_contain">
                <h1>Admin Rights</h1>
                <h5><?php echo $msgBox; ?></h5>
            </div>

        </div>
    </div>
</section>
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/jquery.flexslider-min.js"></script>
<script type="text/javascript" src="js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="js/bootstrap-tabcollapse.js"></script>
<script type="text/javascript" src="js/SmoothScroll.js"></script>
<script type="text/javascript" src="js/custom.js"></script>
</body>
</html>
