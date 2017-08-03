/** 
 * Job.js
 */
import React from 'react';
import axios from 'axios';

export default class Job extends React.Component{
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
    render() {
        const prg = this.state.prg;
        const user = this.state.user;
        return(   
            <div>

            </div>
        )
    }
}