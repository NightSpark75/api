/** 
 * ReceiveList.js
 */
import React from 'react';
import { Link } from 'react-router';
import axios from 'axios';
import { Button, ButtonToolbar, Table, Panel, Pager, FormControl, Alert, Col, ListGroup, ListGroupItem } from "react-bootstrap";
import Posting from './ReceivePosting';

export default class ReceiveList extends React.Component{
    constructor(props) {
        super(props);

        this.state = {
            lsa_m: [], search_m: [], lsa_d: [], lsa_e: [],
            item_m: [], item_d: [], item_e: [],
            search: false, search_str: '',
            barcode: '',
            posting: false,
            showReceive: false,
        }
    }

    componentDidMount() {
        this.init();
    }

    init() {
        let self = this;       
        axios.get('/api/web/mpe/qa/receive/list', null, {
            method: 'get',
        }).then(function (response) {
            if (response.data.result) {
                self.setState({
                    lsa_m: response.data.lsa_m,
                    lsa_d: response.data.lsa_d,
                    lsa_e: response.data.lsa_e,
                });
                console.log(response.data);
            } else {
                console.log(response.data);
                window.location = '/web/login/ppm';
            }
        }).catch(function (error) {
            console.log(error);
        });
    }

    searchChange(e) {
        this.setState({search_str: e.target.value});
    }

    onSearch() {

    }

    goReceive(lsa_no, e) {
        let item_m = this.setSelect(lsa_no, this.state.lsa_m, 'no');
        let item_d = this.setSelect(lsa_no, this.state.lsa_d, 'lsa_no');
        let item_e = this.setSelect(lsa_no, this.state.lsa_e, 'lsa_no');        
        this.setState({
            item_m: item_m, 
            item_d: item_d, 
            item_e: item_e,
            showReceive: true,
        });
    }

    setSelect(lsa_no, list, id) {
        let item = [];
        for (let i = 0; i < list.length; i++) {
            if (list[i][id] === lsa_no) {
                item = list[i];
            }
        }
        return item;
    }

    barcodeChange(e) {
        this.setState({barcode: e.target.value});
    }

    goList() {
        this.setState({showReceive: false});
    }

    render() { 
        const list = this.state.search > 0 ? this.state.search_m : this.state.lsa_m;
        const { showReceive, posting, msg, item_m} = this.state;
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
                                    <Button bsStyle="warning" disabled={!posting} onClick={this.goList.bind(this)}>領料過帳</Button>
                                </ButtonToolbar>
                            </Col>
                        </Panel> 
                        <Panel style={{marginBottom: '10px'}}>
                            <Col sm={2} md={2}>
                                <input 
                                    type="text" 
                                    className="form-control" 
                                    value={this.state.barcode}
                                    placeholder="掃描條碼"
                                    onChange={this.barcodeChange.bind(this)}
                                />
                            </Col>
                            <Col sm={10} md={10}>
                                {msg && <strong>msg</strong>}
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
                        <div style={{height: '670px', overflow: 'auto'}}>
                            {this.state.lsa_d.map((item_d,index_d) => (
                                <table className="table table-bordered table-hover" style={{marginBottom: '10px'}} key={index_d}>
                                    <thead>
                                        <tr className="info">
                                            <th width="80">料號</th><th width="120">{item_d.partno}</th>
                                            <th width="80">批號</th><th width="160">{item_d.bno}</th>
                                            <th width="90">品名</th><th>{item_d.pname}</th>
                                        </tr>
                                        <tr className="info">
                                            <th>倉庫</th><th>{item_d.whouse}</th>
                                            <th>儲位</th><th>{item_d.stor}</th>
                                            <th>申請數量</th><th>{item_d.qty + item_d.unit}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {this.state.lsa_e.map((item_e, index_e) => (
                                            item_d.bno === item_e.bno &&
                                            <tr key={index_e} className={item_e.status === "Y" ? "success" : "default"}>
                                                <td>條碼編號</td><td>{item_e.barcode}</td>
                                                <td>剩餘量</td><td>{item_e.amt + item_e.unit}</td>
                                                <td>瓶身單位</td><td>{item_e.usize + item_e.unit}</td>
                                            </tr> 
                                        ))}
                                    </tbody>
                                </table>
                            ))}
                        </div>
                    </div>
                :
                    <div>
                        <Panel style={{marginBottom: '10px'}}>
                            <Col bsClass="row">
                                <Col sm={9} md={9}>
                                    <ButtonToolbar >
                                        <Button bsStyle="primary">Small button</Button>
                                    </ButtonToolbar>
                                </Col>
                                <Col sm={3} md={3}>
                                    <div className="input-group">
                                        <input 
                                            type="text" 
                                            className="form-control" 
                                            value={this.state.search_str}
                                            onChange={this.searchChange.bind(this)}/>
                                        <span className="input-group-btn">
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
                                    {this.state.lsa_m.map((item, index) => (
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