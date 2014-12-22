<?php

class Semester extends Firelit\DatabaseObject {
	
	protected static $tableName = 'semesters'; // The table name
	protected static $primaryKey = 'id'; // The primary key for table (or false if n/a)

	// Optional fields to set in extension
	protected static $colsJson = array(); // Columns that should be automatically JSON-encoded/decoded when using

}