# local_logexplainer

Moodle local plugin that converts authorised Moodle log events into a factual timeline and then
asks `local_ai_bridge` to explain that sequence without inventing missing events.

## Access model

`local/logexplainer:view` is necessary but never sufficient by itself. The selected course context must also grant
Moodle's native `report/log:view` capability. Target-user visibility respects enrollment/viewing state and
separate-groups visibility. Queries without an explicit target user are additionally constrained to the users visible
through Moodle's group rules, including effective activity group mode. Activity queries are constrained to a course
module belonging to the selected course.

## Privacy and data minimisation

The plugin does not persist analyses, prompts, or model responses. AI Bridge may still record its own usage/accounting
metadata according to its separate privacy provider, but this plugin does not store the exchanged content. It reads
existing Moodle logs on demand. IP addresses are not sent to AI, common IP literals found inside event descriptions are
redacted before the AI payload is built, and arbitrary `event->other` payloads are not forwarded. AI receives a minimal
normalized event record with evidence IDs such as `E1`, `E2`, and `E3`.

The factual table is always rendered separately from the AI explanation. Analyses are capped (100 events by default, 200
maximum); when a query exceeds the cap the UI and AI payload explicitly mark the sequence as truncated. If the bridge is
unavailable, the purpose is not configured, credits are unavailable, a provider fails, or the AI response is malformed,
the factual timeline remains available.

## AI response contract

The model must return JSON containing `summary`, `timeline`, `hypotheses`, and `gaps`. Factual statements require one or
more known evidence IDs. Unknown IDs such as an invented `E99` cause the entire explanation to be rejected. Hypotheses
must be explicitly qualified as possibilities.

## Analysis modes

1. User + course + period.
2. User + activity.
3. Exact Moodle event class.
4. Summarised timeline for the selected filters/period.
