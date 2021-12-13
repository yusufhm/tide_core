<?php

namespace Drupal\tide_core\Commands;

use Drush\Commands\DrushCommands;

/**
 * A drush command file.
 *
 * @package Drupal\tide_core\Commands
 */
class UpdateEditorLinks extends DrushCommands {

  /**
   * Install anchor_link module and add settings.
   *
   * @command tide-core:update-editor-links
   * @aliases update-editor-links
   * @usage tide-core:update-editor-links
   */
  public function update() {
    // Install anchor_link module.
    if (\Drupal::service('module_handler')->moduleExists('anchor_link')) {
      return;
    }
    $module_installer = \Drupal::service('module_installer');
    $module_installer->install(['anchor_link']);

    $this->output()->writeln('Successfully installed: anchor_link');

    $config_factory = \Drupal::configFactory();
    $editor_formats = [
      'editor.editor.admin_text',
      'editor.editor.rich_text',
    ];

    // Add anchor links to the tool bars.
    $link_buttons = [
      'Link',
      'Unlink',
    ];
    foreach ($editor_formats as $format) {
      $editable_config = $config_factory->getEditable($format);
      if ($editable_config->isNew()) {
        continue;
      }
      $toolbar_groups = $editable_config->get('settings.toolbar.rows.0');

      // No toolbar added yet.
      if (!isset($toolbar_groups)) {
        continue;
      }

      // Link group exist, add anchor links to the group.
      if ($links_group = array_search('Links', array_column($toolbar_groups, 'name'))) {
        $toolbar_groups[$links_group]['items'] = array_unique(array_merge($toolbar_groups[$links_group]['items'], $link_buttons));
      }
      else {
        $toolbar_groups[] = [
          'name' => 'Links',
          'items' => $link_buttons,
        ];
      }

      $editable_config->set('settings.toolbar.rows.0', $toolbar_groups);
      $editable_config->save();
    }

    $this->output()->writeln('Successfully imported anchor_link configurations');

  }

}
