/** 
 * Wetestlog.js
 */
import React from "react"
import { Link } from "react-router"
import axios from 'axios'

export default class Wetestlog extends React.Component {
  constructor(props) {
    super(props)
    this.state = {
      point_info: {}, point_no: '', ldate: '',
      mo_hum: '', mo_max: '', mo_min: '', mo_rmk: '', mo_time: '',
      af_hum: '', af_max: '', af_min: '', af_rmk: '', af_time: '',
      ev_hum: '', ev_max: '', ev_min: '', ev_rmk: '', ev_time: '',
      zero: 'N', rmk: '',
      mo: false, af: false, ev: false,
      log_data: {},
      init: false,
    }
    this.sendMsg = this.props.sendMsg.bind(this)
  }

  componentDidMount() {
    this.init()
  }

  init() {
    let self = this
    let point_info = this.props.pointInfo
    this.setState({ init: true })
    axios.get('/api/web/mpz/pointlog/wetest/init/' + point_info.point_no)
      .then(function (response) {
        if (response.data.result) {
          self.setState({
            point_info: point_info,
            point_no: point_info.point_no,
            ldate: response.data.ldate,
            log_data: response.data.log_data,
            init: false,
          })
          console.log(response.data)
          self.setValue(response.data.log_data)
        } else {
          self.props.sendMsg(response.data.msg)
          self.onCancel()
        }
      }).catch(function (error) {
        console.log(error)
        self.props.sendMsg(error)
      })
  }

  setValue(data) {
    if (data !== null) {
      let mo = data.mo_time !== null ? true : false
      let af = data.af_time !== null ? true : false
      let ev = data.ev_time !== null ? true : false
      this.setState({
        mo_hum: data.mo_hum, mo_max: data.mo_max, mo_min: data.mo_min, mo_rmk: data.mo_rmk, mo_time: data.mo_time,
        af_hum: data.af_hum, af_max: data.af_max, af_min: data.af_min, af_rmk: data.af_rmk, af_time: data.af_time,
        ev_hum: data.ev_hum, ev_max: data.ev_max, ev_min: data.ev_min, ev_rmk: data.ev_rmk, ev_time: data.ev_time,
        zero: data.zero, rmk: data.rmk,
        mo: mo, af: af, ev: ev,
      })
    }
  }

  onSave(e) {
    let self = this
    this.setState({ isLoading: true })
    const {
            point_no, ldate,
      mo_hum, mo_max, mo_min, mo_rmk, mo_time,
      af_hum, af_max, af_min, af_rmk, af_time,
      ev_hum, ev_max, ev_min, ev_rmk, ev_time,
      zero, rmk
        } = this.state
    let form_data = new FormData()
    form_data.append('point_no', point_no)
    form_data.append('ldate', ldate)
    form_data.append('mo_hum', mo_hum || '')
    form_data.append('mo_max', mo_max || '')
    form_data.append('mo_min', mo_min || '')
    form_data.append('mo_time', mo_time || '')
    form_data.append('mo_rmk', mo_rmk || '')
    form_data.append('af_hum', af_hum || '')
    form_data.append('af_max', af_max || '')
    form_data.append('af_time', af_time || '')
    form_data.append('af_min', af_min || '')
    form_data.append('af_rmk', af_rmk || '')
    form_data.append('ev_hum', ev_hum || '')
    form_data.append('ev_max', ev_max || '')
    form_data.append('ev_time', ev_time || '')
    form_data.append('ev_min', ev_min || '')
    form_data.append('ev_rmk', ev_rmk || '')
    form_data.append('zero', zero || 'N')
    form_data.append('rmk', rmk || '')
    axios.post('/api/web/mpz/pointlog/wetest/save', form_data)
      .then(function (response) {
        if (response.data.result) {
          self.sendMsg(point_no + '檢查點記錄成功!')
          self.setState({ isLoading: false })
          self.onCancel()
        } else {
          self.sendMsg(response.data.msg)
          self.setState({ isLoading: false })
        }
      }).catch(function (error) {
        console.log(error)
        self.sendMsg(error)
        self.setState({ isLoading: false })
      })
  }

  mo_maxChange(e) {
    this.setState({ mo_max: e.target.value })
  }
  mo_humChange(e) {
    this.setState({ mo_hum: e.target.value })
  }
  mo_minChange(e) {
    this.setState({ mo_min: e.target.value })
  }
  mo_rmkChange(e) {
    this.setState({ mo_rmk: e.target.value })
  }
  af_maxChange(e) {
    this.setState({ af_max: e.target.value })
  }
  af_humChange(e) {
    this.setState({ af_hum: e.target.value })
  }
  af_minChange(e) {
    this.setState({ af_min: e.target.value })
  }
  af_rmkChange(e) {
    this.setState({ af_rmk: e.target.value })
  }
  ev_maxChange(e) {
    this.setState({ ev_max: e.target.value })
  }
  ev_humChange(e) {
    this.setState({ ev_hum: e.target.value })
  }
  ev_minChange(e) {
    this.setState({ ev_min: e.target.value })
  }
  ev_rmkChange(e) {
    this.setState({ ev_rmk: e.target.value })
  }
  zeroChange(e) {
    let value = this.state.zero === 'Y' ? 'N' : 'Y'
    this.setState({ zero: value })
  }
  rmkChange(e) {
    this.setState({ rmk: e.target.value })
  }

  onCancel() {
    this.props.onCancel()
  }

  render() {
    const { init, isLoading, point_info } = this.state
    const { mo, af, ev } = this.state
    return (
      <div>
        <div className="column">
          <h4 className="title is-4">最濕點濕度記錄表</h4>
        </div>
        <div className="column">
          <table className="table is-bordered" style={{ marginBottom: '0px' }}>
            <tbody>
              <tr>
                <td>名稱</td><td>{point_info.point_name}</td><td>儀器編號</td><td>{point_info.mach_no}</td>
              </tr>
              <tr>
                <td>儀器校期</td><td>{point_info.ch_date}</td>
                <td>合格範圍</td><td>濕度：{point_info.hum_range} R.H(%)</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div className="column">
          <table className="table is-bordered" style={{ marginBottom: '0px' }}>
            <tbody>
              <tr>
                <td colSpan={4}>
                  <label className="label">上午(1)濕度記錄</label>
                </td>
              </tr>
              <tr>
                <td>
                  <label className="label">顯示值</label>
                  <input className="input" type="number" maxLength={10}
                    disabled={mo}
                    value={this.state.mo_hum || ''}
                    onChange={this.mo_humChange.bind(this)}
                  />
                </td>
                <td>
                  <label className="label">MAX</label>
                  <input className="input" type="number" maxLength={10}
                    disabled={mo}
                    value={this.state.mo_max || ''}
                    onChange={this.mo_maxChange.bind(this)}
                  />
                </td>
                <td>
                  <label className="label">MIN</label>
                  <input className="input" type="number" maxLength={50}
                    disabled={mo}
                    value={this.state.mo_min || ''}
                    onChange={this.mo_minChange.bind(this)}
                  />
                </td>
                <td>
                  <label className="label">備註</label>
                  <div className="select">
                    <select
                      placeholder="請選擇"
                      disabled={mo}
                      onChange={this.mo_rmkChange.bind(this)}
                      value={this.state.mo_rmk || ''}
                    >
                      <option value=""></option>
                      <option value="參加集會">參加集會</option>
                      <option value="溫濕度異常">溫濕度異常</option>
                      <option value="儀器異常">儀器異常</option>
                      <option value="更換儀器">更換儀器</option>
                      <option value="更換表單">更換表單</option>
                      <option value="其它">其它</option>
                    </select>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div className="column">
          <table className="table is-bordered" style={{ marginBottom: '0px' }}>
            <tbody>
              <tr>
                <td colSpan={4}>
                  <label className="label">下午(1)濕度記錄</label>
                </td>
              </tr>
              <tr>
                <td>
                  <label className="label">顯示值</label>
                  <input className="input" type="number" maxLength={10}
                    disabled={af}
                    value={this.state.af_hum || ''}
                    onChange={this.af_humChange.bind(this)}
                  />
                </td>
                <td>
                  <label className="label">MAX</label>
                  <input className="input" type="number" maxLength={10}
                    disabled={af}
                    value={this.state.af_max || ''}
                    onChange={this.af_maxChange.bind(this)}
                  />
                </td>
                <td>
                  <label className="label">MIN</label>
                  <input className="input" type="number" maxLength={50}
                    disabled={af}
                    value={this.state.af_min || ''}
                    onChange={this.af_minChange.bind(this)}
                  />
                </td>
                <td>
                  <label className="label">備註</label>
                  <div className="select">
                    <select
                      placeholder="請選擇"
                      disabled={af}
                      onChange={this.af_rmkChange.bind(this)}
                      value={this.state.af_rmk || ''}
                    >
                      <option value=""></option>
                      <option value="參加集會">參加集會</option>
                      <option value="溫濕度異常">溫濕度異常</option>
                      <option value="儀器異常">儀器異常</option>
                      <option value="更換儀器">更換儀器</option>
                      <option value="更換表單">更換表單</option>
                      <option value="其它">其它</option>
                    </select>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div className="column">
          <table className="table is-bordered" style={{ marginBottom: '0px' }}>
            <tbody>
              <tr>
                <td colSpan={4}>
                  <label className="label">下午(2)濕度記錄</label>
                </td>
              </tr>
              <tr>
                <td>
                  <label className="label">顯示值</label>
                  <input className="input" type="number" maxLength={10}
                    disabled={ev}
                    value={this.state.ev_hum || ''}
                    onChange={this.ev_humChange.bind(this)}
                  />
                </td>
                <td>
                  <label className="label">MAX</label>
                  <input className="input" type="number" maxLength={10}
                    disabled={ev}
                    value={this.state.ev_max || ''}
                    onChange={this.ev_maxChange.bind(this)}
                  />
                </td>
                <td>
                  <label className="label">MIN</label>
                  <input className="input" type="number" maxLength={50}
                    disabled={ev}
                    value={this.state.ev_min || ''}
                    onChange={this.ev_minChange.bind(this)}
                  />
                </td>
                <td>
                  <label className="label">備註</label>
                  <div className="select">
                    <select
                      placeholder="請選擇"
                      disabled={ev}
                      onChange={this.ev_rmkChange.bind(this)}
                      value={this.state.ev_rmk || ''}
                    >
                      <option value=""></option>
                      <option value="參加集會">參加集會</option>
                      <option value="溫濕度異常">溫濕度異常</option>
                      <option value="儀器異常">儀器異常</option>
                      <option value="更換儀器">更換儀器</option>
                      <option value="更換表單">更換表單</option>
                      <option value="其它">其它</option>
                    </select>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div className="column">
          <label className="checkbox">
            <input type="checkbox"
              value={this.state.zero}
              checked={this.state.zero === 'Y'}
              onChange={this.zeroChange.bind(this)}
            />
            歸零確認
                    </label>
        </div>
        <div className="column">
          <label className="label">備註</label>
          <input type="text" className="input" maxLength={50}
            value={this.state.rmk || ''}
            onChange={this.rmkChange.bind(this)}
          />
        </div>
        <div className="column">
          <div className="field is-grouped">
            <p className="control">
              {(mo && af && ev) ?
                <button className="button is-primary is-static">已記錄完畢</button>
                : isLoading ?
                  <button className="button is-loading is-primary"></button>
                  :
                  <button type="button" className="button is-primary" onClick={this.onSave.bind(this)}>儲存</button>
              }
            </p>
            <p>
              <button className="button" onClick={this.onCancel.bind(this)}>取消</button>
            </p>
          </div>
        </div>
      </div>
    )
  }
}

