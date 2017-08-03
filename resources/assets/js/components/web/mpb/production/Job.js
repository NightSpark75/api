/** 
 * Job.js
 */
import React from 'react';
import { Link } from 'react-router';
import axios from 'axios';
import { Button, ButtonToolbar, Table, Panel, Pager, FormControl, Alert, Col, ListGroup, ListGroupItem } from "react-bootstrap";

export default class Job extends React.Component{
    constructor(props) {
        super(props);

        this.state = {
            ready: false,
            job_list: [],
        }
    }
    
    componentDidMount() {
        this.init();
        this.timer = setInterval(this.updateJobList.bind(this), 5000);
    }

    componentWillUnmount() {
        this.timer && clearInterval(this.timer);
    }

    init() {
        this.getJobList();
    }

    getJobList() {
        let self = this;       
        axios.get('/api/web/mpb/prod/job')
        .then(function (response) {
            if (response.data.result) {
                self.setState({
                    job_list: response.data.job_list,
                });
                console.log(response.data);
            } else {
                console.log(response.data);
            }
        }).catch(function (error) {
            console.log(error);
        });
    }

    updateJobList() {
        if (this.state.job_list.length > 0) {
            let self = this;
            let job_list = JSON.stringify(this.state.job_list);
            let form_data = new FormData();
            form_data.append('job_list', job_list);
            axios.post('/api/web/mpb/prod/compare', form_data)
            .then(function (response) {
                if (response.data.result) {
                    self.setState({
                        job_list: response.data.job_list,
                    });
                    console.log(response.data);
                } else {
                    console.log(response.data);
                }
            }).catch(function (error) {
                console.log(error);
            });
        }
    }


    render() {
        const { job_list } = this.state; 
        return(   
            <div>
                <Panel style={{marginBottom: '10px'}}> 
                    <Col sm={10} md={10}>
                        <ButtonToolbar >
                            <Link className="btn btn-default" to="/auth/web/menu">&larr; 功能選單</Link>
                        </ButtonToolbar>
                    </Col>
                </Panel> 
                {job_list.length > 0 ?
                    <Table bordered hover>
                        <thead>
                            <tr>
                                <th>製程單號</th>
                                <th>批號</th>
                                <th>順序</th>
                                <th>途程名稱</th>
                                <th>設備編號</th>
                                <th>工作室名稱</th>
                                <th width="92.22"></th>
                                <th width="92.22"></th>
                            </tr>
                        </thead>
                        <tbody>
                            {job_list.map((item, index) => (
                                <tr key={index}>
                                    <td>{item.sno}</td>
                                    <td>{item.bno}</td>
                                    <td>{item.psno}</td>
                                    <td>{item.pname}</td>
                                    <td>{item.mno}</td>
                                    <td>{item.rname}</td>
                                    <td>
                                        <Button bsStyle="primary" bsSize="small"
                                        >
                                            料號確認
                                        </Button>
                                    </td>
                                    <td>
                                        <Link className="btn btn-primary btn-sm" 
                                            to={"/auth/web/mpb/prod/working/" + item.sno + "/" + item.psno}>報工</Link>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </Table>
                :
                    <Alert bsStyle="warning">
                        <strong>查無資料!</strong>目前尚無生產資訊...
                    </Alert>
                }
            </div>
        )
    }
}