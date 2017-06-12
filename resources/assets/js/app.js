import React from "react";
import ReactDOM from "react-dom";
import {Router, Route, IndexRoute, browserHistory} from "react-router";
import Layout from "./components/Layout";
import SinglePage from "./components/SinglePage";
import Home from "./components/Home";
import Users from "./components/Users";
import Articles from "./components/Articles";
import Upload from "./components/Upload";

const app = document.getElementById('app');

ReactDOM.render(
    <Router history={browserHistory}>
        <Route path="/" component={Layout}>
            <IndexRoute component={Home}></IndexRoute>
            <Route path="users" component={Users}></Route>
            <Route path="articles" component={Articles}></Route>
            <Route path="upload" component={Upload}></Route>
        </Route>
        <Route path="s" component={SinglePage}>
            <Route path="file/upload/:file_id" component={Upload}></Route>
        </Route>
    </Router>,
app);