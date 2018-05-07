import React from "react"

export default class Image extends React.Component {
  constructor(props) {
    super(props)
    this.state = {
      showImage: false,
    }
    this.showImage = this.showImage.bind(this)
    this.hideImage = this.hideImage.bind(this)
  }

  showImage() {
    this.setState({ showImage: true })
  }

  hideImage() {
    this.setState({ showImage: false })
  }

  render() {
    const { mcu, floor, type } = this.props
    const { showImage } = this.state
    const url = getImage(mcu, floor, type)
    return (
      <div>
      <button
        className="button"
        onClick={this.showImage}
      >
        <span className="icon">
          <i className="fas fa-map-marker-alt"></i>
        </span>
      </button>
      {showImage &&
        <div className="modal is-active">
          <div className="modal-background" onClick={this.hideImage}></div>
          <div className="modal-content">
            <p className="image">
              <img src={url} alt=""/>
            </p>
          </div>
          <button className="modal-close is-large" aria-label="close" onClick={this.hideImage}></button>
        </div>
      }
    </div>
      
    )
  }
}

function getImage(mcu, floor, type) {
  let url, cls
  if (type === 'C') cls = 'PC'
  else if (type === 'P') cls = 'PD'
  else cls = 'TH'
  url = '/images/mpz/' + mcu + '-' + floor + '-' + cls + '.png'
  return url
}