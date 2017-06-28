/** 
 * Login.js
 */
import React from "react";
import { Link } from "react-router";
import AlertMsg from '../components/includes/AlertMsg';

export default class Login extends React.Component{
    constructor(props) {
        super(props);

        this.state = {
            system: '',
            msg: '',
            msg_type: '',
        }
    }
    render(){
        return(   
            <div className="row">
                <div className="col-xs-12 col-sm-4 col-sm-offset-4 col-md-4 col-md-offset-4 col-lg-4 col-lg-offset-4">
                    <form role="form">
                        <h4>請輸入帳號密碼登入</h4>
                        <div className="form-group">
                            <input 
                                className="form-control"
                                type="text" 
                                id="account" 
                                name="account" 
                                placeholder="請輸入帳號"
                                maxLength="20"
                            />
                        </div>
                        <div className="form-group">
                            <input 
                                className="form-control"
                                type="password" 
                                id="password" 
                                name="password" 
                                placeholder="請輸入密碼"
                                maxLength="30"
                            />
                        </div>
                        <AlertMsg 
                            type={this.state.msg_type} 
                            msg={this.state.msg}
                        />
                        <button 
                            type="button" 
                            className="btn btn-primary btn-block"    
                        >
                            登入
                        </button>
                    </form>
                </div>
            </div>    
        );
    }
}