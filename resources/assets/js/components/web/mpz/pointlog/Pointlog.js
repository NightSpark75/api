/** 
 * Pointlog.js
 */
import React from 'react';
import { Link } from 'react-router';
import axios from 'axios';
import Catchlog from './Catchlog';
import Templog from './Templog';
import Wetestlog from './Wetestlog';
import Refrilog from './Refrilog';
import Pressurelog from './Pressurelog';

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
            wetestlog_show: false,
            refrilog_show: false,
            pressurelog_show: false,
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
                this.setState({wetestlog_show: true, scan_message: ''});
                break;
            case 'R':   // 冷藏櫃操作紀錄
                this.setState({refrilog_show: true, scan_message: ''});
                break;
            case 'P':   // 壓差紀錄
                this.setState({pressurelog_show: true, scan_message: ''});
                break;
        }
    }

    onCancel() {
        this.setState({
            catchlog_show: false,
            templog_show: false,
            wetestlog_show: false,
            refrilog_show: false,
            pressurelog_show: false,
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
                <div className="box" style={{ marginTop: '10px', marginBottom: '10px' }}>
                    <p className="control">
                        <Link className="button" to="/auth/web/menu">&larr; 功能選單</Link> 
                    </p>
                </div>
                <div className="box" style={{ marginBottom: '10px' }}>
                    <div className="field is-horizontal">
                        <div className="field-body">
                            <div className="field is-grouped">
                                <div className="field" style={{marginRight: '10px'}}>
                                    <input type="password" className="input is-large" placeholder="掃描條碼"
                                        value={this.state.point_no}
                                        disabled={this.state.scan}
                                        onChange={this.scanChange.bind(this)}
                                    />
                                </div>
                                {this.state.scan_message !== '' &&
                                    <div className="notification is-warning" style={{padding: '1rem 1rem 1rem 1rem'}}>
                                        {this.state.scan_message}
                                    </div>
                                } 
                            </div>
                        </div>
                    </div>
                </div>
                {this.state.catchlog_show &&
                    <div className="box">
                        <Catchlog
                            pointInfo={this.state.point_info}
                            onCancel={this.onCancel.bind(this)}
                            sendMsg={this.componentMsg.bind(this)}
                        >
                        </Catchlog>
                        </div>
                }
                {this.state.templog_show &&
                    <div className="box">
                        <Templog
                            pointInfo={this.state.point_info}
                            onCancel={this.onCancel.bind(this)}
                            sendMsg={this.componentMsg.bind(this)}
                        >
                        </Templog>
                        </div>
                }
                {this.state.wetestlog_show &&
                    <div className="box">
                        <Wetestlog
                            pointInfo={this.state.point_info}
                            onCancel={this.onCancel.bind(this)}
                            sendMsg={this.componentMsg.bind(this)}
                        >
                        </Wetestlog>
                    </div>
                }
                {this.state.refrilog_show &&
                    <div className="box">
                        <Refrilog
                            pointInfo={this.state.point_info}
                            onCancel={this.onCancel.bind(this)}
                            sendMsg={this.componentMsg.bind(this)}
                        >
                        </Refrilog>
                    </div>
                }   
                {this.state.pressurelog_show &&
                    <div className="box">
                        <Pressurelog
                            pointInfo={this.state.point_info}
                            onCancel={this.onCancel.bind(this)}
                            sendMsg={this.componentMsg.bind(this)}
                        >
                        </Pressurelog>
                    </div>
                }   
            </div>
        );
    }
}