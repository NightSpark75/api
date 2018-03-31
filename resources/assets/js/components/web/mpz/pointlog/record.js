import React from "react"
let preValue
export default class Record extends React.Component {
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
    const labelWidth = this.props.width? this.props.width: '120px'
    preValue = this.props.value
    return (
      <div className="field is-horizontal">
        <div className="field-label is-normal" style={{flexGrow: '0', paddingTop: '1.5px'}}>
          <label className="label" style={{width: labelWidth}}>{this.props.label}</label>
        </div>
        <div className="field-body">
          <div className="field has-addons">
            <p className="control">
              <input className="input is-small" type="text" style={{width: '100px'}}
                value={this.props.value}
                disabled={this.props.disabled || false}
                onChange={this.numberCheck.bind(this)}
              />
            </p>
          </div>
        </div>
      </div>
    )
  }
}