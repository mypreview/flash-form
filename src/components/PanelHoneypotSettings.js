/**
 * External dependencies
 */
import { reducer } from '@mypreview/unicorn-js-utils';
import { useDidUpdate } from '@mypreview/unicorn-react-hooks';
import PropTypes from 'prop-types';

/**
 * WordPress dependencies
 */
import { PanelBody, RangeControl, TextControl, TextareaControl, ToggleControl } from '@wordpress/components';
import { useReducer } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

/**
 * A set of settings that let users configure honeypot trap settings.
 *
 * @param 	  {Object}  	   props             			Component properties.
 * @param 	  {Object}  	   props.attributes    		    Available block attributes and their corresponding values.
 * @param 	  {Object}  	   props.attributes.honeypot    The current state of the honeypot settings.
 * @param 	  {Function}  	   props.onChange    			A function that receives the value of the input.
 * @return    {JSX.Element}                      			Component to render.
 */
function PanelHoneypotSettings( { attributes: { honeypot }, onChange } ) {
	const [ state, dispatch ] = useReducer( reducer, honeypot );
	const { autoCompleteOff, a11yMessage, a11yNoLabel, enable, moveInlineCSS, placeholder, timeCheck } = state;

	useDidUpdate( () => {
		onChange( { honeypot: { ...state } } );
	}, [ state ] );

	return (
		<PanelBody initialOpen={ false } title={ __( 'Honeypot Settings', 'flash-form' ) }>
			<ToggleControl
				checked={ Boolean( enable ) }
				help={ __(
					'A honeypot trap is a spam prevention technique designed to trick spam bots into revealing themselves by filling out a hidden input field.',
					'flash-form'
				) }
				label={ __( 'Enable honeypot trap', 'flash-form' ) }
				onChange={ () => dispatch( { enable: ! enable } ) }
			/>
			{ enable && (
				<>
					<TextControl
						help={ __( 'If you’re using placeholders on other fields, this can help the honeypot trap mimic a "real" field.', 'flash-form' ) }
						label={ __( 'Placeholder', 'flash-form' ) }
						onChange={ ( value ) => dispatch( { placeholder: value } ) }
						value={ placeholder }
					/>
					<TextareaControl
						help={ __( 'You can customize the (hidden) accessibility message or keep the default value.', 'flash-form' ) }
						label={ __( 'Accessibility message', 'flash-form' ) }
						onChange={ ( value ) => dispatch( { a11yMessage: value } ) }
						rows="3"
						value={ a11yMessage }
					/>
					<ToggleControl
						checked={ Boolean( autoCompleteOff ) }
						help={ __(
							'To assure the honeypot isn’t auto-completed by a browser, we add an atypical "autocomplete" attribute value. If you are having problems with this, you may switch to the more standard (but less effective) "off" autocomplete value.'
						) }
						label={ __( 'Use standard autocomplete value', 'flash-form' ) }
						onChange={ () => dispatch( { autoCompleteOff: ! autoCompleteOff } ) }
					/>
					<ToggleControl
						checked={ Boolean( moveInlineCSS ) }
						help={ __(
							'By default, the honeypot trap uses inline CSS on the honeypot field to hide it. Enabling this option moves that CSS to the footer of the page. It may help confuse bots.',
							'flash-form'
						) }
						label={ __( 'Move inline CSS', 'flash-form' ) }
						onChange={ () => dispatch( { moveInlineCSS: ! moveInlineCSS } ) }
					/>
					<ToggleControl
						checked={ Boolean( a11yNoLabel ) }
						help={ __( 'If checked, the accessibility label will not be generated.', 'flash-form' ) }
						label={ __( 'Disable accessibility label', 'flash-form' ) }
						onChange={ () => dispatch( { a11yNoLabel: ! a11yNoLabel } ) }
					/>
					<RangeControl
						allowReset
						help={ __(
							'If set, this will perform an additional check for spam bots by tracking the time it has taken to submit the form. Typically, bots submit forms much faster than humans. ',
							'flash-form'
						) }
						label={ __( 'Time check threshold (seconds)', 'flash-form' ) }
						max={ 60 }
						min={ 1 }
						onChange={ ( value ) => dispatch( { timeCheck: value } ) }
						resetFallbackValue={ 10 }
						step={ 1 }
						value={ timeCheck }
					/>
				</>
			) }
		</PanelBody>
	);
}

PanelHoneypotSettings.propTypes = {
	onChange: PropTypes.func,
	value: PropTypes.bool,
};

PanelHoneypotSettings.defaultProps = {
	onChange: () => {},
	value: false,
};

export default PanelHoneypotSettings;
