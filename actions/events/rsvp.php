<?php

$rsvp = get_input('rsvp');
$event_guid = get_input('event_guid');
$user_guid = get_input('user_guid');

$event = get_entity($event_guid);
$user = get_entity($user_guid);

if (!$user || !$user->canEdit()) {
	register_error(elgg_echo('events:rsvp:permission_denied'));
	forward(REFERRER, '403');
}

if (!$event instanceof Events\API\Event) {
	register_error(elgg_echo('events:rsvp:not_found'));
	forward(REFERRER, '404');
}

$options = events_rsvp_options();
if (!in_array($rsvp, $options)) {
	register_error(elgg_echo('events:rsvp:error'));
	forward(REFERRER);
}

$relationships = array();
foreach ($options as $option) {
	if ($option == $rsvp) {
		continue;
	}
	$relationships[] = "'$option'";
}

add_entity_relationship($user->guid, $rsvp, $event->guid);
remove_entity_relationship($event->guid, 'invited', $user->guid);

system_message(elgg_echo('events:rsvp:success'));

if (!empty($relationships)) {
	$relationships_in = implode(',', $relationships);
	$dbprefix = elgg_get_config('dbprefix');
	$query = "DELETE FROM {$dbprefix}entity_relationships WHERE guid_one = {$user->guid} AND guid_two = {$event->guid} AND relationship IN ({$relationships_in})";
	delete_data($query);
}

$calendar = \Events\API\Calendar::getPublicCalendar($user);
if ($rsvp == 'not_attending') {
	if ($event->owner_guid != $user->guid) {
		$calendar->removeEvent($event);
	}
} else {
	$calendar->addEvent($event);
}