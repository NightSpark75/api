import React from 'react';
import { Link } from 'react-router';
import axios from 'axios';

export default class Info extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      list: [],
    }
  }

  componentDidMount() {
    const { partno, batch } = this.props
    const self = this
    axios.get('/api/web/mpe/qc/doc/barcode/list/' + partno + '/' + batch)
    .then(function (response) {
      console.log(response.data)
      self.setState({
        list: response.data,
      })
    }).catch(function (error) {
      console.log(error)
    })
  }

  render() {
    const { list } = this.state;
    return (
      <div className="modal is-active">
        <div className="modal-background"></div>
        <div className="modal-card" style={{width: 800}}>
          <header className="modal-card-head">
            <p className="modal-card-title">{this.props.partno}</p>
            <button className="delete" aria-label="close" onClick={this.props.close}></button>
          </header>
          <section className="modal-card-body">
            {list.length === 0 ?
              <h3 className="title is-3">資訊讀取中...</h3>
            :
              <table className="table is-bordered is-hoverable is-fullwidth" style={{ margin: '0' }}>
                <thead>
                    <tr>
                      <td>條碼號</td>
                      <td>購買日期</td>
                      <td>有效日期</td>
                      <td>開封日期</td>
                      <td>開封後效期</td>
                      <td>狀態</td>
                    </tr>
                </thead>
                <tbody>
                  {list.map((item, index) => (
                    <tr key={index}>
                      <td>{item.barcode}</td>
                      <td>{item.buydate}</td>
                      <td>{item.valid}</td>
                      <td>{item.opdate}</td>
                      <td>{item.opvl}</td>
                      <td>{item.sta === 'N' ? '在庫' : '領用'}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            }
          </section>
          <footer className="modal-card-foot">
            <button className="button" onClick={this.props.close}>關閉</button>
          </footer>
        </div>
      </div>
    )
  }
}