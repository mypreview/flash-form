/**
 * External dependencies
 */
import PropTypes from 'prop-types';

/**
 * WordPress dependencies
 */
import { SVG, Path } from '@wordpress/primitives';

/**
 * A drop-in component replaces the SVG element by adding the required
 * accessibility attributes for SVG elements across browsers.
 *
 * @param     {Object}         props                   Component properties.
 * @param  	  {string}         props.d                 The "d" attribute defines a path to be drawn.
 * @param  	  {Object}         props.otherPathProps    Additional properties to be added to the "Path" component.
 * @return    {JSX.Element}                            The component to be rendered.
 */
function Icon( { d, otherPathProps, ...otherProps } ) {
	return (
		<SVG height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg" { ...otherProps }>
			<Path fillRule="evenodd" clipRule="evenodd" d={ d } { ...otherPathProps } />
		</SVG>
	);
}

Icon.propTypes = {
	d: PropTypes.string,
	otherPathProps: PropTypes.object,
};

Icon.defaultProps = {
	d: undefined,
	otherPathProps: undefined,
};

export default Icon;
