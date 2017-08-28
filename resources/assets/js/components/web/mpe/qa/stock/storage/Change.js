/** 
 * Change.js
 */
import React from 'react';
import { Link } from 'react-router';
import axios from 'axios';

export default class Change extends React.Component{
    constructor(props) {
        super(props);

        this.state = {
            list: [],
            storage: [],
            search: [],
            search_str: '',
            search_key: '',
            searching: false,
            select_item: [],
            storageShow: false,
            isLoading: false,
        }
    }

    componentDidMount() {
        this.init();
    }

    init() {
        let self = this;       
        axios.get('/api/web/mpe/qa/stock/list')
        .then(function (response) {
            if (response.data.result) {
                self.setState({
                    list: response.data.list,
                    storage: response.data.storage,
                });
                console.log(response.data);
            } else {
                console.log(response.data);
            }
        }).catch(function (error) {
            console.log(error);
        });
    }

    onSearch(event) {
        event.preventDefault();
        let str = this.state.search_str;
        if (str !== '' && this.state.search_key !== str) {
            let self = this;       
            axios.get('/api/web/mpe/qa/stock/list/' + str)
            .then(function (response) {
                if (response.data.result) {
                    self.setState({
                        search: response.data.list,
                        searching: true,
                        search_key: str,
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

    searchChange(event) {
        this.setState({search_str: event.target.value});
    }

    cancelSearch() {
        this.setState({
            search: [],
            search_str: '',
            search_key: '',
            searching: false,
            content: this.state.list,
        });
    }

    showStorage(item) {
        this.setState({
            select_item: item,
            storageShow: true,
        });
    }

    hideStorage() {
        this.setState({
            select_item: [],
            storageShow: false,
        });
    }

    setList(data) {
        let list  = this.state.list;
        let newlist = [];
        for (let i = 0; i < list.length; i++) {
            newlist[i] = list[i];
            if ((list[i]['partno'] === data.partno) 
                && (list[i]['batch'] === data.batch)) {
                newlist[i] = {
                    partno: data.partno,
                    batch: data.batch,
                    pname: list[i]['pname'],
                    stor: data.stor,
                    whouse: data.whouse,    
                };
            }
        }
        this.setState({list: newlist});
    }

    onChange(item) {
        let self = this;
        let data = {
            partno: this.state.select_item.partno,
            batch: this.state.select_item.batch,
            whouse: item.whouse,
            stor: item.stor,
        };
        axios.put('/api/web/mpe/qa/stock/storage/change', data)
        .then(function (response) {
            if (response.data.result) {
                self.setList(data);
                self.hideStorage();
            } else {
                console.log(response.data);
            }
            self.setState({isLoading: false});
        }).catch(function (error) {
            console.log(error);
            self.setState({isLoading: false});
        });
    }

    render() { 
        const { list, search, search_str, searching, storageShow, storage, isLoading } = this.state;
        const content = search.length > 0 ? search : list;
        let show = storageShow ? 'is-active': '';
        return(   
            <div>
                <div className="box" style={{ marginTop: '10px', marginBottom: '10px' }}>
                    <div className="level">
                        <div className="level-left">
                            <div className="level-item">
                                <Link className="button" to="/auth/web/menu">&larr; 功能選單</Link> 
                            </div>
                        </div>
                        <div className="level-right">
                            <div className="level-item">
                                <div className="field has-addons">
                                    <div className="control">
                                        <input 
                                            type="text" 
                                            className="input" 
                                            maxLength={30}
                                            value={this.state.search_str}
                                            onChange={this.searchChange.bind(this)}
                                        />
                                    </div>
                                    <div className="control">
                                        {searching && <a className="button is-danger" onClick={this.cancelSearch.bind(this)}>取消</a>}
                                        <button type="submit" className="button">查詢</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {content.length > 0 ?
                    <div>
                        <table className="table is-bordered is-fullwidth">
                            <thead>
                                <tr>

                                    <th>
                                        料號
                                    </th>
                                    <th>
                                        批號
                                    </th>
                                    <th>
                                        品名
                                    </th>
                                    <th>
                                        倉庫
                                    </th>
                                    <th>
                                        儲位
                                    </th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                {content.map((item, index) => (
                                    <tr key={index}>
                                        <td>
                                            {item.partno}
                                        </td>
                                        <td>
                                            {item.batch}
                                        </td>
                                        <td>
                                            {item.pname}
                                        </td>
                                        <td>
                                            {item.whouse}
                                        </td>
                                        <td>
                                            {item.stor}
                                        </td>
                                        <td width="92.22">
                                            <button className="button is-primary" onClick={this.showStorage.bind(this, item)}>變更儲位</button>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                        <div className={ "modal " + show }>
                            <div className="modal-background"></div>
                            <div className="modal-card">
                                <header className="modal-card-head">
                                    <p className="modal-card-title">儲位清單</p>
                                </header>
                                <section className="modal-card-body">
                                    <table className="table is-bordered is-fullwidth">
                                        <thead>
                                            <tr>
                                                <th>
                                                    倉庫
                                                </th>
                                                <th>
                                                    倉庫名稱
                                                </th>
                                                <th>
                                                    儲位
                                                </th>
                                                <th>
                                                    儲位名稱
                                                </th>
                                                <th width="92.22"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {storage.map((item, index) => (
                                                <tr key={index}>
                                                    <td>
                                                        {item.whouse}
                                                    </td>
                                                    <td>
                                                        {item.posit}
                                                    </td>
                                                    <td>
                                                        {item.stor}
                                                    </td>
                                                    <td>
                                                        {item.storn}
                                                    </td>
                                                    <td>
                                                        {isLoading ?
                                                            <button className="button is-loading is-warning"></button>
                                                        :
                                                            <button type="button" className="button is-warning" onClick={this.onChange.bind(this, item)}>變更</button>
                                                        }
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </section>
                                <footer className="modal-card-foot">
                                    <button className="button" onClick={this.hideStorage.bind(this)}>關閉</button>
                                </footer>
                            </div>
                        </div>
                    </div>
                :
                    <div className="notification is-warning" style={{padding: '1rem 1rem 1rem 1rem'}}>
                        無庫存資料
                    </div>
                }
            </div>
        );
    };
}