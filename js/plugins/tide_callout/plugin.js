/**
 * @file
 * Callout plugin definition.
 */

CKEDITOR.plugins.add('tide_callout', {
  init: function(editor) {
    'use strict';

    editor.addCommand( 'callout_template', {
      exec: function(editor) {
        editor.insertHtml( '<div class="callout-wrapper"><div class="callout-wrapper__title">Lorem ipsum dolor</div><div class="callout-wrapper__content">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</div></div>' );
      }
    });

    editor.ui.addButton('tide_callout', {
      label: 'Callout template', //this is the tooltip text for the button
      toolbar: 'insert',
      command: 'callout_template',
      icon: this.path + 'images/icon.png'
    });
  }
});
