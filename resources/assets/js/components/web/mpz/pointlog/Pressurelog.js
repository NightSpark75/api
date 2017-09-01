/** 
 * Pressurelog.js
 */
import React from "react";
import { Link } from "react-router";
import axios from 'axios';

export default class Pressurelog extends React.Component{
    constructor(props) {
        super(props);
        this.state = {
            point_info: {}, point_no: '', ldate: '', 
            mo_pa: '', mo_aq: '', mo_err: '', mo_time: '', 
            af_pa: '', af_aq: '', af_err: '', af_time: '',  
            ev_pa: '', ev_aq: '', ev_err: '', ev_time: '',  
            rmk: '',
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
        axios.get('/api/web/mpz/pointlog/pressure/init/' + point_info.point_no)
        .then(function (response) {
            if (response.data.result) {
                self.setState({
                    point_info: point_info,
                    point_no: point_info.point_no,
                    ldate: response.data.ldate,
                    log_data: response.data.log_data,
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
                mo_pa: data.mo_pa, mo_aq: data.mo_aq, mo_err: data.mo_err, mo_time: data.mo_time,  
                af_pa: data.af_pa, af_aq: data.af_aq, af_err: data.af_err, af_time: data.af_time,  
                ev_pa: data.ev_pa, ev_aq: data.ev_aq, ev_err: data.ev_err, ev_time: data.ev_time,  
                rmk: data.rmk,
                mo: mo, af: af, ev: ev,
            });
        }
    }
    
    onSave(e) {
        let self = this;
        this.setState({isLoading: true});
        const {
            point_no, ldate, 
            mo_pa, mo_aq, mo_err, mo_time,  
            af_pa, af_aq, af_err, af_time,  
            ev_pa, ev_aq, ev_err, ev_time, 
            rmk
        } = this.state;
        let form_data = new FormData();
        form_data.append('point_no', point_no);
        form_data.append('ldate', ldate);
        form_data.append('mo_pa', mo_pa || '');
        form_data.append('mo_aq', mo_aq || '');
        form_data.append('mo_time', mo_time || '');
        form_data.append('mo_err', mo_err || '');
        form_data.append('af_pa', af_pa || '');
        form_data.append('af_aq', af_aq || '');
        form_data.append('af_time', af_time || '');
        form_data.append('af_err', af_err || '');
        form_data.append('ev_pa', ev_pa || '');
        form_data.append('ev_aq', ev_aq || '');
        form_data.append('ev_time', ev_time || '');
        form_data.append('ev_err', ev_err || '');
        form_data.append('rmk', rmk || '');
        axios.post('/api/web/mpz/pointlog/pressure/save', form_data)
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

    mo_aqChange(e) {
        this.setState({mo_aq: e.target.value});
    }
    mo_paChange(e) {
        this.setState({mo_pa: e.target.value});
    }
    mo_errChange(e) {
        this.setState({mo_err: e.target.value});
    }
    af_aqChange(e) {
        this.setState({af_aq: e.target.value});
    }
    af_paChange(e) {
        this.setState({af_pa: e.target.value});
    }
    af_errChange(e) {
        this.setState({af_err: e.target.value});
    }
    ev_aqChange(e) {
        this.setState({ev_aq: e.target.value});
    }
    ev_paChange(e) {
        this.setState({ev_pa: e.target.value});
    }
    ev_errChange(e) {
        this.setState({ev_err: e.target.value});
    }
    rmkChange(e) {
        this.setState({rmk: e.target.value});
    }

    onCancel() {
        this.props.onCancel();
    }

    render() {
        const { init, isLoading, point_info} = this.state;
        const { mo, af, ev } = this.state;
        return(
            <div>
                <div className="column">
                    <h4 className="title is-4">壓差記錄表</h4>
                </div>
                <div className="column">
                    <table className="table is-bordered" style={{marginBottom: '0px'}}>
                        <tbody>
                            <tr>
                                <td>名稱</td><td>{point_info.point_name}</td>
                                <td>儀器編號</td><td>{point_info.mach_no}</td>
                                <td>合格範圍Pa</td><td>{point_info.pa_range}</td>
                            </tr>
                            <tr>
                                <td colSpan={2}></td>
                                <td>儀器校期</td><td>{point_info.ch_date}</td>
                                <td>合格範圍mmAq</td><td>{point_info.aq_range}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div className="column">
                    <table className="table is-bordered" style={{marginBottom: '0px'}}>
                        <tbody>
                            <tr>
                                <td colSpan={4}>
                                    <label className="label">上午(1)壓差記錄</label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label className="label">壓差(Pa)</label>
                                    <input className="input" type="number" maxLength={10}
                                        disabled={mo}
                                        value={this.state.mo_pa || ''}
                                        onChange={this.mo_paChange.bind(this)}
                                    />
                                </td>
                                <td>
                                    <label className="label">壓差(mmAq)</label>
                                    <input className="input" type="number" maxLength={10}
                                        disabled={mo}
                                        value={this.state.mo_aq || ''}
                                        onChange={this.mo_aqChange.bind(this)}
                                    />
                                </td>
                                <td>
                                    <label className="label">備註</label>
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
                                            <option value="其它">其它</option>
                                        </select>
                                    </div>
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
                                    <label className="label">下午(1)壓差記錄</label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label className="label">壓差(Pa)</label>
                                    <input className="input" type="number" maxLength={10}
                                        disabled={af}
                                        value={this.state.af_pa || ''}
                                        onChange={this.af_paChange.bind(this)}
                                    />
                                </td>
                                <td>
                                    <label className="label">壓差(mmAq)</label>
                                    <input className="input" type="number" maxLength={10}
                                        disabled={af}
                                        value={this.state.af_aq || ''}
                                        onChange={this.af_aqChange.bind(this)}
                                    />
                                </td>
                                <td>
                                    <label className="label">備註</label>
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
                                            <option value="其它">其它</option>
                                        </select>
                                    </div>
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
                                    <label className="label">下午(2)壓差記錄</label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label className="label">壓差(Pa)</label>
                                    <input className="input" type="number" maxLength={10}
                                        disabled={ev}
                                        value={this.state.ev_pa || ''}
                                        onChange={this.ev_paChange.bind(this)}
                                    />
                                </td>
                                <td>
                                    <label className="label">壓差(mmAq)</label>
                                    <input className="input" type="number" maxLength={10}
                                        disabled={ev}
                                        value={this.state.ev_aq || ''}
                                        onChange={this.ev_aqChange.bind(this)}
                                    />
                                </td>
                                <td>
                                    <label className="label">備註</label>
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
                                            <option value="其它">其它</option>
                                        </select>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div className="column">
                    <label className="label">備註</label>
                    <input type="text" className="input" maxLength={50}
                        value={this.state.rmk || ''}
                        onChange={this.rmkChange.bind(this)}
                    />
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

