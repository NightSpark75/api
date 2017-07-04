/** 
 * Menu.js
 */
import React from "react";
import { Link } from "react-router";
import axios from 'axios';


export default class Menu extends React.Component{
    constructor(props) {
        super(props);

        this.state = {

        }
    }

    componentDidMount() {
        this.setState({

        });
    }

    onLogin(event) {
        /*
        const {account, password, system} = this.state;
        let self = this;
        if (account === '') {
            this.setMsg('warning', '請輸入帳號');
            return;
        } else if (password === '') {
            this.setMsg('warning', '請輸入密碼');
            return;
        } else {
            this.setMsg('', '');
        }
        this.setState({buttonState: 'submit'});
        let form_data = new FormData();
        form_data.append('account', account);
        form_data.append('password', password);
        form_data.append('system', system);
        axios.post('/api/pad/login', form_data, {
            method: 'post',
        }).then(function (response) {
            console.log(response);
            // response.data.result
        }).catch(function (error) {
            console.log(error);
        });
        this.setState({buttonState: ''});
        */
    }
    render() {
        const buttonStyle = {
            margin: '10px 0px 10px 0px',
        }
        const buttonClass = "col-xs-12 col-sm-6 col-md-4 col-lg-3";
        return(   
            <div className="row">
                <div className={buttonClass} style={buttonStyle}>
                    <button type="button" className="btn btn-primary btn-lg btn-block">new button</button>
                </div>
                <div className={buttonClass} style={buttonStyle}>
                    <button type="button" className="btn btn-primary btn-lg btn-block">new button</button>
                </div>
                <div className={buttonClass} style={buttonStyle}>
                    <button type="button" className="btn btn-primary btn-lg btn-block">new button</button>
                </div>
                <div className={buttonClass} style={buttonStyle}>
                    <button type="button" className="btn btn-primary btn-lg btn-block">new button</button>
                </div>
                <div className={buttonClass} style={buttonStyle}>
                    <button type="button" className="btn btn-primary btn-lg btn-block">new button</button>
                </div>
                <div className={buttonClass} style={buttonStyle}>
                    <button type="button" className="btn btn-primary btn-lg btn-block">new button</button>
                </div>
            </div>    
            
        );
    }
}