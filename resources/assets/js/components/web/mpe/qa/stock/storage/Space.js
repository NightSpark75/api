/** 
 * Space.js
 */
import React from 'react'
import { Link } from 'react-router'
import axios from 'axios'

export default class Space extends React.Component {
  constructor(props) {
    super(props)

    this.state = {
      list: [],
      search_str: '',
      search_key: '',
      searching: false,
      select_item: [],
      storageShow: false,
      isLoading: false,
    }
  }

  onSearch() {
    let str = this.state.search_str
    if (str !== '' && this.state.search_key !== str) {
      let self = this
      axios.get('/api/web/mpe/qa/stock/storage/list/' + str)
        .then(function (response) {
          if (response.data.result) {
            self.setState({
              list: response.data.list,
              searching: true,
              search_key: str,
            })
            console.log(response.data)
          } else {
            console.log(response.data)
          }
        }).catch(function (error) {
          console.log(error)
        })
    }
  }

  searchChange(event) {
    this.setState({ search_str: event.target.value })
  }

  cancelSearch() {
    this.setState({
      list: [],
      search_str: '',
      search_key: '',
      searching: false,
    })
  }

  onChange(item) {
    let self = this
    let data = {
      partno: this.props.item.partno,
      batch: this.props.item.batch,
      whouse: item.whouse,
      stor: item.stor,
      search_str: this.props.searchKey
    }
    axios.put('/api/web/mpe/qa/stock/storage/change', data)
      .then(function (response) {
        if (response.data.result) {
          //self.setState({ search: response.data.list })
          self.props.resetList(response.data.list)
        } else {
          console.log(response.data)
        }
        self.setState({ isLoading: false })
      }).catch(function (error) {
        console.log(error)
        self.setState({ isLoading: false })
      })
  }

  render() {
    const { list, search, search_str, searching, isLoading } = this.state
    const { show } = this.props
    let isActive = show ? 'is-active' : ''
    return (
      <div className={"modal " + isActive}>
        <div className="modal-background"></div>
        <div className="modal-card">
          <header className="modal-card-head">
            <p className="modal-card-title">儲位清單</p>
          </header>
          <section className="modal-card-body">
            <div className="field has-addons">
              <div className="control">
                <input
                  type="text"
                  className="input"
                  maxLength={30}
                  value={this.state.search_str}
                  onChange={this.searchChange.bind(this)}
                />
              </div>
              <div className="control">
                {searching &&
                  <a className="button is-danger"
                    onClick={this.cancelSearch.bind(this)}
                  >
                    取消
                        </a>
                }
                <button type="submit" className="button" onClick={this.onSearch.bind(this)}>查詢</button>
              </div>
            </div>
            <table className="table is-bordered is-fullwidth">
              <thead>
                <tr>
                  <th>倉庫</th>
                  <th>倉庫名稱</th>
                  <th>儲位</th>
                  <th>儲位名稱</th>
                  <th width="92.22"></th>
                </tr>
              </thead>
              <tbody>
                {list.map((item, index) => (
                  <tr key={index}>
                    <td>{item.whouse}</td>
                    <td>{item.posit}</td>
                    <td>{item.stor}</td>
                    <td>{item.storn}</td>
                    <td>
                      {isLoading ?
                        <button className="button is-loading is-warning"></button>
                        :
                        <button type="button" className="button is-warning" onClick={this.onChange.bind(this, item)}>變更</button>
                      }
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </section>
          <footer className="modal-card-foot">
            <button className="button is-primary" onClick={this.props.hide}>關閉</button>
          </footer>
        </div>
      </div>
    )
  }
}