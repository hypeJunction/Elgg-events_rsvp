<?php

use Events\API\Event;

/**
 * RSVPs for Events Framework
 *
 * @author Ismayil Khayredinov <info@hypejunction.com>
 * @copyright Copyright (c) 2015-2016, Ismayil Khayredinov
 */
require_once __DIR__ . '/autoloader.php';

elgg_register_event_handler('init', 'system', function() {

	elgg_register_action('events/rsvp', __DIR__ . '/actions/events/rsvp.php');
	elgg_register_action('events/rsvp_remove', __DIR__ . '/actions/events/rsvp_remove.php');
	elgg_register_action('events/invite', __DIR__ . '/actions/events/invite.php');
	elgg_register_action('events/uninvite', __DIR__ . '/actions/events/uninvite.php');

	elgg_register_plugin_hook_handler('profile_buttons', 'object:event', 'events_rsvp_prepare_profile_buttons');
	elgg_register_plugin_hook_handler('register', 'menu:event:user', 'events_rsvp_setup_event_user_menu');

	elgg_extend_view('events/add/extend', 'events/add/rsvp');
	elgg_extend_view('object/event', 'object/event/rsvps');

	elgg_register_event_handler('create', 'object', 'events_rsvp_save_options');
	elgg_register_event_handler('update', 'object', 'events_rsvp_save_options');

	elgg_register_plugin_hook_handler('route', 'calendar', 'events_rsvp_invite_page_handler');
	elgg_register_plugin_hook_handler('public_pages', 'walled_garden', 'events_rsvp_public_pages');
	
	elgg_extend_view('elgg.css', 'events/rsvp.css');
});

/**
 * Get RSVP options
 * 
 * @param Event $event Additional params
 * @return array
 */
function events_rsvp_options(Event $event = null) {
	$options = array(
		'attending',
		'not_attending',
	);
	$params = array(
		'event' => $event,
	);
	return elgg_trigger_plugin_hook('rsvp_options', 'events', $params, $options);
}

/**
 * Returns current RSVP status of the user
 *
 * @param Event    $event Event
 * @param ElggUser $user  User
 * @return string|void
 */
function events_rsvp_user_status(Event $event, ElggUser $user = null) {

	if (!isset($user)) {
		$user = elgg_get_logged_in_user_entity();
	}
	$options = events_rsvp_options($event);
	foreach ($options as $option) {
		if (check_entity_relationship($user->guid, $option, $event->guid)) {
			return $option;
		}
	}
}

/**
 * Setup event title menu
 * 
 * @param string         $hook   "profile_buttons"
 * @param string         $type   "object:event"
 * @param ElggMenuItem[] $return Menu
 * @param array          $params Hook params
 * @return ElggMenuItem[]
 */
function events_rsvp_prepare_profile_buttons($hook, $type, $return, $params) {

	$event = elgg_extract('event', $params);
	$user = elgg_extract('user', $params, elgg_get_logged_in_user_entity());

//	foreach ($return as $key => $item) {
//		if ($item instanceof ElggMenuItem && $item->getName() == 'add_to_calendar') {
//			unset($return[$key]);
//		}
//	}

	if (events_rsvp_can_rsvp($event, $user)) {
		$rsvp_options_values = array('' => elgg_echo('events:rsvp'));
		$rsvp_options = events_rsvp_options($event);

		foreach ($rsvp_options as $option) {
			$rsvp_options_values[$option] = elgg_echo("events:rsvp:$option");
		}

		$select = elgg_view('input/select', array(
			'class' => 'events-rsvp-select',
			'data-endpoint' => elgg_http_add_url_query_elements(elgg_normalize_url('action/events/rsvp'), array(
				'event_guid' => $event->guid,
				'user_guid' => $user->guid,
			)),
			'value' => events_rsvp_user_status($event, $user),
			'options_values' => $rsvp_options_values,
		));
		$select .= elgg_format_element('script', [], "require(['events/rsvp']);");

		$return[] = ElggMenuItem::factory(array(
					'name' => 'rsvp',
					'text' => $select,
					'href' => false,
					'priority' => 50,
		));
	}

	if (events_rsvp_can_invite($event, $user)) {
		$return[] = ElggMenuItem::factory(array(
					'name' => 'invite',
					'text' => elgg_echo('events:rsvp:invite'),
					'href' => "/calendar/events/invite/$event->guid",
					'link_class' => 'elgg-button elgg-button-action',
		));
	}

	return $return;
}

/**
 * Update event RSVP options
 *
 * @param string $event  "create"|"update"
 * @param string $type   "object"
 * @param string $entity Event object
 * @return void
 */
function events_rsvp_save_options($event, $type, $entity) {

	if (!$entity instanceof Event) {
		return;
	}

	$entity->allowed_rsvps = get_input('allowed_rsvps', 'noone');
	$entity->allowed_invites = get_input('allowed_invites', 'private');
}

/**
 * Check if user can RSVP for an event
 *
 * @param Event    $event Event
 * @param ElggUser $user  User
 * @return bool
 */
function events_rsvp_can_rsvp(Event $event, ElggUser $user = null) {
	if (!isset($user)) {
		$user = elgg_get_logged_in_user_entity();
	}
	if (!$user instanceof ElggUser) {
		return false;
	}

	switch ($event->allowed_rsvps) {

		default :
		case 'noone' :
			$permission = false;
			break;

		case 'public' :
			$permission = true;
			break;

		case 'invitees' :
			$permission = (check_entity_relationship($event->guid, 'invited', $user->guid));
			break;
	}

	$params = array(
		'entity' => $event,
		'user' => $user,
	);

	return elgg_trigger_plugin_hook('permissions_check:rsvp', 'object', $params, $permission);
}

/**
 * Check if user can invite others to an event
 *
 * @param Event    $event Event
 * @param ElggUser $user  User
 * @return bool
 */
function events_rsvp_can_invite(Event $event, ElggUser $user = null) {
	if (!isset($user)) {
		$user = elgg_get_logged_in_user_entity();
	}
	if (!$user instanceof ElggUser) {
		return false;
	}

	switch ($event->allowed_invites) {

		default :
		case 'private' :
			$permission = $event->owner_guid == $user->guid;
			break;

		case 'public' :
			$permission = true;
			break;

		case 'attendees' :
			$permission = (check_entity_relationship($user->guid, 'attending', $event->guid));
			break;
	}

	$params = array(
		'entity' => $event,
		'user' => $user,
	);

	return elgg_trigger_plugin_hook('permissions_check:invite', 'object', $params, $permission);
}

/**
 * Route invite page
 * 
 * @param string $hook   "route"
 * @param string $type   "calendar"
 * @param array  $return URL identifier and segments
 * @param array  $params Hook params
 * @return array|true
 */
function events_rsvp_invite_page_handler($hook, $type, $return, $params) {

	$identifier = elgg_extract('identifier', $return);
	$segments = elgg_extract('segments', $return);

	if ($identifier == 'calendar' && $segments[0] == 'events') {
		switch ($segments[1]) {
			case 'invite' :
				echo elgg_view_resource('events/invite', array(
					'guid' => $segments[2],
				));
				return false;
			case 'rsvps' :
				echo elgg_view_resource('events/rsvps', array(
					'guid' => $segments[2],
					'tab' => $segments[3],
				));
				return false;
			case 'confirm_invite' :
				echo elgg_view_resource('events/confirm_invite');
				return false;
		}
	}
}

/**
 * Setup event user menu
 * 
 * @param string         $hook   "register"
 * @param string         $type   "menu:event:user"
 * @param ElggMenuItem[] $return Menu
 * @param array          $params Hook params
 * @return array
 */
function events_rsvp_setup_event_user_menu($hook, $type, $return, $params) {

	$event = elgg_extract('event', $params);
	$user = elgg_extract('user', $params);

	if (!$event instanceof Event || !$user instanceof ElggUser) {
		return;
	}

	if (!$event->canEdit()) {
		return;
	}

	if (events_rsvp_user_status($event, $user)) {
		$return[] = ElggMenuItem::factory(array(
					'name' => 'rsvp:remove',
					'text' => elgg_echo('events:rsvp:remove'),
					'href' => elgg_http_add_url_query_elements(elgg_normalize_url('/action/events/rsvp_remove'), array(
						'user_guid' => $user->guid,
						'event_guid' => $event->guid,
					)),
					'confirm' => elgg_echo('events:rsvp:remove:confirm'),
					'link_class' => 'elgg-button elgg-button-delete',
					'item_class' => 'elgg-menu-item-delete',
		));
	}

	if (check_entity_relationship($event->guid, 'invited', $user->guid)) {
		$return[] = ElggMenuItem::factory(array(
					'name' => 'rsvp:uninvite',
					'text' => elgg_echo('events:rsvp:uninvite'),
					'href' => elgg_http_add_url_query_elements(elgg_normalize_url('/action/events/uninvite'), array(
						'user_guid' => $user->guid,
						'event_guid' => $event->guid,
					)),
					'confirm' => elgg_echo('events:rsvp:uninvite:confirm'),
					'link_class' => 'elgg-button elgg-button-delete',
					'item_class' => 'elgg-menu-item-delete',
		));
	}

	return $return;
}

/**
 * Add confirm invite page to public pages
 * 
 * @param string $hook   "public_pages"
 * @param string $type   "walled_garden"
 * @param array  $return Public pages
 * @param array  $params Hook params
 * @return array
 */
function events_rsvp_public_pages($hook, $type, $return, $params) {
	$return[] = "calendar/events/confirm_invite";
	return $return;
}