<?php

namespace UnitTests\PhockitoUnit;

use Phockito;
use PhockitoUnit\PhockitoUnitPHPDITestCase;

class MockableFacet1
{
	public function mockableMethod(){
		return 5;
	}

}

class MockableFacet2
{
	public function mockableMethod(){
		return 5;
	}

}

class MockableFacet3
{
	public function mockableMethod(){
		return 5;
	}

}

class SpyableFacet1
{
	public function spyableMethod1(){
		return 5;
	}

	public function spyableMethod2(){
		return 5;
	}
}

class SpyableFacet2
{
	public function spyableMethod1(){
		return 5;
	}

	public function spyableMethod2(){
		return 5;
	}
}

class SpyableFacet3
{
	public function spyableMethod1(){
		return 5;
	}

	public function spyableMethod2(){
		return 5;
	}
}

//We derive from the test case to test it so we can easily access members with protection
//it seems like this really messes with code coverage
class PhockitoUnitPHPDITestCaseTest extends PhockitoUnitPHPDITestCase
{
	/** @var  MockableFacet1 */
	private $mockPrivate;

	/** @var  MockableFacet2 */
	protected $mockProtected;

	/** @var  MockableFacet3 */
	public $mockPublic;

	/** @var  SpyableFacet1 */
	private $spyPrivate;

	/** @var  SpyableFacet2 */
	protected $spyProtected;

	/** @var  SpyableFacet3 */
	public $spyPublic;

	/** @var  MockableFacet1 */
	public $someOtherThing;


	public function setUp()
	{
		//do nothing, we'll call the parent's setup explicitly as needed
	}

    public function testMockGenerationAndRegistry()
    {

	    //everything should start as null
	    $this->assertNull($this->mockPrivate);
	    $this->assertNull($this->mockProtected);
	    $this->assertNull($this->mockPublic);
        $this->assertNull($this->someOtherThing);

	    parent::setUp();

	    //Now everything prefixed with mock should be set to an instance
        $this->assertNotNull($this->mockPrivate);
        $this->assertNotNull($this->mockProtected);
        $this->assertNotNull($this->mockPublic);
	    $this->assertNull($this->someOtherThing, "non matching prefix remains");

	    //now try to do some mocking with it
        Phockito::when($this->mockPrivate->mockableMethod())->return("Private");
        Phockito::when($this->mockProtected->mockableMethod())->return("Protected");
        Phockito::when($this->mockPublic->mockableMethod())->return("Public");

        $this->assertEquals("Private", $this->mockPrivate->mockableMethod() );
        $this->assertEquals("Protected", $this->mockProtected->mockableMethod() );
        $this->assertEquals("Public", $this->mockPublic->mockableMethod() );

	    //Finally ensure they were appropriately registered in the container
	    $this->assertSame($this->mockPrivate, $this->DIContainer->get('UnitTests\PhockitoUnit\MockableFacet1'));
	    $this->assertSame($this->mockProtected, $this->DIContainer->get('UnitTests\PhockitoUnit\MockableFacet2'));
	    $this->assertSame($this->mockPublic, $this->DIContainer->get('UnitTests\PhockitoUnit\MockableFacet3'));

    }


    public function testSpyGenerationAndRegistry()
    {
	    //everything should start as null
	    $this->assertNull($this->spyPrivate);
	    $this->assertNull($this->spyProtected);
	    $this->assertNull($this->spyPublic);
	    $this->assertNull($this->someOtherThing);

	    parent::setUp();

	    //Now everything prefixed with spy should be set to an instance
	    $this->assertNotNull($this->spyPrivate);
	    $this->assertNotNull($this->spyProtected);
	    $this->assertNotNull($this->spyPublic);

	    $this->assertNull($this->someOtherThing, "things not prefixed should remain null");

	    //now try to do some mocking with it
        Phockito::when($this->spyPrivate->spyableMethod1())->return("Private");
        Phockito::when($this->spyProtected->spyableMethod1())->return("Protected");
        Phockito::when($this->spyPublic->spyableMethod1())->return("Public");

        $this->assertEquals("Private", $this->spyPrivate->spyableMethod1() );
        $this->assertEquals("Protected", $this->spyProtected->spyableMethod1() );
        $this->assertEquals("Public", $this->spyPublic->spyableMethod1() );

	    //now do some spying with it
        $this->assertEquals(5, $this->spyPrivate->spyableMethod2() );
        $this->assertEquals(5, $this->spyProtected->spyableMethod2() );
        $this->assertEquals(5, $this->spyPublic->spyableMethod2() );

	    //Finally ensure they were appropriately registered in the container
	    $this->assertSame($this->spyPrivate, $this->DIContainer->get('UnitTests\PhockitoUnit\SpyableFacet1'));
	    $this->assertSame($this->spyProtected, $this->DIContainer->get('UnitTests\PhockitoUnit\SpyableFacet2'));
	    $this->assertSame($this->spyPublic, $this->DIContainer->get('UnitTests\PhockitoUnit\SpyableFacet3'));
    }

    public function testHamcrestEnabled(){
        $this->assertTrue(anything() instanceof \Hamcrest_Matcher);
    }



}