/** 
 * FieldGroup.js
 */
import React from "react";
import { Link } from "react-router";
import { FormGroup, FormControl, ControlLabel, HelpBlock} from "react-bootstrap";

export default class FieldGroup extends React.Component{
    constructor(props) {
        super(props);
    }; 

    getProps(props) {
        let newProps = new Object();
        for (let key in props) {
            if (key !== "id" && key !== "help" && key !== "label") {
                newProps[key] = props[key];
            }
        }
        return newProps;
    }

    render() {
        const props = this.getProps(this.props);
        const id = this.props.id;
        const label = this.props.label;
        const help = this.props.help;
        return (
            <FormGroup controlId={id}>
                <ControlLabel>{label}</ControlLabel>
                <FormControl {...props} />
                {help && <HelpBlock>{help}</HelpBlock>}
            </FormGroup>
        );
    };
}