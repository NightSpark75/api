/** 
 * Catchlog.js
 */
import React from "react";
import { Link } from "react-router";
import { Button, Modal, Form, FormGroup, FormControl, ControlLabel, Checkbox, Col, HelpBlock } from "react-bootstrap";
import FieldGroup from  '../../../../components/includes/FieldGroup';
import axios from 'axios';

export default class Catchlog extends React.Component{
    constructor(props) {
        super(props);
        this.state = {
            log_data:{},
            point_no: '', ldate: 0, device_type: '',
            catch_num1: 0, catch_num2: 0, catch_num3: 0, catch_num4: 0, catch_num5: 0, catch_num6: 0,
            change1: 'N', change2: 'N', change3: 'N', change4: 'N', change5: 'N', change6: 'N', lamp: 'N',
            rmk: '', discription: '',
            changeDate: [],
            vn1: false, vn2: false, vn3: false, vn4: false, vn5: false, vn6: false, 
            vc1: false, vc2: false, vc3: false, vc4: false, vc5: false, vc6: false,  
            vlp: false,
            thisMonth: 0, lastMonth: 0,
            isLoading: false, init: false,
            msg_type: '', msg: '',
        };
        this.sendMsg = this.props.sendMsg.bind(this);
    };

    componentDidMount() {
        let point = this.props.pointInfo;
        this.init(point.point_no, point.device_type);
    }

    init(point_no, device_type) {
        let self = this;
        this.setState({init: true});
        axios.get('/api/web/mpz/pointlog/catch/init/' + point_no)
        .then(function (response) {
            if (response.data.result) {
                self.setState({
                    log_data: response.data.log_data,
                    point_no: point_no,
                    ldate: response.data.ldate,
                    device_type: device_type,
                    thisMonth: response.data.thisMonth,
                    lastMonth: response.data.lastMonth,
                    changeDate: response.data.changeDate,
                    init: false,
                });
                self.setLayout();
                self.setValue(response.data.log_data);
            } else {
                self.props.sendMsg(response.data.msg);
                self.onCancel();
            }
        }).catch(function (error) {
            console.log(error);
            self.props.sendMsg(error);
        });
    }

    setLayout() {
        let device = this.state.device_type;
        switch(device) {
            case '1':
                this.setState({
                    vn3: true, vn4: true, vn5: true, vn6: true,
                    vc5: true
                });
                break;
            case '2':
                this.setState({
                    vn1: true, vn2: true,
                    vc1: true, vc2: true, vc4: true, 
                });
                break;
            case '3':
                this.setState({
                    vn3: true, vn4: true, vn5: true, vn6: true,
                    vc5: true, vc6: true, 
                });
                break;
            case '4':
                this.setState({
                    vn1: true, vn2: true, vlp: true,
                    vc1: true, vc2: true, vc3: true, vc4: true,
                });
                break;
        }
    }

    setValue(data) {
        if (data !== null) {
            let rmk = (data.rmk === null) ? '' : data.rmk;
            this.setState({
                point_no: data.point_no, ldate: data.ldate, 
                catch_num1: data.catch_num1, catch_num2: data.catch_num2, catch_num3: data.catch_num3, 
                catch_num4: data.catch_num4, catch_num5: data.catch_num5, catch_num6: data.catch_num6,
                change1: data.change1, change2: data.change2, change3: data.change3, 
                change4: data.change4, change5: data.change5, change6: data.change6, lamp: data.lamp,
                rmk: rmk, discription: data.discription,
            });
        }
    }

    initState() {
        this.setState({
            point_no: '', ldate: 0, device_type: '',
            catch_num1: 0, catch_num2: 0, catch_num3: 0, catch_num4: 0, catch_num5: 0, catch_num6: 0,
            change1: 'N', change2: 'N', change3: 'N', change4: 'N', change5: 'N', change6: 'N', lamp: 'N',
            rmk: '', discription: '',
            changeDate: [],
            vn1: false, vn2: false, vn3: false, vn4: false, vn5: false, vn6: false, 
            vc1: false, vc2: false, vc3: false, vc4: false, vc5: false, vc6: false,  
            vlp: false,
            thisMonth: 0, lastMonth: 0,
            isLoading: false, init: false,
            msg_type: '', msg: '',
        });
    }

    catchChange(item, e) {
        switch(item) {
            case '1':
                this.setState({catch_num1: e.target.value});
                break;
            case '2':
                this.setState({catch_num2: e.target.value});
                break;
            case '3':
                this.setState({catch_num3: e.target.value});
                break;
            case '4':
                this.setState({catch_num4: e.target.value});
                break;
            case '5':
                this.setState({catch_num5: e.target.value});
                break;
            case '6':
                this.setState({catch_num6: e.target.value});
                break;
            case 'rmk':
                this.setState({rmk: e.target.value});
                break;
            case 'dis':
                this.setState({discription: e.target.value});
                break;
        }
    }
    
    checkboxChange(item, e) {
        let state, value;
        switch(item) {
            case '1':
                state = this.state.change1;
                value = state === 'Y' ? 'N' : 'Y';
                this.setState({change1: value});
                break;
            case '2':
                state = this.state.change2;
                value = state === 'Y' ? 'N' : 'Y';
                this.setState({change2: value});
                break;
            case '3':
                state = this.state.change3;
                value = state === 'Y' ? 'N' : 'Y';
                this.setState({change3: value});
                break;
            case '4':
                state = this.state.change4;
                value = state === 'Y' ? 'N' : 'Y';
                this.setState({change4: value});
                break;
            case '5':
                state = this.state.change5;
                value = state === 'Y' ? 'N' : 'Y';
                this.setState({change5: value});
                break;
            case '6':
                state = this.state.change6;
                value = state === 'Y' ? 'N' : 'Y';
                this.setState({change6: value});
                break;
            case 'lamp':
                state = this.state.lamp;
                value = state === 'Y' ? 'N' : 'Y';
                this.setState({lamp: value});
                break;
        }
    }

    onSave(e) {
        let self = this;
        this.setState({isLoading: true});
        const {
            point_no, ldate, catch_num1, catch_num2, catch_num3, catch_num4, catch_num5, catch_num6, 
            change1, change2, change3, change4, change5, change6, lamp, rmk, discription} = this.state;
        let form_data = new FormData();
        form_data.append('point_no', point_no);
        form_data.append('ldate', ldate);
        form_data.append('catch_num1', catch_num1);
        form_data.append('catch_num2', catch_num2);
        form_data.append('catch_num3', catch_num3);
        form_data.append('catch_num4', catch_num4);
        form_data.append('catch_num5', catch_num5);
        form_data.append('catch_num6', catch_num6);
        form_data.append('change1', change1);
        form_data.append('change2', change2);
        form_data.append('change3', change3);
        form_data.append('change4', change4);
        form_data.append('change5', change5);
        form_data.append('change6', change6);
        form_data.append('check_lamp', lamp);
        form_data.append('rmk', rmk);
        form_data.append('discription', discription);
        axios.post('/api/web/mpz/pointlog/catch/save', form_data)
        .then(function (response) {
            if (response.data.result) {
                self.sendMsg(point_no + '檢查點記錄成功!');
                self.setState({isLoading: false});
                self.initState();
                self.onCancel();
            } else {
                self.sendMsg(response.data.msg);
                self.setState({isLoading: false});
            }
        }).catch(function (error) {
            console.log(error);
            self.sendMsg(error);
            self.setState({isLoading: false});
        });
    }

    onCancel() {
        this.initState();
        this.props.onCancel();
    }

    render() {
        const isLoading = this.state.isLoading;
        const init = this.state.init;
        const comp = (this.state.log_data === null) ? false : true;
        const CatchInput = function (props) {
            return(
                <FormGroup controlId={props.name}>
                    <Col componentClass={ControlLabel} sm={1}>
                        {props.label}
                    </Col>
                    <Col sm={3}>
                        <FormControl 
                            type="number" 
                            value={props.value}
                            onChange={props.onChange}
                            required
                        />
                    </Col>
                </FormGroup>
            );
        }
        const ChangeDevice = function (props) {
            return(
                <FormGroup>
                    <Col smOffset={1} sm={11}>
                        <Checkbox
                            name={props.name} 
                            value={props.value}
                            checked={props.checked}
                            onChange={props.onChange}
                        >
                            <strong>
                                {props.label}
                            </strong>
                            {props.date && 
                                <span style={{marginLeft: '20px'}}>最後更換日期：{props.date}</span>
                            }
                        </Checkbox>
                    </Col>
                </FormGroup>
            );
        }
        return(
            <div>
                <Col smOffset={1}>
                    <h4><strong>本月累計：</strong>{this.state.thisMonth}</h4>
                    <h4><strong>上月統計：</strong>{this.state.lastMonth}</h4>
                </Col>
                <Form horizontal>
                    {this.state.vn1 &&
                        <CatchInput
                            name="catch_num1"
                            value={this.state.catch_num1}
                            onChange={this.catchChange.bind(this, '1')}
                            label="黏附"
                        >
                        </CatchInput>
                    }
                    {this.state.vn2 &&
                        <CatchInput
                            name="catch_num2"
                            value={this.state.catch_num2}
                            onChange={this.catchChange.bind(this, '2')}
                            label="承接"
                        >
                        </CatchInput>
                    }
                    {this.state.vlp && 
                        <ChangeDevice
                            name="lamp"
                            value={this.state.lamp}
                            checked={this.state.lamp === 'Y'}
                            onChange={this.checkboxChange.bind(this, 'lamp')}
                            label="驅蚊燈檢查"
                        >
                        </ChangeDevice>
                    }
                    {this.state.vn3 &&
                        <CatchInput
                            name="catch_num3"
                            value={this.state.catch_num3}
                            onChange={this.catchChange.bind(this, '3')}
                            label="壁虎"
                        >
                        </CatchInput>
                    }
                    {this.state.vn4 &&
                        <CatchInput
                            name="catch_num4"
                            value={this.state.catch_num4}
                            onChange={this.catchChange.bind(this, '4')}
                            label="昆蟲"
                        >
                        </CatchInput>
                    }
                    {this.state.vn5 &&
                        <CatchInput
                            name="catch_num5"
                            value={this.state.catch_num5}
                            onChange={this.catchChange.bind(this, '5')}
                            label="鼠類"
                        >
                        </CatchInput>
                    }
                    {this.state.vn6 &&
                        <CatchInput
                            name="catch_num6"
                            value={this.state.catch_num6}
                            onChange={this.catchChange.bind(this, '6')}
                            label="其他"
                        >
                        </CatchInput>
                    }
                    {this.state.vc1 && 
                        <ChangeDevice
                            name="change1"
                            value={this.state.change1}
                            checked={this.state.change1 === 'Y'}
                            onChange={this.checkboxChange.bind(this, '1')}
                            label="更換捕蚊紙"
                            date={this.state.changeDate['change1']}
                        >
                        </ChangeDevice>
                    }
                    {this.state.vc2 && 
                        <ChangeDevice
                            name="change2"
                            value={this.state.change2}
                            checked={this.state.change2 === 'Y'}
                            onChange={this.checkboxChange.bind(this, '2')}
                            label="更換承接膠帶"
                            date={this.state.changeDate['change2']}
                        >
                        </ChangeDevice>
                    }
                    {this.state.vc3 && 
                        <ChangeDevice
                            name="change3"
                            value={this.state.change3}
                            checked={this.state.change3 === 'Y'}
                            onChange={this.checkboxChange.bind(this, '3')}
                            label="更換驅蚊燈管"
                            date={this.state.changeDate['change3']}
                        >
                        </ChangeDevice>
                    }
                    {this.state.vc4 && 
                        <ChangeDevice
                            name="change4"
                            value={this.state.change4}
                            checked={this.state.change4 === 'Y'}
                            onChange={this.checkboxChange.bind(this, '4')}
                            label="更換捕蚊燈管"
                            date={this.state.changeDate['change4']}
                        >
                        </ChangeDevice>
                    }
                    {this.state.vc5 && 
                        <ChangeDevice
                            name="change5"
                            value={this.state.change5}
                            checked={this.state.change5 === 'Y'}
                            onChange={this.checkboxChange.bind(this, '5')}
                            label="更換黏鼠板"
                            date={this.state.changeDate['change5']}
                        >
                        </ChangeDevice>
                    }
                    {this.state.vc6 && 
                        <ChangeDevice
                            name="change6"
                            value={this.state.change6}
                            checked={this.state.change6 === 'Y'}
                            onChange={this.checkboxChange.bind(this, '6')}
                            label="更換防蟻措施"
                            date={this.state.changeDate['change6']}
                        >
                        </ChangeDevice>
                    }
                    <FormGroup controlId="rmk">
                        <Col componentClass={ControlLabel} sm={1}>
                            備註
                        </Col>
                        <Col sm={3}>
                            <FormControl 
                                componentClass="select" 
                                placeholder="請選擇"
                                onChange={this.catchChange.bind(this, 'rmk')}
                                value={this.state.rmk}
                            >
                                <option value=""></option>
                                <option value="a">數量超標</option>
                                <option value="b">器具異常</option>
                                <option value="c">新設點位</option>
                                <option value="d">其他</option>
                            </FormControl>
                        </Col>
                    </FormGroup>
                    <FormGroup controlId="discription">
                        <Col componentClass={ControlLabel} sm={1}>
                            其他
                        </Col>
                        <Col sm={6}>
                            <FormControl 
                                componentClass="textarea" 
                                placeholder="請輸入其它說明" 
                                value={this.state.discription}
                                onChange={this.catchChange.bind(this, 'dis')}
                            />
                        </Col>
                    </FormGroup>
                    <FormGroup>
                        <Col smOffset={1} sm={2}>
                            <Button onClick={this.onCancel.bind(this)}>取消</Button>
                        </Col>
                        <Col sm={2}>
                            {comp ? 
                                <Button bsStyle="primary" disabled={true}>今日已完成記錄</Button>
                            :
                                <Button 
                                    type="submit"
                                    bsStyle="primary" 
                                    disabled={isLoading || init}
                                    onClick={!isLoading ? this.onSave.bind(this) : null}
                                >
                                    {isLoading ? '資料儲存中...' : '儲存'}
                                </Button>
                            }
                            
                        </Col>
                    </FormGroup>
                </Form>
            </div>
        );
    };
}

