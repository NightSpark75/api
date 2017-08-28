/** 
 * package.Job.js
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
        axios.get('/api/web/mpb/prod/package/list')
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
        let self = this;
        let job_list = JSON.stringify(this.state.job_list);
        let form_data = new FormData();
        form_data.append('job_list', job_list);
        axios.post('/api/web/mpb/prod/package/compare', form_data)
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
                <div className="box" style={{ marginTop: '10px', marginBottom: '10px' }}>
                    <p className="control">
                        <Link className="button" to="/auth/web/menu">&larr; 功能選單</Link> 
                    </p>
                </div>
                {this.state.showInfo &&  
                    <article className="message is-info" style={{ marginBottom: '10px' }}>
                        <div className="message-header">
                            <p>製程單號{this.state.item.sno}詳細資訊</p>
                            <button className="delete" aria-label="delete" onClick={this.hideProcessInfo.bind(this)}></button>
                        </div>
                        <div className="message-body">
                            {this.state.item.info}
                        </div>
                    </article>
                }
                {job_list.length > 0 ?
                    <table className="table is-bordered is-fullwidth">
                        <thead>
                            <tr>
                                <th width="65.56"></th>
                                <th>製程單號</th>
                                <th>批號</th>
                                <th>順序</th>
                                <th>途程名稱</th>
                                <th>設備編號</th>
                                <th>工作室名稱</th>
                                <th width="65.56"></th>
                            </tr>
                        </thead>
                        <tbody>
                            {job_list.map((item, index) => (
                                <tr key={index}>
                                    <td>
                                        <button className="button" onClick={this.showProcessInfo.bind(this, item)}>詳細資訊</button>
                                    </td>
                                    <td>{item.sno}</td>
                                    <td>{item.bno}</td>
                                    <td>{item.psno}</td>
                                    <td>{item.pname}</td>
                                    <td>{item.mno}</td>
                                    <td>{item.rname}</td>
                                    <td>
                                        <Link className="button is-primary" 
                                            to={"/auth/web/mpb/package/working/" + item.sno + "/" + item.psno}>報工</Link>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                :
                    <div className="notification is-warning" style={{padding: '1rem 1rem 1rem 1rem'}}>
                        目前尚無生產資訊...
                    </div>
                }
            </div>
        )
    }
}