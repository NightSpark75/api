// routes/auth/web/index.js
//import React from "react";
import mpe from './mpe/index';
const auth_web = {
    path: 'web',
    getComponent(location, callback) {
        require.ensure([], (require) => {
            callback(null, require('../../../components/sys/layout/NavPage').default)
        }, 'navPage')
    },
    childRoutes: [
        {
            path: 'menu',
            getComponent(location, callback) {
                require.ensure([], (require) => {
                    callback(null, require('../../../components/sys/layout/Menu').default)
                }, 'webMenu')
            }
        },
        {
            path: 'user',
            getComponent(location, callback) {
                require.ensure([], (require) => {
                    callback(null, require('../../../components/web/user/User').default)
                }, 'webUser')
            }
        },
        mpe,
    ],
};
export default auth_web;