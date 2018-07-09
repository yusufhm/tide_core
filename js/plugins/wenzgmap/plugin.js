/**
 * @file
 * Google Map plugin definition.
 */

CKEDITOR.plugins.add('wenzgmap', {
  icons: 'wenzgmap',
  init: function (editor) {
    'use strict';

    editor.addCommand('wenzgmapDialog', new CKEDITOR.dialogCommand('wenzgmapDialog'));
    editor.ui.addButton('wenzgmap', {
      label: 'Insert a google map',
      command: 'wenzgmapDialog',
      toolbar: 'paragraph'
    });

    CKEDITOR.dialog.add('wenzgmapDialog', this.path + 'dialogs/wenzgmap.js');
  },
  afterInit: function (editor) {
    'use strict';
    // Add integration with IFrame plugin: it replaces all <iframe> elements
    // with a placeholder object. We are capturing the original replacement
    // callback from Iframe plugin and adding a condition to call it only if
    // the source of the element does not contain google maps url.
    var dataProcessor = editor.dataProcessor;
    var dataFilter = dataProcessor && dataProcessor.dataFilter;

    if (dataFilter && dataFilter.elementsRules && dataFilter.elementsRules.iframe && dataFilter.elementsRules.iframe.rules) {
      var originalRules = dataFilter.elementsRules.iframe.rules.splice(1, 1);
      var originalRule = originalRules.pop();

      dataFilter.addRules({
        elements: {
          // 'iframe' key is used to rewrite the rule provided by Iframe plugin.
          iframe: function (element) {
            if (element.attributes.src.indexOf('maps.google.com') > -1) {
              return element;
            }
            return originalRule && originalRule.value ? originalRule.value.call(this, element) : element;
          }
        }
      });
    }
  }
});
