import React from 'react'

export default class page2 extends React.Component {
  constructor(props) {
    super(props)
    this.state = {
      menu: [
        {
          name: 'list 1',
          menu: [
            {
              name: 'list 1-1',
              menu: null,
            },
          ],
        },
        {
          name: 'list 2',
          menu: [
            {
              name: 'list 2-1',
              menu: null,
            },
            {
              name: 'list 2-2',
              menu: null,
            },
            {
              name: 'list 2-3',
              menu: null,
            },
          ],
        },
      ],
    }
  }
  componentDidMount() {

  }
  render() {
    const { menu } = this.state
    const v_menu = (menu) => {
      const uuid = () => {
        function s4() {
          return Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
        }
        return s4() + s4() + '-' + s4() + '-' + s4() + '-' + s4() + '-' + s4() + s4() + s4();
      }
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