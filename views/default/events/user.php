<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof ElggUser) {
	return;
}

$event = elgg_extract('event', $vars);

$icon = elgg_view_entity_icon($entity, 'tiny');
$link = elgg_view('output/url', array(
	'text' => $entity->getDisplayName(),
	'href' => $entity->getURL(),
));

$menu = elgg_view_menu('event:user', array(
	'user' => $entity,
	'event' => $event,
	'class' => 'elgg-menu-hz',
));

echo elgg_view_image_block($icon, $link, array(
	'image_alt' => $menu,
	'class' => 'events-rsvp-user',
));