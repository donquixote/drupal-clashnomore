<?php

namespace Drupal\clashnomore\Tests;

use Drupal\clashnomore\ClashnomoreUtil;
use Drupal\clashnomore\ModuleImplementsAlter\ModuleImplementsAlter_Clashnomore;
use Drupal\clashnomore\ModuleImplementsAlter\ModuleImplementsAlterInterface;

class ClashnomoreUnitTest extends \DrupalUnitTestCase  {

  public static function getInfo() {
    return array(
      'name' => 'Clash-no-more unit test case',
      'description' => 'Test all of clashnomore.',
      'group' => 'Clash-no-more',
    );
  }

  public function testBuildBlacklist() {

    $this->assertIdenticalArrays(
      [
        'ccc_ddd' => [],
        'xxx_yyy' => [],
        'bbb_ccc_ddd' => [
          'aaa' => TRUE,
        ],
        'bbb_xxx_yyy' => [
          'aaa' => TRUE,
        ],
        'ddd' => [
          'aaa_bbb_ccc' => TRUE,
        ],
        'yyy' => [
          'aaa_bbb_xxx' => TRUE,
        ],
      ],
      ClashnomoreUtil::buildBlacklist(
        [
          'aaa_bbb' => [
            'ccc_ddd' => FALSE,
            'xxx_yyy' => 'xxx',
          ],
        ],
        [
          'aaa_bbb' => TRUE,
        ]));

    $this->assertIdentical(
      [
        'user_load' => [],
        'load' => [
          'views_user' => TRUE,
        ],
      ],
      ClashnomoreUtil::buildBlacklist(
        [
          'views' => [
            'user_load' => FALSE,
          ],
        ],
        [
          'views' => TRUE,
        ]));

    $this->assertIdentical(
      [
        'load' => [
          'field_group' => TRUE,
        ],
        'permission' => [
          'views_user' => TRUE,
        ],
        'user_load' => [
          'views' => TRUE,
        ],
        'group_permission' => [
          'field' => TRUE,
        ],
      ],
      ClashnomoreUtil::buildBlacklist(
        [
          'views_user' => [
            'load' => FALSE,
          ],
          'field_group' => [
            'permission' => 'perm',
          ],
        ],
        [
          'views_user' => TRUE,
          'field_group' => TRUE,
        ]));
  }

  public function testBuildInfoByHook() {

    $this->assertIdentical(
      [
        'load' => [
          'views_user' => FALSE,
        ],
        'permission' => [
          'field_group' => 'perm',
        ],
      ],
      ClashnomoreUtil::buildInfoByHook(
        [
          'views_user' => [
            'load' => FALSE,
          ],
          'field_group' => [
            'permission' => 'perm',
          ],
        ]));
  }

  public function testModuleImplementsAlter() {

    $mia = ModuleImplementsAlter_Clashnomore::createFromInfoByModule(
      [
        'views' => [
          'user_load' => FALSE,
        ],
        'field_group' => [
          'permission' => 'perm',
        ],
      ],
      [
        'views' => TRUE,
        'field_group' => TRUE,
      ]);

    $this->assertModuleImplementsAlter(
      [
        'node' => FALSE,
      ],
      [
        'node' => FALSE,
        'views_user' => FALSE,  // To be removed.
      ],
      'load',
      $mia);

    $this->assertModuleImplementsAlter(
      [
        'node' => FALSE,
        'views' => FALSE,
      ],
      [
        'node' => FALSE,
        'views' => FALSE,
        'field_group' => FALSE,  // To be removed.
      ],
      'user_load',
      $mia);
  }

  /**
   * @param array $implementationsAlteredExpected
   *   Format: $[$module] = $group|false
   * @param mixed[] $implementations
   *   Format: $[$module] = $group|false
   * @param string $hook
   * @param \Drupal\clashnomore\ModuleImplementsAlter\ModuleImplementsAlterInterface $mia
   */
  private function assertModuleImplementsAlter(
    array $implementationsAlteredExpected,
    array $implementations,
    $hook,
    ModuleImplementsAlterInterface $mia
  ) {
    $mia->moduleImplementsAlter($implementations, $hook);
    $this->assertIdentical($implementationsAlteredExpected, $implementations);
  }

  /**
   * @param array $expected
   * @param array|mixed $actual
   */
  private function assertIdenticalArrays(array $expected, $actual) {

    $this->assertIdentical(
      json_encode($expected, JSON_PRETTY_PRINT),
      json_encode($actual, JSON_PRETTY_PRINT));

    $this->assertIdentical($expected, $actual);
  }

}
