<?php namespace ALttP\Constraint;

use ALttP\Constraint;

/**
 * Class representing joining two Constraints with the logical 'and' operator
 */
class CAnd implements Constraint {
	protected $lhs;
	protected $rhs;
	
	public function __construct($l, $r) {
		$this->lhs = $l;
		$this->rhs = $r;
	}
	
	public function evaluate($items) {
		return $this->lhs->evaluate($items) && $this->rhs->evaluate($items);
	}
	
	public function update($placed_item, $new_constraint) {
		return new CAnd($this->lhs->update($placed_item, $new_constraint), $this->rhs->update($placed_item, $new_constraint));
	}
	
	public static function of($l, $r) {
		return new CAnd($l, $r);
	}
}
