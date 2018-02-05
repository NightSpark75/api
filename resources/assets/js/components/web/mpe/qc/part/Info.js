/** 
 * Info.js
 */
import React from 'react';
import { Link } from 'react-router';
import axios from 'axios';

export default class Info extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      info: null,
      search: '',
      searching: false,
      detail: null,
    }
  }

  searchChange(e) {
    this.setState({ search: e.target.value });
  }

  getInfo() {
    let self = this;
    const search = this.state.search;
    this.setState({ searching: true });
    axios.get('/api/web/mpe/qc/doc/info/' + search)
      .then(function (response) {
        if (response.data.result) {
          if (response.data.info.length > 0) {
            self.setState({
              info: response.data.info,
            });
            console.log(response.data.info)
          } else {
            alert('查詢不到任何資料');
          }
          console.log(response.data);
        } else {
          console.log(response.data);
          alert(response.data.msg)
        }
        self.setState({ searching: false });
      }).catch(function (error) {
        console.log(error);
        self.setState({ searching: false });
      });
  }

  showDetail(item) {
    this.setState({ detail: item })
  }

  hideDetail() {
    this.setState({ detail: null })
  }

  render() {
    const { info, search, searching, detail } = this.state;
    let loading = searching ? 'is-loading' : '';
    let show = detail ? 'is-active' : '';
    return (
      <div>
        <div className="box">
          <div className="column is-7" style={{ padding: 0 }}>
            <div className="field has-addons">
              <div className="control is-expanded">
                <input
                  className="input is-medium"
                  type="text"
                  placeholder="請輸入條碼、料號、批號、品名進行查詢..."
                  onChange={this.searchChange.bind(this)}
                />
              </div>
              <div className="control">
                <button
                  className={"button is-info is-medium " + loading}
                  onClick={this.getInfo.bind(this)}
                >
                  查詢
                                </button>
              </div>
            </div>
          </div>
        </div>
        {info &&
          <table className="table is-bordered is-hoverable is-fullwidth">
            <thead>
              <tr>
                <td width="60"></td>
                <td width="80">料號</td>
                <td>批號</td>
                <td>品名</td>
                <td width="130">儲位</td>
                <td width="120">庫存量</td>
                <td width="120">安全庫存量</td>
                <td width="83">SDS</td>
                <td width="83">COA</td>
              </tr>
            </thead>
            <tbody>
              {info.map((item, index) => (
                <tr key={index}>
                  <td>
                    <button className="button" onClick={this.showDetail.bind(this, item)}>
                      <span className="icon is-small">
                        <i className="fa fa-info"></i>
                      </span>
                    </button>
                  </td>
                  <td>{item.partno}</td>
                  <td>{item.batch}</td>
                  <td>{item.ename}</td>
                  <td>{item.storn}</td>
                  <td>{item.qty}</td>
                  <td>{item.sfty}</td>
                  <td>
                    {item.sds_no ?
                      <a className="button"
                        href={"/api/web/mpe/qc/doc/read/sds/" + item.partno + "/N/" + item.sds_no}
                        target="_blank"
                      >
                        下載
                                            </a>
                      :
                      <a className="button" title="Disabled button" disabled>下載</a>
                    }
                  </td>
                  <td>
                    {item.coa_no ?
                      <a className="button"
                        href={"/api/web/mpe/qc/doc/read/coa/" + item.partno + "/" + item.batch + "/" + item.coa_no}
                        target="_blank"
                        disabled={item.coa_no ? "false" : "true"}
                      >
                        下載
                                            </a>
                      :
                      <a className="button" title="Disabled button" disabled>下載</a>
                    }
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        }
        {detail &&
          <div className={"modal " + show}>
            <div className="modal-background"></div>
            <div className="modal-card">
              <header className="modal-card-head">
                <p className="modal-card-title">{detail.partno}</p>
                <button className="delete" aria-label="close" onClick={this.hideDetail.bind(this)}></button>
              </header>
              <section className="modal-card-body">
                <table className="table is-bordered is-hoverable is-fullwidth" style={{ margin: '0' }}>
                  <tbody>
                    <tr>
                      <td>中文名稱</td>
                      <td>{detail.pname}</td>
                    </tr>
                    <tr>
                      <td>英文名稱</td>
                      <td>{detail.ename}</td>
                    </tr>
                    <tr>
                      <td>試藥同名</td>
                      <td>{detail.reagent}</td>
                    </tr>
                    <tr>
                      <td>CASNO</td>
                      <td>{detail.casno}</td>
                    </tr>
                    <tr>
                      <td>分子式</td>
                      <td>{detail.molef}</td>
                    </tr>
                    <tr>
                      <td>分子量</td>
                      <td>{detail.molew}</td>
                    </tr>
                    <tr>
                      <td>等級</td>
                      <td>{detail.lev}</td>
                    </tr>
                    <tr>
                      <td>濃度</td>
                      <td>{detail.conc}</td>
                    </tr>
                    <tr>
                      <td>分類</td>
                      <td>
                        {detail.toxicizer ? ' 毒:' + detail.toxicizer : ''}
                        {detail.hazard ? ' 危:' + detail.hazard : ''}
                        {detail.pioneer ? ' 先:' + detail.pioneer : ''}
                      </td>
                    </tr>
                    <tr>
                      <td>貯存方式</td>
                      <td>{detail.store_type}</td>
                    </tr>
                    <tr>
                      <td>安全庫存量</td>
                      <td>{detail.sfty}</td>
                    </tr>
                  </tbody>
                </table>
              </section>
              <footer className="modal-card-foot">
                <button className="button" onClick={this.hideDetail.bind(this)}>關閉</button>
              </footer>
            </div>
          </div>
        }
      </div>
    )
  }
}