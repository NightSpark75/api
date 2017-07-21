/** 
 * Pointlog.js
 */
import React from 'react';
import { Link } from 'react-router';
import axios from 'axios';
import { Button, Panel, FormControl, Alert } from "react-bootstrap";
import Catchlog from './catchlog/Catchlog';

export default class Pointlog extends React.Component{
    constructor(props) {
        super(props);

        this.state = {
            point: [],
            point_no: '',
            scan: 'disabled',
            scan_message: '',
            point_info: [],
            mouse_show: false,
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
                    scan: 'disabled',
                    scan_message: '資料驗證中...'
                });
                this.pointCheck(point_no, device_type);
                break;
            }
        }
    }

    pointCheck(point_no, device_type) {
        let self = this;       
        axios.get('/api/web/mpz/pointlog/check/' + point_no)
        .then(function (response) {
            if (response.data.result) {
                let ldate = response.data.ldate;
                self.setState({scan_message: ''});
                self.setComponent(point_no, ldate, device_type);
                console.log(response.data);
            } else {
                self.setState({
                    scan: '',
                    scan_message: response.data.msg,
                });
                console.log(response.data);
            }
        }).catch(function (error) {
            console.log(error);
        });
    }

    setComponent(point_no, ldate, device_type) {
        switch (device_type) {
            case '1':
            case '2':
            case '3':
            case '4':
                this.setState({mouse_show: true});
                this.refs.mouse.init(point_no, ldate, device_type);
                break;
        }
    }

    onCancel() {
        this.setState({
            mouse_show: false,
            point_no: '',
            scan: '',
            point_info: [],
        });
    }

    componentMsg(msg) {
        this.setState({scan_message: msg});
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
                                <strong>
                                    <h4>
                                        {this.state.scan_message}
                                    </h4>
                                </strong>
                            :
                                null
                            }
                        </div>
                    </div>  
                </Panel> 
                {this.state.mouse_show &&
                    <Panel>
                        <Catchlog
                            pointInfo={this.state.point_info}
                            onCancel={this.onCancel.bind(this)}
                            sendMsg={this.componentMsg.bind(this)}
                            ref="mouse"
                        >
                        </Catchlog>
                    </Panel>
                }
            </div>
        );
    }
}