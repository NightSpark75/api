/** 
 * Change.js
 */
import React from 'react'
import { Link } from 'react-router'
import axios from 'axios'
import Space from './Space'

export default class Change extends React.Component {
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

  componentDidMount() {
    //this.init()
  }

  init() {
    let self = this
    axios.get('/api/web/mpe/qa/stock/list')
      .then(function (response) {
        if (response.data.result) {
          self.setState({
            //list: response.data.list,
            storage: response.data.storage,
          })
          console.log(response.data)
        } else {
          console.log(response.data)
        }
      }).catch(function (error) {
        console.log(error)
      })
  }

  onSearch(event) {
    event.preventDefault()
    let str = this.state.search_str
    if (str !== '' && this.state.search_key !== str) {
      let self = this
      axios.get('/api/web/mpe/qa/stock/list/' + str)
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

  showStorage(item) {
    this.setState({
      select_item: item,
      storageShow: true,
    })
  }

  hideStorage() {
    this.setState({
      select_item: [],
      storageShow: false,
    })
  }

  resetList(list) {
    this.setState({ list: list })
    this.hideStorage()
  }

  render() {
    const { list, search, search_str, searching, storageShow, storage, isLoading } = this.state
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
              </div>
            </div>
          </div>
        </div>
        {list.length > 0 ?
          <div>
            <table className="table is-bordered is-fullwidth">
              <thead>
                <tr>
                  <th>料號</th>
                  <th>批號</th>
                  <th>品名</th>
                  <th>倉庫</th>
                  <th>儲位</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                {list.map((item, index) => (
                  <tr key={index}>
                    <td>{item.partno}</td>
                    <td>{item.batch}</td>
                    <td>{item.pname}</td>
                    <td>{item.whouse}</td>
                    <td>{item.stor}</td>
                    <td width="92.22">
                      <button className="button is-primary" onClick={this.showStorage.bind(this, item)}>變更儲位</button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
            <Space
              show={this.state.storageShow}
              searchKey={this.state.search_key}
              hide={this.hideStorage.bind(this)}
              item={this.state.select_item}
              resetList={this.resetList.bind(this)}
            />
          </div>
        :
          <div className="notification is-warning" style={{ padding: '1rem 1rem 1rem 1rem' }}>
            無庫存資料
          </div>
        }
      </div>
    )
  }
}