/** 
 * packing.Working.js
 */
import React from 'react';
import { Link } from 'react-router';
import axios from 'axios';

export default class Job extends React.Component{
    constructor(props) {
        super(props);

        this.state = {
            sno: this.props.params.sno,
            psno: this.props.params.psno,
            waiting_list: [],
            working_list: [],
            updated: false,
            lock: false,
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
        this.setState({lock: true}); 
        form_data.append('sno', sno); 
        form_data.append('psno', psno);
        form_data.append('empno', empno);
        axios.post('/api/web/mpb/prod/packing/working/' + action, form_data)
        .then(function (response) {
            if (response.data.result) {
                console.log(response.data);
                self.setState({ lock: false });
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
            
            this.setState({lock: true});
            let self = this;
            let {sno, psno, working_list, waiting_list} = this.state;
            let form_data = new FormData();   
            form_data.append('sno', sno); 
            form_data.append('psno', psno);
            axios.post('/api/web/mpb/prod/packing/all/' + action, form_data)
            .then(function (response) {
                if (response.data.result) {
                    console.log(response.data);
                    self.setState({ lock: false });
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
            this.setState({lock: true}); 
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
        const { job_list, lock } = this.state; 
        return(   
            <div>
                <div className="box" style={{ marginTop: '10px', marginBottom: '10px' }}>
                    <div className="level">
                        <div className="level-left">
                            <div className="level-item">
                                <Link className="button is-medium " to="/auth/web/mpb/packing/list">&larr; 回生產清單</Link>
                            </div>
                            <div className="level-item is-hidden-touch">
                                <button className="button is-primary is-large" onClick={this.allUpdate.bind(this, 'join')} disabled={ lock }>整批工作</button>
                            </div>
                            <div className="level-item is-hidden-touch">
                                <button className="button is-success is-large" onClick={this.allUpdate.bind(this, 'leave')} disabled={ lock }>整批退出</button>
                            </div>
                        </div>
                        <div className="level-right">
                            <div className="level-item is-hidden-touch">
                                <button className="button is-primary is-large" onClick={this.workingComplete.bind(this, 'N')} disabled={ lock }>結束且完工(無清潔)</button>
                            </div>
                            <div className="level-item">
                                <button className="button is-primary is-large" onClick={this.workingComplete.bind(this, 'Y')} disabled={ lock }>結束且完工(清潔)</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div className="column is-hidden-desktop">
                    <label className="is-size-4">請將畫面轉橫</label>
                </div>
                <div className="columns is-hidden-touch">
                    <div className="column">
                        <article className="message is-success">
                            <div className="message-header">
                                <h4 className="title is-3 has-text-white-ter">待派工生產人員</h4>
                            </div>
                            <div className="message-body"  style={{height: '450px'}}>
                                <div className="field is-grouped is-grouped-multiline">
                                    {this.state.waiting_list.map((item, index) => (
                                        <p className="control" key={index}>
                                            <button className="button is-success is-large" disabled={ lock }
                                                onClick={this.updateWorking.bind(this, item.empno, 'join')}
                                            >
                                                {item.ename}
                                            </button>
                                        </p>
                                    ))}
                                </div>
                            </div>
                        </article>
                    </div>
                    <div className="column">
                        <article className="message is-primary">
                            <div className="message-header">
                                <h4 className="title is-3 has-text-white-ter">目前生產人員</h4>
                            </div>
                            <div className="message-body" style={{height: '450px'}}>
                                <div className="field is-grouped is-grouped-multiline">
                                    {this.state.working_list.map((item, index) => (
                                        <p className="control" key={index}>
                                            <button className="button is-primary is-large" disabled={ lock }
                                                onClick={this.updateWorking.bind(this, item.empno, 'leave')}
                                            >
                                                {item.ename}
                                            </button>
                                        </p>
                                    ))}
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
            </div>
        )
    }
}