/**
 * External dependencies
 */
import { EditableText } from '@mypreview/unicorn-react-components';
import classnames from 'classnames';
import PropTypes from 'prop-types';

/**
 * WordPress dependencies
 */
import { ToggleControl } from '@wordpress/components';
import { ifCondition } from '@wordpress/compose';
import { __, _x } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import Constants from '../constants';

/**
 * A form component that provides a label for form fields.
 *
 * @param 	  {Object}  	   props               		 Component properties.
 * @param 	  {string}  	   props.className 	   		 Component specific CSS class names.
 * @param 	  {boolean}  	   props.noLabel 	   		 Whether the label should only be visible to screen readers.
 * @param 	  {string}  	   props.id 	       		 Field id.
 * @param 	  {boolean}  	   props.isRequired    		 Whether the field is required to be filled out.
 * @param 	  {boolean}  	   props.isSelected    		 Whether or not the block item is currently selected.
 * @param 	  {boolean}  	   props.isSave        	     Whether the field is meant to be rendered on the front-end.
 * @param 	  {Function}  	   props.onChange 	   	     Function that receives the value of the input.
 * @param 	  {Function}  	   props.onChangeNoLabel     Function that toggles the screen-reader control value.
 * @param 	  {string}  	   props.value         	     Label property as the content.
 * @param 	  {string}  	   props.wrapperClassName    CSS class name generated for the block.
 * @return    {JSX.Element}                        		 Label element to render.
 */
function LabelControl( { className, noLabel, id, isRequired, isSelected, isSave, onChange, onChangeNoLabel, value, wrapperClassName } ) {
	return (
		<div className={ classnames( wrapperClassName, { 'visually-hidden': isSave && noLabel } ) }>
			<EditableText
				allowedFormats={ Constants.ALLOWED_LABEL_FORMATS }
				className={ className }
				htmlFor={ id }
				isSave={ isSave }
				onChange={ onChange }
				doRender
				tagName="label"
				value={ value }
				withoutInteractiveFormatting={ false }
			/>
			{ isRequired && (
				<abbr className="required" title={ __( '(required)', 'flash-form' ) }>
					{
						/* translators: Require field symbol. */
						_x( '*', 'required symbol', 'flash-form' )
					}
				</abbr>
			) }
			{ ! isSave && isSelected && (
				<ToggleControl
					checked={ Boolean( noLabel ) }
					css={ {
						marginLeft: 'auto',
					} }
					label={ __( 'Hide?', 'flash-form' ) }
					onChange={ onChangeNoLabel }
				/>
			) }
		</div>
	);
}

LabelControl.propTypes = {
	className: PropTypes.string,
	noLabel: PropTypes.bool,
	id: PropTypes.string.isRequired,
	isRequired: PropTypes.bool,
	isSelected: PropTypes.bool,
	isSave: PropTypes.bool,
	onChange: PropTypes.func,
	onChangeNoLabel: PropTypes.func,
	value: PropTypes.string.isRequired,
	wrapperClassName: PropTypes.string,
};

LabelControl.defaultProps = {
	className: undefined,
	noLabel: false,
	id: undefined,
	isRequired: false,
	isSelected: false,
	isSave: false,
	onChange: () => {},
	onChangeNoLabel: () => {},
	value: undefined,
	wrapperClassName: undefined,
};

export default ifCondition( ( { doRender } ) => Boolean( doRender ) )( LabelControl );
