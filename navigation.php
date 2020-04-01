<header role="banner">
    <nav id="navbar-primary" class="navbar" role="navigation">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse"
                        data-target="#navbar-primary-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <div class="collapse navbar-collapse" id="navbar-primary-collapse">
                <ul class="nav navbar-nav">
                    <li class="active"><a href="/">Home</a></li>
                    <li><a href="/events/browse.php">Browse</a></li>
                    <li><a href="/"><img id="logo-navbar-middle" src="/images/logo.png" width="200" alt="Trip Out Logo"></a>
                    </li>
                    <li><a href="/events/create_event.php">Create</a></li>
                    <?php if (!isset($_SESSION['Username'])) { ?>
                        <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Account
                        </a>
                        <div class="login-drop dropdown-menu">
                        <h3>Login</h3>
                        <form id="navbar_loginform">
                            <input type="hidden" name="navbar" value="true"/>
                            <label for="navbar_username"></label><input class="form-control" type="text"
                                                                        placeholder="Email or Username"
                                                                        id="navbar_username"
                                                                        name="id" required/>
                            <label for="navbar_password"></label><input class="form-control" type="password"
                                                                        placeholder="Password" id="navbar_password"
                                                                        name="password" required/>
                            <button class="btn">Login</button>
                        </form>
                        <a href="/account/register.php">Don't have an account?</a>
                    <?php } else {
                        ?>
                        <li><a href="/account/manage.php"><?php echo ucfirst($_SESSION['Username']) ?></a></li>
                        <?php
                    }
                    ?>
                </ul>
            </div>
        </div>
    </nav>
</header>



