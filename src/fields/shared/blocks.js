/**
 * External dependencies
 */
import { Icon } from '@mypreview/unicorn-react-components';
import { get, has, map, merge, omit } from 'lodash';

/**
 * WordPress dependencies
 */
import { applyFilters } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import icons from '../assets/icons.json';
import { ButtonControl, CheckboxesControl, RadioControl, SelectControl } from '../components';
import { attributes, settings } from '.';
import { transforms } from '../utils';

/**
 * The form block comes with a set of nested blocks and contains
 * other blocks as child components, and this means that each
 * form field is itself an individual block within the main form block.
 *
 * Any of these internal or child form field blocks can be customized
 * independently and rearranged within the main form block’s container.
 *
 * |      Field     |                                    Description                                    |
 * |:--------------:|:---------------------------------------------------------------------------------:|
 * | Checkbox       | Renders by default as a box that is checked (ticked) when activated.              |
 * | Checkbox Group | Displays a set of checkable buttons where multiple items can be selected.         |
 * | Date Picker    | Allows the user to enter a date, with a special date picker interface.            |
 * | Email          | Lets the user enter and edit an e-mail address                                    |
 * | Message        | Displays a multi-line text box.                                                   |
 * | Name           | Viewer’s name.                                                                    |
 * | Number   		| Numeric input element used to let the user enter a number.                        |
 * | Phone Number   | Allows the user to enter and edit a telephone number.                             |
 * | Radio          | Displays a set of checkable buttons that only one item can be selected at a time. |
 * | Select         | Represents a control that provides a menu of options.                             |
 * | Text           | Displays a regular single line text box.                                          |
 * | Website        | Lets the user enter and edit a website URL.                                       |
 */
export default applyFilters(
	'mypreview.flashFormFields',
	map(
		[
			{
				attributes: omit( attributes, [ 'label', 'isRequired', 'name' ] ),
				description: __( 'Add a button and allow form to be submitted.', 'flash-form' ),
				extraProps: {
					className: 'button wp-block-button__link',
					Component: ButtonControl,
					dynamic: { form: 'formId' },
					type: 'submit',
				},
				keywords: [ __( 'submit', 'flash-form' ), __( 'send', 'flash-form' ) ],
				name: 'button',
				supports: {
					multiple: false,
				},
				title: __( 'Submit', 'flash-form' ),
			},
			{
				description: __( 'Add a single checkbox.', 'flash-form' ),
				extraProps: {
					value: 'yes',
				},
				keywords: [ __( 'confirm', 'flash-form' ), __( 'accept', 'flash-form' ) ],
				name: 'checkbox',
				supports: {
					spacing: {
						padding: false,
					},
				},
				title: __( 'Checkbox', 'flash-form' ),
			},
			{
				attributes: omit( attributes, [ 'isRequired' ] ),
				description: __( 'Add several checkbox items. People love options!', 'flash-form' ),
				extraProps: {
					Component: CheckboxesControl,
					type: 'checkbox',
				},
				keywords: [ __( 'choose', 'flash-form' ), __( 'option', 'flash-form' ), __( 'multiple', 'flash-form' ) ],
				name: 'checkboxes',
				supports: {
					spacing: {
						padding: false,
					},
				},
				title: __( 'Checkbox Group', 'flash-form' ),
				transforms: {
					blocks: [ 'select', 'radio' ],
				},
			},
			{
				description: __( 'The best way to set a date. Add a date picker.', 'flash-form' ),
				extraProps: {
					className: 'datepicker',
				},
				keywords: [ __( 'calendar', 'flash-form' ), __( 'date month year', 'flash-form' ) ],
				name: 'date',
				title: __( 'Date Picker', 'flash-form' ),
				transforms: {
					blocks: [ 'email', 'name', 'number', 'tel', 'text', 'textarea', 'url' ],
				},
			},
			{
				description: __( 'Want to reply to folks? Add an email address input.', 'flash-form' ),
				extraProps: {
					autoComplete: 'email username',
				},
				keywords: [ __( 'e-mail', 'flash-form' ), __( 'mail', 'flash-form' ) ],
				name: 'email',
				title: __( 'Email', 'flash-form' ),
				transforms: {
					blocks: [ 'date', 'name', 'number', 'tel', 'text', 'textarea', 'url' ],
				},
			},
			{
				description: __( 'Let folks speak their mind. This text box is great for longer responses.', 'flash-form' ),
				extraProps: {
					rows: 4,
					Component: 'textarea',
					type: null,
				},
				keywords: [ __( 'textarea', 'flash-form' ), __( 'multiline text', 'flash-form' ) ],
				name: 'textarea',
				title: __( 'Message', 'flash-form' ),
				transforms: {
					blocks: [ 'date', 'email', 'name', 'number', 'tel', 'text', 'url' ],
				},
			},
			{
				description: __( 'Introductions are important. Add an input for folks to add their name.', 'flash-form' ),
				extraProps: {
					autoComplete: 'given-name',
					type: 'text',
				},
				keywords: [ __( 'first name', 'flash-form' ), __( 'last name', 'flash-form' ) ],
				name: 'name',
				title: __( 'Name', 'flash-form' ),
				transforms: {
					blocks: [ 'date', 'email', 'number', 'tel', 'text', 'textarea', 'url' ],
				},
			},
			{
				description: __( 'Numeric input element used to let the user enter a number.', 'flash-form' ),
				keywords: [ __( 'integer', 'flash-form' ), __( 'numeric', 'flash-form' ) ],
				name: 'number',
				title: __( 'Number', 'flash-form' ),
				transforms: {
					blocks: [ 'date', 'email', 'name', 'tel', 'text', 'textarea', 'url' ],
				},
			},
			{
				description: __( 'Add a phone number input.', 'flash-form' ),
				extraProps: {
					autoComplete: 'tel',
				},
				keywords: [ __( 'phone', 'flash-form' ), __( 'cellular', 'flash-form' ), __( 'mobile', 'flash-form' ) ],
				name: 'tel',
				title: __( 'Phone Number', 'flash-form' ),
				transforms: {
					blocks: [ 'date', 'email', 'name', 'number', 'text', 'textarea', 'url' ],
				},
			},
			{
				description: __( 'Add several radio button items. Only one radio item can be selected at a time.', 'flash-form' ),
				extraProps: {
					Component: RadioControl,
				},
				keywords: [ __( 'choose', 'flash-form' ), __( 'option', 'flash-form' ), __( 'select', 'flash-form' ) ],
				name: 'radio',
				supports: {
					spacing: {
						padding: false,
					},
				},
				title: __( 'Radio', 'flash-form' ),
				transforms: {
					blocks: [ 'checkboxes', 'select' ],
				},
			},
			{
				description: __( 'Compact, but powerful. Add a select box with several items.', 'flash-form' ),
				extraProps: {
					Component: SelectControl,
				},
				keywords: [ __( 'dropdown', 'flash-form' ), __( 'option', 'flash-form' ), __( 'select', 'flash-form' ) ],
				name: 'select',
				supports: {
					spacing: {
						padding: false,
					},
				},
				title: __( 'Select', 'flash-form' ),
				transforms: {
					blocks: [ 'checkboxes', 'radio' ],
				},
			},
			{
				description: __( 'When you need just a small amount of text, add a text input.', 'flash-form' ),
				keywords: [ __( 'input', 'flash-form' ), __( 'generic', 'flash-form' ) ],
				name: 'text',
				title: __( 'Text', 'flash-form' ),
				transforms: {
					blocks: [ 'date', 'email', 'name', 'number', 'tel', 'textarea', 'url' ],
				},
			},
			{
				description: __( 'Add an address input for a website.', 'flash-form' ),
				extraProps: {
					autoComplete: 'url',
				},
				keywords: [ __( 'url', 'flash-form' ), __( 'internet page', 'flash-form' ) ],
				name: 'url',
				title: __( 'Website', 'flash-form' ),
				transforms: {
					blocks: [ 'date', 'email', 'name', 'number', 'tel', 'text', 'textarea' ],
				},
			},
		],
		( block ) => {
			const identifier = block.name;
			block.icon = { src: <Icon d={ get( icons, identifier ) } /> };
			block.name = `mypreview/flash-form-field-${ identifier }`;

			if ( ! has( block, [ 'attributes' ] ) ) {
				block.attributes = attributes;
			}

			if ( has( block, [ 'transforms' ] ) ) {
				block.transforms = transforms( block.name, block.transforms );
			}

			return merge( {}, settings, merge( {}, block, { extraProps: { identifier } } ) );
		}
	)
);
