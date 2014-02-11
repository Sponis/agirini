jQuery(document).ready(function() {

	// Toggler for contents in desktop 
	
	// Hide content on start
	jQuery(".content-toggler").hide();
	
	// Toggle show/hide content on click to the targeted element
	jQuery(".toggle-trigger").on('click', function(event) {
		
		// prevent the default behaviour of the click event in that element
		event.preventDefault();
		
		// Toggle show/hide
		jQuery(".content-toggler").toggle(700);
		// Toggle active classname on the trigger element
		jQuery(this).toggleClass('toggle-trigger-open');
	});


	if (jQuery('#ggl-map').length > 0) {
		//google map api
		var map;

		function initialize() {
			
			var mapOptions = {
				zoom: 14,
				center: new google.maps.LatLng(36.431, 25.434)
			};
			
			map = new google.maps.Map(document.getElementById('ggl-map'), mapOptions);
			//	console.log(map);
		}

		google.maps.event.addDomListener(window, 'load', initialize);
	};

});