/**
 * External dependencies
 */
import { EditableText } from '@mypreview/unicorn-react-components';
import PropTypes from 'prop-types';

/**
 * A form component that represents a submit button element.
 *
 * @param 	  {Object}  	   props              Component properties.
 * @param 	  {string}  	   props.className    CSS class name generated for the block.
 * @param 	  {string}  	   props.form    	  Form id.
 * @param 	  {string}  	   props.id 	      Field id.
 * @param 	  {boolean}  	   props.isSave       Whether the field is meant to be rendered on the front-end.
 * @param 	  {Function}  	   props.onChange 	  Function that receives the value of the input.
 * @param 	  {Object}  	   props.style        Label property as the content.
 * @param 	  {string}  	   props.type 	   	  Input field type.
 * @param 	  {string}  	   props.value        Label property as the content.
 * @return    {JSX.Element}                       Submit button element to render.
 */
function ButtonControl( { className, form, id, isSave, onChange, style, type, value } ) {
	return (
		<EditableText
			allowedFormats={ [] }
			className={ className }
			doRender
			form={ form }
			id={ id }
			isSave={ isSave }
			onChange={ onChange }
			tagName={ isSave ? 'button' : 'div' }
			type={ isSave ? type : undefined }
			style={ style }
			value={ value }
		/>
	);
}

ButtonControl.propTypes = {
	className: PropTypes.string,
	id: PropTypes.string,
	form: PropTypes.string,
	isSave: PropTypes.bool,
	onChange: PropTypes.func,
	style: PropTypes.object,
	type: PropTypes.string,
	value: PropTypes.string.isRequired,
};

ButtonControl.defaultProps = {
	className: undefined,
	id: undefined,
	form: undefined,
	isSave: false,
	onChange: () => {},
	style: {},
	type: 'submit',
	value: undefined,
};

export default ButtonControl;
