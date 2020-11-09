<?php 

require_once('config.php');
require_once('functions.php');

$dbh = connectDB();

$sql = "SELECT * FROM plans WHERE status = 'notyet' ORDER BY due_date ASC";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$notyet_plans = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql2 = "SELECT * FROM plans WHERE status = 'done'";
$stmt2 = $dbh->prepare($sql2);
$stmt2->execute();
$done_plans = $stmt2->fetchAll(PDO::FETCH_ASSOC); 

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

    if (!$errors) {
        $sql = "INSERT INTO plans (title ,due_date) VALUES (:title, :due_date)";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':due_date', $due_date, PDO::PARAM_STR);
        $stmt->execute();

        header('Location: index.php');
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
    <title>学習管理アプリ</title>
</head>
<body>
    <h2>学習管理アプリ</h2>
    <form action="" method="post">
        <label for="">学習内容</label>
        <input type="text" name="title">
        <br>
        <label for="">期限日</label>
        <input type="date" name="due_date">
        <input type="submit" value="追加">
        <br>
        <?php if(!$errors == '') : ?>
            <?php foreach($errors as $error) : ?>
                <li class="error"><?= h($error) ?></li>
            <?php endforeach ; ?>
        <?php endif ; ?>
    </form>
    <h3>未達成</h3>
    <?php foreach($notyet_plans as $plan) : ?>
        <?php if(date('Y-m-d') >= $plan['due_date']) : ?>
            <li class="error">
                <a href="done.php?id=<?= h($plan['id']) ?>">[完了]</a>
                <a href="edit.php?id=<?= h($plan['id']) ?>">[編集]</a>
                <?= h($plan['title']) ?>・・・完了期限:<?= h($plan['due_date']) ?>
            </li>
        <?php else : ?>
            <li>
                <a href="done.php?id=<?= h($plan['id']) ?>">[完了]</a>
                <a href="edit.php?id=<?= h($plan['id']) ?>">[編集]</a>
                <?= h($plan['title']) ?>・・・完了期限:<?= h($plan['due_date']) ?>
            </li>
        <?php endif ; ?>
    <?php endforeach ; ?>
    
    <br>
    
    <h3>達成済み</h3>
    <?php foreach($done_plans as $plan) : ?>
    <li>
        <?= h($plan['title']) ?>
    </li>
    <?php endforeach ; ?>
    
</body>
</html>