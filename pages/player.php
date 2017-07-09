<?php

// This is the logged-in version of viewing a player

if (!isset($_SESSION['userId'])) {
    header('Location: index.php?page=login');
    exit;
}
$playerToken = isset($_GET['token']) ? trim($_GET['token']) : '';
$playerToken = filter_var($playerToken, FILTER_SANITIZE_STRING);

// Player data
$count = 0;
$playerId = null;
$adminId = null;
$playerCreated = '';
$playerLastUpdated = '';
$playerPicture = '';
$playerGender = '';
$playerFirstName = '';
$playerMiddleName = '';
$playerLastName = '';
$playerFullName = '';
$playerDOB = '';
$playerDesignation = '';
$playerGradYear = '';

try {
    $sql = "
    SELECT
      `Players`.`PlayerId`,
      `Players`.`UserId`,
      `Players`.`Created` AS `PlayerCreated`,
      `Players`.`LastUpdated` AS `PlayerLastUpdated`,
      `Players`.`Picture` AS `PlayerPicture`,
      `Players`.`Gender` AS `PlayerGender`,
      `Players`.`FirstName` AS `PlayerFirstName`,
      `Players`.`MiddleName` AS `PlayerMiddleName`,
      `Players`.`LastName` AS `PlayerLastName`,
      `Players`.`DOB` AS `PlayerDOB`,
      `Players`.`Designation` AS `PlayerDesignation`,
      `Players`.`GradYear` AS `PlayerGradYear`,
      `Players`.`Token` AS `PlayerToken`
    FROM 
      `Players` 
    WHERE
      `Players`.`Token` = :Token
    AND 
      `Players`.`IsActive` = 1";

    $stmt = $PDO->prepare($sql);
    $stmt->bindParam('Token', $playerToken, PDO::PARAM_STR);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $count++;
        $playerId = $row['PlayerId'];
        $adminId = $row['UserId'];
        $playerCreated = $row['PlayerCreated'];
        $playerLastUpdated = $row['PlayerLastUpdated'];
        $playerGender = $row['PlayerGender'];
        $playerFirstName = $row['PlayerFirstName'];
        $playerMiddleName = $row['PlayerMiddleName'];
        $playerLastName = $row['PlayerLastName'];
        $playerFullName = trim($row['PlayerFirstName'] . ' ' . $row['PlayerMiddleName'] . ' ' . $row['PlayerLastName']);
        $playerFullName = preg_replace('/\s+/', ' ', $playerFullName);
        $playerDOB = $row['PlayerDOB'];
        $playerDesignation = $row['PlayerDesignation'];
        $playerGradYear = $row['PlayerGradYear'];
        $playerToken = $row['PlayerToken'];

        if (!empty($row['PlayerPicture']) && file_exists(constant('UPLOADS_PLAYERS') . $row['PlayerPicture'])) {
            $playerPicture = constant('URL_UPLOADS_PLAYERS') . $row['PlayerPicture'];
        } else {
            if ($playerGender == 0) {
                $playerPicture = constant('URL_UPLOADS_PLAYERS') . 'default-male.png';
            } else {
                $playerPicture = constant('URL_UPLOADS_PLAYERS') . 'default-female.png';
            }
        }
    }

} catch (PDOException $e) {
    trigger_error($e->getMessage(), E_USER_ERROR);
}//end try

if ($count == 0) {
    // redirect to player not found page instead
    header('Location: index.php?page=404');
    exit;
}

$isAdmin = false;

try {
    $sql = "
    SELECT
      `PlayerAdmins`.`PlayerAdminId`
    FROM 
      `PlayerAdmins` 
    WHERE
      `PlayerAdmins`.`UserIdFrom` = :UserIdFrom
    AND 
      `PlayerAdmins`.`PlayerId` = :PlayerId
    AND 
      `PlayerAdmins`.`IsAdmin` = 1";

    $stmt = $PDO->prepare($sql);
    $stmt->bindParam('UserIdFrom', $userId, PDO::PARAM_INT);
    $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $isAdmin = true;
    }

} catch (PDOException $e) {
    trigger_error($e->getMessage(), E_USER_ERROR);
}//end try

$isFriend = false;

try {
    $sql = "
    SELECT
      `Follows`.`FollowId`
    FROM 
      `Follows` 
    WHERE
      `Follows`.`UserIdFrom` = :UserIdFrom
    AND 
      `Follows`.`PlayerId` = :PlayerId";

    $stmt = $PDO->prepare($sql);
    $stmt->bindParam('UserIdFrom', $userId, PDO::PARAM_INT);
    $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $isFriend = true;
    }

} catch (PDOException $e) {
    trigger_error($e->getMessage(), E_USER_ERROR);
}//end try

$allCertified = 0;
$allUnCertified = 0;
$sportsTabButtons = array();
$sportsTabs = '';

// Baseball
$baseballCount = 0;
$baseballCertified = 0;
$baseballUnCertified = 0;
$baseballAsOfDate = '';
try {
    $sql = "
    SELECT
      `GameBaseball`.*
    FROM 
      `GameBaseball` 
    WHERE
      `GameBaseball`.`UserId` = :UserId
    AND 
      `GameBaseball`.`PlayerId` = :PlayerId";

    $stmt = $PDO->prepare($sql);
    $stmt->bindParam('UserId', $userId, PDO::PARAM_INT);
    $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        if ($baseballCount == 0) {
            $baseballAsOfDate = date('m/d/Y', strtotime($row['Created']));
        }
        if ($row['Certified'] == 0) {
            $baseballUnCertified++;
            $allUnCertified++;
        } else {
            $baseballCertified++;
            $allCertified++;
        }

        $baseball_VelocityMound = $row['VelocityMound']; // MPH
        $baseball_VelocityOutfield = $row['VelocityOutfield']; // MPH
        $baseball_VelocityInfield = $row['VelocityInfield']; // MPH
        $baseball_SwingVelocity = $row['SwingVelocity']; // MPH
        $baseball_60YardDash = secondsToHMSU($row['60YardDash']); // Time
        $baseball_CatcherPop = secondsToHMSU($row['CatcherPop']); // Time
        $baseball_CatcherRelease = secondsToHMSU($row['CatcherRelease']); // Time
        $baseball_PrimaryPosition = getBaseballFieldPositionId($row['PrimaryPosition']); // Position Number
        $baseball_SecondaryPosition = getBaseballFieldPositionId($row['SecondaryPosition']); // Position Number
        $baseball_TeeVelocity = $row['TeeVelocity']; // MPH

        $baseballCount++;
    }

} catch (PDOException $e) {
    trigger_error($e->getMessage(), E_USER_ERROR);
}//end try

if ($baseballCount > 0) {

    array_push($sportsTabButtons, '<li><a data-toggle="tab" class="" href="#tabBaseball">Baseball</a></li>');

    // total stats for baseball
    $sportsTabs .= '<div id="tabBaseball" class="tab-pane fade">
        <div class="content_full">
            <div class="graphics_charts">
                <div class="single_graph_small">
                    <div class="graph">
                        <img src="images/143img.png" alt="image"/>
                    </div>
                    <div class="graph_title">
                        <p>PlayerFax Score</p>
                    </div>
                </div>
                <div class="single_graph_small">
                    <div class="graph">
                        <img src="images/64img.png" alt="image"/>
                    </div>
                    <div class="graph_title">
                        <p>Percentile</p>
                    </div>
                </div>
                <div class="single_graph_small">
                    <div class="graph">
                        <img src="images/3img.png" alt="image"/>
                    </div>
                    <div class="graph_title">
                        <p>Sports Tracked</p>
                    </div>
                </div>
                <div class="as_date">
                    <p>As of Date: ' . $baseballAsOfDate . '</p>
                </div>
                <div class="big_chart">
                    <img src="images/graph_img.png" alt="image"/>
                </div>
                <div class="big_chart_desc">
                    <ul>
                        <li><span>Top Sport:</span>Baseball</li>
                        <li><span>Trending Sport:</span>Baseball</li>
                        <li><span>Top Skill:</span>Fastball</li>
                        <li><span>Trending Skill:</span>Fastball</li>
                    </ul>
                </div>
            </div>
            <div class="graphics_details">
                <div class="single_detail">
                    <label>Certified Stats vs Community Stats</label>
                    <span>' . $baseballCertified . '/' . $baseballUnCertified . '</span>
                </div>
                <div class="single_detail">
                    <label>Fans</label>
                    <span>24</span>
                </div>
                <div class="single_detail">
                    <label>Followers</label>
                    <span>0</span>
                </div>
                <div class="single_detail">
                    <label>Achievements</label>
                    <span>2</span>
                </div>
                <div class="single_detail dark">
                    <span><label>Latest Personal Best</label>30 Days</span>
                </div>
                <div class="single_detail">
                    <label>Graduation Year</label>
                    <span>' . $playerGradYear . '</span>
                </div>
            </div>
        </div>
    </div>';
}

// Fast Pitch
$fastPitchCount = 0;
$fastPitchCertified = 0;
$fastPitchUnCertified = 0;
$fastPitchAsOfDate = '';
try {
    $sql = "
    SELECT
      `GameFastPitch`.*
    FROM 
      `GameFastPitch` 
    WHERE
      `GameFastPitch`.`UserId` = :UserId
    AND 
      `GameFastPitch`.`PlayerId` = :PlayerId";

    $stmt = $PDO->prepare($sql);
    $stmt->bindParam('UserId', $userId, PDO::PARAM_INT);
    $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        if ($fastPitchCount == 0) {
            $fastPitchAsOfDate = date('m/d/Y', strtotime($row['Created']));
        }
        if ($row['Certified'] == 0) {
            $fastPitchUnCertified++;
            $allUnCertified++;
        } else {
            $fastPitchCertified++;
            $allCertified++;
        }

        $fastPitch_VelocityMound = $row['VelocityMound']; // MPH
        $fastPitch_VelocityOutfield = $row['VelocityOutfield']; // MPH
        $fastPitch_VelocityInfield = $row['VelocityInfield']; // MPH
        $fastPitch_SwingVelocity = $row['SwingVelocity']; // MPH
        $fastPitch_60YardDash = secondsToHMSU($row['60YardDash']); // Time
        $fastPitch_CatcherPop = secondsToHMSU($row['CatcherPop']); // Time
        $fastPitch_CatcherRelease = secondsToHMSU($row['CatcherRelease']); // Time
        $fastPitch_PrimaryPosition = getBaseballFieldPositionId($row['PrimaryPosition']); // Position Number
        $fastPitch_SecondaryPosition = getBaseballFieldPositionId($row['SecondaryPosition']); // Position Number
        $fastPitch_TeeVelocity = $row['TeeVelocity']; // MPH

        $fastPitchCount++;
    }

} catch (PDOException $e) {
    trigger_error($e->getMessage(), E_USER_ERROR);
}//end try

if ($fastPitchCount > 0) {

    array_push($sportsTabButtons, '<li><a data-toggle="tab" class="" href="#tabFastPitch">Fast Pitch</a></li>');

    // total stats for Fast Pitch
    $sportsTabs .= '<div id="tabFastPitch" class="tab-pane fade">
        <div class="content_full">
            <div class="graphics_charts">
                <div class="single_graph_small">
                    <div class="graph">
                        <img src="images/143img.png" alt="image"/>
                    </div>
                    <div class="graph_title">
                        <p>PlayerFax Score</p>
                    </div>
                </div>
                <div class="single_graph_small">
                    <div class="graph">
                        <img src="images/64img.png" alt="image"/>
                    </div>
                    <div class="graph_title">
                        <p>Percentile</p>
                    </div>
                </div>
                <div class="single_graph_small">
                    <div class="graph">
                        <img src="images/3img.png" alt="image"/>
                    </div>
                    <div class="graph_title">
                        <p>Sports Tracked</p>
                    </div>
                </div>
                <div class="as_date">
                    <p>As of Date: ' . $fastPitchAsOfDate . '</p>
                </div>
                <div class="big_chart">
                    <img src="images/graph_img.png" alt="image"/>
                </div>
                <div class="big_chart_desc">
                    <ul>
                        <li><span>Top Sport:</span>-</li>
                        <li><span>Trending Sport:</span>-</li>
                        <li><span>Top Skill:</span>-</li>
                        <li><span>Trending Skill:</span>-</li>
                    </ul>
                </div>
            </div>
            <div class="graphics_details">
                <div class="single_detail">
                    <label>Certified Stats vs Community Stats</label>
                    <span>' . $fastPitchCertified . '/' . $fastPitchUnCertified . '</span>
                </div>
                <div class="single_detail">
                    <label>Fans</label>
                    <span>0</span>
                </div>
                <div class="single_detail">
                    <label>Followers</label>
                    <span>0</span>
                </div>
                <div class="single_detail">
                    <label>Achievements</label>
                    <span>0</span>
                </div>
                <div class="single_detail dark">
                    <span><label>Latest Personal Best</label>30 Days</span>
                </div>
                <div class="single_detail">
                    <label>Graduation Year</label>
                    <span>' . $playerGradYear . '</span>
                </div>
            </div>
        </div>
    </div>';
}


// Lacrosse
$lacrosseCount = 0;
$lacrosseCertified = 0;
$lacrosseUnCertified = 0;
$lacrosseAsOfDate = '';
try {
    $sql = "
    SELECT
      `GameLacrosse`.*
    FROM 
      `GameLacrosse` 
    WHERE
      `GameLacrosse`.`UserId` = :UserId
    AND 
      `GameLacrosse`.`PlayerId` = :PlayerId";

    $stmt = $PDO->prepare($sql);
    $stmt->bindParam('UserId', $userId, PDO::PARAM_INT);
    $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        if ($lacrosseCount == 0) {
            $lacrosseAsOfDate = date('m/d/Y', strtotime($row['Created']));
        }
        if ($row['Certified'] == 0) {
            $lacrosseUnCertified++;
            $allUnCertified++;
        } else {
            $lacrosseCertified++;
            $allCertified++;
        }

        $lacrosse_60YardDash = secondsToHMSU($row['60YardDash']); // Time
        $lacrosse_5ConeFootwork = secondsToHMSU($row['5ConeFootwork']); // Time
        $lacrosse_ShuttleRun = secondsToHMSU($row['ShuttleRun']); // Time
        $lacrosse_Rebounder10 = $row['Rebounder10']; // Count
        $lacrosse_GoalShot10 = $row['GoalShot10']; // Count
        $lacrosse_Accuracy50 = $row['Accuracy50']; // Count
        $lacrosse_VelocityThrow = $row['VelocityThrow']; // MPH

        $lacrosseCount++;
    }

} catch (PDOException $e) {
    trigger_error($e->getMessage(), E_USER_ERROR);
}//end try

if ($lacrosseCount > 0) {

    array_push($sportsTabButtons, '<li><a data-toggle="tab" class="" href="#tabLacrosse">Lacrosse</a></li>');

    // total stats for Fast Pitch
    $sportsTabs .= '<div id="tabLacrosse" class="tab-pane fade">
        <div class="content_full">
            <div class="graphics_charts">
                <div class="single_graph_small">
                    <div class="graph">
                        <img src="images/143img.png" alt="image"/>
                    </div>
                    <div class="graph_title">
                        <p>PlayerFax Score</p>
                    </div>
                </div>
                <div class="single_graph_small">
                    <div class="graph">
                        <img src="images/64img.png" alt="image"/>
                    </div>
                    <div class="graph_title">
                        <p>Percentile</p>
                    </div>
                </div>
                <div class="single_graph_small">
                    <div class="graph">
                        <img src="images/3img.png" alt="image"/>
                    </div>
                    <div class="graph_title">
                        <p>Sports Tracked</p>
                    </div>
                </div>
                <div class="as_date">
                    <p>As of Date: ' . $lacrosseAsOfDate . '</p>
                </div>
                <div class="big_chart">
                    <img src="images/graph_img.png" alt="image"/>
                </div>
                <div class="big_chart_desc">
                    <ul>
                        <li><span>Top Sport:</span>-</li>
                        <li><span>Trending Sport:</span>-</li>
                        <li><span>Top Skill:</span>-</li>
                        <li><span>Trending Skill:</span>-</li>
                    </ul>
                </div>
            </div>
            <div class="graphics_details">
                <div class="single_detail">
                    <label>Certified Stats vs Community Stats</label>
                    <span>' . $lacrosseCertified . '/' . $lacrosseUnCertified . '</span>
                </div>
                <div class="single_detail">
                    <label>Fans</label>
                    <span>0</span>
                </div>
                <div class="single_detail">
                    <label>Followers</label>
                    <span>0</span>
                </div>
                <div class="single_detail">
                    <label>Achievements</label>
                    <span>0</span>
                </div>
                <div class="single_detail dark">
                    <span><label>Latest Personal Best</label>30 Days</span>
                </div>
                <div class="single_detail">
                    <label>Graduation Year</label>
                    <span>' . $playerGradYear . '</span>
                </div>
            </div>
        </div>
    </div>';
}


// Cross Fit
$crossFitCount = 0;
$crossFitCertified = 0;
$crossFitUnCertified = 0;
$crossFitAsOfDate = '';
try {
    $sql = "
    SELECT
      `GameCrossFit`.*
    FROM 
      `GameCrossFit` 
    WHERE
      `GameCrossFit`.`UserId` = :UserId
    AND 
      `GameCrossFit`.`PlayerId` = :PlayerId";

    $stmt = $PDO->prepare($sql);
    $stmt->bindParam('UserId', $userId, PDO::PARAM_INT);
    $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        if ($crossFitCount == 0) {
            $crossFitAsOfDate = date('m/d/Y', strtotime($row['Created']));
        }
        if ($row['Certified'] == 0) {
            $crossFitUnCertified++;
            $allUnCertified++;
        } else {
            $crossFitCertified++;
            $allCertified++;
        }

        $crossFit_ShuttleRun = secondsToHMSU($row['ShuttleRun']); // Time
        $crossFit_40YardDash = secondsToHMSU($row['40YardDash']); // Time
        $crossFit_5105ConeDrill = secondsToHMSU($row['5105ConeDrill']); // Time
        $crossFit_3RMHang = $row['3RMHang']; // Count
        $crossFit_VerticalJump = $row['VerticalJump']; // Inch
        $crossFit_BroadJump = $row['BroadJump']; // Inch
        $crossFit_PowerClean = $row['PowerClean']; // Count
        $crossFit_PullUps = $row['PullUps']; // Count
        $crossFit_PushUps = $row['PushUps']; // Count

        $crossFitCount++;
    }

} catch (PDOException $e) {
    trigger_error($e->getMessage(), E_USER_ERROR);
}//end try

if ($crossFitCount > 0) {

    array_push($sportsTabButtons, '<li><a data-toggle="tab" class="" href="#tabCrossFit">Cross Fit</a></li>');

    // total stats for Fast Pitch
    $sportsTabs .= '<div id="tabCrossFit" class="tab-pane fade">
        <div class="content_full">
            <div class="graphics_charts">
                <div class="single_graph_small">
                    <div class="graph">
                        <img src="images/143img.png" alt="image"/>
                    </div>
                    <div class="graph_title">
                        <p>PlayerFax Score</p>
                    </div>
                </div>
                <div class="single_graph_small">
                    <div class="graph">
                        <img src="images/64img.png" alt="image"/>
                    </div>
                    <div class="graph_title">
                        <p>Percentile</p>
                    </div>
                </div>
                <div class="single_graph_small">
                    <div class="graph">
                        <img src="images/3img.png" alt="image"/>
                    </div>
                    <div class="graph_title">
                        <p>Sports Tracked</p>
                    </div>
                </div>
                <div class="as_date">
                    <p>As of Date: ' . $crossFitAsOfDate . '</p>
                </div>
                <div class="big_chart">
                    <img src="images/graph_img.png" alt="image"/>
                </div>
                <div class="big_chart_desc">
                    <ul>
                        <li><span>Top Sport:</span>-</li>
                        <li><span>Trending Sport:</span>-</li>
                        <li><span>Top Skill:</span>-</li>
                        <li><span>Trending Skill:</span>-</li>
                    </ul>
                </div>
            </div>
            <div class="graphics_details">
                <div class="single_detail">
                    <label>Certified Stats vs Community Stats</label>
                    <span>' . $crossFitCertified . '/' . $crossFitUnCertified . '</span>
                </div>
                <div class="single_detail">
                    <label>Fans</label>
                    <span>0</span>
                </div>
                <div class="single_detail">
                    <label>Followers</label>
                    <span>0</span>
                </div>
                <div class="single_detail">
                    <label>Achievements</label>
                    <span>0</span>
                </div>
                <div class="single_detail dark">
                    <span><label>Latest Personal Best</label>30 Days</span>
                </div>
                <div class="single_detail">
                    <label>Graduation Year</label>
                    <span>' . $playerGradYear . '</span>
                </div>
            </div>
        </div>
    </div>';
}

// Swimming
$swimmingCount = 0;
$swimmingCertified = 0;
$swimmingUnCertified = 0;
$swimmingAsOfDate = '';
try {
    $sql = "
    SELECT
      `GameSwimming`.*
    FROM 
      `GameSwimming` 
    WHERE
      `GameSwimming`.`UserId` = :UserId
    AND 
      `GameSwimming`.`PlayerId` = :PlayerId";

    $stmt = $PDO->prepare($sql);
    $stmt->bindParam('UserId', $userId, PDO::PARAM_INT);
    $stmt->bindParam('PlayerId', $playerId, PDO::PARAM_INT);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        if ($swimmingCount == 0) {
            $swimmingAsOfDate = date('m/d/Y', strtotime($row['Created']));
        }
        if ($row['Certified'] == 0) {
            $swimmingUnCertified++;
            $allUnCertified++;
        } else {
            $swimmingCertified++;
            $allCertified++;
        }

        $swimming_25MFreestyle = secondsToHMSU($row['25MFreestyle']); // Time
        $swimming_25MBackStroke = secondsToHMSU($row['25MBackStroke']); // Time
        $swimming_25MBreastStroke = secondsToHMSU($row['25MBreastStroke']); // Time
        $swimming_25MButterfly = secondsToHMSU($row['25MButterfly']); // Time
        $swimming_50MFreestyle = secondsToHMSU($row['50MFreestyle']); // Time
        $swimming_50MBackStroke = secondsToHMSU($row['50MBackStroke']); // Time
        $swimming_50MBreastStroke = secondsToHMSU($row['50MBreastStroke']); // Time
        $swimming_50MButterfly = secondsToHMSU($row['50MButterfly']); // Time
        $swimming_100MFreestyle = secondsToHMSU($row['100MFreestyle']); // Time
        $swimming_100MBackStroke = secondsToHMSU($row['100MBackStroke']); // Time
        $swimming_100MBreastStroke = secondsToHMSU($row['100MBreastStroke']); // Time
        $swimming_100MButterfly = secondsToHMSU($row['100MButterfly']); // Time
        $swimming_100MIndividualMedley = secondsToHMSU($row['100MIndividualMedley']); // Time
        $swimming_200MIndividualMedley = secondsToHMSU($row['200MIndividualMedley']); // Time

        $swimmingCount++;
    }

} catch (PDOException $e) {
    trigger_error($e->getMessage(), E_USER_ERROR);
}//end try

if ($swimmingCount > 0) {

    array_push($sportsTabButtons, '<li><a data-toggle="tab" class="" href="#tabSwimming">Swimming</a></li>');

    // total stats for Fast Pitch
    $sportsTabs .= '<div id="tabSwimming" class="tab-pane fade">
        <div class="content_full">
            <div class="graphics_charts">
                <div class="single_graph_small">
                    <div class="graph">
                        <img src="images/143img.png" alt="image"/>
                    </div>
                    <div class="graph_title">
                        <p>PlayerFax Score</p>
                    </div>
                </div>
                <div class="single_graph_small">
                    <div class="graph">
                        <img src="images/64img.png" alt="image"/>
                    </div>
                    <div class="graph_title">
                        <p>Percentile</p>
                    </div>
                </div>
                <div class="single_graph_small">
                    <div class="graph">
                        <img src="images/3img.png" alt="image"/>
                    </div>
                    <div class="graph_title">
                        <p>Sports Tracked</p>
                    </div>
                </div>
                <div class="as_date">
                    <p>As of Date: ' . $swimmingAsOfDate . '</p>
                </div>
                <div class="big_chart">
                    <img src="images/graph_img.png" alt="image"/>
                </div>
                <div class="big_chart_desc">
                    <ul>
                        <li><span>Top Sport:</span>-</li>
                        <li><span>Trending Sport:</span>-</li>
                        <li><span>Top Skill:</span>-</li>
                        <li><span>Trending Skill:</span>-</li>
                    </ul>
                </div>
            </div>
            <div class="graphics_details">
                <div class="single_detail">
                    <label>Certified Stats vs Community Stats</label>
                    <span>' . $swimmingCertified . '/' . $swimmingUnCertified . '</span>
                </div>
                <div class="single_detail">
                    <label>Fans</label>
                    <span>0</span>
                </div>
                <div class="single_detail">
                    <label>Followers</label>
                    <span>0</span>
                </div>
                <div class="single_detail">
                    <label>Achievements</label>
                    <span>0</span>
                </div>
                <div class="single_detail dark">
                    <span><label>Latest Personal Best</label>30 Days</span>
                </div>
                <div class="single_detail">
                    <label>Graduation Year</label>
                    <span>' . $playerGradYear . '</span>
                </div>
            </div>
        </div>
    </div>';
}


?>
<!DOCTYPE html>
<html>
<head>
    <title>Playerfax | Profile</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1, maximum-scale=1">
    <link rel="apple-touch-icon" sizes="180x180" href="http://www.playerfax.com/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="http://www.playerfax.com/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="http://www.playerfax.com/favicon-16x16.png">
    <link rel="manifest" href="http://www.playerfax.com/manifest.json">
    <link rel="mask-icon" href="http://www.playerfax.com/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="theme-color" content="#123456">
    <link type="image/png" rel="icon" sizes="32x32" href="favicon.png"/>
    <link type="text/css" rel="stylesheet" href="css/bootstrap.min.css"/>
    <link type="text/css" rel="stylesheet" href="css/font-awesome.css"/>
    <link type="text/css" rel="stylesheet" href="css/flexslider.css"/>
    <link type="text/css" rel="stylesheet" href="css/bootstrap-datepicker.css"/>
    <link type="text/css" rel="stylesheet" href="css/bootstrap-datetimepicker.css"/>
    <link type="text/css" rel="stylesheet" href="css/select2.css"/>
    <link type="text/css" rel="stylesheet" href="css/select2-bootstrap.css"/>
    <link type="text/css" rel="stylesheet" href="js/DataTables-1.10.15/media/css/dataTables.bootstrap.min.css"/>
    <link type="text/css" rel="stylesheet" href="js/DataTables-1.10.15/extensions/Buttons/css/buttons.bootstrap.min.css"/>
    <link type="text/css" rel="stylesheet" href="css/dataTables.fontAwesome.css"/>
    <link type="text/css" rel="stylesheet" href="css/cropper-min.css"/>
    <link type="text/css" rel="stylesheet" href="css/style.css"/>
    <link type="text/css" rel="stylesheet" href="css/responsive.css"/>
    <style>
        .upload-progress {
            display: block;
            text-align: center;
            width: 0;
            height: 3px;
            background: red;
            transition: width .3s;
            -webkit-transition: width .3s; /* Safari */
        }

        .upload-progress.hide {
            opacity: 0;
            transition: opacity 1.3s;
        }

        .wordwrap {
            white-space: pre-wrap; /* CSS3 */
            white-space: -moz-pre-wrap; /* Firefox */
            white-space: -pre-wrap; /* Opera <7 */
            white-space: -o-pre-wrap; /* Opera 7 */
            word-wrap: break-word; /* IE */
        }

        .mediaFile {
            float: left;
            width: 325px;
            height: auto;
            border: 1px solid #D2D2D2;
            padding: 10px;
            background-color: #fff;
            margin-bottom: 10px;
            margin-right: 10px;
        }

        .mediaFile p img {
            width: 300px;
        }

        .hidden {
            visibility: hidden;
            opacity: 0;
            -moz-transition: opacity 1s, visibility 1.3s;
            -webkit-transition: opacity 1s, visibility 1.3s;
            -o-transition: opacity 1s, visibility 1.3s;
            transition: opacity 1s, visibility 1.3s;
        }

        .shown {
            visibility: visible;
            opacity: 1;
            -moz-transition: opacity 1s, visibility 1.3s;
            -webkit-transition: opacity 1s, visibility 1.3s;
            -o-transition: opacity 1s, visibility 1.3s;
            transition: opacity 1s, visibility 1.3s;
        }

        .timeline_loader {
            left: 50%;
            margin: 0 auto;
            position: relative;
            top: 20px;
        }
        #player_timeline {
            margin: 0 auto;
            max-width: 517px;
        }
    </style>
</head>
<body class="user_home" id="portrait">
<?php require 'includes/header.php'; ?>
<section class="profile_section">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="profile_contain">
                    <div class="blue_link_member">
                        <a href="#" class="right_radius"><img class="img-responsive" src="images/btn-like.png"></a>
                        <?php if ($isFriend === true): ?>
                            <a href="#" class="left_radius" data-toggle="modal" data-target="#unFriend"><img class="img-responsive" src="images/btn-friends.png"></a>
                        <?php endif; ?>
                        <?php if ($isFriend === false): ?>
                            <a href="#" class="left_radius" data-toggle="modal" data-target="#myModal4"><img class="img-responsive" src="images/btn-add.png"></a>
                        <?php endif; ?>
                        <div class="clearfix" style="height:30px;"></div>
                    </div>

                    <h3><?php echo $playerFullName; ?></h3>
                    <div class="img_preview">
                        <div class="img-circle">
                            <img class="player_img_profile_picture" src="<?php echo $playerPicture; ?>" alt="<?php echo $playerFullName; ?>" data-token="<?php echo $playerToken; ?>" width="300" height="300"/>
                        </div>
                    </div>
                    <div class="text-center">
                        <?php if ($isAdmin === true): ?>
                            <a href="#" data-dismiss="modal" data-toggle="modal" data-target="#player_img_modal">Change Picture</a>
                        <?php endif; ?>
                    </div>
                    <div class="text-center">
                        <?php if ($isAdmin === true): ?>
                            <a class="view_a" href="#" data-toggle="modal" data-target="#myModal7">View Timeline</a>
                            View Timeline Coming Soon
                        <?php endif; ?>
                    </div>
                    <div class="blue_info_contain">
                        <h4><?php echo ($playerGender == 0) ? 'Male' : 'Female'; ?></h4>
                        <div class="member_info">
                            <p><span>DOB:</span> <?php echo (validateDate($playerDOB, 'Y-m-d')) ? date('n/j/Y', strtotime($playerDOB)) : ''; ?></p>
                            <p><span>Profile Since: </span><?php echo (validateDate($playerCreated, 'Y-m-d H:i:s')) ? date('n/j/Y', strtotime($playerCreated)) : ''; ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <ul class="popup_black_btn">
                    <?php if ($isAdmin === true): ?>
                        <li><a href="#" data-toggle="modal" data-target="#myModal6"><span>Add Player Stats</span></a></li>
                    <?php endif; ?>
                    <?php if ($isAdmin === true): ?>
                        <li><a href="#" data-toggle="modal" data-target="#myModal1"><span>Post To Timeline</span></a></li>
                    <?php endif; ?>
                    <li><a href="#" data-toggle="modal" data-target="#myModal3"><span>Message</span></a></li>
                </ul>
                <hr>
                <div class="profile_tab">
                    <div class="profile_tab_menu">
                        <ul class="nav nav-tabs myTab">
                            <li class="active"><a data-toggle="tab" href="#all">Overview All Sports</a></li>
                            <?php
                            $x = 0;
                            $y = count($sportsTabButtons);
                            for ($z = 0; $z < $y; $z++) {
                                if ($z <= 1) {
                                    // display the first two sports tab buttons
                                    echo $sportsTabButtons[$z];
                                } else {
                                    // display drop down if there are 3 or more sports
                                    if ($y >= 3) {
                                        if ($x == 0) {
                                            echo '<li class="dropdown">';
                                            echo '<a href="#" data-toggle="dropdown" class="more">More</a>';
                                            echo '<ul class="dropdown-menu" role="menu">';
                                        }

                                        // display drop down for other sports
                                        echo $sportsTabButtons[$z];

                                        if ($x == $y) {
                                            echo '</li>';
                                            echo '</ul>';
                                        }
                                    }
                                }
                                $x++;
                            }
                            ?>
                        </ul>
                    </div>
                    <div class="tab-content">
                        <div id="all" class="tab-pane fade in active">
                            <div class="content_full">
                                <div class="graphics_charts">
                                    <div class="single_graph_small">
                                        <div class="graph">
                                            <img src="images/143img.png" alt="image"/>
                                        </div>
                                        <div class="graph_title">
                                            <p>PlayerFax Score</p>
                                        </div>
                                    </div>
                                    <div class="single_graph_small">
                                        <div class="graph">
                                            <img src="images/64img.png" alt="image"/>
                                        </div>
                                        <div class="graph_title">
                                            <p>Percentile</p>
                                        </div>
                                    </div>
                                    <div class="single_graph_small">
                                        <div class="graph">
                                            <img src="images/3img.png" alt="image"/>
                                        </div>
                                        <div class="graph_title">
                                            <p>Sports Tracked</p>
                                        </div>
                                    </div>
                                    <div class="as_date">
                                        <p>As of Date: -/--/----</p>
                                    </div>
                                    <div class="big_chart">
                                        <img src="images/graph_img.png" alt="image"/>
                                    </div>
                                    <div class="big_chart_desc">
                                        <ul>
                                            <li><span>Top Sport:</span>--</li>
                                            <li><span>Trending Sport:</span>--</li>
                                            <li><span>Top Skill:</span>--</li>
                                            <li><span>Trending Skill:</span>--</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="graphics_details">
                                    <div class="single_detail">
                                        <label>Certified Stats vs Community Stats</label>
                                        <span>-/-</span>
                                    </div>
                                    <div class="single_detail">
                                        <label>Followers</label>
                                        <span>-</span>
                                    </div>
                                    <div class="single_detail">
                                        <label>Likes</label>
                                        <span>-</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php echo $sportsTabs; ?>

                    </div>
                </div>
            </div>
        </div>
</section>
    <div class="bottom_navigation">
        <ul>
            <li><a href="index.php?page=home" class="home"><span></span>Home</a></li>
            <li><a href="#alerts" class="alerts"><span></span>Alerts</a></li>
            <li><a href="#messages" class="msgs"><span></span>Messages</a></li>
            <li><a href="#" class="msgs" data-toggle="modal" data-target="#myModal1"><span></span>Post</a></li>
            <!--<li><a href="javascript:void(0);" class="find_player"><span></span>Find Player</a></li>-->
        </ul>
    </div>
<?php require 'includes/footer.php'; ?>
<!--add  media popup start-->
<div class="modal fade forget_section" id="myModal1" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="container">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="close_img">
                        <button type="button" class="close" data-dismiss="modal"></button>
                    </div>
                    <div class="title_h2">
                        <h2>Post To Timeline</h2>
                    </div>
                </div>
                <div class="modal-body">
                    <form id="addPlayerPostForm" method="post" class="form-horizontal">
                        <div id="addPlayerPostErrorBox"></div>
                        <textarea title="What's on your mind, <?php echo $userFirstName; ?>?" class="input_box form-control" name="message" placeholder="What's on your mind, <?php echo $userFirstName; ?>?" style="height:100px;"></textarea>
                        <div class="text-right" style="margin-bottom:10px;">
                            <button type="submit" id="addPlayerPost" value="addPlayerPost" class="btn btn_blue" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Please wait..."> Post</button>
                        </div>
                        <input type="hidden" name="token" value="<?php echo $playerToken; ?>">
                    </form>
                    <div id="mediaContainer"></div>
                    <div class="clearfix"></div>
                    <section>
                        <div class="input-group">
                            <label class="input-group-btn">
                                <span class="btn btn-default">
                                    <i class="fa fa-picture-o text-success" aria-hidden="true"></i> Photo/Video
                                    <input id="file_video_upload" type="file"
                                           accept="video/*, video/x-m4v, video/webm, video/x-ms-wmv, video/x-msvideo, video/3gpp, video/flv, video/x-flv, video/mp4, video/quicktime, video/mpeg, video/ogv, image/*"
                                           data-token="<?php echo $playerToken; ?>" style="display: none;" multiple>
                                </span>
                            </label>
                            <input type="text" class="form-control" readonly disabled>
                        </div>
                    </section>
                    <!--<button type="submit" class="btn btn-primary btn-lg pull-right"
                            data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Uploading ...">
                        Upload
                    </button>-->
                </div>
            </div>
        </div>
    </div>
</div>
<!--add  media popup end-->

<!--Message popup start-->
<div class="modal fade forget_section" id="myModal3" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="container">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="close_img">
                        <button type="button" class="close" data-dismiss="modal"></button>
                    </div>
                    <div class="title_h2">
                        <h2>Message</h2>
                    </div>
                </div>
                <div class="modal-body">
                    <div id="addUserMessageErrorBox">
                        <div class="alertMsg info">
                            <span><i class="fa fa-check-square-o"></i></span> All fields required <a class="alert-close" href="#">x</a>
                        </div>
                    </div>
                    <form id="addUserMessageForm" method="post" class="media_popup">
                        <!--<input type="text" name=placeholder="Name" required class="input_box"/>
                        <input type="email" placeholder="Email" required class="input_box"/>-->
                        <textarea name="message" class="input_box textarea_box" placeholder="Message"></textarea>
                        <input type="hidden" name="token" value="<?php echo $playerToken; ?>">
                        <div class="blue_btn">
                            <button type="submit" id="addUserMessage" value="addUserMessage" class="btn btn_blue" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Please wait..."> Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Message popup end-->

<!--Add Player popup start-->
<div class="modal fade forget_section player_p_section" id="myModal4" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="container">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="close_img">
                        <button type="button" class="close" data-dismiss="modal"></button>
                    </div>
                    <div class="title_h2">
                        <h2>Follow <?php echo $playerFirstName; ?></h2>
                    </div>
                </div>
                <div class="modal-body">
                    <form id="addPlayerForm" method="post">
                        <div class="row">
                            <div class="col-sm-offset-1 col-sm-10">
                                <h2>To Follow <?php echo $playerFirstName; ?> Please Define Your Relationship:</h2>
                                <div id="addPlayerErrorBox">
                                    <div class="alertMsg info">
                                        <span><i class="fa fa-check-square-o"></i></span> Select an option <a class="alert-close" href="#">x</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="checkbox_section">
                            <div class="checkbox">
                                <input type="checkbox" name="relationship[]" value="1" id="check1"/>
                                <label for="check1">I am <?php echo $playerFirstName; ?>’s Parent/Guardian</label>
                            </div>
                            <div class="checkbox">
                                <input type="checkbox" name="relationship[]" value="0" id="check2"/>
                                <label for="check2">I am <?php echo $playerFirstName; ?></label>
                            </div>
                            <div class="checkbox">
                                <input type="checkbox" name="relationship[]" value="2" id="check3"/>
                                <label for="check3">I am <?php echo $playerFirstName; ?>’s Coach/Instructor</label>
                            </div>
                            <div class="checkbox">
                                <input type="checkbox" name="relationship[]" value="3" id="check4"/>
                                <label for="check4"><?php echo $playerFirstName; ?> and I are Team Mates</label>
                            </div>
                            <div class="checkbox">
                                <input type="checkbox" name="relationship[]" value="4" id="check5"/>
                                <label for="check5">I am a Fan of <?php echo $playerFirstName; ?></label>
                            </div>
                            <div class="checkbox">
                                <input type="checkbox" name="relationship[]" value="5" id="check6"/>
                                <label for="check6">I want to invite <?php echo $playerFirstName; ?> to events</label>
                            </div>
                            <div class="checkbox">
                                <input type="checkbox" name="relationship[]" value="6" id="check7"/>
                                <label for="check7">I am tracking <?php echo $playerFirstName; ?>’s progress (recruiter)</label>
                            </div>
                            <div class="checkbox">
                                <select name="requestPlayerAdmin" class="input_box">
                                    <option value="">Request PlayerAdmin Rights?</option>
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                            <div class="blue_btn">
                                <input type="hidden" name="token" value="<?php echo $playerToken; ?>">
                                <button type="submit" id="addPlayer" value="addPlayer" class="btn btn_blue" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Please wait..."> Add Player</button>
                            </div>
                            <span>This will “Follow” <?php echo $playerFirstName; ?></span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Add Player popup end-->

<!--Add Sports popup start-->
<div class="modal fade forget_section" id="myModal5" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="container">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="close_img">
                        <button type="button" class="close" data-dismiss="modal"></button>
                    </div>
                    <div class="title_h2">
                        <h2>Add Sports</h2>
                    </div>
                </div>
                <div class="modal-body">
                    <form class="sport_popup">
                        <div class="radio_section">
                            <div class="radio">
                                <div class="radio_img">
                                    <img src="images/radio_img1.png" alt="radio_img" class="img-responsive"/>
                                </div>
                                <div class="radio_text">Baseball</div>
                                <input type="radio" name="radio1" id="radio1"/>
                                <label for="radio1"></label>

                            </div>
                            <div class="radio">
                                <div class="radio_img">
                                    <img src="images/radio_img2.png" alt="radio_img" class="img-responsive"/>
                                </div>
                                <div class="radio_text">Fast Pitch</div>
                                <input type="radio" name="radio1" id="radio2"/>
                                <label for="radio2"></label>

                            </div>
                            <div class="radio">
                                <div class="radio_img">
                                    <img src="images/radio_img3.png" alt="radio_img" class="img-responsive"/>
                                </div>
                                <div class="radio_text">Swimming</div>
                                <input type="radio" name="radio1" id="radio3"/>
                                <label for="radio3"></label>

                            </div>
                            <div class="radio">
                                <div class="radio_img">
                                    <img src="images/radio_img4.png" alt="radio_img" class="img-responsive"/>
                                </div>
                                <div class="radio_text">Cross Fit<span>middle school & high school.</span></div>
                                <input type="radio" name="radio1" id="radio4"/>
                                <label for="radio4"></label>

                            </div>
                            <div class="radio">
                                <div class="radio_img">
                                    <img src="images/radio_img5.png" alt="radio_img" class="img-responsive"/>
                                </div>
                                <div class="radio_text">Volleyball</div>
                                <input type="radio" name="radio1" id="radio5"/>
                                <label for="radio5"></label>

                            </div>
                            <div class="radio">
                                <div class="radio_img">
                                    <img src="images/radio_img6.png" alt="radio_img" class="img-responsive"/>
                                </div>
                                <div class="radio_text">Basketball</div>
                                <input type="radio" name="radio1" id="radio6"/>
                                <label for="radio6"></label>

                            </div>
                            <div class="radio">
                                <div class="radio_img">
                                    <img src="images/radio_img7.png" alt="radio_img" class="img-responsive"/>
                                </div>
                                <div class="radio_text">Soccer</div>
                                <input type="radio" name="radio1" id="radio7"/>
                                <label for="radio7"></label>

                            </div>
                            <div class="radio">
                                <div class="radio_img">
                                    <img src="images/radio_img8.png" alt="radio_img" class="img-responsive"/>
                                </div>
                                <div class="radio_text">Lacrosse</div>
                                <input type="radio" name="radio1" id="radio8"/>
                                <label for="radio8"></label>

                            </div>
                            <div class="radio">
                                <div class="radio_img">
                                    <img src="images/radio_img9.png" alt="radio_img" class="img-responsive"/>
                                </div>
                                <div class="radio_text">Football</div>
                                <input type="radio" name="radio1" id="radio9"/>
                                <label for="radio9"></label>

                            </div>
                            <div class="radio">
                                <div class="radio_img">
                                    <img src="images/radio_img10.png" alt="radio_img" class="img-responsive"/>
                                </div>
                                <div class="radio_text">Track & Field</div>
                                <input type="radio" name="radio1" id="radio10"/>
                                <label for="radio10"></label>

                            </div>

                        </div>
                        <div class="blue_btn">
                            <input type="submit" value="Submit" class="blue_bg_btn"/>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Add Sports popup end-->

<!--Add Player Stats popup start-->
<div class="modal fade forget_section player_p_section" id="myModal6" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="container">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="close_img">
                        <button type="button" class="close" data-dismiss="modal"></button>
                    </div>
                    <div class="title_h2">
                        <h2>Add Player Stats</h2>
                    </div>
                </div>
                <div class="modal-body">
                    <h2>Choose Sport</h2>
                    <form class="panel-group player_accordion" id="accordion">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#panel_baseball" aria-expanded="false" class="collapsed">
                                        Baseball<span class="player_img1"></span>
                                    </a>
                                </h4>
                            </div>
                            <div id="panel_baseball" class="panel-collapse collapse" aria-expanded="false">
                                <div class="panel-body">
                                    <div class="player_table">
                                        <div class="table">
                                            <div class="thead">
                                                <div class="tr">
                                                    <div class="th"></div>
                                                    <div class="th">Item</div>
                                                    <div class="th">Value</div>
                                                    <div class="th hidden-xs"></div>
                                                </div>
                                            </div>
                                            <div class="tbody">
                                                <div class="tr">
                                                    <div class="td"></div>
                                                    <div class="td">Velocity Mound</div>
                                                    <div class="td">
                                                        <input name="baseball_VelocityMound" type="tel" placeholder="0" class="input_box format_mph"/>
                                                    </div>
                                                    <div class="td hidden-xs">MPH</div>
                                                </div>
                                                <div class="tr">
                                                    <div class="td"></div>
                                                    <div class="td">Velocity Outfield</div>
                                                    <div class="td">
                                                        <input name="baseball_VelocityOutfield" type="tel" placeholder="0" class="input_box format_mph"/>
                                                    </div>
                                                    <div class="td hidden-xs">MPH</div>
                                                </div>
                                                <div class="tr">
                                                    <div class="td"></div>
                                                    <div class="td">Velocity Infield</div>
                                                    <div class="td">
                                                        <input name="baseball_VelocityInfield" type="tel" placeholder="0" class="input_box format_mph"/>
                                                    </div>
                                                    <div class="td hidden-xs">MPH</div>
                                                </div>
                                                <div class="tr">
                                                    <div class="td"></div>
                                                    <div class="td">Swing Velocity</div>
                                                    <div class="td">
                                                        <input name="baseball_SwingVelocity" type="tel" placeholder="0" class="input_box format_mph"/>
                                                    </div>
                                                    <div class="td hidden-xs">MPH</div>
                                                </div>
                                                <div class="tr">
                                                    <div class="td"></div>
                                                    <div class="td">60 Yard Dash</div>
                                                    <div class="td">
                                                        <input name="baseball_60YardDash" type="text" placeholder="M:S.U" class="input_box format_time"/>
                                                    </div>
                                                    <div class="td hidden-xs">Time</div>
                                                </div>
                                                <div class="tr">
                                                    <div class="td"></div>
                                                    <div class="td">Catcher Pop</div>
                                                    <div class="td">
                                                        <input name="baseball_CatcherPop" type="text" placeholder="M:S.U" class="input_box format_time"/>
                                                    </div>
                                                    <div class="td hidden-xs">Time</div>
                                                </div>
                                                <div class="tr">
                                                    <div class="td"></div>
                                                    <div class="td">Catcher Release</div>
                                                    <div class="td">
                                                        <input name="baseball_CatcherRelease" type="text" placeholder="M:S.U" class="input_box format_time"/>
                                                    </div>
                                                    <div class="td hidden-xs">Time</div>
                                                </div>
                                                <div class="tr">
                                                    <div class="td"></div>
                                                    <div class="td">Primary Position</div>
                                                    <div class="td">
                                                        <select name="baseball_PrimaryPosition" class="input_box">
                                                            <option value="">Select</option>
                                                            <option value="P">Pitcher</option>
                                                            <option value="C">Catcher</option>
                                                            <option value="1B">1st Baseman</option>
                                                            <option value="2B">2nd Baseman</option>
                                                            <option value="3B">3rd Baseman</option>
                                                            <option value="SS">Shortstop</option>
                                                            <option value="LF">Left Fielder</option>
                                                            <option value="CF">Center Fielder</option>
                                                            <option value="RF">Right Fielder</option>
                                                        </select>
                                                    </div>
                                                    <div class="td hidden-xs"></div>
                                                </div>
                                                <div class="tr">
                                                    <div class="td"></div>
                                                    <div class="td">Secondary Position</div>
                                                    <div class="td">
                                                        <select name="baseball_SecondaryPosition" class="input_box">
                                                            <option value="">Select</option>
                                                            <option value="P">Pitcher</option>
                                                            <option value="C">Catcher</option>
                                                            <option value="1B">1st Baseman</option>
                                                            <option value="2B">2nd Baseman</option>
                                                            <option value="3B">3rd Baseman</option>
                                                            <option value="SS">Shortstop</option>
                                                            <option value="LF">Left Fielder</option>
                                                            <option value="CF">Center Fielder</option>
                                                            <option value="RF">Right Fielder</option>
                                                        </select>
                                                    </div>
                                                    <div class="td hidden-xs"></div>
                                                </div>
                                                <div class="tr">
                                                    <div class="td"></div>
                                                    <div class="td">Tee Velocity</div>
                                                    <div class="td">
                                                        <input name="baseball_TeeVelocity" type="tel" placeholder="0" class="input_box format_mph"/>
                                                    </div>
                                                    <div class="td hidden-xs">MPH</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title ">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#panel_fastPitch">
                                        Fast Pitch<span class="player_img1 player_img2"></span>
                                    </a>
                                </h4>
                            </div>
                            <div id="panel_fastPitch" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <div class="player_table">
                                        <div class="table">
                                            <div class="thead">
                                                <div class="tr">
                                                    <div class="th"></div>
                                                    <div class="th">Item</div>
                                                    <div class="th">Value</div>
                                                    <div class="th hidden-xs"></div>
                                                </div>
                                            </div>
                                            <div class="tbody">
                                                <div class="tr">
                                                    <div class="td"></div>
                                                    <div class="td">Velocity Mound</div>
                                                    <div class="td">
                                                        <input name="fastPitch_VelocityMound" type="tel" placeholder="0" class="input_box format_mph"/>
                                                    </div>
                                                    <div class="td hidden-xs">MPH</div>
                                                </div>
                                                <div class="tr">
                                                    <div class="td"></div>
                                                    <div class="td">Velocity Outfield</div>
                                                    <div class="td">
                                                        <input name="fastPitch_VelocityOutfield" type="tel" placeholder="0" class="input_box format_mph"/>
                                                    </div>
                                                    <div class="td hidden-xs">MPH</div>
                                                </div>
                                                <div class="tr">
                                                    <div class="td"></div>
                                                    <div class="td">Velocity Infield</div>
                                                    <div class="td">
                                                        <input name="fastPitch_VelocityInfield" type="tel" placeholder="0" class="input_box format_mph"/>
                                                    </div>
                                                    <div class="td hidden-xs">MPH</div>
                                                </div>
                                                <div class="tr">
                                                    <div class="td"></div>
                                                    <div class="td">Swing Velocity</div>
                                                    <div class="td">
                                                        <input name="fastPitch_SwingVelocity" type="tel" placeholder="0" class="input_box format_mph"/>
                                                    </div>
                                                    <div class="td hidden-xs">MPH</div>
                                                </div>
                                                <div class="tr">
                                                    <div class="td"></div>
                                                    <div class="td">60 Yard Dash</div>
                                                    <div class="td">
                                                        <input name="fastPitch_60YardDash" type="text" placeholder="M:S.U" class="input_box format_time"/>
                                                    </div>
                                                    <div class="td hidden-xs">Time</div>
                                                </div>
                                                <div class="tr">
                                                    <div class="td"></div>
                                                    <div class="td">Catcher Pop</div>
                                                    <div class="td">
                                                        <input name="fastPitch_CatcherPop" type="text" placeholder="M:S.U" class="input_box format_time"/>
                                                    </div>
                                                    <div class="td hidden-xs">Time</div>
                                                </div>
                                                <div class="tr">
                                                    <div class="td"></div>
                                                    <div class="td">Catcher Release</div>
                                                    <div class="td">
                                                        <input name="fastPitch_CatcherRelease" type="text" placeholder="M:S.U" class="input_box format_time"/>
                                                    </div>
                                                    <div class="td hidden-xs">Time</div>
                                                </div>
                                                <div class="tr">
                                                    <div class="td"></div>
                                                    <div class="td">Primary Position</div>
                                                    <div class="td">
                                                        <select name="fastPitch_PrimaryPosition" class="input_box">
                                                            <option value="">Select</option>
                                                            <option value="P">Pitcher</option>
                                                            <option value="C">Catcher</option>
                                                            <option value="1B">1st Baseman</option>
                                                            <option value="2B">2nd Baseman</option>
                                                            <option value="3B">3rd Baseman</option>
                                                            <option value="SS">Shortstop</option>
                                                            <option value="LF">Left Fielder</option>
                                                            <option value="CF">Center Fielder</option>
                                                            <option value="RF">Right Fielder</option>
                                                        </select>
                                                    </div>
                                                    <div class="td hidden-xs"></div>
                                                </div>
                                                <div class="tr">
                                                    <div class="td"></div>
                                                    <div class="td">Secondary Position</div>
                                                    <div class="td">
                                                        <select name="fastPitch_SecondaryPosition" class="input_box">
                                                            <option value="">Select</option>
                                                            <option value="P">Pitcher</option>
                                                            <option value="C">Catcher</option>
                                                            <option value="1B">1st Baseman</option>
                                                            <option value="2B">2nd Baseman</option>
                                                            <option value="3B">3rd Baseman</option>
                                                            <option value="SS">Shortstop</option>
                                                            <option value="LF">Left Fielder</option>
                                                            <option value="CF">Center Fielder</option>
                                                            <option value="RF">Right Fielder</option>
                                                        </select>
                                                    </div>
                                                    <div class="td hidden-xs"></div>
                                                </div>
                                                <div class="tr">
                                                    <div class="td"></div>
                                                    <div class="td">Tee Velocity</div>
                                                    <div class="td">
                                                        <input name="fastPitch_TeeVelocity" type="tel" placeholder="0" class="input_box format_mph"/>
                                                    </div>
                                                    <div class="td hidden-xs">MPH</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title ">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#panel_lacrosse">
                                        Lacrosse<span class="player_img1 player_img8"></span>
                                    </a>
                                </h4>
                            </div>
                            <div id="panel_lacrosse" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <div class="player_table">
                                        <div class="table">
                                            <div class="thead">
                                                <div class="tr">
                                                    <div class="th"></div>
                                                    <div class="th">Item</div>
                                                    <div class="th">Value</div>
                                                    <div class="th hidden-xs"></div>
                                                </div>
                                            </div>
                                            <div class="tbody">
                                                <div class="tr">
                                                    <div class="td"></div>
                                                    <div class="td">60 Yard Dash</div>
                                                    <div class="td">
                                                        <input name="lacrosse_60YardDash" type="text" placeholder="M:S.U" class="input_box format_time"/>
                                                    </div>
                                                    <div class="td hidden-xs">Time</div>
                                                </div>
                                                <div class="tr">
                                                    <div class="td"></div>
                                                    <div class="td">5 Cone Footwork</div>
                                                    <div class="td">
                                                        <input name="lacrosse_5ConeFootwork" type="text" placeholder="M:S.U" class="input_box format_time"/>
                                                    </div>
                                                    <div class="td hidden-xs">Time</div>
                                                </div>
                                                <div class="tr">
                                                    <div class="td"></div>
                                                    <div class="td">Shuttle Run</div>
                                                    <div class="td">
                                                        <input name="lacrosse_ShuttleRun" type="text" placeholder="M:S.U" class="input_box format_time"/>
                                                    </div>
                                                    <div class="td hidden-xs">Time</div>
                                                </div>
                                                <div class="tr">
                                                    <div class="td"></div>
                                                    <div class="td">Rebounder 10</div>
                                                    <div class="td">
                                                        <input name="lacrosse_Rebounder10" type="tel" placeholder="0" class="input_box format_count"/>
                                                    </div>
                                                    <div class="td hidden-xs">Count</div>
                                                </div>
                                                <div class="tr">
                                                    <div class="td"></div>
                                                    <div class="td">Goal Shot 10</div>
                                                    <div class="td">
                                                        <input name="lacrosse_GoalShot10" type="tel" placeholder="0" class="input_box format_count"/>
                                                    </div>
                                                    <div class="td hidden-xs">Count</div>
                                                </div>
                                                <div class="tr">
                                                    <div class="td"></div>
                                                    <div class="td">Accuracy 50</div>
                                                    <div class="td">
                                                        <input name="lacrosse_Accuracy50" type="tel" placeholder="0" class="input_box format_count"/>
                                                    </div>
                                                    <div class="td hidden-xs">Count</div>
                                                </div>
                                                <div class="tr">
                                                    <div class="td"></div>
                                                    <div class="td">Velocity Throw</div>
                                                    <div class="td">
                                                        <input name="lacrosse_VelocityThrow" type="tel" placeholder="0" class="input_box format_mph"/>
                                                    </div>
                                                    <div class="td hidden-xs">MPH</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title ">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#panel_crossFit">
                                        Cross Fit<span class="player_img1 player_img4"></span>
                                    </a>
                                </h4>
                            </div>
                            <div id="panel_crossFit" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <div class="player_table">
                                        <div class="table">
                                            <div class="thead">
                                                <div class="tr">
                                                    <div class="th"></div>
                                                    <div class="th">Item</div>
                                                    <div class="th">Value</div>
                                                    <div class="th hidden-xs"></div>
                                                </div>
                                            </div>
                                            <div class="tbody">
                                                <div class="tr">
                                                    <div class="td"></div>
                                                    <div class="td">Shuttle Run</div>
                                                    <div class="td">
                                                        <input name="crossFit_ShuttleRun" type="text" placeholder="M:S.U" class="input_box format_time"/>
                                                    </div>
                                                    <div class="td hidden-xs">Time</div>
                                                </div>
                                                <div class="tr">
                                                    <div class="td"></div>
                                                    <div class="td">40 Yard Dash</div>
                                                    <div class="td">
                                                        <input name="crossFit_40YardDash" type="text" placeholder="M:S.U" class="input_box format_time"/>
                                                    </div>
                                                    <div class="td hidden-xs">Time</div>
                                                </div>
                                                <div class="tr">
                                                    <div class="td"></div>
                                                    <div class="td">5-10-5 Cone Drill</div>
                                                    <div class="td">
                                                        <input name="crossFit_5105ConeDrill" type="text" placeholder="M:S.U" class="input_box format_time"/>
                                                    </div>
                                                    <div class="td hidden-xs">Time</div>
                                                </div>
                                                <div class="tr">
                                                    <div class="td"></div>
                                                    <div class="td">3 RM Hang</div>
                                                    <div class="td">
                                                        <input name="crossFit_3RMHang" type="tel" placeholder="0" class="input_box format_count"/>
                                                    </div>
                                                    <div class="td hidden-xs">Count</div>
                                                </div>
                                                <div class="tr">
                                                    <div class="td"></div>
                                                    <div class="td">Vertical Jump</div>
                                                    <div class="td">
                                                        <input name="crossFit_VerticalJump" type="tel" placeholder="0.00" class="input_box format_inch"/>
                                                    </div>
                                                    <div class="td hidden-xs">Inch</div>
                                                </div>
                                                <div class="tr">
                                                    <div class="td"></div>
                                                    <div class="td">Broad Jump</div>
                                                    <div class="td">
                                                        <input name="crossFit_BroadJump" type="tel" placeholder="0.00" class="input_box format_inch"/>
                                                    </div>
                                                    <div class="td hidden-xs">Inch</div>
                                                </div>
                                                <div class="tr">
                                                    <div class="td"></div>
                                                    <div class="td">Power Clean</div>
                                                    <div class="td">
                                                        <input name="crossFit_PowerClean" type="tel" placeholder="0" class="input_box format_count"/>
                                                    </div>
                                                    <div class="td hidden-xs">Count</div>
                                                </div>
                                                <div class="tr">
                                                    <div class="td"></div>
                                                    <div class="td">Pull-Ups</div>
                                                    <div class="td">
                                                        <input name="crossFit_PullUps" type="tel" placeholder="0" class="input_box format_count"/>
                                                    </div>
                                                    <div class="td hidden-xs">Count</div>
                                                </div>
                                                <div class="tr">
                                                    <div class="td"></div>
                                                    <div class="td">Push-Ups</div>
                                                    <div class="td">
                                                        <input name="crossFit_PushUps" type="tel" placeholder="0" class="input_box format_count"/>
                                                    </div>
                                                    <div class="td hidden-xs">Count</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title ">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#panel_swimming">
                                        Swimming<span class="player_img1 player_img3"></span>
                                    </a>
                                </h4>
                            </div>
                            <div id="panel_swimming" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <div class="player_table">
                                        <div class="table">
                                            <div class="thead">
                                                <div class="tr">
                                                    <div class="th"></div>
                                                    <div class="th">Item</div>
                                                    <div class="th">Value</div>
                                                    <div class="th hidden-xs"></div>
                                                </div>
                                            </div>
                                            <div class="tbody">
                                                <div class="tr">
                                                    <div class="td"></div>
                                                    <div class="td">25 M. Freestyle</div>
                                                    <div class="td">
                                                        <input name="swimming_25MFreestyle" type="text" placeholder="M:S.U" class="input_box format_time"/>
                                                    </div>
                                                    <div class="td hidden-xs">Time</div>
                                                </div>
                                                <div class="tr">
                                                    <div class="td"></div>
                                                    <div class="td">25 M. BackStroke</div>
                                                    <div class="td">
                                                        <input name="swimming_25MBackStroke" type="text" placeholder="M:S.U" class="input_box format_time"/>
                                                    </div>
                                                    <div class="td hidden-xs">Time</div>
                                                </div>
                                                <div class="tr">
                                                    <div class="td"></div>
                                                    <div class="td">25 M. Breast Stroke</div>
                                                    <div class="td">
                                                        <input name="swimming_25MBreastStroke" type="text" placeholder="M:S.U" class="input_box format_time"/>
                                                    </div>
                                                    <div class="td hidden-xs">Time</div>
                                                </div>
                                                <div class="tr">
                                                    <div class="td"></div>
                                                    <div class="td">25 M. Butterfly</div>
                                                    <div class="td">
                                                        <input name="swimming_25MButterfly" type="text" placeholder="M:S.U" class="input_box format_time"/>
                                                    </div>
                                                    <div class="td hidden-xs">Time</div>
                                                </div>

                                                <div class="tr">
                                                    <div class="td"></div>
                                                    <div class="td">50 M. Freestyle</div>
                                                    <div class="td">
                                                        <input name="swimming_50MFreestyle" type="text" placeholder="M:S.U" class="input_box format_time"/>
                                                    </div>
                                                    <div class="td hidden-xs">Time</div>
                                                </div>
                                                <div class="tr">
                                                    <div class="td"></div>
                                                    <div class="td">50 M. BackStroke</div>
                                                    <div class="td">
                                                        <input name="swimming_50MBackStroke" type="text" placeholder="M:S.U" class="input_box format_time"/>
                                                    </div>
                                                    <div class="td hidden-xs">Time</div>
                                                </div>
                                                <div class="tr">
                                                    <div class="td"></div>
                                                    <div class="td">50 M. Breast Stroke</div>
                                                    <div class="td">
                                                        <input name="swimming_50MBreastStroke" type="text" placeholder="M:S.U" class="input_box format_time"/>
                                                    </div>
                                                    <div class="td hidden-xs">Time</div>
                                                </div>
                                                <div class="tr">
                                                    <div class="td"></div>
                                                    <div class="td">50 M. Butterfly</div>
                                                    <div class="td">
                                                        <input name="swimming_50MButterfly" type="text" placeholder="M:S.U" class="input_box format_time"/>
                                                    </div>
                                                    <div class="td hidden-xs">Time</div>
                                                </div>

                                                <div class="tr">
                                                    <div class="td"></div>
                                                    <div class="td">100 M. Freestyle</div>
                                                    <div class="td">
                                                        <input name="swimming_100MFreestyle" type="text" placeholder="M:S.U" class="input_box format_time"/>
                                                    </div>
                                                    <div class="td hidden-xs">Time</div>
                                                </div>
                                                <div class="tr">
                                                    <div class="td"></div>
                                                    <div class="td">100 M. BackStroke</div>
                                                    <div class="td">
                                                        <input name="swimming_100MBackStroke" type="text" placeholder="M:S.U" class="input_box format_time"/>
                                                    </div>
                                                    <div class="td hidden-xs">Time</div>
                                                </div>
                                                <div class="tr">
                                                    <div class="td"></div>
                                                    <div class="td">100 M. Breast Stroke</div>
                                                    <div class="td">
                                                        <input name="swimming_100MBreastStroke" type="text" placeholder="M:S.U" class="input_box format_time"/>
                                                    </div>
                                                    <div class="td hidden-xs">Time</div>
                                                </div>
                                                <div class="tr">
                                                    <div class="td"></div>
                                                    <div class="td">100 M. Butterfly</div>
                                                    <div class="td">
                                                        <input name="swimming_100MButterfly" type="text" placeholder="M:S.U" class="input_box format_time"/>
                                                    </div>
                                                    <div class="td hidden-xs">Time</div>
                                                </div>

                                                <div class="tr">
                                                    <div class="td"></div>
                                                    <div class="td">100 M. Individual Medley</div>
                                                    <div class="td">
                                                        <input name="swimming_100MIndividualMedley" type="text" placeholder="M:S.U" class="input_box format_time"/>
                                                    </div>
                                                    <div class="td hidden-xs">Time</div>
                                                </div>
                                                <div class="tr">
                                                    <div class="td"></div>
                                                    <div class="td">200 M. Individual Medley</div>
                                                    <div class="td">
                                                        <input name="swimming_200MIndividualMedley" type="text" placeholder="M:S.U" class="input_box format_time"/>
                                                    </div>
                                                    <div class="td hidden-xs">Time</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="token" value="<?php echo $playerToken; ?>">
                        <div id="addPlayerStatsErrorBox"></div>
                        <input type="text" name="statsEntryDate" class="input_box date" placeholder="Date">
                        <div class="all_btn">
                            <button id="addPlayerStats" type="submit" name="submit" value="addPlayerStats" class="btn btn-success" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Add Player Stats popup end-->

<!--View Timeline popup start-->
<div class="modal fade" id="myModal7" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="container">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="close_img">
                        <button type="button" class="close" data-dismiss="modal"></button>
                    </div>
                    <div class="title_h2">
                        <h2><?php echo $playerFirstName; ?>’s Timeline</h2>
                    </div>
                </div>
                <div class="modal-body">
                    <div id="player_timeline" data-token="<?php echo $playerToken; ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--View Timeline popup end-->

<!--Sport popup start-->
<div class="modal fade forget_section" id="myModal10" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="container">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="close_img">
                        <button type="button" class="close" data-dismiss="modal"></button>
                    </div>
                    <div class="title_h2">
                        <h2>Add Sport</h2>
                    </div>
                </div>
                <div class="modal-body">
                    <form class="media_popup">
                        <input type="text" placeholder="Sport Name" required class="input_box"/>
                        <textarea class="input_box textarea_box" placeholder="Description"></textarea>
                        <input type="text" name="Date" class="input_box" placeholder="Sport Category"/>
                        <div class="blue_btn">
                            <input type="submit" value="Submit" class="blue_bg_btn"/>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Sport popup end-->


<style>
    #player_img_container {
        width: 650px;
        max-width: 650px;
        height: auto;
        margin-bottom: 20px;
    }

    #player_img_container img.player_img_profile_picture {
        width: 100%;
        max-width: 100%;
        border: 1px solid #ddd;
    }

    #player_img_link {
        background-color: #f3ff2b;
        position: relative;
        transform: translate(-50%, -50%);
    }

    #player_img_link label {
        cursor: pointer;
        font-size: 18px;
    }

    #player_img_link input {
        height: 1px;
        width: 1px;
        opacity: 0.0; /* Standard: FF gt 1.5, Opera, Safari */
        filter: alpha(opacity=0); /* IE lt 8 */
        -ms-filter: "alpha(opacity=0)"; /* IE 8 */
        -khtml-opacity: 0.0; /* Safari 1.x */
        -moz-opacity: 0.0; /* FF lt 1.5, Netscape */
    }

    #player_img_link a {
        color: #fff;
        -webkit-font-smoothing: antialiased;
    }

    #player_img_preview {
        border: 1px solid #ddd;
        margin-bottom: 20px;
    }
</style>

<!-- Change Player Picture -->
<div id="player_img_modal" class="modal fade forget_section" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="container">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="close_img">
                        <button type="button" class="close" data-dismiss="modal"></button>
                    </div>
                    <div class="title_h2">
                        <h2>Change Profile Picture</h2>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-10">
                            <div id="player_img_message"></div>
                        </div>
                        <div class="col-md-2">
                            <a href="#" id="player_img_link">
                                <label><input id="player_img_input" type="file" data-token="<?php echo $playerToken; ?>" accept="image/jpg,image/png,image/jpeg,image/gif"/>Upload Picture</label>
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <div id="player_img_container">
                                <img class="player_img_profile_picture" src="<?php echo $playerPicture; ?>" alt="<?php echo $playerFullName; ?>" data-token="<?php echo $playerToken; ?>"/>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <img id="player_img_preview" src="about:blank">
                        </div>
                    </div>

                    <button id="player_img_crop" type="button" class="btn btn-primary">Crop and Save</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Change User Picture -->

<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/jquery.flexslider-min.js"></script>
<script type="text/javascript" src="js/bootstrap-tabcollapse.js"></script>
<script type="text/javascript" src="js/SmoothScroll.js"></script>
<script type="text/javascript" src="js/moment.min.js"></script>
<script type="text/javascript" src="js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="js/bootstrap-datetimepicker.js"></script>
<script type="text/javascript" src="js/select2.js"></script>
<script type="text/javascript" src="js/DataTables-1.10.15/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="js/DataTables-1.10.15/media/js/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="js/DataTables-1.10.15/extensions/Buttons/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="js/DataTables-1.10.15/extensions/Buttons/js/buttons.bootstrap.min.js"></script>
<script type="text/javascript" src="js/DataTables-1.10.15/extensions/Buttons/js/buttons.print.min.js"></script>
<script type="text/javascript" src="js/DataTables-1.10.15/extensions/Buttons/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="js/jquery.storage.js"></script>
<script type="text/javascript" src="js/cropper-min.js"></script>
<script type="text/javascript" src="js/custom.js"></script>

<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript" src="js/jq-ajax-progress.min.js"></script>

<script>
    var isComplete = false;
    var $playerProfileImage, canvasData, cropBoxData;

    $(document).ready(function () {

        // clear input values
        $('#myModal6').find('input.input_box').each(function () {
            $(this).val('');
        });

        $('input.format_mph').each(function() {
            var input = $(this);
            input.popover({
                title : 'MPH',
                content : 'Whole number (example: 150)',
                placement : 'left',
                trigger: 'focus'
            });
        });

        $('input.format_time').each(function() {
            var input = $(this);
            input.popover({
                title : 'Time',
                content : 'Minutes:Seconds.Microseconds',
                placement : 'left',
                trigger: 'focus'
            });
        });

        $('input.format_count').each(function() {
            var input = $(this);
            input.popover({
                title : 'Count',
                content : 'Whole number (example: 35)',
                placement : 'left',
                trigger: 'focus'
            });
        });

        $('input.format_inch').each(function() {
            var input = $(this);
            input.popover({
                title : 'Inches',
                content : 'Whole or Decimal (35 or 35.2)',
                placement : 'left',
                trigger: 'focus'
            });
        });

        $playerProfileImage = $('#player_img_container img.player_img_profile_picture');

        $('#addPlayer').button('reset');
        $('#addPlayerStats').button('reset');

        $('#accordion').on('submit', function (e) {
            if (isComplete === false) {
                $('#addPlayerStats').button('loading');
                isComplete = true;
                $.ajax({
                    url: 'index.php?action=addPlayerStats',
                    type: 'POST',
                    data: $(this).serializeArray(),
                    dataType: 'json',
                    success: function (json) {

                        $('#addPlayerStatsErrorBox').empty();

                        //data: return data from server
                        if (typeof json.success !== 'undefined') {
                            $('#addPlayerStatsErrorBox').html(json.success);
                        }

                        if (typeof json.error !== 'undefined') {
                            $('#addPlayerStatsErrorBox').html(json.error);
                        }
                        isComplete = false;
                        $('#addPlayerStats').button('reset');
                    }
                });
            }
            e.preventDefault(); //STOP default action
            //e.unbind();
        });

        $('#addPlayerForm').on('submit', function (e) {
            if (isComplete === false) {
                $('#addPlayer').button('loading');
                isComplete = true;
                $.ajax({
                    url: 'index.php?action=followPlayer',
                    type: 'POST',
                    data: $(this).serializeArray(),
                    dataType: 'json',
                    success: function (json) {

                        $('#addPlayerErrorBox').empty();

                        //data: return data from server
                        if (typeof json.success !== 'undefined') {
                            $('#addPlayerErrorBox').html(json.success);
                        }

                        if (typeof json.error !== 'undefined') {
                            $('#addPlayerErrorBox').html(json.error);
                        }
                        isComplete = false;
                        $('#addPlayer').button('reset');
                    }
                });
            }
            e.preventDefault(); //STOP default action
            //e.unbind();
        });

        $('#searchPlayerQuery').select2({
            theme: "bootstrap",
            placeholder: "Search by Name, School, Grad Year, City, State and Email Address",
            allowClear: true,
            minimumInputLength: 3,
            closeOnSelect: false,
            ajax: {
                url: "index.php?action=searchForPlayer",
                dataType: "json",
                width: 'style',
                delay: 800,
                type: 'POST',
                data: function (params) {
                    return {
                        query: params.term,
                        page: params.page,
                        per_page: 10
                    };
                },
                processResults: function (data, params) {

                    //params.page = params.page || 1;
                    return {
                        results: data.results.players.map(function (player) {
                            return {
                                id: player.Token,
                                playerGender: player.Gender,
                                playerName: player.Name,
                                playerPicture: player.Picture,
                                playerSchool: player.School,
                                playerCity: player.City,
                                playerStateLong: player.StateLong,
                                playerGradYear: player.GradYear,
                                playerIsFriend: player.IsFriend
                            };
                        }),
                        pagination: {
                            // If there are 10 matches, there's at least another page
                            more: data.results.players.length === 10
                            //more: (params.page * 10) < data.recordsTotal
                        }
                    };
                },
                cache: true
            },
            templateResult: formatPlayer,
            templateSelection: formatPlayerSelection
        }).on('select2:select', function (e) {
            var selectedElement = $(e.currentTarget);
            var selectedValue = selectedElement.val();
            window.open('index.php?page=player&token=' + selectedValue, 'PlayerFaxWindow');
        });

        $('#addUserMessageForm').on('submit', function (e) {
            if (isComplete === false) {
                $('#addUserMessage').button('loading');
                isComplete = true;
                $.ajax({
                    url: 'index.php?action=addPlayerMessage',
                    type: 'POST',
                    data: $(this).serializeArray(),
                    dataType: 'json',
                    success: function (json) {

                        $('#addUserMessageErrorBox').empty();

                        //data: return data from server
                        if (typeof json.success !== 'undefined') {
                            $('#addUserMessageErrorBox').html(json.success);
                        }

                        if (typeof json.error !== 'undefined') {
                            $('#addUserMessageErrorBox').html(json.error);
                        }
                        isComplete = false;
                        $('#addUserMessage').button('reset');
                    }
                });
            }
            e.preventDefault(); //STOP default action
            //e.unbind();
        });

        $('#addPlayerPostForm').on('submit', function (e) {
            if (isComplete === false) {
                $('#addPlayerPost').button('loading');
                isComplete = true;
                $.ajax({
                    url: 'index.php?action=addPlayerPost',
                    type: 'POST',
                    data: $(this).serializeArray(),
                    dataType: 'json',
                    success: function (json) {

                        $('#addPlayerPostErrorBox').empty();

                        //data: return data from server
                        if (typeof json.success !== 'undefined') {
                            $('#addPlayerPostErrorBox').html(json.success);
                        }

                        if (typeof json.error !== 'undefined') {
                            $('#addPlayerPostErrorBox').html(json.error);
                        }
                        isComplete = false;
                        $('#addPlayerPost').button('reset');
                    }
                });
            }
            e.preventDefault(); //STOP default action
            //e.unbind();
        });

        <?php if ($isAdmin === true): ?>

        $('#player_img_modal').on('shown.bs.modal', function () {
            $playerProfileImage.cropper({
                aspectRatio: 1 / 1,
                autoCropArea: 0.6,
                ready: function () {
                    var croppedCanvas = $playerProfileImage.cropper('getCroppedCanvas');
                    var png = croppedCanvas.toDataURL("image/png");
                    $('#player_img_preview').attr('src', png);
                    $playerProfileImage.cropper('setCanvasData', canvasData);
                    $playerProfileImage.cropper('setCropBoxData', cropBoxData);
                }
            }).on('cropend', function () {
                var croppedCanvas = $playerProfileImage.cropper('getCroppedCanvas');
                var png = croppedCanvas.toDataURL("image/png");
                $('#player_img_preview').attr('src', png);
                $playerProfileImage.cropper('setCanvasData', canvasData);
                $playerProfileImage.cropper('setCropBoxData', cropBoxData);
            }).on('zoom', function () {
                var croppedCanvas = $playerProfileImage.cropper('getCroppedCanvas');
                var png = croppedCanvas.toDataURL("image/png");
                $('#player_img_preview').attr('src', png);
                $playerProfileImage.cropper('setCanvasData', canvasData);
                $playerProfileImage.cropper('setCropBoxData', cropBoxData);
            });
        }).on('hidden.bs.modal', function () {
            $playerProfileImage.cropper('destroy');
            canvasData = '';
            cropBoxData = '';
            $('#player_img_preview').attr('src', 'about:blank');
        });

        $('#player_img_crop').on('click', function () {
            var cropBoxData = $playerProfileImage.cropper('getCropBoxData');
            var croppedCanvas = $playerProfileImage.cropper('getCroppedCanvas');
            var png = croppedCanvas.toDataURL("image/png");
            var imageData = png.replace(/^data:image\/(png|jpeg);base64,/, "");
            var token = $playerProfileImage.data('token');
            var data = new FormData();
            data.append('Filedata', imageData);
            data.append('token', token);

            $.ajax({
                url: 'index.php?action=editPlayerPicture',
                data: data,
                dataType: 'json',
                processData: false,
                contentType: false,
                type: 'POST',
                success: function (json) {
                    if (typeof json.success !== 'undefined') {
                        $('#player_img_message').html(json.success);

                        $playerProfileImage.cropper('destroy');
                        canvasData = '';
                        cropBoxData = '';
                        $('#player_img_preview').attr('src', 'about:blank');

                        $('.player_img_profile_picture').attr('src', json.url);
                        $playerProfileImage.cropper({
                            aspectRatio: 1 / 1,
                            autoCropArea: 0.6,
                            ready: function () {
                                var croppedCanvas = $playerProfileImage.cropper('getCroppedCanvas');
                                var png = croppedCanvas.toDataURL("image/png");
                                $('#player_img_preview').attr('src', png);
                                $playerProfileImage.cropper('setCanvasData', canvasData);
                                $playerProfileImage.cropper('setCropBoxData', cropBoxData);
                            }
                        }).on('cropend', function () {
                            var croppedCanvas = $playerProfileImage.cropper('getCroppedCanvas');
                            var png = croppedCanvas.toDataURL("image/png");
                            $('#player_img_preview').attr('src', png);
                            $playerProfileImage.cropper('setCanvasData', canvasData);
                            $playerProfileImage.cropper('setCropBoxData', cropBoxData);
                        }).on('zoom', function () {
                            var croppedCanvas = $playerProfileImage.cropper('getCroppedCanvas');
                            var png = croppedCanvas.toDataURL("image/png");
                            $('#player_img_preview').attr('src', png);
                            $playerProfileImage.cropper('setCanvasData', canvasData);
                            $playerProfileImage.cropper('setCropBoxData', cropBoxData);
                        });
                    }

                    if (typeof json.error !== 'undefined') {
                        $('#player_img_message').html(json.error);
                    }
                }
            });

        });

        $('#player_img_input').on('change', function () {
            var token = $(this).data('token');
            var file = this.files[0];
            // check file extension
            var ext = file.name.substring(file.name.lastIndexOf('.') + 1).toLowerCase();
            if (!(/(gif|jpe?g|png)$/i).test(ext)) {
                alert('You must select an image file only');
                return false;
            }
            // check file size
            if (file.size > 20971520) {
                alert('Max upload size is 20 MB');
                return false;
            } else {
                $('#player_img_link').hide();
                // upload new file
                var data = new FormData();
                data.append('Filedata', file);
                data.append('token', token);
                $.ajax({
                    url: 'index.php?action=editPlayerPicture',
                    data: data,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    type: 'POST',
                    success: function (json) {
                        if (typeof json.success !== 'undefined') {
                            $('#player_img_message').html(json.success);

                            $playerProfileImage.cropper('destroy');
                            canvasData = '';
                            cropBoxData = '';
                            $('#player_img_preview').attr('src', 'about:blank');

                            $('.player_img_profile_picture').attr('src', json.url);
                            $playerProfileImage.cropper({
                                aspectRatio: 1 / 1,
                                autoCropArea: 0.6,
                                ready: function () {
                                    var croppedCanvas = $playerProfileImage.cropper('getCroppedCanvas');
                                    var png = croppedCanvas.toDataURL("image/png");
                                    $('#player_img_preview').attr('src', png);
                                    $playerProfileImage.cropper('setCanvasData', canvasData);
                                    $playerProfileImage.cropper('setCropBoxData', cropBoxData);
                                }
                            }).on('cropend', function () {
                                var croppedCanvas = $playerProfileImage.cropper('getCroppedCanvas');
                                var png = croppedCanvas.toDataURL("image/png");
                                $('#player_img_preview').attr('src', png);
                                $playerProfileImage.cropper('setCanvasData', canvasData);
                                $playerProfileImage.cropper('setCropBoxData', cropBoxData);
                            }).on('zoom', function () {
                                var croppedCanvas = $playerProfileImage.cropper('getCroppedCanvas');
                                var png = croppedCanvas.toDataURL("image/png");
                                $('#player_img_preview').attr('src', png);
                                $playerProfileImage.cropper('setCanvasData', canvasData);
                                $playerProfileImage.cropper('setCropBoxData', cropBoxData);
                            });
                        }

                        if (typeof json.error !== 'undefined') {
                            $('#player_img_message').html(json.error);
                        }

                        $('#player_img_link').show();
                    }
                });
            }
        });

        $('#file_video_upload').on('change', function () {
            var token = $(this).data('token');
            var input = $(this);
            var numFiles = this.files.length;
            var fileType = '';
            var label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
            input.trigger('fileselect', [numFiles, label]);

            for (var i = 0; i < numFiles; i++) {
                var file = this.files[i];
                var filePath = file.name.replace(/\\/g, '/');
                var fileName = filePath.substring(filePath.lastIndexOf('/') + 1, filePath.lastIndexOf('.'));
                // check file extension
                var ext = file.name.substring(file.name.lastIndexOf('.') + 1).toLowerCase();
                if (!(/(gif|jpe?g|png|mpe?g|mp4|m4v|webm|wmv|3gp|3g2|flv|ogv|mov)$/i).test(ext)) {
                    alert('You must select an image/video file only ' + file.name);
                    return false;
                }
                if ((/(gif|jpe?g|png)$/i).test(ext)) {
                    fileType = '<i class="fa fa-picture-o" aria-hidden="true"></i> Image';
                } else {
                    fileType = '<i class="fa fa-film" aria-hidden="true"></i> Video';
                }
                // check file size
                if (file.size > 2147483648) {
                    alert('Max upload size is 2 GB');
                    return false;
                }
                var id = "progress_" + Math.floor((Math.random() * 100000));
                $('#mediaContainer').append('<div class="mediaFile"><span>' + fileType + '</span><p class="wordwrap">' + fileName + '</p><form><input class="input_box" name="mediaTitle" placeholder="Title"><textarea name="mediaDescription" class="input_box textarea_box" placeholder="Description"></textarea></form><div class="progress"><div id="' + id + '" class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">0%</div></div></div>');

                var data = new FormData();
                data.append('Filedata', file);
                data.append('token', token);
                data.append('progressId', id);

                // upload and display progress bar for each file
                $.when(uploadMedia(data, id)).done(function (json) {
                    //updateMedia(json);
                });

            } // end for

        }).on('fileselect', function (event, numFiles, label) {
            var input = $(this).parents('.input-group').find(':text');
            var log = numFiles > 1 ? numFiles + ' files selected' : label;
            if (input.length) {
                input.val(log);
            }
        });

        <?php endif; ?>

        loadTimeline(0);
        $('#player_timeline').scroll(function () {
            if ($("#timeline_loading").is(":hidden")) {
                if ($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
                    var page = $(".timeline_section").length;
                    loadTimeline(page);
                }
            }
        });

    }); // end doc.ready

    <?php if ($isAdmin === true): ?>

    function uploadMedia(data, id) {
        var progressBar = $('#' + id);
        var parent = progressBar.parent().parent();
        var progressBarContainer = parent.find('.progress');
        var form = parent.find('form');
        var mediaFileContainer = form.closest('.mediaFile');
        $.ajax({
            url: 'index.php?action=addPlayerMedia',
            data: data,
            dataType: 'json',
            processData: false,
            contentType: false,
            type: 'POST',
            timeout: 0,
            success: function (json) {

                $(progressBarContainer).remove();

                if (typeof json.success !== 'undefined') {
                    form.append('<button type="button" class="btn btn-success" data-progress="' + id + '" data-loading-text="<i class=\'fa fa-circle-o-notch fa-spin\'></i> Saving...">Update </button>');
                    if (json.url !== '') {
                        mediaFileContainer.find('p.wordwrap').html('<img class="img-thumbnail center-block" src="' + json.url + '">');
                    }
                    updateMedia(json);
                }

                if (typeof json.error !== 'undefined') {
                    // remove contents of block and display error
                    form.find('input[name="mediaTitle"]').remove();
                    form.find('input[name="mediaDate"]').remove();
                    form.find('textarea[name="mediaDescription"]').remove();
                    progressBarContainer.remove();
                    form.append('<h3>Error</h3><p>' + json.error + '</p>');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                form.find('input[name="mediaTitle"]').remove();
                form.find('input[name="mediaDate"]').remove();
                form.find('textarea[name="mediaDescription"]').remove();
                progressBarContainer.remove();
                form.append('<h3>Error</h3><p>' + textStatus + ': ' + errorThrown + '</p>');
            }
        }).uploadProgress(function (evt) {
            if (evt.lengthComputable) {
                var percentComplete = ((evt.loaded / evt.total) * 100);
                //console.log('Loaded: ' + percentComplete * 100);
                progressBar.attr('aria-valuenow', percentComplete);
                progressBar.text(
                    Math.floor(percentComplete) + '%'
                );
                progressBar.css({
                    width: percentComplete + '%'
                });
            }
        });
    }

    function updateMedia(json) {
        var button = $('button[data-progress="' + json.progressId + '"]');
        var form = button.closest('form');
        if (typeof json.success !== 'undefined') {
            button.click(function (e) {
                e.preventDefault();
                button.button('loading');
                var title = form.find('input[name="mediaTitle"]').val();
                var date = form.find('input[name="mediaDate"]').val();
                var description = form.find('textarea[name="mediaDescription"]').val();
                $.ajax({
                    url: 'index.php?action=editPlayerMedia',
                    data: {'title': title, 'date': date, 'description': description, 'mediaId': json.mediaId},
                    dataType: 'json',
                    type: 'POST',
                    success: function (response) {
                        if (typeof response.success !== 'undefined') {
                            button.button('reset');
                        }

                        if (typeof response.error !== 'undefined') {
                            alert(response.error);
                        }
                    }
                });
            });
        }

        if (typeof json.error !== 'undefined') {
            alert(json.error);
        }
    }

    <?php endif; ?>

    function loadTimeline(page) {
        $("#timeline_loading").show();
        $.ajax({
            url: "index.php?action=searchPlayerTimeline",
            type: "post",
            dataType: "json",
            data: {
                'page': page, 'token': $("#player_timeline").data('token')
            },
            success: function (data) {
                $.each(data, function (index, value) {
                    $("#results").append("<li id='" + index + "'>" + value + "</li>");
                });
                $("#loading").hide();
            }
        });
    }

    function formatPlayer(player) {
        if (!player.id) {
            return player.playerName;
        }

        var suggest = '';
        var isFriend = (player.playerIsFriend === true) ? '<span class="isFriendColor"> <span class="glyphicon glyphicon-star"></span> Friend</span>' : '';
        suggest += '<div class="row">';
        suggest += '<div class="col-sm-3 col-md-3 col-lg-2">';
        suggest += '<img src="' + player.playerPicture + '" alt="' + player.playerName + '" class="img-responsive" style="max-width:80px;">';
        suggest += '</div>';
        suggest += '<div class="col-sm-9 col-md-9 col-lg-10">';
        suggest += '<span>' + player.playerName + '</span>' + isFriend + '<br />';
        suggest += '<span>';
        if (player.playerSchool !== '') {
            suggest += ' ' + player.playerSchool;
        }
        if (player.playerCity !== '') {
            if (player.playerStateLong !== '') {
                suggest += ' <b>(' + player.playerCity + ',  ' + player.playerStateLong + ')</b>';
            } else {
                suggest += ' <b>(' + player.playerCity + ')</b>';
            }
        }
        suggest += '</span><br />';

        if (player.playerGradYear !== '') {
            suggest += '<span>Class of ' + player.playerGradYear + '</span><br />';
        }


        suggest += '</div></div>';
        suggest += '</div>';

        var $player = $(
            suggest
        );
        return $player;
    }

    function formatPlayerSelection(player) {
        // adjust for custom placeholder values
        if (!player.id) {
            return 'Search by Name, School, Grad Year, City, State and Email Address';
        }
        return player.playerName;
    }

    var win = $('#myModal7'),
        tab1 = $('#tab1'),
        page = 1,
        load = $('<img/>').attr('src', 'images/ajax-loader.gif').addClass('timeline_loader'),
        timeline = $('#player_timeline'),
        token = timeline.data('token');

    function getPlayerTimeline() {

        dettachScrollEvent();

        $.ajax({
            url: 'index.php?action=searchPlayerTimeline',
            data: {'page': page, 'token' : token},
            dataType: 'json',
            type: 'POST',
            beforeSend: function() {
                timeline.append(load);
            },
            success: function (json) {
                if (typeof json.success !== 'undefined') {
                    load.remove();
                    timeline.append(json.content);
                    page++;
                }

                if (typeof json.error !== 'undefined') {
                    //tab1.append('<p>No more activity found!</p>');
                    load.remove();
                }

                attachScrollEvent();
            }
        });
    }

    function attachScrollEvent() {
        win.scroll(function() {
            if (win.scrollTop() + win.height() > timeline.height() - 300) {
                getPlayerTimeline();
            }
        });
    }

    function dettachScrollEvent() {
        win.unbind('scroll');
    }

    getPlayerTimeline();
</script>

</body>
</html>
