import React from 'react'
import { Link } from 'react-router'
import axios from 'axios'
import Calendar from './calendar'

const col = ['1', '2', '3', '4', '5', '6', '7']
//const col = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12']


export default class Production extends React.Component {
  constructor(props) {
    super(props)
    this.state = {
      show: true,
      width: 0,
      leftLayout: 0,
      rightLayout: 0,
      columnWidth: 0,
      barShow: false,
      mouseX: 0,
      mouseY: 0,
    }
    this.checkWidth = this.checkWidth.bind(this)
    this.resetWidth = this.resetWidth.bind(this)
  }
  componentDidMount() {
    this.resetWidth()
    window.addEventListener("resize", this.checkWidth);
  }

  checkWidth() {
    const { container } = this.refs
    if (container) {
      const width = this.refs.container.offsetWidth
      const height = this.refs.container.offsetHeight
      if (width <= 768) {
        this.setState({ show: false })
      } else {
        this.setState({ show: true }, () => this.resetWidth())
      }
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
      <div ref="container" className="widescreen-only"
      //onClick={(event) => {console.log(event.pageX)}}
      >
        {this.state.show &&
          <div>
            <div ref="one" style={{ backgroundColor: '#BFF' }}>
              calendar
            </div>
            <div style={{ width: this.state.leftLayout, float: 'left' }}>
              left layout
            </div>
            <Calendar width={this.state.rightLayout} />
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