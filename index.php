<!DOCTYPE html>
<html>

<head>
    <title>Google Calendar API</title>
</head>

<body>

    <?php
    require_once 'vendor/autoload.php';

    session_start();

    $client = new Google_Client();
    $client->setAuthConfig('credentials.json');
    $client->setRedirectUri('http://localhost:8000/index.php');
    $client->addScope(Google_Service_Calendar::CALENDAR);

    if (isset($_GET['code'])) {
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        if (isset($token['error'])) {
            echo 'Error fetching access token: ' . $token['error'];
            exit;
        }
        $_SESSION['access_token'] = $token;
        header('Location: ' . filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_URL));
        exit;
    }

    ?>
</body>

</html>