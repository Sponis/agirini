jQuery(document).ready(function() {

	//jscroll panel - mousewheel scroller
	
	if (jQuery('.scroll-pane').length > 0) {
		
		jQuery('.scroll-pane').jScrollPane({
				showArrows: true
		});
	}

	// Toggler for contents in desktop 
	
	// content open in the beginning
	jQuery(".toggle-trigger").addClass('toggle-trigger-open');
	
	// reinitialise scroller on window resize
	jQuery(window).on('resize', function(event) {
		jQuery('.scroll-pane').jScrollPane({
			verticalDragMinHeight: 40,
			verticalDragMaxHeight: 40,
			reinitialise:	true,
			showArrows:		true
		});		
	});
	
	// Toggle show/hide content on click to the targeted element
	jQuery(".toggle-trigger").on('click', function(event) {
		
		// Prevent the default behaviour of the click event in that element
		event.preventDefault();

		
		// Toggle show/hide
		jQuery(".content-toggler").toggle(700, function() {
			
			// Re initialise the scrollpane plugin when the toggled element becomes visible
			// so the scroller can recalculate the correct dimentions
			if(jQuery(this).is(':visible') && jQuery('.scroll-pane').length > 0){
				jQuery('.scroll-pane').jScrollPane({
					verticalDragMinHeight: 40,
					verticalDragMaxHeight: 40,
					reinitialise:	true,
					showArrows:		true
				});
			}			
		});
		
		// Toggle active classname on the trigger element
		jQuery(this).toggleClass('toggle-trigger-open');
	});


	if (jQuery('#ggl-map').length > 0) {
		// Google map api
		
		function initialize() {

			var myLatlng = new google.maps.LatLng(36.4351382, 25.4205852);

			var mapOptions = {
				zoom: 11,
				center: myLatlng
			}
			
			var map = new google.maps.Map(document.getElementById('ggl-map'), mapOptions);
			//	marker;
			 var marker = new google.maps.Marker({
     		 position: myLatlng,
     		 map: map,
     		 title: 'Hello World!'

		});
		}

		google.maps.event.addDomListener(window, 'load', initialize);
	}
});


36.4351382,25.4205852

