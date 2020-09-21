/**
 * @file
 * Callout plugin definition.
 */

CKEDITOR.plugins.add('tide_callout', {
  init: function(editor) {
    'use strict';

    editor.addCommand( 'callout_template', {
      exec : function( editor ) {
        var selectedHtml = "";
        var selection = editor.getSelection();
        if (selection) {
          selectedHtml = getSelectionHtml(selection);
        }
        editor.insertHtml('<div class="callout-wrapper">' + selectedHtml + '</div>');
      });

    editor.ui.addButton('tide_callout', {
      label: 'Callout (WYSIWYG)', //this is the tooltip text for the button
      toolbar: 'insert',
      command: 'callout_template',
      icon: this.path + 'images/icon.png'
    });
  }
});

/**
 Get HTML of a range.
 */
function getRangeHtml(range) {
  var content = range.extractContents();
  return content.getHtml();
}
/**
 Get HTML of a selection.
 */
function getSelectionHtml(selection) {
  var ranges = selection.getRanges();
  var html = '';
  for (var i = 0; i < ranges.length; i++) {
    html += getRangeHtml(ranges[i]);
  }
  return html;
}
