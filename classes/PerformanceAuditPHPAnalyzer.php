<?php

/**
 * @file
 * Class PerformanceAuditPHPAnalyzer.
 */

// @todo Drupal requirements: https://www.drupal.org/requirements.
// @todo Drupal 7: PHP 5.2.5 or higher (5.4 or higher recommended).

class PerformanceAuditPHPAnalyzer {
  const STATUS_OK = 'status';
  const STATUS_WARNING = 'warning';
  const STATUS_ERROR = 'error';
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
        // @see @todo https://www.drupal.org/requirements/php.
        return array(
          'allow_url_fopen',
          'display_errors',
          'expose_php',
          'max_input_time',
          'max_execution_time',
          'memory_limit',
          'post_max_size',
          'max_input_vars',
          'register_globals',
          'upload_max_filesize',
        );
        break;

      // @todo define constants inside the class.

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

  function analyze() {
    $data = array();
    $type_method = 'analyze_configuration_' . $this->type;

    if (method_exists($this, $type_method)) {
      $data['type'] = $this->$type_method();
    }

    foreach ($this->options as $option_name => $option_value) {
      $option_method = 'analyze_' . $option_name;

      if (method_exists($this, $option_method)) {
        $data['options'][] = $this->$option_method($option_name, $option_value);
      }
    }

    return $data;
  }

  // @todo think how to cache this variables. Probably this check should be run only by cron and then cache in variables.

  /**
   * Analyses and returns general recommendations about PHP_INI configuration.
   *
   * @return string
   */
  function analyze_configuration_php_ini() {
    return l(t('System Requirements: PHP'), 'https://www.drupal.org/requirements/php');
  }

  /**
   * Analyzes and returns information about memory_limit option.
   *
   * @param $name
   * @param $value
   *
   * @return array
   */
  function analyze_memory_limit($name, $value) {
    $recommendations = array();
    $notes = array();
    $description = t('The maximum amount of memory in bytes that a script is allowed to allocate. This helps prevent poorly written scripts for eating up all available memory on a server.');
    $status = self::STATUS_OK;
    // 32 MB for Drupal 7 is required.
    $required_value = 32;
    $integer_value = intval($value['local_value']);
    $recommendations[] = t('Drupal 7 core requires PHP\'s memory_limit to be at least !limit MB.',
      array('!limit' => $required_value));

    if ($integer_value < $required_value) {
      $status = self::STATUS_ERROR;
    }

    $notes = $notes + array(
      t('XHprof is a good solution to find high memory consumption.'),
      t('See this page for more instructions on how to install and use it: !link.',
        array('!link' => l('http://groups.drupal.org/node/82889', 'http://groups.drupal.org/node/82889'))),
    );

    return array(
      'name' => $name,
      'actual' => $value['local_value'],
      'description' => $description,
      'recommendation' => implode(' ', $recommendations),
      'note' => implode(' ', $notes),
      'status' => $status,
    );
  }

  /**
   * Analyzes and returns information about max_execution_time option.
   *
   * @param $name
   * @param $value
   *
   * @return array
   */
  function analyze_max_execution_time($name, $value) {
    $recommendations = array();
    $notes = array();
    $description = t('The number of seconds a script is allowed to run. If this is reached, the script returns a fatal error. The default limit is 30 seconds.');
    $status = self::STATUS_OK;
    // 32 MB for Drupal 7 is required.
    $required_value = 30;
    $integer_value = intval($value['local_value']);
    $recommendations[] = t('Drupal 7 core requires PHP\'s max_execution_time to be at least !limit sec.',
      array('!limit' => $required_value));

    if ($integer_value < $required_value) {
      $status = self::STATUS_ERROR;
    }

    return array(
      'name' => $name,
      'description' => $description,
      'actual' => $value['local_value'],
      'recommendation' => implode(' ', $recommendations),
      'note' => implode(' ', $notes),
      'status' => $status,
    );
  }

  /**
   * Analyzes and returns information about allow_url_fopen option.
   *
   * @param $name
   * @param $value
   *
   * @return array
   */
  function analyze_allow_url_fopen($name, $value) {
    $recommendations = array();
    $notes = array();
    $description = t('This option enables the URL-aware fopen wrappers that enable accessing URL object like files. Default wrappers are provided for the access of remote files using the FTP or HTTP protocol.');
    $status = self::STATUS_OK;
    // 32 MB for Drupal 7 is required.
    $required_value = 0;
    $integer_value = intval($value['local_value']);
    $recommendations[] = t('This is a security issue. Must be OFF or nonexistent.');

    if ($integer_value > $required_value) {
      $status = self::STATUS_ERROR;
    }

    $notes = $notes + array(
        t('If enabled, allow_url_fopen allows PHP\'s file functions - such as file_get_contents() and the include and require statements - can retrieve data from remote locations, like an FTP or web site.'),
        t('Programmers frequently forget this and don\'t do proper input filtering when passing user-provided data to these functions, opening them up to code injection vulnerabilities.'),
        t('A large number of code injection vulnerabilities reported in PHP-based web applications are caused by the combination of enabling allow_url_fopen and bad input filtering.'),
        l('http://phpsec.org/projects/phpsecinfo/tests/allow_url_fopen.html', 'http://phpsec.org/projects/phpsecinfo/tests/allow_url_fopen.html'),
      );

    return array(
      'name' => $name,
      'description' => $description,
      'actual' => $value['local_value'],
      'recommendation' => implode(' ', $recommendations),
      'note' => implode(' ', $notes),
      'status' => $status,
    );
  }

  /**
   * Analyzes and returns information about register_globals option.
   *
   * @param $name
   * @param $value
   *
   * @return array
   */
  function analyze_register_globals($name, $value) {
    if (PHP_VERSION_ID < 50400) {
      $recommendations = array();
      $notes = array();
      $description = t('A common security problem with PHP is the register_globals setting in PHP configuration file. This setting (that can be either On or Off) tells whether or not to register the contents of the EGPCS (Environment, GET, POST, Cookie, Server) variables as global variables.');
      $status = self::STATUS_OK;
      // 32 MB for Drupal 7 is required.
      $required_value = 0;
      $integer_value = intval($value['local_value']);
      $recommendations[] = t('This is a security issue. Must be OFF or nonexistent.');

      if ($integer_value > $required_value) {
        $status = self::STATUS_ERROR;
      }

      $notes = $notes + array(
          t('This feature has been deprecated as of PHP 5.3.0 and removed as of PHP 5.4.0.'),
        );

      return array(
        'name' => $name,
        'description' => $description,
        'actual' => $value['local_value'],
        'recommendation' => implode(' ', $recommendations),
        'note' => implode(' ', $notes),
        'status' => $status,
      );
    }

    return array();
  }

  /**
   * Analyzes and returns information about expose_php option.
   *
   * @param $name
   * @param $value
   *
   * @return array
   */
  function analyze_expose_php($name, $value) {
    $recommendations = array();
    $notes = array();
    $description = t('Shows current PHP version in all header requests, security disclosure. Example: X-Powered-By: PHP/!php_version.',
      array('!php_version' => PHP_VERSION));
    $status = self::STATUS_OK;
    // 32 MB for Drupal 7 is required.
    $required_value = 0;
    $integer_value = intval($value['local_value']);
    $recommendations[] = t('This is a security issue. Must be OFF or nonexistent.');

    if ($integer_value > $required_value) {
      $status = self::STATUS_ERROR;
    }

    return array(
      'name' => $name,
      'description' => $description,
      'actual' => (int) $value['local_value'],
      'recommendation' => implode(' ', $recommendations),
      'note' => implode(' ', $notes),
      'status' => $status,
    );
  }

  /**
   * Analyzes and returns information about display_errors option.
   *
   * @param $name
   * @param $value
   *
   * @return array
   */
  function analyze_display_errors($name, $value) {
    $recommendations = array();
    $notes = array();
    $description = t('Hides errors output to display (website) we want to send to log file instead.');
    $status = self::STATUS_OK;
    // 32 MB for Drupal 7 is required.
    $required_value = 0;
    $integer_value = intval($value['local_value']);

    if ($integer_value > $required_value) {
      $status = self::STATUS_WARNING;
      $recommendations[] = t('This is a feature to support your development and should never be used on production systems (e.g. systems connected to the internet).');
    }

    return array(
      'name' => $name,
      'description' => $description,
      'actual' => $value['local_value'],
      'recommendation' => implode(' ', $recommendations),
      'note' => implode(' ', $notes),
      'status' => $status,
    );
  }
}
