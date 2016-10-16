<?php

/**
 * Declares a whitelist of hook implementations for this module.
 *
 * Modules that implement this hook and return an array opt out of regular hook
 * discovery, and implement only those hooks explicitly declared here.
 *
 * @return mixed[]
 *   Format: $[$hook] = $group|false
 */
function hook_clashnomore_info() {

  return [
    'user_load' => FALSE,
  ];
}

/**
 * @param mixed[][] $hooks_by_module
 *   List of explicit hooks per module.
 *   Format: $[$module][$hook] = $group|false
 * @param true[] $clashnomore_modules
 *   List of modules that opt out of regular magic hook discovery.
 *   Usually these are the same module names as above, unless they are altered.
 *   Format: $[$module] = true
 */
function hook_clashnomore_info_alter(array &$hooks_by_module, array &$clashnomore_modules) {

  /* @see field_group_permission() */
  $hooks_by_module['field_group']['permission'] = FALSE;
}
