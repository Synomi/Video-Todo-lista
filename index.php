<?php
/*
TODO-sovellus
- sovellus näyttää nykyiset tehtävät
- sovellukseen voi lisätä uusia tehtäviä, joissa määritetään otsikko, kuvaus ja prioriteetti
- sovelluksessa tehtävän voi asettaa suoritetuksi
- tehtavia voi järjestää kenttien perusteella.
 */
$host = '127.0.0.1';
$db = 'todo';
$user = 'todo';
$pass = 'helloworld';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int) $e->getCode());
}

if ($_POST) {
    $data = [
        0 => $_POST['otsikko'],
        1 => $_POST['kuvaus'],
        2 => date('Y-m-d H:i:s'),
        3 => $_POST['prioriteetti'],
    ];
    try {
        $pdo->prepare("INSERT INTO tehtava(otsikko,kuvaus,lisatty,prioriteetti) VALUES (?,?,?,?)")->execute($data);
    } catch (PDOException $e) {
        throw $e;
    }
}
//aseta tehdyksi
if (isset($_GET['tehty'])) {
    try {
        $sql = "UPDATE tehtava SET suoritettu = ? WHERE id = ?";
        $pdo->prepare($sql)->execute([date('Y-m-d H:i:s'), $_GET['tehty']]);
    } catch (PDOException $e) {
        throw $e;
    }
}

$sort = 'prioriteetti';
$sortDirection = isset($_GET['direction']) ? 'desc' : 'asc';
if (isset($_GET['sort'])) {
    switch ($_GET['sort']) {
        case 'otsikko':
            $sort = 'otsikko';
            break;
        case 'kuvaus':
            $sort = 'kuvaus';
            break;
        case 'lisatty':
            $sort = 'lisatty';
            break;
        case 'prioriteetti':
            $sort = 'prioriteetti';
            break;
        case 'suoritettu':
            $sort = 'suoritettu';
            break;
        default:
            die('virheellinen järjestys');
            break;
    }
}

//hae rivit
$suoritetutTehtavat = [];
$stmt = $pdo->query("SELECT * FROM tehtava WHERE suoritettu IS NULL ORDER BY $sort $sortDirection");
while ($row = $stmt->fetch()) {
    $suoritetutTehtavat[] = $row;
}

$suorittamattomatTehtavat = [];
$stmt = $pdo->query("SELECT * FROM tehtava WHERE suoritettu IS NOT NULL ORDER BY $sort $sortDirection");
while ($row = $stmt->fetch()) {
    $suorittamattomatTehtavat[] = $row;
}
?>
<DOCTYPE! html>
<html lang="fi">
<head>
  <meta charset="utf-8">
  <title>Todo-lista</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</head>
<body>

<h1>TODO-tehtävälista</h1>
<table class="table table-dark">
<tr>
<th><a href="index.php?sort=otsikko<?=$sort == 'otsikko' && !isset($_GET['direction']) ? '&direction=otsikko' : ''?>">Otsikko</a></th>
<th><a href="index.php?sort=kuvaus<?=$sort == 'kuvaus' && !isset($_GET['direction']) ? '&direction=desc' : ''?>">Kuvaus</a></th>
<th><a href="index.php?sort=lisatty<?=$sort == 'lisatty' && !isset($_GET['direction']) ? '&direction=desc' : ''?>">Lisätty</a></th>
<th><a href="index.php?sort=prioriteetti<?=$sort == 'prioriteetti' && !isset($_GET['direction']) ? '&direction=desc' : ''?>">Prioriteetti</a></th>
<th>Aseta suoritetuksi</th>
</tr>
<?php
foreach ($suoritetutTehtavat as $t) {
    echo '<tr>';
    echo '<td>' . $t['otsikko'] . '</td>';
    echo '<td>' . $t['kuvaus'] . '</td>';
    echo '<td>' . $t['lisatty'] . '</td>';
    echo '<td>' . $t['prioriteetti'] . '</td>';
    echo '<td><a href="index.php?tehty=' . $t['id'] . '">X</a></td>';
    echo '</tr>';
}
?>
</table>

<h2>Lisää tehtävä</h2>
<form method="POST" style="width:80%;margin-left:auto;margin-right:auto;margin-bottom:20px;">
  <div class="form-group">
    <label for="exampleInputEmail1">Otsikko</label>
    <input type="text" class="form-control" id="exampleInputEmail1" name="otsikko" aria-describedby="emailHelp">
    <small id="emailHelp" class="form-text text-muted">Tehtävän otsikko</small>
  </div>
  <div class="form-group">
  <label for="exampleInputK">Kuvaus</label>
    <input type="text" class="form-control" id="exampleInputK" name="kuvaus" aria-describedby="exampleInputK">
    <small id="exampleInputK" class="form-text text-muted">Tehtävän kuvaus</small>

  </div>
  <div class="form-group">
    <label for="exampleInputP">Prioriteetti</label>
    <input type="number" class="form-control" id="exampleInputEmail1" name="prioriteetti" aria-describedby="exampleInputP">
    <small id="exampleInputP" class="form-text text-muted">Prioriteetti</small>
  </div>
  <button type="submit" class="btn btn-primary">Lisää tehtävä</button>
</form>
<h2>Suoritetut tehtävät</h2>

<table class="table table-dark" style="opacity:80%;">
<tr>
<th><a href="index.php?sort=otsikko<?=$sort == 'otsikko' && !isset($_GET['direction']) ? '&direction=otsikko' : ''?>">Otsikko</a></th>
<th><a href="index.php?sort=kuvaus<?=$sort == 'kuvaus' && !isset($_GET['direction']) ? '&direction=desc' : ''?>">Kuvaus</a></th>
<th><a href="index.php?sort=lisatty<?=$sort == 'lisatty' && !isset($_GET['direction']) ? '&direction=desc' : ''?>">Lisätty</a></th>
<th><a href="index.php?sort=prioriteetti<?=$sort == 'prioriteetti' && !isset($_GET['direction']) ? '&direction=desc' : ''?>">Prioriteetti</a></th>
<th><a href="index.php?sort=suoritettu<?=$sort == 'suoritettu' && !isset($_GET['direction']) ? '&direction=desc' : ''?>">Suoritettu</a></th>
</tr>
<?php
foreach ($suorittamattomatTehtavat as $t) {
    echo '<tr>';
    echo '<td>' . $t['otsikko'] . '</td>';
    echo '<td>' . $t['kuvaus'] . '</td>';
    echo '<td>' . $t['lisatty'] . '</td>';
    echo '<td>' . $t['prioriteetti'] . '</td>';
    echo '<td>' . $t['suoritettu'] . '</td>';
    echo '</tr>';
}
?>
</table>
</body>
</html>
