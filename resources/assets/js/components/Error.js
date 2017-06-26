import React from "react";
import { Link } from "react-router";

export default class Error extends React.Component{
    render(){
        return(   
            <div className="row">
                <div className="col-md-6 col-md-offset-3">
                    <div className="panel panel-warning">
                        <div className="panel-heading"><h4 className="panel-title">error page</h4></div>
                        <div className="panel-body">
                            <h4>{this.props.params.msg}</h4>
                        </div>
                    </div>
                </div>
            </div>    
        );
    }
}