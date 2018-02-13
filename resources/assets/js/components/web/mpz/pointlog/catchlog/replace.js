import React from "react"
export default class Replace extends React.Component {
  render() {
    const changeDate = this.props.changeDate
    const warrning = this.props.warring
    return (
      <div className="field is-horizontal" onChange={this.props.onChange}>
        <div className="field-body">
          <div className="field has-addons">
            <div className="control">
              <label className="checkbox">
                <input type="checkbox"
                  value={this.props.value}
                  checked={this.props.checked === 'Y'}
                />
                <span style={{fontSize: '16px', fontWeight: 'bolder'}}>{this.props.label}</span>
                <span style={{marginLeft: '20px'}}>上次更換日期：{this.props.msg}</span>
              </label>
            </div>
          </div>
        </div>
      </div>
    )
  }
}

function getStyle(changeDate, type) {
  changeDate[type]['dday'];
}