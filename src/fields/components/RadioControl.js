/**
 * External dependencies
 */
import PropTypes from 'prop-types';

/**
 * Internal dependencies
 */
import { PrintRadios, SortableChoices } from '.';

/**
 * A form component that could be used to generate
 * a list of custom radio group controls.
 *
 * @param 	  {Object}  	   props               Component properties.
 * @param 	  {string}		   props.className     Additional CSS class names.
 * @param 	  {string}  	   props.id 	       Field id.
 * @param 	  {boolean}  	   props.isSave        Whether the field is meant to be rendered on the front-end.
 * @param 	  {boolean}  	   props.isSelected    Whether or not the block item is currently selected.
 * @param 	  {boolean}  	   props.required      Whether the field is required to be filled out.
 * @param 	  {Function}  	   props.onChange      Function that receives the value of the input.
 * @param 	  {Object}  	   props.style 	   	   Inline CSS styles to apply to the component.
 * @param 	  {string}  	   props.type 	   	   Input field type. `checkbox`, `radio`, and `select` are supported.
 * @param 	  {string}  	   props.value         Field value property as the content.
 * @return    {JSX.Element}                        Radio choices element to render.
 */
function RadioControl( { className, id, isSave, isSelected, required, onChange, style, type, value } ) {
	return isSave || ! isSelected ? (
		<PrintRadios className={ className } id={ id } required={ required } style={ style } value={ value } />
	) : (
		<SortableChoices className={ className } id={ id } onChange={ onChange } style={ style } type={ type } value={ value } />
	);
}

RadioControl.propTypes = {
	className: PropTypes.string,
	id: PropTypes.string.isRequired,
	isSave: PropTypes.bool,
	isSelected: PropTypes.bool,
	required: PropTypes.bool,
	onChange: PropTypes.func,
	style: PropTypes.object,
	type: PropTypes.string.isRequired,
	value: PropTypes.string,
};

RadioControl.defaultProps = {
	className: undefined,
	id: undefined,
	isSave: false,
	isSelected: false,
	required: false,
	onChange: () => {},
	style: {},
	type: 'radio',
	value: null,
};

export default RadioControl;
