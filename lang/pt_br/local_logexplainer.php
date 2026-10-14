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
$string['notingroup'] = 'Você precisa pertencer a um grupo visível para consultar estes logs.';
$string['noevents'] = 'Nenhum evento de log correspondente foi encontrado.';
$string['nologreader'] = 'Nenhum leitor de logs Moodle compatível com SQL está habilitado.';
$string['pluginname'] = 'Explicador de logs';
$string['privacy:metadata'] = 'Este plugin não armazena registros pessoais próprios, prompts ou respostas da IA, mas envia dados selecionados dos logs do Moodle por meio do local_ai_bridge ao provedor de IA configurado quando uma análise é solicitada.';
$string['privacy:metadata:aiprovider'] = 'A cada análise solicitada, até 200 eventos autorizados dos logs Moodle são enviados pelo local_ai_bridge ao provedor de IA configurado. Descrições de eventos, IDs de usuários relacionados e IDs de objetos podem identificar participantes; o plugin não armazena esse conteúdo.';
$string['privacy:metadata:aiprovider:id'] = 'Identificador de sequência usado nesta análise.';
$string['privacy:metadata:aiprovider:timestamp'] = 'Data e hora de criação do evento (Unix).';
$string['privacy:metadata:aiprovider:time'] = 'Data e hora legíveis do evento.';
$string['privacy:metadata:aiprovider:eventname'] = 'Nome legível do evento.';
$string['privacy:metadata:aiprovider:eventclass'] = 'Nome da classe do evento Moodle.';
$string['privacy:metadata:aiprovider:component'] = 'Componente Moodle que gerou o evento.';
$string['privacy:metadata:aiprovider:action'] = 'Ação registrada no evento.';
$string['privacy:metadata:aiprovider:target'] = 'Tipo de alvo registrado no evento.';
$string['privacy:metadata:aiprovider:objectid'] = 'ID do objeto afetado pelo evento.';
$string['privacy:metadata:aiprovider:context'] = 'Nome legível do contexto Moodle, que pode identificar um curso ou atividade.';
$string['privacy:metadata:aiprovider:relateduserid'] = 'ID do usuário relacionado ao evento, quando existir.';
$string['privacy:metadata:aiprovider:origin'] = 'Origem da ação registrada.';
$string['privacy:metadata:aiprovider:actor'] = 'Categoria pseudonimizada do autor do evento em relação ao usuário selecionado.';
$string['privacy:metadata:aiprovider:description'] = 'Descrição do evento Moodle, que pode conter IDs de participantes e outras informações identificáveis.';
$string['privacywarning'] = 'Esta análise envia até 200 eventos dos logs Moodle ao provedor externo de IA configurado pelo local_ai_bridge. Descrições e IDs dos eventos podem identificar participantes do curso. Campos diretos de IP são omitidos e endereços IP comuns em textos são ocultados; este plugin não armazena prompts nem respostas.';
$string['summary'] = 'Resumo';
$string['time'] = 'Horário';
$string['timeline'] = 'Linha do tempo legível';
$string['totime'] = 'Até';
$string['truncatedgap'] = 'A análise contém apenas {$a->shown} de {$a->total} eventos correspondentes e, portanto, não deve ser tratada como histórico completo.';
$string['truncatednotice'] = 'A consulta encontrou {$a->total} eventos. Apenas os primeiros {$a->shown} são exibidos e enviados à IA; reduza o período para obter uma sequência completa.';
$string['user'] = 'Usuário';
