/** 
 * Refrilog.js
 */
import React from "react";
import { Link } from "react-router";
import axios from 'axios';

export default class Refrilog extends React.Component{
    constructor(props) {
        super(props);
        this.state = {
            point_info: {}, point_no: '', ldate: '', 
            mo_temp: '', mo_putt: '', mo_bell: '', mo_light: '', mo_time: '', mo_rmk: '',
            af_temp: '', af_putt: '', af_bell: '', af_light: '', af_time: '', af_rmk: '',
            rmk: '', error_item: '',
            mo: false, af: false,
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
        axios.get('/api/web/mpz/pointlog/refri/init/' + point_info.point_no)
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
            this.setState({
                mo_temp: data.mo_temp, mo_putt: data.mo_putt, mo_bell: data.mo_bell, 
                mo_light: data.mo_light, mo_time: data.mo_time, mo_rmk: data.mo_rmk,
                af_temp: data.af_temp, af_putt: data.af_putt, af_bell: data.af_bell, 
                af_light: data.af_light, af_time: data.af_time, af_rmk: data.af_rmk,
                rmk: data.rmk, error_item: data.error_item,
                mo: mo, af: af,
            });
        }
    }
    
    onSave(e) {
        let self = this;
        this.setState({isLoading: true});
        const {
            point_no, ldate, 
            mo_temp, mo_putt, mo_bell, mo_light, mo_time, mo_rmk,
            af_temp, af_putt, af_bell, af_light, af_time, af_rmk, rmk, error_item,
        } = this.state;
        let form_data = new FormData();
        form_data.append('point_no', point_no);
        form_data.append('ldate', ldate);
        form_data.append('mo_temp', mo_temp || '');
        form_data.append('mo_putt', mo_putt || '');
        form_data.append('mo_bell', mo_bell || '');
        form_data.append('mo_light', mo_light || '');
        form_data.append('mo_time', mo_time || '');
        form_data.append('mo_rmk', mo_rmk || '');
        form_data.append('af_temp', af_temp || '');
        form_data.append('af_putt', af_putt || '');
        form_data.append('af_bell', af_bell || '');
        form_data.append('af_light', af_light || '');
        form_data.append('af_time', af_time || '');
        form_data.append('af_rmk', af_rmk || '');
        form_data.append('error_item', error_item || '');
        form_data.append('rmk', rmk || '');
        axios.post('/api/web/mpz/pointlog/refri/save', form_data).then(function (response) {
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
    mo_puttChange(e) {
        this.setState({mo_putt: e.target.value});
    }
    mo_bellChange(e) {
        this.setState({mo_bell: e.target.value});
    }
    mo_lightChange(e) {
        this.setState({mo_light: e.target.value});
    }
    mo_rmkChange(e) {
        this.setState({mo_rmk: e.target.value});
    }
    af_tempChange(e) {
        this.setState({af_temp: e.target.value});
    }
    af_puttChange(e) {
        this.setState({af_putt: e.target.value});
    }
    af_bellChange(e) {
        this.setState({af_bell: e.target.value});
    }
    af_lightChange(e) {
        this.setState({af_light: e.target.value});
    }
    af_rmkChange(e) {
        this.setState({af_rmk: e.target.value});
    }
    error_itemChange(e) {
        this.setState({error_item: e.target.value});
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
                    <h4 className="title is-4">冷藏櫃操作記錄表</h4>
                </div>
                <div className="column">
                    <table className="table is-bordered" style={{marginBottom: '0px'}}>
                        <tbody>
                            <tr>
                                <td>名稱</td><td>{point_info.point_name}</td><td>儀器編號</td><td>{point_info.mach_no}</td>
                            </tr>
                            <tr>
                                <td>儀器校期</td><td>{point_info.ch_date}</td>
                                <td>合格範圍</td><td>溫度：{point_info.temp_range} ℃</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div className="column">
                    <table className="table is-bordered is-fullwidth" style={{marginBottom: '0px'}}>
                        <tbody>
                            <tr>
                                <td colSpan={4}>
                                    <label className="label is-size-4">上午記錄</label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span className="is-size-5">溫度：</span>
                                    <input className="input" type="number" maxLength={10} style={{width: '80px'}}
                                        disabled={mo}
                                        value={this.state.mo_temp || ''}
                                        onChange={this.mo_tempChange.bind(this)}
                                    />
                                    <span className="is-size-5">℃</span>
                                </td>
                                <td colSpan={2}>
                                <span className="is-size-5">備註：</span>
                                    <div className="select">
                                        <select
                                            placeholder="請選擇"
                                            disabled={mo}
                                            onChange={this.mo_rmkChange.bind(this)}
                                            value={this.state.mo_rmk || ''}
                                        >
                                            <option value=""></option>
                                            <option value="參加集會">參加集會</option>
                                            <option value="溫度異常">溫濕度異常</option>
                                            <option value="設備異常">儀器異常</option>
                                            <option value="其他">其他</option>
                                        </select>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label className="radiobox is-size-5">安全推桿&emsp;</label>
                                    <label className="radio">
                                        <input type="radio"
                                            name="mo_putt_y" 
                                            disabled={mo}
                                            value={'Y'}
                                            checked={this.state.mo_putt === 'Y'}
                                            onChange={this.mo_puttChange.bind(this)}
                                        />
                                        正常
                                    </label>
                                    <label className="radio">
                                        <input type="radio" 
                                            name="mo_putt_n" 
                                            disabled={mo}
                                            value={'N'}
                                            checked={this.state.mo_putt === 'N'}
                                            onChange={this.mo_puttChange.bind(this)}
                                        />
                                        異常
                                    </label>
                                </td>
                                <td>
                                    <label className="radiobox is-size-5">無線門鈴發報機&emsp;</label>
                                    <label className="radio">
                                        <input type="radio"
                                            name="mo_bell_y" 
                                            disabled={mo}
                                            value={'Y'}
                                            checked={this.state.mo_bell === 'Y'}
                                            onChange={this.mo_bellChange.bind(this)}
                                        />
                                        正常
                                    </label>
                                    <label className="radio">
                                        <input type="radio" 
                                            name="mo_bell_n" 
                                            disabled={mo}
                                            value={'N'}
                                            checked={this.state.mo_bell === 'N'}
                                            onChange={this.mo_bellChange.bind(this)}
                                        />
                                        異常
                                    </label>
                                </td>
                                <td>
                                    <label className="radiobox is-size-5">照明設備&emsp;</label>
                                    <label className="radio">
                                        <input type="radio"
                                            name="mo_light_y" 
                                            disabled={mo}
                                            value={'Y'}
                                            checked={this.state.mo_light === 'Y'}
                                            onChange={this.mo_lightChange.bind(this)}
                                        />
                                        正常
                                    </label>
                                    <label className="radio">
                                        <input type="radio" 
                                            name="mo_light_n" 
                                            disabled={mo}
                                            value={'N'}
                                            checked={this.state.mo_light === 'N'}
                                            onChange={this.mo_lightChange.bind(this)}
                                        />
                                        異常
                                    </label>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div className="column">
                    <table className="table is-bordered is-fullwidth" style={{marginBottom: '0px'}}>
                        <tbody>
                            <tr>
                                <td colSpan={4}>
                                    <label className="label is-size-4">下午記錄</label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span className="is-size-5">溫度：</span>
                                    <input className="input" type="number" maxLength={10} style={{width: '80px'}}
                                        disabled={af}
                                        value={this.state.af_temp || ''}
                                        onChange={this.af_tempChange.bind(this)}
                                    />
                                    <span className="is-size-5">℃</span>
                                </td>
                                <td colSpan={2}>
                                    <span className="is-size-5">備註：</span>
                                    <div className="select">
                                        <select
                                            placeholder="請選擇"
                                            disabled={af}
                                            onChange={this.af_rmkChange.bind(this)}
                                            value={this.state.af_rmk || ''}
                                        >
                                            <option value=""></option>
                                            <option value="參加集會">參加集會</option>
                                            <option value="溫度異常">溫濕度異常</option>
                                            <option value="設備異常">儀器異常</option>
                                            <option value="其他">其他</option>
                                        </select>
                                    </div>
                                </td>
                            </tr>
                            {/*
                            <tr>
                                <td>
                                    <label className="radiobox is-size-5">安全推桿&emsp;</label>
                                    <label className="radio">
                                        <input type="radio"
                                            name="af_putt_y" 
                                            disabled={af}
                                            value={'Y'}
                                            checked={this.state.af_putt === 'Y'}
                                            onChange={this.af_puttChange.bind(this)}
                                        />
                                        正常
                                    </label>
                                    <label className="radio">
                                        <input type="radio" 
                                            name="af_putt_n" 
                                            disabled={af}
                                            value={'N'}
                                            checked={this.state.af_putt === 'N'}
                                            onChange={this.af_puttChange.bind(this)}
                                        />
                                        異常
                                    </label>
                                </td>
                                <td>
                                    <label className="radiobox is-size-5">無線門鈴發報機&emsp;</label>
                                    <label className="radio">
                                        <input type="radio"
                                            name="af_bell_y" 
                                            disabled={af}
                                            value={'Y'}
                                            checked={this.state.af_bell === 'Y'}
                                            onChange={this.af_bellChange.bind(this)}
                                        />
                                        正常
                                    </label>
                                    <label className="radio">
                                        <input type="radio" 
                                            name="af_bell_n" 
                                            disabled={af}
                                            value={'N'}
                                            checked={this.state.af_bell === 'N'}
                                            onChange={this.af_bellChange.bind(this)}
                                        />
                                        異常
                                    </label>
                                </td>
                                <td>
                                    <label className="radiobox is-size-5">照明設備&emsp;</label>
                                    <label className="radio">
                                        <input type="radio"
                                            name="af_light_y" 
                                            disabled={af}
                                            value={'Y'}
                                            checked={this.state.af_light === 'Y'}
                                            onChange={this.af_lightChange.bind(this)}
                                        />
                                        正常
                                    </label>
                                    <label className="radio">
                                        <input type="radio" 
                                            name="af_light_n" 
                                            disabled={af}
                                            value={'N'}
                                            checked={this.state.af_light === 'N'}
                                            onChange={this.af_lightChange.bind(this)}
                                        />
                                        異常
                                    </label>
                                </td>
                            </tr>
                            */}
                        </tbody>
                    </table>
                </div>
                <div className="column">
                    <span className="is-size-5">異常事項：</span>
                    <div className="select">
                        <select
                            placeholder="請選擇"
                            disabled={af}
                            onChange={this.error_itemChange.bind(this)}
                            value={this.state.error_item || ''}
                        >
                            <option value=""></option>
                            <option value="1">超溫警報</option>
                            <option value="2">超時警報</option>
                            <option value="3">其他</option>
                        </select>
                    </div>
                </div>
                <div className="column">
                    <label className="label">備註</label>
                    <input type="text" className="input" maxLength={50}
                        disabled={mo && af}
                        value={this.state.rmk || ''}
                        onChange={this.rmkChange.bind(this)}
                    />
                </div>
                <div className="column">
                    <div className="field is-grouped">
                        <p className="control">
                            {(mo && af) ? 
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

