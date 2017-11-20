/** 
 * Retained.js
 */
import React from 'react'
import { Link } from 'react-router'
import axios from 'axios'

export default class Retained extends React.Component{
    constructor(props) {
        super(props)

        this.state = {
            list: [],
            ldate: 999999,
            loading: false,
        }
    }

    componentDidMount() {
        let today = new Date()
        var yyyy = today.toLocaleDateString().slice(0,4)
        var MM = (today.getMonth()+1<10 ? '0' : '')+(today.getMonth()+1)
        var dd = (today.getDate()<10 ? '0' : '')+today.getDate()
        let ldate =  yyyy+MM+dd
        this.setState({ldate: ldate})
        this.init(ldate)
    }

    init(ldate) {
        let self = this
        this.setState({loading: true})
        axios.get('/api/web/mpe/qa/retained/list/' + ldate)
        .then(function (response) {
            if (response.data.result) {
                self.setState({
                    list: response.data.list,
                })
                console.log(response.data)
            } else {
                console.log(response.data)
            }
            self.setState({loading: false})
        }).catch(function (error) {
            console.log(error)
            self.setState({loading: false})
        })
    }

    ldateChange(e) {
        let ldate  = e.target.value
        this.setState({ldate: ldate})
        if (ldate.length === 8) {
            this.init(ldate)
        }
    }

    render() { 
        const { list, ldate, loading } = this.state
        return(   
            <div>
                <div className="box" style={{marginTop: '10px', marginBottom: '10px'}}> 
                    <div className="level">
                        <div className="level-left">
                            <div className="level-item">
                                <Link className="button" to="/auth/web/menu">&larr; 功能選單</Link>
                            </div>
                        </div>
                        <div className="level-right">
                            <div className="level-item">
                                <div className="field is-expanded">
                                    <div className="field has-addons">
                                        <p className="control">
                                        <a className="button is-static">
                                            留樣日期
                                        </a>
                                        </p>
                                        <p className="control is-expanded">
                                            <input type="text" className="input" id="ldate" value={ldate}
                                                maxLength={8}
                                                onChange={this.ldateChange.bind(this)}
                                            />
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> 
                {loading &&
                    <div className="notification is-info">
                        <strong>資料讀取中請稍候...</strong>
                    </div>
                }
                {!loading && list.length > 0 &&
                    <table className="table is-bordered is-fullwidth">
                        <thead>
                            <tr>
                                <th>
                                    分支
                                </th>
                                <th>
                                    料號
                                </th>
                                <th>
                                    名稱
                                </th>
                                <th>
                                    批號
                                </th>
                                <th>
                                    抽樣日
                                </th>
                                <th>
                                    QA留樣量
                                </th>
                                <th>
                                    貯存條件
                                </th>
                                <th>
                                    特殊品
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            {list.map((item, index) => (
                                <tr key={index}>
                                    <td>
                                        {item.irmcu}
                                    </td>
                                    <td>
                                        {item.irlitm}
                                    </td>
                                    <td>
                                        {item.irdsc1}
                                    </td>
                                    <td>
                                        {item.irlotn}
                                    </td>
                                    <td>
                                        {item.iratdt}
                                    </td>
                                    <td>
                                        {item.irsq03 + item.iruom3}
                                    </td>
                                    <td>
                                        {item.t_prp1_name}
                                    </td>
                                    <td>
                                        {item.anda} {item.spec}
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                }
                {!loading && list.length === 0 &&
                    <div className="notification is-warning" style={{padding: '1rem 1rem 1rem 1rem'}}>
                        今日尚無留樣品資訊
                    </div>
                }
            </div>
        )
    }
}