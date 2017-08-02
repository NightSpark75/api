// routes/auth/index.js
import React from "react";
import web from './web/index';
const single = {
    path: 'auth',
    childRoutes: [
        web,
    ],
};
export default single;