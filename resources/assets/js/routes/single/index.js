// routes/single/index.js
import React from "react";
const single = {
    path: 's',
    getComponent(nextState, cb) {
        require.ensure([], (require) => {
            cb(null, require('../../components/sys/layout/SinglePage').default)
        }, 'singlePage')
    },
    childRoutes: [
        {
            path: 'file/upload/:store_type/:file_id/:user_id',
            getComponent(location, callback) {
                require.ensure([], (require) => {
                    callback(null, require('../../components/web/service/Upload').default)
                }, 'fileUpload')
            }
        },
        {
            path: 'error/:msg',
            getComponent(location, callback) {
                require.ensure([], (require) => {
                    callback(null, require('../../components/sys/layout/Error').default)
                }, 'error')
            }
        },
    ],
};
export default single;