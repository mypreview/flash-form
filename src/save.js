/**
 * External dependencies
 */
import { blockClassName } from '@mypreview/unicorn-js-utils';
import classnames from 'classnames';
import { isEqual, keys, map, nth, toPairs } from 'lodash';

/**
 * WordPress dependencies
 */
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import { isURL, getQueryArgs, removeQueryArgs } from '@wordpress/url';

/**
 * Internal dependencies
 */
import { HoneypotField } from './components';

/**
 * The save function defines the way in which the different attributes should
 * be combined into the final markup, which is then serialized by the block
 * editor into `post_content`.
 *
 * @see 	  https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#save
 * @param 	  {Object} 		   props               Component properties.
 * @param 	  {Object} 		   props.attributes    Block attributes.
 * @return    {JSX.Element} 					   Form element to render.
 */
function save( { attributes } ) {
	const { action, formId, honeypot, isAjax, isNewTab, method } = attributes;
	const blockProps = useBlockProps.save();
	const className = blockClassName( blockProps?.className );
	const queryArgs = getQueryArgs( action );
	const hasGetUrl = isURL( action ) && isEqual( 'get', method );

	return (
		<div { ...blockProps }>
			<form
				action={ hasGetUrl ? removeQueryArgs( action, ...keys( queryArgs ) ) : '#' }
				className={ classnames( 'contact-form', `${ className }__fieldset`, { 'is-ajax': isAjax } ) }
				encType="application/x-www-form-urlencoded"
				id={ formId }
				method={ method }
				style={ { '--gap': attributes.style?.spacing?.blockGap } }
				target={ hasGetUrl && isNewTab ? '_blank' : undefined }
			>
				<InnerBlocks.Content />
				<HoneypotField doRender={ Boolean( honeypot?.enable ) } formId={ formId } value={ honeypot } />
				{ hasGetUrl && map( toPairs( queryArgs ), ( arg ) => <input key={ nth( arg ) } name={ nth( arg ) } type="hidden" value={ nth( arg, 1 ) } /> ) }
			</form>
		</div>
	);
}

export default save;
