/**
 * External dependencies
 */
import { insertAtIndex, stringify } from '@mypreview/unicorn-js-utils';
import { Icon } from '@mypreview/unicorn-react-components';
import { compact, get, map, nth } from 'lodash';

/**
 * WordPress dependencies
 */
import { applyFilters } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import icons from './assets/icons.json';

/**
 * List of block variations with a set of predefined initial attributes.
 */
export default applyFilters(
	'mypreview.flashFormVariations',
	compact(
		map(
			[
				{
					description: __( 'Add a contact form', 'flash-form' ),
					isDefault: true,
					innerBlocks: [
						[ 'name', { isRequired: true, label: __( 'Name', 'flash-form' ) } ],
						[ 'email', { isRequired: true, label: __( 'Email', 'flash-form' ) } ],
						[ 'textarea', { label: __( 'Message', 'flash-form' ) } ],
						[ 'button', { defaultValue: __( 'Contact us', 'flash-form' ), lock: { remove: true } } ],
					],
					name: 'contact',
					scope: [ 'block' ],
					title: __( 'Contact', 'flash-form' ),
				},
				{
					description: __( 'Add a RSVP form', 'flash-form' ),
					innerBlocks: [
						[ 'name', { isRequired: true, label: __( 'Name', 'flash-form' ) } ],
						[ 'email', { isRequired: true, label: __( 'Email', 'flash-form' ) } ],
						[
							'radio',
							{
								defaultValue: stringify( [ __( 'Yes', 'flash-form' ), __( 'No', 'flash-form' ) ] ),
								isRequired: true,
								label: __( 'Attending?', 'flash-form' ),
							},
						],
						[ 'textarea', { label: __( 'Other Details', 'flash-form' ) } ],
						[ 'button', { defaultValue: __( 'Send RSVP', 'flash-form' ), lock: { remove: true } } ],
					],
					name: 'rsvp',
					scope: [ 'block' ],
					title: __( 'RSVP', 'flash-form' ),
				},
				{
					description: __( 'Add a registration form', 'flash-form' ),
					innerBlocks: [
						[ 'name', { isRequired: true, label: __( 'Name', 'flash-form' ) } ],
						[ 'email', { isRequired: true, label: __( 'Email', 'flash-form' ) } ],
						[ 'tel', { isRequired: true, label: __( 'Phone', 'flash-form' ) } ],
						[
							'select',
							{
								defaultValue: stringify( [
									__( 'Search Engine', 'flash-form' ),
									__( 'Social Media', 'flash-form' ),
									__( 'TV', 'flash-form' ),
									__( 'Radio', 'flash-form' ),
									__( 'Friend of Family', 'flash-form' ),
								] ),
								isRequired: true,
								label: __( 'How did you hear about us?', 'flash-form' ),
							},
						],
						[ 'textarea', { label: __( 'Other Details', 'flash-form' ) } ],
						[ 'button', { defaultValue: __( 'Register', 'flash-form' ), lock: { remove: true } } ],
					],
					name: 'registration',
					scope: [ 'block' ],
					title: __( 'Registration', 'flash-form' ),
				},
				{
					description: __( 'Add an appointment booking form', 'flash-form' ),
					innerBlocks: [
						[ 'name', { isRequired: true, label: __( 'Name', 'flash-form' ) } ],
						[ 'email', { isRequired: true, label: __( 'Email', 'flash-form' ) } ],
						[ 'tel', { isRequired: true, label: __( 'Phone', 'flash-form' ) } ],
						[ 'date', { isRequired: true, label: __( 'Date', 'flash-form' ) } ],
						[
							'radio',
							{
								defaultValue: stringify( [ __( 'Morning', 'flash-form' ), __( 'Afternoon', 'flash-form' ) ] ),
								isRequired: true,
								label: __( 'Time?', 'flash-form' ),
							},
						],
						[ 'textarea', { label: __( 'Notes', 'flash-form' ) } ],
						[ 'button', { defaultValue: __( 'Book Appointment', 'flash-form' ), lock: { remove: true } } ],
					],
					name: 'appointment',
					scope: [ 'block' ],
					title: __( 'Appointment', 'flash-form' ),
				},
				{
					description: __( 'Add a feedback form', 'flash-form' ),
					innerBlocks: [
						[ 'name', { isRequired: true, label: __( 'Name', 'flash-form' ) } ],
						[ 'email', { isRequired: true, label: __( 'Email', 'flash-form' ) } ],
						[
							'radio',
							{
								defaultValue: stringify( [
									__( '1- Very Bad', 'flash-form' ),
									__( '2- Poor', 'flash-form' ),
									__( '3- Average', 'flash-form' ),
									__( '4- Good', 'flash-form' ),
									__( '5- Excellent', 'flash-form' ),
								] ),
								isRequired: true,
								label: __( 'Please rate our website?', 'flash-form' ),
							},
						],
						[ 'textarea', { label: __( 'How could we improve?', 'flash-form' ) } ],
						[ 'button', { defaultValue: __( 'Send Feedback', 'flash-form' ), lock: { remove: true } } ],
					],
					name: 'feedback',
					scope: [ 'block' ],
					title: __( 'Feedback', 'flash-form' ),
				},
			],
			( variation ) => {
				variation.icon = <Icon d={ get( icons, variation.name ) } />;
				variation.innerBlocks = map( variation?.innerBlocks, ( innerBlock ) =>
					insertAtIndex( innerBlock, `mypreview/flash-form-field-${ nth( innerBlock ) }`, 0 )
				);
				variation.name = `${ variation.name }-variation`;
				return variation;
			}
		)
	)
);
