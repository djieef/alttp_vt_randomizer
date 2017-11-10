<?php namespace ALttP\Constraint;

use ALttP\Constraint;

/**
 * Class representing a literal boolean value
 */
class CLiteral implements Constraint {
	protected $lit;
	
	public function __construct($l) {
		$this->lit = $l;
	}

	public function evaluate($items) {
		return $this->lit;
	}
	
	public function substitute($placed_item, $new_constraint) {
		return $this;
	}
	
	public function normalize() {
		return $this;
	}
	
	public function minRequired() {
		return 0;
	}
	
	public function getLit() {
		return $this->lit;
	}
	
	public function simplify() {
		return $this;
	}
	
	public static function of($l) {
		return new CLiteral($l);
	}
}
