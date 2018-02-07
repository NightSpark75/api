/** 
 * NavPage.js 
 */
import React from "react"
import { Link } from "react-router"
import axios from 'axios'

export default class NavPage extends React.Component {
  constructor(props) {
    super(props)

    this.state = {
      user: [],
      nav: '',
      menu: false,
    }
  }

  componentDidMount() {
    let self = this
    axios.get('/api/web/user/info', new FormData(), {
      method: 'get',
    }).then(function (response) {
      if (response.data.session) {
        self.setState({ user: response.data.info })
      } else {
        console.log('miss session, need login!')
        window.location = '/web/login/ppm'
      }
    }).catch(function (error) {
      console.log(error)
    })
  }

  onLogout(event) {
    if (confirm('您確定要登出系統？')) {
      window.location = '/api/web/logout'
    }
  }

  navMenu(event) {
    let nav = this.state.nav === '' ? 'is-active' : ''
    this.setState({ nav: nav })
  }

  menu() {
    this.setState({menu: !this.state.menu})
  }

  render() {
      let isActive = this.state.menu? ' is-active': ''
    return (
      <div>
        <nav className="navbar is-primary is-transparent">
          <div className="container">
            <div className="navbar-brand">
              <div className="nav-item">
                  {this.state.user.length === 0 ? '資料讀取中...' : this.state.user['user_name'] + ' 您好'}
              </div>
              <div className="navbar-burger burger" onClick={this.menu.bind(this)}>
                <span></span>
                <span></span>
                <span></span>
              </div>
            </div>
            <div className={"navbar-menu" + isActive}>
              <div className="navbar-end">
                <Link className="navbar-item" to="/auth/web/menu">
                  <span className="is-size-4">回功能頁</span>
                </Link>
                <a className="navbar-item" onClick={this.onLogout.bind(this)} href="#">
                  <span className="is-size-4">登出</span>
                </a>
              </div>
            </div>
          </div>
        </nav>
        <section>
          <div className="container">
            {this.props.children}
          </div>
        </section>
      </div>
    )
  }
}

/*
<nav className="navbar is-primary">
  <div className="container">
    <div className="navbar-start">
      <div className="nav-item">
          {this.state.user.length === 0 ? '資料讀取中...' : this.state.user['user_name'] + ' 您好'}
      </div>
    </div>
    <div className="navbar-end">
      <Link className="navbar-item" to="/auth/web/menu">
        <span className="is-size-4">回功能頁</span>
      </Link>
      <a className="navbar-item" onClick={this.onLogout.bind(this)} href="#">
        <span className="is-size-4">登出</span>
      </a>
    </div>
  </div>
</nav>

*/