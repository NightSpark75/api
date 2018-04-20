/** 
 * Pointlog.js
 */
import React from 'react'
import { Link } from 'react-router'
import axios from 'axios'
import Catchlog from './catchlog'
import Templog from './Templog'
import Wetestlog from './Wetestlog'
import Refrilog from './Refrilog'
import Pressurelog from './Pressurelog'

const pointType = {
  C: 'catchlog_show',
  P: 'pressurelog_show',
  W: 'wetestlog_show',
  R: 'refrilog_show',
  T: 'templog_show',
}

const mcuList = ['10A1', '10A2', '10A3']
const floorList = ['1F', '2F', '3F', '4F']

export default class Pointlog extends React.Component {
  constructor(props) {
    super(props)

    this.state = {
      point: [],
      point_no: '',
      point_info: {},
      unrecorded: [],
      scan: false,
      scan_message: '',
      catchlog_show: false,
      templog_show: false,
      wetestlog_show: false,
      refrilog_show: false,
      prerilog_show: false,
      pressurelog_show: false,
      mcu: '10A1',
      floor: '1F',
    }
  }

  init() {
    let self = this
    axios.get('/api/web/mpz/pointlog/init', null, {
      method: 'get',
    }).then(function (response) {
      if (response.data.result) {
        self.setState({
          point: response.data.point,
          scan: true,
          unrecorded: response.data.unrecorded,
        })
        console.log(response.data)
      } else {
        console.log(response.data)
        window.location = '/web/login/ppm'
      }
    }).catch(function (error) {
      console.log(error)
    })
  }

  componentDidMount() {
    this.init()
  }

  scanChange(e) {
    let point_no = e.target.value
    this.setState({ point_no: point_no }, () => { this.pointSearch(point_no) })

  }

  pointSearch(point_no) {
    let list = this.state.point
    list.map((item, index) => {
      if (item.point_no === point_no) {
        this.setState({
          point_info: item,
          [pointType[item.point_type]]: true,
          scan: false,
        })
      }
    })
  }

  onCancel() {
    this.init()
    this.setState({
      catchlog_show: false,
      templog_show: false,
      wetestlog_show: false,
      refrilog_show: false,
      pressurelog_show: false,
      point_no: '',
    })
  }

  componentMsg(msg) {
    this.setState({ scan_message: msg })
  }

  goMenu() {
    window.location = '/auth/web/menu'
  }

  render() {
    let today = new Date()
    const { point_info, scan, unrecorded, mcu, floor } = this.state
    return (
      <div>
        <div className="box" style={{ marginTop: '10px', marginBottom: '10px' }}>
          <p className="control">
            <Link className="button" to="/auth/web/menu">&larr; 功能選單</Link>
          </p>
        </div>
        {scan &&
          <div className="box" style={{ marginBottom: '10px' }}>
            <div className="field is-horizontal">
              <div className="field-body">
                <div className="field is-grouped">
                  <div className="field" style={{ marginRight: '10px' }}>
                    <input type="text" className="input is-large" placeholder="掃描條碼"
                      maxLength={20}
                      value={this.state.point_no}
                      onChange={this.scanChange.bind(this)}
                    />
                  </div>
                  {this.state.scan_message !== '' &&
                    <div className="notification is-warning" style={{ padding: '1rem 1rem 1rem 1rem' }}>
                      {this.state.scan_message}
                    </div>
                  }
                </div>
              </div>
            </div>
          </div>
        }
        {unrecorded.length > 0 && 
          !this.state.catchlog_show &&
          !this.state.refrilog_show &&
          !this.state.templog_show &&
          !this.state.wetestlog_show &&
          !this.state.pressurelog_show &&
          <div>
            {mcuList.map((item) => (
              getMcuButton(item, mcu, () => this.setState({mcu: item}))
            ))}
            {floorList.map((item) => (
              getFloorButton(item, floor, () => this.setState({floor: item}))
            ))}
            <table className="table is-bordered table is-fullwidth" style={{marginTop: 10}}>
              <thead>
                <tr>
                  <td colSpan="4">
                    未記錄點位
                  </td>
                </tr>  
                <tr>
                  <td>點位名稱</td>
                  <td>上午[0600 ~ 0900]</td>
                  <td>下午1[1200 ~ 1400]</td>
                  <td>下午2[1630 ~ 1730]</td>
                </tr>
              </thead>
              <tbody>
                {unrecorded.map((item) => (
                  getUnrecorded(item, mcu, floor)
                ))}
              </tbody>
            </table>
          </div>
        }
        {this.state.catchlog_show &&
          <Catchlog
            pointInfo={point_info}
            onCancel={this.onCancel.bind(this)}
            sendMsg={this.componentMsg.bind(this)}
          >
          </Catchlog>
        }
        {this.state.templog_show &&
          <Templog
            pointInfo={point_info}
            onCancel={this.onCancel.bind(this)}
            sendMsg={this.componentMsg.bind(this)}
          >
          </Templog>
        }
        {this.state.wetestlog_show &&
          <Wetestlog
            pointInfo={point_info}
            onCancel={this.onCancel.bind(this)}
            sendMsg={this.componentMsg.bind(this)}
          >
          </Wetestlog>
        }
        {this.state.refrilog_show &&
          <Refrilog
            pointInfo={point_info}
            onCancel={this.onCancel.bind(this)}
            sendMsg={this.componentMsg.bind(this)}
          >
          </Refrilog>
        }
        {this.state.pressurelog_show &&
          <Pressurelog
            pointInfo={point_info}
            onCancel={this.onCancel.bind(this)}
            sendMsg={this.componentMsg.bind(this)}
          >
          </Pressurelog>
        }
      </div>
    )
  }
}

function getUnrecorded(item, mcu, floor) {
  if (item.mcu === mcu && item.floor === floor) {
    return (
      <tr key={item.point_no}>
        <td>{item.point_name}</td>
        <td>{item.mo}</td>
        <td>{item.af}</td>
        <td>{item.ev}</td>
      </tr>
    )
  }
}

function getMcuButton(item, mcu, click) {
  const cla = item === mcu ? 'button is-primary': 'button'
  return (
    <button 
      key={item}
      className={cla}
      style={{marginRight: 10}}
      onClick={click}
    >
      {item}
    </button>
  )
}

function getFloorButton(item, floor, click) {
  const cla = item === floor ? 'button is-info': 'button'
  return (
    <span 
      key={item}
      className={cla}
      style={{marginRight: 10}}
      onClick={click}
    >
      {item}
    </span>
  )
}