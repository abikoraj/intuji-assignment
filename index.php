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

    if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
        $client->setAccessToken($_SESSION['access_token']);
        $service = new Google_Service_Calendar($client);

        // List events
        $calendarId = 'primary';

        try {
            $events = $service->events->listEvents($calendarId);
            echo '<h3>Upcoming Events</h3>';
            foreach ($events->getItems() as $event) {
                $startDateTime = $event->getStart()->getDateTime();
                $endDateTime = $event->getEnd()->getDateTime();

                if (!$startDateTime) {
                    $startDateTime = $event->getStart()->getDate();
                }
                if (!$endDateTime) {
                    $endDateTime = $event->getEnd()->getDate();
                }

                echo $event->getSummary() . ' - ' . date('Y-m-d h:i A', strtotime($startDateTime)) . ' to ' . date('Y-m-d h:i A', strtotime($endDateTime)) . '<br>';
            }
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }

    } else {
        $authUrl = $client->createAuthUrl();
        echo "<a href='$authUrl'>Connect to Google Calendar</a>";
    }
    ?>
</body>

</html>