<?php

namespace Drupal\Tests\tide_site\Unit;

use Drupal\Tests\UnitTestCase;

/**
 * Tests for TideSiteMenuAutocreate class.
 *
 * @coversDefaultClass Drupal\tide_site\TideSiteMenuAutocreate
 * @group tide
 */
class TideSiteMenuAutocreateTest extends UnitTestCase {

  /**
   * @covers ::toMachineName
   * @dataProvider providerToMachineName
   */
  public function testToMachineName($input, $expected) {
    $mock = self::createMock('Drupal\tide_site\TideSiteMenuAutocreate');
    $actual = $this->callProtectedMethod($mock, 'toMachineName', [$input]);
    $this->assertEquals($expected, $actual);
  }

  /**
   * Data provider to test toMachineName() method.
   */
  public function providerToMachineName() {
    return [
      ['', ''],
      ['abc', 'abc'],
      ['123', '123'],
      ['abc123', 'abc123'],
      ['abc 123', 'abc_123'],
      ['abc-123', 'abc_123'],
      ['abc_123', 'abc_123'],
      ['abc_123 def', 'abc_123_def'],
      ['[abc] 123 def', 'abc_123_def'],
      ['**[abc] 123 def  ', 'abc_123_def'],
    ];
  }

  /**
   * @covers ::makeMenuLabel
   * @dataProvider providerMakeMenuLabel
   */
  public function testMakeMenuLabel($menu_title, $parents, $expected) {
    $mock = self::prepareMock('Drupal\tide_site\TideSiteMenuAutocreate', [
      'loadTermParents' => $this->prepareMockTermParents($parents),
    ]);
    $actual = $this->callProtectedMethod($mock, 'makeMenuLabel', [$menu_title, NULL]);
    $this->assertEquals($expected, $actual);
  }

  /**
   * Data provider to test makeMenuLabel() method.
   */
  public function providerMakeMenuLabel() {
    return [
      ['', [], ''],
      ['abc', [], 'abc'],
      ['abc', ['t1'], 'abc - t1'],
      ['abc', ['t1', 'p1'], 'abc - p1 - t1'],
      ['abc', ['t1', 'p1', 'p2'], 'abc - p2 - p1 - t1'],
    ];
  }

  /**
   * @covers ::makeMenuName
   * @dataProvider providerMakeMenuName
   */
  public function testMakeMenuName($menu_title, $parents, $expected) {
    $mock = self::prepareMock('Drupal\tide_site\TideSiteMenuAutocreate', [
      'loadTermParents' => $this->prepareMockTermParents($parents),
    ]);
    $actual = $this->callProtectedMethod($mock, 'makeMenuName', [$menu_title, NULL]);
    $this->assertEquals($expected, $actual);
  }

  /**
   * Data provider to test makeMenuName() method.
   */
  public function providerMakeMenuName() {
    return [
      ['abc', [], 'abc_site'],
      ['abc', ['t1'], 'abc_site_t1'],
      ['abc', ['t1', 'p1'], 'abc_site_p1_t1'],
      ['abc', ['t1', 'p1', 'p2'], 'abc_site_p2_p1_t1'],
    ];
  }

  /**
   * Helper to prepare term and specified parents.
   *
   * @param array $parents
   *   Array of term and it's parents names, ordered from term to the "oldest"
   *   parent.
   *
   * @return array
   *   Array of mocked term objects.
   */
  protected function prepareMockTermParents(array $parents) {
    $list = [];

    foreach ($parents as $name) {
      $mock = self::createMock('\Drupal\taxonomy\Entity\Term');
      $mock->method('getName')->willReturn($name);
      $list[] = $mock;
    }

    return $list;
  }

  /**
   * Call protected methods on the class.
   *
   * @param object|string $object
   *   Object or class name to use for a method call.
   * @param string $method
   *   Method name. Method can be static.
   * @param array $args
   *   Array of arguments to pass to the method. To pass arguments by
   *   reference, pass them by reference as an element of this array.
   *
   * @return mixed
   *   Method result.
   */
  protected static function callProtectedMethod($object, $method, array $args = []) {
    $class = new \ReflectionClass(is_object($object) ? get_class($object) : $object);
    $method = $class->getMethod($method);
    $method->setAccessible(TRUE);
    $object = $method->isStatic() ? NULL : $object;

    return $method->invokeArgs($object, $args);
  }

  /**
   * Helper to prepare class or trait mock.
   *
   * @param string $class
   *   Class or trait name to generate the mock.
   * @param array $methodsMap
   *   Optional array of methods and values, keyed by method name. Array
   *   elements can be return values, callbacks created with
   *   $this->returnCallback(), or closures.
   * @param array $args
   *   Optional array of constructor arguments. If omitted, a constructor
   *   will not be called.
   *
   * @return object
   *   Mocked class.
   */
  protected function prepareMock($class, array $methodsMap = [], array $args = []) {
    $methods = array_keys($methodsMap);

    $reflectionClass = new \ReflectionClass($class);

    if ($reflectionClass->isAbstract()) {
      $mock = $this->getMockForAbstractClass($class, $args, '', !empty($args), TRUE, TRUE, $methods);
    }
    elseif ($reflectionClass->isTrait()) {
      $mock = $this->getMockForTrait($class, [], '', TRUE, TRUE, TRUE, array_keys($methodsMap));
    }
    else {
      $mockBuilder = $this->getMockBuilder($class);
      if (!empty($args)) {
        $mockBuilder = $mockBuilder->enableOriginalConstructor()
          ->setConstructorArgs($args);
      }
      else {
        $mockBuilder = $mockBuilder->disableOriginalConstructor();
      }
      $mock = $mockBuilder->setMethods($methods)
        ->getMock();
    }

    foreach ($methodsMap as $method => $value) {
      // Handle callback values differently.
      if (is_object($value) && strpos(get_class($value), 'Callback') !== FALSE) {
        $mock->expects($this->any())
          ->method($method)
          ->will($value);
      }
      elseif (is_object($value) && strpos(get_class($value), 'Closure') !== FALSE) {
        $mock->expects($this->any())
          ->method($method)
          ->will($this->returnCallback($value));
      }
      else {
        $mock->expects($this->any())
          ->method($method)
          ->willReturn($value);
      }
    }

    return $mock;
  }

}
