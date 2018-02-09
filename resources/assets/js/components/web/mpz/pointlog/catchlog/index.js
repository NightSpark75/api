/** 
 * Catchlog.js
 */
import React from "react"
import { Link } from "react-router"
import axios from 'axios'
import Capture from './capture'
import Replace from './replace'
import Confirm from '../../../../sys/modal/confirm'
import Deviation from '../deviation'

export default class Catchlog extends React.Component {
  constructor(props) {
    super(props)
    this.state = {
      log_data: {},
      point_no: '', ldate: 0, device_type: '',
      catch_num1: 0, catch_num2: 0, catch_num3: 0, catch_num4: 0, catch_num5: 0, catch_num6: 0,
      change1: 'N', change2: 'N', change3: 'N', change4: 'N', change5: 'N', change6: 'N', check_lamp: 'N',
      rmk: '', discription: '', deviation: 'N',
      changeDate: [],
      co: false,
      vn1: false, vn2: false, vn3: false, vn4: false, vn5: false, vn6: false,
      vc1: false, vc2: false, vc3: false, vc4: false, vc5: false, vc6: false,
      vlp: false,
      rmkShow: false, rmkOption: [], rmkList: [],
      thisMonth: 0, lastMonth: 0,
      isLoading: false, init: false,
      msg_type: '', msg: '',
      confirmShow: false,
      isDeviation: false,
      isChecked: false,
      isOverdue: false,
    }
    this.sendMsg = this.props.sendMsg.bind(this)
  }

  componentDidMount() {
    let point = this.props.pointInfo
    this.setState({ rmkList: ['參加集會', '偏差', '其它'] })
    this.init(point.point_no, point.device_type)
  }

  init(point_no, device_type) {
    let self = this
    this.setState({ init: true })
    axios.get('/api/web/mpz/pointlog/catch/init/' + point_no)
      .then(function (response) {
        if (response.data.result) {
          self.setState({
            log_data: response.data.log_data,
            point_no: point_no,
            ldate: response.data.ldate,
            device_type: device_type,
            thisMonth: response.data.thisMonth,
            lastMonth: response.data.lastMonth,
            changeDate: response.data.changeDate,
            init: false,
          })
          self.setLayout()
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

  setLayout() {
    let device = this.state.device_type
    switch (device) {
      case '1':
        this.setState({
          vn3: true, vn4: true, vn5: true, vn6: true,
          vc5: true
        })
        break
      case '2':
        this.setState({
          vn1: true, vn2: true,
          vc1: true, vc2: true, vc3: true,
        })
        break
      case '3':
        this.setState({
          co: true,
          vn3: true, vn4: true, vn5: true, vn6: true,
          vc5: true, vc6: true,
        })
        break
      case '4':
        this.setState({
          co: true,
          vn1: true, vn2: true, vlp: true,
          vc1: true, vc2: true, vc3: true, vc4: true,
        })
        break
    }
  }

  setValue(data) {
    if (data !== null) {
      let rmk = (data.rmk === null) ? '' : data.rmk
      this.setState({
        point_no: data.point_no, ldate: data.ldate,
        catch_num1: data.catch_num1, catch_num2: data.catch_num2, catch_num3: data.catch_num3,
        catch_num4: data.catch_num4, catch_num5: data.catch_num5, catch_num6: data.catch_num6,
        change1: data.change1, change2: data.change2, change3: data.change3,
        change4: data.change4, change5: data.change5, change6: data.change6, check_lamp: data.lamp,
        rmk: rmk, discription: data.discription, deviation: data.deviation,
      })
    }
  }

  initState() {
    this.setState({
      point_no: '', ldate: 0, device_type: '',
      catch_num1: 0, catch_num2: 0, catch_num3: 0, catch_num4: 0, catch_num5: 0, catch_num6: 0,
      change1: 'N', change2: 'N', change3: 'N', change4: 'N', change5: 'N', change6: 'N', check_lamp: 'N',
      rmk: '', discription: '', deviation: 'N',
      changeDate: [],
      vn1: false, vn2: false, vn3: false, vn4: false, vn5: false, vn6: false,
      vc1: false, vc2: false, vc3: false, vc4: false, vc5: false, vc6: false,
      vlp: false,
      thisMonth: 0, lastMonth: 0,
      isLoading: false, init: false,
      msg_type: '', msg: '',
    })
  }

  catchChange(item, e) {
    let val = e.target.value
    this.setState({[item]: val})
  }

  checkboxChange(item, e) {
    let state, value
    state = this.state[item]
    value = state === 'Y' ? 'N' : 'Y'
    this.setState({[item]: value})
  }

  onSave(e) {
    this.setState({confirmShow: false})
    let self = this
    this.setState({ isLoading: true })
    let form_data = new FormData()
    keyList.map((item) => {
      form_data.append(item, this.state[item])
    })
    return
    axios.post('/api/web/mpz/pointlog/catch/save', form_data)
      .then(function (response) {
        if (response.data.result) {
          self.sendMsg(point_no + '檢查點記錄成功!')
          self.setState({ isLoading: false })
          self.initState()
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

  onCancel() {
    this.initState()
    this.props.onCancel()
  }

  openConfirm() {
    this.setState({confirmShow: true})
  }

  hideConfirm() {
    this.setState({confirmShow: false})
  }

  checkDeviation() {
    this.setState({checkDeviation: true})
  }

  render() {
    const { pointInfo } = this.props
    const { init, isLoading, isChecked, isDeviation, isOverdue } = this.state 
    const isComplete = !(this.state.log_data === null)
    let today = new Date()
    return (
      <div>
        <table className="table is-bordered table is-fullwidth">
          <tbody>
            <tr>
              <td colSpan={2}>
                <span className="title is-4">鼠蟲防治記錄表</span>
                <span className="title is-5" style={{marginLeft: '10px'}}>{pointInfo.device_name}</span>
                <span className="title is-6" style={{marginLeft: '10px'}}>
                  日期：{today.getFullYear()+ "/" + (today.getMonth()+1) + "/" + today.getDate()}
                </span>
              </td>  
            </tr>
            <tr>
              <td width="120">位置</td>
              <td>
                <span>{pointInfo.point_no}</span>
                <span style={{marginLeft: '10px'}}>{pointInfo.point_name}</span>
                <span style={{marginLeft: '10px'}}>{pointInfo.point_des}</span>
              </td>
            </tr>
            <tr>
              <td colSpan={2}>
                <span>
                  本月累計：{this.state.thisMonth} {this.state.co ? ' 本月累計成長率：' : null}
                </span>
                <span style={{marginLeft: '10px'}}>
                  上月統計：{this.state.lastMonth} {this.state.co ? ' 上月累計成長率：' : null}
                </span>
              </td>
            </tr>
            <tr>
              <td>捕捉記錄</td>
              <td>
                {catchList.map((item, index) => {
                  if (this.state[item.show]) {
                    return (<Capture 
                      key={index}
                      label={item.label}
                      value={this.state[item.key]}
                      onChange={this.catchChange.bind(this, item.type)}
                    />)
                  }
                })}
              </td>
            </tr>
            <tr>
              <td>更換工具</td>
              <td>
                {replaceList.map((item, index) => {
                  if (this.state[item.show]) {
                    return (<Replace 
                      key={index}
                      label={item.label}
                      value={this.state[item.key]}
                      checked={this.state[item.key]}
                      onChange={this.checkboxChange.bind(this, item.type)}
                      msg={index}
                    />)
                  }
                })}
              </td>
            </tr>
            <tr>
              <td>撿查</td>
              <td>
                <div className="field is-horizontal">
                  <div className="field-label is-normal" style={{flexGrow: '0', paddingTop: '0px'}}>
                    <label className="label" style={{width: '60px'}}>驅蚊燈</label>
                  </div>
                  <div className="field-body">
                    <div className="field has-addons">
                      <div className="control">
                        <label className="radio">
                          <input type="radio" name="lamp"
                            value={this.state.check_lamp}
                            checked={this.state.check_lamp === 'Y'}
                            onChange={this.checkboxChange.bind(this, 'check_lamp')}
                          />
                          正常
                        </label>
                        <label className="radio" style={{marginLeft: '15px'}}>
                          <input type="radio" name="lamp"
                            value={this.state.check_lamp}
                            checked={this.state.check_lamp === 'N'}
                            onChange={this.checkboxChange.bind(this, 'check_lamp')}
                          />
                          異常
                        </label>
                      </div>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
            <tr>
              <td>逾時原因</td>
              <td>
                <select className="select"
                  placeholder="請選擇"
                  onChange={this.catchChange.bind(this, 'rmk')}
                  value={this.state.rmk || ""}
                >
                  <option value=""></option>
                  <option value="參加集會">參加集會</option>
                  <option value="其它">其它</option>
                </select>
              </td>
            </tr>
            <tr>
              <td>備註說明</td>
              <td>
                <div className="field is-horizontal">
                  <div className="field-body">
                    <div className="field">
                      <div className="control">
                        <textarea className="textarea" placeholder="請輸入其它說明"
                          value={this.state.discription || ''}
                          onChange={this.catchChange.bind(this, 'discription')}
                        >
                        </textarea>
                      </div>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
            {isDeviation &&
              <tr>
                <td>開立偏差</td>
                <td>
                  <div className="field is-horizontal">
                    <div className="field-body">
                      <div className="field has-addons">
                        <div className="control">
                          <label className="checkbox">
                            <input type="checkbox"
                              value={this.state.deviation}
                              checked={this.state.deviation === 'Y'}
                              onChange={this.checkboxChange.bind(this, 'deviation')}
                            />
                              <span style={{fontSize: '16px', fontWeight: 'bolder'}}>
                                開立偏差
                              </span>
                          </label>
                        </div>
                      </div>
                    </div>
                  </div>
                </td>
              </tr>
            }
          </tbody>
        </table>
        <Deviation 
          isLoading={isLoading}
          isComplete={isComplete}
          isDeviation={isDeviation}
          isChecked={isChecked}
          isOverdue={isOverdue}
          onSave={this.openConfirm.bind(this)}
          onCancel={this.onCancel.bind(this)}
        />
        
        {this.state.confirmShow &&
          <Confirm 
            show={this.state.confirmShow}
            title="送出表單確認"
            content="您是否確定要送出表單？"
            onConfirm={this.onSave.bind(this)}
            onCancel={this.hideConfirm.bind(this)}
            btnConfirm="確定"
            btnCancel="取消" 
          />
        }
      </div>
    )
  }
}

const keyList =[
  'point_no', 'ldate', 
  'catch_num1', 'catch_num2', 'catch_num3', 
  'catch_num4', 'catch_num5', 'catch_num6', 
  'change1', 'change2', 'change3', 
  'change4', 'change5', 'change6', 
  'check_lamp', 'rmk', 'discription', 
]

const catchList = [
  {label: '承接', key: 'catch_num2', type: 'catch_num2', show: 'vn2'},
  {label: '壁虎', key: 'catch_num3', type: 'catch_num3', show: 'vn3'},
  {label: '昆蟲', key: 'catch_num4', type: 'catch_num4', show: 'vn4'},
  {label: '鼠類', key: 'catch_num5', type: 'catch_num5', show: 'vn5'},
  {label: '其他', key: 'catch_num6', type: 'catch_num6', show: 'vn6'},
]

const replaceList = [
  {label: '捕蚊紙', key: 'change1', type: 'change1', show: 'vc1'},
  {label: '捕蚊燈管', key: 'change3', type: 'change3', show: 'vc3'},
  {label: '驅蚊燈管', key: 'change4', type: 'change4', show: 'vc4'},
  {label: '粘鼠板', key: 'change5', type: 'change5', show: 'vc5'},
  {label: '粘鼠板+防蟻措施', key: 'change6', type: 'change6', show: 'vc6'},
]

/*

<div className="field is-grouped" style={{marginLeft: '0px'}}>
          <p className="control">
            {comp ?
              <button className="button is-primary is-static">今日已完成記錄</button>
              : 
                isLoading ?
                  <button className="button is-loading is-primary" style={{width: '58px'}}></button>
                :
                  <button type="button" className="button is-primary" onClick={this.openConfirm.bind(this)}>儲存</button>
            }
          </p>
          <p>
            <button className="button" onClick={this.onCancel.bind(this)}>取消</button>
          </p>
        </div>
        */