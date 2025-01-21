<div class="container mt-5">
    <h2>Вход</h2>
    <form action="login.php" method="post">
        <div class="form-group">
            <label for="loginUsername">Логин</label>
            <input type="text" class="form-control" id="loginUsername" name="username" required>
        </div>
        <div class="form-group">
            <label for="loginPassword">Пароль</label>
            <input type="password" class="form-control" id="loginPassword" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Войти</button>
    </form>
</div>