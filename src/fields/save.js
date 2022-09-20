/**
 * External dependencies
 */
import { blockClassName, reducer, slugify } from '@mypreview/unicorn-js-utils';
import classnames from 'classnames';
import { defaultTo, isEmpty } from 'lodash';

/**
 * WordPress dependencies
 */
import {
	useBlockProps,
	__experimentalGetBorderClassesAndStyles as getBorderClassesAndStyles,
	__experimentalGetColorClassesAndStyles as getColorClassesAndStyles,
	__experimentalGetSpacingClassesAndStyles as getSpacingClassesAndStyles,
} from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import { InputControl, LabelControl } from './components';
import { dynamicProps, extraPropsFinder } from './utils';

/**
 * The save function defines the way in which the different attributes should
 * be combined into the final markup, which is then serialized by the block
 * editor into `post_content`.
 *
 * @see 	  https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#save
 * @param 	  {Object} 		   props               Block meta-data properties.
 * @param 	  {Object} 		   props.attributes    Available block attributes and their corresponding values.
 * @return    {JSX.Element} 					   Field element to render.
 */
function save( { attributes } ) {
	const { defaultValue, id, identifier, isRequired, label, name, noLabel, placeholder, width } = attributes;
	const borderProps = getBorderClassesAndStyles( attributes );
	const colorProps = getColorClassesAndStyles( attributes );
	const spacingProps = getSpacingClassesAndStyles( attributes );
	const blockProps = useBlockProps.save( {
		className: classnames( 'form-field', {
			'has-custom-width': width,
			[ `has-custom-width--${ width }` ]: width,
			[ `form-field--${ slugify( identifier ) }` ]: identifier,
		} ),
	} );
	const className = blockClassName( blockProps?.className );
	const { extraProps } = extraPropsFinder( identifier );
	const {
		className: inputClassName,
		Component: InputComponent,
		dynamic: InputDynamicProps,
		identifier: inputIdentifier,
		type: inputType,
		wrapperClassName: inputWrapperClassName,
		...otherProps
	} = extraProps;
	const inputProps = reducer( otherProps, dynamicProps( InputDynamicProps, attributes ) );

	return (
		<div { ...blockProps }>
			<LabelControl
				doRender={ ! isEmpty( label ) }
				noLabel={ noLabel }
				id={ name || id }
				isRequired={ isRequired }
				isSave
				value={ label }
				wrapperClassName={ `${ className }__label` }
			/>
			<InputControl
				className={ classnames( inputClassName, borderProps.className, colorProps.className ) }
				Component={ InputComponent }
				id={ name || id }
				isSave
				name={ name || id }
				placeholder={ placeholder }
				required={ isRequired }
				style={ {
					...colorProps.style,
					...borderProps.style,
					...spacingProps.style,
				} }
				type={ defaultTo( inputType, inputIdentifier ) }
				value={ defaultValue }
				wrapperClassName={ classnames( `${ className }__input`, inputWrapperClassName ) }
				{ ...inputProps }
			/>
		</div>
	);
}

export default save;
