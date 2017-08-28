/** 
 * Search.js
 */
import React from 'react';
import { Link } from 'react-router';
import axios from 'axios';

export default class Search extends React.Component{
    constructor(props) {
        super(props);

        this.state = {
            barcode: '',
            partno: '',
            batch: '',
            info: {},
            message: '',
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
                    message: '',
                });
                console.log(response.data);
            } else {
                self.setState({message: '查詢不到資料!!'})
                console.log(response.data);
            }
        }).catch(function (error) {
            console.log(error);
        });
    }

    render() { 
        const { barcode, partno, batch, info, message } = this.state;
        const marginBottom = {marginBottom: '10px'};
        return(   
            <div>
                <div className="box"> 
                    <div className="column">
                        <input type="text" className="input is-medium" 
                            value={this.state.barcode}
                            placeholder="掃描條碼"
                            maxLength={8}
                            autoFocus
                            onChange={this.barcodeChange.bind(this)}
                        />
                    </div>
                    <div className="column">
                        <input type="text" className="input is-medium" 
                            value={this.state.partno}
                            maxLength={20}
                            placeholder="輸入料號"
                            onChange={this.partnoChange.bind(this)}
                        />
                    </div>
                    <div className="column">
                        <input type="text" className="input is-medium" 
                            value={this.state.batch}
                            maxLength={20}
                            placeholder="輸入批號"
                            onChange={this.batchChange.bind(this)}
                        />
                    </div>
                    <div className="column">
                        <button className="button is-primary is-medium"
                            onClick={this.goSearch.bind(this)}
                        >查詢</button>
                    </div>
                </div> 
                {info.partno !== undefined &&
                    <div className="box">
                        <div>
                            <table className="table is-bordered is-fullwidth">
                                <tbody>
                                    <tr>
                                        <td width="100">料號</td>
                                        <td>{ info.partno }</td>
                                        <td width="100">批號</td>
                                        <td>{ info.batch }</td>
                                    </tr>
                                    <tr>
                                        <td>中文名稱</td><td colSpan={3}>{ info.pname }</td>
                                    </tr>
                                    <tr>
                                        <td>英文名稱</td><td colSpan={3}>{ info.ename }</td>
                                    </tr>
                                </tbody>
                            </table>
                            <div className="columns">
                                <div className="column is-4">
                                    {info.sds_no === null ?
                                        <button className="button is-large is-primary is-static">SDS文件</button>
                                    :
                                        <a className="button is-large is-primary"
                                            href={"/api/web/mpe/qc/doc/read/sds/" + info.partno + "/N/" + info.sds_no}
                                            target="_blank"
                                        >SDS文件</a>
                                    }
                                </div>
                                <div className="column is-4">
                                    {info.coa_no === null ?
                                        <button className="button is-large is-primary is-static">COA文件</button>
                                    :
                                        <a className="button is-large is-primary"
                                            href={"/api/web/mpe/qc/doc/read/coa/" + info.partno + "/" + info.batch + "/" + info.coa_no}
                                            target="_blank"
                                        >COA文件</a>
                                    }
                                </div>
                            </div>
                        </div>
                    </div>
                }
                {message.length > 0 &&
                    <Alert bsStyle="warning">
                        <strong>{message}</strong>
                    </Alert>
                }
            </div>
        )
    }
}