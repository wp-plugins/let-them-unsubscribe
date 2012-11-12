(function ($) {
	"use strict";
	$(function () {

		$( '#submit' ).click( function(e) {

			if ( confirm(data_object.areusure) ) {
				alert(data_object.endtext);
				$( '#iw-user-unsubscribe' ).submit();
			}
			else {
				e.preventDefault();
				return false;
			}
			

		});
		

;	});
}(jQuery));