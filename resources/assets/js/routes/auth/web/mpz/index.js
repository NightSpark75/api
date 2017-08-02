// routes/auth/web/mpz/index.js
import React from "react";

const mpe = {
    path: 'mpz',
    childRoutes: [
        {
            path: 'pointlog',
            getComponent(location, callback) {
                require.ensure([], (require) => {
                    callback(null, require('../../../components/web/mpz/pointlog/Pointlog').default)
                }, 'mpzPointlog')
            }
        },
    ],
};
export default mpe;