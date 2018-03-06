/** 
 * clean.Working.js
 */
import React from 'react'
import { Link } from 'react-router'
import axios from 'axios'

export default class Job extends React.Component {
  constructor(props) {
    super(props)

    this.state = {
      sno: this.props.params.sno,
      deptno: this.props.params.deptno,
      prod: {bno: ''},
      waiting_list: [],
      working_list: [],
      updated: false,
      lock: false,
    }
  }

  componentDidMount() {
    this.getMember()
    this.timer = setInterval(this.getMember.bind(this), 5000)
  }

  componentWillUnmount() {
    this.timer && clearInterval(this.timer)
  }

  getMember() {
    const { sno, deptno } = this.state
    let self = this
    axios.get('/api/web/mpb/prod/clean/member/' + sno + '/' + deptno)
      .then(function (response) {
        if (response.data.result) {
          if (response.data.non_check) {
            window.location = '/auth/web/mpb/clean/dept/' + sno
          }
          if (!self.state.updated) {
            self.setState({
              waiting_list: response.data.waiting,
              working_list: response.data.working,
              prod: response.data.prod
            })
          } else {
            self.setState({
              updated: false,
            })
          }
          console.log(response.data)
        } else {
          console.log(response.data)
          window.location = '/web/login/ppm'
        }
      }).catch(function (error) {
        console.log(error)
      })
  }

  updateWorking(empno, action, event) {
    const { waiting_list, working_list, sno } = this.state
    this.setState({ lock: true })
    let self = this
    let form_data = new FormData()
    form_data.append('sno', sno)
    form_data.append('empno', empno)
    axios.post('/api/web/mpb/prod/clean/working/' + action, form_data)
      .then(function (response) {
        if (response.data.result) {
          console.log(response.data)
          self.setState({ lock: false })
        } else {
          console.log(response.data)
          window.location = '/web/login/ppm'
        }
      }).catch(function (error) {
        console.log(error)
      })
    if (action === 'join') {
      self.updateList(working_list, waiting_list, action, empno)
    } else {
      self.updateList(waiting_list, working_list, action, empno)
    }
  }

  updateList(add, remove, action, empno) {
    for (let r = 0; r < remove.length; r++) {
      if (remove[r]['empno'] === empno) {
        add.push(remove[r])
        remove.splice(r, 1)
        if (action === 'join') {
          this.setState({
            working_list: add,
            waiting_list: remove,
            updated: true,
          })
        } else {
          this.setState({
            working_list: remove,
            waiting_list: add,
            updated: true,
          })
        }
        return
      }
    }
  }

  render() {
    const { job_list, lock, sno, deptno } = this.state
    let dno = deptno.substr(0, 4) + '0'
    return (
      <div>
        <div className="box" style={{ marginTop: '10px', marginBottom: '10px' }}>
          <div className="level">
            <div className="level-left">
              <div className="level-item">
                <Link className="button is-medium" to={"/auth/web/mpb/clean/dept/" + sno + "/" + dno}>&larr; 選擇股別</Link>
              </div>
            </div>
          </div>
        </div>
        <div className="column is-hidden-desktop">
          <label className="is-size-4">請將畫面轉橫</label>
        </div>
        <span className="tag is-info is-large" style={{marginBottom: '10px'}}>{'[' + prod.bno + ']'}</span>
        <div className="columns is-hidden-touch">
          <div className="column">
            <article className="message is-success">
              <div className="message-header">
                <h4 className="title is-3 has-text-white-ter">待派工生產人員</h4>
              </div>
              <div className="message-body" style={{ height: '450px' }}>
                <div className="field is-grouped is-grouped-multiline">
                  {this.state.waiting_list.map((item, index) => (
                    <p className="control" key={index}>
                      <button className="button is-success is-large" disabled={lock}
                        onClick={this.updateWorking.bind(this, item.empno, 'join')}
                      >
                        {item.ename}
                      </button>
                    </p>
                  ))}
                </div>
              </div>
            </article>
          </div>
          <div className="column">
            <article className="message is-primary">
              <div className="message-header">
                <h4 className="title is-3 has-text-white-ter">目前生產人員</h4>
              </div>
              <div className="message-body" style={{ height: '450px' }}>
                <div className="field is-grouped is-grouped-multiline">
                  {this.state.working_list.map((item, index) => (
                    <p className="control" key={index}>
                      <button className="button is-primary is-large" disabled={lock}
                        onClick={this.updateWorking.bind(this, item.empno, 'leave')}
                      >
                        {item.ename}
                      </button>
                    </p>
                  ))}
                </div>
              </div>
            </article>
          </div>
        </div>
      </div>
    )
  }
}