<?php

use ALttP\Item;
use ALttP\World;
use ALttP\Constraint;

class ConstraintTest extends TestCase {
	public function setUp() {
		parent::setUp();

		$this->region = new Region(new World('test_rules', 'NoMajorGlitches'));
	}

	public function tearDown() {
		parent::tearDown();
		unset($this->region);
	}
	
	public function testSimpleEvaluation() {
		$this->assertTrue(new CLiteral(true));
		$this->assertFalse(new CLiteral(false));
	} 
}
