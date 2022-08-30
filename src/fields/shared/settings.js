/**
 * Internal dependencies
 */
import Edit from '../Edit';
import save from '../save';

/**
 * A field-block requires a few properties to be specified
 * before it can be registered successfully. These are defined
 * through the following configuration object, which includes:
 */
export default {
	apiVersion: 2,
	category: 'flash-form',
	edit: Edit,
	icon: {
		background: '#EEF0F3',
		foreground: '#DE6360',
	},
	parent: [ 'mypreview/flash-form' ],
	save,
	supports: {
		align: false,
		anchor: true,
		color: {
			background: true,
			gradients: false,
			link: false,
			text: true,
			__experimentalSkipSerialization: true,
		},
		html: false,
		lock: true,
		reusable: false,
		spacing: {
			margin: [ 'left', 'right' ],
			padding: true,
			units: [ 'px', 'em', 'rem' ],
		},
		__experimentalBorder: {
			color: true,
			radius: true,
			style: true,
			width: true,
			__experimentalSkipSerialization: true,
		},
	},
	textdomain: 'flash-form',
	usesContext: [ 'borderColor', 'formId', 'noLabel', 'style' ],
};
