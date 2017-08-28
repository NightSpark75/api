/** 
 * User.js
 */
import React from 'react';
import axios from 'axios';
import { Link } from 'react-router';
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
        this.refs.add.initState();
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
                <div className="box" style={{ marginTop: '10px' }}>
                    <div className="field is-horizontal">
                        <div className="field-body">
                            <div className="field is-grouped">
                                <p className="control">
                                    <Link className="button" to="/auth/web/menu">&larr; 功能選單</Link> 
                                </p>
                                <p className="control">
                                    {prg['prg_ins'] === 'Y' ?
                                        <button className="button is-primary" onClick={this.openAdd.bind(this)}>新增</button>
                                    :
                                        <button className="button is-primary is-static">新增</button>
                                    }
                                </p>
                            </div>
                            
                            {this.state.allList ? 
                                <div className="field has-addons has-addons-right">
                                    <div className="control">
                                        <input 
                                            type="text" 
                                            className="input" 
                                            value={this.state.search}
                                            onChange={this.searchChange.bind(this)}
                                        />
                                    </div>
                                    <div className="control">
                                        <button className="button is-info" onClick={this.onSearch.bind(this)}>查詢</button>
                                    </div>
                                </div>
                            :
                                <div className="field has-addons has-addons-right">
                                    <p className="control">
                                        <input 
                                            type="text" 
                                            className="input"
                                            value={this.state.search}  
                                            onChange={this.searchChange.bind(this)}
                                        />
                                    </p>
                                    <p className="control">
                                        <button className="button is-warning" onClick={this.nonSearch.bind(this)}>取消</button>
                                    </p>
                                    <p>
                                        <button className="button is-light" onClick={this.onSearch.bind(this)}>查詢</button>
                                    </p>
                                </div>
                            }
                        </div>
                    </div>
                </div>
                {this.state.user.length === 0 ?
                    <h3>資料讀取中...</h3> 
                :
                    <table className="table is-bordered is-striped is-fullwidth">
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
                                            <button className="button is-small is-info" onClick={this.openEdit.bind(this, item)}>編輯</button>
                                        :
                                            <button className="button is-small is-primary is-static">編輯</button>
                                        }
                                    </td>
                                    <td>{item['user_id']}</td>
                                    <td>{item['user_name']}</td>
                                    <td>{item['pw_cday']}</td>
                                    <td>{item['pw_uday']}</td>
                                    <td>
                                        {item['pw_ctrl'] === 'Y' ? <span className="tag is-success">Y</span>
                                        : <span className="tag is-danger">N</span> }
                                    </td>
                                    <td>
                                        {item['class'] === 'Y' ? <span className="tag is-success">Y</span>
                                        : <span className="tag is-danger">N</span> }
                                    </td>
                                    <td>
                                        {item['state'] === 'Y' ? <span className="tag is-success">Y</span>
                                        : <span className="tag is-danger">N</span> }
                                    </td>
                                    <td>{item['rmk']}</td>
                                    <td>
                                        {prg['prg_upd'] === 'Y' ?
                                            <button className="button is-small is-danger" onClick={this.onDelete.bind(this, item['user_id'])}>刪除</button>
                                        :
                                            <button className="button is-small is-danger is-static">刪除</button>
                                        }
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                }
                    <UserAdd 
                        showModal={this.state.isAddShow} 
                        onHide={this.hideAdd.bind(this)} 
                        addList={this.addList.bind(this)} 
                        ref="add"
                    />
                    <UserEdit 
                        showModal={this.state.isEditShow} 
                        onHide={this.hideEdit.bind(this)}  
                        editList={this.editList.bind(this)} 
                        ref="edit"
                    />   
            </div>
        );
    }
}