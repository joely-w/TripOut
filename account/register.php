<?php include('header.php'); ?>
<body>
<?php include('navigation.php'); ?>
<form method="post" action="reg_process.php">
    <input type="text" name="name" placeholder="Full name" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="text" name="username" placeholder="Username" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button>Submit</button>
</form>
</body>