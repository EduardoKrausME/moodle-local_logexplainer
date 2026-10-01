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

use local_ai_bridge\api;

/**
 * AI explanation service.
 *
 * @package   local_logexplainer
 * @copyright 2026 Eduardo Kraus
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class explainer {
    public const PURPOSE = 'logexplainer-explain';

    /**
     * Explain a factual event sequence using local_ai_bridge only.
     *
     * @param array $events Formatted events.
     * @param string $mode Query mode.
     * @param bool $truncated Whether matching events exceeded the extraction cap.
     * @param int|null $totalcount Total matching events when known.
     * @return array Validated structured explanation.
     */
    public function explain(array $events, string $mode, bool $truncated = false, ?int $totalcount = null): array {
        if (!$events) {
            return [
                'summary' => [],
                'timeline' => [],
                'hypotheses' => [],
                'gaps' => [get_string('noevents', 'local_logexplainer')],
            ];
        }

        $facts = array_map([event_formatter::class, 'for_ai'], $events);
        $payload = [
            'mode' => $mode,
            'language' => current_language(),
            'input_truncated' => $truncated,
            'total_matching_events' => $totalcount,
            'rules' => [
                'Use only the provided events. Never invent, infer, or imply an event that is absent.',
                'Every factual statement must cite one or more event ids from the input.',
                'Do not claim causality unless the event sequence explicitly establishes it.',
                'If proposing an interpretation, put it only in hypotheses and phrase it as a possibility.',
                'Do not infer health, intent, personality, competence, or motivation.',
                'Do not expose or request IP addresses.',
                'If input_truncated is true, explicitly avoid claiming that the supplied events are the complete history.',
            ],
            'output_schema' => [
                'summary' => [['text' => 'string', 'evidence_ids' => ['E1']]],
                'timeline' => [['text' => 'string', 'evidence_ids' => ['E1']]],
                'hypotheses' => [['text' => 'Uma possibilidade é...', 'evidence_ids' => ['E1']]],
                'gaps' => ['string'],
            ],
            'events' => $facts,
        ];

        $messages = [
            [
                'role' => 'system',
                'content' => 'Explain Moodle log events faithfully. Return JSON only. ' .
                    'Evidence ids are mandatory for factual claims.',
            ],
            [
                'role' => 'user',
                'content' => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR),
            ],
        ];

        $response = api::generate(self::PURPOSE, $messages);
        $allowedids = array_column($events, 'id');
        $result = ai_response_validator::parse((string)$response->text, $allowedids);
        if ($truncated) {
            $result['gaps'][] = get_string('truncatedgap', 'local_logexplainer', [
                'shown' => count($events),
                'total' => $totalcount ?? count($events),
            ]);
        }
        return $result;
    }
}
