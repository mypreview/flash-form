/**
 * External dependencies
 */
import { useDidUpdate } from '@mypreview/unicorn-react-hooks';
import { merge, defaultTo } from 'lodash';

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
export default ( { style: styles }, { borderColor, style: formStyles }, setAttributes ) => {
	const block = defaultTo( styles, {} );
	const { color, radius, style, width } = defaultTo( formStyles?.border, {} );

	useDidUpdate( () => {
		setAttributes( { borderColor } );
	}, [ borderColor ] );
	useDidUpdate( () => {
		setAttributes( { style: merge( {}, styles, { border: { ...block, color } } ) } );
	}, [ color ] );
	useDidUpdate( () => {
		setAttributes( { style: merge( {}, styles, { border: { ...block, radius } } ) } );
	}, [ radius ] );
	useDidUpdate( () => {
		setAttributes( { style: merge( {}, styles, { border: { ...block, style } } ) } );
	}, [ style ] );
	useDidUpdate( () => {
		setAttributes( { style: merge( {}, styles, { border: { ...block, width } } ) } );
	}, [ width ] );
};
