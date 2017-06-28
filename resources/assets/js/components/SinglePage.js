import React from "react";
import { Link } from "react-router";

export default class SinglePage extends React.Component{
    render(){
        const { location } = this.props;
        const containerStyle = {
            marginTop: "20px"
        };
        return(
            <div>
                <div className="container" style={containerStyle}>
                    <div className="row">
                        <div className="col-lg-12">
                            {this.props.children}
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}