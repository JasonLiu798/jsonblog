<?php
class Stack{
	// A library to implement stacks in PHP via arrays
	// The Initialize function creates a new stack:
	public static function &stack_initialize() {
		// In this case, just return a new array
		$new = array();
		return $new;
	}
	// The destory function will get rid of a stack
	public static function stack_destroy(&$stack) {
		// Since PHP is nice to us, we can just use unset
		unset($stack);
	}
	// The push operation on a stack adds a new value unto the top of the stack
	public static function stack_push(&$stack, $value) {
		// We are just adding a value to the end of the array, so can use the
		//  [] PHP Shortcut for this.  It's faster than usin array_push
		$stack[] = $value;
	}
	// Pop removes the top value from the stack and returns it to you
	public static function stack_pop(&$stack) {
		// Just use array pop:
		return array_pop($stack);
	}
	// Peek returns a copy of the top value from the stack, leaving it in place
	public static function stack_peek(&$stack) {
		// Return a copy of the value on top of the stack (the end of the array)
		return $stack[count($stack)-1];
	}
	// Size returns the number of elements in the stack
	public static function stack_size(&$stack) {
		// Just using count will give the proper number:
		return count($stack);
	}
	// Swap takes the top two values of the stack and switches them
	public static function stack_swap(&$stack) {
		// Calculate the count:
		$n = count($stack);
	
		// Only do anything if count is greater than 1
		if ($n > 1) {
			// Now save a copy of the second to last value
			$second = $stack[$n-2];
			// Place the last value in second to last place:
			$stack[$n-2] = $stack[$n-1];
			// And put the second to last, now in the last place:
			$stack[$n-1] = $second;
		}
	}
	// Dup takes the top value from the stack, duplicates it,
	//  and adds it back onto the stack
	public static function stack_dup(&$stack) {
		// Actually rather simple, just reinsert the last value:
		$stack[] = $stack[count($stack)-1];
	}
}