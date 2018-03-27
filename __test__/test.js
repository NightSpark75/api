import React from 'react'
import renderer from 'react-test-renderer'

import Menu from '../resources/assets/js/components/includes/v_menu'
 
describe('About Test', () => {

  
 
  it('is test', function() {
    // Render into document
    /*
    let about = TestUtils.renderIntoDocument(<About />);
    expect(TestUtils.isCompositeComponent(about)).toBeTruthy();*/
    const app = renderer.create(<Menu />)
    const json = app.toJSON();
    expect(json).toMatchSnapshot();
  });
});