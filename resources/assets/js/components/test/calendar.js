import React from 'react'
import { Link } from 'react-router'
import axios from 'axios'

const col = ['1', '2', '3', '4', '5', '6', '7']
//const col = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12']


export default class Calendar extends React.Component {
  constructor(props) {
    super(props)
    this.state = {
      show: true,
      width: 0,
      leftLayout: 0,
      rightLayout: 0,
      columnWidth: 0,
    }
    this.checkWidth = this.checkWidth.bind(this)
    this.resetWidth = this.resetWidth.bind(this)
  }
  componentDidMount() {
    this.resetWidth()
    window.addEventListener("resize", this.checkWidth);
  }

  checkWidth() {
    const width = this.refs.container.offsetWidth
    if (width <= 768) {
      this.setState({ show: false })
    } else {
      this.setState({ show: true}, () => this.resetWidth())
    }
  }

  resetWidth() {
    const width = this.refs.container.offsetWidth
    const height = this.refs.container.offsetWidth
    const leftLayout = (width / 10) * 2
    const rightLayout = (width / 10) * 8
    this.setState({
      width: width,
      leftLayout: leftLayout,
      rightLayout: rightLayout,
      columnWidth: rightLayout / 7,
    })
  }

  render() {
    return (
      <div ref="container" className="widescreen-only">
        {this.state.show &&
          <div>
            <div ref="one" style={{ backgroundColor: '#BFF' }}>
              calendar
            </div>
            <div style={{ width: this.state.leftLayout, float: 'left' }}>
              left layout
            </div>
            <div className="columns is-gapless" 
              style={{ 
                width: this.state.rightLayout, 
                float: 'lerft' ,
                height: 200,
              }}
            >
              {col.map((item, index) => (
                <div key={index} className="column"
                  style={item === '1' ? styles.columnFirst : styles.column}
                >
                  <div className="is-primary has-text-centered"
                    style={{
                      backgroundColor: '#CCC',
                    }}
                  >
                    {item}
                  </div>
                </div>
              ))}
              <div style={{
                padding: 3,
                height: 30,
                width: this.state.columnWidth * 3,
                position: 'absolute',
                left: this.state.leftLayout + this.state.columnWidth * 2,
                top: 80,
              }}>
                <div style={{ backgroundColor: '#5A9', height: 24 }}>
                </div>
              </div>
              <div style={{
                padding: 3,
                height: 30,
                width: this.state.columnWidth * 5,
                position: 'absolute',
                left: this.state.leftLayout + this.state.columnWidth * 1,
                top: 110,
              }}>
                <div style={{ backgroundColor: '#5A9', height: 24 }}>
                </div>
              </div>
            </div>
          </div>
        }
      </div>
    )
  }
}

const styles = {
  columnFirst: {
    borderWidth: 1,
    borderStyle: 'solid',
  },
  column: {
    borderWidth: 1,
    borderStyle: 'solid',
    borderLeftWidth: 0,
    borderLeftStyle: 'none',
  },
}