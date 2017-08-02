// routes/auth/web/qc/index.js
import React from "react";
const qc = {
    path: 'qc',
    childRoutes: [
        {
            path: 'receive',
            getComponent(location, callback) {
                require.ensure([], (require) => {
                    callback(null, require('../../../../../components/web/mpe/qc/receive/Receive').default)
                }, 'qcReceive')
            }
        },
    ],
};
export default qc;