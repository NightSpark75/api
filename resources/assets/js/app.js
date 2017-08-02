import React from "react";
import ReactDOM from "react-dom";
import {Router, Route, browserHistory, IndexRoute, hashHistory} from "react-router";

import Hello from "./components/sys/layout/Hello";
import SinglePage from "./components/sys/layout/SinglePage";
import NavPage from "./components/sys/layout/NavPage";
import Upload from "./components/web/service/Upload";
import Login from "./components/sys/layout/Login";
import Error from "./components/sys/layout/Error";
import Menu from "./components/sys/layout/Menu";
import User from "./components/web/user/User";
import Pointlog from "./components/web/mpz/pointlog/Pointlog";
import QAReceive from "./components/web/mpe/qa/receive/Receive";
import Retained from "./components/web/mpe/qa/retained/Retained";
import Change from "./components/web/mpe/qa/stock/storage/Change";
import QCReceive from "./components/web/mpe/qc/receive/Receive";

const app = document.getElementById('app');

ReactDOM.render(
    <Router history={browserHistory}>
        <Route path="/">
            <IndexRoute component={Hello}></IndexRoute>
        </Route>
        <Route path="s" component={SinglePage}>
            <Route path="file/upload/:store_type/:file_id/:user_id" component={Upload}></Route>
            <Route path="error/:msg" component={Error}></Route>
        </Route>
        <Route path="web" component={SinglePage}>
            <Route path="login/:system" 
                getComponent = {
                    (Location, callback) => {
                        require.ensure([], (require) => {
                            callback(null, require('./components/sys/layout/Login').default)
                        }, 'Login')
                    }
                }
            >
            </Route>
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
                        <Route path="receive/list" component={QAReceive}></Route>
                        <Route path="retained/list" component={Retained}></Route>
                        <Route path="stock">
                            <Route path="storage/Change" component={Change}></Route>
                        </Route>
                    </Route>
                    <Route path="qc">
                        <Route path="receive" component={QCReceive}></Route>
                    </Route>
                </Route>
            </Route>
        </Route>
    </Router>,
app);

/*
import web from './routes/web/index';
import single from './routes/single/index';
import auth from './routes/auth/index';

const rootRoute = {
    path: '/',
    indexRoute: {
        getComponent(nextState, cb) { 
            require.ensure([], (require) => {  
            cb(null, require('./components/sys/layout/Hello').default);
            }, 'hellow');
        }
    },
    childRoutes: [
        auth,
        single,
        web,
    ]
}

ReactDOM.render(
    (
        <Router
            history={browserHistory}
            routes={rootRoute}
        />
    ), document.getElementById('app')
);
*/