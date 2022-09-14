/**
 * External dependencies
 */
import { PanelUpsell } from '@mypreview/unicorn-js-upsell';
import isEqual from 'lodash/isEqual';
import PropTypes from 'prop-types';

/**
 * WordPress dependencies
 */
import { InspectorControls } from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import { PanelDisplaySettings, PanelFormSettings, PanelHoneypotSettings } from '.';

/**
 * Inspector Controls appear in the post settings sidebar when a block is being edited.
 *
 * @see 	  https://developer.wordpress.org/block-editor/how-to-guides/block-tutorial/block-controls-toolbar-and-sidebar/
 * @param 	  {Object} 	       props 				  Component properties.
 * @param 	  {Object} 	       props.attributes 	  Available block attributes and their corresponding values.
 * @param     {Function}       props.setAttributes    Function to update individual attributes based on user interactions.
 * @return    {JSX.Element} 						  Component to render.
 */
function Inspector( { attributes, setAttributes } ) {
	const { honeypot, method, noLabel } = attributes;

	return (
		<InspectorControls>
			<PanelFormSettings attributes={ attributes } onChange={ ( value ) => setAttributes( { ...value } ) } />
			<PanelDisplaySettings onChange={ ( value ) => setAttributes( { ...value } ) } value={ noLabel } />
			<PanelHoneypotSettings doRender={ isEqual( 'post', method ) } onChange={ ( value ) => setAttributes( { ...value } ) } value={ honeypot } />
			<PanelUpsell pluginSlug="flash-form" />
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
