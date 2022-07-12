<?php
	//Raz Ahamed | raz.abcoder@gmail.com
	/*
	1. Script Task :
	* Create a PHP script, that is executed from the command line, which accepts a CSV file as input (see command
	line directives below) and processes the CSV file. 
	* The parsed file data is to be inserted into a MySQL database.
	A CSV file is provided as part of this task that contains test data, the script must be able to process this file
	appropriately.
	----------------------------------------------------------------------------------------------------------------------
	The PHP script will need to correctly handle the following criteria:
	• CSV file will contain user data and have three columns: name, surname, email (see table
	definition below)
	• CSV file will have an arbitrary list of users
	• Script will iterate through the CSV rows and insert each record into a dedicated MySQL
	database into the table “users”
	• The users database table will need to be created/rebuilt as part of the PHP script. This will be
	defined as a Command Line directive below
	• Name and surname field should be set to be capitalised e.g. from “john” to “John” before being
	inserted into DB
	• Emails need to be set to be lower case before being inserted into DB
	• The script should validate the email address before inserting, to make sure that it is valid (valid
	means that it is a legal email format, e.g. “xxxx@asdf@asdf” is not a legal format). In case that
	an email is invalid, no insert should be made to database and an error message should be
	reported to STDOUT.
	------------------------------------------------------------------------------------------------------------------------
	*/
	
	try{
		// Define Function : Get and parse parameters
		function cmdLineDirectives()
		{
			echo "----------------------------------------------------------------------------------------------------\n";
			echo "Script Command Line Directives:\n";
			echo "----------------------------------------------------------------------------------------------------\n";
			echo "*  --file [csv file name] – this is the name of the CSV to be parsed\n";
			echo "*  --create_table - this will cause the MySQL users table to be built (and no further action will be taken)\n";
			echo "*  --dry_run - this will be used with the --file directive in case we want to run the script but not insert into the DB. All other functions will be executed, but the database won't be altered\n";
			echo "*  -u - MySQL username\n";
			echo "*  -p - MySQL password\n";
			echo "*  -h - MySQL host\n";
	
			echo "*  --help : which will output the above list of directives with details.\n";
		}
	
		// Check whether $argv variable is populated.
		if (count($argv) == 0) {
			die("The script requires register_argc_argv enabled in php.ini\n");
		}
		// If user requests help, print and exit
		if (in_array("--help", $argv)) {
			cmdLineDirectives();
			exit();
		}
	
		// Intilize variables
		$file        = "";
		$createtable = false;
		$dryrun      = false;
		$username    = "";
		$password    = "";
		$host        = "";
		$conn        = "";
		$dbname = "catalyst";
	
		// Filename param validate
		if (in_array("--file", $argv)) {
			$pos = array_search("--file", $argv);
			if ($pos < $argc - 1) {
				$file = $argv[$pos + 1];
				if (!(file_exists($file) && is_file($file))) {
					die("Info: Please provide the valid file name.\n");
				}
			} else {
				die("Info: No file name provided.\n");
			}
		}
	
		// Check whether dry_run was sent
		if (in_array("--dry_run", $argv)) {
			$dryrun = true;
		}
		
		// Username parsing
		if (in_array("-u", $argv)) {
			$pos = array_search("-u", $argv);
			if ($pos < $argc - 1) {
				$username = $argv[$pos + 1];
			}
		}
	
		// Password parsing  
		if (in_array("-p", $argv)) {
			$pos = array_search("-p", $argv);
			if ($pos < $argc - 1) {
				$password = $argv[$pos + 1];
			}
		}
	
		// Host parsing
		if (in_array("-h", $argv)) {
			$pos = array_search("-h", $argv);
			if ($pos < $argc - 1) {
				$host = $argv[$pos + 1];
			}
		}
	
		if (!$dryrun && ($username=="" || $host=="")){
			echo "\n";
			echo "The parameters Username and Host are required. Please try again.\n";
			cmdLineDirectives();
			die();		
		}
		
		// Select --create_table or --dry_run not both
		if ($createtable && $dryrun){
			echo "\n";
			echo "Select --create_table or --dry_run, not both. Please try again.\n";
			cmdLineDirectives();
			die();		
		}	
	
		// If run php user_upload.php --create_table -u root -h 127.0.0.1
		if (in_array("--create_table", $argv)) {		
			echo "--------------------\n";
			echo "Database Information\n";
			echo "--------------------\n";
			$conn = createTable($host, $username, $password, $dbname);
			exit();	
		}
	
		
		
	
		/*=================================== Creating database connection and users Table ===============================================================*/
		function connection($host, $username, $password, $dbname)
		{	
			try{
				$conn = mysqli_connect($host, $username, $password, $dbname);		
				// Check connection
				if (!$conn) {
					die("Error: Connection failed | ". mysqli_connect_errno() . "\n");
				}				
				return $conn;
			} catch(Exception $e) {
				echo 'Message: ' .$e->getMessage();
			}					
		}
	
		function createTable($host, $username, $password, $dbname) {
			// Create table users
			$conn = connection($host, $username, $password, $dbname);
			$sql = "CREATE TABLE if not exists users (
				name VARCHAR(255),
				surname VARCHAR(255),
				email VARCHAR(255) NOT NULL,
				UNIQUE KEY(email)
				)";
		
			if ($conn->query($sql) === TRUE) {
				echo "Table users created successfully\n";
			} else {
				echo "Error creating table: " . $conn->error . "\n";
			}
			// truncate table users
			$sql = "TRUNCATE TABLE users";
			
			if ($conn->query($sql) === FALSE) {
				echo "Error deleting table: " . $conn->error . "\n";
			}	
		}
		if(!$dryrun) {
			// create the connection to database			
			$conn = connection($host, $username, $password, $dbname);
			if($createtable) {
				exit();
			}
		}
		/* Read data from CSV file and insert into DB */
		if ($fh = fopen($file, 'r')) {
			$firstrow = fgets($fh);
			if ($dryrun){
				echo "----------------------------------\n";	
				echo "File records\n";
				echo "---------------------------------\n";			
			} else {
				echo "--------------------\n";
				echo "Database Information\n";
				echo "--------------------\n";
			}
			// Discard the first line - headers
			$insert = 0;
			$notinsert = 0;
			while (!feof($fh)) {
				// Reading CSV data line by line
				$line   = fgets($fh);			
				// Parse $line and split in columns
				$output = explode(",", $line);
				if (!isset($output[1])) {
				   $output[1] = null;
				}
				if (!isset($output[2])) {
				   $output[2] = null;
				}
				//Make name and surname first character uppercase
				//Lowercase and remove all illegal characters from email
				$name = ucfirst(strtolower(trim($output[0])));
				$surname = ucfirst(strtolower(trim($output[1])));
				$email = strtolower(trim($output[2]));
	
				if(!($name == "" && $surname == "" && $email == "")) {
				
					// Insert into database
					// If dry_run is true, read and parse data from csv but not insert to database
					// If create_table is true, connect to database, create the table and exit.
					// Validate email format
					if (filter_var($email, FILTER_VALIDATE_EMAIL)){			
						if (!$dryrun){
							$sql = "INSERT INTO users (name, surname, email) VALUES (?, ?, ?)";
	
							if($stmt  = $conn->prepare($sql)){
								// Bind variables to the prepared statement as parameters
								$stmt ->bind_param("sss", $name, $surname, $email);
								$stmt ->execute();
								$insert++;							
							} else{
								echo "ERROR: Could not prepare query: $sql. " . $mysqli->error . "\n";
							}
						} else {
							echo $name ." | ". $surname ." | ". $email . "\n";
						}
					} else {
						$notinsert++;
						echo "ERROR: Invalid email. Record ignored: ".$name ." | ". $surname ." | ". $email . "\n";
					}				
				}
			}
			if(!$dryrun) {
				echo "Records inserted successfully: ".$insert. "\n";
				echo "Records not inserted : ".$notinsert. "\n";
			}
			fclose($fh);
		} else {
			die("Invalid file specification\n");
		}
	
		if ($createtable){
			$conn->close();
		}
	} catch (Exception $e) {
		die('Message: ' .$e->getMessage());
	}	
?>