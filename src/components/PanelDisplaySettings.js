/**
 * WordPress dependencies
 */
import { PanelBody, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Component that renders setting controls specific to `Display` adjustments.
 *
 * @param 	  {Object}  	   props               Component properties.
 * @param 	  {Object}  	   props.attributes    Available block attributes and their corresponding values.
 * @param 	  {Function}  	   props.onChange	   A callback function invoked when any of the values change.
 * @return    {JSX.Element}                        Panel control components to render.
 */
function PanelDisplaySettings( { attributes, onChange } ) {
	const { noLabel } = attributes;

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

export default PanelDisplaySettings;
