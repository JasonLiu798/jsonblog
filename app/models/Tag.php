<?php

class Tag extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'term';
	protected $primaryKey = 'ID';

	public $timestamps = false;

}