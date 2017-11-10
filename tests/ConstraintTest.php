<?php

use ALttP\Item;
use ALttP\World;
use ALttP\Region;
use ALttP\Constraint\{CLiteral, CAnd, COr, CItem};

class ConstraintTest extends TestCase {
	public function setUp() {
		parent::setUp();

		$this->region = new Region(new World('test_rules', 'NoMajorGlitches'));
	}

	public function tearDown() {
		parent::tearDown();
		unset($this->region);
	}
	
	public function testLiteralEvaluation() {
		$this->assertTrue(CLiteral::of(true)->evaluate($this->collected));
		$this->assertFalse(CLiteral::of(false)->evaluate($this->collected));
	}
	
	public function testLiteralUpdate() {
		$this->assertTrue(CLiteral::of(true)->update(Item::get('MagicMirror'), CItem::of(Item::get('MoonPearl')))->evaluate($this->collected));
		$this->assertFalse(CLiteral::of(false)->update(Item::get('MagicMirror'), CItem::of(Item::get('MoonPearl')))->evaluate($this->collected));
	}
	
	public function testAndEvaluation() {
		$this->assertTrue(CAnd::of(CLiteral::of(true), CLiteral::of(true))->evaluate($this->collected));
		$this->assertFalse(CAnd::of(CLiteral::of(true), CLiteral::of(false))->evaluate($this->collected));
		$this->assertFalse(CAnd::of(CLiteral::of(false), CLiteral::of(true))->evaluate($this->collected));
		$this->assertFalse(CAnd::of(CLiteral::of(false), CLiteral::of(false))->evaluate($this->collected));
	}
	
	public function testOrEvaluation() {
		$this->assertTrue(COr::of(CLiteral::of(true), CLiteral::of(true))->evaluate($this->collected));
		$this->assertTrue(COr::of(CLiteral::of(true), CLiteral::of(false))->evaluate($this->collected));
		$this->assertTrue(COr::of(CLiteral::of(false), CLiteral::of(true))->evaluate($this->collected));
		$this->assertFalse(COr::of(CLiteral::of(false), CLiteral::of(false))->evaluate($this->collected));
	}
	
	public function testItemEvaluation() {
		$c = CItem::of(Item::get('MagicMirror'));
		$this->assertFalse($c->evaluate($this->collected));
		
		$this->addCollected(['MagicMirror']);
		$this->assertTrue($c->evaluate($this->collected));
	}
	
	public function testItemUpdate() {
		$c = CItem::of(Item::get('MagicMirror'));
		$this->assertFalse($c->evaluate($this->collected));
		
		$new = $c->update(Item::get('MagicMirror'), CItem::of(Item::get('Shovel')));
		$this->assertFalse($c->evaluate($this->collected));
		
		$this->addCollected(['Shovel']);
		$this->assertTrue($new->evaluate($this->collected));
	}
	
	public function testNestedUpdate() {
		// this constraint requires Mirror and Moon pearl
		$c = CAnd::of(CItem::of(Item::get('MoonPearl')), CItem::of(Item::get('MagicMirror')));
		$this->assertFalse($c->evaluate($this->collected));
		
		// indicates that the Mirror requires the Shovel and Mushroom
		$new = $c->update(Item::get('MagicMirror'), CAnd::of(CItem::of(Item::get('Shovel')), CItem::of(Item::get('Mushroom'))));
		$this->assertFalse($c->evaluate($this->collected));
		
		// the Mirror is replaced with Shovel and Mushroom. the Constraint is satisfied after adding those items.
		// note that the Mirror is no longer part of this constraint as it has been "solved" (placed)
		
		$this->addCollected(['MoonPearl']);
		$this->assertFalse($new->evaluate($this->collected));
		
		$this->addCollected(['Shovel']);
		$this->assertFalse($new->evaluate($this->collected));
		
		$this->addCollected(['Mushroom']);
		$this->assertTrue($new->evaluate($this->collected));
	}
}
