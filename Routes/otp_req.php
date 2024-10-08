<?php
session_start();
require ("../admin/connect.php");
require("../admin/check_election.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $otp = $_POST['otp'];
    $role = $_POST['role'];

    $sql=$db->querySingle("SELECT otp FROM " . ($role == 1 ? "userdata" : "candidate") . " WHERE mobile = '$username' AND otp = '$otp'");

    if ($sql ) {
        echo ' <script>
                alert("Registration Successful");
                window.location = ".././Routes/login.php";
            </script>';
    } else {
        echo '
            <script>
                alert("Invalid OTP");
                window.location = "reg.html";
            </script>
        ';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify Otp</title>
    <link rel="stylesheet" href="../admin/css/admin_login.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <style>
        #otp.notrecv{
            visibility: hidden;
        }
        #otp.recv{
            visibility: visible;
        }
        
    </style>
</head>
<body>
    <div id="bodysection">
        <h2>Verify OTP</h2>
        <form id="otpForm" onsubmit="handleOTP(event)">
            <fieldset id="mform">
                <input type="text" name="username" id="username" placeholder="Username" required><br>
                <input  type="number" name="otp" class="notrecv" id="otp" placeholder="OTP" ><br>
                <input type="hidden" name="role" id="role" value="<?php echo $_GET['role']; ?>">
                <button type="submit" id="otpButton">Get OTP</button>
            </fieldset>
        </form>
    </div>
    <script>
        function requestOTP() {
            var phone = $('#username').val();
            var role = $('#role').val();
            console.log(role);
            $.ajax({
                url: '../API/request_otp.php',
                type: 'POST',
                dataType: 'json',
                // data: JSON.stringify({ username: phone }),
                data: JSON.stringify({ username: phone, role: role }),
                contentType: 'application/json',
                success: function(data) {
                    if (data.success) {
                        // alert(data.message);
                        $('#otp').removeClass('notrecv');
                        $('#otp').addClass('recv');
                        $('#otpButton').text("Verify OTP");
                    } else {
                        alert(data.message);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert("Error: 70" + textStatus);
                }
            });
        }

        function validateOTP() {
            var phone = $('#username').val();
            var otp = $('#otp').val();
            var role = $('#role').val();
            $.ajax({
                url: '../API/validate_otp.php',
                type: 'POST',
                dataType: 'json',
                // data: JSON.stringify({ username: phone, otp: otp }),
                data: JSON.stringify({ username: phone, otp: otp, role: role }),
                contentType: 'application/json',
                success: function(data) {
                    if (data.success) {
                        alert("Registration Successful");
                        window.location.href = "login.php";
                    } else {
                        alert(data.message);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert("Error: 69" + textStatus);
                }
            });
        }

        function handleOTP(event) {
            event.preventDefault();
            if ($('#otp').hasClass('notrecv')) {
                requestOTP();
            } else {
                validateOTP();
            }
        }
    </script>
</body>
</html>

