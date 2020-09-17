/**
 * @file
 * Callout plugin definition.
 */

CKEDITOR.plugins.add('tide_callout', {
  init: function(editor) {
    'use strict';

    editor.addCommand( 'callout_template', {
      exec: function(editor) {
        editor.insertHtml( '<div class="callout-wrapper"><h2>Lorem ipsum dolor</h2><p>Lorem ipsum dolor</p><ul><li>Lorem ipsum dolor</li><li>Lorem ipsum dolor</li></ul></div>' );
      }
    });

    editor.ui.addButton('Callout', {
      label: 'Callout template', //this is the tooltip text for the button
      toolbar: 'insert',
      command: 'callout_template',
      icon: this.path + 'images/icon.png'
    });
  }
});
