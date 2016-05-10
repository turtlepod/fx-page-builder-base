/**
 * JS SCRIPT
**/
jQuery( document ).ready( function( $ ){

	/* Add Row */
	$( 'body' ).on( 'click', '.fxpb-add-row', function(e){
		e.preventDefault();

		 /* Target the template. */
		var template = '.fxpb-templates > .fxpb-' + $( this ).attr( 'data-template' );

		/* Clone the template and add it. */
		$( template ).clone().appendTo( '.fxpb-rows' );

		/* Hide Empty Row Message */
		$( '.fxpb-rows-message' ).hide();
	});

	/* Hide/Show Empty Row Message */
	if( $( '.fxpb-rows > .fxpb-row' ).length ){
		$( '.fxpb-rows-message' ).hide();
	}
	else{
		$( '.fxpb-rows-message' ).show();
	}

	/* Delete Row */
	$( 'body' ).on( 'click', '.fxpb-remove', function(e){
		e.preventDefault();

		/* Delete Row */
		$( this ).parents( '.fxpb-row' ).remove();
		
		/* Show Empty Message When Applicable. */
		if( ! $( '.fxpb-rows > .fxpb-row' ).length ){
			$( '.fxpb-rows-message' ).show();
		}
	});

	/* Make Row Sortable */
	$( '.fxpb-rows' ).sortable({
		handle: '.fxpb-handle',
		cursor: 'grabbing',
	});

});
