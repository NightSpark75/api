import React from "react";
import { Link } from "react-router";
import axios from 'axios';
import Navigation from "../components/includes/Navigation";

export default class NavPage extends React.Component{
    constructor(props) {
        super(props);

        this.state = {
            user: []
        }
    }
    componentDidMount() {
        let self = this;
        axios.get('/api/pad/user', new FormData(), {
            method: 'get',
        }).then(function (response) {
            console.log(response);
            self.setState({user: response.data});
        }).catch(function (error) {
            console.log(error);
        });
    }
    render(){
        const containerStyle = {
            marginTop: "73px"
        };
        return(
            <div>
                <nav className="navbar navbar-inverse navbar-fixed-top" role="navigation">
                    <div className="container">
                        <div className="navbar-header">
                            <a href="#" className="navbar-brand">
                                {this.state.user.length === 0 ? '資料讀取中...': this.state.user['name'] + ' 您好'}
                            </a>
                        </div>
                    </div>
                </nav>
                <div className="container" role="main" style={containerStyle}>
                    {this.props.children}
                </div>
            </div>
        );
    }
}