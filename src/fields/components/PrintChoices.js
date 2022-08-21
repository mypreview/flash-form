/**
 * External dependencies
 */
import { normalizeJsonify, slugify } from '@mypreview/unicorn-js-utils';
import map from 'lodash/map';
import PropTypes from 'prop-types';

/**
 * WordPress dependencies
 */
import { decodeEntities } from '@wordpress/html-entities';

/**
 * Outputs a list of repeatable fieldset for the save function.
 *
 * @param 	  {Object}  	   props              Component properties.
 * @param 	  {string}  	   props.id 	      Field id.
 * @param 	  {string}		   props.className    Additional CSS class names.
 * @param 	  {boolean}  	   props.required     Indicates that the user must specify a value for the input before the form can be submitted.
 * @param 	  {Object}  	   props.style 	   	  Inline CSS styles to apply to the component.
 * @param 	  {string}  	   props.value        Field value property as the content.
 * @return    {JSX.Element}                       Checkbox choices to render.
 */
function PrintChoices( { className, id, required, style, value } ) {
	const choices = normalizeJsonify( value );

	return map( choices, ( choice, index ) => {
		const fieldId = slugify( [ id, index, choice ] );

		return (
			<div key={ fieldId }>
				<input className={ className } id={ fieldId } name={ `${ id }[]` } required={ required } style={ style } type="checkbox" value={ choice } />
				<label htmlFor={ fieldId }>{ decodeEntities( choice ) }</label>
			</div>
		);
	} );
}

PrintChoices.propTypes = {
	className: PropTypes.string,
	id: PropTypes.string.isRequired,
	required: PropTypes.bool,
	style: PropTypes.object,
	value: PropTypes.string,
};

PrintChoices.defaultProps = {
	className: undefined,
	id: '',
	required: false,
	style: {},
	value: null,
};

export default PrintChoices;
