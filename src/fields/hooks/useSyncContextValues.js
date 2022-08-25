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
 * @param     {Object}      context            Context object that comes with a Provider React component.
 * @param     {string}      context.formId     Unique id assigned to the form (parent) block.
 * @param     {boolean}     context.noLabel    Whether the label should only be visible to screen readers.
 * @param     {Function}    setAttributes      Function to update individual attributes based on user interactions.
 * @return    {void}
 */
export default ( { formId, noLabel }, setAttributes ) => {
	useEffect( () => {
		setAttributes( { formId } );
	}, [ formId ] );

	useDidUpdate( () => {
		setAttributes( { noLabel } );
	}, [ noLabel ] );
};
