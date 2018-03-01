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

export default class Pointlog extends React.Component {
  constructor(props) {
    super(props)

    this.state = {
      point: [],
      point_no: '',
      point_info: {},
      scan: false,
      scan_message: '',
      catchlog_show: false,
      templog_show: false,
      wetestlog_show: false,
      refrilog_show: false,
      prerilog_show: false,
      pressurelog_show: false,
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
    this.setState({
      catchlog_show: false,
      templog_show: false,
      wetestlog_show: false,
      refrilog_show: false,
      pressurelog_show: false,
      point_no: '',
      scan: true,
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
    const { point_info, scan } = this.state
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

