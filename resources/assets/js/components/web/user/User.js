/** 
 * User.js
 */
import React from 'react';
import { Link } from 'react-router';
import axios from 'axios';

export default class User extends React.Component{
    constructor(props) {
        super(props);

        this.state = {
            prg: [],
            user: []
        }
    }

    componentDidMount() {
        /*
        let self = this;
        let form_data = new FormData();
        form_data.append('file_id', file_id);
        axios.post('/api/web/initProgram', form_data, {
            method: 'post',
        }).then(function (response) {
            console.log(response);
            if (response.data.result) {
                self.setState({
                    user: response.data.user,
                    prg: response.data.prg
                });
            } else {
                window.location = '/pad/login/ppm';
            }
        }).catch(function (error) {
            console.log(error);
        });
        */
    }
    render() {
        return(   
            <div>
                <div className="row">
                    {/*this.state.list.length === 0 ? 
                        <div className={buttonClass} style={buttonStyle}>
                            <h3>功能清單建立中...</h3> 
                        </div>
                    : null*/}
                    <div className="row">
                        <div className="col-lg-3">
                            <button className="btn btn-primary">新增</button>
                        </div>
                        <div className="col-lg-9 text-right">
                            <button className="btn btn-primary">新增</button>
                        </div>
                    </div>
                    <p></p>
                    <table className="table table-bordered">
                        <thead>
                            <tr>
                                <td></td>
                                <td>使用者編號</td>
                                <td>使用者姓名</td>
                                <td>使用者密碼</td>
                                <td>上次建立密碼日期</td>
                                <td>密碼預計更新日期</td>
                                <td>密碼永久有效</td>
                                <td>使用User Menu</td>
                                <td>備註</td>
                                <td></td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><button className="btn btn-sm btn-warning">編輯</button></td>
                                <td>106013</td>
                                <td>林于斌</td>
                                <td>********</td>
                                <td>20-MAR-2017</td>
                                <td>20-SEP-2017</td>
                                <td><span className="label label-warning">N</span></td>
                                <td><span className="label label-success">Y</span></td>
                                <td>系統開發、系統測試</td>
                                <td><button className="btn btn-sm btn-danger">刪除</button></td>
                            </tr>
                        </tbody>
                    </table>
                    {/*this.state.list.map((item, index) => (
                        <div className={buttonClass} style={buttonStyle} key={item['prg_id']}>
                            <button type="button" className="btn btn-primary btn-lg btn-block">
                                {item['prg_name']}
                            </button>
                        </div>
                    ))*/}
                </div>    
            </div>
        );
    }
}