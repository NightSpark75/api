import React from "react"
export default class Deviation extends React.Component {
  render() {
    const { isLoading, isDeviation, isChecked, onCancel, onSave, isComplete, isOverdue, isEmpty, alert } = this.props 
    let btnSubmit = null
    let btnCancel = (<button className="button" onClick={onCancel}>取消</button>)
    if (isDeviation && isChecked) {
      btnSubmit = (<button className="button is-primary" onClick={onSave}>儲存</button>)
    }
    if (!isDeviation) {
      btnSubmit = (<button className="button is-primary" onClick={onSave}>儲存</button>)
    }
    if (isDeviation && !isChecked) {
      btnSubmit = (<button className="button is-warning">請點選開立偏差</button>)
    }
    if (isLoading) {
      btnSubmit = (<button className="button is-loading is-primary" style={{width: '58px'}}></button>)
    }
    if (isOverdue) {
      btnSubmit = (<button className="button is-warning">目前已逾時</button>)
    }
    if (alert.length > 0) {
      btnSubmit = (<button className="button is-warning">請排除異常</button>)
    }
    if (isEmpty) {
      btnSubmit = null
    }
    return (
      <div className="buttons space">
        {btnSubmit}
        {btnCancel}
      </div>
    )
  }
}

/*
if (isComplete) {
  btnSubmit = (<button className="button is-success">今日已完成記錄</button>)
}
*/