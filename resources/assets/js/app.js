// v 1.23.5
import React from "react"
import ReactDOM from "react-dom"
import {Router, Route, browserHistory, IndexRoute, hashHistory} from "react-router"

import Hello from "./components/sys/layout/Hello"
import SinglePage from "./components/sys/layout/SinglePage"
import NavPage from "./components/sys/layout/NavPage"
import Upload from "./components/web/service/Upload"
import Login from "./components/sys/layout/Login"
import Error from "./components/sys/layout/Error"
import Menu from "./components/sys/layout/Menu"
import User from "./components/web/user/User"
import Pointlog from "./components/web/mpz/pointlog/Pointlog"
import QAReceive from "./components/web/mpe/qa/receive/Receive"
import QARestore from "./components/web/mpe/qa/restore/Restore"
import Retained from "./components/web/mpe/qa/retained/Retained"
import Change from "./components/web/mpe/qa/stock/storage/Change"
import QCReceive from "./components/web/mpe/qc/receive/Receive"
import Production_Job from "./components/web/mpb/production/Job"
import Production_Working from "./components/web/mpb/production/Working"
import Production_Material from "./components/web/mpb/production/Material"
import Packing_Job from "./components/web/mpb/packing/Job"
import Packing_Working from "./components/web/mpb/packing/Working"
import Package_Job from "./components/web/mpb/package/Job"
import Package_Working from "./components/web/mpb/package/Working"
import QCPartInfo from "./components/web/mpe/qc/part/Info"

import Menu1 from "./components/test/menu1"
import page1 from "./components/test/page1"
import page2 from "./components/test/page2"
import page3 from "./components/test/page3"

const app = document.getElementById('app')

ReactDOM.render(
    <Router history={browserHistory}>
        <Route path="/">
            <IndexRoute component={Hello}></IndexRoute>
        </Route>
        <Route path="test">
            <Route path="menu1" component={Menu1}>
                <Route path="page1" component={page1}></Route>
                <Route path="page2" component={page2}></Route>
                <Route path="page3" component={page3}></Route>
            </Route>
        </Route>
        <Route path="s" component={SinglePage}>
            <Route path="file/upload/:store_type/:file_id/:user_id" component={Upload}></Route>
            <Route path="error/:msg" component={Error}></Route>
            <Route path="qc">
                <Route path="document" component={QCPartInfo}></Route>
            </Route>
        </Route>
        <Route path="web" component={SinglePage}>
            <Route path="login/:system" component={Login}></Route>
        </Route>
        <Route path="auth">
            <Route path="web" component={NavPage}>
                <Route path="menu" component={Menu}></Route>
                <Route path="user" component={User}></Route>
                <Route path="mpb">
                    <Route path="prod">
                        <Route path="list" component={Production_Job}></Route>
                        <Route path="material/:sno/:psno" component={Production_Material}></Route>
                        <Route path="working/:sno/:psno" component={Production_Working}></Route>
                    </Route>
                    <Route path="package">
                        <Route path="list" component={Package_Job}></Route>
                        <Route path="working/:sno/:psno" component={Package_Working}></Route>
                    </Route>
                    <Route path="packing">
                        <Route path="list" component={Packing_Job}></Route>
                        <Route path="working/:sno/:psno" component={Packing_Working}></Route>
                    </Route>
                </Route>
                <Route path="mpz">
                    <Route path="pointlog" component={Pointlog}></Route>
                </Route>
                <Route path="mpe">
                    <Route path="qa">
                        <Route path="receive/list" component={QAReceive}></Route>
                        <Route path="restore/list" component={QARestore}></Route>
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
app)