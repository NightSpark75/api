/** 
 * Wetestlog.js
 */
import React from "react";
import { Link } from "react-router";
import { Button, Col, Panel, FormControl, FormGroup, Checkbox } from "react-bootstrap";
import FieldGroup from  '../../../../components/includes/FieldGroup';
import axios from 'axios';

export default class Wetestlog extends React.Component{
    constructor(props) {
        super(props);
        this.state = {
            point_info: {}, point_no: '', ldate: '', 
            mo_hum: '', mo_max: '', mo_min: '', mo_rmk: '', mo_time: '', 
            af_hum: '', af_max: '', af_min: '', af_rmk: '', af_time: '',  
            ev_hum: '', ev_max: '', ev_min: '', ev_rmk: '', ev_time: '',  
            zero: 'N', rmk: '',
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
        axios.get('/api/web/mpz/pointlog/wetest/init/' + point_info.point_no)
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
                mo_hum: data.mo_hum, mo_max: data.mo_max, mo_min: data.mo_min, mo_rmk: data.mo_rmk, mo_time: data.mo_time,  
                af_hum: data.af_hum, af_max: data.af_max, af_min: data.af_min, af_rmk: data.af_rmk, af_time: data.af_time,  
                ev_hum: data.ev_hum, ev_max: data.ev_max, ev_min: data.ev_min, ev_rmk: data.ev_rmk, ev_time: data.ev_time,  
                zero: data.zero, rmk: data.rmk,
                mo: mo, af: af, ev: ev,
            });
        }
    }
    
    onSave(e) {
        let self = this;
        this.setState({isLoading: true});
        const {
            point_no, ldate, 
            mo_hum, mo_max, mo_min, mo_rmk, mo_time,  
            af_hum, af_max, af_min, af_rmk, af_time,  
            ev_hum, ev_max, ev_min, ev_rmk, ev_time, 
            zero, rmk
        } = this.state;
        let form_data = new FormData();
        form_data.append('point_no', point_no);
        form_data.append('ldate', ldate);
        form_data.append('mo_hum', mo_hum || '');
        form_data.append('mo_max', mo_max || '');
        form_data.append('mo_min', mo_min || '');
        form_data.append('mo_time', mo_time || '');
        form_data.append('mo_rmk', mo_rmk || '');
        form_data.append('af_hum', af_hum || '');
        form_data.append('af_max', af_max || '');
        form_data.append('af_time', af_time || '');
        form_data.append('af_min', af_min || '');
        form_data.append('af_rmk', af_rmk || '');
        form_data.append('ev_hum', ev_hum || '');
        form_data.append('ev_max', ev_max || '');
        form_data.append('ev_time', ev_time || '');
        form_data.append('ev_min', ev_min || '');
        form_data.append('ev_rmk', ev_rmk || '');
        form_data.append('zero', zero || 'N');
        form_data.append('rmk', rmk || '');
        axios.post('/api/web/mpz/pointlog/wetest/save', form_data)
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

    mo_maxChange(e) {
        this.setState({mo_max: e.target.value});
    }
    mo_humChange(e) {
        this.setState({mo_hum: e.target.value});
    }
    mo_minChange(e) {
        this.setState({mo_min: e.target.value});
    }
    mo_rmkChange(e) {
        this.setState({mo_rmk: e.target.value});
    }
    af_maxChange(e) {
        this.setState({af_max: e.target.value});
    }
    af_humChange(e) {
        this.setState({af_hum: e.target.value});
    }
    af_minChange(e) {
        this.setState({af_min: e.target.value});
    }
    af_rmkChange(e) {
        this.setState({af_rmk: e.target.value});
    }
    ev_maxChange(e) {
        this.setState({ev_max: e.target.value});
    }
    ev_humChange(e) {
        this.setState({ev_hum: e.target.value});
    }
    ev_minChange(e) {
        this.setState({ev_min: e.target.value});
    }
    ev_rmkChange(e) {
        this.setState({ev_rmk: e.target.value});
    }
    zeroChange(e) {
        let value = this.state.zero === 'Y' ? 'N' : 'Y';
        this.setState({zero: value});
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
                <h4><strong>最濕點濕度記錄表</strong></h4>
                <table className="table table-bordered" style={{marginBottom: '10px'}}>
                    <tbody>
                        <tr>
                            <td>名稱</td><td>{point_info.point_name}</td><td>儀器編號</td><td>{point_info.mach_no}</td>
                        </tr>
                        <tr>
                            <td>儀器校期</td><td>{point_info.ch_date}</td>
                            <td>合格範圍</td><td>{point_info.hum_range}</td>
                        </tr>
                    </tbody>
                </table>
                <div className="row">
                    <div className="col-md-12 col-sm-12">
                        <Panel header="上午(1)濕度記錄" bsStyle="info" style={{marginBottom: '10px'}}>
                            <table>
                                <tbody>
                                    <tr>
                                        <td className="col-md-2 col-sm-2">
                                            <label htmlFor="mo_hum">顯示值</label>
                                            <input type="text" className="form-control" id="mo_hum" maxLength={10}
                                                disabled={mo}
                                                value={this.state.mo_hum || ''}
                                                onChange={this.mo_humChange.bind(this)}
                                            />
                                        </td>
                                        <td className="col-md-2 col-sm-3">
                                            <label htmlFor="mo_max">MAX</label>
                                            <input type="text" className="form-control" id="mo_max" maxLength={10}
                                                disabled={mo}
                                                value={this.state.mo_max || ''}
                                                onChange={this.mo_maxChange.bind(this)}
                                            />
                                        </td>
                                        <td className="col-md-5 col-sm-4">
                                            <label htmlFor="mo_min">MIN</label>
                                            <input type="text" className="form-control" id="mo_min" maxLength={50}
                                                disabled={mo}
                                                value={this.state.mo_min || ''}
                                                onChange={this.mo_minChange.bind(this)}
                                            />
                                        </td>
                                        <td className="col-md-3 col-sm-3">
                                            <label htmlFor="mo_rmk">備註</label>
                                            <FormControl 
                                                componentClass="select" 
                                                placeholder="請選擇"
                                                disabled={mo}
                                                onChange={this.mo_rmkChange.bind(this)}
                                                value={this.state.mo_rmk || ''}
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
                                    </tr>
                                </tbody>
                            </table>
                        </Panel>
                    </div>
                </div>
                <div className="row">
                    <div className="col-md-12 col-sm-12">
                        <Panel header="下午(1)濕度記錄" bsStyle="info" style={{marginBottom: '10px'}}>
                            <table>
                                <tbody>
                                    <tr>
                                        <td className="col-md-2 col-sm-2">
                                            <label htmlFor="af_hum">顯示值</label>
                                            <input type="text" className="form-control" id="af_hum" maxLength={10}
                                                disabled={af}
                                                value={this.state.af_hum || ''}
                                                onChange={this.af_humChange.bind(this)}
                                            />
                                        </td>
                                        <td className="col-md-2 col-sm-3">
                                            <label htmlFor="af_max">MAX</label>
                                            <input type="text" className="form-control" id="af_max" maxLength={10}
                                                disabled={af}
                                                value={this.state.af_max || ''}
                                                onChange={this.af_maxChange.bind(this)}
                                            />
                                        </td>
                                        <td className="col-md-5 col-sm-4">
                                            <label htmlFor="af_min">MIN</label>
                                            <input type="text" className="form-control" id="af_min" maxLength={50}
                                                disabled={af}
                                                value={this.state.af_min || ''}
                                                onChange={this.af_minChange.bind(this)}
                                            />
                                        </td>
                                        <td className="col-md-3 col-sm-3">
                                            <label htmlFor="af_rmk">備註</label>
                                            <FormControl 
                                                componentClass="select" 
                                                placeholder="請選擇"
                                                disabled={af}
                                                onChange={this.af_rmkChange.bind(this)}
                                                value={this.state.af_rmk || ''}
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
                                    </tr>
                                </tbody>
                            </table>
                        </Panel>
                    </div>
                </div>
                <div className="row">
                    <div className="col-md-12 col-sm-12">
                        <Panel header="下午(2)濕度記錄" bsStyle="info" style={{marginBottom: '10px'}}>
                            <table>
                                <tbody>
                                    <tr>
                                        <td className="col-md-2 col-sm-2">
                                            <label htmlFor="ev_hum">顯示值</label>
                                            <input type="text" className="form-control" id="ev_hum" maxLength={10}
                                                disabled={ev}
                                                value={this.state.ev_hum || ''}
                                                onChange={this.ev_humChange.bind(this)}
                                            />
                                        </td>
                                        <td className="col-md-2 col-sm-3">
                                            <label htmlFor="ev_max">MAX</label>
                                            <input type="text" className="form-control" id="ev_max" maxLength={10}
                                                disabled={ev}
                                                value={this.state.ev_max || ''}
                                                onChange={this.ev_maxChange.bind(this)}
                                            />
                                        </td>
                                        <td className="col-md-5 col-sm-4">
                                            <label htmlFor="ev_min">MIN</label>
                                            <input type="text" className="form-control" id="ev_min" maxLength={50}
                                                disabled={ev}
                                                value={this.state.ev_min || ''}
                                                onChange={this.ev_minChange.bind(this)}
                                            />
                                        </td>
                                        <td className="col-md-3 col-sm-3">
                                            <label htmlFor="ev_rmk">備註</label>
                                            <FormControl 
                                                componentClass="select" 
                                                placeholder="請選擇"
                                                disabled={ev}
                                                onChange={this.ev_rmkChange.bind(this)}
                                                value={this.state.ev_rmk || ''}
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
                                    </tr>
                                </tbody>
                            </table>
                        </Panel>
                    </div>
                </div>
                <div className="row" style={{marginBottom: '10px'}}>
                    <div className="col-md-12 col-sm-12">
                        <FormGroup>
                            <Checkbox
                                name="zero" 
                                value={this.state.zero}
                                checked={this.state.zero === 'Y'}
                                onChange={this.zeroChange.bind(this)}
                            >
                                <strong>
                                    歸零確認
                                </strong>
                            </Checkbox>
                        </FormGroup>
                        <label htmlFor="rmk">備註</label>
                        <input type="text" className="form-control" id="rmk" maxLength={50}
                            value={this.state.rmk || ''}
                            onChange={this.rmkChange.bind(this)}
                        />
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

