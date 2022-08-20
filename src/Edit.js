/**
 * External dependencies
 */
import { blockClassName } from '@mypreview/unicorn-js-utils';
import { useGetBlockVariations, useHasInnerBlocks, useInnerBlocksProps } from '@mypreview/unicorn-react-hooks';
import classnames from 'classnames';
import get from 'lodash/get';

/**
 * WordPress dependencies
 */
import { BlockIcon, useBlockProps, __experimentalBlockVariationPicker as BlockVariationPicker } from '@wordpress/block-editor';
import { createBlocksFromInnerBlocksTemplate } from '@wordpress/blocks';
import { useDispatch } from '@wordpress/data';
import { applyFilters } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { Inspector } from './components';
import { useSyncDefaultValues } from './hooks';
import Constants from './constants';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see 	  https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 * @param 	  {Object}         props    		 Component properties.
 * @param 	  {string} 	       props.clientId    The block’s client id.
 * @return    {JSX.Element} 			  		 Component to render.
 */
function Edit( { clientId, ...otherProps } ) {
	useSyncDefaultValues( clientId, otherProps );
	const { defaultVariation, icon, variations, title } = useGetBlockVariations( clientId );
	const hasInnerBlocks = useHasInnerBlocks( clientId );
	const blockProps = useBlockProps();
	const className = blockClassName( blockProps?.className );
	const innerBlocksProps = useInnerBlocksProps(
		{
			className: classnames( 'contact-form', `${ className }__fieldset` ),
			style: { '--gap': get( otherProps.attributes.style, [ 'spacing', 'blockGap' ] ) },
		},
		{
			allowedBlocks: applyFilters( 'mypreview.flashFormAllowedBlocks', Constants.ALLOWED_BLOCKS ),
		}
	);
	const { replaceInnerBlocks, selectBlock } = useDispatch( 'core/block-editor' );
	const handleOnSelect = ( variation ) => {
		const { attributes, innerBlocks } = variation || defaultVariation;

		if ( attributes ) {
			otherProps.setAttributes( attributes );
		}
		if ( innerBlocks ) {
			replaceInnerBlocks( clientId, createBlocksFromInnerBlocksTemplate( innerBlocks ), true );
		}

		selectBlock( clientId );
	};

	return (
		<div { ...blockProps }>
			{ hasInnerBlocks ? (
				<div { ...innerBlocksProps } />
			) : (
				<BlockVariationPicker
					allowSkip
					icon={ <BlockIcon icon={ get( icon, 'src' ) } /> }
					instructions={ __( 'Select a variation to start with or create your own using the skip option.', 'flash-form' ) }
					label={ title }
					onSelect={ handleOnSelect }
					variations={ variations }
				/>
			) }
			<Inspector { ...otherProps } />
		</div>
	);
}

export default Edit;
