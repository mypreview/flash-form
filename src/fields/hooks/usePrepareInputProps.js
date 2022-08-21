/**
 * WordPress dependencies
 */
import { useSelect } from '@wordpress/data';
import { useEffect } from '@wordpress/element';

/**
 * Prepare properties for the "InputControl" component.
 *
 * @function
 * @name 	  usePrepareInputProps
 * @param     {string}      clientId         The block’s client id.
 * @param     {Function}    setAttributes    Function to update individual attributes based on user interactions.
 * @return    {Object}						 An object containing all the properties for the "InputControl" component.
 */
export default ( clientId, setAttributes ) => {
	const { inputClassName, InputComponent, inputIdentifier, inputProps, inputType, inputWrapperClassName } = useSelect(
		( select ) => {
			const { getBlockType } = select( 'core/blocks' );
			const { getBlock } = select( 'core/block-editor' );
			const { name } = getBlock( clientId );
			const { extraProps } = getBlockType( name );
			const { className, Component, identifier, type, wrapperClassName, ...otherProps } = extraProps;

			return {
				inputClassName: className,
				InputComponent: Component,
				inputIdentifier: identifier,
				inputProps: otherProps,
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
