/** 
 * Templog.js
 */
import React from "react";
import { Link } from "react-router";
import axios from 'axios';

export default class Templog extends React.Component{
    constructor(props) {
        super(props);
        this.state = {
            point_no: '', ldate: '', zone: '', mach_no: '', ch_date: '', temp_high: '', temp_low: '', humi_high: '', humi_low: '', 
            mo_temp: '', mo_hum: '', mo_time: '', mo_err: '', mo_type: '', mo_rmk: '', 
            af_temp: '', af_hum: '', af_time: '', af_err: '', af_type: '', af_rmk: '', 
            ev_temp: '', ev_hum: '', ev_time: '', ev_err: '', ev_type: '', ev_rmk: '', 
            mo: true, af: true, ev: true,
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
                <div className="column">
                    <h4 className="title is-4">溫濕度記錄表</h4>
                </div>
                <div className="column">
                    <table className="table is-bordered" style={{marginBottom: '0px'}}>
                        <tbody>
                            <tr>
                                <td>名稱</td><td>{point_name}</td><td>儀器編號</td><td>{mach_no}</td><td>儀器校期</td><td>{ch_date}</td>
                            </tr>
                            <tr>
                                <td>區域</td><td>{zone}</td>
                                <td>溫度範圍</td><td>{temp_low + " ~ " + temp_high}</td>
                                <td>濕度範圍</td><td>{humi_low + " ~ " + humi_high}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div className="column">
                    <table className="table is-bordered" style={{marginBottom: '0px'}}>
                        <tbody>
                            <tr>
                                <td colSpan={4}>
                                    <label className="label">上午記錄</label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label className="label">溫度℃</label>
                                    <input className="input" type="number" maxLength={10}
                                        disabled={mo}
                                        value={this.state.mo_temp || ''}
                                        onChange={this.mo_tempChange.bind(this)}
                                    />
                                </td>
                                <td>
                                    <label className="label">相對濕度 % R.H</label>
                                    <input className="input" type="number" maxLength={10}
                                        disabled={mo}
                                        value={this.state.mo_hum || ''}
                                        onChange={this.mo_humChange.bind(this)}
                                    />
                                </td>
                                <td>
                                    <label className="label">異常代碼</label>
                                    <div className="select">
                                        <select
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
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <label className="label">備註</label>
                                    <input className="input" type="text" maxLength={50}
                                        disabled={mo}
                                        value={this.state.mo_rmk || ''}
                                        onChange={this.mo_rmkChange.bind(this)}
                                    />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div className="column">
                    <table className="table is-bordered" style={{marginBottom: '0px'}}>
                        <tbody>
                            <tr>
                                <td colSpan={4}>
                                    <label className="label">下午記錄(1)</label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label className="label">溫度℃</label>
                                    <input className="input" type="number" maxLength={10}
                                        disabled={af}
                                        value={this.state.af_temp || ''}
                                        onChange={this.af_tempChange.bind(this)}
                                    />
                                </td>
                                <td>
                                    <label className="label">相對濕度 % R.H</label>
                                    <input className="input" type="number" maxLength={10}
                                        disabled={af}
                                        value={this.state.af_hum || ''}
                                        onChange={this.af_humChange.bind(this)}
                                    />
                                </td>
                                <td>
                                    <label className="label">異常代碼</label>
                                    <div className="select">
                                        <select
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
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <label className="label">備註</label>
                                    <input className="input" type="text" maxLength={50}
                                        disabled={af}
                                        value={this.state.af_rmk || ''}
                                        onChange={this.af_rmkChange.bind(this)}
                                    />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div className="column">
                    <table className="table is-bordered" style={{marginBottom: '0px'}}>
                        <tbody>
                            <tr>
                                <td colSpan={4}>
                                    <label className="label">下午記錄(1)</label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label className="label">溫度℃</label>
                                    <input className="input" type="number" maxLength={10}
                                        disabled={ev}
                                        value={this.state.ev_temp || ''}
                                        onChange={this.ev_tempChange.bind(this)}
                                    />
                                </td>
                                <td>
                                    <label className="label">相對濕度 % R.H</label>
                                    <input className="input" type="number" maxLength={10}
                                        disabled={ev}
                                        value={this.state.ev_hum || ''}
                                        onChange={this.ev_humChange.bind(this)}
                                    />
                                </td>
                                <td>
                                    <label className="label">異常代碼</label>
                                    <div className="select">
                                        <select
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
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <label className="label">備註</label>
                                    <input className="input" type="text" maxLength={50}
                                        disabled={ev}
                                        value={this.state.ev_rmk || ''}
                                        onChange={this.ev_rmkChange.bind(this)}
                                    />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div className="column">
                    <div className="field is-grouped">
                        <p className="control">
                            {(mo && af && ev) ? 
                                <button className="button is-primary is-static">已記錄完畢</button>
                            : isLoading ?
                                <button className="button is-loading is-primary"></button>
                            :
                                <button type="button" className="button is-primary" onClick={this.onSave.bind(this)}>儲存</button>
                            }
                        </p>
                        <p>
                            <button className="button" onClick={this.onCancel.bind(this)}>取消</button>
                        </p>
                    </div>
                </div>
            </div>
        );
    };
}

