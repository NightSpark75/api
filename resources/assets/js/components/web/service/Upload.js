/** 
 * Upload.js
 */
import React from 'react';
import axios from 'axios';
import AlertMsg from '../../includes/AlertMsg';

export default class Upload extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            file_id: '',
            user_id: '',
            file_data: undefined,
            store_type: '',
            msg: '',
            msg_type: '',
            buttonState: 'default',
        }
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
            sotre_type: this.props.params.store_type,
        });
    }

    onFileChange(event) {
        event.preventDefault();
        this.setState({file_data: event.target.files[0]})
    }

    onUpload(event) {
        const {file_id, user_id, file_data, store_type} = this.state;
        let self = this;
        let url = '';
        if (file_data === undefined) {
            this.setMsg('danger', '請選擇檔案!');
            return;
        } else {
            this.setMsg('', '');
        }

        if (store_type === 'c') {
            url = '/api/file/upload/code';
        } else {
            url = '/api/file/upload/path';
        }
        this.setState({buttonState: 'uploading'});
        let form_data = new FormData();
        form_data.append('file_id', file_id);
        form_data.append('user_id', user_id);
        form_data.append('file_data', file_data);
        axios.post(url, form_data, {
            method: 'post',
            headers: {'Content-Type': 'multipart/form-data'}
        }).then(function (response) {
            if (response.data.result) {
                self.setMsg('success', response.data.msg);
                self.setState({buttonState: 'complete'})
            } else {
                self.setMsg('danger', response.data.msg);
                self.setState({buttonState: 'default'})
            }
        }).catch(function (error) {
            console.log(error);
            self.setMsg('danger', error);
            self.setState({buttonState: 'default'})
        });
    }
    render() {
        return(
            <div className="row">
                <div className="col-md-6 col-md-offset-3">
                    <div className="panel panel-primary">
                        <div className="panel-heading">
                            <h4 className="panel-title">上傳檔案</h4>
                        </div>
                        <div className="panel-body">
                            <div role="form">
                                <div className="form-group">
                                    <input 
                                        type="file" 
                                        id="file_data" 
                                        name="file_data" 
                                        onChange={this.onFileChange.bind(this)}
                                    />
                                </div>
                                {this.state.buttonState === 'default' ? 
                                    <div className="form-group">
                                        <button 
                                            type="button" 
                                            className="btn btn-primary" 
                                            onClick={this.onUpload.bind(this)}
                                        >
                                            <span className="glyphicon glyphicon-upload"></span>上傳
                                        </button>
                                    </div>
                                : this.state.buttonState === 'uploading' ? 
                                    <div className="form-group">
                                        <button 
                                            type="button" 
                                            className="btn btn-info disable"
                                        >
                                            檔案上傳中......
                                        </button>
                                    </div>
                                :  this.state.buttonState === 'complete' ?
                                    <div className="form-group">
                                        <button 
                                            type="button" 
                                            className="btn btn-success disable" 
                                        >
                                            <span className="glyphicon glyphicon-saved"></span>檔案已上傳完成
                                        </button>
                                    </div>
                                : null
                                }
                                <AlertMsg 
                                    type={this.state.msg_type} 
                                    msg={this.state.msg}
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}