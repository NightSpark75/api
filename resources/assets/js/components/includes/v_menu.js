import React from 'react'

export default class menu extends React.Component {
  constructor(props) {
    super(props)
    this.state = {}
  }
  componentDidMount() {

  }
  render() {
    const { menu } = this.props
    const v_menu = (menu) => {
      return (
        menu.map((item, index) => (
          <div className="v-menu-item" key={index}>
            <a>
              {item.name}
              {item.menu &&
                <span className="icon">
                  <i className="fa fa-angle-right"></i>
                </span>
              }
            </a>
            {item.menu &&
              <div className="v-menu">
                <div className="v-menu-list">
                  <div className="v-menu-content">
                    {v_menu(item.menu)}
                  </div>
                </div>
              </div>
            }
          </div>
        ))
      )
    }
    return (
      <div className="v-menu">
        <div className="v-menu-list">
          <div className="v-menu-content">
            {v_menu(menu)}
          </div>
        </div>
      </div>
    )
  }
}