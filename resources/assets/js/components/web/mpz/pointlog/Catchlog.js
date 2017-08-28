/** 
 * Catchlog.js
 */
import React from "react";
import { Link } from "react-router";
import axios from 'axios';

export default class Catchlog extends React.Component{
    constructor(props) {
        super(props);
        this.state = {
            log_data: {},
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
        return(
            <div>
                <div className="column is-offset-1">
                    <h4 className="title is-4">鼠蟲防治記錄表</h4>
                    <div className="subtitle is-5">
                        本月累計：{this.state.thisMonth}<br/>
                        上月統計：{this.state.lastMonth}
                    </div>
                </div>
                <form>
                    {this.state.vn1 &&
                        <div className="field is-horizontal">
                            <div className="field-label is-normal">
                                <label className="label">黏附</label>
                            </div>
                            <div className="field-body">
                                <div className="field">
                                    <input className="input" type="number"
                                        value={this.state.catch_num1}
                                        onChange={this.catchChange.bind(this, '1')}
                                        required
                                    />
                                </div>
                            </div>
                        </div>
                    }
                    {this.state.vn2 &&
                        <div className="field is-horizontal">
                            <div className="field-label is-normal">
                                <label className="label">承接</label>
                            </div>
                            <div className="field-body">
                                <div className="field">
                                    <input className="input" type="number"
                                        value={this.state.catch_num2}
                                        onChange={this.catchChange.bind(this, '2')}
                                        required
                                    />
                                </div>
                            </div>
                        </div>
                    }
                    {this.state.vlp && 
                        <div className="field is-horizontal">
                            <div className="field-label">
                                <label className="label">驅蚊燈檢查</label>
                            </div>
                            <div className="field-body">
                                <div className="field is-narrow">
                                    <div className="control">
                                        <label className="checkbox">
                                            <input type="checkbox"
                                                value={this.state.lamp}
                                                checked={this.state.lamp === 'Y'}
                                                onChange={this.checkboxChange.bind(this, 'lamp')}
                                            />
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    }
                    {this.state.vn3 &&
                        <div className="field is-horizontal">
                            <div className="field-label is-normal">
                                <label className="label">壁虎</label>
                            </div>
                            <div className="field-body">
                                <div className="field is-narrow">
                                    <input className="input" type="number"
                                        value={this.state.catch_num3}
                                        onChange={this.catchChange.bind(this, '3')}
                                        required
                                    />
                                </div>
                            </div>
                        </div>
                    }
                    {this.state.vn4 &&
                        <div className="field is-horizontal">
                            <div className="field-label">昆蟲</div>
                            <div className="field-body">
                                <div className="field is-expanded">
                                    <div className="control">
                                        <input className="input" type="number"
                                            value={this.state.catch_num4}
                                            onChange={this.catchChange.bind(this, '4')}
                                            required
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    }
                    {this.state.vn5 &&
                        <div className="field is-horizontal">
                            <div className="field-label">鼠類</div>
                            <div className="field-body">
                                <div className="field is-expanded">
                                    <input className="input" type="number"
                                        value={this.state.catch_num5}
                                        onChange={this.catchChange.bind(this, '5')}
                                        required
                                    />
                                </div>
                            </div>
                        </div>
                    }
                    {this.state.vn6 &&
                        <div className="field is-horizontal">
                            <div className="field-label">昆蟲</div>
                            <div className="field-body">
                                <div className="field is-expanded">
                                    <input className="input" type="number"
                                        value={this.state.catch_num6}
                                        onChange={this.catchChange.bind(this, '6')}
                                        required
                                    />
                                </div>
                            </div>
                        </div>
                    }
                    {this.state.vc1 && 
                        <div className="field is-horizontal">
                            <div className="field-label">
                                <label className="label">更換捕蚊紙</label>
                            </div>
                            <div className="field-body">
                                <div className="field is-narrow">
                                    <div className="control">
                                        <label className="checkbox">
                                            <input type="checkbox"
                                                value={this.state.change1}
                                                checked={this.state.change1 === 'Y'}
                                                onChange={this.checkboxChange.bind(this, '1')}
                                            />
                                            {this.state.changeDate['change1']}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    }
                    {this.state.vc2 && 
                        <div className="field is-horizontal">
                            <div className="field-label">
                                <label className="label">更換承接膠帶</label>
                            </div>
                            <div className="field-body">
                                <div className="field is-narrow">
                                    <div className="control">
                                        <label className="checkbox">
                                            <input type="checkbox"
                                                value={this.state.change2}
                                                checked={this.state.change2 === 'Y'}
                                                onChange={this.checkboxChange.bind(this, '2')}
                                            />
                                            {this.state.changeDate['change2']}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    }
                    {this.state.vc3 &&  
                        <div className="field is-horizontal">
                            <div className="field-label">
                                <label className="label">更換驅蚊燈管</label>
                            </div>
                            <div className="field-body">
                                <div className="field is-narrow">
                                    <div className="control">
                                        <label className="checkbox">
                                            <input type="checkbox"
                                                value={this.state.change3}
                                                checked={this.state.change3 === 'Y'}
                                                onChange={this.checkboxChange.bind(this, '3')}
                                            />
                                            {this.state.changeDate['change3']}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    }
                    {this.state.vc4 && 
                        <div className="field is-horizontal">
                            <div className="field-label">
                                <label className="label">更換驅蚊燈管</label>
                            </div>
                            <div className="field-body">
                                <div className="field is-narrow">
                                    <div className="control">
                                        <label className="checkbox">
                                            <input type="checkbox"
                                                value={this.state.change4}
                                                checked={this.state.change4 === 'Y'}
                                                onChange={this.checkboxChange.bind(this, '4')}
                                            />
                                            {this.state.changeDate['change4']}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    }
                    {this.state.vc5 && 
                        <div className="field is-horizontal">
                            <div className="field-label">
                                <label className="label">更換黏鼠板</label>
                            </div>
                            <div className="field-body">
                                <div className="field is-narrow">
                                    <div className="control">
                                        <label className="checkbox">
                                            <input type="checkbox"
                                                value={this.state.change5}
                                                checked={this.state.change5 === 'Y'}
                                                onChange={this.checkboxChange.bind(this, '5')}
                                            />
                                            {this.state.changeDate['change5']}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    }
                    {this.state.vc6 && 
                        <div className="field is-horizontal">
                            <div className="field-label">
                                <label className="label">更換防蟻措施</label>
                            </div>
                            <div className="field-body">
                                <div className="field is-narrow">
                                    <div className="control">
                                        <label className="checkbox">
                                            <input type="checkbox"
                                                value={this.state.change6}
                                                checked={this.state.change6 === 'Y'}
                                                onChange={this.checkboxChange.bind(this, '6')}
                                            />
                                            {this.state.changeDate['change6']}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    }
                    <div className="field is-horizontal">
                        <div className="field-label is-normal">
                            <label className="label">備註</label>
                        </div>
                        <div className="field-body">
                            <div className="field is-narrow">
                                <div className="control">
                                    <div className="select is-fullwidth">
                                        <select
                                            placeholder="請選擇"
                                            onChange={this.catchChange.bind(this, 'rmk')}
                                            value={this.state.rmk}
                                        >
                                            <option value=""></option>
                                            <option value="數量超標">數量超標</option>
                                            <option value="器具異常">器具異常</option>
                                            <option value="新設點位">新設點位</option>
                                            <option value="其他">其他</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="field is-horizontal">
                        <div className="field-label is-normal">
                            <label className="label">其他</label>
                        </div>
                        <div className="field-body">
                            <div className="field">
                            <div className="control">
                                <textarea className="textarea" placeholder="請輸入其它說明" 
                                    value={this.state.discription || ''}
                                    onChange={this.catchChange.bind(this, 'dis')}
                                >
                                </textarea>
                            </div>
                            </div>
                        </div>
                    </div>
                    <div className="column is-offset-1">
                        <div className="field is-grouped">
                            <p className="control">
                                {comp ? 
                                    <button className="button is-primary is-static">今日已完成記錄</button>
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
                </form>
            </div>
        );
    };
}

