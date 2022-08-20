/**
 * External dependencies
 */
import { EmailControl } from '@mypreview/unicorn-react-components';
import { useDidUpdate, useGetAuthorData } from '@mypreview/unicorn-react-hooks';
import { defaultTo, isEqual } from 'lodash';
import PropTypes from 'prop-types';

/**
 * WordPress dependencies
 */
import { URLInput } from '@wordpress/block-editor';
import { SelectControl, TextControl, TextareaControl, ToggleControl } from '@wordpress/components';
import { ifCondition } from '@wordpress/compose';
import { __, sprintf } from '@wordpress/i18n';

/**
 * Component that renders setting controls specific to the `POST` method submission.
 *
 * @param 	  {Object}  	   props               Component properties.
 * @param 	  {string}  	   props.attributes    Available block attributes and their corresponding values.
 * @param 	  {Function}  	   props.onChange      A callback function invoked when any of the values change.
 * @return    {JSX.Element}                        Panel control components to render.
 */
function PostMethodSettings( { attributes, onChange } ) {
	const { authorEmail } = useGetAuthorData();
	const { customThankyou, customThankyouMessage, customThankyouRedirect, isAjax, subject, to } = attributes;

	useDidUpdate( () => {
		if ( isEqual( 'redirect', customThankyou ) ) {
			onChange( { isAjax: false } );
		}
	}, [ customThankyou ] );

	return (
		<>
			<EmailControl
				help={
					/* translators: %s: Post author email address. */ sprintf(
						__( 'Enter recipients (comma separated) for this form. Defaults to %s.', 'flash-form' ),
						authorEmail
					)
				}
				label={ __( 'Recipient(s)', 'flash-form' ) }
				onChange={ ( value ) => onChange( { to: value } ) }
				placeholder={ __( 'name@example.com', 'flash-form' ) }
				value={ to }
				withError
			/>
			<TextControl
				label={ __( 'Subject', 'flash-form' ) }
				help={ __( 'Choose a subject line that you recognize as an email from your website.', 'flash-form' ) }
				onChange={ ( value ) => onChange( { subject: value } ) }
				placeholder={ __( 'Enter a subject line', 'flash-form' ) }
				value={ subject }
			/>
			<SelectControl
				label={ __( 'On Submission', 'flash-form' ) }
				onChange={ ( value ) => onChange( { customThankyou: value } ) }
				options={ [
					{ label: __( 'Show a summary of submitted fields', 'flash-form' ), value: '' },
					{ label: __( 'Show a custom text message', 'flash-form' ), value: 'message' },
					{ label: __( 'Redirect to another webpage', 'flash-form' ), value: 'redirect' },
				] }
				value={ customThankyou }
			/>
			{ isEqual( 'message', customThankyou ) && (
				<TextareaControl
					label={ __( 'Message Text', 'flash-form' ) }
					onChange={ ( value ) => onChange( { customThankyouMessage: value } ) }
					placeholder={ __( 'Thank you for your submission!', 'flash-form' ) }
					value={ customThankyouMessage }
				/>
			) }
			{ isEqual( 'redirect', customThankyou ) ? (
				<URLInput
					css={ {
						'&&': {
							input: {
								border: '1px solid #757575',
								borderRadius: 2,
								padding: '6px 8px',
								width: '100%',
							},
						},
					} }
					label={ __( 'Redirect address', 'flash-form' ) }
					onChange={ ( value ) => onChange( { customThankyouRedirect: value } ) }
					value={ customThankyouRedirect }
				/>
			) : (
				<ToggleControl
					checked={ Boolean( isAjax ) }
					help={ __(
						'This option allows you to send data in the background, eliminating the need to reload the page to see the confirmation.',
						'flash-form'
					) }
					label={ __( 'Ajaxify form submission', 'flash-form' ) }
					onChange={ ( value ) => onChange( { isAjax: defaultTo( value, ! isAjax ) } ) }
				/>
			) }
		</>
	);
}

PostMethodSettings.propTypes = {
	customThankyou: PropTypes.string,
	customThankyouMessage: PropTypes.string,
	customThankyouRedirect: PropTypes.string,
	isAjax: PropTypes.bool,
	onChange: PropTypes.func,
};

PostMethodSettings.defaultProps = {
	customThankyou: '',
	customThankyouMessage: '',
	customThankyouRedirect: '',
	isAjax: false,
	onChange: () => {},
};

export default ifCondition( ( { doRender } ) => Boolean( doRender ) )( PostMethodSettings );
