<html>
  <head>
    <title>Add data to PLACE</title>
  </head>
  <body>
    <h1>Add data to PLACE</h1>

<?php
$db = new SQLite3('testdb.sqlite3');
$db->loadExtension('libspatialite.so.5');
$db->exec("SELECT InitSpatialMetadata()");
$db->exec("BEGIN");

$data = explode("\n", $_POST['content']);
foreach ($data as $datum) {
  $line = str_replace(array("\r", "\n"), '', $datum);
  $items = explode("\t", $line);
  $sql = "INSERT INTO place (category, name, geom) VALUES (";
  $sql .= "'" . $items[0] . "',";
  $sql .= "'" . $items[1] . "',";
  $sql .= "GeomFromText('POINT(";
  $sql .= $items[3];
  $sql .= " ";
  $sql .= $items[2];
  $sql .= ")', 4326))";
  $db->exec($sql);
}

$db->exec("COMMIT");

$sql = "SELECT DISTINCT Count(*), ST_GeometryType(geom), ";
$sql .= "ST_Srid(geom) FROM place";
$rs = $db->query($sql);
while ($row = $rs->fetchArray())
{
  # read the result set
  $msg = "Inserted ";
  $msg .= $row[0];
  $msg .= " entities of type ";
  $msg .= $row[1];
  $msg .= " SRID=";
  $msg .= $row[2];
  print "<h3>$msg</h3>";
}

# closing the DB connection
$db->close();
?>

  </body>
</html>