/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * All files containing `style` keyword are bundled together. The code used
 * gets applied both to the front of your site and to the editor.
 *
 * @see    https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './style.scss';

/**
 * Internal dependencies
 */
import Edit from './Edit';
import save from './save';
import variations from './variations';
import { collections, registerBlockCollections } from './utils';
import './utils/setCategories';

/**
 * Group all blocks registered from this plugin.
 */
registerBlockCollections( collections );

/**
 * Register child-blocks.
 */
import './fields';

/**
 * Block registration API.
 *
 * @see    https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
registerBlockType( 'mypreview/flash-form', {
	/**
	 * @see    ./Edit.js
	 */
	edit: Edit,

	/**
	 * @see    ./save.js
	 */
	save,

	/**
	 * @see    ./variations.js
	 */
	variations,
} );
