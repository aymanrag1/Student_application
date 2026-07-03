/**
 * RSYI Stage 1 form - client-side interactivity.
 * Shows/hides the "track" (شعبة) field based on high school type.
 */
(function () {
	'use strict';

	function init() {
		var hsType = document.getElementById('rsyi_hs_type');
		var trackContainer = document.querySelector('[data-track-container]');
		var trackSelect = document.getElementById('rsyi_hs_track');
		if (!hsType || !trackContainer || !trackSelect) return;

		var eastern = ['general_egyptian', 'azhari'];

		function toggle() {
			if (eastern.indexOf(hsType.value) !== -1) {
				trackContainer.classList.remove('rsyi-hidden');
				trackSelect.required = true;
			} else {
				trackContainer.classList.add('rsyi-hidden');
				trackSelect.required = false;
				trackSelect.value = '';
			}
		}

		hsType.addEventListener('change', toggle);
		toggle();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
