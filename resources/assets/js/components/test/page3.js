import React from 'react'

export default class page3 extends React.Component {
  constructor(props) {
    super(props)
    this.state = {}
  }
  componentDidMount() {

  }
  render() {
    const sub_menu = (id) => (
      <div className="v-menu" id={id} role="menu">
        <div className="v-menu-list">
          <div className="v-menu-content">
            <div className="v-menu-item">
              <a>
                Dropdown item
                                <span className="icon">
                  <i className="fa fa-angle-right"></i>
                </span>
              </a>
            </div>
            <div className="v-menu-item">
              <a>
                Other dropdown item
                                <span className="icon">
                  <i className="fa fa-angle-right"></i>
                </span>
              </a>
            </div>
            <div className="v-menu-item">
              <a>
                Active dropdown item
                                <span className="icon">
                  <i className="fa fa-angle-right"></i>
                </span>
              </a>
            </div>
            <div className="v-menu-item">
              <a>
                Other dropdown item
                                <span className="icon">
                  <i className="fa fa-angle-right"></i>
                </span>
              </a>
            </div>
            <hr className="v-menu-divider" />
            <div className="v-menu-item">
              <a>
                With a divider
                                <span className="icon">
                  <i className="fa fa-angle-right"></i>
                </span>
              </a>
            </div>
          </div>
        </div>
      </div>
    )
    return (
      <div className="v-menu" role="menu">
        <div className="v-menu-list">
          <div className="v-menu-content">
            <div className="v-menu-item">
              <a>
                Dropdown item Dropdown item Dropdown item Dropdown item
                                <span className="icon">
                  <i className="fa fa-angle-right"></i>
                </span>
              </a>
              {sub_menu('sub_menu1')}
            </div>
            <div className="v-menu-item">
              <a>
                Other dropdown item
                                <span className="icon">
                  <i className="fa fa-angle-right"></i>
                </span>
              </a>
              {sub_menu('sub_menu2')}
            </div>
            <div className="v-menu-item">
              <a>
                Active dropdown item
                                <span className="icon">
                  <i className="fa fa-angle-right"></i>
                </span>
              </a>
              {sub_menu('sub_menu3')}
            </div>
            <div className="v-menu-item">
              <a>
                Other dropdown item
                                <span className="icon">
                  <i className="fa fa-angle-right"></i>
                </span>
              </a>
              {sub_menu('sub_menu4')}
            </div>
            <hr className="v-menu-divider" />
            <div className="v-menu-item">
              <a>
                With a divider
                                <span className="icon">
                  <i className="fa fa-angle-right"></i>
                </span>
              </a>
              {sub_menu('sub_menu5')}
            </div>
          </div>
        </div>
      </div>
    )
  }
}