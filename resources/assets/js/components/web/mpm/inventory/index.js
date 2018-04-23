/** 
 * Inventory.js
 */
import React from 'react';
import { Link } from 'react-router';
import axios from 'axios';

export default class Inventory extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      cyno: '',
      inventoried: [],
      msg: '',
    }
    this.cynoChange = this.cynoChange.bind(this)
  }

  cynoChange(e) {
    const cyno = e.target.value
    this.setState({
      inventoried: [],
      cyno: cyno,
      msg: '',
    })
    if (cyno.length === 8) this.getInventoried(cyno)
  }

  getInventoried(cyno) {
    let self = this;
    this.setState({msg: '盤點資料查詢中'})
    axios.get('/api/productWarehouse/inventory/inventoried/' + cyno)
    .then((response) => {
      self.setState({
        inventoried: response.data.inventoried,
        msg: '成功取得盤點單[' + cyno + ']資料'
      });
    }).catch((error) => {
      self.setState({
        inventoried: [],
        msg: error.response.data.message,
      })
      console.log(error);
    });
  }
  
  goMenu() {
    window.location = '/auth/web/menu';
  }

  render() {
    const { inventoried, cyno } = this.state
    return (
      <div>
        <div className="box" style={{ marginTop: '10px', marginBottom: '10px' }}>
          <div className="level">
            <div className="level-left">
              <div className="level-item">
                <Link className="button" to="/auth/web/menu">&larr; 功能選單</Link>
              </div>
            </div>
            <div className="level-right">
              <div className="level-item">
                {inventoried.length > 0 &&
                  <a 
                    className="button is-success" 
                    href={'/api/productWarehouse/inventory/export/' + cyno}
                    target="_blank"
                  >
                    下載Excel
                  </a>
                }
              </div>
            </div>
          </div>
        </div>
        <div className="box" style={{ marginBottom: '10px' }}>
          <div className="field is-horizontal">
            <div className="field-body">
              <div className="field is-grouped">
                <div className="field" style={{ marginRight: '10px' }}>
                  <input type="text" className="input is-large"
                    disabled={false}
                    value={this.state.cyno}
                    autoFocus
                    maxLength={8}
                    placeholder="輸入盤點單號"
                    onChange={this.cynoChange}
                  />
                </div>
                {this.state.msg !== '' &&
                  <div className="notification is-warning" style={{ padding: '1rem 1rem 1rem 1rem' }}>
                    {this.state.msg}
                  </div>
                }
              </div>
            </div>
          </div>
        </div>
        {inventoried.length > 0 &&
          <table className="table is-bordered is-fullwidth">
            <thead>
              <tr>
                <td>儲位</td>
                <td>料號</td>
                <td>批號</td>
                <td>盤點數量</td>
                <td>盤點人員</td>
                <td>時間</td>
              </tr>
            </thead>
            {inventoried.map((item, index) => (
              <tbody key={index}>
                <tr>
                  <td>{item.locn}</td>
                  <td>{item.litm}</td>
                  <td>{item.lotn}</td>
                  <td>{item.amount}</td>
                  <td>{item.duser}</td>
                  <td>{item.ddate}</td>
                </tr>
              </tbody>
            ))}
          </table>
        }
      </div>
    );
  }
}