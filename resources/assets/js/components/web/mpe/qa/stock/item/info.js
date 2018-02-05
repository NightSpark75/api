/** 
 * Info.js
 */
import React from 'react'
import { Link } from 'react-router'
import axios from 'axios'

export default class Info extends React.Component {
  constructor(props) {
    super(props)

    this.state = {
      info: [],
      barcode: '',
      msg: '',
      msgType: '',
    }
  }

  barcodeChange(e) {
    let barcode = e.target.value
    this.setState({ barcode })
    if (barcode.length === 8) {
      this.getItemInfo(barcode)
    }
  }

  getItemInfo($barcode) {
    let self = this
    axios.get('/api/web/mpe/qa/stock/item/' + $barcode)
      .then(function (response) {
        if (response.status === 200) {
          let info = response.data;
          let msg = '';
          let msgType = '';
          if (!Object.keys(info).length) {
            msg = '查詢不到此條碼資訊!'
            msgType = 'warning'
          }
          self.setState({ info, msg, msgType })
        } else {
          console.log(response.data)
        }
      }).catch(function (error) {
        console.log(error)
      })
  }

  render() {
    const { info, barcode, msg, msgType } = this.state
    return (
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
              </div>
            </div>
          </div>
        </div>
        <div className="box" style={{ marginBottom: '10px' }}>
          <div className="field is-horizontal">
            <div className="field-body">
              <div className="field is-grouped">
                <div className="field" style={{ marginRight: '10px' }}>
                  <input type="text" className="input is-large"
                    value={barcode}
                    autoFocus
                    maxLength={8}
                    placeholder="掃描條碼"
                    onChange={this.barcodeChange.bind(this)}
                  />
                </div>
                {msg !== '' &&
                  <div className={"notification is-" + msgType} style={{ padding: '1rem 1rem 1rem 1rem' }}>
                    {msg}
                  </div>
                }
              </div>
            </div>
          </div>
          {Object.keys(info).length > 0 &&
            <table className="table is-bordered">
              <tbody>
                <tr>
                  <td>料號</td>
                  <td>{info.partno}</td>
                  <td>品名</td>
                  <td>{info.pname}</td>
                  <td>批號</td>
                  <td>{info.batch}</td>
                </tr>
                <tr>
                  <td>倉庫</td>
                  <td>{info.posit}</td>
                  <td>儲位</td>
                  <td>{info.storn}</td>
                  <td>狀態</td>
                  <td>{info.amt + '/' + info.usize + ' ' + info.unit + '(' + info.sta + ')'}</td>
                </tr>
              </tbody>
            </table>
          }
        </div>
      </div>
    )
  }
}