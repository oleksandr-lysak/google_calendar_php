<?php

class EventDurationCalculator {
    /**
     * @param array $events
     * @return string
     * @throws Exception
     */
    public function calculateTotalOccupiedTime(array $events): string
    {
        if (empty($events)) {
            return '00:00';
        }

        // Sort events by start time
        $intervals = [];
        foreach ($events as $event) {
            $start = new DateTime($event->start->dateTime);
            $end = new DateTime($event->end->dateTime);
            $intervals[] = ['start' => $start, 'end' => $end];
        }

        usort($intervals, function($a, $b) {
            return $a['start'] <=> $b['start'];
        });

        // Union of intervals
        $mergedIntervals = [];
        $currentStart = $intervals[0]['start'];
        $currentEnd = $intervals[0]['end'];

        foreach ($intervals as $interval) {
            if ($interval['start'] <= $currentEnd) {
                $currentEnd = max($currentEnd, $interval['end']);
            } else {
                $mergedIntervals[] = ['start' => $currentStart, 'end' => $currentEnd];
                $currentStart = $interval['start'];
                $currentEnd = $interval['end'];
            }
        }
        $mergedIntervals[] = ['start' => $currentStart, 'end' => $currentEnd];

        // Calculate total time
        $totalSeconds = 0;
        foreach ($mergedIntervals as $interval) {
            $totalSeconds += $interval['end']->getTimestamp() - $interval['start']->getTimestamp();
        }

        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds / 60) % 60);

        return sprintf('%02d:%02d', $hours, $minutes);
    }
}