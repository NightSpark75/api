/** 
 * Password.js
 */
import React from "react"
import axios from 'axios'

export default class Password extends React.Component {
  constructor(props) {
    super(props)

    this.state = {
      system: '',
      account: '',
      old: '',
      changed: '',
      confirm: '',
      buttonState: '',
      msg: '',
      msg_type: '',
      isSuccess: false,
    }
  }

  setMsg(type = '', msg = '') {
    this.setState({
      msg_type: type,
      msg: msg,
    })
  }

  componentDidMount() {
    console.log('1234')
    this.setState({
      system: this.props.params.system,
    })
  }

  onAccountChange(event) {
    event.preventDefault()
    this.setState({ account: event.target.value })
  }

  onOldChange(event) {
    this.setState({ old: event.target.value })
  }

  onNewChange(event) {
    this.setState({ changed: event.target.value }, () => {
      const conf = this.state.confirm
      const pass = this.state.changed
      if (conf.length > 0 && conf !== pass) {
        this.setMsg('danger', '新密碼與確認密碼不同')
      } else {
        this.setMsg('', '')
      }
    })
  }

  onConfirmChange(event) {
    this.setState({ confirm: event.target.value }, () => {
      const conf = this.state.confirm
      const pass = this.state.changed
      if (conf !== pass) {
        this.setMsg('danger', '新密碼與確認密碼不同')
      } else {
        this.setMsg('', '')
      }
    })
  }

  checkInput() {
    const { account, old, changed, confirm } = this.state
    if (account === '') {
      this.setMsg('warning', '請輸入帳號')
      return false
    } 
    if (old === '') {
      this.setMsg('warning', '請輸入舊密碼')
      return false
    }
    if (changed === '') {
      this.setMsg('warning', '請輸入新密碼')
      return false
    }
    if (confirm === '') {
      this.setMsg('warning', '請輸入確認密碼')
      return false
    }
    if (old === changed) {
      this.setMsg('warning', '新密碼與舊密碼相同')
      return false
    }
    return true
  }

  onLogin(event) {
    event.preventDefault()
    const { account, old, changed, confirm, system } = this.state
    let self = this
    if (!this.checkInput()) {
      return
    }
    this.setState({ buttonState: 'submit'})
    let form_data = new FormData()
    form_data.append('account', account)
    form_data.append('old', old)
    form_data.append('changed', changed)
    form_data.append('confirm', confirm)
    form_data.append('system', system)
    axios.post('/api/web/password', form_data, {
      method: 'post',
    }).then(function (response) {
      if (response.data.result === true) {
        console.log(response.data)
        self.setState({ isSuccess: true })
        self.setMsg('success', '密碼修改成功!')
      } else {
        console.log(response.data)
        self.setMsg('danger', response.data.msg)
        self.setState({ buttonState: '', isSuccess: false })
      }
    }).catch(function (error) {
      console.log(error)
      self.setMsg('danger', error.message)
      self.setState({ buttonState: '', isSuccess: false })
    })
  }
  render() {
    return (
      <div className="columns">
        <div className="column is-half is-offset-one-quarter">
          {!this.state.isSuccess && 
            <form onSubmit={this.onLogin.bind(this)}>
              <div className="field">
                <label className="label is-medium">請輸入帳號密碼登入</label>
                <div className="control has-icons-left">
                  <input className="input is-medium" type="text"
                    placeholder="請輸入帳號"
                    maxLength="20"
                    onChange={this.onAccountChange.bind(this)}
                  />
                  <span className="icon is-small is-left">
                    <i className="fa fa-user"></i>
                  </span>
                </div>
              </div>
              <div className="field">
                <div className="control has-icons-left">
                  <input className="input is-medium" type="password"
                    placeholder="請輸入舊密碼"
                    maxLength="30"
                    onChange={this.onOldChange.bind(this)}
                  />
                  <span className="icon is-small is-left">
                    <i className="fa fa-lock"></i>
                  </span>
                </div>
              </div>
              <div className="field">
                <div className="control has-icons-left">
                  <input className="input is-medium" type="password"
                    placeholder="請輸入新密碼"
                    maxLength="30"
                    onChange={this.onNewChange.bind(this)}
                  />
                  <span className="icon is-small is-left">
                    <i className="fa fa-lock"></i>
                  </span>
                </div>
              </div>
              <div className="field">
                <div className="control has-icons-left">
                  <input className="input is-medium" type="password"
                    placeholder="請輸入確認密碼"
                    maxLength="30"
                    onChange={this.onConfirmChange.bind(this)}
                  />
                  <span className="icon is-small is-left">
                    <i className="fa fa-lock"></i>
                  </span>
                </div>
              </div>
              {this.state.buttonState === 'submit' ?
                <button className="button is-loading is-primary is-medium is-fullwidth"></button>
              :
                <button type="submit" className="button is-primary is-medium is-fullwidth">變更密碼</button>
              }
            </form>
          }
          {this.state.msg !== '' &&
            <div className={"notification is-" + this.state.msg_type} style={{ marginTop: '10px' }}>
              {this.state.msg}
            </div>
          }
          {this.state.isSuccess && 
            <a href="/web/login/ppm" className="button is-info is-medium is-fullwidth" style={{fontSize: '18px'}}>返回登入頁面</a>
          }
        </div>
      </div>
    )
  }
}