<?php

$entity = elgg_extract('entity', $vars);

if (elgg_is_admin_logged_in()) {
	echo elgg_view_input('tokeninput/users', array(
		'name' => 'invitee_guids',
		'label' => elgg_echo('events:invite:users:select'),
		'multiple' => true,
	));
} else {
	echo elgg_view_input('tokeninput/friends', array(
		'name' => 'invitee_guids',
		'label' => elgg_echo('events:invite:friends:select'),
		'multiple' => true,
	));
}

echo elgg_view_input('plaintext', array(
	'name' => 'message',
	'label' => elgg_echo('events:invite:message'),
	'rows' => 3,
));

echo elgg_view_input('hidden', array(
	'name' => 'guid',
	'value' => $entity->guid,
));

echo elgg_view_input('submit', array(
	'value' => elgg_echo('events:rsvp:invite'),
	'field_class' => 'elgg-foot',
));

