<?php

return [
	'events:rsvp' => 'RSVP',
	'events:rsvp:attending' => 'Attending',
	'events:rsvp:not_attending' => 'Not attending',
	'events:rsvp:permission_denied' => 'You are not allowed to perform this action',
	'events:rsvp:not_found' => 'Event not found',
	'events:rsvp:success' => 'Your RSVP has been saved',
	'events:rsvp:error' => 'Your RSVP could not be saved',
	'events:rsvp:rsvps:allowed' => 'Who can RSVP for the event?',
	'events:rsvp:rsvps:allowed:noone' => 'Disable RSVPs',
	'events:rsvp:rsvps:allowed:invitees' => 'Only invited people',
	'events:rsvp:rsvps:allowed:public' => 'Anyone who can access the event',
	'events:rsvp:invites:allowed' => 'Who can invite people to the event?',
	'events:rsvp:invites:allowed:private' => 'Only me',
	'events:rsvp:invites:allowed:attendees' => 'Attendees',
	'events:rsvp:invites:allowed:public' => 'Anyone who can access the event',
	'events:rsvp:invite' => 'Invite',
	'events:invite:users:select' => 'Select users',
	'events:invite:friends:select' => 'Select friends',
	'events:invite:message' => 'Add optional message',

	'events:rsvp:invite:notify:subject' => 'You are invited to attend %s',
	'events:rsvp:invite:notify:body' => '%1$s has invited you to attend an event:

		%2$s
		%3$s

		%4$s
		
		Please RSVP by clicking this link: %5$s.
		',
	'events:rsvp:invite:notify:message' => '
		They have included the following message for you:
		%s

		',
	'events:rsvp:invite:result:invited' => '%s of %s invitations were successfully sent',
	'events:rsvp:invite:result:skipped' => '%s of %s invitations were skipped, because users have already been invited',
	'events:rsvp:invite:result:error' => '%s of %s invitations could not be sent due to errors',

	'events:rsvp:list:no_results' => 'There are no users to display',
	'events:rsvp:list:attending' => 'Attending',
	'events:rsvp:list:not_attending' => 'Not Attending',
	'events:rsvp:list:invited' => 'Invited',

	'events:rsvp:remove' => 'Remove',
	'events:rsvp:remove:confirm' => 'Are you sure you want to remove user\'s RSVP and potentially revoke their access to the event?',
	'events:rsvp:remove:success' => 'User has been removed from the event',

	'events:rsvp:uninvite' => 'Revoke',
	'events:rsvp:uninvite:confirm' => 'Are you sure you want to revoke the invitation and potentially prevent access to the event?',
	'events:rsvp:uninvite:success' => 'Invitation has been revoked successfully',

	'events:rsvp:confirm:invalid' => 'The URL of the page you are trying to reach is invalid or has expired',
	'events:rsvp:confirm:title' => 'Thank you',
	'events:rsvp:confirm' => 'Your attendance at %s has been confirmed. You can change your RSVP status at any time by visiting the event page',
	
];
