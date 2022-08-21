/**
 * External dependencies
 */
import forEach from 'lodash/forEach';

/**
 * WordPress dependencies
 */
import { registerBlockCollection } from '@wordpress/blocks';

/**
 * Function to register block collections for grouping together all blocks from this plugin.
 *
 * @param     {Array}    collections    List of collections to register.
 * @return    {void}
 */
export default ( collections ) => {
	forEach( collections, ( collection ) => {
		if ( ! collection ) {
			return;
		}

		const { name, settings } = collection;

		registerBlockCollection( name, settings );
	} );
};
