<?php namespace ALttP\Constraint;

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
	
	public function update($placed_item, $new_constraint) {
		return $lit;
	}
	
	public static function of($l) {
		return new CLiteral($l);
	}
}
}
