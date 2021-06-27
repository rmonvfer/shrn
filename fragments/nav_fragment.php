<?php
// Fragmento para la barra de navegación. ?>
<nav class="navbar">
    <ul>
        <li>
            <a class="logo" href="/">
                SHRN
            </a>
            <span id="navbar-menu-button" class="hide">
                <em class="fa fa-bars"></em>
            </span>
        </li>
    <?php if (!isset($_SESSION['id'])) { // No autenticado ?>
        <li><a href="/user/login.php">Login</a></li>
        <li><a href="/user/register.php">Registro</a></li>
        
    <?php } else { ?>
        <li>
            <a href="/user/home.php">
                <em class="fas fa-comments"></em>
                Comunidad
            </a>
        </li>

        <li id="queries" class="dropdown">
            <a class="dropbtn">
                <em class="fas fa-code"></em>
                Consultas
                <em class="fa fa-caret-down"></em>
            </a>
            <div class="dropdown-content">
                <a href="/query/new.php">Nueva</a>
                <a href="/query/published.php">Publicadas</a>
            </div>
        </li>

        <li id="username" class="dropdown">
            <a class="dropbtn">
                <em class="fas fa-user"></em>
                <?php echo $_SESSION['username'] ?>
                <em class="fa fa-caret-down"></em>
            </a>
            <div class="dropdown-content">
                <a href="/user/import.php">Importar</a>
                <a href="/user/export.php">Exportar</a>
                <a href="/user/logout.php">Salir</a>
            </div>
        </li>
    <?php } ?>
    </ul>
</nav>