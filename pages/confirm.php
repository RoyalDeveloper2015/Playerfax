<?php

$msgBox = '';
$userToken = isset($_GET['token']) ? trim($_GET['token']) : '';
$userToken = filter_var($userToken, FILTER_SANITIZE_STRING);

$count = 0;
$userId = null;
$userLastUpdated = date('Y-m-d H:i:s');
$userFullName = '';
$userEmail = '';

try {
    $sql = "
    SELECT 
      `Users`.`UserId`, 
      `Users`.`FirstName`, 
      `Users`.`LastName`, 
      `Users`.`Email`
    FROM 
      `Users` 
    WHERE
      `Users`.`Token` = :Token
    AND
      `Users`.`IsActive` = 0";

    $stmt = $PDO->prepare($sql);
    $stmt->bindParam('Token', $userToken, PDO::PARAM_STR);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $count++;
        $userId = $row['UserId'];
        $userFullName = trim($row['FirstName'] . ' ' . $row['LastName']);
        $userEmail = $row['Email'];
    }

} catch (PDOException $e) {
    trigger_error($e->getMessage(), E_USER_ERROR);
}//end try

if ($count > 0) {

    $newUserToken = sha1($userIp . microseconds());

    // activate the user
    try {
        $sql = "
        UPDATE 
          `Users` 
        SET
          `Users`.`IsActive` = 1,
          `Users`.`LastUpdated` = :LastUpdated,
          `Users`.`Token` = :Token
        WHERE
          `Users`.`UserId` = :UserId";

        $stmt = $PDO->prepare($sql);
        $stmt->bindParam('LastUpdated', $userLastUpdated, PDO::PARAM_STR);
        $stmt->bindParam('Token', $newUserToken, PDO::PARAM_STR);
        $stmt->bindParam('UserId', $userId, PDO::PARAM_INT);
        $stmt->execute();

    } catch (PDOException $e) {
        trigger_error($e->getMessage(), E_USER_ERROR);
    }//end try

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
    $mail->AddAddress($userEmail, $userFullName);  // Send to registrant
    $mail->Subject = 'Playerfax Registration Confirmed!';

    // HTML version
    $htmlBody = file_get_contents('includes/email/email.userConfirmed.html');
    $htmlBody = str_replace('%NAME%', $userFullName, $htmlBody);
    $htmlBody = str_replace('%EMAIL%', $userEmail, $htmlBody);

    $mail->Body = $htmlBody;

    // plain text version
    $plainBody = file_get_contents('includes/email/email.userConfirmed.txt');
    $plainBody = str_replace('%NAME%', $userFullName, $plainBody);
    $plainBody = str_replace('%EMAIL%', $userEmail, $plainBody);

    $mail->AltBody = $plainBody;

    $mail->send();

    $msgBox = 'Your account is now confirmed';

} else {
    $msgBox = 'The requested link is expired or invalid';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Playerfax | Registration Confirmation</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1, maximum-scale=1">
    <link rel="apple-touch-icon" sizes="180x180" href="http://www.playerfax.com/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="http://www.playerfax.com/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="http://www.playerfax.com/favicon-16x16.png">
    <link rel="manifest" href="http://www.playerfax.com/manifest.json">
    <link rel="mask-icon" href="http://www.playerfax.com/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="theme-color" content="#123456">
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
                    <h1>Registration</h1>
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
