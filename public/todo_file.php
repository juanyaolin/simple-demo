<?php

declare(strict_types = 1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../framework/Supports/helpers.php';

$now = time();
$todoPath = base_path('/storage/logs/todos');
$todos = file_exists($todoPath)
    ? json_decode(file_get_contents($todoPath), true)
    : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (strlen($_POST['add'] ?? '') > 0) {
        $todos[] = [
            'id' => uniqid(),
            'content' => $_POST['add'],
            'enabled' => true,
            'createdAt' => $now,
            'updatedAt' => $now,
            'deletedAt' => null,
        ];
    }

    if (isset($_POST['edit'])) {
        foreach ($todos as $idx => $todo) {
            if ($todo['id'] === $_POST['edit']) {
                $todo['content'] = $_POST[$todo['id']];
                $todo['updatedAt'] = $now;

                $todos[$idx] = $todo;
            }
        }
    }

    if (isset($_POST['toggle'])) {
        foreach ($todos as $idx => $todo) {
            if ($todo['id'] === $_POST['toggle']) {
                $todo['enabled'] = !$todo['enabled'];
                $todo['updatedAt'] = $now;

                $todos[$idx] = $todo;
            }
        }
    }

    if (isset($_POST['delete'])) {
        foreach ($todos as $idx => $todo) {
            if ($todo['id'] === $_POST['delete']) {
                $todo['deletedAt'] = $now;
                $todos[$idx] = $todo;
            }
        }
    }

    file_put_contents($todoPath, json_encode($todos));
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <main>
        <h1>Todo List</h1>

        <!-- add -->
        <form action="" method="post">
            <div class="">
                <input type="text" name="add" autocomplete="off">
                <button type="submit">新增</button>
            </div>
        </form>

        <hr>

        <?php foreach ($todos as $todo) { ?>

            <?php if (!is_null($todo['deletedAt'])) {
                continue;
            } ?>

            <div class="" style="margin-bottom: 0.25rem; display:flex">

                <!-- toggle -->
                <form action="" method="post">
                    <input
                        type="hidden"
                        name="toggle"
                        value="<?php echo $todo['id']; ?>"
                        autocomplete="off"
                    >
                    <button type="submit">
                        <?php echo $todo['enabled'] ? '關閉' : '開啟'; ?>
                    </button>
                </form>

                <!-- edit -->
                <form action="" method="post">
                    <input
                        type="text"
                        name="<?php echo $todo['id']; ?>"
                        value="<?php echo $todo['content']; ?>"
                        autocomplete="off"
                        <?php echo $todo['enabled'] ? '' : 'disabled'; ?>
                    >
                    <input
                        type="hidden"
                        name="edit"
                        value="<?php echo $todo['id']; ?>"
                        autocomplete="off"
                    >
                    <?php if ($todo['enabled']) { ?>
                        <button type="submit">儲存</button>
                    <?php } ?>
                </form>

                <!-- delete -->
                <form action="" method="post">
                    <input
                        type="hidden"
                        name="delete"
                        value="<?php echo $todo['id']; ?>"
                        autocomplete="off"
                    >
                    <button type="submit">刪除</button>
                </form>
            </div>
        <?php } ?>

    </main>
</body>
</html>