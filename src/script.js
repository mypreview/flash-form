/* global FormData, mypreviewFlashFormLocalizedData */

/**
 * External dependencies
 */
import forEach from 'lodash/forEach';
import { fetch } from 'whatwg-fetch';
import '../node_modules/freezeui/freeze-ui';

/**
 * WordPress dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import domReady from '@wordpress/dom-ready';

/**
 * Initialize form submission enhancements on the front-end.
 */
const flashFormBlock = {
	cache() {
		this.vars = {};
		this.els = {};
		this.vars.block = 'wp-block-mypreview-flash-form';
		this.els.$ajaxForms = document.querySelectorAll( `.${ this.vars.block } form.is-ajax` );
	},
	ready() {
		this.cache();

		if ( this.els.$ajaxForms ) {
			this.onSubmit();
		}
	},
	onSubmit() {
		forEach( this.els.$ajaxForms, ( $block ) => {
			$block.addEventListener( 'submit', this.handleOnSubmit );
		} );
	},
	async handleOnSubmit( event ) {
		event.preventDefault();
		const $form = event.target;
		const formData = new FormData( $form );
		formData.append( 'action', 'mypreview_flash_form_submit' );
		const serialized = new URLSearchParams( formData ).toString();
		window.FreezeUI( { text: __( 'Please wait', 'flash-form' ) } );

		try {
			await fetch( mypreviewFlashFormLocalizedData?.ajaxurl, {
				method: 'POST',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				body: serialized,
			} )
				.then( ( res ) => res.json() )
				.then( ( { data } ) => {
					window.UnFreezeUI();
					const response = document.createElement( 'div' );
					response.innerHTML = data;
					$form.parentNode.replaceWith( response );
				} );
		} catch ( { message } ) {
			window.UnFreezeUI();
			/* translators: %s: Error message returned from the API response. */
			throw new Error( sprintf( __( 'The form cannot be submitted because of an error. %s', 'flash-form' ), message ) );
		}
	},
};

// Initialize after DOM loads.
domReady( () => {
	flashFormBlock.ready();
} );
