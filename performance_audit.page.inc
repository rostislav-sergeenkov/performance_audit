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
  $info = $analyzer->analyze();

  dpm($info);

  return array(
    '#markup' => 'test',
  );
}
