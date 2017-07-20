/** 
 * UserAdd.js
 */
import React from "react";
import { Link } from "react-router";
import { Button, Modal, Form, FormGroup, FormControl, ControlLabel, Radio } from "react-bootstrap";
import axios from 'axios';
import FieldGroup from  '../../../components/includes/FieldGroup';
import AlertMsg from '../../../components/includes/AlertMsg';

export default class UserAdd extends React.Component{
    constructor(props) {
        super(props);
        this.state = {
            user_id: '',
            user_name: '',
            user_pw: '',
            conf_pw: '',
            pw_ctrl: 'Y',
            s_class: 'Y',
            state: 'Y',
            rmk: '',
            pw_ctrl_y: 'Y',
            pw_ctrl_n: 'N',
            class_y: 'Y',
            class_n: 'N',
            state_y: 'Y',
            state_n: 'N',
            isLoading: false,
            msg_type: '',
            msg: ''
        };

        this.addList = this.props.addList.bind(this);
        this.onHide = this.props.onHide.bind(this);
    };

    initState() {
        this.setState({
            user_id: '',
            user_name: '',
            user_pw: '',
            conf_pw: '',
            pw_ctrl: 'Y',
            s_class: 'Y',
            state: 'Y',
            rmk: '',
            pw_ctrl_y: 'Y',
            pw_ctrl_n: 'N',
            class_y: 'Y',
            class_n: 'N',
            state_y: 'Y',
            state_n: 'N',
            isLoading: false,
            msg_type: '',
            msg: ''
        });
    }

    setMsg(type = '', msg = '') {
        this.setState({
            msg_type: type,
            msg: msg,
        });
    }

    userIdChange(e) {
        this.setState({user_id: e.target.value});
    }

    userNameChange(e) {
        this.setState({user_name: e.target.value});
    }

    userPwChange(e) {
        this.setState({user_pw: e.target.value});
    }

    confPwChange(e) {
        this.setState({conf_pw: e.target.value});
    }

    pwCtrlChange(e) {
        this.setState({pw_ctrl: e.target.value});
    }

    classChange(e) {
        this.setState({s_class: e.target.value});
    }

    stateChange(e) {
        this.setState({state: e.target.value});
    }

    rmkChange(e) {
        this.setState({rmk: e.target.value});
    }

    formValidate(obj) {
        const {user_id, user_name, user_pw, conf_pw, pw_ctrl, s_class, state, rmk} = obj;
        let msg = [];
        (user_id === '') ? msg.push('請輸入帳號') : null;
        (user_name === '') ? msg.push('請輸入姓名') : null;
        (user_pw === '') ? msg.push('請輸入密碼') : null;
        (conf_pw === '') ? msg.push('請輸入確認密碼') : null;
        (pw_ctrl === '') ? msg.push('請選擇是否密碼永久有效') : null;
        (s_class === '') ? msg.push('請選擇是否使用User Menu') : null;
        (state === '') ? msg.push('請選擇使用狀態') : null;
        (user_pw !== conf_pw) ? msg.push('密碼與確認密碼不同') : null;
        let newText = msg.map((i, index) => {
            return <div key={index}>{i}<br /></div>
        });
        return newText;
    }

    onInsert() {
        this.setState({isLoading: true});
        const {user_id, user_name, user_pw, conf_pw, pw_ctrl, s_class, state, rmk} = this.state;
        let self = this;
        let errorMsg = this.formValidate(this.state);
        if (errorMsg.length > 0) {
            this.setMsg('danger', errorMsg);
            this.setState({isLoading: false});
            return;
        }
        let form_data = new FormData();
        form_data.append('user_id', user_id);
        form_data.append('user_name', user_name);
        form_data.append('user_pw', user_pw);
        form_data.append('pw_ctrl', pw_ctrl);
        form_data.append('class', s_class);
        form_data.append('state', state);
        form_data.append('rmk', rmk);
        axios.post('/api/web/user/insert', form_data)
        .then(function (response) {
            if (response.data.result) {
                self.setMsg('success', response.data.msg);
                self.setState({buttonState: 'complete'});
                self.initState();
                self.addList(response.data.user);
                self.onHide();
            } else {
                self.setMsg('danger', response.data.msg);
                self.setState({buttonState: 'default'});
            }
            self.setState({isLoading: false});
        }).catch(function (error) {
            console.log(error);
            self.setMsg('danger', error);
            self.setState({isLoading: false});
        });
    };

    render() {
        const radioSyle = {marginRight: '10px'};
        let isLoading = this.state.isLoading;
        return(
            <Modal show={this.props.showModal} onHide={this.props.onHide} backdrop="static">
                <Modal.Header closeButton>
                    <Modal.Title>新增使用者</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    <Form>
                        <FieldGroup
                            id="user_id"
                            type="text"
                            label="使用者帳號"
                            placeholder="請輸入使用者帳號"
                            value={this.state.user_id}
                            onChange={this.userIdChange.bind(this)}
                            required
                        />
                        <FieldGroup
                            id="user_name"
                            type="text"
                            label="使用者姓名"
                            placeholder="請輸入使用者姓名"
                            value={this.state.user_name}
                            onChange={this.userNameChange.bind(this)}
                        />
                        <FieldGroup
                            id="user_pw"
                            type="password"
                            label="密碼"
                            placeholder="請輸入密碼"
                            value={this.state.user_pw}
                            onChange={this.userPwChange.bind(this)}
                        />
                        <FieldGroup
                            id="conf_pw"
                            type="password"
                            label="確認密碼"
                            placeholder="請輸入確認密碼"
                            value={this.state.conf_pw}
                            onChange={this.confPwChange.bind(this)}
                        />
                        <FormGroup>
                            <ControlLabel style={radioSyle}>密碼永久有效</ControlLabel>
                            <Radio 
                                name="pw_ctrl" 
                                inline 
                                style={radioSyle}
                                value={this.state.pw_ctrl_y}
                                checked={this.state.pw_ctrl === this.state.pw_ctrl_y}
                                onChange={this.pwCtrlChange.bind(this)}
                            >
                                {this.state.pw_ctrl_y}
                            </Radio>
                            <Radio 
                                name="pw_ctrl" 
                                inline
                                value={this.state.pw_ctrl_n}
                                checked={this.state.pw_ctrl === this.state.pw_ctrl_n}
                                onChange={this.pwCtrlChange.bind(this)}
                            >
                                {this.state.pw_ctrl_n}
                            </Radio>
                        </FormGroup>
                        <FormGroup>
                            <ControlLabel style={radioSyle}>使用User Menu</ControlLabel>
                            <Radio 
                                name="class" 
                                inline 
                                style={radioSyle}
                                value={this.state.class_y}
                                checked={this.state.s_class === this.state.class_y}
                                onChange={this.classChange.bind(this)}
                            >
                                {this.state.class_y}
                            </Radio>
                            <Radio 
                                name="class" 
                                inline
                                value={this.state.class_n}
                                checked={this.state.s_class === this.state.class_n}
                                onChange={this.classChange.bind(this)}
                            >
                                {this.state.class_n}
                            </Radio>
                        </FormGroup>
                        <FormGroup>
                            <ControlLabel style={radioSyle}>使用狀態</ControlLabel>
                            <Radio 
                                name="state" 
                                inline 
                                style={radioSyle}
                                value={this.state.state_y}
                                checked={this.state.state === this.state.state_y}
                                onChange={this.stateChange.bind(this)}
                            >
                                {this.state.state_y}
                            </Radio>
                            <Radio 
                                name="state" 
                                inline
                                value={this.state.state_n}
                                checked={this.state.state === this.state.state_n}
                                onChange={this.stateChange.bind(this)}
                            >
                                {this.state.state_n}
                            </Radio>
                        </FormGroup>
                        <FormGroup controlId="rmk">
                            <ControlLabel>備註</ControlLabel>
                            <FormControl 
                                componentClass="textarea" 
                                placeholder="請輸入備註" 
                                value={this.state.rmk}
                                onChange={this.rmkChange.bind(this)}
                            />
                        </FormGroup>
                    </Form>
                    <AlertMsg 
                        type={this.state.msg_type} 
                        msg={this.state.msg}
                    />
                </Modal.Body>
                <Modal.Footer>
                    <Button onClick={this.props.onHide}>關閉</Button>
                    <Button 
                        bsStyle="primary" 
                        disabled={isLoading}
                        onClick={!isLoading ? this.onInsert.bind(this) : null}
                    >
                        {isLoading ? '新增資料中...' : '新增'}
                    </Button>
                </Modal.Footer>
        </Modal>
        );
    };
}