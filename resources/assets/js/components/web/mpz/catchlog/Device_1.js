import React from "react";
import { Link } from "react-router";
import { Button, Modal, Form, FormGroup, FormControl, ControlLabel, Checkbox, Col } from "react-bootstrap";
import FieldGroup from  '../../../../components/includes/FieldGroup';
import AlertMsg from '../../../../components/includes/AlertMsg';
import axios from 'axios';

export default class Device_1 extends React.Component{
    constructor(props) {
        super(props);
        this.state = {

            catch_num3: 0,
            catch_num4: 0,
            catch_num5: 0,
            catch_num6: 0,
            change5: 'N',
            isLoading: false,
            msg_type: '',
            msg: ''
        };
    };

    setMsg(type = '', msg = '') {
        this.setState({
            msg_type: type,
            msg: msg,
        });
    }

    catchNum3Change(e) {
        this.setState({catch_num3: e.target.value})
    }

    catchNum4Change(e) {
        this.setState({catch_num4: e.target.value})
    }

    catchNum5Change(e) {
        this.setState({catch_num5: e.target.value})
    }

    catchNum6Change(e) {
        this.setState({catch_num6: e.target.value})
    }

    change5Change(e) {
        if (this.state.change5 === 'Y') {
            this.setState({change5: 'N'});
            return;
        }
        this.setState({change5: 'Y'});
    }

    onSave(e) {
        let self = this;
        this.setState({isLoading: true});
        const {catch_num3, catch_num4, catch_num5, catch_num6, change5} = this.state;
        const point_no = this.props.point_no;
        let form_data = new FormData();
        form_data.append('point_no', point_no);
        form_data.append('catch_num3', catch_num3);
        form_data.append('catch_num4', catch_num4);
        form_data.append('catch_num5', catch_num5);
        form_data.append('catch_num6', catch_num6);
        form_data.append('change5', change5);
        axios.post('/api/web/mpz/save', form_data, {
            method: 'post'
        }).then(function (response) {
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
    }

    onCancel() {
        this.setState({
            catch_num3: 0,
            catch_num4: 0,
            catch_num5: 0,
            catch_num6: 0,
            change5: 'N',
            isLoading: false,
            msg_type: '',
            msg: ''
        });
        this.props.onCancel();
    }

    render() {
        const isLoading = this.state.isLoading;
        return(
            <div>
                <Form horizontal>
                    <FormGroup controlId="catch_num3">
                        <Col componentClass={ControlLabel} sm={1}>
                            壁虎
                        </Col>
                        <Col sm={3}>
                            <FormControl 
                                type="number" 
                                value={this.state.catch_num3}
                                onChange={this.catchNum3Change.bind(this)}
                                required
                            />
                        </Col>
                    </FormGroup>
                    <FormGroup controlId="catch_num4">
                        <Col componentClass={ControlLabel} sm={1}>
                            昆蟲
                        </Col>
                        <Col sm={3}>
                            <FormControl 
                                type="number" 
                                value={this.state.catch_num4}
                                onChange={this.catchNum4Change.bind(this)}
                                required
                            />
                        </Col>
                    </FormGroup>
                    <FormGroup controlId="catch_num5">
                        <Col componentClass={ControlLabel} sm={1}>
                            鼠類
                        </Col>
                        <Col sm={3}>
                            <FormControl 
                                type="number" 
                                value={this.state.catch_num5}
                                onChange={this.catchNum5Change.bind(this)}
                                required
                            />
                        </Col>
                    </FormGroup>
                    <FormGroup controlId="catch_num6">
                        <Col componentClass={ControlLabel} sm={1}>
                            其它
                        </Col>
                        <Col sm={3}>
                            <FormControl 
                                type="number" 
                                value={this.state.catch_num6}
                                onChange={this.catchNum6Change.bind(this)}
                                required
                            />
                        </Col>
                    </FormGroup>
                    <FormGroup>
                        <Col smOffset={1} sm={10}>
                            <Checkbox
                                name="change5" 
                                inline 
                                value={this.state.change5}
                                checked={this.state.change5 === 'Y'}
                                onChange={this.change5Change.bind(this)}
                            >
                                更換黏鼠板
                            </Checkbox>
                        </Col>
                    </FormGroup>
                    <AlertMsg 
                        type={this.state.msg_type} 
                        msg={this.state.msg}
                    />
                    <FormGroup>
                        <Col smOffset={1} sm={2}>
                            <Button onClick={this.onCancel.bind(this)}>取消</Button>
                        </Col>
                        <Col sm={2}>
                            <Button 
                                type="submit"
                                bsStyle="primary" 
                                disabled={isLoading}
                                onClick={!isLoading ? this.onSave.bind(this) : null}
                            >
                                {isLoading ? '資料儲存中...' : '儲存'}
                            </Button>
                        </Col>
                    </FormGroup>
                </Form>
            </div>
        );
    };
}