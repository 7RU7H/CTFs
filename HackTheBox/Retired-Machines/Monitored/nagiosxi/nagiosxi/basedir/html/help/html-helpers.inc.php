<?php

/*
$table = new help_table("first header", "second header");

$table
	->tr("third data", "fourth data", "dfgdf")
	->write();


/*
class to make it easier to incorporate gettext while keeping creating some html easy
 ..developers are lazy.. this is helpful -bh
 */
class help_table

{

	protected $html = "";

	public function __construct() {

		$this->_reset();

		$this->_html('<table class="table table-condensed table-bordered table-hover" style="margin-bottom: 10px;">');

		if ( func_num_args() > 0 ) {

			$this->tr();

			$args = func_get_args();
			foreach( $args as $arg_key => $arg ) {

				$this->th($arg);
			}

			$this->tr();
		}

		return $this;
	}

	// this is a duplicate function of __construct
	public function reset() {

		$this->_reset();

		$this->_html('<table class="table table-condensed table-bordered table-hover" style="margin-bottom: 10px;">');

		if ( func_num_args() > 0 ) {

			$this->tr();

			$args = func_get_args();
			foreach( $args as $arg_key => $arg ) {

				$this->th($arg);
			}

			$this->tr();
		}

		return $this;
	}

	public function tr() {

		if ( func_num_args() > 0 ) {

			if ( $this->_open_tr() )
				$this->tr()->tr();
			else
				$this->tr();

			$args = func_get_args();
			foreach( $args as $arg_key => $arg ) {

				$this->td($arg);
			}

			$this->tr();


		} else {

			if ( $this->_open_tr() )
				$this->_html("</tr>");
			else
				$this->_html("<tr>");
		}

		return $this;
	}

	public function td($text = "") {

		if ( ! $this->_open_tr() )
			$this->tr();

		$this->_html("<td>" . $text . "</td>");

		return $this;
	}

	public function th($text = "") {

		$this->_html("<th>" . $text . "</th>");

		return $this;
	}

	public function write() {

		$this->_html("</table>");

		echo $this->html;
	}

	public function return_text() {

		$this->_html("</table>");

		return $this->html;
	}

	private function _html($text = "") {

		$this->html .= $text;
	}

	private function _reset() {

		$this->html = "";
	}

	// check if we have an open <tr> tag prior to the end of the string
	private function _open_tr() {

		// check tr tags to see if they match
		if ( substr_count($this->html, "<tr>") == substr_count($this->html, "</tr>") )
			return false;

		return true;
	}
}
