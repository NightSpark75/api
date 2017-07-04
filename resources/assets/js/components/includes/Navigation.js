import React from "react";
import { IndexLink, Link } from "react-router";
export default class Navigation extends React.Component {
    constructor() {
        super();
        this.state = {
            collapsed: true,
            userName: '',
        };
    }

    toggleCollapse(){
        const collapsed =!this.state.collapsed;
        this.setState({collapsed});
    }

    render() {
        const { location } = this.props;
        const {collapsed } = this.state;
        const homeClass = location.pathname === '/' ? "active" : "";
        const usersClass = location.pathname.match(/^\/users/) ? "active" : "";
        const articlesClass = location.pathname.match(/^\/articles/) ? "active" : "";
        const navClass = collapsed ? "collapse" : "";
    return(
        <div className="navbar navbar-default navbar-fixed-top">
            <div className="container">
                <div className="navbar-header">
                <span className="navbar-brand">user name</span>
                <button className="navbar-toggle" type="button" onClick={this.toggleCollapse.bind(this)}>
                    <span className="sr-only">Toggle Navigation</span>
                    <span className="icon-bar"></span>
                    <span className="icon-bar"></span>
                    <span className="icon-bar"></span>
                </button>
                </div>
            </div>
        </div>
        );
    }
}