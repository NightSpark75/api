/** 
 * package.Duty.js
 */
import React from 'react'
import { Link } from 'react-router'
import axios from 'axios'

export default class Duty extends React.Component {
  constructor(props) {
    super(props)

    this.state = {
      ready: false,
      duty_list: [],
      showInfo: false,
      item: [],
    }
  }

  componentDidMount() {
    this.init()
  }

  init() {
    this.getDutyList()
  }

  getDutyList() {
    let self = this
    const { sno, psno } = this.props.params
    axios.get('/api/web/mpb/prod/package/duty/list/' + sno + '/' + psno)
      .then(function (response) {
        if (response.data.result) {
          self.setState({
            duty_list: response.data.duty,
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

  showProcessInfo(item) {
    this.setState({
      showInfo: true,
      item: item,
    })
  }

  hideProcessInfo() {
    this.setState({
      showInfo: false,
      item: [],
    })
  }

  render() {
    const { duty_list } = this.state
    return (
      <div>
        <div className="box" style={{ marginTop: '10px', marginBottom: '10px' }}>
          <p className="control">
            <Link className="button is-medium" to="/auth/web/mpb/package/list">&larr; 分包裝工單清單</Link>
          </p>
        </div>
        {this.state.showInfo &&
          <article className="message is-info" style={{ marginBottom: '10px' }}>
            <div className="message-header is-size-4">
              <p>製程單號{this.state.item.sno}詳細資訊</p>
              <button className="delete" aria-label="delete" onClick={this.hideProcessInfo.bind(this)}></button>
            </div>
            <div className="message-body is-size-4">
              {this.state.item.info}
            </div>
          </article>
        }
        {duty_list.length > 0 ?
          <div>
            <div className="column is-hidden-desktop">
              <label className="is-size-4">請將畫面轉橫</label>
            </div>
            <table className="table is-bordered is-striped is-fullwidth is-size-4 is-hidden-touch">
              <thead>
                <tr>
                  <th>品名規格</th>
                  <th>途程</th>
                  <th>班別</th>
                  <th width="62"></th>
                </tr>
              </thead>
              <tbody>
                {duty_list.map((item, index) => (
                  <tr key={index}>
                    <td>{item.iname}</td>
                    <td>{item.pname}</td>
                    <td>
                      {item.duty === '1' && "早班"}
                      {item.duty === '2' && "中班"}
                      {item.duty === '3' && "晚班"}
                    </td>
                    <td>
                      <Link className="button is-primary is-medium"
                        to={"/auth/web/mpb/package/working/"
                          + item.sno + "/" + item.psno + "/" + item.pgno + "/" + item.duty + "/" + item.gro}>報工</Link>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
          :
          <div className="notification is-warning is-size-4" style={{ padding: '1rem 1rem 1rem 1rem' }}>
            目前尚無生產資訊...
                    </div>
        }
      </div>
    )
  }
}