import React from "react"
export default class Remark extends React.Component {
  render() {
    return (
      <tr>
        <td>特別註記</td>
        <td>
          <div className="field is-horizontal">
            <div className="field-body">
              <div className="field">
                <div className="control">
                  <textarea className="textarea" placeholder="請輸入特別註記"
                    value={this.props.value || ''}
                    onChange={this.props.onChange}
                  >
                  </textarea>
                </div>
              </div>
            </div>
          </div>
        </td>
      </tr>
    )
  }
}