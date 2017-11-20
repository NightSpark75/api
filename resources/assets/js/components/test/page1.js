import React from 'react'

export default class page1 extends React.Component{
    constructor(props) {
        super(props)
        this.state = {}
    }
    componentDidMount() {}
    render() {
        return(   
            <div className="dropdown is-active">
                <div className="dropdown-menu" role="menu">
                    <div className="dropdown-content">
                        <a href="#" className="dropdown-item">
                            Dropdown item
                        </a>
                        <a className="dropdown-item">
                            Other dropdown item
                        </a>
                        <a href="#" className="dropdown-item is-active">
                            Active dropdown item
                        </a>
                        <a href="#" className="dropdown-item">
                            Other dropdown item
                        </a>
                        <hr className="dropdown-divider"/>
                        <a href="#" className="dropdown-item">
                            With a divider
                        </a>
                    </div>
                </div>
            </div>
        )
    }
}