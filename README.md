# Clash-no-more

Prevents hook name collisions, by allowing modules to specify a whitelist.

## Use cases

1. Module 'elephant_lifter' implements hook_zoo_load() and hook_zoo_close(), and wants that
  - Functions by other modules that start with 'elephant_lifter_' are not interpreted as hook implementations by this module.
  - The functions elephant_lifter_zoo_open() and elephant_lifter_zoo_close() are not interpreted as hook implementations by other modules, e.g. as elephant + hook_lifter_zoo_close() or elephant_lifter_zoo + close().

2. You learn about the problem of `field_group_permission()` ambiguity, and want a custom module to tell Drupal that
  - field_group implements hook_permission()
  - field does NOT implement hook_group_permission().

3. (possibly questionable)  
  Module 'mymodule' wants to organize hook implementations in different files, by setting the `$group`.  
  Currently the API of Clash-no-more allows this, but it is not fully tested yet.

## API

See clashnomore.api.php.

```php
function elephant_lifter_clashnomore_info() {
  return [
    'zoo_open' => false,
    'zoo_close' => false,
  ];
}
```

```php
function mymodule_clashnomore_info_alter(array &$hooks_by_module, array &$modules) {
  $hooks_by_module['field_group']['permission'] = FALSE;
}
```

## Background

Hooks in Drupal are fragile and can have name clashes.

Issues on drupal.org:
- [hook_group_permission() / hook_permission collision with field_group module](https://www.drupal.org/node/2008388)
- [Prevent potential naming collisions caused by the hook naming pattern](https://www.drupal.org/node/2016003)
- [Use something other than a single underscore for hook namespacing](https://www.drupal.org/node/548470)
- [Support alternative module hook syntax alongside the existing syntax.](https://www.drupal.org/node/2817439)
