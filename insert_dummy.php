<html>
  <head>
    <title>Add data to PLACE</title>
  </head>
  <body>
    <h1>Add data to PLACE</h1>

<?php
$db = new SQLite3('testdb.sqlite3');
# loading SpatiaLite as an extension
$db->loadExtension('libspatialite.so.5');
$db->exec("SELECT InitSpatialMetadata()");

$db->exec("BEGIN");
for ($i = 0; $i < 10000; $i++)
{
  # for POINTs we'll use full text sql statements
  $sql = "INSERT INTO place (category, name, geom) VALUES (";
  $sql .= "'Category #";
  $sql .= $i + 1;
  $sql .= "', 'Name #";
  $sql .= $i + 1;
  $sql .= "', GeomFromText('POINT(";
  $sql .= $i / 1000.0;
  $sql .= " ";
  $sql .= $i / 1000.0;
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