/**
 * WordPress dependencies
 */
import { getCategories, setCategories } from '@wordpress/blocks';
import { _x } from '@wordpress/i18n';

/**
 * Sets the block categories.
 */
setCategories( [
	{
		slug: 'flash-form',
		title: _x( 'Flash Form', 'block category', 'flash-form' ),
		icon: null,
	},
	...getCategories().filter( ( { slug } ) => 'flash-form' !== slug ),
] );
