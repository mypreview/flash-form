/**
 * External dependencies
 */
import { sanitizeSlug } from '@mypreview/unicorn-js-utils';
import isUndefined from 'lodash/isUndefined';
import PropTypes from 'prop-types';

/**
 * WordPress dependencies
 */
import { TextControl } from '@wordpress/components';
import { ifCondition } from '@wordpress/compose';
import { __ } from '@wordpress/i18n';

/**
 * Text input component that allows specifying a name for the input field/block.
 *
 * @param 	  {Object}  	   props             Component properties.
 * @param 	  {Function}  	   props.onChange    A callback function invoked when any of the values change.
 * @param 	  {string}  	   props.value       The current value of the field.
 * @return    {JSX.Element}                      Control to render.
 */
function NameControl( { onChange, value } ) {
	return (
		<TextControl
			autoComplete="off"
			help={ __(
				'The name attribute is used to reference elements in JavaScript, or to reference form data after the form is submitted. ',
				'flash-form'
			) }
			label={ __( 'Name (Optional)', 'flash-form' ) }
			onChange={ ( newValue ) => onChange( sanitizeSlug( newValue ) ) }
			value={ value }
		/>
	);
}

NameControl.propTypes = {
	onChange: PropTypes.func,
	value: PropTypes.string,
};

NameControl.defaultProps = {
	onChange: () => {},
	value: undefined,
};

export default ifCondition( ( { value } ) => ! isUndefined( value ) )( NameControl );
