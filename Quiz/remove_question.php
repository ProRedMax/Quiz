<!doctype html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Removing</title>
</head>
<body>

</body>
</html>
<?php
function connectToServer($name, $user, $password, $db)
{
    return new PDO('mysql:host=' . $name . ';dbname=' . $db, $user, $password);
}

$conn = connectToServer("localhost", "root", "insy56!", "quiz");

$sql = "DELETE FROM question WHERE pk_question_id = :pk;";
$sql2 = "DELETE FROM answer WHERE fk_question_id = :pk;";




foreach ($_POST['delete'] as $entry) {
    $sth = $conn->prepare($sql2);
    $sth->bindParam(':pk', $entry);
    $sth->execute();
    $sth = $conn->prepare($sql);
    $sth->bindParam(':pk', $entry);
    $sth->execute();
    print_r($sth->errorInfo());
}

header("Location: adminpage.php");
die();

?>