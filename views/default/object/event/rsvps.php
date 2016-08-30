<?php

$full_view = elgg_extract('full_view', $vars, false);
if (!$full_view || elgg_is_xhr()) {
	return;
}

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof \Events\API\Event) {
	return;
}

$allowed_rsvps = $entity->allowed_rsvps && $entity->allowed_rsvps !== 'noone';
if (!$allowed_rsvps && !$entity->canEdit()) {
	return;
}

echo elgg_view('components/tabs', array(
    'id' => "events-rsvps-$entity->guid",
    'tabs' => elgg_view('filters/events/rsvps', array(
		'entity' => $entity,
	)),
    'content' => elgg_view('events/rsvps/attending', array(
		'entity' => $entity,
	)),
));