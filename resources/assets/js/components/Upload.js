import React from 'react';
export default class Upload extends React.Component{
    constructor(props){
        super(props);

        this.stats = {
            errors: '',
        }

        this.handleSubmit = this.handleSubmit.bind(this);
    }

    handleSubmit(event){
        return;
    }
    render(){
        return(
            <div className="panel panel-default">
                <div className="panel-body">
                    <form className="form-horizontal" role="form" onSubmit={this.handleSubmit}>
                        <input type="hidden" id="file_id" name="file_id" defaultValue={this.props.params.file_id}/>
                        <div className="form-group">
                            <label className="col-md-3 control-label">檔案名稱</label>
                            <div className="col-md-9">
                                <input type="text" className="form-control" id="file_name" name="file_name"/>
                            </div>
                        </div>
                        <div className="form-group">
                            <label className="col-md-3 control-label">檔案描述</label>
                            <div className="col-md-9">
                                <textarea className="form-control" rows="3" id="file_description" name="file_description"></textarea>
                            </div>
                        </div>
                        <div className="form-group">
                            <label className="col-md-3 control-label">上傳檔案</label>
                            <div className="col-md-9">
                                <input type="file" id="file_data" name="file_data"/>
                            </div>
                        </div>
                        <div className="form-group">
                            <div className="col-md-offset-3 col-md-9">
                                <button type="submit" className="btn btn-default">上傳</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        );
    }
}
/*
    <div className="panel panel-default">
        <div className="panel-body">
            <form className="form-horizontal" role="form" method="POST" enctype="multipart/form-data" onSubmit={this.handleSubmit}>
                <div className="form-group">
                    <label className="control-label" for="file">檔案上傳</label>
                    <input type="file" nam="file"/>
                </div>
            </form>
        </div>
    </div>
*/