/** 
 * clean.Dept.js
 */
import React from 'react'
import { Link } from 'react-router'
import axios from 'axios'

export default class Dept extends React.Component {
  constructor(props) {
    super(props)

    this.state = {
      ready: false,
      sno: this.props.params.sno,
      deptno: this.props.params.deptno,
      dept_list: [],
      showInfo: false,
      item: [],
    }
  }

  componentDidMount() {
    this.init()
  }

  init() {
    this.getDeptList()
  }

  getDeptList() {
    let self = this
    let { deptno } = this.state
    axios.get('/api/web/mpb/prod/clean/dept/' + deptno)
      .then(function (response) {
        if (response.data.result) {
          self.setState({
            dept_list: response.data.dept_list,
          })
          console.log(response.data)
        } else {
          console.log(response.data)
        }
      }).catch(function (error) {
        console.log(error)
      })
  }

  render() {
    const { sno, dept_list } = this.state
    return (
      <div>
        <div className="box" style={{ marginTop: '10px', marginBottom: '10px' }}>
          <p className="control">
            <Link className="button is-medium" to="/auth/web/mpb/clean/list">&larr; 工單列表</Link>
          </p>
        </div>
        {dept_list.length !== 0 &&
          <div className="columns is-multiline" style={{ margin: '0px' }}>
            {dept_list.map((item, index) => (
              <div className="column is-4" key={index}>
                <Link className="button is-orange is-4 is-fullwidth is-large"
                  to={'/auth/web/mpb/clean/working/' + sno + '/' + item['deptno']}>
                  {item['dname']}
                </Link>
              </div>
            ))}
          </div>
        }
      </div>
    )
  }
}