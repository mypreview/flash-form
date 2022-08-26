/**
 * External dependencies
 */
import { WidthPanel } from '@mypreview/unicorn-react-components';
import PropTypes from 'prop-types';

/**
 * WordPress dependencies
 */
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { NameControl } from '.';

/**
 * Inspector Controls appear in the post settings sidebar when a block is being edited.
 *
 * @see 	  https://developer.wordpress.org/block-editor/how-to-guides/block-tutorial/block-controls-toolbar-and-sidebar/
 * @param 	  {Object} 	       props 				  Block meta-data properties.
 * @param 	  {Object} 	       props.attributes 	  Available block attributes and their corresponding values.
 * @param     {Function}       props.setAttributes    Function to update individual attributes based on user interactions.
 * @return    {JSX.Element} 						  Inspector element to render.
 */
function Inspector( { attributes, setAttributes } ) {
	const { name, width } = attributes;

	return (
		<InspectorControls>
			<PanelBody>
				<NameControl onChange={ ( value ) => setAttributes( { name: value } ) } value={ name } />
			</PanelBody>
			<WidthPanel
				help={ __( 'Adjust the width of the field to include multiple fields on a single line.', 'flash-form' ) }
				onChange={ ( value ) => setAttributes( { width: value } ) }
				title={ __( 'Width settings', 'flash-form' ) }
				value={ width }
			/>
		</InspectorControls>
	);
}

Inspector.propTypes = {
	attributes: PropTypes.object,
	setAttributes: PropTypes.func,
};

Inspector.defaultProps = {
	attributes: {},
	setAttributes: () => {},
};

export default Inspector;
