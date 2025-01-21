<?php
session_start();
?>

<div class="container mt-5">
    <h2>Загрузка инструкции</h2>
    <?php
    // Отображение сообщений об ошибках и успехах
    if (isset($_SESSION['error'])) {
        echo "<div style='color: red;'>" . $_SESSION['error'] . "</div>";
        unset($_SESSION['error']);
    }
    if (isset($_SESSION['success'])) {
        echo "<div style='color: green;'>" . $_SESSION['success'] . "</div>";
        unset($_SESSION['success']);
    }
    ?>
    <form action="/app/users/upload.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="instructionTitle">Название инструкции</label>
            <input type="text" class="form-control" id="instructionTitle" name="title" required>
        </div>
        <div class="form-group">
            <label for="instructionFile">Выберите файл инструкции</label>
            <input type="file" class="form-control-file" id="instructionFile" name="file" required>
        </div>
        <div class="form-group">
            <label for="instructionDescription">Описание инструкции</label>
            <textarea class="form-control" id="instructionDescription" name="description" rows="3" required></textarea>
        </div>
        <div class="form-group">
            <label for="categorySelect">Выберите категорию</label>
            <select class="form-control" id="categorySelect" name="category_id" required>
                <option value="">-- Выберите категорию --</option>
            </select>
        </div>
        <div class="form-group">
            <label for="subcategorySelect">Выберите подкатегорию</label>
            <select class="form-control" id="subcategorySelect" name="subcategory_id" required>
                <option value="">-- Выберите подкатегорию --</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Загрузить инструкцию</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Загрузка категорий
    $.ajax({
        url: '/../app/users/get_categories.php',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.error) {
                console.error(data.error);
                alert(data.error);
                return;
            }
            var categorySelect = $('#categorySelect');
            categorySelect.empty();
            categorySelect.append($('<option>', { value: '', text: '-- Выберите категорию --' }));
            $.each(data, function(index, category) {
                categorySelect.append($('<option>', { value: category.id, text: category.name }));
            });
        },
        error: function(xhr, status, error) {
            console.error('Error fetching categories:', error);
            alert('Ошибка при загрузке категорий');
        }
    });

    // Получение подкатегорий при изменении категории
    $('#categorySelect').change(function() {
        var categoryId = $(this).val();
        if (!categoryId) return; // Если не выбрана категория, выходим

        $.ajax({
            url: '/../app/users/get_subcategories.php',
            method: 'GET',
            data: { category_id: categoryId },
            dataType: 'json',
            success: function(data) {
                if (data.error) {
                    console.error(data.error);
                    alert(data.error);
                    return;
                }
                var subcategorySelect = $('#subcategorySelect');
                subcategorySelect.empty();
                subcategorySelect.append($('<option>', { value: '', text: '-- Выберите подкатегорию --' }));
                $.each(data, function(index, subcategory) {
                    subcategorySelect.append($('<option>', { value: subcategory.id, text: subcategory.name }));
                });
            },
            error: function(xhr, status, error) {
                console.error('Error fetching subcategories:', error);
                alert('Ошибка при загрузке подкатегорий');
            }
        });
    });
});
</script>



