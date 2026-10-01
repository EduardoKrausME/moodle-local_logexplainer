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
 * Strict validation for AI output.
 *
 * @package   local_logexplainer
 * @copyright 2026 Eduardo Kraus
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ai_response_validator {
    /**
     * Parse and validate a JSON response.
     *
     * @param string $text Raw model text.
     * @param array $allowedids Allowed E ids.
     * @return array
     */
    public static function parse(string $text, array $allowedids): array {
        $text = trim($text);
        if (preg_match('/^```(?:json)?\s*(.*?)\s*```$/s', $text, $match)) {
            $text = trim($match[1]);
        }

        $data = json_decode($text, true);
        if (!is_array($data) || json_last_error() !== JSON_ERROR_NONE) {
            throw new invalid_ai_response_exception('Response is not valid JSON.');
        }

        $allowed = array_fill_keys($allowedids, true);
        foreach (['summary', 'timeline', 'hypotheses'] as $section) {
            if (!isset($data[$section]) || !is_array($data[$section])) {
                throw new invalid_ai_response_exception("Missing or invalid section: {$section}.");
            }
            foreach ($data[$section] as $item) {
                self::validate_item($item, $allowed, $section === 'hypotheses');
            }
        }
        if (!isset($data['gaps']) || !is_array($data['gaps'])) {
            throw new invalid_ai_response_exception('Missing or invalid gaps section.');
        }
        foreach ($data['gaps'] as $gap) {
            if (!is_string($gap)) {
                throw new invalid_ai_response_exception('Every gap must be text.');
            }
        }

        preg_match_all('/\bE\d+\b/', $text, $matches);
        foreach (array_unique($matches[0]) as $id) {
            if (!isset($allowed[$id])) {
                throw new invalid_ai_response_exception("Invented or unknown event id: {$id}.");
            }
        }

        return $data;
    }

    /**
     * Validate one factual/hypothesis item.
     *
     * @param mixed $item Item.
     * @param array $allowed Allowed ids map.
     * @param bool $hypothesis Whether this is a hypothesis.
     * @return void
     */
    private static function validate_item($item, array $allowed, bool $hypothesis): void {
        if (!is_array($item) || !isset($item['text'], $item['evidence_ids']) || !is_string($item['text']) ||
            !is_array($item['evidence_ids'])) {
            throw new invalid_ai_response_exception('Explanation items require text and evidence_ids.');
        }
        if (!$hypothesis && count($item['evidence_ids']) === 0) {
            throw new invalid_ai_response_exception('Factual statements require at least one evidence id.');
        }
        foreach ($item['evidence_ids'] as $id) {
            if (!is_string($id) || !isset($allowed[$id])) {
                throw new invalid_ai_response_exception('Explanation references an unknown event id.');
            }
        }
        if ($hypothesis &&
            stripos($item['text'], 'possib') === false &&
            stripos($item['text'], 'may') === false &&
            stripos($item['text'], 'might') === false &&
            stripos($item['text'], 'could') === false &&
            stripos($item['text'], 'pode') === false &&
            stripos($item['text'], 'talvez') === false) {
            throw new invalid_ai_response_exception('Hypotheses must be explicitly qualified as possibilities.');
        }
    }
}
