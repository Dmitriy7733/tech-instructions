<?php 
session_start();
// Проверяем, авторизован ли пользователь
/*if (!isset($_SESSION['user_id'])) { // Предположим, что храните ID пользователя в сессии
    header("Location: login.php"); // Перенаправляем на страницу входа, если не авторизован
    exit;
}*/
// Подключение к базе данных 
require_once __DIR__ . '/../config/db.php'; // Убедитесь, что путь правильный
//require_once __DIR__ . '/../config/db_init.php';
// Получаем пользователей и их инструкции 
$users = getDb()->query("SELECT * FROM users")->fetchAll(); 
$instructions = getDb()->query("SELECT * FROM instructions WHERE approved = 0")->fetchAll(); 
$complaints = getDb()->query("SELECT * FROM complaints")->fetchAll(); 
?> 

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <title>Управление пользователями и инструкциями</title>
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="text-dark">Управление пользователями и инструкциями</h2>
    
    <!-- Вкладки -->
    <ul class="nav nav-tabs" id="adminTabs">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#instructions">Инструкции на одобрение</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#users">Пользователи</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#complaints">Жалобы пользователей</a>
        </li>
        <li class="nav-item">
            <form method="POST" action="/app/logout.php" class="d-inline">
                <button type="submit" class="btn btn-danger nav-link">Выход</button>
            </form>
        </li>
    </ul>

    <div class="tab-content">
        <!-- Инструкции на одобрение -->
        <div class="tab-pane fade show active" id="instructions">
            <div class="card mb-4">
                <div class="card-header">
                    <h3>Инструкции на одобрение</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Пользователь</th>
                                <th>Ссылка</th>
                                <th>Действия</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($instructions as $instruction): ?>
                                <?php $user = $db->query("SELECT * FROM users WHERE id = {$instruction['user_id']}")->fetch(); ?>
                                <tr>
                                    <td><?= htmlspecialchars($instruction['id']) ?></td>
                                    <td><?= htmlspecialchars($user['username']) ?></td>
                                    <td><a href="<?= htmlspecialchars($instruction['link']) ?>" target="_blank"><?= htmlspecialchars($instruction['link']) ?></a></td>
                                    <td>
                                        <button class="btn btn-success approve-instruction" data-id="<?= htmlspecialchars($instruction['id']) ?>">Одобрить</button>
                                        <button class="btn btn-danger delete-instruction" data-id="<?= htmlspecialchars($instruction['id']) ?>">Удалить</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Пользователи -->
        <div class="tab-pane fade" id="users">
            <div class="card mb-4">
                <div class="card-header">
                    <h3>Пользователи</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Имя пользователя</th>
                                <th>Статус</th>
                                <th>Причина блокировки</th>
                                <th>Действия</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= htmlspecialchars($user['id']) ?></td>
                                    <td><?= htmlspecialchars($user['username']) ?></td>
                                    <td><?= htmlspecialchars($user['status']) ?></td>
                                    <td><?= htmlspecialchars($user['block_reason'] ?? 'Нет') ?></td>
                                    <td>
                                        <?php if ($user['status'] == 'active'): ?>
                                            <button class="btn btn-warning block-user" data-id="<?= htmlspecialchars($user['id']) ?>">Заблокировать</button>
                                        <?php else: ?>
                                            <button class="btn btn-success unblock-user" data-id="<?= htmlspecialchars($user['id']) ?>">Разблокировать</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Жалобы пользователей -->
        <div class="tab-pane fade" id="complaints">
            <div class="card mb-4">
                <div class="card-header">
                    <h3>Жалобы пользователей</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Пользователь</th>
                                <th>Сообщение</th>
                                <th>Ответ</th>
                                <th>Статус ответа</th>
                                <th>Действия</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($complaints as $complaint): ?>
                                <?php $user = $db->query("SELECT * FROM users WHERE id = {$complaint['user_id']}")->fetch(); ?>
                                <tr>
                                    <td><?= htmlspecialchars($complaint['id']) ?></td>
                                    <td><?= htmlspecialchars($user['username']) ?></td>
                                    <td><?= htmlspecialchars($complaint['message']) ?></td>
                                    <td>
                                        <form method="POST" action="respond_complaint.php" class="d-inline">
                                            <input type="hidden" name="id" value="<?= htmlspecialchars($complaint['id']) ?>">
                                            <textarea name="response" class="form-control" rows="1" required></textarea>
                                            <button type="submit" class="btn btn-primary mt-1">Ответить</button>
                                        </form>
                                    </td>
                                    <td><?= htmlspecialchars($complaint['response'] ? 'Ответ дан' : 'Ответ не дан') ?></td>
                                    <td>
                                        <form method="POST" action="delete_complaint.php" class="d-inline">
                                            <input type="hidden" name="id" value="<?= htmlspecialchars($complaint['id']) ?>">
                                            <button type="submit" class="btn btn-danger mt-1">Удалить</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
$(document).ready(function() {
    // Одобрить инструкцию
    $('.approve-instruction').on('click', function() {
        var instructionId = $(this).data('id');
        $.ajax({
            url: 'approve_instruction.php',
            type: 'POST',
            data: {id: instructionId},
            success: function(response) {
                location.reload();
            }
        });
    });
    
    // Удалить инструкцию
    $('.delete-instruction').on('click', function() {
        var instructionId = $(this).data('id');
        if (confirm('Вы уверены, что хотите удалить эту инструкцию?')) {
            $.ajax({
                url: 'delete_instruction.php',
                type: 'POST',
                data: {id: instructionId},
                success: function(response) {
                    location.reload();
                }
            });
        }
    });
    
    // Блокировка пользователя
    $('.block-user').on('click', function() {
        var userId = $(this).data('id');
        var reason = prompt('Введите причину блокировки:');
        if (reason) {
            $.ajax({
                url: 'block_user.php',
                type: 'POST',
                data: {id: userId, reason: reason},
                success: function(response) {
                    location.reload();
                }
            });
        }
    });

    // Разблокировка пользователя
    $('.unblock-user').on('click', function() {
        var userId = $(this).data('id');
        $.ajax({
            url: 'unblock_user.php',
            type: 'POST',
            data: {id: userId},
            success: function(response) {
                location.reload();
            }
        });
    });
});
</script>

</body> 
</html>