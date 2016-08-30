<?php

$guid = get_input('guid');
$entity = get_entity($guid);
$inviter = elgg_get_logged_in_user_entity();
$message = get_input('message');

if (!$entity instanceof Events\API\Event) {
	register_error(elgg_echo('events:rsvp:not_found'));
	forward(REFERRER);
}

if (!events_rsvp_can_invite($entity)) {
	register_error(elgg_echo('events:rsvp:permission_denied'));
	forward(REFERRER);
}

$invitee_guids = get_input('invitee_guids');
if ($invitee_guids && !is_array($invitee_guids)) {
	$invitee_guids = string_to_tag_array($invitee_guids);
}

$error = 0;
$skipped = 0;
$invited = 0;

$allowed_rsvps = $entity->allowed_rsvps && $entity->allowed_rsvps !== 'noone';

foreach ($invitee_guids as $invitee_guid) {
	$invitee = get_entity($invitee_guid);
	if (!$invitee) {
		$error++;
	}

	if (check_entity_relationship($entity->guid, 'invited', $invitee->guid)) {
		// already invited
		$skipped++;
	}

	if (events_rsvp_user_status($entity, $invitee)) {
		// already RSVP'ed
		$skipped++;
	}

	add_entity_relationship($entity->guid, 'invited', $invitee->guid);
	add_entity_relationship($entity->guid, 'access_grant', $invitee->guid);

	$time = time();

	$hmac = elgg_build_hmac([
		'i' => $invitee->guid,
		'e' => $entity->guid,
		't' => $time,
	]);

	$base_url = elgg_normalize_url('calendar/events/confirm_invite');
	$confirm_url = elgg_http_add_url_query_elements($base_url, [
		'i' => $invitee->guid,
		'e' => $entity->guid,
		't' => $time,
		'm' => $hmac->getToken(),
	]);

	$notification_params = array(
		'inviter' => elgg_view('output/url', array(
			'text' => $inviter->getDisplayName(),
			'href' => $inviter->getURL(),
		)),
		'event' => elgg_view('output/url', array(
			'text' => $entity->getDisplayName(),
			'href' => $entity->getURL(),
		)),
		'date' => elgg_view('output/events_ui/date_range', array(
			'user' => $invitee,
			'start' => $entity->start_timestamp,
			'end' => $entity->end_timestamp,
			'timezone' => \Events\API\Util::getClientTimezone($invitee),
		)),
		'message' => ($message) ? elgg_echo('events:rsvp:invite:notify:message', array($message), $invitee->language) : '',
		'url' => elgg_view('output/url', [
			'href' => $confirm_url,
		]),
	);

	$subject = elgg_echo('events:rsvp:invite:notify:subject', array($entity->getDisplayName()), $invitee->language);
	$body = elgg_echo('events:rsvp:invite:notify:body', $notification_params, $invitee->language);

	$params = [
		'action' => 'invite',
		'object' => $entity,
	];

	$result = notify_user($invitee->guid, $inviter->guid, $subject, $body, $params);
	if ($result) {
		$invited++;
	} else {
		$error++;
	}
}

$total = $error + $invited + $skipped;
if ($invited) {
	system_message(elgg_echo('events:rsvp:invite:result:invited', array($invited, $total)));
}
if ($skipped) {
	system_message(elgg_echo('events:rsvp:invite:result:skipped', array($skipped, $total)));
}
if ($error) {
	register_error(elgg_echo('events:rsvp:invite:result:error', array($error, $total)));
}