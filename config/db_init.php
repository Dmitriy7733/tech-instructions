<?php
require_once  "db.php";

try {
    // Создание таблицы пользователей
    getDb()->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        status TEXT DEFAULT 'active',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        block_reason TEXT,
        role TEXT DEFAULT 'user'
    )");

    // Создание таблицы категорий
    getDb()->exec("CREATE TABLE IF NOT EXISTS categories (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        parent_id INTEGER,
        FOREIGN KEY (parent_id) REFERENCES categories(id)
    )");
    //создание таблицы инструкций
    getDb()->exec("CREATE TABLE IF NOT EXISTS instructions (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        filename TEXT NOT NULL,
        category_id INTEGER NOT NULL,
        subcategory_id INTEGER,
        title TEXT NOT NULL,  
        description TEXT NOT NULL, 
        upload_date DATETIME DEFAULT CURRENT_TIMESTAMP,
        approved INTEGER DEFAULT 0,
        FOREIGN KEY (category_id) REFERENCES categories(id),
        FOREIGN KEY (subcategory_id) REFERENCES categories(id), 
        FOREIGN KEY (user_id) REFERENCES users(id)
    )");
    //Создание таблицы жалоб
    getDb()->exec("CREATE TABLE IF NOT EXISTS complaints (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        instruction_id INTEGER NOT NULL,
        complaint_text TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (instruction_id) REFERENCES instructions(id)
    )");
    
} catch (PDOException $e) {
    echo "Ошибка подключения к базе данных: " . $e->getMessage();
}

try {
    // Добавление категорий
    $categories = ['Бытовая техника', 'Электроника'];
    $categoryIds = [];
    foreach ($categories as $category) {
        $stmt = getDb()->prepare("INSERT INTO categories (name) VALUES (:name)");
        $stmt->execute([':name' => $category]);
        $categoryIds[$category] = getDb()->lastInsertId(); // Сохраняем ID категории
    }

    // Вложенные категории для "Бытовая техника" и "Электроника"
    $subcategories = [
        'Бытовая техника' => [
            'Стиральные машинки (LG, Samsung, Bosch)',
            'Холодильники (Whirlpool, Electrolux, Beko)',
            'Посудомоечные машины (Siemens, Miele, Zanussi)',
            'Кондиционеры (LG, Daikin, Mitsubishi)',
            'Утюги (Philips, Braun, Tefal)',
            'Микроволновые печи (Samsung, LG, Panasonic)',
            'Варочные панели (Bosch, Electrolux, Whirlpool)'
        ],
        'Электроника' => [
            'Телевизоры (Sony, LG, Samsung)',
            'Компьютеры (Dell, HP, Asus)',
            'Смартфоны (Apple, Xiaomi, Huawei)'
        ]
    ];

    // Вставка подкатегорий в базу данных
    foreach ($subcategories as $categoryName => $subs) {
        foreach ($subs as $subcategory) {
            $stmt = getDb()->prepare("INSERT INTO categories (name, parent_id) VALUES (:name, :parent_id)");
            $stmt->execute([':name' => $subcategory, ':parent_id' => $categoryIds[$categoryName]]);
        }
    }

    // Добавление пользователей
    $users = [
        [
            'username' => 'admin',
            'password' => password_hash('123', PASSWORD_DEFAULT), // хэшируем пароль
            'email' => 'admin@admins.com',
            'role' => 'admin'
        ],
        [
            'username' => 'user',
            'password' => password_hash('123', PASSWORD_DEFAULT), // хэшируем пароль
            'email' => 'user@users.com',
            'role' => 'user'
        ]
    ];

    foreach ($users as $user) {
        $stmt = getDb()->prepare("INSERT INTO users (username, password, email, role) VALUES (:username, :password, :email, :role)");
        $stmt->execute([
            ':username' => $user['username'],
            ':password' => $user['password'],
            ':email' => $user['email'],
            ':role' => $user['role']
        ]);
    }

    echo "Таблицы успешно заполнены данными.";
} catch (PDOException $e) {
    echo "Ошибка при добавлении данных: " . $e->getMessage();
}

try {
    // Установите все инструкции как одобренные
    getDb()->exec("UPDATE instructions SET approved = 1");
    echo "Все инструкции были успешно одобрены.";
} catch (PDOException $e) {
    echo "Ошибка при одобрении инструкций: " . $e->getMessage();
}