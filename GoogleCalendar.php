<?php
require 'vendor/autoload.php';

use Google\Client;
use Google\Service\Calendar;

class GoogleCalendar {
    private $service;

    public function __construct($apiKey) {
        $client = new Client();
        $client->setDeveloperKey($apiKey);
        $this->service = new Calendar($client);
    }

    /**
     * @param String $calendarId
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return array
     * @throws \Google\Service\Exception
     */
    public function getWeeklyEvents(String $calendarId, DateTime $startDate, DateTime $endDate): array
    {
        $optParams = array(
            'timeMin' => $startDate->format(DateTime::RFC3339),
            'timeMax' => $endDate->format(DateTime::RFC3339),
            'singleEvents' => true,
            'orderBy' => 'startTime',
        );
        $results = $this->service->events->listEvents($calendarId, $optParams);
        return $results->getItems();
    }
}

