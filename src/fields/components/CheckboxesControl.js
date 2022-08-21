/**
 * External dependencies
 */
import PropTypes from 'prop-types';

/**
 * Internal dependencies
 */
import { PrintChoices, SortableChoices } from '.';

/**
 * A form component that could be used to generate
 * a list of custom checkbox group controls.
 *
 * @param 	  {Object}  	   props               Component properties.
 * @param 	  {string}		   props.className     Additional CSS class names.
 * @param 	  {string}  	   props.id 	       Field id.
 * @param 	  {boolean}  	   props.isSave        Whether the field is meant to be rendered on the front-end.
 * @param 	  {boolean}  	   props.isSelected    Whether or not the block item is currently selected.
 * @param 	  {Function}  	   props.onChange      Function that receives the value of the input.
 * @param 	  {Object}  	   props.style 	   	   Inline CSS styles to apply to the component.
 * @param 	  {string}  	   props.type 	   	   Input field type. `checkbox`, `radio`, and `select` are supported.
 * @param 	  {string}  	   props.value         Field value property as the content.
 * @return    {JSX.Element}                        Checkbox choices element to render.
 */
function CheckboxesControl( { className, id, isSave, isSelected, onChange, style, type, value } ) {
	return isSave || ! isSelected ? (
		<PrintChoices className={ className } id={ id } style={ style } type={ type } value={ value } />
	) : (
		<SortableChoices className={ className } id={ id } onChange={ onChange } style={ style } type={ type } value={ value } />
	);
}

CheckboxesControl.propTypes = {
	className: PropTypes.string,
	id: PropTypes.string.isRequired,
	isSave: PropTypes.bool,
	isSelected: PropTypes.bool,
	onChange: PropTypes.func,
	style: PropTypes.object,
	type: PropTypes.string.isRequired,
	value: PropTypes.string,
};

CheckboxesControl.defaultProps = {
	className: undefined,
	id: undefined,
	isSave: false,
	isSelected: false,
	onChange: () => {},
	style: {},
	type: 'checkbox',
	value: null,
};

export default CheckboxesControl;
