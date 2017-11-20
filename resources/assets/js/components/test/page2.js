import React from 'react'

export default class page2 extends React.Component{
    constructor(props) {
        super(props)
        this.state = {
            menu: {
                0: {
                    name: 'list 1',
                    menu: {
                        0: {
                            name: 'list 1-1',
                            menu: null,
                        },
                    },
                },
                1: {
                    name: 'list 2',
                    menu: {
                        0: {
                            name: 'list 2-1',
                            menu: null,
                        },
                        1: {
                            name: 'list 2-2',
                            menu: null,
                        },
                        2: {
                            name: 'list 2-3',
                            menu: null,
                        },
                    },
                },
            },
        }
    }
    componentDidMount() {

    }
    render() {
        const { menu } = this.state
        const v_menu = (menu) => {
            return (
                <div>
                    {Object.keys(menu).map((key) => (
                        <div className="v-menu-item" id={key + menu[key].name}>
                            <a>
                                {menu[key].name}
                                {menu[key].menu && 
                                    <span className="icon">
                                        <i className="fa fa-angle-right"></i>
                                    </span>
                                }
                            </a>
                            {menu[key].menu && 
                                <div className="v-menu" role="menu">
                                    <div className="v-menu-list">
                                        <div className="v-menu-content">
                                            {v_menu(menu[key].menu)}
                                        </div>
                                    </div>
                                </div>
                            }
                        </div>
                    ))}
                </div>
            )
        }
        return( 
            <div className="v-menu" role="menu">
                <div className="v-menu-list">
                    <div className="v-menu-content"> 
                        {v_menu(menu)}
                    </div>
                </div>
            </div>
        )
    }
}