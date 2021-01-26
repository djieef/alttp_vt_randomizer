<?php namespace ALttP\Constraint;

use ALttP\Constraint;
use ALttP\Item;

/**
 * Class representing a boolean variable for possessing an item
 */
class CItem implements Constraint {
	protected $item;
	
	public function __construct($i) {
		if(substr( $i->getRawName(), 0, 11 ) === "Progressive") {
			throw new \Exception('Item constraints cannot contain progressive items');
		}
		$this->item = $i;
	}

	public function evaluate($items) {
		$o = false;
		switch($this->item->getRawName()) {
			case 'PowerGlove':
				$o = $items->has('ProgressiveGlove', 1);
				break;
			case 'TitansMitt':
				$o = $items->has('ProgressiveGlove', 2);
				break;
			case 'L1Sword':
			case 'L1SwordAndShield':
				$o = $items->has('ProgressiveSword', 1);
				break;
			case 'L2Sword':
				$o = $items->has('ProgressiveSword', 2);
				break;
			case 'L3Sword':
				$o = $items->has('ProgressiveSword', 3);
				break;
			case 'L4Sword':
				$o = $items->has('ProgressiveSword', 4);
				break;
		}
		return $o || $items->has($this->item->getRawName());
	}
	
	public function substitute($placed_item, $new_constraint) {
		if($placed_item == $this->item->getRawName()) {
			return $new_constraint->normalize();
		} else {
			return $this;
		}
	}
	
	public function normalize() {
		return $this;
	}
	
	public function minRequired() {
		return 1;
	}
	
	public function simplify() {
		return $this;
	}
	
	public static function of($l, $world) {
		return new CItem(Item::get($l, $world));
	}
}
