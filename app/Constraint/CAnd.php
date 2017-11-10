<?php namespace ALttP\Constraint;

use ALttP\Constraint;
use ALttP\Constraint\COr;

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
	
	public function substitute($placed_item, $new_constraint) {
		return new CAnd($this->lhs->substitute($placed_item, $new_constraint), $this->rhs->substitute($placed_item, $new_constraint));
	}
	
	public function normalize() {
		if(is_a($this->lhs, Constraint\COr::class)) {
			return COr::of(CAnd::of($this->lhs->getLhs(), $this->rhs), CAnd::of($this->lhs->getRhs(), $this->rhs))->normalize();
		}
		if(is_a($this->rhs, Constraint\COr::class)) {
			return COr::of(CAnd::of($this->lhs, $this->rhs->getLhs()), CAnd::of($this->lhs, $this->rhs->getRhs()))->normalize();
		}
		return $this;
	}
	
	public function minRequired() {
		return $this->lhs->minRequired() + $this->rhs->minRequired();
	}
	
	public function simplify() {
		if(is_a($this->lhs, Constraint\CLiteral::class)) {
			if(!$this->lhs->getLit()) {
				return $this->lhs;
			}
		}
		if(is_a($this->rhs, Constraint\CLiteral::class)) {
			if(!$this->rhs->getLit()) {
				return $this->rhs;
			}
		}
		return CAnd::of($this->lhs->simplify(), $this->rhs->simplify());
	}
	
	public static function of($l, $r) {
		return new CAnd($l, $r);
	}
}
