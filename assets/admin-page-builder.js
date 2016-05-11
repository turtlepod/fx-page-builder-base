/**
 * f(x) Page Builder Base Admin JS
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @link https://shellcreeper.com/wp-page-builder-plugin-from-scratch/
 * @author David Chandra Purnama <david@genbumedia.com>
 * @copyright Copyright (c) 2016, Genbu Media
**/
jQuery( document ).ready( function( $ ){

	/* Function: Update Order */
	function fxPB_UpdateOrder(){

		/* In each of rows */
		$('.fxpb-rows > .fxpb-row').each( function(i){

			/* Increase num by 1 to avoid "0" as first index. */
			var num = i + 1;

			/* Update order number in row title */
			$( this ).find( '.fxpb-order' ).text( num );

			/* In each input in the row */
			$( this ).find( '.fxpb-row-input' ).each( function(i) {

				/* Get field id for this input */
				var field = $( this ).attr( 'data-field' );

				/* Update name attribute with order and field name.  */
				$( this ).attr( 'name', 'fxpb[' + num + '][' + field + ']');
			});
		});
	}

	/* Update Order on Page load */
	fxPB_UpdateOrder();

	/* Make Row Sortable */
	$( '.fxpb-rows' ).sortable({
		handle: '.fxpb-handle',
		cursor: 'grabbing',
		stop: function( e, ui ) {
			fxPB_UpdateOrder();
		},
	});

	/* Add Row */
	$( 'body' ).on( 'click', '.fxpb-add-row', function(e){
		e.preventDefault();
 
		 /* Target the template. */
		var template = '.fxpb-templates > .fxpb-' + $( this ).attr( 'data-template' );

		/* Clone the template and add it. */
		$( template ).clone().appendTo( '.fxpb-rows' );

		/* Hide Empty Row Message */
		$( '.fxpb-rows-message' ).hide();

		/* Update Order */
		fxPB_UpdateOrder();
	});

	/* Hide/Show Empty Row Message On Page Load */
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

		/* Update Order */
		fxPB_UpdateOrder();
	});

});
