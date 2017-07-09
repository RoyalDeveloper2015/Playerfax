<?php

if (!isset($_SESSION['userId'])) {
    header('Location: index.php?page=login');
    exit;
}

$states = array(
    'AL' => 'Alabama',
    'AK' => 'Alaska',
    'AZ' => 'Arizona',
    'AR' => 'Arkansas',
    'CA' => 'California',
    'CO' => 'Colorado',
    'CT' => 'Connecticut',
    'DE' => 'Delaware',
    'DC' => 'District Of Columbia',
    'FL' => 'Florida',
    'GA' => 'Georgia',
    'HI' => 'Hawaii',
    'ID' => 'Idaho',
    'IL' => 'Illinois',
    'IN' => 'Indiana',
    'IA' => 'Iowa',
    'KS' => 'Kansas',
    'KY' => 'Kentucky',
    'LA' => 'Louisiana',
    'ME' => 'Maine',
    'MD' => 'Maryland',
    'MA' => 'Massachusetts',
    'MI' => 'Michigan',
    'MN' => 'Minnesota',
    'MS' => 'Mississippi',
    'MO' => 'Missouri',
    'MT' => 'Montana',
    'NE' => 'Nebraska',
    'NV' => 'Nevada',
    'NH' => 'New Hampshire',
    'NJ' => 'New Jersey',
    'NM' => 'New Mexico',
    'NY' => 'New York',
    'NC' => 'North Carolina',
    'ND' => 'North Dakota',
    'OH' => 'Ohio',
    'OK' => 'Oklahoma',
    'OR' => 'Oregon',
    'PA' => 'Pennsylvania',
    'RI' => 'Rhode Island',
    'SC' => 'South Carolina',
    'SD' => 'South Dakota',
    'TN' => 'Tennessee',
    'TX' => 'Texas',
    'UT' => 'Utah',
    'VT' => 'Vermont',
    'VA' => 'Virginia',
    'WA' => 'Washington',
    'WV' => 'West Virginia',
    'WI' => 'Wisconsin',
    'WY' => 'Wyoming',
    'AS' => 'American Samoa',
    'GU' => 'Guam',
    'MP' => 'Northern Mariana Islands',
    'PR' => 'Puerto Rico',
    'UM' => 'United States Minor Outlying Islands',
    'VI' => 'Virgin Islands',
    'AA' => 'Armed Forces Americas',
    'AP' => 'Armed Forces Pacific',
    'AE' => 'Armed Forces Others'
);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Playerfax</title>
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

</head>
<body class="user_home" id="portrait">
<?php require 'includes/header.php'; ?>
<section class="user_profile_main">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="btns_bottom_text">
                    <span></span><!-- Create new athlete -->
                    <a href="#" data-dismiss="modal" data-toggle="modal" data-target="#addPlayer" class="create_player">Lets Create</a>
                   <!-- <p>*You must be the athlete or the athlete's parent or guardian to create a <span>PlayerFax Player Profile</span></p>-->
                </div>
                <br>
                <br>
                <div class="title">
                    <h4>MY ATHLETES</h4>
                </div>
                <div class="team_section">
                    <div class="team_members">
                        <ul>
                            <?php
                            $count = 0;
                            $topPlayers = '';
                            // Get all players I created

                            // Select Player data
                            try {
                                $sql_player = "
                                SELECT 
                                  `Players`.`PlayerId`,
                                  `Players`.`UserId` AS `PlayerUserId`,
                                  `Players`.`LastUpdated` AS `PlayerLastUpdated`,
                                  `Players`.`Picture` AS `PlayerPicture`,
                                  `Players`.`Gender` AS `PlayerGender`,
                                  `Players`.`FirstName` AS `PlayerFirstName`,
                                  `Players`.`LastName` AS `PlayerLastName`,
                                  `Players`.`Designation` AS `PlayerDesignation`,
                                  `Players`.`Token` AS `PlayerToken`
                                FROM 
                                  `Players`
                                WHERE
                                  `Players`.`UserId` = :UserId";

                                $stmt_player = $PDO->prepare($sql_player);
                                $stmt_player->bindParam('UserId', $userId, PDO::PARAM_INT); // logged-in user
                                $stmt_player->execute();

                                while ($row_player = $stmt_player->fetch(PDO::FETCH_ASSOC)) {
                                    $count++;
                                    $topPlayers .= '<li>';

                                    $topPlayers .= '<div class="designation"><p>' . getSimpleDesignation($row_player['PlayerDesignation']) . '</p></div>';

                                    if (!empty($row_player['PlayerPicture']) && file_exists(constant('UPLOADS_PLAYERS') . $row_player['PlayerPicture'])) {
                                        $topPlayers .= '<div class="img_preview"><div class="img-circle"><img src="' . constant('UPLOADS_PLAYERS') . $row_player['PlayerPicture'] . '" alt="' . trim($row_player['PlayerFirstName'] . ' ' . $row_player['PlayerLastName']) . '"/></div></div>';
                                    } else {
                                        if ($row_player['PlayerGender'] == 0) {
                                            $topPlayers .= '<div class="img_preview"><div class="img-circle"><img src="' . constant('UPLOADS_PLAYERS') . 'default-male.png" alt="' . trim($row_player['PlayerFirstName'] . ' ' . $row_player['PlayerLastName']) . '"/></div></div>';
                                        } else {
                                            $topPlayers .= '<div class="img_preview"><div class="img-circle"><img src="' . constant('UPLOADS_PLAYERS') . 'default-female.png" alt="' . trim($row_player['PlayerFirstName'] . ' ' . $row_player['PlayerLastName']) . '"/></div></div>';
                                        }
                                    }

                                    $topPlayers .= '<div class="member_name"><a href="index.php?page=player&token=' . $row_player['PlayerToken'] . '">' . trim($row_player['PlayerFirstName'] . ' ' . $row_player['PlayerLastName']) . '</a></div>';
                                    $topPlayers .= '</li>';

                                }

                            } catch (PDOException $e) {
                                trigger_error($e->getMessage(), E_USER_ERROR);
                            }//end try


                            echo $topPlayers;
                            ?>
                        </ul>
                        <?php
                        // if you have no players or have not "followed" any players, then say so.
                        if ($count == 0) {
                            echo alertBox("You have no players", "<i class='fa fa-times'></i>", "info");
                        }

                        ?>
                    </div>
                </div>
                <div class="title">
                    <h4>ATHLETES FOLLOWING</h4>
                </div>
                <div class="team_section">
                    <div class="team_members">
                        <ul>
                            <?php
                            $count = 0;
                            $topPlayers = '';
                            // Get all players I "followed", except my players
                            try {
                                $sql_follow = "
                                SELECT 
                                  `Follows`.`FollowId`,
                                  `Follows`.`Designation` AS `FollowDesignation`,
                                  `Follows`.`UserId` AS `FollowUserId`,
                                  `Follows`.`UserIdFrom` AS `FollowUserIdFrom`,
                                  `Follows`.`PlayerId` AS `FollowPlayerId`
                                FROM 
                                  `Follows`
                                WHERE
                                  `Follows`.`UserIdFrom` = :UserIdFrom";

                                $stmt_follow = $PDO->prepare($sql_follow);
                                $stmt_follow->bindParam('UserIdFrom', $userId, PDO::PARAM_INT); // logged-in user
                                $stmt_follow->execute();

                                while ($row_follow = $stmt_follow->fetch(PDO::FETCH_ASSOC)) {

                                    // Select Player data
                                    try {
                                        $sql_player = "
                                        SELECT 
                                          `Players`.`PlayerId`,
                                          `Players`.`UserId` AS `PlayerUserId`,
                                          `Players`.`LastUpdated` AS `PlayerLastUpdated`,
                                          `Players`.`Picture` AS `PlayerPicture`,
                                          `Players`.`Gender` AS `PlayerGender`,
                                          `Players`.`FirstName` AS `PlayerFirstName`,
                                          `Players`.`LastName` AS `PlayerLastName`,
                                          `Players`.`Designation` AS `PlayerDesignation`,
                                          `Players`.`Token` AS `PlayerToken`
                                        FROM 
                                          `Players`
                                        WHERE
                                          `Players`.`PlayerId` = :PlayerId";

                                        $stmt_player = $PDO->prepare($sql_player);
                                        $stmt_player->bindParam('PlayerId', $row_follow['FollowPlayerId'], PDO::PARAM_INT); // logged-in user
                                        $stmt_player->execute();

                                        while ($row_player = $stmt_player->fetch(PDO::FETCH_ASSOC)) {
                                            $count++;
                                            $topPlayers .= '<li>';
                                            if ($row_follow['FollowUserIdFrom'] == $userId) {
                                                // if I "followed" my own player
                                                $topPlayers .= '<div class="designation"><p>' . getSimpleDesignation($row_follow['FollowDesignation']) . '</p></div>';
                                            } else {
                                                // if I "followed" someone else's player
                                                $topPlayers .= '<div class="designation"><p>' . getSimpleDesignation($row_follow['FollowDesignation']) . '</p></div>';
                                            }

                                            if (!empty($row_player['PlayerPicture']) && file_exists(constant('UPLOADS_PLAYERS') . $row_player['PlayerPicture'])) {
                                                $topPlayers .= '<div class="img_preview"><div class="img-circle"><img src="' . constant('UPLOADS_PLAYERS') . $row_player['PlayerPicture'] . '" alt="' . trim($row_player['PlayerFirstName'] . ' ' . $row_player['PlayerLastName']) . '"/></div></div>';
                                            } else {
                                                if ($row_player['PlayerGender'] == 0) {
                                                    $topPlayers .= '<div class="img_preview"><div class="img-circle"><img src="' . constant('UPLOADS_PLAYERS') . 'default-male.png" alt="' . trim($row_player['PlayerFirstName'] . ' ' . $row_player['PlayerLastName']) . '"/></div></div>';
                                                } else {
                                                    $topPlayers .= '<div class="img_preview"><div class="img-circle"><img src="' . constant('UPLOADS_PLAYERS') . 'default-female.png" alt="' . trim($row_player['PlayerFirstName'] . ' ' . $row_player['PlayerLastName']) . '"/></div></div>';
                                                }
                                            }

                                            $topPlayers .= '<div class="member_name"><a href="index.php?page=player&token=' . $row_player['PlayerToken'] . '">' . trim($row_player['PlayerFirstName'] . ' ' . $row_player['PlayerLastName']) . '</a></div>';
                                            $topPlayers .= '</li>';

                                        }

                                    } catch (PDOException $e) {
                                        trigger_error($e->getMessage(), E_USER_ERROR);
                                    }//end try
                                }

                            } catch (PDOException $e) {
                                trigger_error($e->getMessage(), E_USER_ERROR);
                            }//end try

                            echo $topPlayers;
                            ?>
                        </ul>
                        <?php
                        // if you have no players or have not "followed" any players, then say so.
                        if ($count == 0) {
                            echo alertBox("You are not following anyone", "<i class='fa fa-times'></i>", "info");
                        }

                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require 'includes/footer.php'; ?>

<!--Add Player -->
<div id="addPlayer" class="modal fade forget_section" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="container">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="close_img">
                        <button type="button" class="close" data-dismiss="modal"></button>
                    </div>
                    <div class="title_h2">
                        <h2>Create Athlete</h2>
                    </div>
                </div>
                <div class="modal-body">
                    <form id="addPlayerForm" class="form-horizontal">
                        <div class="row">
                            <div class="col-sm-12">
                                <div id="addPlayerErrorBox">
                                    <div class="alertMsg info">
                                        <span><i class="fa fa-check-square-o"></i></span> Fill in all required fields <a class="alert-close" href="#">x</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <select name="playerGender" class="form-control input_box">
                                    <option value="">Select Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <input type="text" name="playerFirstName" placeholder="First Name" class="form-control input_box">
                            </div>
                            <div class="col-sm-4">
                                <input type="text" name="playerMiddleName" placeholder="Middle Name" class="form-control input_box">
                            </div>
                            <div class="col-sm-4">
                                <input type="text" name="playerLastName" placeholder="Last Name" class="form-control input_box">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-8">
                                <input type="text" name="playerEmail" placeholder="Email" class="form-control input_box">
                            </div>
                            <div class="col-sm-4">
                                <input type="text" name="playerDOB" placeholder="DOB (MM/DD/YYYY)" class="form-control input_box">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-8">
                                <input type="text" name="playerSchool" placeholder="Full Name Of School" class="form-control input_box">
                            </div>
                            <div class="col-sm-4">
                                <select id="playerGradYear" class="form-control input_box">
                                    <option value="">Graduation year</option>
                                    <?php

                                    $years = array_combine(range(date("Y", strtotime('+20 Years')), 1910), range(date("Y", strtotime('+20 Years')), 1910));
                                    foreach ($years as $k => $v) {
                                        echo '<option value="' . $k . '">' . $v . '</option>';
                                    }

                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <select id="searchCityQuery" class="form-control" style="width: 100%;">
                                    <option></option>
                                </select>
                                <input type="hidden" name="playerCity">
                            </div>
                            <div class="col-sm-6">
                                <select name="playerStateShort" class="form-control input_box">
                                    <option value="">Select State</option>
                                    <option value="AL">Alabama</option>
                                    <option value="AK">Alaska</option>
                                    <option value="AZ">Arizona</option>
                                    <option value="AR">Arkansas</option>
                                    <option value="CA">California</option>
                                    <option value="CO">Colorado</option>
                                    <option value="CT">Connecticut</option>
                                    <option value="DE">Delaware</option>
                                    <option value="DC">District Of Columbia</option>
                                    <option value="FL">Florida</option>
                                    <option value="GA">Georgia</option>
                                    <option value="HI">Hawaii</option>
                                    <option value="ID">Idaho</option>
                                    <option value="IL">Illinois</option>
                                    <option value="IN">Indiana</option>
                                    <option value="IA">Iowa</option>
                                    <option value="KS">Kansas</option>
                                    <option value="KY">Kentucky</option>
                                    <option value="LA">Louisiana</option>
                                    <option value="ME">Maine</option>
                                    <option value="MD">Maryland</option>
                                    <option value="MA">Massachusetts</option>
                                    <option value="MI">Michigan</option>
                                    <option value="MN">Minnesota</option>
                                    <option value="MS">Mississippi</option>
                                    <option value="MO">Missouri</option>
                                    <option value="MT">Montana</option>
                                    <option value="NE">Nebraska</option>
                                    <option value="NV">Nevada</option>
                                    <option value="NH">New Hampshire</option>
                                    <option value="NJ">New Jersey</option>
                                    <option value="NM">New Mexico</option>
                                    <option value="NY">New York</option>
                                    <option value="NC">North Carolina</option>
                                    <option value="ND">North Dakota</option>
                                    <option value="OH">Ohio</option>
                                    <option value="OK">Oklahoma</option>
                                    <option value="OR">Oregon</option>
                                    <option value="PA">Pennsylvania</option>
                                    <option value="RI">Rhode Island</option>
                                    <option value="SC">South Carolina</option>
                                    <option value="SD">South Dakota</option>
                                    <option value="TN">Tennessee</option>
                                    <option value="TX">Texas</option>
                                    <option value="UT">Utah</option>
                                    <option value="VT">Vermont</option>
                                    <option value="VA">Virginia</option>
                                    <option value="WA">Washington</option>
                                    <option value="WV">West Virginia</option>
                                    <option value="WI">Wisconsin</option>
                                    <option value="WY">Wyoming</option>
                                    <option value="AS">American Samoa</option>
                                    <option value="GU">Guam</option>
                                    <option value="MP">Northern Mariana Islands</option>
                                    <option value="PR">Puerto Rico</option>
                                    <option value="UM">United States Minor Outlying Islands</option>
                                    <option value="VI">Virgin Islands</option>
                                    <option value="AA">Armed Forces Americas</option>
                                    <option value="AP">Armed Forces Pacific</option>
                                    <option value="AE">Armed Forces Others</option>
                                </select>
                            </div>
                        </div>
                        <h5>Choose your relationship to the player:</h5>
                        <label class="radio-inline"><input type="radio" name="playerDesignation" id="playerType1" value="1"> Parent</label>
                        <label class="radio-inline"><input type="radio" name="playerDesignation" id="playerType2" value="2"> Coach</label>
                        <label class="radio-inline"><input type="radio" name="playerDesignation" id="playerType3" value="0" checked="checked"> Player</label>
                        <label class="radio-inline"><input type="radio" name="playerDesignation" id="playerType4" value="3"> Team</label>
                        <label class="radio-inline"><input type="radio" name="playerDesignation" id="playerType5" value="4"> Fan</label>
                        <div class="btns_bottom_text">
                            <p>
                                *You must be the player or the players parent or guardian to create a <span>PlayerFax Player Profile</span>
                                You will be the owner of this <span>PlayerFax Player Profile.</span> You will be able to share admin rights
                                with other users, only admins will be able to add stats & sports to a <span>PlayerFax Player Profile.</span>
                            </p>
                        </div>
                        <div class="all_btn">
                            <button id="createPlayer" type="submit" name="submit" value="submit" class="btn btn-success" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Please wait..."><i class="fa fa-check"></i> Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Add Player-->

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
<script type="text/javascript" src="js/jquery.storage.js"></script>
<script type="text/javascript" src="js/cropper-min.js"></script>
<script type="text/javascript" src="js/custom.js"></script>

<script>

    var isComplete = false;

    $(document).ready(function () {

        $('input.date').each(function () {
            var opts = {};
            opts.format = 'mm/dd/yyyy';
            $(this).datepicker(opts);
        });

        $('input.datetime').each(function () {
            var opts = {};
            var currentValue = moment($(this).val());
            if (currentValue.isValid()) {
                opts.defaultDate = currentValue;
            }
            $(this).datetimepicker(opts);
        });

        $('#addPlayerForm').on('submit', function (e) {
            if (isComplete === false) {
                $('#createPlayer').button('loading');
                isComplete = true;
                $.ajax({
                    url: 'index.php?action=addPlayer',
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
                        $('#createPlayer').button('reset');
                    }
                });
            }
            e.preventDefault(); //STOP default action
            //e.unbind();
        });

        $('#searchCityQuery').select2({
            theme: "bootstrap",
            dropdownParent: $("#addPlayer"),
            placeholder: "Full City Name",
            allowClear: true,
            minimumInputLength: 3,
            closeOnSelect: true,
            selectOnBlur: true,
            tags: true,
            ajax: {
                url: "index.php?action=searchForCity",
                dataType: "json",
                width: 'style',
                delay: 300,
                type: 'POST',
                data: function (params) {
                    return {
                        query: params.term,
                        page: params.page,
                        per_page: 10
                    };
                },
                processResults: function (data, params) {
                    return {
                        results: data.results.cities.map(function (city) {
                            return {
                                id: city.Id,
                                text: city.Name
                            };
                        })
                    };
                },
                cache: true
            }
        }).on('select2:select', function (e) {
            var cityName = $('#select2-searchCityQuery-container').attr('title');
            $('input[name="playerCity"]').val(cityName);

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

    }); // end doc.ready

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

</script>
</body>
</html>