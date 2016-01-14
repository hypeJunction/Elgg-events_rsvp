<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof \Events\API\Event) {
	return;
}

echo elgg_list_entities_from_relationship(array(
	'types' => 'user',
	'relationship' => 'not_attending',
	'relationship_guid' => $entity->guid,
	'inverse_relationship' => true,
	'base_url' => elgg_normalize_url("/calendar/events/rsvps/$entity->guid/not_attending"),
	'item_view' => 'events/user',
	'no_results' => elgg_echo('events:rsvp:list:no_results'),
	'event' => $entity,
	'pagination_type' => 'default',
));