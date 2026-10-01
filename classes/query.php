<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace local_logexplainer;

/**
 * Immutable query definition for log extraction.
 *
 * @package   local_logexplainer
 * @copyright 2026 Eduardo Kraus
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class query {
    public const MODE_USER_COURSE = 'usercourse';
    public const MODE_USER_ACTIVITY = 'useractivity';
    public const MODE_EVENT = 'event';
    public const MODE_SUMMARY = 'summary';

    /** @var int Course id. */
    public int $courseid;
    /** @var string Mode. */
    public string $mode;
    /** @var int|null User id. */
    public ?int $userid;
    /** @var int|null Course module id. */
    public ?int $cmid;
    /** @var int Start timestamp. */
    public int $from;
    /** @var int End timestamp. */
    public int $to;
    /** @var string Exact event class/name filter. */
    public string $eventname;

    /**
     * Constructor.
     *
     * @param int $courseid Course id.
     * @param string $mode Mode.
     * @param int|null $userid User id.
     * @param int|null $cmid Course module id.
     * @param int $from Start timestamp.
     * @param int $to End timestamp.
     * @param string $eventname Event class/name.
     */
    public function __construct(
        int    $courseid,
        string $mode,
        ?int   $userid,
        ?int   $cmid,
        int    $from,
        int    $to,
        string $eventname = ''
    ) {
        $this->courseid = $courseid;
        $this->mode = $mode;
        $this->userid = $userid;
        $this->cmid = $cmid;
        $this->from = $from;
        $this->to = $to;
        $this->eventname = trim($eventname);
    }
}
