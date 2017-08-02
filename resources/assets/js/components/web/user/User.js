/** 
 * User.js
 */
import React from 'react';
import axios from 'axios';
import UserAdd from './UserAdd';
import UserEdit from './UserEdit';

export default class User extends React.Component{
    constructor(props) {
        super(props);

        this.state = {
            prg: [],
            user: [],
            search: '',
            isAddShow: false,
            isEditShow: false,
            allList: true
        }
    }

    init() {
        let self = this;       
        axios.get('/api/web/user/init', null, {
            method: 'get',
        }).then(function (response) {
            if (response.data.result) {
                self.setState({
                    user: response.data.user,
                    prg: response.data.prg
                });
            } else {
                console.log(response.data);
                window.location = '/web/login/ppm';
            }
        }).catch(function (error) {
            console.log(error);
        });
    }

    addList(item) {
        let list = this.state.user;
        list.push(item);
        this.setState({user: list});
    }

    editList(item) {
        let list = this.state.user;
        let user_id = item['user_id'];
        for (var i = 0; i < list.length; i++) {
            if (list[i]['user_id'] === user_id) {
                list[i] = item;
                this.setState({user: list});
            }
        }
    }

    deleteList(user_id) {
        let list = this.state.user;
        for (var i = 0; i < list.length; i++) {
            if (list[i]['user_id'] === user_id) {
                list.splice(i, 1);
                this.setState({user: list});
            }
        }
    }

    openAdd() {
        this.setState({isAddShow: true});
    }

    hideAdd() {
        this.setState({isAddShow: false});
    }

    openEdit(item) {
        this.refs.edit.setData(item);
        this.setState({isEditShow: true});
    }

    hideEdit() {
        this.setState({isEditShow: false});
    }

    onDelete(user_id) {
        if(confirm('您確定要刪除資料？')) {
            let self = this;
            axios.delete('/api/web/user/delete/' + user_id)
            .then(function (response) {
                if (response.data.result) {
                    self.deleteList(user_id);
                    alert('使用者[' + user_id + ']已刪除');
                } else {
                    alert(response.data.msg);
                }
            }).catch(function (error) {
                console.log(error);
            });
        }
    }

    nonSearch() {
        let self = this;       
        axios.get('/api/web/user/search/', null, {
            method: 'get',
        }).then(function (response) {
            if (response.data.result) {
                self.setState({
                    user: response.data.user,
                    search: '',
                    allList: true
                });
                console.log(response.data);
            } else {
                console.log(response.data);
            }
        }).catch(function (error) {
            console.log(error);
        });
    }

    onSearch() {
        let search = this.state.search;
        if (search !== '') {
            let self = this;       
            axios.get('/api/web/user/search/' + search, null, {
                method: 'get',
            }).then(function (response) {
                if (response.data.result) {
                    self.setState({
                        user: response.data.user,
                        allList: false
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
    
    searchChange(e) {
        this.setState({search: e.target.value});
    }

    componentDidMount() {
        this.init();
    }

    render() {
        const prg = this.state.prg;
        const user = this.state.user;
        return(   
            <div>
                <div className="row">
                    <div className="row">
                        <div className="col-lg-8 col-md-8 col-sm-6">
                            {prg['prg_ins'] === 'Y' ?
                                <button className="btn btn-primary" onClick={this.openAdd.bind(this)}>新增</button>
                            :
                                <button className="btn btn-primary disabled">新增</button>
                            }
                        </div>
                        <div className="col-lg-4 col-md-4 col-sm-6 text-right">
                            {this.state.allList ? 
                                <div className="input-group">
                                    <input 
                                        type="text" 
                                        className="form-control" 
                                        value={this.state.search}
                                        onChange={this.searchChange.bind(this)}/>
                                    <span className="input-group-btn">
                                        <button className="btn btn-default" onClick={this.onSearch.bind(this)}>查詢</button>
                                    </span>
                                </div>
                            :
                                <div className="input-group">
                                    <input 
                                        type="text" 
                                        className="form-control"
                                        value={this.state.search}  
                                        onChange={this.searchChange.bind(this)}
                                    />
                                    <span className="input-group-btn">
                                        <button 
                                            className="btn btn-danger" 
                                            onClick={this.nonSearch.bind(this)}
                                        >
                                        取消
                                        </button>
                                        <button 
                                            className="btn btn-default" 
                                            onClick={this.onSearch.bind(this)}
                                        >
                                        查詢
                                        </button>
                                    </span>
                                </div>
                            }
                        </div>
                    </div>
                    <p></p>
                    <table className="table table-bordered">
                        <thead>
                            <tr>
                                <td></td>
                                <td>使用者編號</td>
                                <td>使用者姓名</td>
                                <td>上次建立密碼日期</td>
                                <td>密碼預計更新日期</td>
                                <td>密碼永久有效</td>
                                <td>使用User Menu</td>
                                <td>使用狀態</td>
                                <td>備註</td>
                                <td></td>
                            </tr>
                        </thead>
                        <tbody>
                            {this.state.user.map((item, index) => (
                                <tr key={index}>
                                    <td>
                                        {prg['prg_upd'] === 'Y' ?
                                            <button className="btn btn-sm btn-warning" onClick={this.openEdit.bind(this, item)}>編輯</button>
                                        :
                                            <button className="btn btn-sm btn-warning disabled">編輯</button>
                                        }
                                    </td>
                                    <td>{item['user_id']}</td>
                                    <td>{item['user_name']}</td>
                                    <td>{item['pw_cday']}</td>
                                    <td>{item['pw_uday']}</td>
                                    <td>
                                        {item['pw_ctrl'] === 'Y' ? <span className="label label-success">Y</span>
                                        : <span className="label label-danger">N</span> }
                                    </td>
                                    <td>
                                        {item['class'] === 'Y' ? <span className="label label-success">Y</span>
                                        : <span className="label label-danger">N</span> }
                                    </td>
                                    <td>
                                        {item['state'] === 'Y' ? <span className="label label-success">Y</span>
                                        : <span className="label label-danger">N</span> }
                                    </td>
                                    <td>{item['rmk']}</td>
                                    <td>
                                        {prg['prg_upd'] === 'Y' ?
                                            <button className="btn btn-sm btn-danger" onClick={this.onDelete.bind(this, item['user_id'])}>刪除</button>
                                        :
                                            <button className="btn btn-sm btn-danger disabled">刪除</button>
                                        }
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                    {this.state.user.length === 0 ? 
                        <div>
                            <h3>資料讀取中...</h3> 
                        </div>
                    : null}
                    <UserAdd 
                        showModal={this.state.isAddShow} 
                        onHide={this.hideAdd.bind(this)} 
                        addList={this.addList.bind(this)} 
                    />
                    <UserEdit 
                        showModal={this.state.isEditShow} 
                        onHide={this.hideEdit.bind(this)}  
                        editList={this.editList.bind(this)} 
                        ref="edit"
                    />
                </div>    
            </div>
        );
    }
}