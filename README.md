PhockitoUnit PHP-DI
===================
[![Build Status](https://travis-ci.org/balihoo/phockito-unit-php-di.png?branch=master)](https://travis-ci.org/balihoo/phockito-unit-php-di)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/balihoo/phockito-unit-php-di/badges/quality-score.png?s=51aec695b6ace7d45544743d7a7fc564020c04cc)](https://scrutinizer-ci.com/g/balihoo/phockito-unit-php-di/)
[![Code Coverage](https://scrutinizer-ci.com/g/balihoo/phockito-unit-php-di/badges/coverage.png?s=7f9c0e9601492c12876be8d0b236ed6823bc5236)](https://scrutinizer-ci.com/g/balihoo/phockito-unit-php-di/)
[![Latest Stable Version](https://poser.pugx.org/balihoo/phockito-unit-php-di/v/stable.png)](https://packagist.org/packages/balihoo/phockito-unit)
[![Total Downloads](https://poser.pugx.org/balihoo/phockito-unit-php-di/downloads.png)](https://packagist.org/packages/balihoo/phockito-unit)
[![Latest Unstable Version](https://poser.pugx.org/balihoo/phockito-unit-php-di/v/unstable.png)](https://packagist.org/packages/balihoo/phockito-unit)


PhockitoUnit PHP-DI exists to marry [PHP Unit](https://github.com/sebastianbergmann/phpunit/) with the [Phockito](https://github.com/hafriedlander/phockito) mocking framework and the [PHP-DI](https://github.com/mnapoli/PHP-DI) dependency injection framework in an everlasting love praised by PHP developers everywhere.  It is a PHP-DI specific enhancement to the [PhockitoUnit](https://github.com/balihoo/phockito-unit) libary.  It's features are rather simple:
* Automatically generate mocks that your tests require
* Automatically generate spys that your tests require
* Automatically turn on hamcrest matching
* Automatically register your mocks in the DI Container

That's it!

PhockitoUnit PHP-DI in Action
============
Here is a classic PHP Unit test that uses Phockito to mock a dependency
```
class SomeTest extends PHPUnit_Framework_TestCase
{
  public function setUp(){
    Phockito::include_hamcrest();
  }
  
  testSomeMethod(){
    /** @var SomeDependency $mockDependency **/
    $mockDependency = Phockito::mock('SomeDependency');
    Phockito::when($mockDependency->dependentMethod(anything()))->return("value");
    
    $instance = new ThingThatNeedsDependency($mockDependency);
    
    $this->assertEquals("value", $instance->methodThatUsesDependency());
  }
  
  testSomeMethodWhenSomeDependencyThrows(){
    /** @var SomeDependency $mockDependency **/
    $mockDependency = Phockito::mock('SomeDependency');
    Phockito::when($mockDependency->dependentMethod(anything()))->throw(new Exception("Some error"));
    
    $instance = new ThingThatNeedsDependency($mockDependency);
    try{
      $instance->methodThatUsesDependency());
      $this->fail("Expected exception not thrown");
    } catch(Exception $ex) {
      $this->assertEquals("Some error", $ex->getMessage());
    }
  }
}
```
Certainly you have encoutnered or written a unit tests that is at least similar to this structure.  PhockitoUnit simplifies this structure by eliminating some common boilerplate, here it is:

```
class SomeTest extends \PhockitoUnit\PhockitoUnitTestCase
{
  
  /** @var SomeDependency **/
  protected $mockDependency;
  
  testSomeMethod(){
    
    Phockito::when($this->mockDependency->dependentMethod(anything()))->return("value");
    
    $instance = new ThingThatNeedsDependency($mockDependency);
    
    $this->assertEquals("value", $instance->methodThatUsesDependency());
  }
  
  testSomeMethodWhenSomeDependencyThrows(){

    Phockito::when($this->mockDependency->dependentMethod(anything()))->throw(new Exception("Some error"));
    
    $instance = new ThingThatNeedsDependency($mockDependency);
    try{
      $instance->methodThatUsesDependency());
      $this->fail("Expected exception not thrown");
    } catch(Exception $ex) {
      $this->assertEquals("Some error", $ex->getMessage());
    }
  }
}
```
It's not a monsterous change, but it helps quite a bit, eliminating the chance of class name typos, class rename refactorings, etc.  And in more advanced scenarios where you are mocking an domain object graph it can make it easier to write more tests.  More tests means more coverage of intent.  Here's an example that sets up a graph and uses a spy:
```
class FamilyTest extends \PhockitoUnit\PhockitoUnitTestCase
{
  
  /** @var Child **/
  protected $mockChild1;
  
  /** @var Child **/
  protected $spyChild2;
  
  /** @var Parent **/
  protected $mockParent;
  
  public function setUp(){
    parent::setUp();
    
    Phockito::when($this->mockParent->getEledestChild())->return($this->mockChild1);
    Phockito::when($this->mockParent->getYoungestChild())->return($this->spyChild1);
    
  }
  
  testGetEldestChildNickName(){
    
    Phockito::when($this->mockChild1->getNickName())->return("Oldie");
    
    $family = new Family(array($this->mockParent));
    
    $this->assertEquals("Oldie", $family->getElestChildNickName());
  }
  
  testGetYoungestchildFullName(){
    
    Phockito::when($this->spyChild2->getFirstName())->return("Youngy");
    Phockito::when($this->spyChild2->getLastName())->return("McYoung");
    
    $family = new Family(array($this->mockParent));
    
    $this->assertEquals("Youngy McYoung", $parent->testGetYoungestchildFullName());
  }
}
```


