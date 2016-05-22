<?php

/**
 * @file
 * Class PerformanceAuditPHPCollector.
 */

class PerformanceAuditPHPCollector {
  /**
   * Returns array of configuration settings.
   *
   * @param string $type
   *
   * @return array|string
   */
  public static function getSettings($type) {
    switch($type) {
      case PERFORMANCE_AUDIT_PHP_INI:
        return ini_get_all();
        break;

      case PERFORMANCE_AUDIT_PHP_EXTENSIONS:
        return get_loaded_extensions();
        break;

      case PERFORMANCE_AUDIT_PHP_VERSION:
        return phpversion();
        break;

      case PERFORMANCE_AUDIT_PHP_CONSTANTS:
        return get_defined_constants();
        break;

      case PERFORMANCE_AUDIT_OS:
        return php_uname();
        break;

      case PERFORMANCE_AUDIT_APACHE_MODULES:
        return apache_get_modules();
        break;

      case PERFORMANCE_AUDIT_ENV:
        return $_ENV;
        break;

      case PERFORMANCE_AUDIT_SERVER:
        return $_SERVER;
        break;

      case PERFORMANCE_AUDIT_STREAM_WRAPPERS:
        return stream_get_wrappers();
        break;
    }
  }
}
