<?php
require 'vendor/autoload.php';
require 'GoogleCalendar.php';
require 'EventDurationCalculator.php';

use Dotenv\Dotenv;
use Google\Service\Exception as GoogleException;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Get environment variables
$apiKey = $_ENV['API_KEY'];
$calendarId = $_ENV['CALENDAR_ID'];

//calculate start and end dates of the current week
$startDate = (new DateTime())
    ->setISODate((new DateTime())->format('o'), (new DateTime())->format('W'))
    ->setTime(0, 0);

$endDate = (clone $startDate)
    ->modify('+6 days')
    ->setTime(23, 59, 59);

// Get events from Google Calendar
$googleCalendar = new GoogleCalendar($apiKey);
try {
    $events = $googleCalendar->getWeeklyEvents($calendarId, $startDate, $endDate);
} catch (GoogleException $e) {
    echo $e->getMessage();
    return;
}

// Calculate total occupied time
$calculator = new EventDurationCalculator();
try {
    $totalOccupiedTime = $calculator->calculateTotalOccupiedTime($events);
    echo "Total occupied time this week: " . $totalOccupiedTime . "\n";
} catch (Exception $e) {
    echo $e->getMessage();
}


