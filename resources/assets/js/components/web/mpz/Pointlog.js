/** 
 * Pointlog.js
 */
import React from 'react';
import { Link } from 'react-router';
import axios from 'axios';
import { Button, Panel, FormControl, Alert } from "react-bootstrap";
import Catchlog from './catchlog/Catchlog';
import Device_1 from './catchlog/Device_1';
import Device_2 from './catchlog/Device_2';
import Device_3 from './catchlog/Device_3';
import Device_4 from './catchlog/Device_4';

export default class Pointlog extends React.Component{
    constructor(props) {
        super(props);

        this.state = {
            point: [],
            point_no: '',
            scan: 'disabled',
            scan_message: '',
            point_info: [],
            logComponent: null,
        }
    }

    init() {
        let self = this;       
        axios.get('/api/web/mpz/pointlog/init', null, {
            method: 'get',
        }).then(function (response) {
            if (response.data.result) {
                self.setState({
                    point: response.data.point,
                    scan: ''
                });
                console.log(response.data);
            } else {
                console.log(response.data);
                window.location = '/web/login/ppm';
            }
        }).catch(function (error) {
            console.log(error);
        });
    }

    componentDidMount() {
        this.init();
    }

    scanChange(e) {
        let point_no = e.target.value;
        this.setState({
            point_no: point_no,
        });
        this.pointSearch(point_no);
    }

    pointSearch(point_no) {
        let list  = this.state.point;
        for (var i = 0; i < list.length; i++) {
            let item = [];
            Object.keys(list[i]).map(function(e) {
                item[e] = list[i][e];
            });         
            if (item['point_no'] === point_no) {
                let device_type = list[i]['device_type'];
                this.setState({
                    point_info: list[i],
                    scan: 'disabled'
                });
                this.setComponent(device_type);
            }
        }
    }

    pointCheck(point_no) {
        let self = this;       
        axios.get('/api/web/mpz/pointlog/check', null, {
            method: 'get',
        }).then(function (response) {
            if (response.data.result) {
                self.setState({
                    point: response.data.point,
                    scan: ''
                });
                console.log(response.data);
            } else {
                console.log(response.data);
                window.location = '/web/login/ppm';
            }
        }).catch(function (error) {
            console.log(error);
        });
    }

    setComponent(device_type) {
        let component = '';
        switch (device_type) {
            case '1':
                component = this.device_1();
                break;
            case '2':
                component = this.device_2();
                break;
            case '3':
                component = this.device_3();
                break;
            case '4':
                component = this.device_4();
                break;
            default:
                component = null;
        }
        this.setState({logComponent: component});
    }

    onCancel() {
        this.setState({
            logComponent: null,
            point_no: '',
            scan: ''
        });
    }

    device_1() {
        return (
            <Panel>
                <Device_1
                    pointInfo={this.state.point_info}
                    onCancel={this.onCancel.bind(this)}
                >
                </Device_1>
            </Panel>
        );
    }

    device_2() {
        return (
            <Panel>
                <Device_1>
                </Device_1>
            </Panel>
        );
    }

    device_3() {
        return (
            <Panel>
                <Device_1>
                </Device_1>
            </Panel>
        );
    }

    device_4() {
        return (
            <Panel>
                <Device_1>
                </Device_1>
            </Panel>
        );
    }

    render() {
        return(   
            <div>
                <Panel>
                    <div className="row">
                        <div className="col-sm-4 col-md-3">
                            <FormControl
                                type="text"
                                className="input-lg"
                                value={this.state.point_no}
                                placeholder="掃描條碼"
                                disabled={this.state.scan}
                                onChange={this.scanChange.bind(this)}
                            />
                        </div>
                        <div className="col-sm-8 col-md-9">
                            {this.state.scan_message !== '' ? 
                                <Alert bsStyle="danger">
                                    {this.state.scan_message}
                                </Alert>
                            :
                                null
                            }
                        </div>
                    </div>  
                </Panel> 
                {this.state.logComponent}
            </div>
        );
    }
}