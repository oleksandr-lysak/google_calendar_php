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
$startDate = new DateTime();
$startDate->setISODate($startDate->format('o'), $startDate->format('W'), 1); // Понеділок поточного тижня
$startDateFormatted = $startDate->format(DateTime::RFC3339);

$endDate = clone $startDate;
$endDate->modify('+6 days'); // Неділя поточного тижня
$endDateFormatted = $endDate->format(DateTime::RFC3339);

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


