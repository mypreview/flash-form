/**
 * External dependencies
 */
import { reducer } from '@mypreview/unicorn-js-utils';
import { ErrorMessage } from '@mypreview/unicorn-react-components';
import { useDidMount, useTimeout, useToast, useToggle } from '@mypreview/unicorn-react-hooks';
import { defaultTo, isEqual } from 'lodash';
import PropTypes from 'prop-types';

/**
 * WordPress dependencies
 */
import apiFetch from '@wordpress/api-fetch';
import { Button, ExternalLink, Flex, FlexBlock, PanelBody, TextareaControl, ToggleControl } from '@wordpress/components';
import { ifCondition } from '@wordpress/compose';
import { useState, useReducer } from '@wordpress/element';
import { escapeAttribute } from '@wordpress/escape-html';
import { __ } from '@wordpress/i18n';

/**
 * Block specific constants.
 */
import Constants from '../constants';

/**
 * Constants.
 */
const INITIAL_STATE = { siteKey: '', secretKey: '' };

/**
 * PanelReCaptchaSettings component let users configure their site and secret keys
 * required to establish an integration between the instance of form created
 * by the block and reCAPTCHA V2 service as a layer of protection against spam.
 *
 * @param 	  {Object}  	   props             Component properties.
 * @param 	  {Function}  	   props.onChange    A function that receives the value of the input.
 * @param 	  {boolean}  	   props.value       The current state of the reCAPTCHA integration.
 * @return    {JSX.Element}                      Component to render.
 */
function PanelReCaptchaSettings( { onChange, value: isCaptcha } ) {
	const [ errors, setErrors ] = useState( INITIAL_STATE );
	const [ isBusy, toggleIsBusy ] = useToggle( false );
	const { start } = useTimeout( toggleIsBusy, 2000 );
	const toast = useToast();
	const [ state, dispatch ] = useReducer( reducer, INITIAL_STATE );
	const { siteKey, secretKey } = state;
	const handleOnKeyDown = ( event ) => {
		if ( isEqual( 'Enter', event?.key ) ) {
			event.preventDefault();
			event.stopPropagation();
		}
	};
	const handleOnBlur = ( event ) => {
		const {
			target: { name: inputName, value: inputValue },
		} = event;
		setErrors( ( errs ) => ( { ...errs, [ inputName ]: ! Boolean( inputValue.length ) } ) );
	};
	const handleOnSubmit = async () => {
		// Throttle the request to execute only once every two seconds.
		if ( ! isBusy ) {
			toggleIsBusy();
			try {
				await apiFetch( { data: { siteKey, secretKey }, method: 'POST', path: Constants.RECAPTCHA_ENDPOINT } );
				toast( 'Google reCAPTCHA keys are updated.', 'flash-form' );
			} catch ( { message } ) {
				toast( message );
			} finally {
				start();
			}
		}
	};

	useDidMount( async () => {
		const { siteKey: _siteKey, secretKey: _secretKey } = await apiFetch( { path: Constants.RECAPTCHA_ENDPOINT, method: 'GET' } );
		dispatch( { siteKey: defaultTo( _siteKey, INITIAL_STATE.siteKey ), secretKey: defaultTo( _secretKey, INITIAL_STATE.secretKey ) } );
	} );

	return (
		<PanelBody initialOpen={ false } title={ __( 'reCAPTCHA Settings', 'flash-form' ) }>
			<FlexBlock>
				<ToggleControl
					checked={ Boolean( isCaptcha ) }
					help={ __(
						'This option will add a reCAPTCHA widget represented as an "I’m not a robot" Checkbox, which requires the user to click a checkbox indicating the user is not a robot.',
						'flash-form'
					) }
					label={ __( 'Enable Google reCAPTCHA', 'flash-form' ) }
					onChange={ onChange }
				/>
				{ isCaptcha && (
					<>
						<TextareaControl
							label={ __( 'Site key', 'flash-form' ) }
							name="siteKey"
							onBlur={ handleOnBlur }
							onChange={ ( value ) => dispatch( { siteKey: escapeAttribute( value ) } ) }
							onKeyDown={ handleOnKeyDown }
							rows="2"
							type="text"
							value={ siteKey }
						/>
						<ErrorMessage doRender={ Boolean( errors?.siteKey ) }>{ __( 'Site key is required.', 'flash-form' ) }</ErrorMessage>
						<TextareaControl
							label={ __( 'Secret key', 'flash-form' ) }
							name="secretKey"
							onBlur={ handleOnBlur }
							onChange={ ( value ) => dispatch( { secretKey: escapeAttribute( value ) } ) }
							onKeyDown={ handleOnKeyDown }
							rows="2"
							type="text"
							value={ secretKey }
						/>
						<ErrorMessage doRender={ Boolean( errors?.secretKey ) }>{ __( 'Secret key is required.', 'flash-form' ) }</ErrorMessage>
						<Button disabled={ isBusy } isPrimary onClick={ handleOnSubmit }>
							{ __( 'Save', 'flash-form' ) }
						</Button>
					</>
				) }
			</FlexBlock>
			<Flex className="components-placeholder__learn-more" css={ { '&': { marginTop: 14 } } }>
				<ExternalLink href={ Constants.RECAPTCHA_HELP_LINK }>{ __( 'Learn more about reCAPTCHA V2', 'flash-form' ) }</ExternalLink>
			</Flex>
		</PanelBody>
	);
}

PanelReCaptchaSettings.propTypes = {
	onChange: PropTypes.func,
	value: PropTypes.bool,
};

PanelReCaptchaSettings.defaultProps = {
	onChange: () => {},
	value: false,
};

export default ifCondition( ( { doRender } ) => Boolean( doRender ) )( PanelReCaptchaSettings );
