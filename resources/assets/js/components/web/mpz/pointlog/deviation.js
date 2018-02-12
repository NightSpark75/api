import React from "react"
export default class Deviation extends React.Component {
  render() {
    const { isLoading, isDeviation, isChecked, onCancel, onSave, isComplete, isOverdue } = this.props 
    let btnSubmit = null
    let btnCancel = (<button className="button" onClick={onCancel}>取消</button>)
    if (isDeviation && !isChecked) {
      btnSubmit = (<button className="button is-warning" title="Disabled button" disabled>請點選開立偏差</button>)
    }
    if (isDeviation && isChecked) {
      btnSubmit = (<button type="button" className="button is-warning" onClick={onSave}>儲存</button>)
    }
    if (!isDeviation) {
      btnSubmit = (<button type="button" className="button is-primary" onClick={onSave}>儲存</button>)
    }
    if (isLoading) {
      btnSubmit = (<button className="button is-loading is-primary" style={{width: '58px'}}></button>)
    }
    if (isComplete) {
      btnSubmit = (<button className="button is-success">今日已完成記錄</button>)
    }
    if (isOverdue) {
      btnSubmit = (<button className="button is-success">目前已逾時</button>)
    }
    return (
      <div className="buttons">
        {btnSubmit}
        {btnCancel}
      </div>
    )
  }
}