<?php

class TermTaxonomy extends Eloquent {

	protected $table = 'term_taxonomy';
	protected $primaryKey = 'term_id';

	public $timestamps = false;

}