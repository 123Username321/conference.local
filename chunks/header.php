<header>
    <section class="central">
        <h2><a href="index.php">IT-Con 2020</a></h2>
        <ul>
            <?php if (!isUser()): ?>
                <li><a id="login-link">Вход</a></li>
                <li><a id="register-link">Регистрация</a></li>

                <?php
                    require_once $_SERVER['DOCUMENT_ROOT'].'/chunks/login.php';
                    require_once $_SERVER['DOCUMENT_ROOT'].'/chunks/register.php';
                ?>
                <script src="js/log-reg.js"></script>

            <?php else: ?>
                <li>Привет, <?= $_SESSION['name'] ?></li>
                <li><a href="/actions/logout.php">Выход</a></li>

            <?php endif; ?>
        </ul>
    </section>
</header>