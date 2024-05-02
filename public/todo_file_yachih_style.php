<?php

declare(strict_types = 1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../framework/Supports/helpers.php';

$now = time();
$prettyNow = date_format(date_create('now', timezone_open('Asia/Taipei')), 'Y - m - d');;
$todoPath = base_path('/storage/logs/todos');
$todos = file_exists($todoPath)
    ? json_decode(file_get_contents($todoPath), true)
    : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (strlen($_POST['add'] ?? '') > 0) {
        $todos[] = [
            'id' => uniqid(),
            'content' => $_POST['add'],
            'isEdit' => false,
            'createdAt' => $now,
            'updatedAt' => $now,
            'finishedAt' => null,
            'deletedAt' => null,
        ];
    }

    if (isset($_POST['edit'])) {
        foreach ($todos as $idx => $todo) {
            if ($todo['id'] === $_POST['edit']) {
                $todo['isEdit'] = true;
                $todo['updatedAt'] = $now;

                $todos[$idx] = $todo;
            }
        }
    }

    if (isset($_POST['store'])) {
        foreach ($todos as $idx => $todo) {
            if ($todo['id'] === $_POST['store']) {
                $todo['content'] = $_POST[$todo['id']];
                $todo['isEdit'] = false;
                $todo['updatedAt'] = $now;

                $todos[$idx] = $todo;
            }
        }
    }

    if (isset($_POST['toggle'])) {
        foreach ($todos as $idx => $todo) {
            if ($todo['id'] === $_POST['toggle']) {
                $todo['finishedAt'] = $todo['finishedAt'] ? null : $now;
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

    if (isset($_POST['clear'])) {
        $todos = [];
    }

    file_put_contents($todoPath, json_encode($todos));
}

$remain = count(array_filter($todos, fn ($todo) => !$todo['deletedAt']));

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-do List</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@100..900&display=swap" rel="stylesheet">
    <style>
        *{
            margin: 0;
            padding: 0;
            font-family: "Noto Sans TC", sans-serif;
        }

        .hidden{
            display: none;
        }

        button, ul, li{
            border: none;
            box-shadow: none;
            background: none;
            text-decoration: none;
        }

        .action-button{
            cursor: pointer;
        }

        body {
            background-color: #D8DDEF;
        }

        body .main-container{
            padding: 0 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            gap: 30px;
        }

        .title-container,
        .to-do-container{
            width: 100%;
            max-width: 680px;
            padding: 15px 20px;
            border-radius: 10px;
        }

        .title-container{
            background-color: #665687;
        }

        .title-container h1{
            font-size: 20px;
            font-weight: 600;
            text-align: center;
            color: white;
        }

        .title-container p{
            color: #D8DDEF;
            text-align: center;
        }

        .to-do-container{
            background-color: #FFF;
            height: 540px;
        }

        .to-do-container p:nth-child(1) {
            font-size: 20px;
            font-weight: 600;
        }

        .to-do-container ul {
            height: calc(540px - 140px);
            overflow: auto;
        }


        .to-do-container p:nth-child(2) {
            font-size: 16px;
            font-weight: 600;
            color: #665687;
        }

        .to-do-container .form-header{
            width: 100%;
            display: flex;
            align-items: center;
            gap: 20px;
            margin-top: 25px;
        }

        .to-do-header {
            display: flex;
            width: 100%;
            justify-content: space-between;
            align-items: center;
        }

        .label-button{
            color: #665687;
            cursor: pointer;
        }

        .to-do-container .to-do-header .label-button {
            font-size: 16px;
            background: none;
            color: #665687;
            padding: 10px 18px;
        }

        .to-do-container .to-do-header .label-button:active{
            font-size: 16px;
            background: none;
            color: #554870;
        }

        .to-do-container .form-header input,
        .to-do-container .to-do-list .to-do-item input{
            outline: none;
            width: 100%;
            border: none;
            background-color: #D8DDEF;
            padding: 12px 25px;
            border-radius: 10px;
            border: #D8DDEF 1.5px solid;
            transition: border 0.3s;
            font-size: 16px;
            color: #554870;
        }

        .to-do-container .form-header input:focus,
        .to-do-container .to-do-list .to-do-item input:focus{
            border-color: #665687;
        }

        .to-do-container .form-header input::placeholder{
            color: white;
        }

        .to-do-container #to_do_form .label-button{
            border: none;
            box-shadow: none;
            padding: 10px 18px;
            border-radius: 10px;
            background-color: #665687;
            white-space: nowrap;
            color: white;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
        }

        .to-do-container #to_do_form .label-button:active{
            background-color: #554870;
        }

        .to-do-container .to-do-list{
            margin-top: 12px;
        }

        .to-do-container .to-do-list .to-do-item{
            width: 100%;
            display: flex;
            align-items: center;
            gap: 10px;
            color: black;
            padding: 12px 0;
        }

        .to-do-container .to-do-list .to-do-item p,
        .to-do-container .to-do-list .to-do-item input{
            width: 100%;
            color: black;
            font-weight: 400;
            font-size: 16px;
        }

        .to-do-container .to-do-list .to-do-item input{
            border: 1.5px solid #D8DDEF;
            background: #D8DDEF;
            padding: 8px 16px;
            border-radius: 10px;
            color: #554870;
        }

        .checkbox-container{
            width: 100%;
            color: #554870;
            position: relative;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            white-space: nowrap;
            user-select: none;
            margin-right: 30px;
            gap: 10px;
        }

        .checkbox-inner{
            flex-shrink: 0;
            width: 100%;
            display: inline-block;
            position: relative;
            border: #665687 1px solid;
            border-radius: 3px;
            box-sizing: border-box;
            width: 14px;
            height: 14px;
            background-color: #fff;
            z-index: 1;
            transition: border-color .25s cubic-bezier(.71,-.46,.29,1.46), background-color .25s cubic-bezier(.71,-.46,.29,1.46), outline .25s cubic-bezier(.71,-.46,.29,1.46);
        }

        .checkbox-container input.checked ~ .checkbox-inner{
            background-color: #665687;
            border-color:#665687;
        }

        .checkbox-container input.checked ~ .checkbox-inner:after {
            transform: rotate(45deg) scaleY(1);
            border-color: white;
        }

        .checkbox-inner:after{
            box-sizing: content-box;
            content: "";
            border: 1.5px solid transparent;
            border-left: 0;
            border-top: 0;
            height: 7px;
            left: 4px;
            position: absolute;
            top: 1px;
            transform: rotate(45deg) scaleY(0);
            width: 4px;
            transition: transform .15s ease-in .05s;
            transform-origin: center;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="title-container">
            <h1>TO - DO LIST</h1>
            <p>Keep track of your daily to-do list.</p>
        </div>

        <form class="to-do-container" action="" method="POST">
            <div class="to-do-header">
                <div>
                    <p><?php echo $prettyNow ?></p>
                    <p><?php echo $remain ?> tasks</p>
                </div>
                <label for="clear" class="label-button">
                    <span class="action-button">Clear All</span>
                    <input class="hidden" type="submit" id="clear" name="clear"/>
                </label>
            </div>

            <!-- 新增 -->
            <div class="form-header" action="" method="post" id="to_do_form">
                <input type="text" placeholder="Please enter the content" name="add" autocomplete="off">
                <label class="label-button" for="form-add">
                    <span class="action-button">ADD IT</span>
                    <input class="hidden" id="form-add" type="submit"></input>
                </label>
            </div>

            <!-- 顯示列表 -->
            <div class="to-do-list">
                <ul>
                    <?php foreach ($todos as $todo) { ?>
                        <?php if (is_null($todo['deletedAt'])) { ?>
                            <li class="to-do-item">
                                <?php if ($todo['isEdit']) { ?>
                                    <!-- 編輯狀態 -->
                                    <input
                                        type="text"
                                        name="<?php echo $todo["id"]; ?>"
                                        value="<?php echo $todo["content"]; ?>"
                                        autocomplete="off"
                                    />
                                    <label class="label-button" for="store-<?php echo $todo['id']; ?>">
                                        <span>CHECK</span>
                                        <input
                                            class="hidden"
                                            id="store-<?php echo $todo['id']; ?>"
                                            type="submit"
                                            name="store"
                                            value="<?php echo $todo['id']; ?>"
                                        />
                                    </label>
                                <?php } else { ?>
                                    <!-- 非編輯狀態 -->
                                    <label for="toggle-<?php echo $todo['id']; ?>" class='checkbox-container'>
                                        <!-- checkbox 表示是否完成 -->
                                        <input
                                            class="hidden <?php echo $todo['finishedAt'] ? 'checked' : '' ?>"
                                            id="toggle-<?php echo $todo['id']; ?>"
                                            type="submit"
                                            name="toggle"
                                            value="<?php echo $todo['id']; ?>"
                                        />
                                        <span class='checkbox-inner'></span>
                                        <p><?php echo $todo["content"]; ?></p>
                                    </label>
                                    <label class="label-button" for="edit-<?php echo $todo['id']; ?>">
                                        <span>EDIT</span>
                                        <input
                                            class="hidden"
                                            id="edit-<?php echo $todo['id']; ?>"
                                            type="submit"
                                            name="edit"
                                            value="<?php echo $todo["id"]; ?>"
                                        />
                                    </label>
                                    <label class="label-button" for="delete-<?php echo $todo['id']; ?>">
                                        <span>DELETE</span>
                                        <input
                                            class="hidden"
                                            id="delete-<?php echo $todo['id']; ?>"
                                            type="submit"
                                            name="delete"
                                            value="<?php echo $todo['id']; ?>"
                                        />
                                    </label>
                                <?php } ?>
                            </li>
                        <?php } ?>
                    <?php } ?>
                </ul>
            </div>
        </form>
    </div>
</body>
</html>