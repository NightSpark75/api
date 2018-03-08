/** 
 * Prework.Job.js
 */
import React from 'react'
import { Link } from 'react-router'
import axios from 'axios'

export default class Job extends React.Component {
  constructor(props) {
    super(props)

    this.state = {
      ready: false,
      job_list: [],
      item: [],
    }
  }

  componentDidMount() {
    this.init()
    this.timer = setInterval(this.updateJobList.bind(this), 5000)
  }

  componentWillUnmount() {
    this.timer && clearInterval(this.timer)
  }

  init() {
    this.getJobList()
  }

  getJobList() {
    let self = this
    axios.get('/api/web/mpb/prod/prework/list')
      .then(function (response) {
        if (response.data.result) {
          self.setState({
            job_list: response.data.job_list,
          })
          console.log(response.data)
        } else {
          console.log(response.data)
        }
      }).catch(function (error) {
        console.log(error)
      })
  }

  updateJobList() {
    let self = this
    let job_list = JSON.stringify(this.state.job_list)
    let form_data = new FormData()
    form_data.append('job_list', job_list)
    axios.post('/api/web/mpb/prod/prework/compare', form_data)
      .then(function (response) {
        if (response.data.result) {
          self.setState({
            job_list: response.data.job_list,
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
    const { job_list } = this.state
    return (
      <div>
        <div className="box" style={{ marginTop: '10px', marginBottom: '10px' }}>
          <p className="control">
            <Link className="button is-medium" to="/auth/web/menu">&larr; 功能選單</Link>
          </p>
        </div>
        {job_list.length > 0 ?
          <div>
            <div className="column is-hidden-desktop">
              <label className="is-size-4">請將畫面轉橫</label>
            </div>
            <table className="table is-bordered is-striped is-fullwidth is-size-4 is-hidden-touch">
              <thead>
                <tr>
                  <th width="140">批號</th>
                  <th>品名</th>
                  <th width="100">順序</th>
                  <th>途程</th>
                  <th width="149"></th>
                </tr>
              </thead>
              <tbody>
                {job_list.map((item, index) => (
                  <tr key={index}>
                    <td>{item.bno}</td>
                    <td>{item.iname}</td>
                    <td>{item.psno}</td>
                    <td>{item.pname}</td>
                    <td>
                      <Link className="button is-primary is-medium"
                        to={"/auth/web/mpb/prework/working/" + item.sno + "/" + item.psno}>前置作業</Link>
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