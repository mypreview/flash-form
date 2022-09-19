/**
 * External dependencies
 */
import { defaultTo, map, omit } from 'lodash';

/**
 * WordPress dependencies
 */
import { createBlock } from '@wordpress/blocks';

/**
 * Generates a transformation object allowing field blocks to be converted into a different one.
 * The API adds a corresponding UI control within each field/block toolbar.
 *
 * @function
 * @name 	  transforms
 * @param     {string}    name    		  Block name.
 * @param     {Object}    props    		  Transform API properties.
 * @param     {Array}     props.blocks    A list of known block types
 * @param     {Array}     props.paths     The property paths to omit.
 * @return    {void}
 */
export default ( name, { blocks, paths } ) => ( {
	from: [
		{
			blocks: map( blocks, ( block ) => `mypreview/flash-form-field-${ block }` ),
			transform: ( attributes ) => createBlock( name, omit( attributes, defaultTo( paths, [] ) ) ),
			type: 'block',
		},
	],
} );
