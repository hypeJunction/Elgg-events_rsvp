<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof \Events\API\Event) {
	return;
}

$filter_context = elgg_extract('filter_context', $vars, 'attending');

$rsvp_options = events_rsvp_options($entity);
$rsvp_options[] = 'invited';

$tabs = array();
foreach ($rsvp_options as $option) {
	$counter = elgg_get_entities_from_relationship([
		'relationship' => $option,
		'relationship_guid' => $entity->guid,
		'inverse_relationship' => false,
		'count' => true,
	]);
	
	$tabs[] = array(
		'name' => $option,
		'text' => elgg_echo("events:rsvp:list:$option") . '<span class="indicator">' . $counter . '</span>',
		'href' => "/calendar/events/rsvps/$entity->guid/$option",
		'selected' => $option == $filter_context,
	);
}

foreach ($tabs as $tab) {
	elgg_register_menu_item('filter', $tab);
}

echo elgg_view_menu('filter', array(
	'entity' => $entity,
	'sort_by' => 'priority',
	'class' => 'events-rsvp-filter',
));

