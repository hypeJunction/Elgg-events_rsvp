<?php

namespace Events\UI;

use Events\API\Event;

$guid = elgg_extract('guid', $vars);
$entity = get_entity($guid);

if (!$entity instanceof Event) {
	forward('', '404');
}

if (!events_rsvp_can_invite($entity)) {
	forward('', '403');
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

$title = elgg_echo('events:rsvp:invite');

$content = elgg_view_form('events/invite', array(), array(
	'entity' => $entity,
		));

$layout = elgg_view_layout('content', array(
	'title' => $title,
	'content' => $content,
	'filter' => false,
	'entity' => $entity,
		));

echo elgg_view_page($title, $layout, 'default', array(
	'entity' => $entity,
));
