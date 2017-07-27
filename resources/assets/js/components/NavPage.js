/** 
 * NavPage.js 
 */
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
        axios.get('/api/web/user/info', new FormData(), {
            method: 'get',
        }).then(function (response) {
            if (response.data.session) {
                self.setState({user: response.data.info});
            } else {
                console.log('miss session, need login!');
                window.location = '/web/login/ppm';
            }
        }).catch(function (error) {
            console.log(error);
        });
    }

    onLogout(event) {
        if(confirm('您確定要登出系統？')) {
            window.location = '/api/web/logout';
        }
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
                            <span className="navbar-brand">
                                {this.state.user.length === 0 ? '資料讀取中...': this.state.user['user_name'] + ' 您好'}
                            </span>
                        </div>
                        <div className="navbar-collapse collapse">
                            <ul className="nav navbar-nav navbar-right">
                                <li>
                                    <a href="/auth/web/menu">
                                        回功能頁
                                    </a>
                                </li>
                                <li>
                                    <a onClick={this.onLogout.bind(this)} href="#">
                                        登出
                                    </a>
                                </li>
                            </ul>
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