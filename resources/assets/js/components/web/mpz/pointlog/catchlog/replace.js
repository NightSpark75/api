import React from "react"
export default class Replace extends React.Component {
  render() {
    const { changeDate, value, checked, onChange, label, type, rule } = this.props
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
  if (operatorHandle(changeDate[type]['dday']), rule.CHANGE_WARNING.cond, rule.CHANGE_WARNING.val) {
    className = 'tag is-warning'
  }
  return (<span className={className}>上次更換日期：{changeDate[type]['pday']}</span>)
}

function operatorHandle(v1, ope, v2) {
  switch (ope) {
    case '=':
      return v1 = v2
      break
    case '>':
      return v1 > v2
      break
    case '<':
      return v1 < v2
      break
    case '>=':
      return v1 >= v2
      break
    case '<=':
      return v1 <= v2
      break
  }
}