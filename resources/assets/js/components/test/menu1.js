import React from 'react'
import { Link } from 'react-router'
import axios from 'axios'

export default class Menu1 extends React.Component{
    constructor(props) {
        super(props)

        this.state = {

        }
    }

    componentDidMount() {

    }

    render() {

        return(   
            <div>
                <nav className="navbar is-fixed-top">
                        <div className="navbar-brand">
                            <div className="navbar-item" style={{height: 52}}>
                                <label className="label is-medium has-text-white-ter">
                                    {'xxx您好'}
                                </label>
                            </div>
                            <div className="navbar-burger burger" data-target="navMenu" >
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                        </div>
                        <div id="navMenu" className={this.state.nav + " navbar-menu"}>
                            <div className="navbar-end">
                                <Link className="navbar-item" to="/auth/web/menu">
                                    <span className="is-size-4">回功能頁</span>
                                </Link>
                                <a className="navbar-item" href="#">
                                    <span className="is-size-4">登出</span>
                                </a>
                            </div>
                        </div>
                </nav>
                <div className="columns warpper">
                    <div className="column siderbar menu-color">
                        <aside className="menu">
                            <p className="menu-label">
                                General
                            </p>
                            <ul className="menu-list">
                                <li><a>Dashboard</a></li>
                                <li><a>Customers</a></li>
                                <li><Link to="/test/menu1/page1">{'page1'}</Link></li>
                                <li><Link to="/test/menu1/page2">{'page2'}</Link></li>
                                <li><Link to="/test/menu1/page3">{'page3'}</Link></li>
                            </ul>
                            <p className="menu-label">
                                Administration
                            </p>
                            <ul className="menu-list">
                                <li><a>Team Settings</a></li>
                                <li>
                                    <a className="is-active">Manage Your Team</a>
                                    <ul>
                                        <li><a>Members</a></li>
                                        <li><a>Plugins</a></li>
                                        <li><a>Add a member</a></li>
                                    </ul>
                                </li>
                                <li><a>Invitations</a></li>
                                <li><a>Cloud Storage Environment Settings</a></li>
                                <li><a>Authentication</a></li>
                            </ul>
                            <p className="menu-label">
                                Transactions
                            </p>
                            <ul className="menu-list">
                                <li><a>Payments</a></li>
                                <li><a>Transfers</a></li>
                                <li><a>Balance</a></li>
                            </ul>
                        </aside>
                    </div>
                    <div className="column">
                        {this.props.children}
                    </div>
                </div>
            </div>
        )
    }
}