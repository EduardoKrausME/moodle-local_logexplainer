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

use ArrayIterator;
use core\event\base;
use core\log\sql_reader;
use stdClass;
use Traversable;

/**
 * Small SQL reader used by repository unit tests.
 *
 * @package   local_logexplainer
 * @copyright 2026 Eduardo Kraus
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class fake_sql_reader implements sql_reader {
    /** @var base Event returned by the reader. */
    private base $event;

    /**
     * Constructor.
     *
     * @param base $event Event.
     */
    public function __construct(base $event) {
        $this->event = $event;
    }

    /** @return string Reader name. */
    public function get_name() {
        return 'Fake';
    }

    /** @return string Reader description. */
    public function get_description() {
        return 'Fake reader';
    }

    /** @return bool Whether logging is enabled. */
    public function is_logging() {
        return true;
    }

    /**
     * Return events.
     *
     * @param string $selectwhere SQL selector.
     * @param array $params Parameters.
     * @param string $sort Sort.
     * @param int $limitfrom Offset.
     * @param int $limitnum Limit.
     * @return base[]
     */
    public function get_events_select($selectwhere, array $params, $sort, $limitfrom, $limitnum) {
        return [$this->event];
    }

    /**
     * Return event count.
     *
     * @param string $selectwhere SQL selector.
     * @param array $params Parameters.
     * @return int
     */
    public function get_events_select_count($selectwhere, array $params) {
        return 1;
    }

    /**
     * Return existence.
     *
     * @param string $selectwhere SQL selector.
     * @param array $params Parameters.
     * @return bool
     */
    public function get_events_select_exists(string $selectwhere, array $params): bool {
        return true;
    }

    /**
     * Return iterator.
     *
     * @param string $selectwhere SQL selector.
     * @param array $params Parameters.
     * @param string $sort Sort.
     * @param int $limitfrom Offset.
     * @param int $limitnum Limit.
     * @return Traversable
     */
    public function get_events_select_iterator($selectwhere, array $params, $sort, $limitfrom, $limitnum) {
        return new ArrayIterator([$this->event]);
    }

    /**
     * Convert raw record into event.
     *
     * @param stdClass $data Raw record.
     * @return base
     */
    public function get_log_event($data) {
        return $this->event;
    }
}
