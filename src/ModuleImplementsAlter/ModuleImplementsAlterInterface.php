<?php

namespace Drupal\clashnomore\ModuleImplementsAlter;

interface ModuleImplementsAlterInterface {

  /**
   * @param mixed[] $implementations
   *   Format: $[$module] = $group
   * @param string $hook
   */
  public function moduleImplementsAlter(array &$implementations, $hook);

}
