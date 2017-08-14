/** 
 * Pointlog.js
 */
import React from 'react';
import { Link } from 'react-router';
import axios from 'axios';
import { Button, Panel, FormControl, Alert, Col, ButtonToolbar } from "react-bootstrap";
import Catchlog from './Catchlog';
import Templog from './Templog';

export default class Pointlog extends React.Component{
    constructor(props) {
        super(props);

        this.state = {
            point: [],
            point_no: '',
            scan: 'disabled',
            scan_message: '',
            point_info: [],
            catchlog_show: false,
            templog_show: false,
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
                this.setComponent(list[i])
                break;
            }
        }
    }

    setComponent(point) {
        let point_type = point.point_type;
        switch (point_type) {
            case 'C':   // 鼠蟲防治紀錄
                this.setState({catchlog_show: true, scan_message: ''});
                break;
            case 'T':   // 溫溼紀錄
                this.setState({templog_show: true, scan_message: ''});
                break;
            case 'W':   // 最濕點紀錄
                this.setState({catchlog_show: true, scan_message: ''});
                break;
            case 'R':   // 冷藏櫃操作紀錄
                this.setState({catchlog_show: true, scan_message: ''});
                break;
            case 'P':   // 壓差紀錄
                this.setState({catchlog_show: true, scan_message: ''});
                break;
        }
    }

    onCancel() {
        this.setState({
            catchlog_show: false,
            templog_show: false,
            point_no: '',
            scan: '',
            point_info: [],
        });
    }

    componentMsg(msg) {
        this.setState({scan_message: msg});
    }

    goMenu() {
        window.location = '/auth/web/menu';
    }

    render() {
        return(   
            <div>
                <Panel style={{marginBottom: '10px'}}> 
                    <Col sm={10} md={10}>
                        <ButtonToolbar >
                            <Link className="btn btn-default" to="/auth/web/menu">&larr; 功能選單</Link> 
                        </ButtonToolbar>
                    </Col>
                </Panel> 
                <Panel style={{marginBottom: '10px'}}>
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
                            {this.state.scan_message !== '' && 
                                <strong>
                                    <h4>
                                        {this.state.scan_message}
                                    </h4>
                                </strong>
                            }
                        </div>
                    </div>  
                </Panel> 
                {this.state.catchlog_show &&
                    <Panel>
                        <Catchlog
                            pointInfo={this.state.point_info}
                            onCancel={this.onCancel.bind(this)}
                            sendMsg={this.componentMsg.bind(this)}
                        >
                        </Catchlog>
                    </Panel>
                }
                {this.state.templog_show &&
                    <Panel>
                        <Templog
                            pointInfo={this.state.point_info}
                            onCancel={this.onCancel.bind(this)}
                            sendMsg={this.componentMsg.bind(this)}
                        ></Templog>
                    </Panel>
                }
            </div>
        );
    }
}