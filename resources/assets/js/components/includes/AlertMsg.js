import React from "react";
import { Link } from "react-router";

export default class AlertMsg extends React.Component{
    render(){
        let type_class = {
            success: 'alert alert-success',
            danger: 'alert alert-danger',
            primary: 'alert alert-primary',
            default: 'alert alert-default',
            warning: 'alert alert-warning',
        }    
        return(
            <div>
                {this.props.msg !== '' ?
                    <div className={type_class[this.props.type]}>
                        {this.props.msg}
                    </div>
                : null
                }
            </div>
        );
    }
}