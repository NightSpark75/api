/** 
 * Receive.js
 */
import React from 'react';
import { Link } from 'react-router';
import axios from 'axios';

export default class Receive extends React.Component{
    constructor(props) {
        super(props);

        this.state = {
            barcode_list: [],
            receive_list: [],
            receive: false,
            barcode: '',
            msg: '',
            msgType: '',
        }
    }

    componentDidMount() {
        this.init();
    }

    init() {
        let self = this;       
        axios.get('/api/web/mpe/qc/receive/init')
        .then(function (response) {
            if (response.data.result) {
                self.setState({
                    barcode_list: response.data.barcode,
                });
                console.log(response.data);
            } else {
                console.log(response.data);
            }
        }).catch(function (error) {
            console.log(error);
        });
    }

    barcodeChange(e) {
        let barcode = e.target.value
        this.setState({barcode: barcode});
        if (barcode.length === 8) {
            this.checkBarcode(barcode);
        }
    }

    checkBarcode(barcode) {
        let list = this.state.barcode_list;
        let receive = this.state.receive_list;
        let item = [];
        for (let i = 0; i < list.length; i++) {
            if (receive.length > 0) {
                for (let x = 0; x < receive.length; x++) {
                    if (receive[x]['barcode'] === barcode) {
                        this.setState({
                            msgType: 'danger',
                            msg: '[' + barcode + ']已重複領用!'
                        });
                        return;
                    }
                }
            }
            if (list[i]['barcode'] === barcode) {
                item = list[i];
                this.addReceive(item);
                return;
            }
        }
        this.setState({
            msgType: 'danger',
            msg: '找不到[' + barcode + ']料品資訊，此料品並非在庫狀態!'
        });
    }

    addReceive(item) {
        let list = this.state.receive_list;
        list.push(item);
        this.setState({
            receive: true,
            receive_list: list,
            barcode: '',
            msgType: 'success',
            msg: '[' + item.barcode + ']' + item.partno + ' 已領用!',
        });
    }

    removeReceive(item) {
        let remove = []
        let list = this.state.receive_list;
        for (let i = 0; i < list.length; i++) {
            if (list[i]['barcode'] === item.barcode) {
                list.splice(i, 1);
            }
        }
        this.setState({
            receive_list: list,
            msgType: 'warning',
            msg: '[' + item.barcode + ']' + item.partno + ' 已移除!',
        });
    }

    goPosting() {
        if(confirm('您確定要領料過帳嗎？')) {
            let self = this;
            let list = JSON.stringify(this.state.receive_list);
            let form_data = new FormData();
            form_data.append('receive_list', list);
            axios.post('/api/web/mpe/qc/receive/posting', form_data)
            .then(function (response) {
                if (response.data.result) {
                    self.setState({
                        receive_list: [],
                        barcode: '',
                        msgType: 'success',
                        msg: '已完成領料過帳!',
                    });
                    console.log(response.data);
                } else {
                    console.log(response.data);
                }
            }).catch(function (error) {
                console.log(error);
            });
        }
    }

    goMenu() {
        window.location = '/auth/web/menu';
    }

    render() { 
        const { barcode, barcode_list, receive, receive_list, msg, msgType } = this.state;
        return(   
            <div>
                <div className="box" style={{ marginTop: '10px', marginBottom: '10px' }}>
                    <div className="level">
                        <div className="level-left">
                            <div className="level-item">
                                <Link className="button" to="/auth/web/menu">&larr; 功能選單</Link> 
                            </div>
                        </div>
                        <div className="level-right">
                            <div className="level-item">
                                <button className="button is-success" disabled={receive_list.length === 0} onClick={this.goPosting.bind(this)}>領料過帳</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div className="box" style={{ marginBottom: '10px' }}>
                    <div className="field is-horizontal">
                        <div className="field-body">
                            <div className="field is-grouped">
                                <div className="field" style={{marginRight: '10px'}}>
                                    <input type="text" className="input is-large" 
                                        disabled={barcode_list.length === 0}
                                        value={this.state.barcode}
                                        autoFocus
                                        maxLength={8}
                                        placeholder="掃描條碼"
                                        onChange={this.barcodeChange.bind(this)}
                                    />
                                </div>
                                {this.state.msg !== '' &&
                                    <div className="notification is-warning" style={{padding: '1rem 1rem 1rem 1rem'}}>
                                        {this.state.msg}
                                    </div>
                                } 
                            </div>
                        </div>
                    </div>
                </div>
                {receive_list.length > 0 &&
                    <table className="table is-bordered is-fullwidth">
                        <thead>
                            <tr>
                                <td>條碼號</td>
                                <td>料號</td>
                                <td>品名</td>
                                <td>批號</td>
                                <td>購入日期</td>
                                <td>失效日期</td>
                                <td>開封日期</td>
                                <td>開封後失效日期</td>
                                <td width="92.22"></td>
                            </tr>
                        </thead>
                        {receive_list.map((item,index) => (
                            <tbody key={index}>
                                <tr>
                                    <td>{item.barcode}</td>
                                    <td>{item.partno}</td>
                                    <td>{item.ename}</td>
                                    <td>{item.batch}</td>
                                    <td>{item.buydate}</td>
                                    <td>{item.valid}</td>
                                    <td>{item.opdate}</td>
                                    <td>{item.opvl}</td>
                                    <td>
                                        <button className="button is-danger" onClick={this.removeReceive.bind(this, item)}>移除</button>
                                    </td>
                                </tr>
                            </tbody>
                        ))}
                    </table>
                }
            </div>
        );
    }
}