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
            receiver: '',
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
        axios.get('/api/web/mpe/qa/receive/check')
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
        axios.get('/api/web/mpe/qa/receive/check/detail/' + lsa_no)
        .then(function (response) {
            if (response.data.result) {
                self.setState({
                    lsa_d: response.data.lsa_d,
                    lsa_e: response.data.lsa_e,
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

    receiveUserChange(e) {
        let receiver = e.target.value
        this.setState({receiver: receiver})
    }

    checkReceiver() {
        let receiver = this.state.receiver
        let self = this  
        axios.get('/api/web/mpe/qa/receive/user/' + receiver)
        .then(function (response) {
            if (response.data.result) {
                let empno = response.data.user.empno
                let ename = response.data.user.ename
                let dname = response.data.user.dname
                self.setState({
                    msgType: 'success',
                    msg: '['+empno+']'+ename+' '+dname,
                    posting: true,
                })
            } else {
                self.setState({
                    msgType: 'success',
                    msg: '人員確認有誤',
                    posting: false,
                })
            }
        }).catch(function (error) {
            console.log(error)
        })
    }

    goPosting() {
        if(confirm('您確定要領料過帳嗎？')) {
            let self = this      
            let no = this.state.item_m['no']
            let receiver = this.state.receiver
            let form_data = new FormData()
            form_data.append('no', no)
            form_data.append('receive_user', receiver)
            axios.post('/api/web/mpe/qa/receive/confirm', form_data)
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
                    alert('已完成收樣確認!')
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
                                        <button className={"button is-success"} disabled={!posting} onClick={this.goPosting.bind(this)}>收樣確認</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="box" style={{ marginBottom: '10px' }}>
                            <div className="field is-horizontal">
                                <div className="field-body">
                                    <div className="field is-grouped">
                                        <div className="field has-addons has-addons-right" style={{marginRight: '10px'}}>
                                            <p className="control">
                                                <input 
                                                    type="text" 
                                                    className="input" 
                                                    disabled={posting}
                                                    value={this.state.barcode}
                                                    autoFocus
                                                    maxLength={8}
                                                    placeholder="輸入收樣人員編號"
                                                    onChange={this.receiveUserChange.bind(this)}
                                                />
                                            </p>
                                            <p>
                                                <button className="button" onClick={this.checkReceiver.bind(this)}>人員確認</button>
                                            </p>
                                        </div>
                                        {this.state.msg !== '' &&
                                            <div className={"notification is-" + this.state.msgType} style={{padding: '6px'}}>
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
                            {this.state.lsa_d.map((item_d,index_d) => (
                                <table className={"table is-bordered is-fullwidth"} style={{marginBottom: '10px'}} key={index_d}>
                                    <thead>
                                        <tr>
                                            <th width="80">料號</th>
                                            <th width="120">{item_d.partno}</th>
                                            <th width="80">批號</th>
                                            <th width="160">{item_d.bno}</th>
                                            <th width="90">品名</th>
                                            <th colSpan={3}>{item_d.pname}</th>
                                        </tr>
                                        <tr>
                                            <th>倉庫</th><th>{item_d.whouse}</th>
                                            <th>儲位</th><th>{item_d.stor}</th>
                                            <th>申請數量</th><th colSpan={3}>{item_d.qty + item_d.unit}</th>
                                        </tr>
                                    </thead>
                                    {item_d &&
                                        <tbody>
                                            {this.state.lsa_e.map((item_e, index_e) => (
                                                (item_d.bno === item_e.bno && item_d.partno === item_e.partno) &&
                                                <tr key={index_e}>
                                                    <td>條碼號</td><td style={{color: 'red'}}>{item_e.barcode}</td>
                                                    <td>剩餘量</td><td>{item_e.qty + item_e.unit}</td>
                                                    <td>瓶身單位</td><td>{item_e.usize + item_e.unit}</td>
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
                                        <th width="60"></th>
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
                                                    收樣確認
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