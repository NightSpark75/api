import React from "react"

export default class Image extends React.Component {
  render() {
    const {mcu, floor, type, hide} = this.props
    const url = getImage(mcu, floor, type)
    return (
      <div className="modal is-active">
        <div className="modal-background" onClick={hide}></div>
        <div className="modal-content">
          <p className="image">
            <img src={url} alt=""/>
          </p>
        </div>
        <button className="modal-close is-large" aria-label="close" onClick={hide}></button>
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