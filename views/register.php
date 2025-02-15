<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'includes/header.php';

?>

<div class="register-container">
    <h2>Регистрация</h2>
    <form action="app/register_user.php" method="post">
        <div class="form-group">
            <label for="username">Логин</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="email">Введите Ваш E-mail:</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Пароль</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="confirm_password">Повторите пароль</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
        </div>
        <div class="form-group">
            <label for="captcha">Пожалуйста, введите текст с изображения:</label><br>
            <img src="app/captcha.php" alt="Капча" id="captchaImage">
            <button type="button" class="btn btn-secondary" onclick="refreshCaptcha()">Обновить капчу</button>
            <input type="text" class="form-control" id="captcha" name="captcha" required>
        <div id="captchaError" class="text-danger" style="display:none;"></div>
        </div>
        <button type="button" class="btn btn-primary" onclick="submitForm()">Зарегистрироваться</button>
    </form>
</div>
<script>
function refreshCaptcha() {
    var captchaImage = document.getElementById('captchaImage');
    captchaImage.src = 'app/captcha.php?' + Math.random(); // Обновление капчи
}

function submitForm() {
    var captchaInput = document.getElementById('captcha').value;
    var captchaError = document.getElementById('captchaError');

    // Проверка капчи с помощью AJAX
    fetch('app/validate_captcha.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'captcha=' + encodeURIComponent(captchaInput)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Если капча верна, отправляем форму
            document.querySelector('form').submit();
        } else {
            // Если капча неверна, показываем сообщение об ошибке
            captchaError.textContent = data.message;
            captchaError.style.display = 'block';
        }
    });
}
// Привязка функции submitForm к событию отправки формы
document.querySelector('form').addEventListener('submit', submitForm);
</script>
<?php
include 'includes/footer.php';
?>