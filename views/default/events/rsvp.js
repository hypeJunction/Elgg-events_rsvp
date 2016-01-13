define(function(require) {

	var elgg = require('elgg');
	var $ = require('jquery');
	var spinner = require('elgg/spinner');
	
	$(document).on('change', '.events-rsvp-select', function(e) {
		var $elem = $(this);
		var endpoint = $elem.data('endpoint');
		elgg.action(endpoint, {
			data: {
				rsvp: $elem.val(),
			},
			beforeSend: spinner.start,
			complete: spinner.stop
		});
	});

});