import React from "react"

export default class Error extends React.Component {
  render() {
    return (
      <article class="message is-danger">
        <div class="message-body">
          {this.props.params.msg}
        </div>
      </article>
    )
  }
}