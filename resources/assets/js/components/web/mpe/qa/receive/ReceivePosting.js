/** 
 * ReceivePosting.js
 */
import React from 'react';
import { Link } from 'react-router';
import axios from 'axios';
import { Button, Panel, FormControl, Alert } from "react-bootstrap";

export default class ReceivePosting extends React.Component{
    constructor(props) {
        super(props);

        this.state = {
            point: [],
            point_no: '',
            scan: 'disabled',
            scan_message: '',
            point_info: [],
            mouse_show: false,
        }
    }

    render() {
        return(   
            <div>

            </div>
        );
    };
}