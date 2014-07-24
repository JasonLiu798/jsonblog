<?php

class BaseController extends Controller {
	
	
	
	
	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		date_default_timezone_set ( "Asia/Shanghai" );
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
		
	}

}
