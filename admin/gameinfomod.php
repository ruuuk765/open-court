<?php
require_once('../model/dao/GameInfoDao.php');
require_once('../model/dao/DetailDao.php');
use dao\DetailDao;
use dao\GameInfoDao;

$gameInfo = null;
$gameInfoDao = new GameInfoDao();
// 試合情報取得
if (isset($_GET['id'])) {
    $gameInfo = $gameInfoDao->getGameInfo($_GET['id']);
}
if (empty($gameInfo)) {
    $gameInfo['id'] = '';
    $gameInfo['title'] = '';
    $gameInfo['game_date'] = '';
    $gameInfo['start_time'] ='';
    $gameInfo['end_time'] ='';
    $gameInfo['place'] ='';
    $gameInfo['limit_number'] = 0;
    $gameInfo['detail'] ='';
}

// 参加者情報取得
$participantList = null;
if(!empty($gameInfo['id'])) {
    $detailDao = new DetailDao();
    $participantList = $detailDao->getParticipantList($gameInfo['id']);
}

// CSFR対策
session_start();

// 暗号学的的に安全なランダムなバイナリを生成し、それを16進数に変換することでASCII文字列に変換します
$toke_byte = openssl_random_pseudo_bytes(16);
$csrf_token = bin2hex($toke_byte);
// 生成したトークンをセッションに保存します
$_SESSION['csrf_token'] = $csrf_token;

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>イベント情報修正</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="container">
    <a href="index.php">イベント一覧ページに戻る</a>
    <h2>イベント情報</h2>
    <form action="register.php" method="post" class="form-group">
        <input type="hidden" id="id" name="id" value="<?php echo $gameInfo['id'] ?>">
        <input type="hidden" name="csrf_token" value="<?=$csrf_token?>">
        <p>
            タイトル<input class="form-control" type="text" name="title"  required value="<?php echo $gameInfo['title'] ?>">
        </p>
        <p>
            日程<input class="form-control" type="date" name="game_date" required value="<?php echo $gameInfo['game_date'] ?>">
        </p>
        <p>
            開始時間<input class="form-control" type="time" name="start_time" required value="<?php echo $gameInfo['start_time'] ?>">
        </p>
        <p>
            終了時間<input class="form-control" type="time" name="end_time" required value="<?php echo $gameInfo['end_time'] ?>">
        </p>
        <p>
            場所<input class="form-control" type="text" name="place" required value="<?php echo $gameInfo['place'] ?>">
        </p>
        <p>
            人数上限<input class="form-control" type="number" name="limit_number" min="1" required value="<?php echo $gameInfo['limit_number'] ?>">
        </p>
        <p>
            詳細<textarea class="form-control" name="detail"><?php echo $gameInfo['detail'] ?></textarea>
        </p>
        <p>
            <button class="btn btn-primary" type="submit" name="register">登録</button>
            <button id="btn-delete" class="btn btn-secondary" type="submit" name="delete">削除</button>
        </p>
    </form>

    <hr>
    <div>
        <?php include('../participationInfo.php'); ?>
    </div>
    <hr>
    <div>
        <h3>参加者詳細</h3>
        <?php foreach ((array)$participantList as $participant): ?>
            <p>
                <?php echo htmlspecialchars($participant['name']); ?>&nbsp;&nbsp;
                <?php echo htmlspecialchars($participant['occupation_name']); ?>&nbsp;&nbsp;
                <?php echo htmlspecialchars($participant['sex_name']); ?>&nbsp;&nbsp;
                同伴：<?php echo htmlspecialchars($participant['companion']); ?>人
            </p>
            <p>
                <?php echo htmlspecialchars($participant['remark']); ?>
            </p>
            <p>
                <a class="btn btn-secondary" href="participant.php?id=<?php echo $participant['id'] ?>">修正</a>
            </p>
            <hr>
        <?php endforeach; ?>
    </div>
    <a href="index.php">イベント一覧ページに戻る</a>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script>
        'use strict';
        $(function(){ 
            let id = $('#id').val();
            if(id === '') {
                // 新規の時は削除ボタンは非表示に
                $('#btn-delete').addClass('hidden');
            } else {
                // 修正の時は削除ボタンに確認処理のイベント追加
                $('#btn-delete').on('click', function() {
                    return confirm('削除してもよろしいですか');
                });
            }
        });
    
    </script>
</body>
</html>