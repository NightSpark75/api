import React from "react"
import { operatorHandle } from '../rule'
export default class Replace extends React.Component {
  render() {
    const { changeDate, value, checked, onChange, label, type, rule } = this.props
    return (
      <div className="field is-horizontal">
        <div className="field-body">
          <div className="field has-addons">
            <div className="control">
              <label className="checkbox">
                <input type="checkbox"
                  value={value}
                  checked={checked === 'Y'}
                  onChange={onChange}
                />
                <span style={{fontSize: '16px', fontWeight: 'bolder'}}>{label}</span>
                <span style={{marginLeft: '20px'}}>{getChangeDate(changeDate, type, rule)}</span>
              </label>
            </div>
          </div>
        </div>
      </div>
    )
  }
}

function getChangeDate(changeDate, type, rule) {
  let className = ''
  if (operatorHandle(changeDate[type]['dday'], rule.CHANGE_WARNING.cond, rule.CHANGE_WARNING.val)) {
    className = 'tag is-warning'
  }
  return (<span className={className}>上次更換日期：{changeDate[type]['pday']}</span>)
}