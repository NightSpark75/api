import React from 'react'
import { Link } from 'react-router'
import axios from 'axios'

export default class Menu1 extends React.Component{
    constructor(props) {
        super(props)

        this.state = {
            menu: [
                {
                    name: 'list 1',
                    menu: [
                        {
                            name: 'list 1-1',
                            menu: null,
                        },
                    ],
                },
                {
                    name: 'list 2',
                    menu: [
                        {
                            name: 'list 2-1',
                            menu: null,
                        },
                        {
                            name: 'list 2-2',
                            menu: null,
                        },
                        {
                            name: 'list 2-3',
                            menu: null,
                        },
                    ],
                },
            ],
        }
    }

    componentDidMount() {

    }

    render() {
        const { menu } = this.state
        const v_menu = (menu) => {
            const uuid = () => {
                function s4() {
                    return Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
                }
                return s4() + s4() + '-' + s4() + '-' + s4() + '-' + s4() + '-' + s4() + s4() + s4();
            }
            return (
                menu.map((item, index) => (
                    <div className="v-menu-item" key={index}>
                        <a>
                            {item.name}
                            {item.menu && 
                                <span className="icon">
                                    <i className="fa fa-angle-right"></i>
                                </span>
                            }
                        </a>
                        {item.menu && 
                            <div className="v-menu">
                                <div className="v-menu-list">
                                    <div className="v-menu-content">
                                        {v_menu(item.menu)}
                                    </div>
                                </div>
                            </div>
                        }
                    </div>
                ))
            )
        }
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
                        <div className="v-menu">
                            <div className="v-menu-list">
                                <div className="v-menu-content"> 
                                    {v_menu(menu)}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="column">
                        {this.props.children}
                    </div>
                </div>
            </div>
        )
    }
}