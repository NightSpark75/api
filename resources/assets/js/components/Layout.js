import React from "react";
import { Link } from "react-router";

export default class Layout extends React.Component{
    render(){
        const { location } = this.props;
        const containerStyle = {
            marginTop: "60px"
        };
        return(
            <div>
                <span>Standart D2K API</span>
            </div>
        );
    }
}