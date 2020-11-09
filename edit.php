<?php 
require_once('config.php');
require_once('functions.php');

$dbh = connectDB();

$id = $_GET['id'];

$sql = "SELECT * FROM plans WHERE id = :id";
$stmt = $dbh->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

$task = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $due_date = $_POST['due_date'];

    $errors = [];

    if ($title == '') {
        $errors['title'] = '学習内容を入力してください';
    }

    if ($due_date == '') {
        $errors['due_date'] = '期限日を入力してください';
    }

    if ($title == $task['title'] && $due_date == $task['due_date']) {
        $errors['no_edit'] = '変更内容がありません';
    }

    if (!$errors) {
        $sql = "UPDATE plans SET title = :title, due_date = :due_date WHERE id = :id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':due_date', $due_date, PDO::PARAM_STR);
        $stmt->execute();

        header('Location : index.php');
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>編集画面</title>
</head>
<body>
    <h2>編集</h2>
    <form action="" method="post">
        <label for="">学習内容</label>
        <input type="text" name="title" value="<?= h($task['title']) ?>">
        <br>
        <label for="">期限日</label>
        <input type="date" name="due_date" value="<?= h($task['due_date']) ?>">
        <input type="submit" value="追加">
        <br>
        
        <?php if(!$errors == '') : ?>
            <?php foreach ($errors as $error) : ?>
            <li class="error"><?= $error ?></li>
            <?php endforeach ; ?>
        <?php endif ; ?>
        <a href="index.php">戻る</a>
        
    </form>
</body>
</html>