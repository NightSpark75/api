import React from "react"
export default class Checking extends React.Component {
  render() {
    return (
      <div className="field is-horizontal">
        <div className="field-label is-normal" style={{ flexGrow: '0', paddingTop: '0px' }}>
          <label className="label" style={{ width: '120px' }}>{this.props.label}</label>
        </div>
        <div className="field-body">
          <div className="field has-addons">
            <div className="control">
              <label className="radio">
                <input type="radio" name={this.props.name}
                  checked={this.props.value === 'Y'}
                  onChange={this.props.onChange}
                />
                正常
              </label>
              <label className="radio" style={{ marginLeft: '15px' }}>
                <input type="radio" name={this.props.name}
                  checked={this.props.value === 'N'}
                  onChange={this.props.onChange}
                />
                異常
              </label>
            </div>
          </div>
        </div>
      </div>
    )
  }
}