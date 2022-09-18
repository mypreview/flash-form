/**
 * External dependencies
 */
import { useDidUpdate } from '@mypreview/unicorn-react-hooks';
import defaultTo from 'lodash/defaultTo';

/**
 * WordPress dependencies
 */
import { useCallback } from '@wordpress/element';

/**
 * Synchronizes border styles defined at the form block level.
 *
 * @function
 * @name 	  useSyncBorderStyles
 * @param 	  {Object} 	       attributes    	 	  Available block attributes and their corresponding values.
 * @param 	  {null|Object}    attributes.style		  Border styles specified at the field (block) level.
 * @param     {Object}         context          	  Context object that comes with a Provider React component.
 * @param     {string}         context.borderColor	  The color of the form field’s border.
 * @param     {null|Object}    context.style		  Border styles specified at the form block level.
 * @param     {Function}       setAttributes    	  Function to update individual attributes based on user interactions.
 * @return    {void}
 */
export default ( { style: blockStyles }, { borderColor, style: formStyles }, setAttributes ) => {
	const block = defaultTo( blockStyles, {} );
	const border = defaultTo( blockStyles?.border, {} );
	const form = defaultTo( formStyles?.border, {} );
	const { color, radius, style, width } = form;
	const setStyle = useCallback( ( prop ) => ( { style: { ...block, border: { ...border, ...prop } } } ), [ block, border ] );

	useDidUpdate( () => {
		setAttributes( { borderColor } );
	}, [ borderColor ] );
	useDidUpdate( () => {
		setAttributes( setStyle( { color } ) );
	}, [ color ] );
	useDidUpdate( () => {
		setAttributes( setStyle( { radius } ) );
	}, [ radius ] );
	useDidUpdate( () => {
		setAttributes( setStyle( { style } ) );
	}, [ style ] );
	useDidUpdate( () => {
		setAttributes( setStyle( { width } ) );
	}, [ width ] );
};
