/**
 * External dependencies
 */
import isEqual from 'lodash/isEqual';
import PropTypes from 'prop-types';

/**
 * WordPress dependencies
 */
import { BaseControl, Button, ButtonGroup, FlexBlock, PanelBody } from '@wordpress/components';
import { useInstanceId } from '@wordpress/compose';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { GetMethodSettings, PostMethodSettings } from '.';
import { baseClassName } from '../utils';

/**
 * PanelFormSettings component provides a set of fields to determine
 * what should happen when the form is submitted by the user.
 *
 * @param 	  {Object}  	   props               Component properties.
 * @param 	  {Object}  	   props.attributes    Available block attributes and their corresponding values.
 * @param 	  {Function}  	   props.onChange	   A callback function invoked when any of the values change.
 * @return    {JSX.Element}                        Panel controls element to render.
 */
function PanelFormSettings( { attributes, onChange } ) {
	const { action, isNewTab, method } = attributes;
	const instanceId = useInstanceId( PanelFormSettings, `${ baseClassName }-panel-form-settings` );

	return (
		<PanelBody initialOpen title={ __( 'Form Settings', 'flash-form' ) }>
			<BaseControl help={ __( 'The HTTP method to submit the form with.', 'flash-form' ) } label={ __( 'Method', 'flash-form' ) } id={ instanceId }>
				<FlexBlock>
					<ButtonGroup>
						<Button
							isPrimary={ isEqual( 'get', method ) }
							onClick={ () =>
								onChange( {
									customThankyou: '',
									customThankyouMessage: undefined,
									customThankyouRedirect: undefined,
									isAjax: false,
									inNewTab: false,
									method: 'get',
								} )
							}
							value="get"
						>
							{ __( 'GET', 'flash-form' ) }
						</Button>
						<Button isPrimary={ isEqual( 'post', method ) } onClick={ () => onChange( { action: undefined, method: 'post' } ) } value="post">
							{ __( 'POST', 'flash-form' ) }
						</Button>
					</ButtonGroup>
				</FlexBlock>
			</BaseControl>
			<GetMethodSettings isNewTab={ isNewTab } onChange={ onChange } doRender={ isEqual( 'get', method ) } value={ action } />
			<PostMethodSettings attributes={ attributes } onChange={ onChange } doRender={ isEqual( 'post', method ) } />
		</PanelBody>
	);
}

PanelFormSettings.propTypes = {
	attributes: PropTypes.object,
	onChange: PropTypes.func,
};

PanelFormSettings.defaultProps = {
	attributes: {},
	onChange: () => {},
};

export default PanelFormSettings;
