import React from "react";
import ReactDOM from "react-dom";
import {Router, Route, IndexRoute, browserHistory} from "react-router";

import Layout from "./components/Layout";
import SinglePage from "./components/SinglePage";
import NavPage from "./components/NavPage";
import Upload from "./components/Upload";
import Login from "./components/Login";
import Error from "./components/Error";
import Menu from "./components/Menu";
import User from "./components/web/user/User";
import Pointlog from "./components/web/mpz/pointlog/Pointlog"
import ReceiveList from "./components/web/mpe/qa/receive/ReceiveList"
import ReceivePosting from "./components/web/mpe/qa/receive/ReceivePosting"


import Test from "./components/Test";

import 'babel-polyfill'

const app = document.getElementById('app');

ReactDOM.render(
    <Router history={browserHistory}>
        <Route path="/" component={Layout}>
            {/*<IndexRoute component={Home}></IndexRoute>*/}
        </Route>
        <Route path="s" component={SinglePage}>
            <Route path="file/upload/:store_type/:file_id/:user_id" component={Upload}></Route>
            <Route path="error/:msg" component={Error}></Route>
        </Route>
        <Route path="web" component={SinglePage}>
            <Route path="login/:system" component={Login}></Route>
        </Route>
        <Route path="auth">
            <Route path="web" component={NavPage}>
                <Route path="menu" component={Menu}></Route>
                <Route path="user" component={User}></Route>
                <Route path="mpz">
                    <Route path="pointlog" component={Pointlog}></Route>
                </Route>
                <Route path="mpe">
                    <Route path="qa">
                        <Route path="receive/list" component={ReceiveList}></Route>
                        <Route path="receive/posting/:lsa_no" component={ReceivePosting}></Route>
                    </Route>
                </Route>
            </Route>
        </Route>
        <Route path="ui" component={SinglePage}>
            <Route path="test" component={Test}></Route>
        </Route>
    </Router>,
app);