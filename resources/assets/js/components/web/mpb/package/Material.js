/** 
 * production.material.js
 */
import React from 'react'
import { Link } from 'react-router'
import axios from 'axios'

export default class Material extends React.Component{
    constructor(props) {
        super(props)

        this.state = {
            info: [],
            material: [],
            litm: '',
            msg: '',
        }
    }
    
    componentDidMount() {
        this.init()
    }

    init() {
        const { sno, psno } = this.props.params
        this.getMaterialList(sno, psno)
    }

    getMaterialList(sno, psno) {
        let self = this       
        axios.get('/api/web/mpb/prod/package/material/' + sno + '/' + psno)
        .then(function (response) {
            if (response.data.result) {
                self.setState({
                    info: response.data.info,
                    material: response.data.material,
                })
                console.log(response.data)
            } else {
                console.log(response.data)
            }
        }).catch(function (error) {
            console.log(error)
        })
    }

    checkLitm(e) {
        let material = this.state.material
        let litm = e.target.value
        this.setState({litm: litm})
        if (litm.length === 7) {
            if(Object.keys(material).map((key) => {
                if (material[key].litm === litm) {
                    material[key].ukid = '***'
                    this.setState({
                        material: material,
                        msg: '',
                        litm: '',
                    })
                    return true
                }
            })) {
                return 
            }
            this.setState({msg: litm + '並非此途程所需料號'})
        }
    }

    submitCheck() {
        const { material } = this.state
        const { sno, psno } = this.props.params
        Object.keys(material).map((key) => {
            if (!material[key].ukid) {
                this.setState({msg: '尚有料品未確認!!'})
                return
            }
        })
        let self = this
        let form_data = new FormData()
        form_data.append('sno', sno)
        form_data.append('psno', psno)
        axios.post('/api/web/mpb/prod/package/material/check', form_data)
        .then(function (response) {
            if (response.data.result) {
                console.log(response.data)
                window.location = '/auth/web/mpb/prod/duty/' + sno + '/' + psno
            } else {
                console.log(response.data)
                window.location = '/web/login/ppm'
            }
        }).catch(function (error) {
            console.log(error)
        })
    }

    render() {
        const { material, info } = this.state 
        const { sno } = this.props.params
        return(   
            <div>
                <div className="box" style={{ marginTop: '10px', marginBottom: '10px' }}>
                    <div className="level">
                        <div className="level-left">
                            <div className="level-item">
                                <Link className="button is-medium" to="/auth/web/mpb/prod/list">&larr; 返回上一頁</Link> 
                            </div>
                        </div>
                        <div className="level-right">
                            <div className="level-item">
                                <button className="button is-medium is-success" 
                                    disabled={false} 
                                    onClick={this.submitCheck.bind(this)}>領料確認</button>
                            </div>
                        </div>
                    </div>
                </div>
                {material.length > 0 ?
                    <div>
                        <div className="column is-hidden-desktop">
                            <label className="is-size-4">請將畫面轉橫</label>
                        </div>
                        <div className="box" style={{ marginBottom: '10px' }}>
                            <div className="field is-horizontal">
                                <div className="field-body">
                                    <div className="field is-grouped">
                                        <div className="field" style={{marginRight: '10px'}}>
                                            <input type="text" className="input is-large" 
                                                disabled={false}
                                                value={this.state.litm}
                                                autoFocus
                                                maxLength={7}
                                                placeholder="掃描條碼"
                                                onChange={this.checkLitm.bind(this)}
                                            />
                                        </div>
                                        {this.state.msg !== '' &&
                                            <div className="notification is-warning" style={{padding: '1rem 1rem 1rem 1rem'}}>
                                                <h5 className="title is-5">{this.state.msg}</h5>
                                            </div>
                                        } 
                                    </div>
                                </div>
                            </div>
                        </div>
                        {/*
                        <article className="message is-info is-hidden-touch" style={{ marginBottom: '10px' }}>
                            <div className="message-header is-size-4">
                                <p>製程單號{sno}詳細資訊</p>
                            </div>
                            <div className="message-body is-size-4">
                                <span className="title is-4">{info.minfo}</span><br />
                                <span className="title is-4">{info.sinfo}</span><br />
                                <span className="title is-4">{info.mainfo}</span>
                            </div>
                        </article>
                        */}
                        <table className="table is-bordered is-fullwidth is-size-4 is-hidden-touch">
                            <thead>
                                <tr>
                                    <th>料號</th>
                                    <th>品名</th>
                                    <th>舊料號</th>
                                    <th>確認</th>
                                </tr>
                            </thead>
                            <tbody>
                                {material.map((item, index) => (

                                    <tr key={index}>
                                        <td>{item.litm}</td>
                                        <td>{item.iname}</td>
                                        <td>{item.srtx}</td>
                                        <td>
                                            {item.ukid &&
                                                <span className="icon has-text-success"><i className="fa fa-check is-success"></i></span>
                                            }
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                :
                    <div className="notification is-warning is-size-4" style={{padding: '1rem 1rem 1rem 1rem'}}>
                        目前尚無生產資訊...
                    </div>
                }
            </div>
        )
    }
}