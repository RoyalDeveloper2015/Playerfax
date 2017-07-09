<?php

$msgBox = '';

$resetToken = isset($_GET['token']) ? trim($_GET['token']) : '';
$resetToken = filter_var($resetToken, FILTER_SANITIZE_STRING);
$resetError = false;
$userId = null;

if (isset($_POST['submit']) && $_POST['submit'] == 'reset') {

    $resetToken = isset($_POST['token']) ? trim($_POST['token']) : '';
    $resetToken = filter_var($resetToken, FILTER_SANITIZE_STRING);

    $userPassword = isset($_POST['userPassword']) ? trim($_POST['userPassword']) : '';

    if (!empty($userPassword) && !empty($resetToken)) {

        $count = 0;

        try {
            $sql = "
            SELECT 
              `ResetPassword`.`UserId`,
              `Users`.`Email`,
              `Users`.`FirstName`, 
              `Users`.`LastName`
            FROM 
              `ResetPassword` 
            LEFT JOIN `Users`
              ON `ResetPassword`.`UserId` = `Users`.`UserId`
            WHERE
              `ResetPassword`.`Token` = :Token
            AND 
              `Users`.`IsActive` = 1";

            $stmt = $PDO->prepare($sql);
            $stmt->bindParam('Token', $resetToken, PDO::PARAM_STR);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $count++;
                $userId = $row['UserId'];
                $userEmail = $row['Email'];
                $userFullName = trim($row['FirstName'] . ' ' . $row['LastName']);
            }

        } catch (PDOException $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
        }//end try

        if ($resetError == false) {

            if ($count > 0) {

                $options = [
                    'cost' => 12,
                ];

                $userPasswordHash = password_hash($userPassword, PASSWORD_DEFAULT, $options);
                $newUserToken = sha1($userIp . microseconds());

                try {
                    $sql = "
                    UPDATE 
                      `Users` 
                    SET
                      `Users`.`Password` = :Password,
                      `Users`.`Token` = :Token
                    WHERE
                      `Users`.`UserId` = :UserId
                    AND 
                      `Users`.`IsActive` = 1";

                    $stmt = $PDO->prepare($sql);
                    $stmt->bindParam('Password', $userPasswordHash, PDO::PARAM_STR);
                    $stmt->bindParam('Token', $newUserToken, PDO::PARAM_STR);
                    $stmt->bindParam('UserId', $userId, PDO::PARAM_INT);
                    $stmt->execute();

                } catch (PDOException $e) {
                    trigger_error($e->getMessage(), E_USER_ERROR);
                }//end try

                try {
                    $sql = "
                        DELETE FROM `ResetPassword` 
                        WHERE
                          `ResetPassword`.`UserId` = :UserId
                        AND
                          `ResetPassword`.`Token` = :Token";

                    $stmt = $PDO->prepare($sql);
                    $stmt->bindParam('UserId', $userId, PDO::PARAM_INT);
                    $stmt->bindParam('Token', $resetToken, PDO::PARAM_STR);
                    $stmt->execute();

                } catch (PDOException $e) {
                    trigger_error($e->getMessage(), E_USER_ERROR);
                }//end try

                $msgBox = alertBox("Password reset successful", "<i class='fa fa-check-square-o'></i>", "success");

            } else {
                // Invalid Reset Link - redirect to login page
                header('Location: index.php?page=login');
                exit;
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
    <title>Playerfax | Reset Password</title>
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
<section class="login_section">
    <div class="container">
        <div class="login_content">
            <div class="men_img">
                <div class="home_logo">
                    <a href="/">
                        <img src="images/playerlogo_img.png" class="img-responsive" alt="logo"/>
                    </a>
                </div>
                <img src="images/men_img.png" class="img-responsive imgmen" alt="men_img"/>
            </div>
            <div class="login_contain">
                <h1>Reset Password</h1>
                <?php if ($msgBox) {
                    echo $msgBox;
                } ?>
                <form action="index.php?action=resetPassword" method="post">
                    <input name="userPassword" type="password" placeholder="New Password" required class="input_box"/>
                    <input name="token" type="hidden" value="<?php echo $resetToken; ?>"/>
                    <button type="submit" name="submit" value="reset" class="btn btn_blue pull-right"><i class="fa fa-check-square-o"></i> Submit</button>
                </form>
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

