<?php

namespace Drupal\tide_core\Plugin\views\filter;

use Drupal\Core\Datetime\DateHelper;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\views\Plugin\views\filter\FilterPluginBase;

/**
 * @file
 * Month Year view filter.
 */

/**
 * Month Year view filter.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("tide_core_month_year_filter")
 */
class MonthYearFilter extends FilterPluginBase implements ContainerFactoryPluginInterface {

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['expose']['contains']['month_format']['default'] = 'full';
    $options['expose']['contains']['year_range']['default'] = 5;
    $options['value']['contains']['month']['default'] = '';
    $options['value']['contains']['year']['default'] = '';

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildExposeForm(&$form, FormStateInterface $form_state) {
    parent::buildExposeForm($form, $form_state);

    $settings = $this->options['expose'];

    $form['expose']['month_format'] = [
      '#type' => 'select',
      '#title' => $this->t('Month format'),
      '#options' => [
        'short' => $this->t('Short textual format (eg. %example)', ['%example' => date('M')]),
        'full' => $this->t('Full textual format (eg. %example)', ['%example' => date('F')]),
      ],
      '#default_value' => !empty($settings['month_format']) ? $settings['month_format'] : 'full',
      '#multiple' => FALSE,
    ];

    $form['expose']['year_range'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Year range'),
      '#default_value' => !empty($settings['year_range']) ? $settings['year_range'] : 5,
      '#size' => 10,
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function valueForm(&$form, FormStateInterface $form_state) {
    parent::valueForm($form, $form_state);

    $exposed = $form_state->get('exposed');
    if ($exposed) {
      $settings = $this->options['expose'];
      $month_format = !empty($settings['month_format']) ? $settings['month_format'] : 'full';
      $year_range = !empty($settings['year_range']) ? intval($settings['year_range']) : 10;

      $months = ['' => $this->t('-All months-')];
      if ($month_format == 'full') {
        $months += DateHelper::monthNames();
      }
      else {
        $months += DateHelper::monthNamesAbbr();
      }
      $form['value']['month'] = [
        '#type' => 'select',
        '#title' => $this->t('Month'),
        '#options' => $months,
        '#multiple' => FALSE,
        '#default_value' => NULL,
      ];

      $current_year = intval(date('Y'));
      $years = ['' => $this->t('-All years-')];
      for ($year = $current_year - $year_range + 1; $year <= $current_year; $year++) {
        $years[$year] = $year;
      }
      $form['value']['year'] = [
        '#type' => 'select',
        '#title' => $this->t('Year'),
        '#options' => $years,
        '#multiple' => FALSE,
        '#default_value' => NULL,
      ];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function acceptExposedInput($input) {
    if (empty($this->options['exposed'])) {
      return TRUE;
    }

    $month = '';
    if (!empty($input['month'])) {
      $month = intval($input['month']);
      if ($month < 1 && $month > 12) {
        return FALSE;
      }
    }

    $year = '';
    if (!empty($input['year'])) {
      $year = intval($input['year']);
    }

    $this->value = [
      'month' => $month,
      'year' => $year,
    ];

    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    $this->ensureMyTable();
    $field = $this->tableAlias . '.' . $this->realField;
    $value = $this->value;
    if (!empty($value['month'])) {
      $month = intval($value['month']);
      $this->query->addWhereExpression($this->options['group'], "MONTH({$field}) = {$month}");
    }
    if (!empty($value['year'])) {
      $year = intval($value['year']);
      $this->query->addWhereExpression($this->options['group'], "YEAR({$field}) = {$year}");
    }
  }

}
