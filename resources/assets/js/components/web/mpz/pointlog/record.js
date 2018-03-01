import React from "react"
export default class Record extends React.Component {
  render() {
    return (
      <div className="field is-horizontal">
        <div className="field-label is-normal" style={{flexGrow: '0', paddingTop: '1.5px'}}>
          <label className="label" style={{width: '120px'}}>{this.props.label}</label>
        </div>
        <div className="field-body">
          <div className="field has-addons">
            <p className="control">
              <input className="input is-small" type="number" style={{width: '100px'}}
                min={-999}
                max={999}
                value={Number(this.props.value)}
                onChange={this.props.onChange}
              />
            </p>
          </div>
        </div>
      </div>
    )
  }
}