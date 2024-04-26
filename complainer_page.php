<!DOCTYPE html>
<html>

<head>
    <title>Complainer Home Page</title>
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <link href="http://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic" rel="stylesheet" type="text/css">
    <link href="complainer_page.css" rel="stylesheet" type="text/css" media="all" />
</head>

<body style="background-size: cover; background-image: url(home_bg1.jpeg); background-position: center;">

<?php
session_start();
if (!isset($_SESSION['x'])) {
    header("location:userlogin.php");
}

$conn = mysqli_connect("localhost", "root", "", "crime_portal");
if (!$conn) {
    die("Could not connect: " . mysqli_connect_error());
}
mysqli_select_db($conn, "crime_portal");

$u_id = $_SESSION['u_id'];

$result = mysqli_query($conn, "SELECT a_no, u_name FROM user where u_id='$u_id' ");
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
$q2 = mysqli_fetch_assoc($result);
$a_no = $q2['a_no'];
$u_name = $q2['u_name'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $location = $_POST['location'];
    $type_crime = $_POST['type_crime'];
    $d_o_c = $_POST['d_o_c'];
    $description = $_POST['description'];

    $var = strtotime(date("Ymd")) - strtotime($d_o_c);

    // Check if files were uploaded
    if (!empty($_FILES['pictures']['name'][0])) {
        $file_paths = array();
        $upload_dir = 'uploads/'; // Directory to upload images
        foreach ($_FILES['pictures']['tmp_name'] as $key => $tmp_name) {
            $file_name = $_FILES['pictures']['name'][$key];
            $file_size = $_FILES['pictures']['size'][$key];
            $file_tmp = $_FILES['pictures']['tmp_name'][$key];
            $file_type = $_FILES['pictures']['type'][$key];
            // Check file size (2MB)
            if ($file_size > 2097152) {
                $errors[] = 'File size must be less than 2 MB';
            }
            // Move uploaded file to directory
            $target_file = $upload_dir . basename($file_name);
            if (!move_uploaded_file($file_tmp, $target_file)) {
                die("File upload failed");
            }
            $file_paths[] = $target_file; // Save file paths for database insertion
        }
        // Insert file paths into database table (comma-separated string)
        $file_names = implode(',', $file_paths);
    } else {
        $file_names = NULL; // No files uploaded
    }

    // Handle witness details
    $witness_name = isset($_POST['witness_name']) ? $_POST['witness_name'] : '';

    // Handle priority level
    $priority = isset($_POST['priority']) ? $_POST['priority'] : '';

    $comp = "INSERT INTO complaint(a_no, location, type_crime, d_o_c, description, file_names, witness_name, priority) VALUES ('$a_no', '$location', '$type_crime', '$d_o_c', '$description', '$file_names', '$witness_name', '$priority')";
    $res = mysqli_query($conn, $comp);

    if (!$res) {
        $message1 = "Error: " . mysqli_error($conn);
        echo "<script type='text/javascript'>alert('$message1');</script>";
    } else {
        // Check if any rows were affected
        if (mysqli_affected_rows($conn) > 0) {
            $message = "Complaint Registered Successfully";
            echo "<script type='text/javascript'>alert('$message');</script>";
        } else {
            $message = "No rows were inserted";
            echo "<script type='text/javascript'>alert('$message');</script>";
        }
    }
}
?>


    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="home.php"><b>Home</b></a>
            </div>
            <div id="navbar" class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li><a href="userlogin.php">User Login</a></li>
                    <li class="active"><a href="complainer_page.php">User Home</a></li>
                </ul>

                <ul class="nav navbar-nav navbar-right">
                    <li class="active"><a href="complainer_page.php">Log New Complain</a></li>
                    <li><a href="complainer_complain_history.php">Complaint History</a></li>
                    <li><a href="logout.php">Logout &nbsp <i class="fa fa-sign-out" aria-hidden="true"></i></a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="video" style="margin-top: 5%">
        <div class="center-container">
            <div class="bg-agile">
                <br><br>
                <div class="login-form">
                    <p><h1 style="color:black">Welcome <?php echo "$u_name" ?></h1></p><br>
                    <p><h2>Log New Complain</h2></p><br>
                    <form action="#" method="post" style="color: gray">
                        Aadhar
                        <input type="text" name="aadhar_number" placeholder="Aadhar Number" required="" disabled value=<?php echo "$a_no"; ?>>

                        <div class="top-w3-agile" style="color: gray">Location of Crime
                            <select class="form-control" name="location">
                                <?php
                                $loc = mysqli_query($conn, "select location from police_station");
                                while ($row = mysqli_fetch_array($loc)) {
                                ?>
                                    <option> <?php echo $row[0]; ?> </option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="top-w3-agile" style="color: gray">Type of Crime
                            <select class="form-control" name="type_crime">
                                <option>Theft</option>
                                <option>Robbery</option>
                                <option>Pick Pocket</option>
                                <option>Murder</option>
                                <option>Rape</option>
                                <option>Molestation</option>
                                <option>Kidnapping</option>
                                <option>Missing Person</option>
                            </select>
                        </div>
                        <div class="Top-w3-agile" style="color: gray">
                            Date Of Crime : &nbsp &nbsp
                            <input style="background-color: #313131;color: white" type="date" name="d_o_c" required>
                        </div>
                        <br>
                        <div class="top-w3-agile" style="color: gray">
                            Description
                            <textarea name="description" rows="20" cols="50" placeholder="Describe the incident in details with time" onfocusout="f1()" id="desc" required></textarea>
                        </div>
                        <div class="top-w3-agile" style="color: gray">
                            Upload Pictures :
                            <input type="file" name="pictures[]" multiple accept="image/*">
                        </div>
                        <div class="top-w3-agile" style="color: gray">
                            Witness Name and Contact:
                            <input type="text" name="witness_name">
                        </div>
                        <div class="top-w3-agile" style="color: gray">
    Priority Level:
    <div class="radio">
        <label><input type="radio" name="priority" value="1">High</label>
    </div>
    <div class="radio">
        <label><input type="radio" name="priority" value="2">Medium</label>
    </div>
    <div class="radio">
        <label><input type="radio" name="priority" value="3">Low</label>
    </div>
</div>

                        <input type="submit" value="Submit" name="s">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.js"></script>
    <script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <?php include 'footer.php'; ?>
</body>

</html>
