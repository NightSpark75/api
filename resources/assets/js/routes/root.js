import React from "react";
import ReactDOM from "react-dom";
import {Router, Route, browserHistory, IndexRoute, hashHistory} from "react-router";
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