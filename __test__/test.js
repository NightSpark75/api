import React from 'react'
import renderer from 'react-test-renderer'
 
describe('About Test', () => {
 
  it('is test', function() {
    // Render into document
    /*
    let about = TestUtils.renderIntoDocument(<About />);
    expect(TestUtils.isCompositeComponent(about)).toBeTruthy();*/
    expect(2 + 2).toBe(4)
  });
});