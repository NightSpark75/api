/** 
 * Pointlog.js
 */
import React from 'react';
import { Link } from 'react-router';
import axios from 'axios';
//import UserAdd from './UserAdd';
//import UserEdit from './UserEdit';
//import uikit from 'react-uikit-base';

export default class Pointlog extends React.Component{
    constructor(props) {
        super(props);

        this.state = {
            prg: [],
            user: [],
            search: '',
            isAddShow: false,
            isEditShow: false,
            allList: true
        }
    }

    init() {
        let self = this;       
        axios.get('/api/web/user/init', null, {
            method: 'get',
        }).then(function (response) {
            if (response.data.result) {
                self.setState({
                    user: response.data.user,
                    prg: response.data.prg
                });
            } else {
                console.log(response.data);
                window.location = '/pad/login/ppm';
            }
        }).catch(function (error) {
            console.log(error);
        });
    }

    componentDidMount() {
        //this.init();
    }

    render() {
        return(   
            <div>
                <span>pointlog</span>   
            </div>
        );
    }
}