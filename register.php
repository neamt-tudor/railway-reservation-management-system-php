<!DOCTYPE html>
<html>
<head>
    <title>Register - Train Booking</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Create an Account</h2>
    <form action="register_user.php" method="POST">
        <label>Username:</label>
        <input type="text" name="username" required><br>

        <label>Password:</label>
        <input type="password" name="password" required><br>

        <label>Email:</label>
        <input type="email" name="email" required><br>

        <label>Full Name:</label>
        <input type="text" name="full_name"><br>

        <input type="submit" value="Register">
    </form>
</div>
</body>
</html>
