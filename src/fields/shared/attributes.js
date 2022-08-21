/**
 * Set of block registration attributes
 * shared across all input-field block types.
 *
 * Attributes provide the structured data needs of a block.
 * They can exist in different forms when they are serialized,
 * however, they are declared together under a common interface.
 */
export default Object.freeze( {
	defaultValue: {
		type: 'string',
	},
	formId: {
		type: 'string',
		default: '',
	},
	id: {
		type: 'string',
	},
	identifier: {
		type: 'string',
	},
	isRequired: {
		type: 'boolean',
		default: false,
	},
	label: {
		type: 'string',
		default: '',
	},
	name: {
		type: 'string',
		default: '',
	},
	noLabel: {
		type: 'boolean',
		default: false,
	},
	placeholder: {
		type: 'string',
	},
	type: {
		type: 'string',
		default: '',
	},
	width: {
		type: 'number',
		default: 100,
	},
} );
