/**
 * External dependencies
 */
import { stringify, normalizeJsonify } from '@mypreview/unicorn-js-utils';
import { AddButton, RemoveButton, Sortable } from '@mypreview/unicorn-react-components';
import { useDidUpdate } from '@mypreview/unicorn-react-hooks';
import { lte, map } from 'lodash';
import PropTypes from 'prop-types';

/**
 * WordPress dependencies
 */
import { Flex, FlexBlock, FlexItem } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { usePrepareOnChange } from '../hooks';

/**
 * A sortable repeatable fields group that allows you to add lists of input entities.
 * Repeatable field groups could be consdiered as a table, containing rows for
 * each of the items and columns for their different fields.
 *
 * Data or value generated using this component will be stored
 * within a single field, simulating a relational structure.
 *
 * @param 	  {Object}  	   props              Component properties.
 * @param 	  {string}		   props.className    Additional CSS class names.
 * @param 	  {string}  	   props.id 	      Field id.
 * @param 	  {Function}  	   props.onChange     Function that receives the value of the input.
 * @param 	  {Object}  	   props.style 	   	  Inline CSS styles to apply to the component.
 * @param 	  {string}  	   props.type 	   	  Input field type. `checkbox`, `radio`, and `select` are supported.
 * @param 	  {string}  	   props.value        Field value property as the content.
 * @return    {JSX.Element}                       Sortable choices element to render.
 */
function SortableChoices( { className, id, onChange, style, type, value } ) {
	const choices = normalizeJsonify( value );
	const { preparedChoices, handleOnSortEnd, handleOnChangeInput, handleOnClickAddButton, handleOnClickRemoveButton } = usePrepareOnChange( choices );

	useDidUpdate( () => {
		onChange( stringify( preparedChoices ) );
	}, [ preparedChoices ] );

	return (
		<>
			<Sortable
				css={ {
					maxHeight: 300,
					overflowY: 'auto',
					position: 'relative',
					'> div': { marginTop: 20, '> div:first-of-type': { height: 24 } },
				} }
				onChange={ handleOnSortEnd }
				withSortableKnob
			>
				{ map( choices, ( choice, index ) => (
					<Flex align="center" choice={ choice } key={ `${ id }-${ index }` } justify="center">
						{ type && (
							<FlexItem>
								<input className={ className } disabled style={ style } type={ type } value={ choice } />
							</FlexItem>
						) }
						<FlexBlock>
							<input
								className="components-text-control__input"
								onChange={ ( event ) => handleOnChangeInput( event.target?.value, index ) }
								type="text"
								value={ choice }
							/>
						</FlexBlock>
						<FlexItem>
							<RemoveButton doRender onClick={ () => handleOnClickRemoveButton( id, index, lte( choices.length, 1 ) ) } />
						</FlexItem>
					</Flex>
				) ) }
			</Sortable>
			<AddButton doRender css={ { marginTop: 22 } } onClick={ handleOnClickAddButton }>
				{ __( 'Add choice', 'flash-form' ) }
			</AddButton>
		</>
	);
}

SortableChoices.propTypes = {
	className: PropTypes.string,
	id: PropTypes.string,
	onChange: PropTypes.func,
	style: PropTypes.object,
	type: PropTypes.string,
	value: PropTypes.string,
};

SortableChoices.defaultProps = {
	className: undefined,
	id: '',
	onChange: () => {},
	style: {},
	type: null,
	value: null,
};

export default SortableChoices;
