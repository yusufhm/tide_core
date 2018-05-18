<?php

/**
 * @file
 * Create demo menus.
 */

use Drupal\system\Entity\Menu;

/**
 * Implements hook_vicgovau_demo_create_ENTITY_TYPE_BUNDLE().
 */
function vicgovau_demo_create_menu_menu() {
  $menus = [];

  $menu_data = [
    'Main menu - demo.vic.gov.au' => [
      'id' => 'main_menu_vicgovau_demo',
      'description' => 'Demo main menu',
    ],
    'Footer menu - demo.vic.gov.au' => [
      'id' => 'footer_menu_vicgovau_demo',
      'description' => 'Demo footer menu',
    ],
    'Main menu - demo2.vic.gov.au' => [
      'id' => 'main_menu_vicgovau_demo2',
      'description' => 'Demo 2 main menu',
    ],
    'Footer menu - demo2.vic.gov.au' => [
      'id' => 'footer_menu_vicgovau_demo2',
      'description' => 'Demo 2 footer menu',
    ],
    'Main menu - another.demo.vic.gov.au' => [
      'id' => 'main_menu_vicgovau_demo_another',
      'description' => 'Another Demo menu',
    ],
    'Footer menu - another.demo.vic.gov.au' => [
      'id' => 'footer_menu_vicgovau_demo_another',
      'description' => 'Another Demo footer menu',
    ],
  ];

  foreach ($menu_data as $label => $data) {
    try {
      $menu = Menu::create([
        'id' => $data['id'],
        'label' => $label,
        'description' => $data['description'],
      ]);
      $menu->save();
      $menus[] = $menu;
    }
    catch (Exception $exception) {
      watchdog_exception('vicgovau_demo', $exception);
    }
  }

  return $menus;
}
