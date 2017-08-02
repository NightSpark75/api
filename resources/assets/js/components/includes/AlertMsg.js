/** 
 * AlertMsg.js
 */
import React from "react";
import { Alert } from 'react-bootstrap';

export default class AlertMsg extends React.Component{
    render() {
        return (
            <div>
                {(this.props.msg !== '' && this.props.type !== '') ?
                <Alert bsStyle={this.props.type}>
                    <strong>{this.props.msg}</strong>
                </Alert>
                : null}
            </div>
        );
    }
}