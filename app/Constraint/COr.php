<?php namespace ALttP\Constraint;

use ALttP\Constraint;

/**
 * Class representing joining two Constraints with the logical 'or' operator
 */
class COr implements Constraint {
	protected $lhs;
	protected $rhs;
	
	public function __construct($l, $r) {
		$this->lhs = $l;
		$this->rhs = $r;
	}
	
	public function evaluate($items) {
		return $this->lhs->evaluate($items) || $this->rhs->evaluate($items);
	}
	
	public function substitute($placed_item, $new_constraint) {
		return new COr($this->lhs->substitute($placed_item, $new_constraint), $this->rhs->substitute($placed_item, $new_constraint));
	}
	
	public function normalize() {
		return COr::of($this->lhs->normalize(), $this->rhs->normalize());
	}
	
	public function getLhs() {
		return $this->lhs;
	}
	
	public function getRhs() {
		return $this->rhs;
	}
	
	public function minRequired() {
		return min($this->lhs->minRequired(), $this->rhs->minRequired());
	}
	
	public function simplify() {
		if(is_a($this->lhs, Constraint\CLiteral::class)) {
			if($this->lhs->getLit()) {
				return $this->lhs;
			}
		}
		if(is_a($this->rhs, Constraint\CLiteral::class)) {
			if($this->rhs->getLit()) {
				return $this->rhs;
			}
		}
		return COr::of($this->lhs->simplify(), $this->rhs->simplify());
	}
	
	public static function of($l, $r) {
		return new COr($l, $r);
	}
}
