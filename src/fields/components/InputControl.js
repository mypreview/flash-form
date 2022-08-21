/**
 * External dependencies
 */
import PropTypes from 'prop-types';

/**
 * A form component that determines which form element or
 * corresponding React input control component should be rendered.
 *
 * @param 	  {Object}  	          props               		Component properties.
 * @param 	  {string|JSX.Element}    props.Component 	   		HTML tag name (in string) or a React component.
 * @param 	  {string}  	          props.wrapperClassName    CSS class name generated for the block.
 * @return    {JSX.Element}                        		        Label element to render.
 */
function InputControl( { Component, wrapperClassName, ...otherProps } ) {
	return (
		<div className={ wrapperClassName }>
			<Component { ...otherProps } />
		</div>
	);
}

InputControl.propTypes = {
	Component: PropTypes.oneOfType( [ PropTypes.string, PropTypes.element ] ),
	wrapperClassName: PropTypes.string,
};

InputControl.defaultProps = {
	Component: 'input',
	wrapperClassName: undefined,
};

export default InputControl;
