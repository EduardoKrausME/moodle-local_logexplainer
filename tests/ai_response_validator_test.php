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

/**
 * Tests for strict AI response validation.
 *
 * @package   local_logexplainer
 * @copyright 2026 Eduardo Kraus
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_logexplainer;

use advanced_testcase;

/**
 * AI response validator tests.
 *
 * @package local_logexplainer
 * @covers \local_logexplainer\ai_response_validator
 */
final class ai_response_validator_test extends advanced_testcase {
    /**
     * Method test_valid_response_is_accepted.
     *
     * @return void Return value.
     */
    public function test_valid_response_is_accepted(): void {
        $json = json_encode([
            'summary' => [
                ['text' => 'The user viewed the activity (E1).', 'evidence_ids' => ['E1']],
            ],
            'timeline' => [
                ['text' => 'The attempt was submitted (E2).', 'evidence_ids' => ['E2']],
            ],
            'hypotheses' => [
                ['text' => 'One possibility is that E1 preceded E2 as part of the same workflow.', 'evidence_ids' => ['E1', 'E2']],
            ],
            'gaps' => ['The logs do not establish why the user submitted at that time.'],
        ], JSON_THROW_ON_ERROR);

        $data = ai_response_validator::parse($json, ['E1', 'E2']);
        $this->assertCount(1, $data['timeline']);
    }

    /**
     * Method test_invented_event_id_is_rejected.
     *
     * @return void Return value.
     */
    public function test_invented_event_id_is_rejected(): void {
        $json = json_encode([
            'summary' => [
                ['text' => 'An event occurred (E99).', 'evidence_ids' => ['E99']],
            ],
            'timeline' => [],
            'hypotheses' => [],
            'gaps' => [],
        ], JSON_THROW_ON_ERROR);

        $this->expectException(invalid_ai_response_exception::class);
        ai_response_validator::parse($json, ['E1']);
    }

    /**
     * Method test_malformed_response_is_rejected.
     *
     * @return void Return value.
     */
    public function test_malformed_response_is_rejected(): void {
        $this->expectException(invalid_ai_response_exception::class);
        ai_response_validator::parse('not-json', ['E1']);
    }

    /**
     * Method test_factual_statement_without_evidence_is_rejected.
     *
     * @return void Return value.
     */
    public function test_factual_statement_without_evidence_is_rejected(): void {
        $json = json_encode([
            'summary' => [
                ['text' => 'The user submitted an attempt.', 'evidence_ids' => []],
            ],
            'timeline' => [],
            'hypotheses' => [],
            'gaps' => [],
        ], JSON_THROW_ON_ERROR);

        $this->expectException(invalid_ai_response_exception::class);
        ai_response_validator::parse($json, ['E1']);
    }
}
