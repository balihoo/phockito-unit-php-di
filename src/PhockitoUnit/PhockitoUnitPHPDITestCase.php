<?php

namespace PhockitoUnit;

use DI\ContainerBuilder;
use DI\Container;

use Phockito;

use PhpDocReader\PhpDocReader;
use ReflectionClass;

use Doctrine\Common\Cache\Cache;

class PhockitoUnitPHPDITestCase extends PhockitoUnitTestCase
{

	/**
	 * @var Container
	 */
	protected $DIContainer;



	public function setUp($useReflection = true, $useAnnotations = true, Cache $cache = null)
    {
        parent::setUp();

	    $builder = new ContainerBuilder();
	    $builder
		    ->useReflection($useReflection)
		    ->useAnnotations($useAnnotations)
	    ;
	    if(!is_null($cache)){
		    $builder->setDefinitionCache($cache);
	    }

	    $this->DIContainer = $builder->build();

	    $this->registerMocks();

    }

	protected function registerMocks(){
		$parser = new PhpDocReader();

		$class = new ReflectionClass($this);

		$registeredNames = array();

		//Find every member that begins with "mock" or "spy"
		foreach ($class->getProperties() as $property) {

			if (strpos($property->name, 'mock') === 0 || strpos($property->name, 'spy') === 0) {
				if($property->name == "mockObjects"){
					//This is inherited from PHPUnit_Framework_TestCase and we can't mock it
					continue;
				}

				//Use the type as the DI name
				$name = $parser->getPropertyType($property);

				//TODO: Check for duplicates
				//$this->DIContainer->getDefinitionManager()->getDefinition()

				$property->setAccessible(true);
				$this->DIContainer->set($name,  $property->getValue( $this ));
			}
		}
	}


}
