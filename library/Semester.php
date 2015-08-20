<?php

class Semester extends Firelit\DatabaseObject {

	protected static $tableName = 'semesters'; // The table name
	protected static $primaryKey = 'id'; // The primary key for table (or false if n/a)

	// Optional fields to set in extension
	protected static $colsJson = array(); // Columns that should be automatically JSON-encoded/decoded when using

	static public function latestOpen() {

		$sql = "SELECT * FROM `". static::$tableName ."` WHERE `status`='OPEN' ORDER BY `start_date` ASC LIMIT 1";
		$q = new Firelit\Query($sql);

		return $q->getObject( get_called_class() );

	}

	static public function getCurrent() {

		$req = Firelit\Request::init();

		if (!empty($req->cookie['semester'])) {

			$semesterId = intval($req->cookie['semester']);

			$sql = "SELECT * FROM `". static::$tableName ."` WHERE `id`=:id LIMIT 1";
			$q = new Firelit\Query($sql, array(':id' => $semesterId));

			$semester = $q->getObject( get_called_class() );

			if ($semester) return $semester;

		}

		return static::latestOpen();

	}

}