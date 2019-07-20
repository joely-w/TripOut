<?php include('header.php'); ?>
<body>
<?php include('navigation.php'); ?>
<div class="hero">
    <ul>
        <li><h1>Scratch Conference Europe</h1></li>
    </ul>
    <div class="dropdown">
        <form class="login" role="login" method="post" action="authenticate.php">
            <input class="form-control" type="text" placeholder="Email" name="email" required autocomplete="off">
            <input class="form-control" type="password" placeholder="Password" name="password" required
                   autocomplete="off">
            <button>Login</button>
        </form>
    </div>
</div>
</body>