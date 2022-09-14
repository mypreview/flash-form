/**
 * External dependencies
 */
import isEqual from 'lodash/isEqual';

/**
 * WordPress dependencies
 */
import { isURL } from '@wordpress/url';

/**
 * Function to determine whether the form will be submitted via "GET" method.
 *
 * @param     {string}     action    The URL that processes the form submission.
 * @param     {string}     method    The HTTP method name "GET" to submit the form with.
 * @return    {boolean}    			 Returns true when the form can be submitted via the "GET" method.
 */
export default ( action, method = 'get' ) => isURL( action ) && isEqual( 'get', method );
