/**
 * External dependencies
 */
import { jsonify, stringify } from '@mypreview/unicorn-js-utils';
import { ErrorMessage, HtmlAttrs } from '@mypreview/unicorn-react-components';
import { defaultTo, isEqual, keys, map, nth, reduce, size, toPairs } from 'lodash';
import PropTypes from 'prop-types';

/**
 * WordPress dependencies
 */
import { BaseControl, TabPanel, TextControl, ToggleControl } from '@wordpress/components';
import { ifCondition, useInstanceId } from '@wordpress/compose';
import { useCallback, useMemo, useState } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';
import { addQueryArgs, isURL, getQueryArgs, removeQueryArgs } from '@wordpress/url';

/**
 * Internal dependencies
 */
import { baseClassName } from '../utils';

/**
 * Component that renders setting controls specific to the `GET` method submission.
 *
 * @param 	  {Object}  	   props             Component properties.
 * @param 	  {boolean}  	   props.isNewTab    Whether to open form response in a new tab.
 * @param 	  {Function}  	   props.onChange    A callback function invoked when any of the values change.
 * @param 	  {string}  	   props.value       The current value of the action
 * @return    {JSX.Element}                      Panel control components to render.
 */
function GetMethodSettings( { isNewTab, onChange, value: action } ) {
	const instanceId = useInstanceId( GetMethodSettings, `${ baseClassName }-get-method-settings` );
	const [ showError, setError ] = useState( false );
	const { args, hasQueryArgs, url } = useMemo( () => {
		const _args = getQueryArgs( action );
		const _url = ! action ? '' : removeQueryArgs( action, ...keys( _args ) );

		return {
			args: stringify( map( toPairs( _args ), ( arg ) => ( { name: nth( arg ), value: nth( arg, 1 ) } ) ) ),
			hasQueryArgs: size( _args ),
			url: _url,
		};
	}, [ action ] );
	const handleOnChange = useCallback(
		( newQueryArgs ) => {
			const _newQueryArgs = reduce(
				map( jsonify( newQueryArgs ), ( { name, value } ) => ( { [ defaultTo( name, '' ) ]: defaultTo( value, '' ) } ) ),
				( acc, cur ) => ( { ...acc, ...cur } )
			);
			onChange( {
				action: addQueryArgs( url, _newQueryArgs ),
			} );
		},
		[ action ]
	);
	const handleOnClickAddButton = () => {
		const __args = jsonify( args );
		__args.push( { name: ' ', value: ' ' } );
		handleOnChange( stringify( __args ) );
	};

	return (
		<TabPanel
			css={ {
				'.components-tab-panel__tabs': {
					borderBottom: '1px solid #ddd',
					marginBottom: 16,
				},
			} }
			tabs={ [
				{
					name: 'action-tab',
					title: __( 'Action', 'flash-form' ),
				},
				{
					name: 'queryarguments-tab',
					/* translators: %s: HTML filled circle symbol. */
					title: sprintf( __( 'Query Arguments%s', 'flash-form' ), hasQueryArgs ? ' ●' : '' ),
				},
			] }
		>
			{ ( { name } ) =>
				isEqual( 'action-tab', name ) ? (
					<>
						<TextControl
							autoComplete="off"
							onBlur={ () => setError( ! isURL( url ) ) }
							help={ __( 'The URL that processes the form submission.', 'flash-form' ) }
							label={ __( 'URL', 'flash-form' ) }
							onChange={ ( newValue ) => onChange( { action: newValue } ) }
							type="url"
							value={ url }
						/>
						<ErrorMessage doRender={ showError }>{ __( 'Enter a valid URL to processes the form submission.', 'flash-form' ) }</ErrorMessage>
						{ ! showError && (
							<ToggleControl
								checked={ Boolean( isNewTab ) }
								help={ __(
									'Indicates whether to load the response after submitting the form into a new unnamed browsing context.',
									'flash-form'
								) }
								label={ __( 'Open response in a new tab?', 'flash-form' ) }
								onChange={ () => onChange( { isNewTab: ! isNewTab } ) }
							/>
						) }
					</>
				) : (
					<BaseControl
						help={ __( 'You can optionally append arguments as querystring to the provided URL.', 'flash-form' ) }
						label={ __( 'Arguments', 'flash-form' ) }
						id={ `${ instanceId }-action` }
					>
						{ ! showError && (
							<HtmlAttrs
								onChange={ handleOnChange }
								otherAddButtonProps={ { onClick: handleOnClickAddButton, text: __( 'Add query argument', 'flash-form' ) } }
								otherNameProps={ {
									label: __( 'Key', 'flash-form' ),
								} }
								otherRemoveButtonProps={ {
									label: __( 'Delete', 'flash-form' ),
								} }
								otherValueProps={ {
									label: __( 'Value', 'flash-form' ),
								} }
								value={ args }
							/>
						) }
						<ErrorMessage doRender={ showError } status="warning">
							{ __( 'To specify query arguments you need to enter a valid URL.', 'flash-form' ) }
						</ErrorMessage>
					</BaseControl>
				)
			}
		</TabPanel>
	);
}

GetMethodSettings.propTypes = {
	isNewTab: PropTypes.bool,
	onChange: PropTypes.func,
	value: PropTypes.string,
};

GetMethodSettings.defaultProps = {
	isNewTab: false,
	onChange: () => {},
	value: undefined,
};

export default ifCondition( ( { doRender } ) => Boolean( doRender ) )( GetMethodSettings );
