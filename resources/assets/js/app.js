// v 1.23.5
import React from "react"
import ReactDOM from "react-dom"
import { Router, Route, browserHistory, IndexRoute, hashHistory } from "react-router"

import Hello from "./components/sys/layout/Hello"
import SinglePage from "./components/sys/layout/SinglePage"
import NavPage from "./components/sys/layout/NavPage"
import Upload from "./components/web/service/Upload"
import Login from "./components/sys/layout/Login"
import Password from "./components/sys/layout/password"
import Error from "./components/sys/layout/Error"
import Menu from "./components/sys/layout/Menu"
import User from "./components/web/user/User"
import Pointlog from "./components/web/mpz/pointlog/Pointlog"
import QAReceive from "./components/web/mpe/qa/receive/Receive"
import QAReceiveCheck from "./components/web/mpe/qa/receive/Check"
import QARestore from "./components/web/mpe/qa/restore/Restore"
import QAStockInfo from "./components/web/mpe/qa/Stock/item/info"
import Retained from "./components/web/mpe/qa/retained/Retained"
import Change from "./components/web/mpe/qa/stock/storage/Change"
import QCReceive from "./components/web/mpe/qc/receive/Receive"
import Production_Job from "./components/web/mpb/production/Job"
import Production_Working from "./components/web/mpb/production/Working"
import Production_Material from "./components/web/mpb/production/Material"
import Prework_Job from "./components/web/mpb/prework/Job"
import Prework_Working from "./components/web/mpb/prework/Working"
import Clean_Job from "./components/web/mpb/clean/Job"
import Clean_Dept from "./components/web/mpb/clean/Dept"
import Clean_Working from "./components/web/mpb/clean/Working"
import Packing_Job from "./components/web/mpb/packing/Job"
import Packing_Working from "./components/web/mpb/packing/Working"
import Package_Job from "./components/web/mpb/package/Job"
import Package_Working from "./components/web/mpb/package/Working"
import Package_Material from "./components/web/mpb/package/Material"
import Package_Duty from "./components/web/mpb/package/Duty"
import QCPartInfo from "./components/web/mpe/qc/part/Info"
import Inventory from "./components/web/mpm/inventory"

import Menu1 from "./components/test/menu1"
import page1 from "./components/test/page1"
import page2 from "./components/test/page2"
import page3 from "./components/test/page3"
import Calendar from './components/test/production'

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
      <Route path="calendar" component={Calendar}></Route>
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
      <Route path="password" component={Password}></Route>
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
          <Route path="prework">
            <Route path="list" component={Prework_Job}></Route>
            <Route path="working/:sno/:psno" component={Prework_Working}></Route>
          </Route>
          <Route path="clean">
            <Route path="list" component={Clean_Job}></Route>
            <Route path="dept/:sno/:deptno" component={Clean_Dept}></Route>
            <Route path="working/:sno/:deptno" component={Clean_Working}></Route>
          </Route>
          <Route path="package">
            <Route path="list" component={Package_Job}></Route>
            <Route path="material/:sno/:psno" component={Package_Material}></Route>
            <Route path="duty/:sno/:psno" component={Package_Duty}></Route>
            <Route path="working/:sno/:psno/:pgno/:duty/:group" component={Package_Working}></Route>
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
            <Route path="receive/check/list" component={QAReceiveCheck}></Route>
            <Route path="restore/list" component={QARestore}></Route>
            <Route path="retained/list" component={Retained}></Route>
            <Route path="stock">
              <Route path="storage/Change" component={Change}></Route>
              <Route path="item/info" component={QAStockInfo}></Route>
            </Route>
          </Route>
          <Route path="qc">
            <Route path="receive" component={QCReceive}></Route>
          </Route>
        </Route>
        <Route path="mpm">
          <Route path="inventory" component={Inventory}></Route>
        </Route>
      </Route>
    </Route>
  </Router>,
  app)