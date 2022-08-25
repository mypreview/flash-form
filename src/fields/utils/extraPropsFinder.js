/**
 * External dependencies
 */
import { defaultTo, find, isEqual } from 'lodash';

/**
 * Internal dependencies
 */
import { blocks } from '../shared';

/**
 * Iterates over blocks of collection.
 * Returning the first block predicate returns truthy for.
 *
 * @function
 * @name 	  extraPropsFinder
 * @param 	  {string}    needle    The searched identifier value.
 * @return    {Array}			    Returns the matched element, else undefined.
 */
export default ( needle ) => {
	const block = find( blocks, ( { extraProps: { identifier } } ) => isEqual( identifier, needle ) );
	return { extraProps: defaultTo( block?.extraProps, {} ), block };
};
