/**
 * External dependencies
 */
import { useDidUpdate } from '@mypreview/unicorn-react-hooks';

/**
 * WordPress dependencies
 */
import { useEffect } from '@wordpress/element';

/**
 * Synchronizes empty attributes with pre-defined default values upon block initialization.
 *
 * @function
 * @name 	  useSyncContextValues
 * @param     {Object}      context          Context object that comes with a Provider React component.
 * @param     {Function}    setAttributes    Function to update individual attributes based on user interactions.
 * @return    {void}
 */
export default ( context, setAttributes ) => {
	const { formId, noLabel } = context;

	useEffect( () => {
		setAttributes( { formId } );
	}, [ formId ] );

	useDidUpdate( () => {
		setAttributes( { noLabel } );
	}, [ noLabel ] );
};
