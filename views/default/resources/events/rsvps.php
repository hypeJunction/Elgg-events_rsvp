<?php

namespace Events\UI;

use Events\API\Event;

$guid = elgg_extract('guid', $vars);
$entity = get_entity($guid);

if (!$entity instanceof Event) {
	forward('', '404');
}

$container = $entity->getContainerEntity();
elgg_set_page_owner_guid($container->guid);

//elgg_push_breadcrumb(elgg_echo('events:calendar'), "calendar/all");
if (elgg_instanceof($container, 'user')) {
	elgg_push_breadcrumb($container->getDisplayName(), "calendar/owner/$container->username");
} else if (elgg_instanceof($container, 'group')) {
	elgg_push_breadcrumb($container->getDisplayName(), "calendar/group/$container->guid");
}

elgg_push_breadcrumb($entity->getDisplayName(), $entity->getURL());

$tab = elgg_extract('tab', $vars);
if (!$tab || !elgg_view_exists("events/rsvps/$tab")) {
	$tab = 'attending';
}
$title = elgg_echo("events:rsvp:list:$tab");
elgg_push_breadcrumb($title);

$content = elgg_view("events/rsvps/$tab", array(
	'entity' => $entity,
		));

if (elgg_is_xhr()) {
	echo $content;
} else {
	$filter = elgg_view('filters/events/rsvps', array(
		'entity' => $entity,
		'filter_context' => $tab,
	));
	$layout = elgg_view_layout('content', array(
		'title' => $title,
		'content' => $content,
		'filter' => $filter,
		'entity' => $entity,
	));
	echo elgg_view_page($title, $layout, 'default', array(
		'entity' => $entity,
	));
}