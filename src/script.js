/* global FormData, mypreviewFlashFormLocalizedData */

/**
 * External dependencies
 */
import forEach from 'lodash/forEach';
import { fetch } from 'whatwg-fetch';

/**
 * WordPress dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import domReady from '@wordpress/dom-ready';

/**
 * Internal dependencies
 */
import { FreezeUI } from './classes';
import icons from './assets/icons.json';
import { baseClassName } from './utils';

/**
 * Initialize form submission enhancements on the front-end.
 */
const flashForm = {
	cache() {
		this.vars = {};
		this.els = {};
		this.vars.block = baseClassName;
		this.vars.delay = 3500;
		this.vars.action = 'mypreview_flash_form_submit';
		this.vars.$loading = `<svg height="24" viewBox="0 0 24 24" width="24"><path d="${ icons.loading }" /></svg>`;
		this.els.$ajaxForms = document.querySelectorAll( `.${ this.vars.block } form.is-ajax` );
		this.els.$history = window.history;
		this.els.$submission = document.querySelector( `.${ baseClassName }__submission` );
	},
	ready() {
		this.cache();
		this.afterSubmit();

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
		const $button = $form.querySelector( '[type="submit"]' );
		const formData = new FormData( $form );
		$button.disabled = true;
		formData.append( 'action', flashForm.vars.action );
		const serialized = new URLSearchParams( formData ).toString();
		const freezed = new FreezeUI( { innerHTML: flashForm.vars.$loading } );

		try {
			await fetch( mypreviewFlashFormLocalizedData?.ajaxurl, {
				method: 'POST',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				body: serialized,
			} )
				.then( ( res ) => res.json() )
				.then( ( { data } ) => {
					const response = document.createElement( 'div' );
					response.innerHTML = data;
					freezed.unfreeze( flashForm.vars.delay, () => {
						$form.parentNode.replaceWith( response );
					} );
				} );
		} catch ( { message } ) {
			freezed.unfreeze( flashForm.vars.delay );
			$button.disabled = false;
			/* translators: %s: Error message returned from the API response. */
			throw new Error( sprintf( __( 'The form cannot be submitted because of an error. %s', 'flash-form' ), message ) );
		}
	},
	afterSubmit() {
		// Only purge history when form submission is confirmed.
		if ( this.els.$submission && this.els.$history.replaceState ) {
			// Prevent a resubmit on refresh and back button press/click.
			this.els.$history.replaceState( null, null, window.location.href );
		}
	},
};

// Initialize after DOM loads.
domReady( () => {
	flashForm.ready();
} );
