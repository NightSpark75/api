import React from "react"
export default class Capture extends React.Component {
  render() {
    return (
      <div className="field is-horizontal">
        <div className="field-label is-normal" style={{flexGrow: '0', paddingTop: '1.5px'}}>
          <label className="label" style={{width: '40px'}}>{this.props.label}</label>
        </div>
        <div className="field-body">
          <div className="field has-addons">
            <p className="control">
              <input className="input is-small" type="number"
                min={0}
                value={this.props.value}
                onChange={this.props.onChange}
                required
              />
            </p>
          </div>
        </div>
      </div>
    )
  }
}