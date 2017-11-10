<?php namespace ALttP\Constraint;

use ALttP\Constraint;

/**
 * Class representing a boolean variable for possessing an item
 */
class CItem implements Constraint {
	protected $item;
	protected $count;
	
	public function __construct($i, $c = 1) {
		$this->item = $i;
		$this->count = $c;
	}

	public function evaluate($items) {
		return $items->has($this->item->getName(), $this->count);
	}
	
	public function update($placed_item, $new_constraint) {
		if(is_a($placed_item, get_class($this->item))) {
			return $new_constraint;
		} else {
			return $this;
		}
	}
	
	public static function of($l) {
		return new CItem($l);
	}
}
