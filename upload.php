<?php

# Includes the autoloader for libraries installed with composer
require __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/database.php';


# Imports the Google Cloud client library
use Google\Cloud\Speech\SpeechClient;

$longitude = $_POST['longitude'];
$latitude = $_POST['latitude'];

$size = $_FILES['audio_data']['size'];
$input = $_FILES['audio_data']['tmp_name'];
$output = $_FILES['audio_data']['name'] . time() . ".wav";
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
if ($res) {
    echo "Error Command: $command, ";
    return;
}

$options = [
    'encoding' => 'FLAC',
];

// $results = $speech->recognize(fopen($flac, 'r'), $options);

// $text = "";
// if (!empty($results)) {
//     $text = $results[0]->alternatives()[0]['transcript'];
// }
$text = "公園";

// TODO 結果から特定ワードの場合は地図画面へ
if (strpos($text, '公園') !== false) {
    $rs = read_db("select * from place where geom is not null and category like '%" . $text . "%'");
    if ($rs) {
        // TODO geojson
        echo $text;
    } else {
        echo "該当データがありませんでした";
    }
} else {
    $rs = read_db("select * from place where and category like '%" . $text . "%'");
    if ($rs) {
        // TODO json
        echo $text;
    } else {
        echo "該当データがありませんでした";
    }
}
