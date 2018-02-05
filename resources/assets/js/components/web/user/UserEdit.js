/** 
 * UserEdit.js
 */
import React from "react"
import axios from 'axios'

export default class UserEdit extends React.Component {
  constructor(props) {
    super(props)
    this.state = {
      user_id: '',
      user_name: '',
      user_pw: '',
      conf_pw: '',
      pw_ctrl: 'Y',
      s_class: 'Y',
      state: 'Y',
      rmk: '',
      pw_ctrl_y: 'Y',
      pw_ctrl_n: 'N',
      class_y: 'Y',
      class_n: 'N',
      state_y: 'Y',
      state_n: 'N',
      isLoading: false,
      msg_type: '',
      msg: ''
    }

    this.editList = this.props.editList.bind(this)
    this.onHide = this.props.onHide.bind(this)
  }

  setMsg(type = '', msg = '') {
    this.setState({
      msg_type: type,
      msg: msg,
    })
  }

  initState() {
    this.setState({
      user_id: '',
      user_name: '',
      user_pw: '',
      conf_pw: '',
      pw_ctrl: 'Y',
      s_class: 'Y',
      state: 'Y',
      rmk: '',
      pw_ctrl_y: 'Y',
      pw_ctrl_n: 'N',
      class_y: 'Y',
      class_n: 'N',
      state_y: 'Y',
      state_n: 'N',
      isLoading: false,
      msg_type: '',
      msg: ''
    })
  }

  setData(item) {
    this.setState({
      user_id: item['user_id'],
      user_name: item['user_name'],
      user_pw: item['user_pw'],
      conf_pw: item['user_pw'],
      pw_ctrl: item['pw_ctrl'],
      s_class: item['class'],
      state: item['state'],
      rmk: item['rmk'] = '',
    })
  }

  userIdChange(e) {
    return null
  }

  userNameChange(e) {
    this.setState({ user_name: e.target.value })
  }

  userPwChange(e) {
    this.setState({ user_pw: e.target.value })
  }

  confPwChange(e) {
    this.setState({ conf_pw: e.target.value })
  }

  pwCtrlChange(e) {
    this.setState({ pw_ctrl: e.target.value })
  }

  classChange(e) {
    this.setState({ s_class: e.target.value })
  }

  stateChange(e) {
    this.setState({ state: e.target.value })
  }

  rmkChange(e) {
    this.setState({ rmk: e.target.value })
  }

  formValidate(obj) {
    const { user_id, user_name, user_pw, conf_pw, pw_ctrl, s_class, state, rmk } = obj
    let msg = []
      (user_id === '') ? msg.push('請輸入帳號') : null
        (user_name === '') ? msg.push('請輸入姓名') : null
          (user_pw === '') ? msg.push('請輸入密碼') : null
            (conf_pw === '') ? msg.push('請輸入確認密碼') : null
              (pw_ctrl === '') ? msg.push('請選擇是否密碼永久有效') : null
                (s_class === '') ? msg.push('請選擇是否使用User Menu') : null
                  (state === '') ? msg.push('請選擇使用狀態') : null
                    (user_pw !== conf_pw) ? msg.push('密碼與確認密碼不同') : null
    let newText = msg.map((i, index) => {
      return <div key={index}>{i}<br /></div>
    })
    return newText
  }

  onUpdate() {
    this.setState({ isLoading: true })
    const { user_id, user_name, user_pw, conf_pw, pw_ctrl, s_class, state, rmk } = this.state
    let self = this
    let errorMsg = this.formValidate(this.state)
    if (errorMsg.length > 0) {
      this.setMsg('danger', errorMsg)
      this.setState({ isLoading: false })
      return
    }
    let data = {
      user_id: user_id,
      user_name: user_name,
      user_pw: user_pw,
      pw_ctrl: pw_ctrl,
      class: s_class,
      state: state,
      rmk: rmk,
    }
    axios.put('/api/web/user/update', data)
      .then(function (response) {
        if (response.data.result) {
          self.setMsg('success', response.data.msg)
          self.setState({ buttonState: 'complete' })
          self.initState()
          self.editList(response.data.user)
          self.onHide()
        } else {
          self.setMsg('danger', response.data.msg)
          self.setState({ buttonState: 'default' })
        }
        self.setState({ isLoading: false })
      }).catch(function (error) {
        console.log(error)
        self.setMsg('danger', error)
        self.setState({ isLoading: false })
      })
  }

  render() {
    let isLoading = this.state.isLoading
    let show = this.props.showModal ? 'is-active' : ''
    return (
      <div className={"modal " + show}>
        <div className="modal-background"></div>
        <div className="modal-card">
          <header className="modal-card-head">
            <p className="modal-card-title">新增使用者</p>
          </header>
          <section className="modal-card-body">
            <div className="field is-horizontal">
              <div className="field-label">使用者帳號</div>
              <div className="field-body">
                <div className="field is-expanded">
                  <input className="input" type="text" placeholder="請輸入使用者帳號"
                    value={this.state.user_id}
                    onChange={this.userIdChange.bind(this)}
                    required
                  />
                </div>
              </div>
            </div>
            <div className="field is-horizontal">
              <div className="field-label">使用者姓名</div>
              <div className="field-body">
                <div className="field is-expanded">
                  <input className="input" type="text" placeholder="請輸入使用者姓名"
                    value={this.state.user_name}
                    onChange={this.userNameChange.bind(this)}
                  />
                </div>
              </div>
            </div>
            <div className="field is-horizontal">
              <div className="field-label">密碼</div>
              <div className="field-body">
                <div className="field is-expanded">
                  <input className="input" type="password" placeholder="請輸入密碼"
                    value={this.state.user_pw}
                    onChange={this.userPwChange.bind(this)}
                  />
                </div>
              </div>
            </div>
            <div className="field is-horizontal">
              <div className="field-label">確認密碼</div>
              <div className="field-body">
                <div className="field is-expanded">
                  <input className="input" type="password" placeholder="請輸入確認密碼"
                    value={this.state.conf_pw}
                    onChange={this.confPwChange.bind(this)}
                  />
                </div>
              </div>
            </div>
            <div className="field is-horizontal">
              <div className="field-label">
                <label className="label">密碼永久有效</label>
              </div>
              <div className="field-body">
                <div className="field is-narrow">
                  <div className="control">
                    <label className="radio">
                      <input type="radio"
                        name="e_pw_ctrl_y"
                        value={this.state.pw_ctrl_y}
                        checked={this.state.pw_ctrl === this.state.pw_ctrl_y}
                        onChange={this.pwCtrlChange.bind(this)}
                      />
                      {this.state.pw_ctrl_y}
                    </label>
                    <label className="radio">
                      <input type="radio"
                        name="e_pw_ctrl_n"
                        value={this.state.pw_ctrl_n}
                        checked={this.state.pw_ctrl === this.state.pw_ctrl_n}
                        onChange={this.pwCtrlChange.bind(this)}
                      />
                      {this.state.pw_ctrl_n}
                    </label>
                  </div>
                </div>
              </div>
            </div>
            <div className="field is-horizontal">
              <div className="field-label">
                <label className="label">使用User Menu</label>
              </div>
              <div className="field-body">
                <div className="field is-narrow">
                  <div className="control">
                    <label className="radio">
                      <input type="radio"
                        name="e_class_y"
                        value={this.state.class_y}
                        checked={this.state.s_class === this.state.class_y}
                        onChange={this.classChange.bind(this)}
                      />
                      {this.state.class_y}
                    </label>
                    <label className="radio">
                      <input type="radio"
                        name="e_class_n"
                        value={this.state.class_n}
                        checked={this.state.s_class === this.state.class_n}
                        onChange={this.classChange.bind(this)}
                      />
                      {this.state.class_n}
                    </label>
                  </div>
                </div>
              </div>
            </div>
            <div className="field is-horizontal">
              <div className="field-label">
                <label className="label">使用狀態</label>
              </div>
              <div className="field-body">
                <div className="field is-narrow">
                  <div className="control">
                    <label className="radio">
                      <input type="radio"
                        name="e_state_y"
                        value={this.state.state_y}
                        checked={this.state.state === this.state.state_y}
                        onChange={this.stateChange.bind(this)}
                      />
                      {this.state.state_y}
                    </label>
                    <label className="radio">
                      <input type="radio"
                        name="e_state_n"
                        value={this.state.state_n}
                        checked={this.state.state === this.state.state_n}
                        onChange={this.stateChange.bind(this)}
                      />
                      {this.state.state_n}
                    </label>
                  </div>
                </div>
              </div>
            </div>

            <div className="field is-horizontal">
              <div className="field-label is-normal">
                <label className="label">備註</label>
              </div>
              <div className="field-body">
                <div className="field">
                  <div className="control">
                    <textarea className="textarea" placeholder="請輸入備註"
                      onChange={this.rmkChange.bind(this)}
                      value={this.state.rmk || ''}
                    >
                    </textarea>
                  </div>
                </div>
              </div>
            </div>
            {this.state.msg !== '' &&
              <div className="notification is-warning" style={{ marginTop: '10px' }}>
                {this.state.msg}
              </div>
            }
          </section>
          <footer className="modal-card-foot">
            {isLoading ?
              <button className="button is-loading is-primary"></button>
              :
              <button type="button" className="button is-primary" onClick={this.onUpdate.bind(this)}>新增</button>
            }
            <button className="button" onClick={this.props.onHide}>取消</button>
          </footer>
        </div>
      </div>
    )
  }
}