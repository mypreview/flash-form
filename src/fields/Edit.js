/**
 * External dependencies
 */
import { blockClassName, slugify } from '@mypreview/unicorn-js-utils';
import { IsRequiredToolbarControl } from '@mypreview/unicorn-react-components';
import classnames from 'classnames';
import { defaultTo, isString, isUndefined } from 'lodash';

/**
 * WordPress dependencies
 */
import {
	useBlockProps,
	__experimentalUseBorderProps as useBorderProps,
	__experimentalUseColorProps as useColorProps,
	__experimentalGetSpacingClassesAndStyles as useSpacingProps,
} from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { InputControl, Inspector, LabelControl } from './components';
import { usePrepareInputProps, useSyncBorderStyles, useSyncContextValues } from './hooks';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see 	  https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 * @param 	  {Object}         props    		   	  Component properties.
 * @param 	  {Object} 	       props.attributes    	  Available block attributes and their corresponding values.
 * @param 	  {string} 	       props.clientId    	  The block’s client id.
 * @param 	  {Object} 	       props.context    	  Context object that comes with a Provider React component.
 * @param 	  {boolean}  	   props.isSelected    	  Whether or not the block item is currently selected.
 * @param 	  {Function} 	   props.setAttributes    Function to update individual attributes based on user interactions.
 * @return    {JSX.Element} 			  			  Component to render.
 */
function Edit( { attributes, clientId, context, isSelected, setAttributes } ) {
	useSyncContextValues( context, setAttributes );
	useSyncBorderStyles( attributes, context, setAttributes );
	const { inputClassName, InputComponent, inputIdentifier, inputProps, inputType, inputWrapperClassName } = usePrepareInputProps( clientId, setAttributes );
	const borderProps = useBorderProps( attributes );
	const colorProps = useColorProps( attributes );
	const spacingProps = useSpacingProps( attributes );
	const { defaultValue, formId, isRequired, label, noLabel, placeholder, width } = attributes;
	const blockProps = useBlockProps( {
		className: classnames( 'form-field', {
			'has-custom-width': width,
			[ `has-custom-width--${ width }` ]: width,
			[ `form-field--${ slugify( inputIdentifier ) }` ]: inputIdentifier,
		} ),
	} );
	const className = blockClassName( blockProps?.className );
	const handleOnChangeInput = ( input ) => {
		if ( isString( input ) ) {
			setAttributes( { defaultValue: input } );
		} else {
			const value = input.target?.value;
			setAttributes( { placeholder: value } );
		}
	};

	return (
		<div { ...blockProps }>
			<LabelControl
				doRender={ ( isSelected || ! noLabel ) && ! isUndefined( label ) }
				noLabel={ noLabel }
				isSelected={ isSelected }
				isRequired={ isRequired }
				onChange={ ( value ) => setAttributes( { label: value } ) }
				onChangeNoLabel={ ( value ) => setAttributes( { noLabel: value } ) }
				value={ label }
				wrapperClassName={ `${ className }__label` }
			/>
			<InputControl
				className={ classnames( 'components-text-control__input', inputClassName, borderProps.className, colorProps.className ) }
				Component={ InputComponent }
				form={ formId }
				id={ clientId }
				isSelected={ isSelected }
				onChange={ handleOnChangeInput }
				required={ isRequired }
				style={ {
					...colorProps.style,
					...borderProps.style,
					...spacingProps.style,
				} }
				type={ defaultTo( inputType, inputIdentifier ) }
				value={ defaultValue || placeholder || '' }
				wrapperClassName={ classnames( `${ className }__input`, inputWrapperClassName ) }
				{ ...inputProps }
			/>
			<IsRequiredToolbarControl
				doRender={ ! isUndefined( isRequired ) }
				label={ __( 'Required?', 'flash-form' ) }
				onClick={ () => setAttributes( { isRequired: ! isRequired } ) }
				value={ isRequired }
			/>
			<Inspector attributes={ attributes } setAttributes={ setAttributes } />
		</div>
	);
}

export default Edit;
