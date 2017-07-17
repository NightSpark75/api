import React from 'react';
import { Link } from 'react-router';
import uikit from 'react-uikit-base';
import Notify from 'react-uikit-notify';

export default class Test extends React.Component{
    constructor(props) {
        super(props);
        this.state = {
            show: false
        };
    }

    handleNotifyIn(kitid) {

    }

    handleNotifyOut(kitid) {

    }

    handleClose(e) {

    }

    show(e, id) {
        let element = uikit.helpers.getElement(id);
        return element;
    }

    render() {
        const message = {
            message: 'Message...',
            kitid:  'message_0',
            timeout: 3000,
            context: 'info',
            animate: {
                in : kitid => this.handleNotifyIn(kitid),
                out: kitid => this.handleNotifyOut(kitid)
            },
            onClick: e => this.handleClose(e)
        };
        return(   
            <div>
                <button 
                    className="uk-button uk-button-default" 
                    onClick={this.show.bind(this, message.kitid)}
                >
                Show
                </button>
                {/*
                <Notify
                    kitid='notify1'
                    pos='top-center'
                    messages={[message]}
                />
                */}
                
            </div>
        );
    }
}