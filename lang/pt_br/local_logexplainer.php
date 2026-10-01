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
 * Brazilian Portuguese strings.
 *
 * @package   local_logexplainer
 * @copyright 2026 Eduardo Kraus
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$string['activity'] = 'Atividade';
$string['aiunavailable'] = 'Os eventos factuais estão disponíveis abaixo, mas não foi possível produzir a explicação por IA.';
$string['allusers'] = 'Todos os usuários';
$string['analyse'] = 'Montar linha do tempo e explicar';
$string['chooseactivity'] = 'Selecione uma atividade';
$string['component'] = 'Componente / ação / alvo';
$string['context'] = 'Contexto';
$string['defaultperioddays'] = 'Período padrão em dias';
$string['defaultperioddays_desc'] = 'Quantidade de dias usada por padrão no filtro inicial.';
$string['details'] = 'Detalhes factuais';
$string['error_activityrequired'] = 'Selecione uma atividade para este modo.';
$string['error_eventrequired'] = 'Informe a classe exata de um evento Moodle.';
$string['error_period'] = 'A data final deve ser posterior à data inicial.';
$string['error_userrequired'] = 'Selecione um usuário para este modo.';
$string['event'] = 'Evento';
$string['eventname'] = 'Classe exata do evento';
$string['eventname_help'] = 'Exemplo: \\mod_quiz\\event\\attempt_submitted. A consulta é exata de propósito, mantendo a busca portátil e previsível entre log stores.';
$string['evidenceid'] = 'ID';
$string['explanationheading'] = 'Explicação';
$string['factsheading'] = 'Eventos factuais do log';
$string['fromtime'] = 'De';
$string['gaps'] = 'O que os logs não permitem concluir';
$string['hypotheses'] = 'Interpretações possíveis';
$string['invalidairesponse'] = 'A resposta da IA foi rejeitada porque não preservou as referências factuais dos eventos.';
$string['logexplainer:view'] = 'Visualizar e explicar logs Moodle autorizados';
$string['maxevents'] = 'Máximo de eventos por análise';
$string['maxevents_desc'] = 'Limita a quantidade de eventos lidos e enviados à camada de explicação. Em tempo de execução o limite máximo é 200.';
$string['mode'] = 'Modo';
$string['mode_event'] = 'Evento específico';
$string['mode_summary'] = 'Linha do tempo resumida';
$string['mode_useractivity'] = 'Usuário + atividade';
$string['mode_usercourse'] = 'Usuário + curso + período';
$string['noevents'] = 'Nenhum evento de log correspondente foi encontrado.';
$string['nologreader'] = 'Nenhum leitor de logs Moodle compatível com SQL está habilitado.';
$string['pluginname'] = 'Explicador de logs';
$string['privacy:metadata'] = 'O Explicador de logs não armazena dados pessoais próprios. Ele lê logs Moodle existentes sob demanda e não persiste prompts nem análises.';
$string['privacywarning'] = 'Esta ferramenta lê dados pessoais de logs sob demanda. O plugin não persiste análises e não envia endereços IP para a IA.';
$string['summary'] = 'Resumo';
$string['time'] = 'Horário';
$string['timeline'] = 'Linha do tempo legível';
$string['totime'] = 'Até';
$string['truncatedgap'] = 'A análise contém apenas {$a->shown} de {$a->total} eventos correspondentes e, portanto, não deve ser tratada como histórico completo.';
$string['truncatednotice'] = 'A consulta encontrou {$a->total} eventos. Apenas os primeiros {$a->shown} são exibidos e enviados à IA; reduza o período para obter uma sequência completa.';
$string['user'] = 'Usuário';
