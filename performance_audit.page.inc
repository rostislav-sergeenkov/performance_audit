<?php

/**
 * @file
 * performance_audit.page.inc
 */

/**
 * Returns content for the performance report page.
 *
 * @todo break content of the page into tabs depending on the type of configuration option.
 *
 * @return array
 */
function performance_audit_get_content() {
  $analyzer = new PerformanceAuditPHPAnalyzer(PERFORMANCE_AUDIT_PHP_INI);
  $data = $analyzer->analyze();

  return array(
    'content' => array(
      '#theme' => 'performance_audit_page',
      '#data' => $data,
      '#attached' => array(
        'css' => array(
          'data' => drupal_get_path('module', 'performance_audit') . '/performance_audit.css',
        ),
      ),
    ),
  );
}

/**
 * Returns themed representation of Performance Audit page.
 *
 * @param $data
 *
 * @return string
 */
function theme_performance_audit_page($variables) {
  $link = '<p><strong>' . t('Drupal Community Documentation') . '</strong>: ' . $variables['data']['type'] . '.</p>';
  $header = array(
    t('Name'),
    t('Description'),
    t('Value'),
    t('Recommendation'),
    t('Note'),
  );
  $rows = array();

  foreach ($variables['data']['options'] as $item) {
    $rows[] = array(
      'data' => array(
        $item['name'],
        $item['description'],
        $item['actual'],
        $item['recommendation'],
        $item['note'],
      ),
      'class' => array($item['status']),
    );
  }

  return $link . theme('table',
    array(
      'header' => $header,
      'rows' => $rows,
      'attributes' => array(
        'class' => array('performance_audit_table'),
      ),
    )
  );
}
