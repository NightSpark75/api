/** 
 * Receive.js
 */
import React from 'react'
import { Link } from 'react-router'
import axios from 'axios'

export default class Receive extends React.Component{
    constructor(props) {
        super(props)

        this.state = {
            lsa_m: [], search_m: [], lsa_d: [], lsa_e: [],
            item_m: [], item_d: [], item_e: [],
            search: false, search_str: '',
            barcode: '',
            posting: false,
            showReceive: false,
            msg: '',
            msgType: '',
        }
    }

    componentDidMount() {
        this.init()
    }

    init() {
        let self = this       
        axios.get('/api/web/mpe/qa/receive/list')
        .then(function (response) {
            if (response.data.result) {
                self.setState({
                    lsa_m: response.data.lsa_m,
                })
                console.log(response.data)
            } else {
                console.log(response.data)
            }
        }).catch(function (error) {
            console.log(error)
        })
    }

    searchChange(e) {
        this.setState({search_str: e.target.value})
    }

    onSearch() {
        let lsa_m = this.state.lsa_m
        let lsa_no = this.state.search_str
        let item = []
        for (let i = 0; i < lsa_m.length; i++) {
            if (lsa_m[i]['no'] === lsa_no) {
                item[0] = lsa_m[i]
                this.setState({
                    search: true,
                    search_m: item
                })
                return
            }
        }
    }

    cancelSearch() {
        this.setState({
            search: false,
            search_m: [],
        })
    }

    goReceive(lsa_no) {
        this.getReceiveList(lsa_no)
        const { lsa_d, lsa_e } = this.state
        const item_m = this.setFormMaster(lsa_no)
        
        this.setState({
            item_m: item_m, 
            showReceive: true,
        })
    }

    getReceiveList(lsa_no) {
        let self = this       
        axios.get('/api/web/mpe/qa/receive/detail/' + lsa_no)
        .then(function (response) {
            if (response.data.result) {
                const item_d = self .setSelect(lsa_no, response.data.lsa_d, 'lsa_no')
                const item_e = self .setSelect(lsa_no, response.data.lsa_e, 'lsa_no')
                self.setState({
                    lsa_d: response.data.lsa_d,
                    lsa_e: response.data.lsa_e,
                    item_d: item_d, 
                    item_e: item_e,
                })
                console.log(response.data)
            } else {
                console.log(response.data)
            }
        }).catch(function (error) {
            console.log(error)
        })
    }

    setFormMaster(lsa_no) {
        const lsa_m = this.state.lsa_m
        let item = []
        for (let i = 0; i < lsa_m.length; i++) {
            if (lsa_m[i]['no'] === lsa_no) {
                item = lsa_m[i]
                return item
            }
        }
    }

    setSelect(lsa_no, list, id) {
        let item = []
        let seq = 0
        for (let i = 0; i < list.length; i++) {
            if (list[i][id] === lsa_no) {
                item[seq] = list[i]
                seq++
            }
        }
        return item
    }

    barcodeChange(e) {
        let barcode = e.target.value
        this.setState({barcode: barcode})
        if (barcode.length === 8) {
            this.checkBarcode(barcode)
        }
    }

    checkBarcode(barcode) { 
        const { item_d, item_e } = this.state
        let index_e = 0
        let index_d = 0
        for (let i = 0; i < item_e.length; i++) {
            if (item_e[i].barcode === barcode) {
                if (item_e[i].sta === 'Y') {
                    this.setState({
                        msgType: 'warning',
                        msg: '[' + barcode + ']已重複領用!',
                    })
                    return
                }
                item_e[i].sta = 'Y'
                index_e = i
                this.setState({
                    item_e: item_e,
                    barcode: '',
                    msgType: 'info',
                    msg: '[' + barcode + ']已領用',
                })
                this.checkSuccess(index_e)
                return
            }
        }
        this.setState({
            msgType: 'danger',
            msg: '[' + barcode + ']非此申請單內之品項!',
        })
    }

    checkSuccess(index_e) {
        const { item_d, item_e } = this.state
        let receive_qty = 0
        let apply_qty = 0
        let index_d = 0

        for (let i = 0; i < item_d.length; i++) {
            if (item_d[i].partno === item_e[index_e].partno && item_d[i].bno === item_e[index_e].batch) {
                apply_qty = parseFloat(item_d[i].qty)
                receive_qty = parseFloat(item_d[i].receive_qty)
                receive_qty = isNaN(receive_qty) ? 0 : receive_qty
                index_d = i
            }
        }

        if (receive_qty >= apply_qty) {
            this.setState({
                msgType: 'warning',
                msg: '批號[' + item_d[index_d].bno + ']已領足數量!',
            })
        } else {
            receive_qty = receive_qty + parseFloat(item_e[index_e].amt)
            item_d[index_d].receive_qty = receive_qty
            if (receive_qty >= apply_qty) {
                item_d[index_d].status = 'Y'
                this.setState({
                    item_d: item_d,
                    msgType: 'info',
                    msg: '批號[' + item_d[index_d].bno + ']已領足數量!',
                })
                this.checkPosting(item_d)
            }
        }
    }

    checkPosting(item_d) {
        for (let i = 0; i < item_d.length; i++) {
            if (item_d[i].status !== 'Y') {
                this.setState({
                    posting: false,
                })
                return
            }
        }

        var item_m =  this.state.item_m
        item_m.status = 'R'
        this.setState({
            item_m: item_m,
            barcode: '',
            posting: true,
            msgType: 'success',
            msg: '申請單號[' + item_m.no + ']已領用完畢，確認後即可過帳!'
        })
    }

    goPosting() {
        if(confirm('您確定要領料過帳嗎？')) {
            let self = this      
            let no = this.state.item_m['no']
            let item_e = this.state.item_e
            let form_data = new FormData()
            form_data.append('no', no)
            form_data.append('item_e', JSON.stringify(item_e))
            axios.post('/api/web/mpe/qa/receive/posting', form_data)
            .then(function (response) {
                if (response.data.result) {
                    let { lsa_m, lsa_d, lsa_e } = self.state 
                    lsa_m = self.removeItem(no, lsa_m, 'no')
                    lsa_d = self.removeItem(no, lsa_d, 'lsa_no')
                    lsa_e = self.removeItem(no, lsa_e, 'lsa_no')
                    self.setState({
                        lsa_m: lsa_m,
                        lsa_d: lsa_d,
                        lsa_e: lsa_e,    
                    })
                    alert('已完成領料過帳!')
                    self.goReceiveList()
                    console.log(response.data)
                } else {
                    console.log(response.data)
                }
            }).catch(function (error) {
                console.log(error)
            })
        }
    }

    removeItem(no, list, pk) {
        let seq = 0
        for (let i = 0; i < list.length; i++) {
            if (list[seq][pk] === no) {
                list.splice(seq, 1)
            } else {
                seq++
            }
        }
        return list
    }

    goMenu() {
        window.location = '/auth/web/menu'
    }

    goReceiveList() {
        this.setState({
            item_m: [],
            item_d: [],
            item_e: [],
            posting: false,
            showReceive: false,
            msg: '',
        })
    }

    deleteReceive(index_d, index_e) {
        var item_e = this.state.item_e
        var item_d = this.state.item_d
        let posting = this.state.posting
        item_d[index_d].receive_qty = item_d[index_d].receive_qty - item_e[index_e].amt
        if (item_d[index_d].receive_qty < item_d[index_d].qty) {
            item_d[index_d].status = 'N'
            posting = false
        }
        item_e[index_e].sta = 'N'
        this.setState({
            item_d: item_d,
            item_e: item_e,
            posting: posting,
            msgType: 'warning',
            msg: item_e[index_e]['barcode'] + '已取消領用!'
        })
    }

    render() { 
        const { showReceive, posting, msg, msgType, item_m, search} = this.state
        const list = search ? this.state.search_m : this.state.lsa_m
        let table_color = (status) => {
            return status === 'Y'? 'is-success': 'is-orange'
        }
        return(   
            <div>
                {showReceive ? 
                    <div>
                        <div className="box" style={{ marginTop: '10px', marginBottom: '10px' }}>
                            <div className="level">
                                <div className="level-left">
                                    <div className="level-item">
                                    {showReceive ?
                                        <button className="button" 
                                            onClick={this.goReceiveList.bind(this)}
                                        >
                                            返回領料申請清單
                                        </button>
                                    :
                                        <Link className="button" to="/auth/web/menu">&larr;; 功能選單</Link>
                                    }
                                    </div>
                                </div>
                                <div className="level-right">
                                    <div className="level-item">
                                        <button className={"button is-success"} disabled={!posting} onClick={this.goPosting.bind(this)}>領料過帳</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="box" style={{ marginBottom: '10px' }}>
                            <div className="field is-horizontal">
                                <div className="field-body">
                                    <div className="field is-grouped">
                                        <div className="field" style={{marginRight: '10px'}}>
                                            <input 
                                                type="text" 
                                                className="input is-large" 
                                                disabled={posting}
                                                value={this.state.barcode}
                                                autoFocus
                                                maxLength={8}
                                                placeholder="掃描條碼"
                                                onChange={this.barcodeChange.bind(this)}
                                            />
                                        </div>
                                        {this.state.msg !== '' &&
                                            <div className={"notification is-" + this.state.msgType} style={{padding: '1rem 1rem 1rem 1rem'}}>
                                                {this.state.msg}
                                            </div>
                                        } 
                                    </div>
                                </div>
                            </div>
                        </div>
                        <table className="table is-bordered is-fullwidth" style={{marginBottom: '10px'}}> 
                            <tbody>
                                <tr>
                                    <td>申請單號</td><td>{item_m.no}</td>
                                    <td>申請日期</td><td>{item_m.apply_date}</td>
                                    <td>需求日期</td><td>{item_m.req_date}</td>
                                </tr>
                                <tr>
                                    <td>申請人</td><td>{item_m.apply_user + item_m.uname}</td>
                                    <td>申請單位</td><td colSpan="3">{item_m.apply_unit + item_m.dname}</td>
                                </tr>
                            </tbody>
                        </table>
                        <div style={{height: '730px', overflow: 'auto'}}>
                            {this.state.item_d.map((item_d,index_d) => (
                                <table className={"table is-bordered is-fullwidth " + table_color(item_d.status)} style={{marginBottom: '10px'}} key={index_d}>
                                    <thead>
                                        <tr>
                                            <th width="80">料號</th>
                                            <th width="120">{item_d.partno}</th>
                                            <th width="80">批號</th>
                                            <th width="160">{item_d.bno}</th>
                                            <th width="90">品名</th>
                                            <th colSpan={2}>{item_d.pname}</th>
                                        </tr>
                                        <tr>
                                            <th>倉庫</th><th>{item_d.whouse}</th>
                                            <th>儲位</th><th>{item_d.stor}</th>
                                            <th>申請數量</th><th colSpan={2}>{item_d.qty + item_d.unit}</th>
                                        </tr>
                                    </thead>
                                    {item_d &&
                                        <tbody>
                                            {this.state.item_e.map((item_e, index_e) => (
                                                (item_d.bno === item_e.batch && item_d.partno === item_e.partno && item_e.sta === 'Y') &&
                                                <tr key={index_e}>
                                                    <td>條碼號</td><td>{item_e.barcode}</td>
                                                    <td>剩餘量</td><td>{item_e.amt + item_e.unit}</td>
                                                    <td>瓶身單位</td><td>{item_e.usize + item_e.unit}</td>
                                                    <td width="83">
                                                        <button className="button is-danger" 
                                                            onClick={this.deleteReceive.bind(this, index_d, index_e)}
                                                        >
                                                            刪除
                                                        </button>
                                                    </td>
                                                </tr> 
                                            ))}
                                        </tbody>
                                    }
                                </table>
                            ))}
                        </div>
                    </div>
                :
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
                                        <div className="field has-addons has-addons-right">
                                            <p className="control">
                                                <input 
                                                    type="text" 
                                                    className="input" 
                                                    maxLength={9}
                                                    value={this.state.search_str}
                                                    onChange={this.searchChange.bind(this)}
                                                />
                                            </p>
                                            {search &&
                                                <p className="control">
                                                    <button className="button is-warning" onClick={this.cancelSearch.bind(this)}>取消</button>
                                                </p>
                                            }
                                            <p>
                                                <button className="button" onClick={this.onSearch.bind(this)}>查詢</button>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> 
                        {list.length > 0 && 
                            <table className="table is-bordered is-fullwidth"r>
                                <thead>
                                    <tr>
                                        <th>單號</th>
                                        <th>申請人</th>
                                        <th>單位</th>
                                        <th>申請日期</th>
                                        <th>需求日期</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {list.map((item, index) => (
                                        <tr key={index}>
                                            <td>{item.no}</td>
                                            <td>{item.apply_user + item.uname}</td>
                                            <td>{item.apply_unit + item.dname}</td>
                                            <td>{item.apply_date}</td>
                                            <td>{item.req_date}</td>
                                            <td>
                                                <button 
                                                    className="button is-primary"
                                                    onClick={this.goReceive.bind(this, item.no)}
                                                >
                                                    領用
                                                </button>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        }
                    </div>
                }    
            </div>
        )
    }
}