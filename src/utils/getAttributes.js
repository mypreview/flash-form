/**
 * External dependencies
 */
import { ifArray } from '@mypreview/unicorn-js-utils';
import get from 'lodash/get';

/**
 * Internal dependencies
 */
import metadata from '../../block.json';

/**
 * Constant
 */
const { attributes: ATTRIBUTES } = metadata;

/**
 * Extracts the value at path of attribute object.
 *
 * @param     {Array}    path    The path of the attribute property to get.
 * @return    {*}        		 Returns the attribute node given extracted from the metadata information.
 */
export default ( path ) => ( ifArray( path ) ? get( ATTRIBUTES, path ) : ATTRIBUTES );
