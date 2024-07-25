<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Calendar API</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 min-h-screen">
    <nav class="bg-blue-600 p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <div class="text-white text-lg font-bold">Google Calendar Integration</div>
            <button type="button" class="text-white hover:text-red-700 border border-white-700 hover:bg-white focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2">Disconnect</button>
        </div>
    </nav>

    <div class="container mx-auto p-8 space-y-8">

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
                echo '<div class="text-red-500">Error fetching access token: ' . htmlspecialchars($token['error']) . '</div>';
                exit;
            }
            $_SESSION['access_token'] = $token;
            header('Location: ' . filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_URL));
            exit;
        }

        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            $client->setAccessToken($_SESSION['access_token']);
            $service = new Google_Service_Calendar($client);

            // Create event container
            echo '<div class="bg-white p-6 shadow-lg rounded-lg">';
            echo '<h3 class="text-xl font-bold mb-4">Create Event</h3>';
            echo '<form method="POST" action="create_event.php" class="flex flex-wrap space-x-4 items-center">
                    <input type="text" name="summary" placeholder="Event Summary" required class="w-full md:w-1/4 p-2 border border-gray-300 rounded mb-2 md:mb-0">
                    <input type="datetime-local" name="start" required class="w-full md:w-1/4 p-2 border border-gray-300 rounded mb-2 md:mb-0">
                    <input type="datetime-local" name="end" required class="w-full md:w-1/4 p-2 border border-gray-300 rounded mb-2 md:mb-0">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white py-2 px-4 rounded">Create Event</button>
                  </form>';
            echo '</div>';

            // List events container
            echo '<div class="rounded-lg">';
            echo '<h3 class="text-xl font-bold mb-4">Upcoming Events</h3>';

            // List events
            $calendarId = 'primary';

            try {
                $events = $service->events->listEvents($calendarId);
                echo '<div class="space-y-4">';
                foreach ($events->getItems() as $event) {
                    $startDateTime = $event->getStart()->getDateTime();
                    $endDateTime = $event->getEnd()->getDateTime();

                    if (!$startDateTime) {
                        $startDateTime = $event->getStart()->getDate();
                    }
                    if (!$endDateTime) {
                        $endDateTime = $event->getEnd()->getDate();
                    }

                    echo '<div class="p-4 shadow-md hover:shadow-lg bg-gray-50 border border-gray-300 rounded flex justify-between items-center">';
                    echo '<div>';
                    echo '<p class="font-semibold">' . htmlspecialchars($event->getSummary()) . '</p>';
                    echo '<p class="text-gray-600">' . date('Y-m-d h:i A', strtotime($startDateTime)) . ' to ' . date('Y-m-d h:i A', strtotime($endDateTime)) . '</p>';
                    echo '</div>';
                    echo '<div>';
                    echo '<form method="POST" action="delete_event.php" class="inline-block">';
                    echo '<input type="hidden" name="event_id" value="' . htmlspecialchars($event->getId()) . '">';
                    echo '<button type="submit" class="bg-red-500 hover:bg-red-700 text-white py-1 px-3 rounded">Delete</button>';
                    echo '</form>';
                    echo '</div>';
                    echo '</div>';
                }
                echo '</div>';
            } catch (Exception $e) {
                echo '<div class="text-red-500">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
            echo '</div>';
        } else {
            $authUrl = $client->createAuthUrl();
            echo "<a href='" . htmlspecialchars($authUrl) . "' class='bg-blue-500 text-white py-2 px-4 rounded'>Connect to Google Calendar</a>";
        }
        ?>

    </div>
</body>

</html>