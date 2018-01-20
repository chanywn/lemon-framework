<?php
namespace Lemon\Session;

class Flash {

    public function __construct() {
        //if (!session_id()) @session_start();
    }


    public function save($data)
    {
        if($data)
            $_SESSION['flashMessage'] = $data;
    }


    public function get()
    {
        $session = '';

		if (session_id()) {
			if(isset($_SESSION['flashMessage'])){
				$session = $_SESSION['flashMessage'];
				unset($_SESSION['flashMessage']);
			}
		}

		return $session;
    }

}