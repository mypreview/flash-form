/**
 * External dependencies
 */
import PropTypes from 'prop-types';

/**
 * Internal dependencies
 */
import { PrintOptions, SortableChoices } from '.';

/**
 * A form component that could be used to generate
 * a list of custom select option controls.
 *
 * @param 	  {Object}  	   props               	   Component properties.
 * @param 	  {string}		   props.className     	   Additional CSS class names.
 * @param 	  {string}  	   props.id 	       	   Field id.
 * @param 	  {boolean}  	   props.isSave        	   Whether the field is meant to be rendered on the front-end.
 * @param 	  {boolean}  	   props.isSelected    	   Whether or not the block item is currently selected.
 * @param 	  {boolean}  	   props.required      	   Whether the field is required to be filled out.
 * @param 	  {Function}  	   props.onChange      	   Function that receives the value of the input.
 * @param 	  {Object}  	   props.style 	   	   	   Inline CSS styles to apply to the component.
 * @param 	  {string}  	   props.value         	   Field value property as the content.
 * @return    {JSX.Element}                        	   Select dropdown element to render.
 */
function SelectControl( { className, id, isSave, isSelected, required, onChange, style, value } ) {
	return isSave || ! isSelected ? (
		<PrintOptions className={ className } id={ id } required={ required } style={ style } value={ value } />
	) : (
		<SortableChoices className={ className } id={ id } onChange={ onChange } style={ style } value={ value } />
	);
}

SelectControl.propTypes = {
	className: PropTypes.string,
	id: PropTypes.string.isRequired,
	isSave: PropTypes.bool,
	isSelected: PropTypes.bool,
	required: PropTypes.bool,
	onChange: PropTypes.func,
	style: PropTypes.object,
	value: PropTypes.string,
};

SelectControl.defaultProps = {
	className: undefined,
	id: undefined,
	isSave: false,
	isSelected: false,
	required: false,
	onChange: () => {},
	style: {},
	value: null,
};

export default SelectControl;
