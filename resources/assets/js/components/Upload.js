/** 
 * Upload.js
 */
import React from 'react';
import axios from 'axios';

function alert_msg (type, msg) {
    let type_class = {
        success: 'alert alert-success',
        danger: 'alert alert-danger',
        primary: 'alert alert-primary',
        default: 'alert alert-default',
        warning: 'alert alert-warning',
    }
    if (msg !== '') {
        return(
            <div className={type_class[type]}>
                {msg}
            </div>
        );
    };
}

export default class Upload extends React.Component{
    constructor(props){
        super(props);

        this.state = {
            file_id: '',
            user_id: '',
            file_data: undefined,
            msg: '',
            msg_type: '',
        }

        this.alert_msg = this.alert_msg.bind(this);
    }

    alert_msg (type, msg) {
        let type_class = {
            success: 'alert alert-success',
            danger: 'alert alert-danger',
            primary: 'alert alert-primary',
            default: 'alert alert-default',
            warning: 'alert alert-warning',
        }
        if (msg !== '') {
            return(
                <div className={type_class[type]}>
                    {msg}
                </div>
            );
        };
    }

    setMsg(type = '', msg = '') {
        this.setState({
            msg_type: type,
            msg: msg,
        });
    }

    componentDidMount() {
        this.setState({
            file_id: this.props.params.file_id,
            user_id: this.props.params.user_id,
        });
    }

    onFileChange(event){
        event.preventDefault();
        this.setState({file_data: event.target.files[0]})
    }

    onUpload(event){
        const {file_id, user_id, file_data} = this.state;
        let self = this;
        if (file_data === undefined) {
            this.setMsg('danger', '請選擇檔案!');
            return;
        } else {
            this.setMsg('', '');
        }

        let form_data = new FormData();
        form_data.append('file_id', file_id);
        form_data.append('user_id', user_id);
        form_data.append('file_data', file_data);
        axios.post('/api/file/upload', form_data, {
            method: 'post',
            headers: {'Content-Type': 'multipart/form-data'}
        }).then(function (response) {
            console.log(response);
            if (response.data.result) {
                self.setMsg('success', response.data.msg);
            } else {
                self.setMsg('danger', response.data.msg);
            }
        }).catch(function (error) {
            console.log(error);
        });
        
    }
    render(){
        return(
            <div className="row">
                <div className="col-md-6 col-md-offset-3">
                    <div className="panel panel-primary">
                        <div className="panel-heading"><h4 className="panel-title">上傳檔案</h4></div>
                        <div className="panel-body">
                            <form role="form">
                                <div className="form-group">
                                    <input type="file" id="file_data" name="file_data" onChange={this.onFileChange.bind(this)}/>
                                </div>
                                {alert_msg(this.state.msg_type, this.state.msg)}
                                <div className="form-group">
                                    <button type="button" className="btn btn-primary" onClick={this.onUpload.bind(this)}>
                                        <span className="glyphicon glyphicon-upload"></span>上傳
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}