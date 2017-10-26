/** 
 * Info.js
 */
import React from 'react';
import { Link } from 'react-router';
import axios from 'axios';

export default class Info extends React.Component{
    constructor(props) {
        super(props);

        this.state = {
            info: null,
            search: '',
            searching: false,
        }
    }

    searchChange(e) {
        this.setState({search: e.target.value});
    }
    
    getInfo() {
        let self = this;
        const search = this.state.search;
        this.setState({searching: true});
        axios.get('/api/web/mpe/qc/doc/info/' + search)
        .then(function (response) {
            if (response.data.result) {
                if (response.data.info.length > 0) {
                    self.setState({
                        info: response.data.info,
                    });
                } else {
                    alert('查詢不到任何資料');
                }
                console.log(response.data);
            } else {
                console.log(response.data);
                alert(response.data.msg)
            }
            self.setState({searching: false});
        }).catch(function (error) {
            console.log(error);
            self.setState({searching: false});
        });
    }

    render() { 
        const { info, search, searching } = this.state;
        let loading = searching ? 'is-loading': '';
        return(   
            <div>
                <div className="box"> 
                    <div className="column is-7" style={{padding: 0}}>
                        <div className="field has-addons">
                            <div className="control is-expanded">
                                <input 
                                    className="input is-medium" 
                                    type="text" 
                                    placeholder="請輸入條碼、料號、批號、品名進行查詢..."
                                    onChange={this.searchChange.bind(this)}
                                />
                            </div>
                            <div className="control">
                                <button 
                                    className={"button is-info is-medium " + loading}
                                    onClick={this.getInfo.bind(this)}
                                >
                                    查詢
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                {info &&
                    <table className="table is-bordered is-hoverable is-fullwidth">
                        <thead>
                            <tr>
                                <td>料號</td>
                                <td>批號</td>
                                <td>品名</td>
                                <td>庫存量</td>
                                <td>安全庫存量</td>
                                <td width="63">SDS</td>
                                <td width="63">COA</td>
                            </tr>
                        </thead>
                        <tbody>
                            {info.map((item,index) => (
                                <tr key={index}>
                                    <td>{item.partno}</td>
                                    <td>{item.batch}</td>
                                    <td>{item.ename}</td>
                                    <td width="120">{item.qty}</td>
                                    <td width="120">{item.sfty}</td>
                                    <td width="83">
                                        {item.sds_no ?
                                            <a className="button"
                                                href={"/api/web/mpe/qc/doc/read/sds/" + item.partno + "/N/" + item.sds_no}
                                                target="_blank"
                                            >
                                                下載
                                            </a>
                                        :
                                        <a className="button" title="Disabled button" disabled>下載</a>
                                        }
                                    </td>
                                    <td width="83">
                                        {item.coa_no ?
                                            <a className="button"
                                                href={"/api/web/mpe/qc/doc/read/coa/" + item.partno + "/" + item.batch + "/" + item.coa_no}
                                                target="_blank"
                                                disabled={item.coa_no? "false": "true"}
                                            >
                                                下載
                                            </a>
                                        :
                                            <a className="button" title="Disabled button" disabled>下載</a>
                                        }
                                    </td>
                                </tr>
                            ))}
                        </tbody>

                    </table>
                }
            </div>
        )
    }
}