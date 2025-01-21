<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Временно устанавливаем user_id для тестирования
$_SESSION['user_id'] = 2;
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../assets/functions.php';
require_once __DIR__ . '/../app/users/fetch_instructions.php';
require_once __DIR__ . '/../app/users/get_instruction.php';
require_once __DIR__ . '/../app/users/submit_complaint.php';
$isRegistered = isset($_SESSION['registered']) ? $_SESSION['registered'] : false; // Проверка существования ключа
?>

<div class="container mt-5">
    <h1 class="text-center">Поиск инструкций для техники</h1>

    <!-- Форма поиска -->
    <form class="mt-4">
        <div class="input-group">
            <input type="text" class="form-control" placeholder="Введите название прибора" aria-label="Название прибора" required>
            <div class="input-group-append">
                <button class="btn btn-primary" type="submit">Поиск</button>
            </div>
        </div>
    </form>

    <div class="mt-5">
        <h2>Инструкции и руководства по эксплуатации техники</h2>

        <div class="list-group">
            <?php
            // Используем функцию getCategories для получения всех категорий
            $categories = getCategories(); // Не передаем параметр, если не нужно в JSON

            // Группируем подкатегории по родительским категориям
            $categoryGroups = [];
            foreach ($categories as $category) {
                // Проверяем, является ли категория родительской
                if ($category['parent_id'] === null) {
                    $categoryGroups[$category['id']] = [
                        'id' => $category['id'],
                        'name' => $category['name'],
                        'subcategories' => []
                    ];
                } else {
                    // Если это подкатегория, добавляем к родительской категории
                    if (isset($categoryGroups[$category['parent_id']])) {
                        $categoryGroups[$category['parent_id']]['subcategories'][] = $category;
                    }
                }
            }
            // Отображаем категории и подкатегории
            foreach ($categoryGroups as $category) {
                echo "<div class='dropdown mb-2'>";
                echo "<button class='btn btn-secondary dropdown-toggle' type='button' id='categoryDropdown{$category['id']}' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>{$category['name']}</button>";
                echo "<div class='dropdown-menu' aria-labelledby='categoryDropdown{$category['id']}'>";

                // Проверяем, есть ли подкатегории
                if (!empty($category['subcategories'])) {
                    foreach ($category['subcategories'] as $subcategory) {
                        echo "<a class='dropdown-item' href='#' data-toggle='modal' data-subcategory-id='{$subcategory['id']}'>{$subcategory['name']}</a>";
                    }
                } else {
                    echo "<a class='dropdown-item disabled' href='#'>Нет подкатегорий</a>";
                }

                echo "</div></div>";
            }
            ?>
        </div>
        <ul class="list-group mt-3" id="instructionList">
        <!-- Инструкции будут загружены через AJAX -->
        </ul>
    </div>
</div>
<!-- Модальное окно для отображения списка инструкций -->
<div class="modal fade" id="instructionsListModal" tabindex="-1" aria-labelledby="instructionsListModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="instructionsListModalLabel">Инструкции к прибору</h5> <!-- Этот заголовок будет обновлен в JavaScript -->
                <button type="button" class="btn btn-danger" class="close" data-dismiss="modal" aria-label="Закрыть">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Название</th>
                            <th>Описание</th>
                        </tr>
                    </thead>
                    <tbody id="instructionListModal">
                        <!-- Список инструкций будет загружен здесь -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Модальное окно для просмотра инструкций -->
<div class="modal fade" id="instructionModal" tabindex="-1" aria-labelledby="instructionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-fullscreen">
        <div class="modal-content" style="border: 2px solid #007bff; border-radius: 10px;">
            <div class="modal-header">
                <h5 class="modal-title" id="instructionModalLabel">Инструкция к прибору</h5>
                <div class="ml-auto d-flex">
                    <button type="button" class="btn btn-secondary" id="minimizeButton">−</button>
                    <button type="button" class="btn btn-secondary" id="fullscreenButton">⛶</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Закрыть"> 
                        <span>&times;</span>
                    </button>
                </div>
            </div>
            <div class="modal-body">
                <div id="instructionContent">
                    <iframe id="instructionIframe" src="" width="100%" height="800px" style="border:none;"></iframe>
                </div>
                <a href="#" class="btn btn-success" id="downloadButton">Скачать инструкцию</a>
                <button class="btn btn-danger" id="complaintButton" onclick="openComplaintModal(currentInstructionId)">Пожаловаться</button>
                <p id="registrationMessage" style="display: none;">Для отправки жалобы необходимо зарегистрироваться.</p>
            </div>
        </div>
    </div>
</div>
<!-- Модальное окно для жалобы -->
<div class="modal fade" id="complaintModal" tabindex="-1" aria-labelledby="complaintModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="complaintModalLabel">Жалоба на инструкцию</h5>
                <button type="button" class="btn btn-danger" class="close" data-dismiss="modal" aria-label="Закрыть">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="complaintForm">
                    <div class="form-group">
                        <label for="complaintText">Укажите суть жалобы</label>
                        <textarea class="form-control" id="complaintText" rows="4" required></textarea>
                    </div>
                    <input type="hidden" id="complaintInstructionId" name="complaintInstructionId" value="">
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                        <button type="submit" class="btn btn-danger">Отправить жалобу</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Раздел "О сайте" -->
<div class="mt-5" id="about">
    <h2>О сайте</h2>
    <p>Наш сайт предоставляет доступ к инструкциям по эксплуатации различных видов техники и электроники. Мы стремимся помочь пользователям быстро находить необходимые документы для комфортного использования своей техники.</p>
</div>
<!-- Подключение jQuery позже удалить, при удалении почему-то подключение в футере не работает-->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    var currentFileUrl = ''; // Переменная для хранения URL текущего файла
    var openInstructionModals = 0; // Переменная для отслеживания количества открытых модальных окон для инструкций
    var currentInstructionId; // Переменная для хранения ID инструкции
    var isFullscreen = false; // Состояние модального окна
    var isRegistered = true; // Установите в true для тестирования

    // Обработчик для клика на кнопку "Развернуть"
$(document).on('click', '#fullscreenButton', function() {
    var element = document.getElementById("instructionModal");
    if (element.requestFullscreen) {
        element.requestFullscreen();
    } else if (element.mozRequestFullScreen) { // Firefox
        element.mozRequestFullScreen();
    } else if (element.webkitRequestFullscreen) { // Chrome, Safari and Opera
        element.webkitRequestFullscreen();
    } else if (element.msRequestFullscreen) { // IE/Edge
        element.msRequestFullscreen();
    }
    $(this).hide(); // Скрываем кнопку развертывания
    $('#minimizeButton').show(); // Показываем кнопку сворачивания
});

// Обработчик для выхода из полноэкранного режима
$(document).on('click', '#minimizeButton', function() {
    if (document.fullscreenElement) {
        document.exitFullscreen();
    }
    $(this).hide(); // Скрываем кнопку сворачивания
    $('#fullscreenButton').show(); // Показываем кнопку развертывания
});

// Отправляем AJAX-запрос для получения инструкций по выбранной подкатегории
$(document).on('click', '.dropdown-item', function(e) {
    e.preventDefault();
    var subcategoryId = $(this).data('subcategory-id');
    var subcategoryName = $(this).text(); // Получаем название подкатегории

    $.ajax({
        url: '/../app/users/fetch_instructions.php',
        type: 'POST',
        data: {subcategory_id: subcategoryId},
        success: function(response) {
            var instructions = JSON.parse(response);
            var instructionListModal = $('#instructionListModal');
            instructionListModal.empty(); // Очищаем предыдущий список
            // Устанавливаем заголовок модального окна
            $('#instructionsListModalLabel').text('Инструкции к ' + subcategoryName);
            if (instructions.length > 0) {
                instructions.forEach(function(instruction) {
                    instructionListModal.append(`
                        <tr>
                            <td><a href="#" class="instruction-link" data-file="/uploads/${instruction.filename}" data-id="${instruction.id}">${instruction.title}</a></td>
                            <td>${instruction.description}</td>
                        </tr>
                    `);
                });
            } else {
                instructionListModal.append('<tr><td colspan="2">Нет доступных инструкций для этой подкатегории.</td></tr>');
            }
            // Показываем модальное окно со списком инструкций
            $('#instructionsListModal').modal('show');
        },
        error: function() {
            alert('Произошла ошибка при загрузке инструкций.');
        }
    });
});

// Обработчик для клика на ссылки в списке инструкций
//var currentInstructionId; // Переменная для хранения ID инструкции
$(document).on('click', '.instruction-link', function(e) {
    e.preventDefault();
    console.log("Instruction link clicked."); // Лог для проверки
    currentFileUrl = $(this).data('file').replace('http://', 'https://');
    currentInstructionId = $(this).data('id'); // Получаем ID инструкции
    console.log("Current Instruction ID: ", currentInstructionId); // Лог для проверки
    $('#instructionModal').modal('show');
    $('#instructionContent').html(`<iframe id="instructionIframe" src="${currentFileUrl}" width="100%" height="400px" style="border:none;"></iframe>`);
    
    // Устанавливаем значение instructionId в скрытое поле формы жалобы
    $('#complaintInstructionId').val(currentInstructionId);
});


/*$(document).on('click', '.instruction-link', function(e) {
    e.preventDefault();
    currentFileUrl = $(this).data('file').replace('http://', 'https://');
    currentInstructionId = $(this).data('id'); // Получаем ID инструкции
    console.log("Current Instruction ID: ", currentInstructionId); // Лог для проверки
    $('#instructionModal').modal('show');
    openInstructionModals++;
    $('#instructionContent').html(`<iframe id="instructionIframe" src="${currentFileUrl}" width="100%" height="400px" style="border:none;"></iframe>`);
});*/

// Обработчик для закрытия модального окна с инструкцией
$('#instructionModal').on('hidden.bs.modal', function() {
    openInstructionModals--; // Уменьшаем счетчик при закрытии
    // Если все окна для просмотра закрыты, закрываем модальное окно со списком
    if (openInstructionModals === 0) {
        $('#instructionsListModal').modal('hide');
    }
    // Сбрасываем состояние полноэкранного режима
    isFullscreen = false;
    $('#instructionModal').removeClass('modal-fullscreen'); // Убираем полноэкранный класс
    $('#instructionContent').show(); // Показываем содержимое
    $('#minimizeButton').show(); // Показываем кнопку сворачивания
    $('#fullscreenButton').show(); // Показываем кнопку развертывания
});

// Обработчик для клика на кнопку "Скачать инструкцию"
$(document).on('click', '#downloadButton', function(e) {
    e.preventDefault();
    
    if (currentFileUrl) {
        // Создаем временную ссылку и инициируем скачивание
        var link = document.createElement('a');
        link.href = currentFileUrl; // Используем сохраненный URL
        link.download = ''; 
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    } else {
        alert('Файл для скачивания недоступен.');
    }
});

// Обработчик события отправки формы жалобы
$(document).on('submit', '#complaintForm', function(e) {
    e.preventDefault();
    
    var complaintText = $('#complaintText').val();
    var instructionId = $('#complaintInstructionId').val();
    console.log("Submitting complaint for Instruction ID: ", instructionId); // Лог для проверки
    // Проверяем, зарегистрирован ли пользователь для отправки жалобы
    /*if (!isRegistered) {
        $('#registrationMessage').show();
        return;
    }*/

    // Проверяем корректность ID инструкции
    if (!instructionId || instructionId === "0") {
        alert("ID инструкции не найден. Пожалуйста, попробуйте снова.");
        return;
    }

    $.ajax({
        url: '/../app/users/submit_complaint.php',
        type: 'POST',
        data: {
            instruction_id: instructionId,
            complaint_text: complaintText
        },
        success: function(response) {
            var result = JSON.parse(response);
            if (result.success) {
                alert('Жалоба успешно отправлена!');
                $('#complaintModal').modal('hide');
            } else {
                alert(result.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error: ', xhr.responseText);
            alert('Произошла ошибка: ' + error);
        }
    });
});

/*$(document).on('submit', '#complaintForm', function(e) {
    e.preventDefault();
    
    // Получаем данные из формы
    var complaintText = $('#complaintText').val();
    var instructionId = $('#complaintInstructionId').val();
    var userId = <?php echo json_encode($_SESSION['user_id']); ?>; // Получаем user_id из PHP

    console.log("Submitting complaint for Instruction ID: ", instructionId); // Лог для проверки

    // Проверяем корректность ID инструкции и наличие текста жалобы
    if (!instructionId || instructionId === "0") {
        alert("ID инструкции не найден. Пожалуйста, попробуйте снова.");
        return;
    }
    if (!complaintText.trim()) {
        alert("Пожалуйста, укажите текст жалобы.");
        return;
    }

    // Отправляем AJAX-запрос для отправки жалобы
    $.ajax({
        url: '/../app/users/submit_complaint.php',
        type: 'POST',
        data: {
            instruction_id: instructionId,
            complaint_text: complaintText,
            user_id: userId // Передаем user_id
        },
        success: function(response) {
            var result = JSON.parse(response);
            if (result.success) {
                alert('Жалоба успешно отправлена!');
                $('#complaintModal').modal('hide');
            } else {
                alert(result.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error: ', xhr.responseText);
            alert('Произошла ошибка: ' + error);
        }
    });
});*/
function openInstructionModal(instructionId) {
    currentInstructionId = instructionId; // Устанавливаем ID инструкции
    $('#complaintInstructionId').val(currentInstructionId); // Устанавливаем ID в скрытое поле
    $('#instructionIframe').attr('src', '/path/to/instruction/' + instructionId); // Устанавливаем источник для iframe
    $('#instructionModal').modal('show'); // Открываем модальное окно с инструкцией
}
function openComplaintModal(instructionId) {
    $('#complaintInstructionId').val(instructionId); // Устанавливаем ID инструкции в скрытое поле
    $('#complaintModal').modal('show'); // Открываем модальное окно для жалобы
}
// Открытие модального окна для жалобы
/*function openComplaintModal(instructionId) {
    $('#complaintInstructionId').val(instructionId);
    $('#complaintModal').modal('show');
}

$(document).on('click', '#complaintButton', function() {
    if (currentInstructionId) {
        openComplaintModal(currentInstructionId); // Вызов функции с текущим ID инструкции
    } else {
        alert("Текущий ID инструкции недоступен.");
    }
});*/
</script>
