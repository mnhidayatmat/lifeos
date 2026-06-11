<?php

namespace App\Http\Controllers;

use App\Models\ImportantDate;
use App\Models\User;
use Illuminate\Http\Response;

class CalendarController extends Controller
{
    /**
     * Public iCalendar (.ics) feed for a user's important dates.
     *
     * Authenticated by the secret token in the URL, so this route lives
     * OUTSIDE the auth middleware — calendar apps fetch it without a session.
     * Subscribe to it once in Google / Apple / Outlook and every date
     * (plus its reminders, as VALARMs) syncs and auto-updates.
     */
    public function feed(string $token): Response
    {
        $user = User::where('calendar_token', $token)->firstOrFail();

        $dates = $user->importantDates()->get();

        $ics = $this->buildCalendar($user, $dates);

        return response($ics, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'inline; filename="lifeos.ics"',
            'Cache-Control' => 'no-cache, must-revalidate',
        ]);
    }

    private function buildCalendar(User $user, $dates): string
    {
        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//LifeOS//Important Dates//EN',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'X-WR-CALNAME:'.$this->escape($user->name.' — Important Dates'),
            'X-WR-CALDESC:Important deadlines from LifeOS',
        ];

        foreach ($dates as $date) {
            $lines = array_merge($lines, $this->buildEvent($date));
        }

        $lines[] = 'END:VCALENDAR';

        // Fold long lines and join with CRLF per RFC 5545.
        return collect($lines)
            ->map(fn ($line) => $this->fold($line))
            ->implode("\r\n")."\r\n";
    }

    private function buildEvent(ImportantDate $date): array
    {
        // DTSTAMP must be a fixed UTC instant; updated_at keeps it stable per edit.
        $stamp = $date->updated_at->copy()->utc()->format('Ymd\THis\Z');

        $event = [
            'BEGIN:VEVENT',
            'UID:important-date-'.$date->id.'@lifeos',
            'DTSTAMP:'.$stamp,
            'SUMMARY:'.$this->escape($date->title),
        ];

        if ($date->all_day) {
            $start = $date->date->copy()->startOfDay();
            $event[] = 'DTSTART;VALUE=DATE:'.$start->format('Ymd');
            $event[] = 'DTEND;VALUE=DATE:'.$start->copy()->addDay()->format('Ymd');
        } else {
            // Floating local time (no TZID) — interpreted in the viewer's own zone.
            $start = $date->date->copy()->setTimeFromTimeString($date->time);
            $event[] = 'DTSTART:'.$start->format('Ymd\THis');
            $event[] = 'DTEND:'.$start->copy()->addHour()->format('Ymd\THis');
        }

        if ($date->recurrence === 'yearly') {
            $event[] = 'RRULE:FREQ=YEARLY';
        } elseif ($date->recurrence === 'monthly') {
            $event[] = 'RRULE:FREQ=MONTHLY';
        }

        if ($date->description) {
            $event[] = 'DESCRIPTION:'.$this->escape($date->description);
        }

        if ($date->lifeArea) {
            $event[] = 'CATEGORIES:'.$this->escape($date->lifeArea->name);
        }

        // Each reminder offset becomes a VALARM, so the calendar app notifies natively.
        foreach ($date->reminderOffsets() as $daysBefore) {
            $trigger = $daysBefore === 0 ? '-PT0S' : '-P'.$daysBefore.'D';
            $event[] = 'BEGIN:VALARM';
            $event[] = 'ACTION:DISPLAY';
            $event[] = 'DESCRIPTION:'.$this->escape($date->title);
            $event[] = 'TRIGGER:'.$trigger;
            $event[] = 'END:VALARM';
        }

        $event[] = 'END:VEVENT';

        return $event;
    }

    /** Escape text per RFC 5545 (backslash, semicolon, comma, newline). */
    private function escape(string $text): string
    {
        return str_replace(
            ['\\', ';', ',', "\r\n", "\n", "\r"],
            ['\\\\', '\\;', '\\,', '\\n', '\\n', '\\n'],
            $text
        );
    }

    /** Fold lines longer than 75 octets with CRLF + leading space (RFC 5545 §3.1). */
    private function fold(string $line): string
    {
        if (strlen($line) <= 75) {
            return $line;
        }

        $folded = '';
        while (strlen($line) > 75) {
            $folded .= substr($line, 0, 75)."\r\n ";
            $line = substr($line, 75);
        }

        return $folded.$line;
    }
}
