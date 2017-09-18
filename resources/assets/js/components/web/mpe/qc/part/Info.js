/** 
 * Info.js
 */
import React from 'react';
import { Link } from 'react-router';
import axios from 'axios';

export default class Info extends React.Component{
    constructor(props) {
        super(props);

        this.state = {
            barcode: '',
            partno: '',
            batch: '',
            info: {},
            message: '',
        }
    }

    render() { 
        const { barcode, partno, batch, info, message } = this.state;
        return(   
            <div>
                partinfo
            </div>
        )
    }
}