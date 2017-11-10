/** 
 * Menu.js
 */
import React from 'react'
import { Link } from 'react-router'
import axios from 'axios'

export default class Menu extends React.Component{
    constructor(props) {
        super(props)

        this.state = {
            list: [],
            collapsed: true
        }
    }

    componentDidMount() {
        let self = this
        axios.get('/api/web/menu', new FormData(), {
            method: 'get',
        }).then(function (response) {
            if (response.data.result) {
                self.setState({list: response.data.menu})
            } else {
                window.location = '/web/login/ppm'
            }
        }).catch(function (error) {
            console.log(error)
        })
    }

    render() {
        let list = this.state.list
        let row = list.length / 4
        return(   
            <div className="columns is-multiline" style={{margin: '0px'}}>
                {list.map((item, index) => (
                    <div className="column is-4" key={item['prg_id']}>
                        <Link className="button is-orange is-4 is-fullwidth is-large" to={item['web_route']}>
                            {item['prg_name']}
                        </Link>
                    </div>
                ))}
                {list.length === 0 && 
                    <label className="label is-large">功能清單建立中...</label> 
                }
            </div>
        )
    }
}