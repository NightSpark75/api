/** 
 * Coa.js
 */
import React from 'react';
import ReactDOM from "react-dom";
import { Link } from 'react-router';
import axios from 'axios';
import { Button, ButtonToolbar, Table, Panel, Pager, FormControl, Alert, Col, ListGroup, ListGroupItem } from "react-bootstrap";

export default class Search extends React.Component{
    constructor(props) {
        super(props);

        this.state = {
            barcode: '',
            partno: '',
            batch: '',
            info: {},
        }
    }

    goSearch() {
        let { partno, batch} = this.state;
        if (partno.length > 0) {
            let form_data = new FormData();
            form_data.append('partno', partno);
            let url = '/api/web/mpe/qc/doc/partno';
            this.getInfo(url, form_data);
        } 
        if (batch.length > 0) {
            let form_data = new FormData();
            form_data.append('batch', batch);
            let url = '/api/web/mpe/qc/doc/batch';
            this.getInfo(url, form_data);
        }
    }

    barcodeChange(e) {
        this.setState({
            barcode: e.target.value,
            partno: '', 
            batch: '',
            info: {},
        });

        if (e.target.value.length === 8) {
            let form_data = new FormData();
            form_data.append('barcode', e.target.value);
            let url = '/api/web/mpe/qc/doc/barcode';
            this.getInfo(url, form_data);
        }
    }

    partnoChange(e) {
        this.setState({
            barcode: '',
            partno: e.target.value,
            batch: '',
            info: {},
        });
    }

    batchChange(e) {
        this.setState({
            barcode: '',
            partno: '',
            batch: e.target.value,
            info: {},
        });
    }

    getInfo(url, data) {
        let self = this;
        axios.post(url, data)
        .then(function (response) {
            if (response.data.result) {
                self.setState({
                    info: response.data.info,
                });
                console.log(response.data);
            } else {
                console.log(response.data);
            }
        }).catch(function (error) {
            console.log(error);
        });
    }

    render() { 
        const { barcode, partno, batch, info } = this.state;
        const marginBottom = {marginBottom: '10px'};
        return(   
            <div>
                <Panel style={marginBottom}> 
                    <div className="row" style={marginBottom}>
                        <div className="col-md-4 col-sm-4 col-xs-6">
                            <input type="text" className="form-control input-lg" 
                                value={this.state.barcode}
                                placeholder="掃描條碼"
                                autoFocus
                                onChange={this.barcodeChange.bind(this)}
                            />
                        </div>
                    </div>
                    <div className="row" style={marginBottom}>
                        <div className="col-md-4 col-sm-4 col-xs-6">
                            <input type="text" className="form-control input-lg" 
                                value={this.state.partno}
                                placeholder="輸入料號"
                                onChange={this.partnoChange.bind(this)}
                            />
                        </div>
                    </div>
                    <div className="row">
                        <div className="col-md-4 col-sm-4 col-xs-6">
                            <input type="text" className="form-control input-lg" 
                                value={this.state.batch}
                                placeholder="輸入批號"
                                onChange={this.batchChange.bind(this)}
                            />
                        </div>
                        <div className="col-md-8 col-sm-8 col-xs-6">
                            <button className="btn btn-primary btn-lg"
                                onClick={this.goSearch.bind(this)}
                            >查詢</button>
                        </div>
                    </div>
                </Panel> 
                {info.partno !== undefined &&
                    <Panel>
                        <div>
                            <Table bordered>
                                <tbody>
                                    <tr>
                                        <td className="col-md-2 col-sm-2 col-xs-2">料號</td>
                                        <td className="col-md-4 col-sm-4 col-xs-4">{ info.partno }</td>
                                        <td className="col-md-2 col-sm-2 col-xs-2">批號</td>
                                        <td className="col-md-4 col-sm-4 col-xs-4">{ info.batch }</td>
                                    </tr>
                                    <tr>
                                        <td>中文名稱</td><td colSpan={3}>{ info.pname }</td>
                                    </tr>
                                    <tr>
                                        <td>英文名稱</td><td colSpan={3}>{ info.ename }</td>
                                    </tr>
                                </tbody>
                            </Table>
                            <div className="row">
                                <div className="col-md-6 col-sm-6 col-xs-6">
                                    {info.sds_no === null ?
                                        <button className="btn btn-default btn-lg btn-primary disabled">SDS文件</button>
                                    :
                                        <a className="btn btn-default btn-lg btn-primary"
                                            href={"/api/web/mpe/qc/doc/read/sds/" + info.partno + "/N/" + info.sds_no}
                                            target="_blank"
                                        >SDS文件</a>
                                    }
                                </div>
                                <div className="col-md-6 col-sm-6 col-xs-6">
                                    {info.coa_no === null ?
                                        <button className="btn btn-default btn-lg btn-primary disabled">COA文件</button>
                                    :
                                        <a className="btn btn-default btn-lg btn-primary"
                                            href={"/api/web/mpe/qc/doc/read/coa/" + info.partno + "/" + info.batch + "/" + info.coa_no}
                                            target="_blank"
                                        >COA文件</a>
                                    }
                                </div>
                            </div>
                        </div>
                    </Panel>
                }
            </div>
        )
    }
}