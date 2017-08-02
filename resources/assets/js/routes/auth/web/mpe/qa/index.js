// routes/auth/web/qa/index.js
import React from "react";
const qa = {
    path: 'qa',
    childRoutes: [
        {
            path: 'receive/list',
            getComponent(location, callback) {
                require.ensure([], (require) => {
                    callback(null, require('../../../../../components/web/mpe/qa/receive/Receive').default)
                }, 'qaReceive')
            }
        },
        {
            path: 'retained/list',
            getComponent(location, callback) {
                require.ensure([], (require) => {
                    callback(null, require('../../../../../components/web/mpe/qa/retained/Retained').default)
                }, 'qaRetained')
            }
        },
        {
            path: 'stock/storage/change',
            getComponent(location, callback) {
                require.ensure([], (require) => {
                    callback(null, require('../../../../../components/web/mpe/qa/stock/storage/Change').default)
                }, 'qaStoreChange')
            }
        },
    ],
};
export default qa;