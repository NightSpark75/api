import React from "react"
export default class Confirm extends React.Component {
  render() {
    const { show, title, content, onConfirm, onCancel, btnConfirm, btnCancel } = this.props
    return (
      <div className={"modal" + show? ' is-active': ''} style={{position: 'fixed'}}>
        <div className="modal-background" style={{position: 'fixed'}}></div>
        <div style={{position: 'fixed', top: '0', bottom: '0', left: '0', right: '0'}}>
          <div className="modal-card" style={{marginTop: '100px'}}>
            <header className="modal-card-head">
              <p className="modal-card-title">{title}</p>
            </header>
            <section className="modal-card-body">
              {content}
            </section>
            <footer className="modal-card-foot" style={{float: 'right'}}>
              <button className="button is-success" onClick={onConfirm}>{btnConfirm}</button>
              <button className="button" onClick={onCancel}>{btnCancel}</button>
            </footer>
          </div>
        </div>
      </div>
    )
  }
}