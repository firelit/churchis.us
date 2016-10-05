<?php

class Vars extends Firelit\DatabaseObject {

	protected static $tableName = 'vars'; // The table name
	protected static $primaryKey = 'name'; // The primary key for table (or false if n/a)

	// Optional fields to set in extension
	protected static $colsSerialize = array('value'); // Columns that should be automatically JSON-encoded/decoded when using

}