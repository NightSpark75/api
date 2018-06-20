import React from "react"
export default class Exception extends React.Component {
  render() {
    const { value, checked, onChange, label } = this.props
    return (
      <div className="field is-horizontal" onChange={onChange}>
        <div className="field-body">
          <div className="field has-addons">
            <div className="control">
              <label className="checkbox">
                <input type="checkbox"
                  value={value}
                  checked={checked === 'Y'}
                />
                <span style={{fontSize: '16px', fontWeight: 'bolder'}}>{label}</span>
              </label>
            </div>
          </div>
        </div>
      </div>
    )
  }
}