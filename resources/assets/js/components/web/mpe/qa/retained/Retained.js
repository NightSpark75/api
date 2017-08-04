/** 
 * Retained.js
 */
import React from 'react';
import ReactDOM from "react-dom";
import { Link } from 'react-router';
import axios from 'axios';
import { Button, ButtonToolbar, Table, Panel, Pager, FormControl, Alert, Col, ListGroup, ListGroupItem } from "react-bootstrap";

export default class Retained extends React.Component{
    constructor(props) {
        super(props);

        this.state = {
            list: [],
            ldate: 999999,
        }
    }

    componentDidMount() {
        let today = new Date();
        var yyyy = today.toLocaleDateString().slice(0,4)
        var MM = (today.getMonth()+1<10 ? '0' : '')+(today.getMonth()+1);
        var dd = (today.getDate()<10 ? '0' : '')+today.getDate();
        let ldate =  yyyy+MM+dd;
        this.setState({ldate: ldate});
        this.init(ldate);
    }

    init(ldate) {
        let self = this;       
        axios.get('/api/web/mpe/qa/retained/list/' + ldate)
        .then(function (response) {
            if (response.data.result) {
                self.setState({
                    list: response.data.list,
                });
                console.log(response.data);
            } else {
                console.log(response.data);
            }
        }).catch(function (error) {
            console.log(error);
        });
    }

    ldateChange(e) {
        let ldate  = e.target.value;
        this.setState({ldate: ldate});
        if (ldate.length === 8) {
            this.init(ldate);
        }
    }

    render() { 
        const { list, ldate } = this.state;
        return(   
            <div>
                <Panel style={{marginBottom: '10px'}}> 
                    <ButtonToolbar >
                        <Link className="btn btn-default" to="/auth/web/menu">&larr; 功能選單</Link>
                        <div className="pull-right col-lg-2 col-md-2 col-sm-3 col-xs-4">
                            <input type="text" className="form-control" id="ldate" value={ldate}
                                maxLength={8}
                                onChange={this.ldateChange.bind(this)}
                            />
                        </div>  
                        <div className="pull-right text-right">
                            <label className="control-label" style={{margin: '6px 0 6px 0'}}>留樣日期</label>
                        </div>
                    </ButtonToolbar>
                </Panel> 
                {list.length > 0 ?
                    <Table bordered hover>
                        <thead>
                            <tr>
                                <th>
                                    分支
                                </th>
                                <th>
                                    料號
                                </th>
                                <th>
                                    名稱
                                </th>
                                <th>
                                    批號
                                </th>
                                <th>
                                    抽樣日
                                </th>
                                <th>
                                    QA留樣量
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            {list.map((item, index) => (
                                <tr key={index}>
                                    <td>
                                        {item.irmcu}
                                    </td>
                                    <td>
                                        {item.irlitm}
                                    </td>
                                    <td>
                                        {item.irdsc1}
                                    </td>
                                    <td>
                                        {item.irlotn}
                                    </td>
                                    <td>
                                        {item.iratdt}
                                    </td>
                                    <td>
                                        {item.irsq03 + item.iruom3}
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </Table>
                :
                    <Alert bsStyle="warning">
                        <strong>查無資料!</strong>今日尚無留樣品資訊...
                    </Alert>
                }
            </div>
        );
    };
}