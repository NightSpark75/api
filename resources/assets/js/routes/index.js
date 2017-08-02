// routes/web/index.js
import React from "react";
const web = {
    path: 'web',
    getComponent(nextState, cb) {
        require.ensure([], (require) => {
            cb(null, require('../../components/sys/layout/SinglePage').default)
        }, 'singlePage')
    },
    childRoutes: [
        {
            path: 'login/:system',
            getComponent(location, callback) {
                require.ensure([], (require) => {
                    callback(null, require('../../components/sys/layout/Login').default)
                }, 'login')
            }
        },
    ],
};
export default web;