<!doctype html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>

</body>
</html>

<?php
function connectToServer($name, $user, $password, $db)
{
    return new PDO('mysql:host=' . $name . ';dbname=' . $db, $user, $password);
}

if (!isset($_POST['answers'])) {
    echo "You need to add at least one answer";
    sleep(3);
    header("Location: adminpage.php");
    die();
}

if (!isset($_POST['correctAnswer'])) {
    echo "You need to mark an answer as correct!";
    sleep(3);
    header("Location: adminpage.php");
    die();
}

$conn = connectToServer("localhost", "root", "insy56!", "quiz");


//Determine the primary key for the question
$sql = "SELECT pk_question_id FROM question ORDER BY pk_question_id DESC LIMIT 1;";
$sth = $conn->prepare($sql);
$sth->execute();
$nextQuestion = $sth->fetch()['pk_question_id'];
$nextQuestion++;

// Question
$sql = "INSERT INTO question(pk_question_id, fk_pk_category, question_name) VALUES (:qpk, :pk, :qname);";

$sth = $conn->prepare($sql);
$sth->bindParam(':qpk', $nextQuestion);
$sth->bindParam(':pk', $_POST['for_category']);
$sth->bindParam(':qname', $_POST['question']);
$sth->execute();
print_r($sth->errorInfo());


// Answers
$sql = "INSERT INTO answer(fk_question_id, answer_name, isCorrect) VALUES (:question, :aname, :correct);";
$sth = $conn->prepare($sql);
$sth->bindParam(':question', $nextQuestion);
$index = 0;
$true = 1;
$false = 0;
foreach ($_POST['answers'] as $val) {
    $sth->bindParam(':aname', $val);
    if ($index == $_POST['correctAnswer']) {
        $sth->bindParam(':correct', $true);
    } else {
        $sth->bindParam(':correct', $false);
    }
    $sth->execute();
    print_r($sth->errorInfo());
    $index++;
}


header("Location: adminpage.php");
die();

?>
