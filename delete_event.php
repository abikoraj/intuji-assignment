<?php
require_once 'vendor/autoload.php';

session_start();

if (isset($_POST['event_id'])) {
    $eventId = $_POST['event_id'];

    $client = new Google_Client();
    $client->setAuthConfig('credentials.json');
    $client->setRedirectUri('http://localhost:8000/index.php');
    $client->addScope(Google_Service_Calendar::CALENDAR);

    if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
        $client->setAccessToken($_SESSION['access_token']);
        $service = new Google_Service_Calendar($client);

        try {
            $service->events->delete('primary', $eventId);
            header('Location: index.php');
            exit;
        } catch (Exception $e) {
            echo '<div class="text-red-500">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    } else {
        echo '<div class="text-red-500">Error: Access token is missing or invalid.</div>';
    }
} else {
    echo '<div class="text-red-500">Error: Event ID is missing.</div>';
}
?>
