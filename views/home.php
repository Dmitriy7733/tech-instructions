<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db.php';
//include 'config/db_init.php';  //для создания таблиц и первоначального заполнения,предварительно закоментить db.php
include 'assets/functions.php';
include 'app/users/fetch_instructions.php';
include 'app/users/submit_complaint.php';
include 'app/users/search_instructions.php';
include 'includes/header.php';

$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
?>

<div class="container">
    <div class="container">
    <h1 class="text-center">Поиск инструкций для техники</h1>

    <!-- Форма поиска -->
    <form class="mt-4" id="searchForm">
        <div class="input-group">
            <input type="text" id="searchInput" class="form-control" placeholder="Введите название прибора" aria-label="Название прибора" required>
            <div class="input-group-append">
                <button class="btn btn-primary" type="submit">Поиск</button>
            </div>
        </div>
    </form>
    <div class="table-responsive">
        <h2>Результаты поиска</h2>
        <table class="table table-bordered mt-3" id="searchResultsTable">
        <thead>
            <tr>
                <th>Название</th>
                <th>Описание</th>
            </tr>
        </thead>
        <tbody id="searchResults">
            <!-- Результаты поиска будут загружены здесь -->
        </tbody>
        </table>
    </div>
<script>
let debounceTimer;
const debounceDelay = 300; // 300ms задержка

document.getElementById('searchInput').addEventListener('input', function() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        const searchTerm = this.value;
        if (searchTerm.length > 2) { // Минимум 3 символа для поиска
            searchInstructions(searchTerm);
        } else {
            document.getElementById('searchResults').innerHTML = '';
        }
    }, debounceDelay);
});

function searchInstructions(searchTerm) {
    $.ajax({
        url: 'app/users/search_instructions.php',
        type: 'POST',
        data: { search_term: searchTerm },
        success: function(response) {
            try {
                let results = JSON.parse(response);
                const searchResultsBody = $('#searchResults');
                searchResultsBody.empty(); // Очищаем предыдущие результаты

                if (results.error) {
                    alert(results.error);
                    return;
                }

                if (Array.isArray(results) && results.length > 0) {
                    results.forEach(function(instruction) {
                        searchResultsBody.append(`
                            <tr>
                                <td>
                                    <a href="#" class="instruction-link" data-file="/uploads/${instruction.filename}" data-id="${instruction.id}">${instruction.title}</a>
                                </td>
                                <td>${instruction.description}</td>
                            </tr>
                        `);
                    });
                } else {
                    searchResultsBody.append('<tr><td colspan="2" class="text-center">Нет результатов для данного запроса.</td></tr>');
                }
            } catch (e) {
                console.error("JSON parse error: ", e);
                alert('Ошибка обработки данных от сервера.');
            }
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error: ", status, error);
            console.error("Response Text: ", xhr.responseText);
            
            let errorMsg = 'Произошла ошибка при выполнении поиска.';
            try {
                const jsonResponse = JSON.parse(xhr.responseText);
                if (jsonResponse.error) {
                    errorMsg = jsonResponse.error; // Используем ошибку из ответа сервера
                }
            } catch (e) {
                // Если не удается разобрать JSON, используем стандартное сообщение
            }
            
            alert(errorMsg);
        }
    });
}
</script>

    <div>
        <h2 class="text-center">Инструкции и руководства по эксплуатации техники</h2>

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
                        echo "<a class='dropdown-item' href='#' data-toggle='modal' data-target='#instructionsListModal' data-subcategory-id='{$subcategory['id']}'>{$subcategory['name']}</a>";
                    }
                } else {
                    echo "<a class='dropdown-item disabled' href='#'>Нет подкатегорий</a>";
                }

                echo "</div></div>";
            }
            ?>
    </div>
    <!-- Раздел "О сайте" -->
    <div id="about">
        <h2>О сайте</h2>
        <p>Наш сайт предоставляет доступ к инструкциям по эксплуатации различных видов техники и электроники. Мы стремимся помочь пользователям быстро находить необходимые документы для комфортного использования своей техники.</p>
    </div>
</div>

<!-- Модальное окно для отображения списка инструкций -->
<div class="modal fade" id="instructionsListModal" tabindex="-1" aria-labelledby="instructionsListModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="instructionsListModalLabel">Инструкции к прибору</h5>
                <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Закрыть">
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
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content" style="border: 2px solid #007bff; border-radius: 10px;">
            <div class="modal-header">
                <h5 class="modal-title" id="instructionModalLabel">Инструкция к прибору</h5>
                    <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Закрыть"> 
                        <span>&times;</span>
                    </button>
            </div>
            <div class="modal-body">
                <div id="instructionContent">
                    <iframe id="instructionIframe" src="" width="100%" height="800px" style="border:none;"></iframe>
                </div>
                <a href="#" class="btn btn-success" id="downloadButton">Скачать инструкцию</a>
                <button class="btn btn-danger" id="complaintButton" onclick="openComplaintModal(currentInstructionId)">Пожаловаться</button>
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
                <form id="complaintForm" method="POST" action="app/users/submit_complaint.php">
                    <div class="form-group">
                        <label for="complaintText">Укажите суть жалобы</label>
                        <textarea class="form-control" id="complaintText" rows="4" required></textarea>
                    </div>
                    <input type="hidden" id="complaintInstructionId" name="instruction_id" value="">
                    <input type="hidden" id="userId" name="user_id" value="<?php echo $_SESSION['user_id'] ?? ''; ?>">
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                        <button type="submit" class="btn btn-danger">Отправить жалобу</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    var currentFileUrl = ''; // Переменная для хранения URL текущего файла
    var openInstructionModals = 0; // Переменная для отслеживания количества открытых модальных окон для инструкций
    //var isRegistered = true; // Установка в true для тестирования

$(document).on('click', '.dropdown-item', function(e) {
    e.preventDefault();
    var subcategoryId = $(this).data('subcategory-id');
    if (!subcategoryId) {
        alert("ID подкатегории не указан.");
        return; // Если ID не указан, выходим из функции
    }
    var subcategoryName = $(this).text(); // Получаем название подкатегории

// Отправляем AJAX-запрос для получения инструкций по выбранной подкатегории
$.ajax({
    url: 'app/users/fetch_instructions.php',
    type: 'POST',
    data: {subcategory_id: subcategoryId},
    dataType: 'json',
    success: function(response) {
        var instructionListModal = $('#instructionListModal');
        instructionListModal.empty();
        $('#instructionsListModalLabel').text('Инструкции к ' + subcategoryName);

        if (response.success) {
            var instructions = response.instructions;

            if (instructions && instructions.length > 0) {
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
        } else {
            instructionListModal.append(`<tr><td colspan="2">${response.message}</td></tr>`);
        }

        $('#instructionsListModal').modal('show');
    },
    error: function() {
        alert('Произошла ошибка при загрузке инструкций.');
    }
    });
});

// Обработчик для клика на ссылки в списке инструкций
var currentInstructionId; // Переменная для хранения ID инструкции
$(document).on('click', '.instruction-link', function(e) {
    e.preventDefault();
    console.log("Instruction link clicked."); // Лог для проверки
    currentFileUrl = $(this).data('file').replace('http://', 'https://');
    currentInstructionId = $(this).data('id'); // Получаем ID инструкции
    $('#instructionModal').modal('show');
    openInstructionModals++;
    $('#instructionContent').html(`<iframe id="instructionIframe" src="${currentFileUrl}" width="100%" height="400px" style="border:none;"></iframe>`);
    // Устанавливаем значение instructionId в скрытое поле формы жалобы
    $('#complaintInstructionId').val(currentInstructionId);
});

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
//для жалоб
function openComplaintModal(instructionId) {
    $('#complaintInstructionId').val(instructionId);
    $('#complaintModal').modal('show');
}

$(document).on('submit', '#complaintForm', function(e) {
    e.preventDefault(); // Предотвращаем обычное поведение формы
    var complaintText = $('#complaintText').val();
    var instructionId = $('#complaintInstructionId').val();
    var userId = $('#userId').val();
    console.log("Submitting complaint for Instruction ID: ", instructionId);

    // Проверяем корректность ID инструкции
    if (!instructionId || instructionId === "0") {
        alert("ID инструкции не найден. Пожалуйста, попробуйте снова.");
        return;
    }

    $.ajax({
        url: 'app/users/submit_complaint.php',
        type: 'POST',
        data: {
            instruction_id: instructionId,
            complaint_text: complaintText,
            user_id: userId
        },
        success: function(response) {
            let result;
            try {
                result = JSON.parse(response);
            } catch (e) {
                console.error('JSON Parse Error: ', e);
                alert('Ошибка обработки ответа сервера. Убедитесь, что сервер возвращает корректный JSON.');
                return;
            }
            if (result.success) {
                alert('Жалоба успешно отправлена!');
                $('#complaintModal').modal('hide');
            } else {
                alert(result.message || 'Произошла ошибка при отправке жалобы.');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error: ', xhr.responseText);
            alert('Произошла ошибка: ' + error);
        }
    });
});
</script>
    <?php
    include 'includes/footer.php';
    ?>

