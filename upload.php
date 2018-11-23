<?php

# Includes the autoloader for libraries installed with composer
require __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/database.php';

# Imports the Google Cloud client library
use Google\Cloud\Speech\SpeechClient;

// 特定ワード
$keywords = array(
    '公園',
    '公民館'
);

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

$results = $speech->recognize(fopen($flac, 'r'), $options);

$text = "";
if (!empty($results)) {
    $text = $results[0]->alternatives()[0]['transcript'];
}

$json = false;
foreach ($keywords as $keyword) {
    if (strpos($text, $keyword) !== false) {
        $json = get_data($keyword, true);
        break;
    }    
}
if (!$json) {
    $json = get_data($text);
}

header('Content-type: application/json');
echo @json_encode($json);
