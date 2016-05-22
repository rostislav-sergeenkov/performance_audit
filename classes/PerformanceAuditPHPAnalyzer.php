<?php

/**
 * @file
 * Class PerformanceAuditPHPAnalyzer.
 */

class PerformanceAuditPHPAnalyzer {
  /**
   * @var string
   *   Type of the configuration options.
   */
  private $type;
  /**
   * @var array
   *   Array of all configuration options of the provided type.
   */
  private $data;
  /**
   * @var array
   * Array of supported options of the provided type.
   */
  private $options;

  function __construct($type) {
    $this->type = $type;
    $this->data = PerformanceAuditPHPCollector::getSettings($type);
    $this->options = $this->getOptions();
  }

  function getSupportedOptions() {
    switch ($this->type) {
      // https://www.prestashop.com/blog/en/php-ini-file/

      case PERFORMANCE_AUDIT_PHP_INI:
        return array(
          'max_input_time',
          'max_execution_time',
          'memory_limit',
          'post_max_size',
          'upload_max_filesize',
          'max_input_vars',
          'suhosin.get.max_vars',
          'suhosin.post.max_vars',
        );
        break;

      // Opcode caching
      case PERFORMANCE_AUDIT_PHP_EXTENSIONS:
        return $this->data;
        break;

      case PERFORMANCE_AUDIT_PHP_VERSION:
        return $this->data;
        break;

      case PERFORMANCE_AUDIT_PHP_CONSTANTS:
        return $this->data;
        break;

      case PERFORMANCE_AUDIT_OS:
        return $this->data;
        break;

      case PERFORMANCE_AUDIT_APACHE_MODULES:
        return $this->data;
        break;

      case PERFORMANCE_AUDIT_ENV:
        return $this->data;
        break;

      case PERFORMANCE_AUDIT_SERVER:
        return $this->data;
        break;

      case PERFORMANCE_AUDIT_STREAM_WRAPPERS:
        return $this->data;
        break;
    }
  }

  function getOptions() {
    $options = array();
    $supported_options = array_intersect(array_keys($this->data), $this->getSupportedOptions());

    foreach ($supported_options as $option) {
      $options[$option] = $this->data[$option];
    }

    return $options;
  }

  // @todo introduce 3 levels of recommendation: good (green), warning (orange), attention (red).
  function analyze() {
    return $this->options;
  }
}
