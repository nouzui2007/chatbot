<html>
  <head>
    <title>Create table</title>
  </head>
  <body>
    <h1>create table</h1>

<?php
# connecting some SQLite DB
# we'll actually use an IN-MEMORY DB
# so to avoid any further complexity;
# an IN-MEMORY DB simply is a temp-DB 
$db = new SQLite3('testdb.sqlite3');

# loading SpatiaLite as an extension
$db->loadExtension('libspatialite.so.5');
$db->exec("SELECT InitSpatialMetadata()");

# creating a POINT table
$sql = "CREATE TABLE IF NOT EXISTS place (";
$sql .= "id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,";
$sql .= "category TEXT NOT NULL,";
$sql .= "name TEXT NOT NULL)";
$db->exec($sql);
# creating a POINT Geometry column
$sql = "SELECT AddGeometryColumn('place', ";
$sql .= "'geom', 4326, 'POINT', 'XY')";
$db->exec($sql);

# closing the DB connection
$db->close();
?>

  </body>
</html>