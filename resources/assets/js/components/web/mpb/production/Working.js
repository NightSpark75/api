/** 
 * Working.js
 */
import React from 'react';
import { Link } from 'react-router';
import axios from 'axios';
import { Button, ButtonToolbar, Table, Panel, Pager, FormControl, Alert, Col, ListGroup, ListGroupItem } from "react-bootstrap";

export default class Job extends React.Component{
    constructor(props) {
        super(props);

        this.state = {
            sno: this.props.params.sno,
            psno: this.props.params.psno,
            waiting_list: [],
            working_list: [],
        }
        
    }
    
    componentDidMount() {
        this.getMember();
    }

    componentWillUnmount() {

    }

    getMember() {
        const { sno, psno } = this.state;
        let self = this;       
        axios.get('/api/web/mpb/prod/member/' + sno + '/' + psno)
        .then(function (response) {
            if (response.data.result) {
                self.setState({
                    waiting_list: response.data.waiting,
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
        let ready = this.state.ready;
        if (ready) {
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
        const buttonStyle = {margin: '0px 0px 10px 0px'}
        const buttonClass = "col-xs-12 col-sm-6 col-md-6 col-lg-6";
        return(   
            <div>
                <Panel style={{marginBottom: '10px'}}> 
                    <Col>
                        <ButtonToolbar >
                            <Link className="btn btn-default" to="/auth/web/mpb/prod/list">&larr; 回生產清單</Link>
                            <Button bsStyle="success">整批工作</Button>
                            <Button bsStyle="info">整批退出</Button>
                            <Button bsStyle="primary" className="pull-right">結束且完工(無清潔)</Button>
                            <Button bsStyle="primary" className="pull-right">結束且完工(清潔)</Button>
                        </ButtonToolbar>
                    </Col>
                </Panel>
                <div className="row">
                    <Col lg={6} md={6} sm={6}>
                        <Panel header="待派工生產人員" bsStyle="info" style={{height: '500px'}}>
                            {this.state.waiting_list.map((item, index) => (
                                <div className={buttonClass} style={buttonStyle} key={index}>
                                    <button className="btn btn-primary btn-block">
                                        {item.empno + ' ' + item.ename}
                                    </button>
                                </div>
                            ))}
                        </Panel>
                    </Col>
                    <Col lg={6} md={6} sm={6}>
                        <Panel header="目前生產人員" bsStyle="success" style={{height: '500px'}}>
                            <div className={buttonClass} style={buttonStyle}>
                                <button className="btn btn-success btn-block">
                                    123
                                </button>
                            </div>
                        </Panel>
                    </Col>
                </div>
            </div>
        )
    }
}