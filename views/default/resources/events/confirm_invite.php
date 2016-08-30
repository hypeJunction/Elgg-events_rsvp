<?php

use Events\API\Calendar;
use Events\API\Event;

$invitee_guid = (int) get_input('i');
$event_guid = (int) get_input('e');
$ts = (int) get_input('t');
$mac = get_input('m');

$hmac = elgg_build_hmac([
	'i' => $invitee_guid,
	'e' => $event_guid,
	't' => $ts,
]);

$ia = elgg_set_ignore_access(true);

$invitee = get_entity($invitee_guid);
$event = get_entity($event_guid);

if ($hmac->matchesToken($mac) && $event instanceof Event && $user instanceof ElggUser) {

	$options = events_rsvp_options();
	$relationships = array();
	foreach ($options as $option) {
		if ($option == 'attending') {
			continue;
		}
		$relationships[] = "'$option'";
	}

	add_entity_relationship($user->guid, 'attending', $event->guid);
	remove_entity_relationship($event->guid, 'invited', $user->guid);

	if (!empty($relationships)) {
		$relationships_in = implode(',', $relationships);
		$dbprefix = elgg_get_config('dbprefix');
		$query = "DELETE FROM {$dbprefix}entity_relationships
		WHERE guid_one = {$user->guid} AND guid_two = {$event->guid} AND relationship IN ({$relationships_in})";
		delete_data($query);
	}

	$calendar = Calendar::getPublicCalendar($user);
	$calendar->addEvent($event);

	$title = elgg_echo('events:rsvp:confirm:title');
	$content = elgg_format_element('p', [], elgg_echo('events:rsvp:confirm', [
		'event' => elgg_view('output/url', array(
			'text' => $entity->getDisplayName(),
			'href' => $entity->getURL(),
		)),
	]));

	$layout = elgg_view_layout('one_column', [
		'title' => $title,
		'content' => $content,
	]);

	$page = elgg_view_page($title, $layout);
}

elgg_set_ignore_access($ia);

if ($page) {
	echo $page;
} else {
	register_error(elgg_echo('events:rsvp:confirm:invalid'));
	forward('', '404');
}
