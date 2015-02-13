<?php

class Meeting extends Firelit\DatabaseObject {
	
	protected static $tableName = 'meetings'; // The table name
	protected static $primaryKey = 'id'; // The primary key for table (or false if n/a)

	// Optional fields to set in extension
	protected static $colsJson = array(); // Columns that should be automatically JSON-encoded/decoded when using

	/**
	 *	Get this object's details in an associative array
	 *	@return Array
	 */
	public function getArray() {
		
		return array(
			'id' => $this->id,
			'date' => date('n/j/y', strtotime($this->date)),
			'attendance' => $this->attendance,
			'group_size' => $this->group_size
		);

	}
}