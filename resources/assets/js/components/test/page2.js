import React from 'react'

export default class page2 extends React.Component{
    constructor(props) {
        super(props)
        this.state = {}
    }
    componentDidMount() {}
    render() {
        return(   
            <div className="smenu">
                <div className="smenu-list" role="smenu">
                    <div className="smenu-list-content">
                        <a href="#" className="smenu-list-item" aria-haspopup="true" aria-controls="smenu1">
                            Dropdown item
                            <span className="icon is-small">
                                <i className="fa fa-angle-right" aria-hidden="true"></i>
                            </span>
                        </a>
                        <div className="smenu" id="smenu1">
                            <div className="smenu-list" role="smenu">
                                <div className="smenu-list-content">
                                    <a href="#" className="smenu-list-item">
                                        Dropdown item
                                        <span className="icon is-small">
                                            <i className="fa fa-angle-right" aria-hidden="true"></i>
                                        </span>
                                    </a>
                                    <a className="smenu-list-item">
                                        Other dropdown item
                                    </a>
                                    <a href="#" className="smenu-list-item is-active">
                                        Active dropdown item
                                    </a>
                                    <a href="#" className="smenu-list-item">
                                        Other dropdown item
                                    </a>
                                    <hr className="smenu-list-divider"/>
                                    <a href="#" className="smenu-list-item">
                                        With a divider
                                    </a>
                                </div>
                            </div>
                        </div>
                        <a className="smenu-list-item">
                            Other dropdown item
                        </a>
                        <a href="#" className="smenu-list-item is-active">
                            Active dropdown item
                        </a>
                        <a href="#" className="smenu-list-item">
                            Other dropdown item
                        </a>
                        <hr className="smenu-list-divider"/>
                        <a href="#" className="smenu-list-item">
                            With a divider
                        </a>
                    </div>
                </div>
            </div>
        )
    }
}