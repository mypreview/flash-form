/**
 * External dependencies
 */
import { insertAtIndex, removeAtIndex } from '@mypreview/unicorn-js-utils';
import map from 'lodash/map';

/**
 * WordPress dependencies
 */
import { useDispatch } from '@wordpress/data';
import { useState } from '@wordpress/element';

/**
 * Prepares choices made by interacting with the sortable component.
 *
 * @function
 * @name 	  usePrepareOnChange
 * @param 	  {string}    initialChoices    Choices stored from the previous state.
 * @return    {Object}                      Prepared choices and a few methods to alter existing state.
 */
export default ( initialChoices ) => {
	const { removeBlock } = useDispatch( 'core/block-editor' );
	const [ choices, setChoices ] = useState( initialChoices );

	const handleOnSortEnd = ( value ) => {
		setChoices( map( value, ( { props: { choice } } ) => choice ) );
	};
	const handleOnChangeInput = ( value, index ) => {
		setChoices( ( arr ) => insertAtIndex( arr, value, index ) );
	};
	const handleOnClickAddButton = () => {
		setChoices( ( arr ) => insertAtIndex( arr, '', arr.length ) );
	};
	const handleOnClickRemoveButton = ( clientId, index, shouldDelete ) => {
		if ( clientId && shouldDelete ) {
			removeBlock( clientId );
		} else {
			setChoices( ( arr ) => removeAtIndex( arr, index ) );
		}
	};

	return { preparedChoices: choices, handleOnSortEnd, handleOnChangeInput, handleOnClickAddButton, handleOnClickRemoveButton };
};
