// routes/auth/web/index.js
import React from "react";
import qa from './qa/index';
import qc from './qc/index';

const mpe = {
    path: 'mpe',
    childRoutes: [
        qa,
        qc,
    ],
};
export default mpe;