import React from "react"
let preValue
export default class Record extends React.Component {
  constructor(props) {
    super(props)
    this.state = {
      nullValue: false,
    }
    this.clickNull = this.clickNull.bind(this)
  }

  numberCheck(e) {
    let value = e.target.value
    if (isNaN(value) && value !== '-') {
      alert('請輸入數字')
      e.target.value = this.props.value
      this.props.onChange(e)
    }
    this.props.onChange(e)
  }

  clickNull() {
    const { nullValue } = this.state
    this.setState({nullValue: !nullValue})
    this.props.setEmpty(!nullValue)
  }

  render() {
    const labelWidth = this.props.width? this.props.width: '120px'
    const noEmpty = this.props.noEmpty || false
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
                ref="val"
                value={this.props.value}
                disabled={this.props.disabled || false || this.state.nullValue}
                onChange={this.numberCheck.bind(this)}
              />
              {!noEmpty &&
              <label className="checkbox" style={{marginLeft: '10px'}}>
                <input type="checkbox"
                  onChange={this.clickNull}
                />
                <span style={{fontSize: '16px', fontWeight: 'bolder'}}>空值</span>
              </label>
              }
            </p>
          </div>
        </div>
      </div>
    )
  }
}