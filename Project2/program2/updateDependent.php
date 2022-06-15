<?php
	session_start();
	require_once "config.php";

// Define variables and initialize with empty values
// Note: You can not update SSN
$Dname = $Bdate = $Sex = $Relationship = "";
$Dname_err = $Bname_err = $Sex_err = $Relationship_err = "";

// Get values from DB and populate the form
if (isset($_GET["Dname"]) && !empty(trim($_GET["Dname"]))) {
	$_SESSION["Dname"] = $_GET["Dname"];
	$Dname = $_GET["Dname"];
	$Essn = $_SESSION["Ssn"];

	// prepare a select statement
	$sql1 = "SELECT * FROM DEPENDENT WHERE Essn = ? AND Dependent_name = ?";

	if ($stmt1 = mysqli_prepare($link, $sql1)) {
		// bind variables to a prepared statement
		mysqli_stmt_bind_param($stmt1, "ss", $param_Essn, $param_Dname);
		$param_Essn = $Essn;
		$param_Dname = $Dname;

		// Attempt to execute prepared statement
		if (mysqli_stmt_execute($stmt1)) {
			$result1 = mysqli_stmt_get_result($stmt1);
			if (mysqli_num_rows($result1) > 0) {
				$row = mysqli_fetch_array($result1);
				$Bdate = $row['Bdate'];
				$_SESSION["Bdate"] = $Bdate;
				$Relationship = $row['Relationship'];
				$_SESSION["Relationship"] = $Relationship;
				$Sex = $row['Sex'];
				$_SESSION['Sex'] = $Sex;
			}
		}
	}
} else { echo "Missing Dname in URL"; }

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
	$Essn = $_SESSION["Ssn"];

    // Validate dependent's new name
    $Dname = trim($_POST["Dname"]);

    if(empty($Dname)){
        $Dname_err = "Please enter a Dname.";
    } elseif(!filter_var($Dname, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $Dname_err = "Please enter a valid Dname.";
    }
    // Validate Relationship
    $Relationship = trim($_POST["Relationship"]);
    if(empty($Relationship)){
        $Relationship_err = "Please enter a Relationship.";
    } elseif(!filter_var($Relationship, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $Relationship_err = "Please enter a valid Relationship.";
    }

	// Validate Sex
    $Sex = trim($_POST["Sex"]);
    if(empty($Sex)){
        $Sex_err = "Please enter Sex.";
    }
	// Validate Birthdate
    $Bdate = trim($_POST["Bdate"]);

    if(empty($Bdate)){
        $Bdate_err = "Please enter birthdate.";
    }

    // Check input errors before updating dependent in database
    if(empty($Dname_err) && empty($Relationship_err) && empty($Bdate_err)
				&& empty($Sex_err)){
        // Prepare an UPDATE statement
        $sql = "UPDATE DEPENDENT
				SET Dependent_name = ?, Sex = ?, Bdate = ?, Relationship = ?
				WHERE Essn = ? AND Dependent_name = ?";

        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssssss", $param_NewDname, $param_Sex,
			$param_Bdate, $param_Relationship, $param_Essn, $param_OldDname);

            // Set parameters
			$param_Essn = $Essn;
            $param_NewDname = $Dname;
			$param_Sex = $Sex;
			$param_Bdate = $Bdate;
            $param_Relationship = $Relationship;
			$param_OldDname = $_SESSION["Dname"];

            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records created successfully. Redirect to landing page
				    header("location: index.php");
					exit();
            } else{
                echo "<center><h4>Error while updating new Dependent</h4></center>";
				$SQL_err = mysqli_error($link);
				// echo "<h3>SQL Errors ".$SQL_err;"</h3>";
				// $Dname_err = "Enter a unique dependent's name.";
				$Dname = $_SESSION["Dname"];
            }
        }

        // Close statement
        mysqli_stmt_close($stmt);
    }

    // Close connection
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>College DB</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        .wrapper{
            width: 500px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h3>Update Record for SSN =  <?php echo $_GET["Ssn"]; ?> </H3>
                    </div>
                    <p>Please edit the input values and submit to update.
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
						<div class="form-group <?php echo (!empty($Dname_err)) ? 'has-error' : ''; ?>">
                            <label>Dependent Name</label>
                            <input type="text" name="Dname" class="form-control" value="<?php echo $Dname; ?>">
                            <span class="help-block"><?php echo $Dname_err;?></span>
                        </div>

                        <div class="form-group <?php echo (!empty($Relationship_err)) ? 'has-error' : ''; ?>">
                            <label>Relationship</label>
                            <input type="text" name="Relationship" class="form-control" value="<?php echo $Relationship; ?>">
                            <span class="help-block"><?php echo $Relationship_err;?></span>
                        </div>

                        <div class="form-group <?php echo (!empty($Sex_err)) ? 'has-error' : ''; ?>">
                            <label>Sex</label>
                            <input type="text" name="Sex" class="form-control" value="<?php echo $Sex; ?>">
                            <span class="help-block"><?php echo $Sex_err;?></span>
                        </div>

                        <div class="form-group <?php echo (!empty($Bdate_err)) ? 'has-error' : ''; ?>">
                            <label>Birth date</label>
                            <input type="date" name="Bdate" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                            <span class="help-block"><?php echo $Bdate_err;?></span>
                        </div>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="viewDependents.php" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
