<?php

# Includes the autoloader for libraries installed with composer
require __DIR__ . '/vendor/autoload.php';

# Imports the Google Cloud client library
use Google\Cloud\Speech\SpeechClient;

read_db('select * from os');

$longitude = $_POST['longitude'];
$latitude = $_POST['latitude'];

$size = $_FILES['audio_data']['size']; 
$input = $_FILES['audio_data']['tmp_name'];
$output = $_FILES['audio_data']['name'] . time() .".wav"; 
move_uploaded_file($input, $output);

$projectId = 'tactile-stack-223313';
$speech = new SpeechClient([
    'projectId' => $projectId,
    'languageCode' => 'ja-JP',
]);

# wav -> flac
$flac = "wave.flac";
$command = "ffmpeg -y -i $output $flac";
exec($command, $out, $res);
if($res) {
	echo "Error Command: $command, ";
	return;
}

$options = [
    'encoding' => 'FLAC'
];

$results = $speech->recognize(fopen($flac, 'r'), $options);

$text = "";
if( !empty($results) ) {
   $text = $results[0]->alternatives()[0]['transcript'];
}

if( strpos($text, '公園') !== false){
  echo $text;
} else {
  echo $text;
}
echo $text;

function read_db($sql, array $params = null) {
    try {
        $pdo = new PDO('sqlite:testdb.sqlite3');
        // (毎回if文を書く必要がなくなる)
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // デフォルトのフェッチモードを連想配列形式に設定 
        // (毎回PDO::FETCH_ASSOCを指定する必要が無くなる)
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        
        // $stmt = $pdo->prepare("SELECT * FROM os WHERE id = ?");
        // $stmt->execute(['1']);
        $stmt = $pdo->prepare("SELECT * FROM os");
        $stmt->execute($params);
        $r1 = $stmt->fetchAll();
        // 結果を確認
        write_log($r1[0]['name']);
    } catch (Exception $e) {
        write_log('エラー発生');
        write_log($e->getMessage());
    }    
}

/**
 * write logs
 */
function write_log($text) {
    $f = fopen("chatbot.log", "a");
    @fwrite($f, date("Y/m/d H:i:s") . "\t" . $text."\n");
    fclose($f);    
}

?>