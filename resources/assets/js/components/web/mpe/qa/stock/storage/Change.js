/** 
 * Change.js
 */
import React from 'react';
import ReactDOM from "react-dom";
import { Link } from 'react-router';
import axios from 'axios';
import { Button, ButtonToolbar, Table, Panel, Pager, FormControl, Alert, Col, ListGroup, ListGroupItem, Modal } from "react-bootstrap";

export default class Change extends React.Component{
    constructor(props) {
        super(props);

        this.state = {
            list: [],
            storage: [],
            search: [],
            search_str: '',
            searching: false,
            select_item: [],
            storageShow: false,
            isLoading: false,
        }
    }

    componentDidMount() {
        this.init();
    }

    init() {
        let self = this;       
        axios.get('/api/web/mpe/qa/stock/list')
        .then(function (response) {
            if (response.data.result) {
                self.setState({
                    list: response.data.list,
                    storage: response.data.storage,
                });
                console.log(response.data);
            } else {
                console.log(response.data);
            }
        }).catch(function (error) {
            console.log(error);
        });
    }

    onSearch() {
        let str = this.state.search_str;
        if (str === '') {
            return null;
        }
        let self = this;       
        axios.get('/api/web/mpe/qa/stock/list/' + str)
        .then(function (response) {
            if (response.data.result) {
                self.setState({
                    search: response.data.list,
                    searching: true,
                });
                console.log(response.data);
            } else {
                console.log(response.data);
            }
        }).catch(function (error) {
            console.log(error);
        });

        this.setState({search: this.state.list});
    }

    searchChange(event) {
        this.setState({search_str: event.target.value});
    }

    cancelSearch() {
        this.setState({
            search: [],
            search_str: '',
            searching: false,
        });
    }

    showStorage(item) {
        this.setState({
            select_item: item,
            storageShow: true,
        });
    }

    hideStorage() {
        this.setState({
            select_item: [],
            storageShow: false,
        });
    }

    setList(data) {
        let list  = this.state.list;
        let newlist = [];
        for (let i = 0; i < list.length; i++) {
            newlist[i] = list[i];
            if ((list[i]['partno'] === data.partno) 
                && (list[i]['batch'] === data.batch)) {
                newlist[i] = {
                    partno: data.partno,
                    batch: data.batch,
                    pname: list[i]['pname'],
                    stor: data.stor,
                    whouse: data.whouse,    
                };
            }
        }
        this.setState({list: newlist});
    }

    onChange(item) {
        let self = this;
        let data = {
            partno: this.state.select_item.partno,
            batch: this.state.select_item.batch,
            whouse: item.whouse,
            stor: item.stor,
        };
        axios.put('/api/web/mpe/qa/stock/storage/change', data)
        .then(function (response) {
            if (response.data.result) {
                self.setList(data);
                self.hideStorage();
            } else {
                console.log(response.data);
            }
            self.setState({isLoading: false});
        }).catch(function (error) {
            console.log(error);
            self.setState({isLoading: false});
        });
    }

    goMenu() {
        window.location = '/auth/web/menu';
    }

    render() { 
        const { list, search, search_str, searching, storageShow, storage, isLoading } = this.state;
        let content = searching ? search : list; 
        return(   
            <div>
                <Panel style={{marginBottom: '10px'}}> 
                    <Col sm={6} md={6}>
                        <ButtonToolbar >
                            <Button onClick={this.goMenu.bind(this)}>&larr; 回清單頁</Button>
                        </ButtonToolbar>
                    </Col>
                    <Col sm={6} md={6}>
                        <div className="input-group">
                            <input 
                                type="text" 
                                className="form-control" 
                                maxLength={30}
                                value={this.state.search_str}
                                onChange={this.searchChange.bind(this)}/>
                            <span className="input-group-btn">
                                {searching && <button className="btn btn-danger" onClick={this.cancelSearch.bind(this)}>取消</button>}
                                <button className="btn btn-default" onClick={this.onSearch.bind(this)}>查詢</button>
                            </span>
                        </div>
                    </Col>
                </Panel> 
                {content.length > 0 ?
                    <div>
                        <Table bordered hover>
                            <thead>
                                <tr>

                                    <th>
                                        料號
                                    </th>
                                    <th>
                                        批號
                                    </th>
                                    <th>
                                        品名
                                    </th>
                                    <th>
                                        倉庫
                                    </th>
                                    <th>
                                        儲位
                                    </th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                {content.map((item, index) => (
                                    <tr key={index}>
                                        <td>
                                            {item.partno}
                                        </td>
                                        <td>
                                            {item.batch}
                                        </td>
                                        <td>
                                            {item.pname}
                                        </td>
                                        <td>
                                            {item.whouse}
                                        </td>
                                        <td>
                                            {item.stor}
                                        </td>
                                        <td width="92.22">
                                            <Button bsStyle="primary" bsSize="small" onClick={this.showStorage.bind(this, item)}>變更儲位</Button>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </Table>
                        <Modal show={storageShow} onHide={this.hideStorage.bind(this)} backdrop="static">
                            <Modal.Header closeButton>
                                <Modal.Title>編輯使用者</Modal.Title>
                            </Modal.Header>
                            <Modal.Body>
                                <Table bordered hover>
                                    <thead>
                                        <tr>
                                            <th>
                                                倉庫
                                            </th>
                                            <th>
                                                倉庫名稱
                                            </th>
                                            <th>
                                                儲位
                                            </th>
                                            <th>
                                                儲位名稱
                                            </th>
                                            <th width="92.22"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {storage.map((item, index) => (
                                            <tr key={index}>
                                                <td>
                                                    {item.whouse}
                                                </td>
                                                <td>
                                                    {item.posit}
                                                </td>
                                                <td>
                                                    {item.stor}
                                                </td>
                                                <td>
                                                    {item.storn}
                                                </td>
                                                <td>
                                                    <Button 
                                                        bsStyle="primary" 
                                                        disabled={isLoading}
                                                        onClick={!isLoading ? this.onChange.bind(this, item) : null}
                                                    >
                                                        {isLoading ? '變更儲位中...' : '變更'}
                                                    </Button>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </Table>
                            </Modal.Body>
                            <Modal.Footer>
                                <Button onClick={this.hideStorage.bind(this)}>關閉</Button>
                            </Modal.Footer>
                        </Modal>
                    </div>
                :
                    <Alert bsStyle="warning">
                        <strong>查無庫存資料...</strong>
                    </Alert>
                }
            </div>
        );
    };
}