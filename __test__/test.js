import React from 'react'
import renderer from 'react-test-renderer'

import Error from '../resources/assets/js/components/sys/layout/Error'
import Hello from '../resources/assets/js/components/sys/layout/Hello'
 
describe('component/layout/sys', () => {
  it('error', () => {
    let params = {msg: 'test'}
    const app = renderer.create(<Error params={params} />)
    const json = app.toJSON()
    expect(json).toMatchSnapshot()
  })

  it('hello', () => {
    const app = renderer.create(<Hello />)
    const json = app.toJSON()
    expect(json).toMatchSnapshot()
  })
})