<?php namespace ALttP;

/**
 * A constraint representing a boolean formula determining whether or not a location is accessible.
 */
interface Constraint {
	public function evaluate($items);
	public function update($placed_item, $new_constraint);
}
