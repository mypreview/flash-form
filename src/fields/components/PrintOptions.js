/**
 * External dependencies
 */
import { formattedContent, normalizeJsonify, slugify } from '@mypreview/unicorn-js-utils';
import map from 'lodash/map';
import PropTypes from 'prop-types';

/**
 * Outputs a list of repeatable fieldset for the save function.
 *
 * @param 	  {Object}  	   props              Component properties.
 * @param 	  {string}  	   props.id 	      Field id.
 * @param 	  {string}		   props.className    Additional CSS class names.
 * @param 	  {boolean}  	   props.required     Indicates that the user must specify a value for the input before the form can be submitted.
 * @param 	  {Object}  	   props.style 	   	  Inline CSS styles to apply to the component.
 * @param 	  {string}  	   props.value        Field value property as the content.
 * @return    {JSX.Element}                       Sortable options element to render.
 */
function PrintOptions( { className, id, required, style, value } ) {
	const options = normalizeJsonify( value );

	return (
		<select className={ className } name={ id } required={ required } style={ style }>
			{ map( options, ( option, index ) => {
				const fieldId = slugify( [ id, index, option ] );

				return (
					<option key={ fieldId } value={ option }>
						{ formattedContent( option ) }
					</option>
				);
			} ) }
		</select>
	);
}

PrintOptions.propTypes = {
	className: PropTypes.string,
	id: PropTypes.string.isRequired,
	required: PropTypes.bool,
	style: PropTypes.object,
	value: PropTypes.string,
};

PrintOptions.defaultProps = {
	className: undefined,
	id: '',
	required: false,
	style: {},
	value: null,
};

export default PrintOptions;
