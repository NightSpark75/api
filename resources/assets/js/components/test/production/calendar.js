import React from 'react'
import { Link } from 'react-router'
import axios from 'axios'

const col = ['1', '2', '3', '4', '5', '6', '7']
//const col = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12']
const days = []


export default class Calendar extends React.Component {
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
    this.moveBar = this.moveBar.bind(this)
  }

  componentDidMount() {
    console.log('did mount')
    this.resetWidth()
    window.addEventListener("resize", this.checkWidth);
  }

  setDays() {
    // const d, w, x = 7, y = 5
    // for (d = 0; d < x; d++) {
    //   for (w = 0; d < y; w++) {

    //   }
    // }

  }

  checkWidth() {
    const { calendar } = this.refs
    if (calendar) {
      const width = this.refs.calendar.offsetWidth
      const height = this.refs.calendar.offsetHeight
      if (width <= 768) {
        this.setState({ show: false })
      } else {
        this.setState({ show: true }, () => this.resetWidth())
      }
    }
  }

  resetWidth() {
    const width = this.refs.calendar.offsetWidth
    const height = this.refs.calendar.offsetWidth
    const leftLayout = (width / 10) * 2
    const rightLayout = (width / 10) * 8
    this.setState({
      width: width,
      leftLayout: leftLayout,
      rightLayout: rightLayout,
      columnWidth: rightLayout / 7,
    })
  }

  moveBar(e) {
    console.log(e.pageX + ', ' + e.pageY)
    /*
    this.setState({ 
      barShow: true,
      mouseX: e.pageX,
      mouseY: e.pageY,
    }, () => {
      this.refs.bar.style.top = this.refs.bar.offsetTop + 10 + 'px'
    })
    */
  }

  render() {
    return (
      <div
        ref="calendar"
        style={{
          width: this.props.width,
          float: 'lerft',
          height: 200,
        }}
      >
        {col.map((item, index) => (
          <div key={index}
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
          <div
            style={{ backgroundColor: '#5A9', height: 24 }}
          >
          </div>
        </div>
        <div
          ref="test"
          style={{
            padding: 3,
            height: 30,
            width: this.state.columnWidth * 5,
            position: 'absolute',
            left: this.state.leftLayout + this.state.columnWidth * 1,
            top: 110,
          }}
        >
          <div
            style={{ backgroundColor: '#5A9', height: 24 }}
            //onDragStart={this.moveBar}
            onDrag={this.moveBar}
          >
          </div>
        </div>
        {this.state.barShow &&
          <div
            ref="bar"
            style={{
              padding: 3,
              height: 30,
              width: this.state.columnWidth * 5 - 10,
              borderWidth: 2,
              borderStyle: 'solid',
              position: 'absolute',
              left: this.state.leftLayout + this.state.columnWidth * 1,
              top: 110,
            }}
          >
            <div
              style={{ backgroundColor: '#5A9', height: 24 }}
            >
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