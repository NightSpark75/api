/** 
 * Templog.js
 */
import React from "react";
import { Link } from "react-router";
import { Button, Col, Panel, FormControl } from "react-bootstrap";
import FieldGroup from  '../../../../components/includes/FieldGroup';
import axios from 'axios';

export default class Templog extends React.Component{
    constructor(props) {
        super(props);
        this.state = {
            point_no: '', ldate: '', zone: '', mach_no: '', ch_date: '', temp_high: '', temp_low: '', humi_high: '', humi_low: '', 
            mo_temp: '', mo_hum: '', mo_time: '', mo_err: '', mo_type: '', mo_rmk: '', 
            af_temp: '', af_hum: '', af_time: '', af_err: '', af_type: '', af_rmk: '', 
            ev_temp: '', ev_hum: '', ev_time: '', ev_err: '', ev_type: '', ev_rmk: '', 
            mo: false, af: false, ev: false,
            log_data: {},
            init: false,
        };
        this.sendMsg = this.props.sendMsg.bind(this);
    };

    componentDidMount() {
        this.init();
    }

    init() {
        let self = this;
        let point_info = this.props.pointInfo;
        this.setState({init: true});
        axios.get('/api/web/mpz/pointlog/temp/init/' + point_info.point_no)
        .then(function (response) {
            if (response.data.result) {
                self.setState({
                    point_no: point_info.point_no,
                    ldate: response.data.ldate,
                    point_name: point_info.point_name,
                    zone: point_info.zone,
                    mach_no: point_info.mach_no,
                    ch_date: point_info.ch_date,
                    log_data: response.data.log_data,
                    temp_high: response.data.temp_high,
                    temp_low: response.data.temp_low,
                    humi_high: response.data.humi_high,
                    humi_low: response.data.humi_low,
                    init: false,
                });
                console.log(response.data);
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

    setValue(data) {
        if (data !== null) {
            let mo = data.mo_time !== null ? true : false;
            let af = data.af_time !== null ? true : false;
            let ev = data.ev_time !== null ? true : false;
            this.setState({
                mo_temp: data.mo_temp, mo_hum: data.mo_hum, mo_time: data.mo_time, 
                mo_err: data.mo_err, mo_type: data.mo_type, mo_rmk: data.mo_rmk, 
                af_temp: data.af_temp, af_hum: data.af_hum, af_time: data.af_time, 
                af_err: data.af_err, af_type: data.af_type, af_rmk: data.af_rmk, 
                ev_temp: data.ev_temp, ev_hum: data.ev_hum, ev_time: data.ev_time, 
                ev_err: data.ev_err, ev_type: data.ev_type, ev_rmk: data.ev_rmk, 
                mo: mo, af: af, ev: ev,
            });
        }
    }
    
    onSave(e) {
        let self = this;
        this.setState({isLoading: true});
        const {
            point_no, ldate, 
            mo_temp, mo_hum, mo_err, mo_time, mo_type, mo_rmk, 
            af_temp, af_hum, af_err, af_time, af_type, af_rmk,
            ev_temp, ev_hum, ev_err, ev_time, ev_type, ev_rmk,
        } = this.state;
        let form_data = new FormData();
        form_data.append('point_no', point_no);
        form_data.append('ldate', ldate);
        form_data.append('mo_temp', mo_temp || '');
        form_data.append('mo_hum', mo_hum || '');
        form_data.append('mo_err', mo_err || '');
        form_data.append('mo_time', mo_time || '');
        form_data.append('mo_type', mo_type || '');
        form_data.append('mo_rmk', mo_rmk || '');
        form_data.append('af_temp', af_temp || '');
        form_data.append('af_hum', af_hum || '');
        form_data.append('af_err', af_err || '');
        form_data.append('af_time', af_time || '');
        form_data.append('af_type', af_type || '');
        form_data.append('af_rmk', af_rmk || '');
        form_data.append('ev_temp', ev_temp || '');
        form_data.append('ev_hum', ev_hum || '');
        form_data.append('ev_err', ev_err || '');
        form_data.append('ev_time', ev_time || '');
        form_data.append('ev_type', ev_type || '');
        form_data.append('ev_rmk', ev_rmk || '');
        axios.post('/api/web/mpz/pointlog/temp/save', form_data)
        .then(function (response) {
            if (response.data.result) {
                self.sendMsg(point_no + '檢查點記錄成功!');
                self.setState({isLoading: false});
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

    mo_tempChange(e) {
        this.setState({mo_temp: e.target.value});
    }
    mo_humChange(e) {
        this.setState({mo_hum: e.target.value});
    }
    mo_errChange(e) {
        this.setState({mo_err: e.target.value});
    }
    mo_rmkChange(e) {
        this.setState({mo_rmk: e.target.value});
    }
    af_tempChange(e) {
        this.setState({af_temp: e.target.value});
    }
    af_humChange(e) {
        this.setState({af_hum: e.target.value});
    }
    af_errChange(e) {
        this.setState({af_err: e.target.value});
    }
    af_rmkChange(e) {
        this.setState({af_rmk: e.target.value});
    }
    ev_tempChange(e) {
        this.setState({ev_temp: e.target.value});
    }
    ev_humChange(e) {
        this.setState({ev_hum: e.target.value});
    }
    ev_errChange(e) {
        this.setState({ev_err: e.target.value});
    }
    ev_rmkChange(e) {
        this.setState({ev_rmk: e.target.value});
    }

    onCancel() {
        this.props.onCancel();
    }

    render() {
        const { init, isLoading, point_name, zone, mach_no, ch_date, temp_high, temp_low, humi_high, humi_low } = this.state;
        const { mo, af, ev } = this.state;
        return(
            <div>
                <table className="table table-bordered" style={{marginBottom: '10px'}}>
                    <tbody>
                        <tr>
                            <td>名稱</td><td>{point_name}</td><td>儀器編號</td><td>{mach_no}</td><td>校正日期</td><td>{ch_date}</td>
                        </tr>
                        <tr>
                            <td>區域</td><td>{zone}</td>
                            <td>溫度範圍</td><td>{temp_low + " ~ " + temp_high}</td>
                            <td>濕度範圍</td><td>{humi_low + " ~ " + humi_high}</td>
                        </tr>
                    </tbody>
                </table>
                <div className="row">
                    <div className="col-md-12 col-sm-12">
                        <Panel header="上午記錄" bsStyle="info" style={{marginBottom: '10px'}}>
                            <table>
                                <tbody>
                                    <tr>
                                        <td className="col-md-2 col-sm-2">
                                            <label htmlFor="mo_temp">溫度℃</label>
                                            <input type="text" className="form-control" id="mo_temp" maxLength={10}
                                                disabled={mo}
                                                value={this.state.mo_temp || ''}
                                                onChange={this.mo_tempChange.bind(this)}
                                            />
                                        </td>
                                        <td className="col-md-2 col-sm-3">
                                            <label htmlFor="mo_hum">相對濕度 % R.H</label>
                                            <input type="text" className="form-control" id="mo_hum" maxLength={10}
                                                disabled={mo}
                                                value={this.state.mo_hum || ''}
                                                onChange={this.mo_humChange.bind(this)}
                                            />
                                        </td>
                                        <td className="col-md-3 col-sm-3">
                                            <label htmlFor="mo_err">異常代碼</label>
                                            <FormControl 
                                                componentClass="select" 
                                                placeholder="請選擇"
                                                disabled={mo}
                                                onChange={this.mo_errChange.bind(this)}
                                                value={this.state.mo_err || ''}
                                            >
                                                <option value=""></option>
                                                <option value="參加集會">參加集會</option>
                                                <option value="溫濕度異常">溫濕度異常</option>
                                                <option value="儀器異常">儀器異常</option>
                                                <option value="更換儀器">更換儀器</option>
                                                <option value="更換表單">更換表單</option>
                                                <option value="其它">其它</option>
                                            </FormControl>
                                        </td>
                                        <td className="col-md-5 col-sm-4">
                                            <label htmlFor="mo_rmk">備註</label>
                                            <input type="text" className="form-control" id="mo_rmk" maxLength={50}
                                                disabled={mo}
                                                value={this.state.mo_rmk || ''}
                                                onChange={this.mo_rmkChange.bind(this)}
                                            />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </Panel>
                    </div>
                </div>
                <div className="row">
                    <div className="col-md-12 col-sm-12">
                        <Panel header="下午記錄(1)" bsStyle="info" style={{marginBottom: '10px'}}>
                            <table>
                                <tbody>
                                    <tr>
                                        <td className="col-md-2 col-sm-2">
                                            <label htmlFor="af_temp">溫度℃</label>
                                            <input type="text" className="form-control" id="af_temp" maxLength={10}
                                                disabled={af}
                                                value={this.state.af_temp || ''}
                                                onChange={this.af_tempChange.bind(this)}
                                            />
                                        </td>
                                        <td className="col-md-2 col-sm-3">
                                            <label htmlFor="af_hum">相對濕度 % R.H</label>
                                            <input type="text" className="form-control" id="af_hum" maxLength={10}
                                                disabled={af}
                                                value={this.state.af_hum || ''}
                                                onChange={this.af_humChange.bind(this)}
                                            />
                                        </td>
                                        <td className="col-md-3 col-sm-3">
                                            <label htmlFor="af_err">異常代碼</label>
                                            <FormControl 
                                                componentClass="select" 
                                                placeholder="請選擇"
                                                disabled={af}
                                                onChange={this.af_errChange.bind(this)}
                                                value={this.state.af_err || ''}
                                            >
                                                <option value=""></option>
                                                <option value="參加集會">參加集會</option>
                                                <option value="溫濕度異常">溫濕度異常</option>
                                                <option value="儀器異常">儀器異常</option>
                                                <option value="更換儀器">更換儀器</option>
                                                <option value="更換表單">更換表單</option>
                                                <option value="其它">其它</option>
                                            </FormControl>
                                        </td>
                                        <td className="col-md-5 col-sm-4">
                                            <label htmlFor="af_rmk">備註</label>
                                            <input type="text" className="form-control" id="af_rmk" maxLength={50}
                                                disabled={af}
                                                value={this.state.af_rmk || ''}
                                                onChange={this.af_rmkChange.bind(this)}
                                            />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </Panel>
                    </div>
                </div>
                <div className="row">
                    <div className="col-md-12 col-sm-12">
                        <Panel header="下午記錄(2)" bsStyle="info" style={{marginBottom: '10px'}}>
                            <table>
                                <tbody>
                                    <tr>
                                        <td className="col-md-2 col-sm-2">
                                            <label htmlFor="ev_temp">溫度℃</label>
                                            <input type="text" className="form-control" id="ev_temp" maxLength={10}
                                                disabled={ev}
                                                value={this.state.ev_temp || ''}
                                                onChange={this.ev_tempChange.bind(this)}
                                            />
                                        </td>
                                        <td className="col-md-2 col-sm-3">
                                            <label htmlFor="ev_hum">相對濕度 % R.H</label>
                                            <input type="text" className="form-control" id="ev_hum" maxLength={10}
                                                disabled={ev}
                                                value={this.state.ev_hum || ''}
                                                onChange={this.ev_humChange.bind(this)}
                                            />
                                        </td>
                                        <td className="col-md-3 col-sm-3">
                                            <label htmlFor="ev_err">異常代碼</label>
                                            <FormControl 
                                                componentClass="select" 
                                                placeholder="請選擇"
                                                disabled={ev}
                                                onChange={this.ev_errChange.bind(this)}
                                                value={this.state.ev_err || ''}
                                            >
                                                <option value=""></option>
                                                <option value="參加集會">參加集會</option>
                                                <option value="溫濕度異常">溫濕度異常</option>
                                                <option value="儀器異常">儀器異常</option>
                                                <option value="更換儀器">更換儀器</option>
                                                <option value="更換表單">更換表單</option>
                                                <option value="其它">其它</option>
                                            </FormControl>
                                        </td>
                                        <td className="col-md-5 col-sm-4">
                                            <label htmlFor="ev_rmk">備註</label>
                                            <input type="text" className="form-control" id="ev_rmk" maxLength={50}
                                                disabled={ev}
                                                value={this.state.ev_rmk || ''}
                                                onChange={this.ev_rmkChange.bind(this)}
                                            />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </Panel>
                    </div>
                </div>
                <div className="row">
                    <div className="col-md-12 col-sm-12">
                        <Col smOffset={1} sm={2}>
                            <Button onClick={this.onCancel.bind(this)}>取消</Button>
                        </Col>
                        <Col sm={2}>
                            {(mo && af && ev) ? 
                                <Button disabled={true} bsStyle="primary">已記錄完畢</Button>
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
                    </div>
                </div>
            </div>
        );
    };
}

