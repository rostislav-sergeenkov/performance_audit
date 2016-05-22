<?php

/**
 * @file
 * performance_audit.block.inc
 */

/**
 * Returns content for the specified block.
 *
 * @param string $delta
 *
 * return array
 */
function performance_audit_block_get_content($delta = 'performance_audit_php') {
  if ($delta == 'performance_audit_php') {
    return array(
      'open_map_button' => array(
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#value' => t('Some Text'),
        '#attributes' => array(
          'id' => 'some-id',
          'class' => array('some-class'),
        ),
      ),
    );
  }
}
