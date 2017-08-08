/** 
 * Sds.js
 */
import React from 'react';
import ReactDOM from "react-dom";
import { Link } from 'react-router';
import axios from 'axios';
import { Button, ButtonToolbar, Table, Panel, Pager, FormControl, Alert, Col, ListGroup, ListGroupItem } from "react-bootstrap";

export default class Sds extends React.Component{
    constructor(props) {
        super(props);

        this.state = {
            barcode_list: [],
            receive_list: [],
            receive: false,
            barcode: '',
            msg: '',
            msgType: '',
        }
    }
    render() { 
        const { barcode, barcode_list, receive, receive_list, msg, msgType } = this.state;
        return(   
            <div>
            </div>
        )
    }
}