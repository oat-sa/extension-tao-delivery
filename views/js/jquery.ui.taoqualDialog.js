require(['require', 'jquery', 'jqueryUI'], function(req, $) {
	$.fn.taoqualDialog = function (options) {
		options = $.extend({
			closable: true,
			autoOpen: false
		}, options);

		// Force autoOpen to false.
		if (options && (options.autoOpen != null || options.autoOpen != undefined)) {
			if (options.autoOpen == true) {
				options.autoOpen = false;
			}
		}

		return this.each(function (i) {
			$(this).dialog(options);

			// Let's modify the dialog if necessary.
			if (!options.closable) {
				// Lets find the parent > .ui-dialog-titlebar-close and remove it.
				$(this).parent().find('.ui-dialog-titlebar-close').remove();
			}

			$(this).dialog('open');
		});
	}
});