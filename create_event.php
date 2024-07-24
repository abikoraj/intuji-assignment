<?php
require_once 'vendor/autoload.php';

session_start();

if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    $client = new Google_Client();
    $client->setAccessToken($_SESSION['access_token']);
    $service = new Google_Service_Calendar($client);
    $event = new Google_Service_Calendar_Event([
        'summary' => $_POST['summary'],
        'start' => [
            'dateTime' => date('c', strtotime($_POST['start'])),
            'timeZone' => 'Asia/Kathmandu'
        ],
        'end' => [
            'dateTime' => date('c', strtotime($_POST['end'])),
            'timeZone' => 'Asia/Kathmandu'
        ],
    ]);

    $calendarId = 'primary';
    try {
        $service->events->insert($calendarId, $event);
        header('Location: index.php');
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
} else {
    echo 'No access token found. Please connect to Google Calendar.';
}

?>
