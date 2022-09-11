/**
 * External dependencies
 */
import classnames from 'classnames';
import PropTypes from 'prop-types';

/**
 * WordPress dependencies
 */
import { ifCondition } from '@wordpress/compose';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { InputControl, LabelControl } from '../fields/components';
import { baseClassName } from '../utils';

/**
 * Constants.
 */
const CLASSNAME = `${ baseClassName }-field-hp`;

/**
 * This component renders field and label elements for the honeypot trap.
 * Basic implementation of a honeypot trap for the Form block.
 *
 * @param 	  {Object}  	   props           Component properties.
 * @param 	  {string}  	   props.formId    A block/form’s clientId.
 * @param 	  {Object}  	   props.value     The current state of the honeypot settings.
 * @return    {JSX.Element}                    Component to render.
 */
function HoneypotField( { formId, value } ) {
	const style = {};
	const { autoCompleteOff, a11yMessage, a11yNoLabel, moveInlineCSS, placeholder } = value;
	const id = `hp-${ formId }`;

	if ( ! moveInlineCSS ) {
		style.display = 'none';
		style.visibility = 'hidden';
	}
	return (
		<div className={ classnames( CLASSNAME, 'form-field', 'form-field--hp' ) } style={ style }>
			<LabelControl
				doRender={ ! Boolean( a11yNoLabel ) }
				id={ id }
				isRequired={ false }
				isSave
				value={ a11yMessage || __( 'Please leave this field empty.', 'flash-form' ) }
				wrapperClassName={ `${ CLASSNAME }__label` }
			/>
			<InputControl
				autoComplete={ Boolean( autoCompleteOff ) ? 'off' : 'new-password' }
				id={ id }
				isSave
				name={ id }
				placeholder={ placeholder }
				required={ false }
				size="40"
				tabIndex="-1"
				type="text"
				wrapperClassName={ `${ CLASSNAME }__input` }
			/>
		</div>
	);
}

HoneypotField.propTypes = {
	formId: PropTypes.string,
	value: PropTypes.object,
};

HoneypotField.defaultProps = {
	formId: undefined,
	value: {},
};

export default ifCondition( ( { doRender } ) => Boolean( doRender ) )( HoneypotField );
