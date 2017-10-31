/** 
 * Login.js
 */
import React from "react";
import axios from 'axios';

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
        axios.post('/api/web/nativeLogin', form_data, {
            method: 'post',
        }).then(function (response) {
            if (response.data.result === true) {
                console.log(response.data);
                //window.location = '/auth/web/menu';
            } else {
                console.log(response.data);
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
            <div className="columns">
                <div className="column is-half is-offset-one-quarter">
                    <form onSubmit={this.onLogin.bind(this)}>
                        <div className="field">
                            <label className="label is-medium">請輸入帳號密碼登入</label>
                            <div className="control has-icons-left">
                                <input className="input is-medium" type="text"
                                    placeholder="請輸入帳號"
                                    maxLength="20"
                                    onChange={this.onAccountChange.bind(this)}
                                />
                                <span className="icon is-small is-left">
                                    <i className="fa fa-user"></i>
                                </span>
                            </div>
                        </div>
                        <div className="field">
                            <div className="control has-icons-left">
                                <input className="input is-medium" type="password"
                                    placeholder="請輸入密碼"
                                    maxLength="30"
                                    onChange={this.onPasswordChange.bind(this)}
                                />
                                <span className="icon is-small is-left">
                                    <i className="fa fa-lock"></i>
                                </span>
                            </div>
                        </div>
                        {this.state.buttonState === 'submit' ?
                            <button className="button is-loading is-primary is-medium is-fullwidth"></button>
                        :
                            <button type="submit" className="button is-primary is-medium is-fullwidth">登入</button>
                        }
                    </form>
                    {this.state.msg !== '' &&
                        <div className="notification is-warning" style={{marginTop: '10px'}}>
                            {this.state.msg}
                        </div>
                    }
                </div>
            </div>    
        );
    }
}