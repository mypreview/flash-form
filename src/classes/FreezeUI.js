/**
 * External dependencies
 */
import isFunction from 'lodash/isFunction';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * A minimal block UI interface to simulate synchronous behavior when using AJAX.
 *
 * @class
 * @example
 *
 * const freezed = new FreezeUI( {
 *    // DOM element to freeze.
 *    target: document.querySelector( '.wp-block-mypreview-flash-form' ),
 *
 *    // The text to be displayed.
 *    message: 'Please wait',
 * } );
 *
 * freezed.unfreeze();
 */
class FreezeUI {
	/**
	 * @constructs 	  FreezeUI
	 * @param         {Object}    options    Configuration object.
	 */
	constructor( options = {} ) {
		this.freeze( {
			innerHTML: '',
			message: __( 'Hang on…', 'flash-form' ),
			target: document.body,
			...options,
		} );
	}
	freeze( { innerHTML, message, target } ) {
		const $container = document.createElement( 'div' );
		const $parent = target || document.body;

		$container.classList.add( 'freeze-ui' );
		$container.setAttribute( 'data-text', message );
		$container.innerHTML = innerHTML;

		if ( target ) {
			$container.style.position = 'fixed';
		}

		$parent.appendChild( $container );

		return {
			unfreeze( delay = 250 ) {
				this.unfreeze( delay );
			},
		};
	}
	unfreeze( delay, delayCallback ) {
		const $container = document.querySelector( '.freeze-ui' );

		if ( $container ) {
			$container.classList.add( 'is-unfreezing' );
			setTimeout( () => {
				if ( $container ) {
					$container.classList.remove( 'is-unfreezing' );
					$container.parentElement.removeChild( $container );
					if ( isFunction( delayCallback ) ) delayCallback();
				}
			}, delay );
		}
	}
}

export default FreezeUI;
