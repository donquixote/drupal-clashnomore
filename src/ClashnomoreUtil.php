<?php

namespace Drupal\clashnomore;

class ClashnomoreUtil {

  /**
   * Builds a blacklist of magic naming methods to remove.
   *
   * @param mixed[][] $hooksByModule
   *   Format: $[$module][$hook] = $group|false
   * @param true[] $modulesMap
   *   Format: $[$module] = true
   *
   * @return string[][]
   *   List of magic naming matches to EXCLUDE from list of implementations.
   *   Format: $[$hook][] = $module
   */
  public static function buildBlacklist(array $hooksByModule, array $modulesMap) {

    // All hooks that are mentioned anywhere in clashnomore info.
    $hooks_all_map = [];
    foreach ($hooksByModule as $module => $hooks) {
      $hooks_all_map += $hooks;
    }
    $hooks_all = array_keys($hooks_all_map);

    // Blacklist all combinations, then later remove items from the blacklist.
    $blacklist = array_fill_keys($hooks_all, $modulesMap);

    foreach ($hooksByModule as $module => $hooks) {

      // Remove unintended magic naming matches due to underscores in the module name.
      if (FALSE !== $pos = strpos($module, '_')) {
        do {
          $module_prefix = substr($module, 0, $pos);
          $module_suffix = substr($module, $pos + 1);
          foreach ($hooks as $hook => $group) {
            $blacklist[$module_suffix . '_' . $hook][$module_prefix] = TRUE;
          }
        }
        while (FALSE !== $pos = strpos($module, '_', $pos + 1));
      }

      // Remove unintended magic naming matches due to underscores in the hook name.
      foreach ($hooks as $hook => $group) {

        // Unlist registered implementations.
        unset($blacklist[$hook][$module]);

        if (FALSE !== $pos = strpos($hook, '_')) {
          do {
            $hook_prefix = substr($hook, 0, $pos);
            $hook_suffix = substr($hook, $pos + 1);
            $blacklist[$hook_suffix][$module . '_' . $hook_prefix] = TRUE;
          }
          while (FALSE !== $pos = strpos($hook, '_', $pos + 1));
        }
      }
    }

    return $blacklist;
  }

  /**
   * @param mixed[][] $hooksByModule
   *   Format: $[$module][$hook] = $group|false
   *
   * @return mixed[][]
   *   Format: $[$hook][$module] = $group|false
   */
  public static function buildInfoByHook(array $hooksByModule) {

    $info_by_hook = [];
    foreach ($hooksByModule as $module => $hooks) {
      foreach ($hooks as $hook => $group) {
        $info_by_hook[$hook][$module] = $group;
      }
    }

    return $info_by_hook;
  }

  /**
   * @return mixed[][]
   *   Format: $[$module][$hook] = $group|false
   */
  public static function collectHooksByModule() {

    $info_by_module = [];
    foreach (module_implements('clashnomore_info') as $module) {
      $function = $module . '_clashnomore_info';
      $module_info = $function();
      if (is_array($module_info)) {
        $info_by_module[$module] = $module_info;
      }
    }

    return $info_by_module;
  }

}
