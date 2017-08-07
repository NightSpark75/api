/** 
 * packing.Working.js
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
            updated: false,
        }
    }
    
    componentDidMount() {
        this.getMember();
        this.timer = setInterval(this.getMember.bind(this), 5000);
    }

    componentWillUnmount() {
        this.timer && clearInterval(this.timer);
    }

    getMember() {
        const { sno, psno } = this.state;
        let self = this;       
        axios.get('/api/web/mpb/prod/packing/member/' + sno + '/' + psno)
        .then(function (response) {
            if (response.data.result) {
                if (!self.state.updated) {
                    self.setState({
                        waiting_list: response.data.waiting,
                        working_list: response.data.working,
                    });
                } else {
                    self.setState({
                        updated: false,
                    })
                }
                console.log(response.data);
            } else {
                console.log(response.data);
            }
        }).catch(function (error) {
            console.log(error);
        });
    }

    updateWorking(empno, action, event) {
        const { waiting_list, working_list, sno, psno } = this.state;
        let self = this;    
        let form_data = new FormData();   
        form_data.append('sno', sno); 
        form_data.append('psno', psno);
        form_data.append('empno', empno);
        axios.post('/api/web/mpb/prod/packing/working/' + action, form_data)
        .then(function (response) {
            if (response.data.result) {
                console.log(response.data);
            } else {
                console.log(response.data);
            }
        }).catch(function (error) {
            console.log(error);
        });
        if (action === 'join') {
            self.updateList(working_list, waiting_list, action, empno);
        } else {
            self.updateList(waiting_list, working_list, action, empno);
        }
    }

    updateList(add, remove, action, empno) {
        for (let r = 0; r < remove.length; r++) {
            if (remove[r]['empno'] === empno) {
                add.push(remove[r]);
                remove.splice(r, 1);
                if (action === 'join') {
                    this.setState({
                        working_list: add,
                        waiting_list: remove,
                        updated: true,
                    });
                } else {
                    this.setState({
                        working_list: remove,
                        waiting_list: add,
                        updated: true,
                    });
                }
                return;
            }
        }
    }

    allUpdateList(action) {
        if (action === 'join') {
            let add = this.state.working_list;
            let remove = this.state.waiting_list;
            add = add.concat(remove);
            this.setState({
                working_list: add,
                waiting_list: [],
                updated: true,
            })
        } else {
            let add = this.state.waiting_list;
            let remove = this.state.working_list;
            add = add.concat(remove);
            this.setState({
                working_list: [],
                waiting_list: add,
                updated: true,
            })
        }
    }

    allUpdate(action, event) {
        if ((action === 'join' && this.state.waiting_list.length > 0) ||
            (action === 'leave' && this.state.working_list.length > 0)) {

            let self = this;
            let {sno, psno, working_list, waiting_list} = this.state;
            let form_data = new FormData();   
            form_data.append('sno', sno); 
            form_data.append('psno', psno);
            axios.post('/api/web/mpb/prod/packing/all/' + action, form_data)
            .then(function (response) {
                if (response.data.result) {
                    console.log(response.data);
                } else {
                    console.log(response.data);
                }
            }).catch(function (error) {
                console.log(error);
            });
            self.allUpdateList(action);
        }
    }

    workingComplete(clean, event) {
        let msg =  (clean === 'N') ? '按確定後, 該製程完工(無清潔)..' : '按確定後,該製程完工(清潔)..';
        if(confirm(msg)) {
            let self = this;
            let {sno, psno} = this.state;
            let form_data = new FormData();   
            form_data.append('sno', sno); 
            form_data.append('psno', psno);
            form_data.append('clean', clean);
            axios.post('/api/web/mpb/prod/packing/work/complete', form_data)
            .then(function (response) {
                if (response.data.result) {
                    console.log(response.data);
                    self.props.router.push('/auth/web/mpb/packing/list');
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
                            <Link className="btn btn-default" to="/auth/web/mpb/packing/list">&larr; 回生產清單</Link>
                            <Button bsStyle="success"
                                onClick={this.allUpdate.bind(this, 'join')}
                            >整批工作</Button>
                            <Button bsStyle="info"
                                onClick={this.allUpdate.bind(this, 'leave')}
                            >整批退出</Button>
                            <Button bsStyle="primary" className="pull-right"
                                onClick={this.workingComplete.bind(this, 'N')}
                            >結束且完工(無清潔)</Button>
                            <Button bsStyle="primary" className="pull-right"
                                onClick={this.workingComplete.bind(this, 'Y')}
                            >結束且完工(清潔)</Button>
                        </ButtonToolbar>
                    </Col>
                </Panel>
                <div className="row">
                    <Col lg={6} md={6} sm={6}>
                        <Panel header="待派工生產人員" bsStyle="info" style={{height: '500px'}}>
                            {this.state.waiting_list.map((item, index) => (
                                <div className={buttonClass} style={buttonStyle} key={index}>
                                    <button className="btn btn-info btn-block"
                                        onClick={this.updateWorking.bind(this, item.empno, 'join')}
                                    >
                                        {item.ename}
                                    </button>
                                </div>
                            ))}
                        </Panel>
                    </Col>
                    <Col lg={6} md={6} sm={6}>
                        <Panel header="目前生產人員" bsStyle="success" style={{height: '500px'}}>
                            {this.state.working_list.map((item, index) => (
                                <div className={buttonClass} style={buttonStyle} key={index}>
                                    <button className="btn btn-success btn-block"
                                        onClick={this.updateWorking.bind(this, item.empno, 'leave')}
                                    >
                                        {item.ename}
                                    </button>
                                </div>
                            ))}
                        </Panel>
                    </Col>
                </div>
            </div>
        )
    }
}