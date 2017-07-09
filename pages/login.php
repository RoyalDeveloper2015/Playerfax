<?php

ini_set('session.use_only_cookies', 1); // Forces sessions to only use cookies.
$cookieParams = session_get_cookie_params(); // Gets current cookies params.
session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], false, true);
session_start();

$msgBox = '';

$loginError = false;
$resetError = false;

$userIp = getClientIp();
$userId = null;
$userEmail = '';
$userGender = '';
$userTimezone = '';
$userFullName = '';
$userFirstName = '';
$userLastName = '';
$userToken = '';
$userPicture = '';
$userPermissions = '';
$userPassword = '';
$userPasswordFromDb = '';

if (isset($_POST['submit']) && $_POST['submit'] == 'login') {

    $userEmail = isset($_POST['userEmail']) ? trim($_POST['userEmail']) : '';
    $userEmail = filter_var($userEmail, FILTER_SANITIZE_EMAIL);

    $userPassword = isset($_POST['userPassword']) ? trim($_POST['userPassword']) : '';

    if (!empty($userEmail) && !empty($userPassword)) {

        if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
            $loginError = true;
            $msgBox = alertBox("Enter a valid email address", "<i class='fa fa-times'></i>", "danger");
        }

        if ($loginError === false) {

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
                USE INDEX (`EmailIsActive`)
                LEFT JOIN 
                  `UserOptions`
                USE INDEX FOR JOIN (`UserId`)
                ON
                  `UserOptions`.`UserId` = `Users`.`UserId`
                WHERE
                  `Users`.`Email` = :Email
                AND 
                  `Users`.`IsActive` = 1";

                $stmt = $PDO->prepare($sql);
                $stmt->bindParam('Email', $userEmail, PDO::PARAM_STR);
                $stmt->execute();

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
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

            if (password_verify($userPassword, $userPasswordFromDb)) {

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
                //$msgBox = alertBox("Login successful", "<i class='fa fa-check-square-o'></i>", "success");

                header('Location: index.php?page=home');
                exit;

            } else {
                $msgBox = alertBox("Email or Password is incorrect", "<i class='fa fa-times'></i>", "danger");
            }
        }

    } else {
        $msgBox = alertBox("All fields are required", "<i class='fa fa-times'></i>", "danger");
    }
}

if (isset($_POST['submit']) && $_POST['submit'] == 'reset') {

    $userEmail = isset($_POST['userEmail']) ? trim($_POST['userEmail']) : '';
    $userEmail = filter_var($userEmail, FILTER_SANITIZE_EMAIL);

    if (!empty($userEmail)) {

        if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
            $resetError = true;
            $msgBox = alertBox("Enter a valid email address", "<i class='fa fa-times'></i>", "danger");
        }

        if ($resetError == false) {

            $resetCreated = date('Y-m-d H:i:s');
            $resetToken = sha1($userIp . microseconds());
            $count = 0;

            try {
                $sql = "
                SELECT 
                  `Users`.* 
                FROM 
                  `Users` 
                USE INDEX (`EmailIsActive`)
                WHERE
                  `Users`.`Email` = :Email
                AND 
                  `Users`.`IsActive` = 1";

                $stmt = $PDO->prepare($sql);
                $stmt->bindParam('Email', $userEmail, PDO::PARAM_STR);
                $stmt->execute();

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $count++;
                    $userId = $row['UserId'];
                    $userEmail = $row['Email'];
                    $userFullName = $row['FullName'];
                }

            } catch (PDOException $e) {
                trigger_error($e->getMessage(), E_USER_ERROR);
            }//end try

            if ($count > 0) {

                try {
                    $sql = "
                    INSERT INTO `ResetPassword` 
                    (
                      `ResetId` ,
                      `Created` ,
                      `UserId` ,
                      `Token`
                    ) VALUES (
                      NULL , 
                      :Created, 
                      :UserId, 
                      :Token
                    )";

                    $stmt1 = $PDO->prepare($sql);
                    $stmt1->bindParam('Created', $resetCreated, PDO::PARAM_STR);
                    $stmt1->bindParam('UserId', $userId, PDO::PARAM_INT);
                    $stmt1->bindParam('Token', $resetToken, PDO::PARAM_STR);
                    $stmt1->execute();

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
                $mail->AddAddress($userEmail, $userFullName);  // Send to user
                $mail->AddAddress('ray@myphoneroom.com', 'R Solomon');  // Send to admin
                $mail->Subject = 'Forgot Your Password?';

                // HTML version
                $htmlBody = file_get_contents('includes/email/email.resetPassword.html');
                $htmlBody = str_replace('%NAME%', $userFullName, $htmlBody);
                $htmlBody = str_replace('%RESET_TOKEN%', $resetToken, $htmlBody);

                $mail->Body = $htmlBody;

                // plain text version
                $plainBody = file_get_contents('includes/email/email.resetPassword.txt');
                $plainBody = str_replace('%NAME%', $userFullName, $plainBody);
                $plainBody = str_replace('%RESET_TOKEN%', $resetToken, $plainBody);

                $mail->AltBody = $plainBody;

                $mail->send();

                $msgBox = alertBox("Check email for password reset", "<i class='fa fa-check-square-o'></i>", "success");
            }
        }

    } else {
        $msgBox = alertBox("All fields are required", "<i class='fa fa-times'></i>", "danger");
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Playerfax | Login</title>
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
<section class="login_section">
    <div class="container">
        <div class="login_content">
            <div class="men_img">
                <div class="home_logo">
                    <a href="/">
                        <img src="images/playerlogo_img.png" class="img-responsive" alt="logo"/>
                    </a>
                </div>
                <!--<img src="images/men_img.png" class="img-responsive imgmen" alt="men_img"/>-->
            </div>
            <div class="login_contain">
                <h1>Login</h1>
                <?php if ($msgBox) {
                    echo $msgBox;
                } ?>
                <form action="index.php?page=login" method="post">
                    <input name="userEmail" type="email" placeholder="Email" required class="input_box" value="<?php echo $userEmail; ?>"/>
                    <input name="userPassword" type="password" placeholder="Password" required class="input_box"/>
                    <div class="checkbox remember_text">
                        <input type="checkbox" id="checkbox1"/>
                        <label>Remember me</label>
                    </div>
                    <button type="submit" name="submit" value="login" class="btn btn_blue pull-right"><i class="fa fa-check-square-o"></i> Log in</button>
                </form>
                <p><a href="#" data-toggle="modal" data-target="#myModal">Get help</a> accessing your account.</p>
                <p><a href="index.php?page=registration">Sign up</a> for an account.</p>
            </div>
        </div>
        <div class="modal fade forget_section" id="myModal" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="container">
                    <div class="modal-content">
                        <div class="modal-header">
                            <div class="close_img">
                                <button type="button" class="close" data-dismiss="modal"></button>
                            </div>
                            <div class="title_h2">
                                <h2>forget password</h2>
                            </div>
                        </div>
                        <div class="modal-body">
                            <form action="index.php?page=login" method="post">
                                <label for="userEmail">Email Address</label>
                                <input id="userEmail" name="userEmail" type="email" required class="input_box"/>
                                <p>The email address associated with your account.</p>
                                <button type="reset" name="cancel" value="Cancel" class="btn btn_grey pull-right" data-dismiss="modal"><i class="fa fa-check-square-o"></i> Cancel</button>
                                <button type="submit" name="submit" value="reset" class="btn btn_blue pull-right"><i class="fa fa-check-square-o"></i> Send</button>
                            </form>
                        </div>
                    </div>
                </div>
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
