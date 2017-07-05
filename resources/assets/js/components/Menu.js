/** 
 * Menu.js
 */
import React from 'react';
import { Link } from 'react-router';
import axios from 'axios';

export default class Menu extends React.Component{
    constructor(props) {
        super(props);

        this.state = {
            list: [],
            collapsed: true
        }
    }

    componentDidMount() {
        let self = this;
        axios.get('/api/pad/menu', new FormData(), {
            method: 'get',
        }).then(function (response) {
            console.log(response);
            if (response.data.result) {
                self.setState({list: response.data.menu});
            } else {
                window.location = '/pad/login/ppm';
            }
        }).catch(function (error) {
            console.log(error);
        });
    }
    toggleCollapse() {
        const collapsed =!this.state.collapsed;
        this.setState({collapsed});
    }
    render() {
        const buttonStyle = {
            margin: '0px 0px 20px 0px',
        }
        const buttonClass = "col-xs-12 col-sm-6 col-md-4 col-lg-3";
        return(   
            <div>
                <div className="row">
                    {this.state.list.length === 0 ? 
                        <div className={buttonClass} style={buttonStyle}>
                            <h3>功能清單建立中...</h3> 
                        </div>
                    : null}
                    {this.state.list.map((item, index) => (
                        <div className={buttonClass} style={buttonStyle} key={item['prg_id']}>
                            <button type="button" className="btn btn-primary btn-lg btn-block">
                                {item['prg_name']}
                            </button>
                        </div>
                    ))}
                </div>    
            </div>
        );
    }
}