/** 
 * Receive.js
 */
import React from 'react';
import ReactDOM from "react-dom";
import { Link } from 'react-router';
import axios from 'axios';
import { Button, ButtonToolbar, Table, Panel, Pager, FormControl, Alert, Col, ListGroup, ListGroupItem } from "react-bootstrap";

export default class Receive extends React.Component{
    constructor(props) {
        super(props);

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
        this.init();
    }

    init() {
        let self = this;       
        axios.get('/api/web/mpe/qa/receive/list')
        .then(function (response) {
            if (response.data.result) {
                self.setState({
                    lsa_m: response.data.lsa_m,
                    lsa_d: response.data.lsa_d,
                    lsa_e: response.data.lsa_e,
                });
                console.log(response.data);
            } else {
                console.log(response.data);
            }
        }).catch(function (error) {
            console.log(error);
        });
    }

    searchChange(e) {
        this.setState({search_str: e.target.value});
    }

    onSearch() {
        let lsa_m = this.state.lsa_m;
        let lsa_no = this.state.search_str;
        let item = [];
        for (let i = 0; i < lsa_m.length; i++) {
            if (lsa_m[i]['no'] === lsa_no) {
                item[0] = lsa_m[i];
                this.setState({
                    search: true,
                    search_m: item
                });
                return;
            }
        }
    }

    cancelSearch() {
        this.setState({
            search: false,
            search_m: [],
        });
    }

    goReceive(lsa_no, e) {
        let item_m = this.setFormMaster(lsa_no);
        let item_d = this.setSelect(lsa_no, this.state.lsa_d, 'lsa_no');
        let item_e = this.setSelect(lsa_no, this.state.lsa_e, 'lsa_no');
        this.setState({
            posting: (item_m['status'] === 'R' || item_m['posting'] === 'Y') ? true : false,
            item_m: item_m, 
            item_d: item_d, 
            item_e: item_e,
            showReceive: true,
        });
    }

    setFormMaster(lsa_no) {
        let lsa_m = this.state.lsa_m;
        let item = [];
        for (let i = 0; i < lsa_m.length; i++) {
            if (lsa_m[i]['no'] === lsa_no) {
                item = lsa_m[i];
                return item;
            }
        }
    }

    setSelect(lsa_no, list, id) {
        let item = [];
        let seq = 0;
        for (let i = 0; i < list.length; i++) {
            if (list[i][id] === lsa_no) {
                item[seq] = list[i];
                seq++;
            }
        }
        return item;
    }

    barcodeChange(e) {
        let barcode = e.target.value
        this.setState({barcode: barcode});
        if (barcode.length === 8) {
            this.checkBarcode(barcode);
        }
    }

    checkBarcode(barcode) {
        let item_d = this.state.item_d;
        let item_e = this.state.item_e;
        for (let i = 0; i < item_e.length; i++) {
            if (item_e[i]['barcode'] === barcode) {
                item_e[i]['status'] = 'Y';
                this.setState({
                    item_e: item_e,
                    barcode: '',
                    msgType: 'info',
                    msg: '[' + barcode + ']已領用',
                });
                this.checkSuccess(item_e[i]['bno']);
                return;
            }
        }
        this.setState({
            msgType: 'danger',
            msg: '[' + barcode + ']非此申請單內之品項!',
        });
    }

    checkSuccess(bno) {
        let item_d = this.state.item_d;
        let item_e = this.state.item_e;
        let count = 0;
        let count_y = 0;
        for (let i = 0; i < item_e.length; i++) {
            if (item_e[i]['bno'] === bno) {
                if (item_e[i]['status'] === 'Y') {
                    count_y++;
                }
                count++;
            }
        }
        if (count === count_y) {
            for (let i = 0; i < item_d.length; i++) {
                if (item_d[i]['bno'] === bno) {
                    item_d[i]['status'] = 'Y';
                    this.setState({
                        item_d: item_d,
                        msgType: 'success',
                        msg: '批號[' + item_d[i]['bno'] + ']已全部領用'
                        
                    });
                    this.checkPosting();
                }
            }
        }       
    }

    checkPosting() {
        let item_m = this.state.item_m;
        let item_d = this.state.item_d;
        let total = 0;
        let total_y = 0;
        for (let i = 0; i < item_d.length; i++) {
            if (item_d[i]['status'] === 'Y') {
                total_y++;
            }
            total++;
        }
        if (total === total_y) {
            let item_m =  this.state.item_m;
            item_m['status'] = 'R'
            this.setState({
                item_m: item_m,
                barcode: '',
                posting: true,
                msgType: 'success',
                msg: '申請單號[' + item_m['no'] + ']已領用完畢，確認後即可過帳!'
            });
        }
    }

    goPosting() {
        if(confirm('您確定要領料過帳嗎？')) {
            let self = this;      
            let no = this.state.item_m['no'];
            let form_data = new FormData();
            form_data.append('no', no);
            axios.post('/api/web/mpe/qa/receive/posting', form_data)
            .then(function (response) {
                if (response.data.result) {
                    let { lsa_m, lsa_d, lsa_e } = self.state; 
                    lsa_m = self.removeItem(no, lsa_m, 'no');
                    lsa_d = self.removeItem(no, lsa_d, 'lsa_no');
                    lsa_e = self.removeItem(no, lsa_e, 'lsa_no');
                    self.setState({
                        lsa_m: lsa_m,
                        lsa_d: lsa_d,
                        lsa_e: lsa_e,    
                    });
                    alert('已完成領料過帳!');
                    self.goList();
                    console.log(response.data);
                } else {
                    console.log(response.data);
                }
            }).catch(function (error) {
                console.log(error);
            });
        }
    }

    removeItem(no, list, pk) {
        let seq = 0;
        for (let i = 0; i < list.length; i++) {
            if (list[seq][pk] === no) {
                list.splice(seq, 1);
            } else {
                seq++;
            }
        }
        return list;
    }


    goList() {
        this.setState({
            item_m: [], item_d: [], item_e: [],
            barcode: '',
            posting: false,
            showReceive: false,
            msg: '',
            msgType: '',
        });
    }

    render() { 
        const { showReceive, posting, msg, msgType, item_m, search} = this.state;
        const list = search ? this.state.search_m : this.state.lsa_m;
        return(   
            <div>
                {showReceive ? 
                    <div>
                        <Panel style={{marginBottom: '10px'}}> 
                            <Col sm={10} md={10}>
                                <ButtonToolbar >
                                    <Button onClick={this.goList.bind(this)}>&larr; 回清單頁</Button>
                                </ButtonToolbar>
                            </Col>
                            <Col sm={2} md={2} >
                                <ButtonToolbar >
                                    <Button bsStyle={posting ? "success" : "warning"} disabled={!posting} onClick={this.goPosting.bind(this)}>領料過帳</Button>
                                </ButtonToolbar>
                            </Col>
                        </Panel> 
                        <Panel style={{marginBottom: '10px'}}>
                            <Col sm={3} md={3}>
                                    <input 
                                        type="text" 
                                        className="form-control" 
                                        disabled={posting}
                                        value={this.state.barcode}
                                        autoFocus
                                        maxLength={8}
                                        placeholder="掃描條碼"
                                        onChange={this.barcodeChange.bind(this)}
                                    />
                            </Col>
                            <Col sm={9} md={9}>
                                {msg && 
                                    <div className={"alert alert-" + msgType} style={{padding: '6px', marginBottom: '0px'}}>
                                        {msg}
                                    </div>
                                }
                            </Col>
                        </Panel>
                        <Table bordered style={{marginBottom: '10px'}}> 
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
                        </Table>
                        <div style={{height: '730px', overflow: 'auto'}}>
                            {this.state.item_d.map((item_d,index_d) => (
                                <table className="table table-bordered table-hover" style={{marginBottom: '10px'}} key={index_d}>
                                    <thead>
                                        <tr className={item_d.status === 'N' ? "info" : "success"}>
                                            <th width="80">料號</th><th width="120">{item_d.partno}</th>
                                            <th width="80">批號</th><th width="160">{item_d.bno}</th>
                                            <th width="90">品名</th><th>{item_d.pname}</th>
                                        </tr>
                                        <tr className={item_d.status === 'N' ? "info" : "success"}>
                                            <th>倉庫</th><th>{item_d.whouse}</th>
                                            <th>儲位</th><th>{item_d.stor}</th>
                                            <th>申請數量</th><th>{item_d.qty + item_d.unit}</th>
                                        </tr>
                                    </thead>
                                    {item_d.status === 'N' &&
                                        <tbody>
                                            {this.state.item_e.map((item_e, index_e) => (
                                                item_d.bno === item_e.bno &&
                                                <tr key={index_e} className={item_e.status === "Y" ? "success" : "default"}>
                                                    <td>條碼編號</td><td>{item_e.barcode}</td>
                                                    <td>剩餘量</td><td>{item_e.amt + item_e.unit}</td>
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
                        <Panel style={{marginBottom: '10px'}}>
                            <Col bsClass="row">
                                <Col sm={7} md={9}>
                                    <ButtonToolbar >
                                    </ButtonToolbar>
                                </Col>
                                <Col sm={5} md={3}>
                                    <div className="input-group">
                                        <input 
                                            type="text" 
                                            className="form-control" 
                                            maxLength={9}
                                            value={this.state.search_str}
                                            onChange={this.searchChange.bind(this)}/>
                                        <span className="input-group-btn">
                                            {search && <button className="btn btn-danger" onClick={this.cancelSearch.bind(this)}>取消</button>}
                                            <button className="btn btn-default" onClick={this.onSearch.bind(this)}>查詢</button>
                                        </span>
                                    </div>
                                </Col>
                            </Col> 
                        </Panel> 
                        {list.length > 0 && 
                            <Table bordered hover>
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
                                                <Button 
                                                    bsStyle="primary" 
                                                    bsSize="small"
                                                    onClick={this.goReceive.bind(this, item.no)}
                                                >
                                                    領用
                                                </Button>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </Table>
                        }
                    </div>
                }    
            </div>
        );
    };
}