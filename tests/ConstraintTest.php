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
	
	public function testLiteralSubstitute() {
		$this->assertTrue(CLiteral::of(true)->substitute('MagicMirror', CItem::of('MoonPearl'))->evaluate($this->collected));
		$this->assertFalse(CLiteral::of(false)->substitute('MagicMirror', CItem::of('MoonPearl'))->evaluate($this->collected));
	}
	
	public function testLiteralSimplify() {
		$this->assertTrue(CLiteral::of(true)->simplify()->evaluate($this->collected));
		$this->assertFalse(CLiteral::of(false)->simplify()->evaluate($this->collected));
	}
	
	public function testAndEvaluation() {
		$this->assertTrue(CAnd::of(CLiteral::of(true), CLiteral::of(true))->evaluate($this->collected));
		$this->assertFalse(CAnd::of(CLiteral::of(true), CLiteral::of(false))->evaluate($this->collected));
		$this->assertFalse(CAnd::of(CLiteral::of(false), CLiteral::of(true))->evaluate($this->collected));
		$this->assertFalse(CAnd::of(CLiteral::of(false), CLiteral::of(false))->evaluate($this->collected));
	}
	
	public function testAndSimplify() {
		$this->assertTrue(CAnd::of(CLiteral::of(true), CLiteral::of(true))->simplify()->evaluate($this->collected));
		$this->assertFalse(CAnd::of(CLiteral::of(false), CLiteral::of(true))->simplify()->evaluate($this->collected));
	}
	
	public function testOrEvaluation() {
		$this->assertTrue(COr::of(CLiteral::of(true), CLiteral::of(true))->evaluate($this->collected));
		$this->assertTrue(COr::of(CLiteral::of(true), CLiteral::of(false))->evaluate($this->collected));
		$this->assertTrue(COr::of(CLiteral::of(false), CLiteral::of(true))->evaluate($this->collected));
		$this->assertFalse(COr::of(CLiteral::of(false), CLiteral::of(false))->evaluate($this->collected));
	}
	
	public function testOrSimplify() {
		$this->assertTrue(COr::of(CLiteral::of(true), CLiteral::of(false))->simplify()->evaluate($this->collected));
		$this->assertFalse(COr::of(CLiteral::of(false), CLiteral::of(false))->simplify()->evaluate($this->collected));
	}
	
	public function testItemEvaluation() {
		$c = CItem::of('MagicMirror');
		$this->assertFalse($c->evaluate($this->collected));
		
		$this->addCollected(['MagicMirror']);
		$this->assertTrue($c->evaluate($this->collected));
	}
	
	public function testItemSimplify() {
		$c = CItem::of('MagicMirror');
		$s = $c->simplify();
		$this->assertTrue($c == $s);
	}
	
	public function testProgressiveGloveEvaluation() {
		$g1 = CItem::of('PowerGlove');
		$this->assertFalse($g1->evaluate($this->collected));
		
		$this->addCollected(['ProgressiveGlove']);
		$this->assertTrue($g1->evaluate($this->collected));
		
		$g2 = CItem::of('TitansMitt');
		$this->assertFalse($g2->evaluate($this->collected));
		
		$this->addCollected(['ProgressiveGlove']);
		$this->assertTrue($g2->evaluate($this->collected));
	}
	
	public function testProgressiveSwordEvaluation() {
		$l1 = CItem::of('L1Sword');
		$this->assertFalse($l1->evaluate($this->collected));
		
		$this->addCollected(['ProgressiveSword']);
		$this->assertTrue($l1->evaluate($this->collected));
		
		$l2 = CItem::of('L2Sword');
		$this->assertFalse($l2->evaluate($this->collected));
		
		$this->addCollected(['ProgressiveSword']);
		$this->assertTrue($l2->evaluate($this->collected));
		
		$l3 = CItem::of('L3Sword');
		$this->assertFalse($l3->evaluate($this->collected));
		
		$this->addCollected(['ProgressiveSword']);
		$this->assertTrue($l3->evaluate($this->collected));
		
		$l4 = CItem::of('L4Sword');
		$this->assertFalse($l4->evaluate($this->collected));
		
		$this->addCollected(['ProgressiveSword']);
		$this->assertTrue($l4->evaluate($this->collected));
	}
	
	public function testItemSubstitute() {
		$c = CItem::of('MagicMirror');
		$this->assertFalse($c->evaluate($this->collected));
		
		$new = $c->substitute('MagicMirror', CItem::of('Shovel'));
		$this->assertFalse($c->evaluate($this->collected));
				
		$this->addCollected(['MagicMirror']);
		$this->assertFalse($new->evaluate($this->collected));
		
		$this->addCollected(['Shovel']);
		$this->assertTrue($new->evaluate($this->collected));
	}
	
	public function testNestedSubstitute() {
		// this constraint requires Mirror and Moon pearl
		$c = CAnd::of(CItem::of('MoonPearl'), CItem::of('MagicMirror'));
		$this->assertFalse($c->evaluate($this->collected));
		
		// indicates that the Mirror requires the Shovel and Mushroom
		$new = $c->substitute('MagicMirror', CAnd::of(CItem::of('Shovel'), CItem::of('Mushroom')));
		$this->assertFalse($c->evaluate($this->collected));
		
		// the Mirror requirement is ANDed with Shovel and Mushroom. the Constraint is satisfied after adding those items.
		
		$this->addCollected(['MagicMirror']);
		$this->assertFalse($new->evaluate($this->collected));
		
		$this->addCollected(['MoonPearl']);
		$this->assertFalse($new->evaluate($this->collected));
		
		$this->addCollected(['Shovel']);
		$this->assertFalse($new->evaluate($this->collected));
		
		$this->addCollected(['Mushroom']);
		$this->assertTrue($new->evaluate($this->collected));
	}
	
	public function testNormalizeAnd() {
		$c = CAnd::of(CItem::of('MoonPearl'), CItem::of('MagicMirror'))->normalize();
		$this->assertFalse($c->evaluate($this->collected));
				
		$this->addCollected(['MagicMirror']);
		$this->assertFalse($c->evaluate($this->collected));
		
		$this->addCollected(['MoonPearl']);
		$this->assertTrue($c->evaluate($this->collected));
	}
	
	public function testNormalizeAndNestedLeft() {
		$c = CAnd::of(COr::of(CItem::of('Shovel'), CItem::of('Mushroom')), CItem::of('MoonPearl'))->normalize();
		$normalized = COr::of(CAnd::of(CItem::of('Shovel'), CItem::of('MoonPearl')), CAnd::of(CItem::of('Mushroom'), CItem::of('MoonPearl')));

		$this->assertTrue($c == $normalized);
		
		$renormalized = $normalized->normalize();
		$this->assertTrue($c == $renormalized);
	}
	
	public function testNormalizeAndNestedRight() {
		$c = CAnd::of(CItem::of('MoonPearl'), COr::of(CItem::of('Shovel'), CItem::of('Mushroom')))->normalize();
		$this->assertFalse($c->evaluate($this->collected));
						
		$this->addCollected(['MoonPearl']);
		$this->assertFalse($c->evaluate($this->collected));
		
		$this->addCollected(['Shovel']);
		$this->assertTrue($c->evaluate($this->collected));
	}
	
	public function testNormalizeAndNestedBoth() {
		$c = CAnd::of(COr::of(CItem::of('MoonPearl'), CItem::of('MagicMirror')), COr::of(CItem::of('Shovel'), CItem::of('Mushroom')))->normalize();
		$normalized = COr::of(
							COr::of(CAnd::of(CItem::of('MoonPearl'), CItem::of('Shovel')), CAnd::of(CItem::of('MoonPearl'), CItem::of('Mushroom'))),
							COr::of(CAnd::of(CItem::of('MagicMirror'), CItem::of('Shovel')),CAnd::of(CItem::of('MagicMirror'), CItem::of('Mushroom'))));
		$this->assertTrue($c == $normalized);
						
		$this->addCollected(['MoonPearl']);
		$this->assertFalse($c->evaluate($this->collected));
		
		$this->addCollected(['Shovel']);
		$this->assertTrue($c->evaluate($this->collected));
	}
	
	public function testMinimumNestedAndOr() {
		$c = COr::of(COr::of(CAnd::of(CItem::of('MoonPearl'), CItem::of('Shovel')), CAnd::of(CItem::of('MoonPearl'), CItem::of('Mushroom'))),
					 COr::of(CAnd::of(CItem::of('MagicMirror'), CItem::of('Shovel')),CAnd::of(CItem::of('MagicMirror'), CItem::of('Mushroom'))));
		$this->assertTrue($c->minRequired() == 2);
	}
}
