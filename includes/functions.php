<?php


/*
 * Function to show an Alert type Message Box
 *
 * @param string $message   The Alert Message
 * @param string $icon      The Font Awesome Icon
 * @param string $type      The CSS style to apply
 * @return string           The Alert Box
 */
function alertBox($message, $icon = '', $type = '')
{
    return '<div class="alertMsg ' . $type . '"><span>' . $icon . '</span> ' . $message . ' <a class="alert-close" href="#">x</a></div>';
}

/*
 * 64 bit integers
 */
function milliseconds()
{
    $mt = explode(' ', microtime());
    return ((int)$mt[1]) * 1000 + ((int)round($mt[0] * 1000));
}

/*
 * 64 bit integers
 */
function microseconds()
{
    $mt = explode(' ', microtime());
    return ((int)$mt[1]) * 1000000 + ((int)round($mt[0] * 1000000));
}

// Function to get the client IP address
function getClientIp()
{
    $ip = '';

    if (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else if (isset($_SERVER['HTTP_X_FORWARDED'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED'];
    } else if (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_FORWARDED_FOR'];
    } else if (isset($_SERVER['HTTP_FORWARDED'])) {
        $ip = $_SERVER['HTTP_FORWARDED'];
    } else if (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    } else {
        $ip = 'UNKNOWN';
    }

    return $ip;
}

function generateRandomString($length = 16)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function validateDate($date, $format = 'Y-m-d H:i:s')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

/*
 * Function to show a message from type of alert
 *
 * @param integer $key      Message Type
 * @return string           Formatted Message
 */
function getAlertMessage($key)
{
    $alerts = array(
        "%NAME1% likes a link you shared.",
        "%NAME1% likes a link %NAME2% shared.",
        "%NAME1% followed %PLAYER1%",
        "%NAME1% requested admin rights for %PLAYER1%",
        "Admin rights granted to %NAME1% for %PLAYER1%",
        "%NAME1% commented on %NAME2%'s photo.",
        "%NAME1% liked %NAME2%'s photo.",
        "%NAME1% became friends with %NAME2%",
        "%NAME1% created an event"
    );

    return (isset($alerts[$key])) ? $alerts[$key] : false;
}

function getDesignation($key, $name)
{
    $designations = array(
        "I am %NAME%",
        "I am %NAME%'s Parent",
        "I am %NAME%'s Coach/Instructor",
        "I am %NAME% & I are Team Mates",
        "I am a Fan of %NAME%",
        "I want to invite %NAME% to events",
        "I am tracking %NAME%'s progress (recruiter)"
    );

    return (isset($designations[$key])) ? str_replace('%NAME%', $name, $designations[$key]) : false;
}

function getSimpleDesignation($key)
{
    $designations = array(
        "I am",
        "My Player!",
        "I'm coaching",
        "My Team",
        "I'm following",
        "Attendee",
        "I'm tracking"
    );

    return (isset($designations[$key])) ? $designations[$key] : false;
}

/*
 * Function to convert position number to position identifier
 *
 * @param integer $key field position number
 * @return string field position identifier
 * @return bool false on failure
 */

function getBaseballFieldPositionId($key)
{
    $positions = array(
        '',
        'P',
        'C',
        '1B',
        '2B',
        '3B',
        'SS',
        'LF',
        'CF',
        'RF'
    );

    return (isset($positions[$key])) ? $positions[$key] : false;
}

/*
 * Function to convert position number to position name
 *
 * @param integer $key field position number
 * @return string field position name
 * @return bool false on failure
 */

function getBaseballFieldPositionName($key)
{
    $positions = array(
        '',
        'Pitcher',
        'Catcher',
        '1st Baseman',
        '2nd Baseman',
        '3rd Baseman',
        'Short Stop',
        'Left Fielder',
        'Center Fielder',
        'Right Fielder'
    );

    return (isset($positions[$key])) ? $positions[$key] : false;
}

/*
 * Function to convert position identifier to position number
 *
 * @param integer $key field position number
 * @return string field position identifier
 */

function getBaseballFieldPositionNumber($id)
{
    $id = strtoupper($id);
    $positions = array(
        'P' => '1',
        'C' => '2',
        '1B' => '3',
        '2B' => '4',
        '3B' => '5',
        'SS' => '6',
        'LF' => '7',
        'CF' => '8',
        'RF' => '9'
    );

    return (isset($positions[$id])) ? $positions[$id] : '0';
}

/*
 * Function to convert HH:MM:SS.UU to decimal
 * (Hours:Minutes:Seconds.Microseconds)
 *
 * @param string $time HH:MM:SS.UU (or H:M:S.U)
 * @param string $time MM:SS.UU (or M:S.U)
 * @param string $time SS.UU (or S.U)
 * @return decimal seconds.microseconds
 * @return bool false on failure
 */
function hmsuToDecimal($time)
{
    $time = trim($time);
    $seconds = 0;
    $uSeconds = 0;
    $isValid = true;

    if (!empty($time)) {
        // check for semi-colon typo
        $time = str_replace(';', ':', $time);
        // filter invalid characters
        $time = preg_replace("/[^0-9:.]/", "", $time);
        // split by semicolon and decimal
        $hmsu = preg_split("/[:.]+/", $time);
        // check for decimal in the time input
        $hasUSecond = preg_match('/\./', $time);

        // determine string format
        if ($hasUSecond) {

            if (count($hmsu) == 2) {
                // user entered seconds.microseconds
                // Array ( [0] => 30 [1] => 375 )
                $_h = "00";
                $_m = "00";
                $_s = str_pad(substr($hmsu[0], 0, 2), 2, 0, STR_PAD_LEFT);
                if ($hmsu[0] > 60) {
                    // reformat total seconds to HH:MM:SS time string using gmdate() before using strtotime()
                    $dateString = gmdate("H:i:s", $_s);
                    $_total = strtotime($dateString) - strtotime('today');
                } else {
                    // parse seconds normally
                    $_total = strtotime($_h . ":" . $_m . ":" . $_s) - strtotime('today');
                }
                // check for negative value
                $seconds = ($_total > 0) ? $_total : 0;
                $uSeconds = $hmsu[1];
            } elseif (count($hmsu) == 3) {
                // user entered minutes:seconds.microseconds
                // Array ( [0] => 01 [1] => 30 [2] => 375 )
                $_h = "00";
                $_m = str_pad(substr($hmsu[0], 0, 2), 2, 0, STR_PAD_LEFT);

                $_s = str_pad(substr($hmsu[1], 0, 2), 2, 0, STR_PAD_LEFT);
                $_total = strtotime($_h . ":" . $_m . ":" . $_s) - strtotime('today');
                // check for negative value
                $seconds = ($_total > 0) ? $_total : 0;
                $uSeconds = $hmsu[2];
                // check for invalid input
                if ($_m > 60 || $_s > 60) {
                    $isValid = false;
                }

            } elseif (count($hmsu) == 4) {
                // Array ( [0] => 00 [1] => 01 [2] => 30 [3] => 375 )
                // user entered hours:minutes:seconds.microseconds
                $_h = str_pad(substr($hmsu[0], 0, 2), 2, 0, STR_PAD_LEFT);
                $_m = str_pad(substr($hmsu[1], 0, 2), 2, 0, STR_PAD_LEFT);
                $_s = str_pad(substr($hmsu[2], 0, 2), 2, 0, STR_PAD_LEFT);
                $_total = strtotime($_h . ":" . $_m . ":" . $_s) - strtotime('today');
                // check for negative value
                $seconds = ($_total > 0) ? $_total : 0;
                $uSeconds = $hmsu[3];
                // check for invalid input
                if ($_h > 60 || $_m > 60 || $_s > 60) {
                    $isValid = false;
                }

            } else {
                // user entered seconds
                $seconds = $time;
            }
        } else {

            // If there is no microsecond, then parse normally
            if (isset($hmsu[1]) && !isset($hmsu[2])) {
                // user entered minutes:seconds
                // Array ( [0] => 01 [1] => 30 )
                $_h = "00";
                $_m = str_pad(substr($hmsu[0], 0, 2), 2, 0, STR_PAD_LEFT);
                $_s = str_pad(substr($hmsu[1], 0, 2), 2, 0, STR_PAD_LEFT);
                // fixed for proper string formatting
                $_total = strtotime($_h . ":" . $_m . ":" . $_s) - strtotime('today');
                // check for negative value
                $seconds = ($_total > 0) ? $_total : 0;
                // check for invalid input
                if ($_m > 60 || $_s > 60) {
                    $isValid = false;
                }

            } elseif (isset($hmsu[2])) {
                // Array ( [0] => 00 [1] => 01 [2] => 30 )
                // user entered hours:minutes:seconds
                $_h = str_pad(substr($hmsu[0], 0, 2), 2, 0, STR_PAD_LEFT);
                $_m = str_pad(substr($hmsu[1], 0, 2), 2, 0, STR_PAD_LEFT);
                $_s = str_pad(substr($hmsu[2], 0, 2), 2, 0, STR_PAD_LEFT);
                // fixed for proper string formatting
                $_total = strtotime($_h . ":" . $_m . ":" . $_s) - strtotime('today');
                // check for negative value
                $seconds = ($_total > 0) ? $_total : 0;
                // check for invalid input
                if ($_h > 60 || $_m > 60 || $_s > 60) {
                    $isValid = false;
                }

            } else {
                // user entered seconds
                $seconds = $time;
            }
        }
    }

    return ($isValid) ? $seconds . '.' . $uSeconds : false;
}

/*
 * Function to convert seconds to H:M:S.UUU (optional thousandths of second)
 *
 * @param integer $seconds
 * @return string $time H:M:S.UUU
 * @return bool false on failure
 */
function secondsToHMSU($seconds)
{
    $dateString = gmdate("H:i:s", $seconds);
    $uSec = '';
    if (preg_match('/\./', $seconds)) {
        $_u = preg_split("/[.]+/", $seconds);
        if ($_u > 0) {
            $uSec = '.' . substr($_u[1], 0, 3);
        }
        $dateString = gmdate("H:i:s", $_u[0]);
        //ltrim($dateString, '00');
    }

    return ($dateString !== false && $seconds > 0) ? $dateString . $uSec : '00:00:00.000';
}

/*
 * Check if stats exist for a sport
 *
 * @param array $sportArray a $_POST array
 * @param string $sportName lowercase sport name
 * @return bool
 */
function hasSportStat($sportArray, $sportName)
{
    $hasStat = false;
    foreach ($sportArray as $key => $value) {
        if (strpos($key, $sportName) !== false) {
            $value = trim($value);
            if (!empty($value)) {
                $hasStat = true;
                break;
            }
        }
    }
    return $hasStat;
}

function getSportNumber($id)
{
    $sports = array(
        'baseball' => '1',
        'crossfit' => '2',
        'fastpitch' => '3',
        'lacrosse' => '4',
        'swimming' => '5'
    );

    return (isset($sports[$id])) ? $sports[$id] : '0';
}

function getSportName($id)
{
    $sports = array(
        '',
        'Baseball',
        'Cross Fit',
        'Fast Pitch',
        'Lacrosse',
        'Swimming'
    );

    return (isset($sports[$id])) ? $sports[$id] : false;
}

