<?php

$entity = elgg_extract('entity', $vars);
?>

<div class="events-ui-row">
	<label><?php echo elgg_echo('events:rsvp:rsvps:allowed') ?></label>
	<?php
	echo elgg_view('input/select', array(
		'name' => 'allowed_rsvps',
		'value' => elgg_extract('allowed_rsvps', $vars, $entity->allowed_rsvps),
		'options_values' => array(
			'noone' => elgg_echo('events:rsvp:rsvps:allowed:noone'),
			'invitees' => elgg_echo('events:rsvp:rsvps:allowed:invitees'),
			'public' => elgg_echo('events:rsvp:rsvps:allowed:public'),
		)
	));
	?>
</div>

<div class="events-ui-row">
	<label><?php echo elgg_echo('events:rsvp:invites:allowed') ?></label>
	<?php
	echo elgg_view('input/select', array(
		'name' => 'allowed_invites',
		'value' => elgg_extract('allowed_invites', $vars, $entity->allowed_invites),
		'options_values' => array(
			'private' => elgg_echo('events:rsvp:invites:allowed:private'),
			'attendees' => elgg_echo('events:rsvp:invites:allowed:attendees'),
			'public' => elgg_echo('events:rsvp:invites:allowed:public'),
		)
	));
	?>
</div>