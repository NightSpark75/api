import React from "react";
import ReactDOM from "react-dom";
import {Router, Route, IndexRoute, browserHistory} from "react-router";
import Layout from "./components/Layout";
import SinglePage from "./components/SinglePage";
import Upload from "./components/Upload";
import Error from "./components/Error";
//import jQuery from "jquery";
//import Fileinput from "bootstrap-fileinput";
import 'babel-polyfill'

const app = document.getElementById('app');
var Promise = require('es6-promise').Promise;
//const $ = require('jQuery');

ReactDOM.render(
    <Router history={browserHistory}>
        <Route path="/" component={Layout}>
            {/*<IndexRoute component={Home}></IndexRoute>*/}
        </Route>
        <Route path="s" component={SinglePage}>
            <Route path="file/upload/:store_type/:file_id/:user_id" component={Upload}></Route>
            <Route path="error/:msg" component={Error}></Route>
        </Route>
        
    </Router>,
app);