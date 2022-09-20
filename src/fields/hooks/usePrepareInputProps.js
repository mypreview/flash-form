/**
 * External dependencies
 */
import { reducer } from '@mypreview/unicorn-js-utils';

/**
 * WordPress dependencies
 */
import { useSelect } from '@wordpress/data';
import { useEffect } from '@wordpress/element';

/**
 * Internal dependencies
 */
import dynamicProps from '../utils/dynamicProps';

/**
 * Prepare properties for the "InputControl" component.
 *
 * @function
 * @name 	  usePrepareInputProps
 * @param     {Object}      attributes    	 Available block attributes and their corresponding values.
 * @param     {string}      clientId         The block’s client id.
 * @param     {Function}    setAttributes    Function to update individual attributes based on user interactions.
 * @return    {Object}						 An object containing all the properties for the "InputControl" component.
 */
export default ( attributes, clientId, setAttributes ) => {
	const { inputClassName, InputComponent, inputIdentifier, inputProps, inputType, inputWrapperClassName } = useSelect(
		( select ) => {
			const { getBlockType } = select( 'core/blocks' );
			const { getBlock } = select( 'core/block-editor' );
			const { name } = getBlock( clientId );
			const { extraProps } = getBlockType( name );
			const { className, Component, dynamic, identifier, type, wrapperClassName, ...otherProps } = extraProps;
			const _inputProps = reducer( otherProps, dynamicProps( dynamic, attributes ) );

			return {
				inputClassName: className,
				InputComponent: Component,
				inputIdentifier: identifier,
				inputProps: _inputProps,
				inputType: type,
				inputWrapperClassName: wrapperClassName,
			};
		},
		[ clientId ]
	);

	useEffect( () => {
		setAttributes( { id: clientId, identifier: inputIdentifier, type: inputType } );
	}, [ clientId, inputIdentifier, inputType ] );

	return { inputClassName, InputComponent, inputIdentifier, inputProps, inputType, inputWrapperClassName };
};
