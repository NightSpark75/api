import React from "react"
export default class Capture extends React.Component {
  numberCheck(e) {
    let value = e.target.value
    if (isNaN(value)) {
      alert('請輸入數字')
      e.target.value = this.props.value
      this.props.onChange(e)
    }
    this.props.onChange(e)
  }
  render() {
    return (
      <div className="field is-horizontal">
        <div className="field-label is-normal" style={{flexGrow: '0', paddingTop: '1.5px'}}>
          <label className="label" style={{width: '40px'}}>{this.props.label}</label>
        </div>
        <div className="field-body">
          <div className="field has-addons">
            <p className="control">
              <input className="input is-small" type="text"
                value={this.props.value}
                onChange={this.numberCheck.bind(this)}
              />
            </p>
          </div>
        </div>
      </div>
    )
  }
}