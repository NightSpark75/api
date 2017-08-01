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
            list: []
        }
    }

    componentDidMount() {
        this.init();
    }

    init() {
        let self = this;       
        axios.get('/api/web/mpe/qa/retained/list')
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
    
    goMenu() {
        window.location = '/auth/web/menu';
    }

    render() { 
        const list = this.state.list;
        return(   
            <div>
                <Panel style={{marginBottom: '10px'}}> 
                    <Col sm={6} md={6}>
                        <ButtonToolbar >
                            <Button onClick={this.goMenu.bind(this)}>&larr; 功能選單</Button>
                        </ButtonToolbar>
                    </Col>
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