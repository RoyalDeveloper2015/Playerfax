<?php

$msgBox = '';
$registerSuccess = false;
$registerError = false;

$userGender = '';
$userFullName = '';
$userFirstName = '';
$userLastName = '';
$userEmail = '';
$userId = null;

if (isset($_POST['submit']) && $_POST['submit'] == 'register') {
    
    $userGender = isset($_POST['userGender']) ? trim($_POST['userGender']) : 'male';
    $userGender = filter_var($userGender, FILTER_SANITIZE_STRING);

    if ($userGender == 'male') {
        // male
        $userGender = '0';
    } else {
        // female
        $userGender = '1';
    }

    $userFirstName = isset($_POST['userFirstName']) ? trim($_POST['userFirstName']) : '';
    $userFirstName = filter_var($userFirstName, FILTER_SANITIZE_STRING);
    $userFirstName = ucfirst(strtolower($userFirstName));

    $userLastName = isset($_POST['userLastName']) ? trim($_POST['userLastName']) : '';
    $userLastName = filter_var($userLastName, FILTER_SANITIZE_STRING);
    $userLastName = ucfirst(strtolower($userLastName));

    $userFullName = trim($userFirstName . ' ' . $userLastName);

    $userEmail = isset($_POST['userEmail']) ? trim($_POST['userEmail']) : '';
    $userEmail = filter_var($userEmail, FILTER_SANITIZE_EMAIL);
    $userEmail = strtolower($userEmail);

    $userPassword = isset($_POST['userPassword']) ? trim($_POST['userPassword']) : '';

    $gRecaptchaResponse = isset($_POST['g-recaptcha-response']) ? trim($_POST['g-recaptcha-response']) : '';

    $options = [
        'cost' => 12,
    ];

    $userPasswordHash = password_hash($userPassword, PASSWORD_DEFAULT, $options);

    $userConfirmPassword = isset($_POST['userConfirmPassword']) ? trim($_POST['userConfirmPassword']) : '';


    if (!empty($userFirstName) && !empty($userLastName) && !empty($userEmail) && !empty($userPassword) && !empty($userConfirmPassword)) {

        if (empty($gRecaptchaResponse)) {
            $registerError = true;
            $msgBox = alertBox("Google Recaptcha required.", "<i class='fa fa-times'></i>", "danger");
        }

        if ($userPassword !== $userConfirmPassword) {
            $registerError = true;
            $msgBox = alertBox("Passwords do not match.", "<i class='fa fa-times'></i>", "danger");
        }

        if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
            $registerError = true;
            $msgBox = alertBox("Enter a valid email address", "<i class='fa fa-times'></i>", "danger");
        }

        // Google Recaptcha API
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => 'https://www.google.com/recaptcha/api/siteverify',
            CURLOPT_POST => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POSTFIELDS => array(
                'secret' => '6LfEmxcUAAAAAEdqpJS_rAtnomwvqccPwrz1SOcA',
                'response' => $gRecaptchaResponse,
                'remoteip' => $_SERVER["REMOTE_ADDR"]
            )
        ));
        $googleApiResponse = curl_exec($curl);
        curl_close($curl);
        if (strpos($googleApiResponse, '"success": true') === FALSE) {
            $registerError = true;
            $msgBox = alertBox("Failed Google Recaptcha", "<i class='fa fa-times'></i>", "danger");
        }

        $userCreated = date('Y-m-d H:i:s');
        $userLastUpdated = date('Y-m-d H:i:s');
        $userToken = sha1($userIp . microseconds());
        $userIsActive = '0'; // not able to login until account is confirmed

        $count = 0;

        try {
            $sql = "
            SELECT `UserId` FROM `Users` 
            WHERE
              `Email` = :Email
          ";

            $stmt = $PDO->prepare($sql);
            $stmt->bindParam('Email', $userEmail, PDO::PARAM_STR);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $count++;
            }

        } catch (PDOException $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
        }//end try

        if ($count > 0) {
            $registerError = true;
            $msgBox = alertBox("This email is already registered", "<i class='fa fa-times'></i>", "danger");
        }

        if ($registerError == false) {

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
                  `IsActive`
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
                  :IsActive
                )";

                $stmt = $PDO->prepare($sql);
                $stmt->bindParam('Created', $userCreated, PDO::PARAM_STR);
                $stmt->bindParam('LastUpdated', $userLastUpdated, PDO::PARAM_STR);
                $stmt->bindParam('Gender', $userGender, PDO::PARAM_STR);
                $stmt->bindParam('FirstName', $userFirstName, PDO::PARAM_STR);
                $stmt->bindParam('LastName', $userLastName, PDO::PARAM_STR);
                $stmt->bindParam('Email', $userEmail, PDO::PARAM_STR);
                $stmt->bindParam('Password', $userPasswordHash, PDO::PARAM_STR);
                $stmt->bindParam('Token', $userToken, PDO::PARAM_STR);
                $stmt->bindParam('IsActive', $userIsActive, PDO::PARAM_INT);
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
            $mail->AddAddress('support@playerfax.com', 'Playerfax');  // Send to admin
            $mail->AddAddress('jay@businessreportcard.com', 'Jay');  // Send to admin
            $mail->Subject = 'Playerfax Registration';

            // HTML version
            $htmlBody = file_get_contents('includes/email/email.userRegistration.html');
            $htmlBody = str_replace('%NAME%', $userFullName, $htmlBody);
            $htmlBody = str_replace('%EMAIL%', $userEmail, $htmlBody);
            $htmlBody = str_replace('%PASSWORD%', $userPassword, $htmlBody);
            $htmlBody = str_replace('%CONFIRM_TOKEN%', $userToken, $htmlBody);

            $mail->Body = $htmlBody;

            // plain text version
            $plainBody = file_get_contents('includes/email/email.userRegistration.txt');
            $plainBody = str_replace('%NAME%', $userFullName, $plainBody);
            $plainBody = str_replace('%EMAIL%', $userEmail, $plainBody);
            $plainBody = str_replace('%PASSWORD%', $userPassword, $plainBody);
            $plainBody = str_replace('%CONFIRM_TOKEN%', $userToken, $plainBody);

            $mail->AltBody = $plainBody;

            $mail->send();

            $registerSuccess = true;

            //$msgBox = alertBox("Success! Email confirmation was sent", "<i class='fa fa-check-square-o'></i>", "success");

            /*
            if (!$mail->send()) {
                $messageBox = 'Unable to deliver the message ' . $mail->ErrorInfo;
            }
            */
        }

    } else {
        $msgBox = alertBox("All fields are required", "<i class='fa fa-times'></i>", "danger");
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Playerfax | Registration</title>
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
    <script src='https://www.google.com/recaptcha/api.js'></script>
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
            <?php if ($registerSuccess === false) : ?>
                <div class="login_contain">
                    <h1>Registration</h1>
                    <?php if ($msgBox) {
                        echo $msgBox;
                    } ?>
                    <form action="index.php?page=registration" method="post">
                        <input name="userFirstName" type="text" placeholder="First Name" required class="input_box" value="<?php echo $userFirstName; ?>"/>
                        <input name="userLastName" type="text" placeholder="Last Name" required class="input_box" value="<?php echo $userLastName; ?>"/>
                        <input name="userEmail" type="email" placeholder="Email" required class="input_box" value="<?php echo $userEmail; ?>"/>
                        <input name="userPassword" type="password" placeholder="Password" required class="input_box"/>
                        <input name="userConfirmPassword" type="password" placeholder="Confirm Password" required class="input_box"/>
                        <div class="g-recaptcha" data-sitekey="6LfEmxcUAAAAAF8hbeapx5Y1Z39StSiw1rKHR1Ar"></div>
                        <button type="submit" name="submit" value="register" class="btn btn_blue pull-right" style="margin-top:10px;"><i class="fa fa-check-square-o"></i> Submit</button>
                    </form>
                </div>
            <?php endif; ?>
            <?php if ($registerSuccess === true) : ?>
                <div class="login_contain">
                    <h1>Registration</h1>
                    <h5>Your registration was received.</h5>
                    <h5>Check your email for a confirmation link.</h5>
                </div>
            <?php endif; ?>
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
<script>
    $('document').ready(function () {

    });
</script>
</body>
</html>
