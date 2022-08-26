/**
 * External dependencies
 */
import { Icon } from '@mypreview/unicorn-react-components';

/**
 * WordPress dependencies
 */
import { getCategories, setCategories } from '@wordpress/blocks';
import { _x } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import icons from '../assets/icons.json';

/**
 * Sets the block categories.
 */
setCategories( [
	{
		slug: 'flash-form',
		title: _x( 'Flash Form', 'block category', 'flash-form' ),
		icon: <Icon d={ icons?.block } />,
	},
	...getCategories().filter( ( { slug } ) => 'flash-form' !== slug ),
] );
