/**
 * External dependencies
 */
import { useGetAuthorData, useGetCurrentPost, useGetSiteData } from '@mypreview/unicorn-react-hooks';

/**
 * WordPress dependencies
 */
import { useEffect } from '@wordpress/element';

/**
 * Synchronizes empty attributes with pre-defined default values upon block initialization.
 *
 * @function
 * @name 	  useSyncDefaultValues
 * @param     {string}      clientId               The block’s client id.
 * @param     {Object}      props                  Component properties.
 * @param     {Object}      props.attributes       Available block attributes and their corresponding values.
 * @param     {Function}    props.setAttributes    Function to update individual attributes based on user interactions.
 * @return    {void}
 */
export default ( clientId, { attributes, setAttributes } ) => {
	const { to, subject } = attributes;
	const { authorEmail } = useGetAuthorData();
	const { postTitle } = useGetCurrentPost();
	const { siteTitle } = useGetSiteData();

	useEffect( () => {
		if ( ! to && authorEmail ) {
			setAttributes( { to: authorEmail } );
		}

		if ( ! subject && siteTitle && postTitle ) {
			setAttributes( { subject: `[${ siteTitle }] ${ postTitle }` } );
		}
	}, [ authorEmail, siteTitle, postTitle ] );

	useEffect( () => {
		setAttributes( { id: clientId } );
	}, [ clientId ] );
};
