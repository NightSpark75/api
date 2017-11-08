/** 
 * Restore.js
 */
import React from 'react'
import { Link } from 'react-router'
import axios from 'axios'

export default class Receive extends React.Component{
    constructor(props) {
        super(props)

        this.state = {
            restore: [],
            item: null,
            restore_value: 0,
            ready: false,
            barcode: '',
            msg: '',
            msgType: '',
        }
    }

    componentDidMount() {
        this.init()
    }

    init() {
        let self = this       
        axios.get('/api/web/mpe/qa/restore/list')
        .then(function (response) {
            if (response.data.result) {
                self.setState({
                    restore: response.data.restore,
                    ready: true,
                })
            } else {
                self.setState({
                    msgType: 'warning',
                    msg: response.data.msg,
                })
            }
        }).catch(function (error) {
            console.log(error)
        })
    }

    barcodeChange(e) {
        let barcode = e.target.value
        const { restore } = this.state 
        this.setState({barcode: barcode})
        if (barcode.length === 8) {
            for (let i = 0; i < restore.length; i++) {
                if (restore[i].barcode === barcode) {
                    this.setState({
                        item: restore[i],
                        msg:'',
                    })
                    return
                }
            }
            this.setState({
                msgType: 'warning',
                msg: barcode + ' 非已領用狀態之料品',
            })
        }
    }

    valueChange(e) {
        let value = e.target.value
        this.setState({restore_value: value})
    }

    goRestore() {
        if(confirm('您確定要入庫過帳嗎？')) {
            let self = this      
            const { barcode, restore_value, item } = this.state
            if (restore_value > item.receive_qty) {
                this.setState({
                    msgType: 'warning',
                    msg: '回庫量大於領用量',
                })
                return
            }
            let form_data = new FormData()
            form_data.append('barcode', barcode)
            form_data.append('qty', restore_value)
            axios.post('/api/web/mpe/qa/restore/posting', form_data)
            .then(function (response) {
                if (response.data.result) {
                    self.resetList(barcode)
                } else {
                    self.setState({
                        msgType: 'warning',
                        msg: response.data.msg,
                    })
                }
            }).catch(function (error) {
                console.log(error)
            })
        }
    }

    resetList(barcode) {
        let list = this.state.restore
        for (let i = 0; i < list.length; i++) {
            if (list[i].barcode === barcode) {
                list.splice(i, 1)
                this.setState({
                    restore: list,
                    item: null,
                    restore_value: 0,
                    barcode: '',
                    msgType: 'success',
                    msg: barcode + ' 已完成入庫',
                })
                return
            }
        }
    }

    render () {
        const { item, barcode, ready, restore_value, msg, msgType } = this.state
        return(  
            <div>
                <div className="box" style={{ marginTop: '10px', marginBottom: '10px' }}>
                    <div className="level">
                        <div className="level-left">
                            <div className="level-item">
                                <Link className="button" to="/auth/web/menu">&larr 功能選單</Link>
                            </div>
                        </div>
                        <div className="level-right">
                            <div className="level-item">
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
                                        disabled={!ready}
                                        value={barcode}
                                        autoFocus
                                        maxLength={8}
                                        placeholder="掃描條碼"
                                        onChange={this.barcodeChange.bind(this)}
                                    />
                                </div>
                                {msg !== '' &&
                                    <div className={"notification is-" + msgType} style={{padding: '1rem 1rem 1rem 1rem'}}>
                                        {msg}
                                    </div>
                                } 
                            </div>
                        </div>
                    </div>
                    {item &&
                        <table className="table is-bordered">
                            <tbody>
                                <tr>
                                    <td>料號</td>
                                    <td>{ item.partno }</td>
                                    <td>批號</td>
                                    <td>{ item.batch }</td>
                                    <td>料品名稱</td>
                                    <td>{ item.pname }</td>
                                    <td>貯藏方式</td>
                                    <td>{ item.stor_me }</td>
                                </tr>
                                <tr>
                                    <td>倉庫</td>
                                    <td>{ item.posit }</td>
                                    <td>儲位</td>
                                    <td>{ item.storn }</td>
                                    <td>原料倉</td>
                                    <td>{ item.mcu }</td>
                                    <td>入庫日期</td>
                                    <td>{ item.ldate }</td>
                                </tr>
                                <tr>
                                    <td>單瓶領用量/總量</td>
                                    <td>{ item.receive_qty + '/' + item.usize + ' ' + item.unit }</td>
                                    <td>申請量</td>
                                    <td>{ item.apply_qty + ' ' + item.unit}</td>
                                    <td>領用總量</td>
                                    <td>{ item.all_receive_qty + ' ' + item.unit }</td>
                                    <td>應回庫量</td>
                                    <td>{ (item.all_receive_qty - item.apply_qty) + ' ' + item.unit }</td>
                                </tr>
                                <tr>
                                    <td>回庫量</td>
                                    <td colSpan={2}>
                                        <input type="number" className="input is-medium" 
                                            value={restore_value}
                                            onChange={this.valueChange.bind(this)}
                                        />
                                    </td> 
                                    <td colSpan={5}>
                                    <button className="button is-medium is-primary" onClick={this.goRestore.bind(this)}>入庫過帳</button>
                                    </td>   
                                </tr>
                            </tbody>
                        </table>
                    }
                </div>
            </div>
        )
    }
}