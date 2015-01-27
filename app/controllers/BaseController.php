<?php

class BaseController extends Controller {

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout() {
		date_default_timezone_set("Asia/Shanghai");
		if (!is_null($this->layout)) {
			$this->layout = View::make($this->layout);
		}
	}

	public function about() {
		$username = User::get_name_from_session();
		$resp = View::make('templates/about', array('title' => '关于',
			'nav' => Constant::$NAV_ABOUT,
			'username' => $username,

		));
		return $resp;
	}

}
