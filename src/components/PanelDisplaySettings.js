/**
 * External dependencies
 */
import PropTypes from 'prop-types';

/**
 * WordPress dependencies
 */
import { PanelBody, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Component that renders setting controls specific to `Display` adjustments.
 *
 * @param 	  {Object}  	   props               		   Component properties.
 * @param 	  {Object}  	   props.attributes    		   Available block attributes and their corresponding values.
 * @param 	  {boolean}  	   props.attributes.noLabel    Current status of the fieldset label visibility.
 * @param 	  {Function}  	   props.onChange	   		   A callback function invoked when any of the values change.
 * @return    {JSX.Element}                        		   Component to render.
 */
function PanelDisplaySettings( { attributes: { noLabel }, onChange } ) {
	return (
		<PanelBody initialOpen={ false } title={ __( 'Display Settings', 'flash-form' ) }>
			<ToggleControl
				checked={ Boolean( noLabel ) }
				help={ __(
					'Enabling this option will visually hide labels. The labels are still visible to screen readers. This option can be overridden for each input field individually.',
					'flash-form'
				) }
				label={ __( 'Hide labels from view?', 'flash-form' ) }
				onChange={ () => onChange( { noLabel: ! noLabel } ) }
			/>
		</PanelBody>
	);
}

PanelDisplaySettings.propTypes = {
	attributes: PropTypes.object,
	noLabel: PropTypes.bool,
	setAttributes: PropTypes.func,
};

PanelDisplaySettings.defaultProps = {
	attributes: {},
	noLabel: {},
	setAttributes: () => {},
};

export default PanelDisplaySettings;
