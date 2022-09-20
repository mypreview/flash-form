/**
 * External dependencies
 */
import { defaultTo, get, map, nth } from 'lodash';

/**
 * Iterate over dynamic attributes and assign each with a corresponding value from block attributes.
 *
 * @function
 * @name 	  dynamicProps
 * @param     {Object}    props    		Dynamic block component properties.
 * @param     {Object}    attributes    Available block attributes and their corresponding values.
 * @return    {Object}					Additional block properties associated with their dynamic values extracted from block attributes.
 */
export default ( props, attributes ) => defaultTo( nth( map( props, ( value, key ) => ( { [ key ]: get( attributes, value ) } ) ) ), {} );
