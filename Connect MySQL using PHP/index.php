<?php
/********************************
*****Disabling the magic quotes**
********************************/
//Required for  PHP V.5.03.00 or earlier,
//uncomment the following snippet
/*
if (get_magic_quotes_gpc()) {
    $process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
    while (list($key, $val) = each($process)) {
        foreach ($val as $k => $v) {
            unset($process[$key][$k]);
            if (is_array($v)) {
                $process[$key][stripslashes($k)] = $v;
                $process[] = &$process[$key][stripslashes($k)];
            } else {
                $process[$key][stripslashes($k)] = stripslashes($v);
            }
        }
    }
    unset($process);
}
*/

//If add-joke link is clicked, the following condition will hold true and include the form.html
if(isset($_GET['addjoke'])){
	include 'form.html';
	exit();
}

/********
Connect to the database using PDO
********/
try{
	$pdo=new PDO('mysql:host=localhost;dbname=ijdb','ijdbuser','mypassword');
	$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	$pdo->exec('SET NAMES "utf8"');
} catch(PDOException $ex){
	$output='Unable to connect to the database'.$ex->getMessage();
	include 'error.html.php';
	exit();
}

/*********************
*Inserting Jokes*****
*********************/
if (isset($_POST['joketext'])){
	try{
		$sql='INSERT INTO joke SET
		joketext=:joketext,
		jokedate=CURDATE()';
		$s = $pdo->prepare($sql);
		$s->bindValue(':joketext', $_POST['joketext']);
		$s->execute();
	} catch(PDOException $ex){
		$error='Error adding submitted joke'.$ex->getMessage();
		include 'error.html.php';
		exit();
	}
	header('Location:http://localhost/dbconnect/'); //Reload the current directory
	exit();
}
/*********************************
*Deleting Jokes******************/
/********************************/

if (isset($_GET['deletejoke'])){ //True if the delete button is clicked
	try{
		$sql = 'DELETE FROM joke WHERE id = :id';
		$s = $pdo ->prepare($sql);
		$s -> bindValue(':id', $_POST['id']);
		$s->execute();
	}
	catch(PDOException $e){
		$error = 'Error deleting joke: '.$e->getMessage();
		include 'error.html.php';
		exit();
	}
	header('Location:http://localhost/dbconnect/');
	exit();
}  
/**************
Select the joketext column and display the results
***************/

//Fetch the result-set from the database.
try{
	$sql='SELECT id,joketext FROM joke';
	$result=$pdo->query($sql);
} catch(PDOException $e){
	$output='Error fetching jokes'.$e->getMessage();
	include 'error.html.php';
	exit();
}

// Extracting results from the result set

while($row = $result->fetch()){
	$jokes[]=array('id' => $row['id'], 'text' => $row['joketext']);
}
// Display the jokes
include 'jokes.html.php';







