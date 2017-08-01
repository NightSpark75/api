/** 
 * Login.js
 */
import React from "react";
import { Link } from "react-router";
import axios from 'axios';
import AlertMsg from '../../../components/includes/AlertMsg';

export default class Login extends React.Component{
    constructor(props) {
        super(props);

        this.state = {
            system: '',
            account: '',
            password: '',
            buttonState: '',
            msg: '',
            msg_type: '',
        }
    }

    setMsg(type = '', msg = '') {
        this.setState({
            msg_type: type,
            msg: msg,
        });
    }

    componentDidMount() {
        this.setState({
            system: this.props.params.system,
        });
    }

    onAccountChange(event) {
        event.preventDefault();
        this.setState({account: event.target.value})
    }

    onPasswordChange(event) {
        event.preventDefault();
        this.setState({password: event.target.value})
    }

    onLogin(event) {
        event.preventDefault();
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
        axios.post('/api/web/login', form_data, {
            method: 'post',
        }).then(function (response) {
            if (response.data.result === true) {
                window.location = '/auth/web/menu';
            } else {
                self.setMsg('danger', response.data.msg);
                self.setState({buttonState: ''});
            }
        }).catch(function (error) {
            console.log(error);
            self.setMsg('danger', error.message);
            self.setState({buttonState: ''});
        });
    }
    render() {
        return(   
            <div className="row">
                <div className="col-xs-12 col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4 col-lg-4 col-lg-offset-4">
                    <form role="form" onSubmit={this.onLogin.bind(this)}>
                        <h4>請輸入帳號密碼登入</h4>
                        <div className="form-group">
                            <input 
                                className="form-control"
                                type="text" 
                                id="account" 
                                name="account" 
                                placeholder="請輸入帳號"
                                maxLength="20"
                                onChange={this.onAccountChange.bind(this)}
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
                                onChange={this.onPasswordChange.bind(this)}
                            />
                        </div>
                        {this.state.buttonState === 'submit' ?
                            <button 
                                type="button" 
                                className="btn btn-primary btn-block disabled"  
                            >
                                資料驗證中......
                            </button>
                        :
                            <button 
                                type="submit" 
                                className="btn btn-primary btn-block"   
                            >
                                登入
                            </button>
                        }
                        <AlertMsg 
                            type={this.state.msg_type} 
                            msg={this.state.msg}
                        />
                    </form>
                </div>
            </div>    
        );
    }
}