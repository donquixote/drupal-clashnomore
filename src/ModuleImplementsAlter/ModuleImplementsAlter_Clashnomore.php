<?php

namespace Drupal\clashnomore\ModuleImplementsAlter;

use Drupal\clashnomore\ClashnomoreUtil;

class ModuleImplementsAlter_Clashnomore implements ModuleImplementsAlterInterface {

  /**
   * Modules participating in clashnomore.
   *
   * @var true[]
   *   Format: $[$module] = TRUE
   */
  private $modulesMap;

  /**
   * Entries to set the $group.
   *
   * @var mixed[][]
   *   Format: $[$hook][$module] = $group|false
   */
  private $infoByHook;

  /**
   * Entries to remove.
   *
   * @var true[][]
   *   Format: $[hook][$module] = true
   */
  private $entriesToRemove;

  /**
   * @return self
   */
  public static function create() {
    $hooksByModule = ClashnomoreUtil::collectHooksByModule();
    $modulesMap = array_fill_keys(array_keys($hooksByModule), TRUE);
    drupal_alter('clashnomore_info', $hooksByModule, $modulesMap);
    return self::createFromInfoByModule($hooksByModule, $modulesMap);
  }


  /**
   * Builds a blacklist of magic naming methods to remove.
   *
   * @param mixed[][] $hooksByModule
   *   Format: $[$module][$hook] = $group|false
   * @param true[] $modulesMap
   *   Format: $[$module] = true
   *
   * @return self
   */
  public static function createFromInfoByModule(array $hooksByModule, array $modulesMap) {
    return new self(
      $modulesMap,
      ClashnomoreUtil::buildInfoByHook($hooksByModule),
      ClashnomoreUtil::buildBlacklist($hooksByModule, $modulesMap));
  }

  /**
   * @param array $modulesMap
   * @param array $infoByHook
   * @param array $entriesToRemove
   */
  public function __construct(array $modulesMap, array $infoByHook, array $entriesToRemove) {
    $this->modulesMap = $modulesMap;
    $this->infoByHook = $infoByHook;
    $this->entriesToRemove = $entriesToRemove;
  }

  /**
   * @param mixed[] $implementations
   *   Format: $[$module] = $group
   * @param string $hook
   */
  public function moduleImplementsAlter(array &$implementations, $hook) {

    if (array_key_exists($hook, $this->infoByHook)) {
      // The hook is known, all entries to remove already in the blacklist.
      // Update the $group for registered implementations.
      foreach (array_intersect_key($this->infoByHook[$hook], $implementations) as $module => $group) {
        $implementations[$module] = $group;
      }
    }
    else {
      // Remove all implementations by modules that participate in clashnomore.
      $implementations = array_diff_key($implementations, $this->modulesMap);
    }

    if (array_key_exists($hook, $this->entriesToRemove)) {
      foreach ($this->entriesToRemove[$hook] as $module => $_true) {
        unset($implementations[$module]);
      }
    }
  }
}
