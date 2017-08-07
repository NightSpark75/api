/** 
 * Job.js
 */
import React from 'react';
import { Link } from 'react-router';
import axios from 'axios';
import { Button, ButtonToolbar, Table, Panel, Alert, Col } from "react-bootstrap";

export default class Job extends React.Component{
    constructor(props) {
        super(props);

        this.state = {
            ready: false,
            job_list: [],
            showInfo: false,
            item: [],
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

    showProcessInfo(item) {
        this.setState({
            showInfo: true, 
            item: item,
        });
    }

    hideProcessInfo() {
        this.setState({
            showInfo: false,
            item: [],
        })
    }

    render() {
        const { job_list } = this.state; 
        return(   
            <div>
                <Panel style={{marginBottom: '10px'}}> 
                    <Col sm={10} md={10}>
                        <ButtonToolbar >
                            <Link className="btn btn-default" to="/auth/web/menu">&larr; 功能選單</Link>
                            {/*}
                            <Button bsStyle="primary" className="pull-right"

                            >結束且完工(無清潔)</Button>
                            <Button bsStyle="primary" className="pull-right"
                                
                            >結束且完工(清潔)</Button>
                            */}
                        </ButtonToolbar>
                    </Col>
                </Panel> 
                {this.state.showInfo &&  
                    <Alert bsStyle="info" onDismiss={this.hideProcessInfo.bind(this)} style={{marginBottom: '10px'}}>
                        <h4>製程單號{this.state.item.sno}詳細資訊</h4>
                        <p>{this.state.item.info}</p>
                    </Alert>
                }
                {job_list.length > 0 ?
                    <Table bordered hover>
                        <thead>
                            <tr>
                                <th width="65.56"></th>
                                <th>製程單號</th>
                                <th>批號</th>
                                <th>順序</th>
                                <th>途程名稱</th>
                                <th>設備編號</th>
                                <th>工作室名稱</th>
                                <th width="92.22"></th>
                                <th width="65.56"></th>
                            </tr>
                        </thead>
                        <tbody>
                            {job_list.map((item, index) => (
                                <tr key={index}>
                                    <td>
                                        <Button bsSize="small" onClick={this.showProcessInfo.bind(this, item)}>詳細資訊</Button>
                                    </td>
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