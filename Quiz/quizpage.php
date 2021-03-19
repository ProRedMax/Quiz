<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Quiz Page</title>
    <script src="../main.js" defer></script>
    <link href="../main.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
</head>
<body>
<?php
session_name("Quiz");
session_start();

function connectToServer($name, $user, $password, $db)
{
    return new PDO('mysql:host=' . $name . ';dbname=' . $db, $user, $password);
}

$conn = connectToServer("localhost", "root", "insy56!", "quiz");

if (isset($_SESSION['category'])) {
    $pk_category = $_SESSION['category'];
} else {
    $pk_category = $_POST['category'];
}
$sql = "SELECT category_name FROM category WHERE pk_category_id = :pk";
$sth = $conn->prepare($sql);
$sth->bindParam(':pk', $pk_category);
$sth->execute();
while ($row = $sth->fetch()) {
    $category = $row['category_name'];
}
?>
<header>
    <nav>
        <ul class="head">
            <li class="head"><a href="../index.html">Home</a></li>
            <li class="head"><a href="quiz.php">Start the quiz</a></li>
        </ul>
    </nav>
</header>
<h1 class="headText">You chose the Category: <?php echo $category ?></h1>
<?php
$totalQuestions = 0;
$correctQuestions = 0;

if (isset($_POST['submit'])) {
    printf("<form method=\"post\" action=\"quiz.php\">");
    $sql1 = "SELECT * FROM question WHERE fk_pk_category =" . $pk_category;
    $sql2 = "SELECT * FROM answer WHERE fk_question_id = ?;";
    $questions = $conn->query($sql1);
    foreach ($questions as $item) {
        printf("<fieldset> <legend>" . $item['question_name'] . "</legend>");
        $sth = $conn->prepare($sql2);
        $sth->execute(array($item['pk_question_id']));
        $totalQuestions++;

        while ($answer = $sth->fetch()) {
            if ($answer['isCorrect'] == TRUE && ($answer['pk_answer_id'] == $_POST[$item['pk_question_id']])) {
                printf("<label class='correctAnswer'> " . $answer['answer_name']);
                printf("<input disabled checked type='radio' name=" . $item['pk_question_id'] . " value=" . $answer['pk_answer_id'] . ">");
                $correctQuestions++;
            } elseif ($answer['isCorrect'] == false && ($answer['pk_answer_id'] == $_POST[$item['pk_question_id']])) {
                printf("<label class='wrongAnswer'> " . $answer['answer_name']);
                printf("<input disabled checked type='radio' name=" . $item['pk_question_id'] . " value=" . $answer['pk_answer_id'] . ">");
            } elseif ($answer['pk_answer_id'] == $_POST[$item['pk_question_id']]) {
                printf("<label class='wrongAnswer'> " . $answer['answer_name']);
                printf("<input disabled checked type='radio' name=" . $item['pk_question_id'] . " value=" . $answer['pk_answer_id'] . ">");
            } elseif ($answer['isCorrect'] == false) {
                printf("<label class='wrongAnswer'> " . $answer['answer_name']);
                printf("<input disabled type='radio' name=" . $item['pk_question_id'] . " value=" . $answer['pk_answer_id'] . ">");
            } else {
                printf("<label class='correctAnswer'> " . $answer['answer_name']);
                printf("<input disabled type='radio' name=" . $item['pk_question_id'] . " value=" . $answer['pk_answer_id'] . ">");
            }
            printf("</label>");
            printf("<br>");
        }
        printf("</fieldset> <br>");
    }
    printf("<p>Correctly answered questions: " . $correctQuestions . "</p>");
    printf("<p>Your score: " . ($correctQuestions * 100 / $totalQuestions) . "&#037</p>");
    printf("<button class='positive-btn' type=\"submit\" name=\"submit\">Finish</button>");
    printf("</form>");
    session_destroy();
} else {
    printf("<form method=\"post\" action=\"quizpage.php\">");
    $sql1 = "SELECT * FROM question WHERE fk_pk_category =" . $pk_category;
    $sql2 = "SELECT * FROM answer WHERE fk_question_id = ?;";
    $questions = $conn->query($sql1);
    foreach ($questions as $item) {
        printf("<fieldset><legend>" . $item['question_name'] . "</legend>");
        $sth = $conn->prepare($sql2);
        $sth->execute(array($item['pk_question_id']));

        while ($answer = $sth->fetch()) {
            printf("<label> " . $answer['answer_name']);
            printf("<input type='radio' name=" . $item['pk_question_id'] . " value=" . $answer['pk_answer_id'] . ">");
            printf("</label>");
            printf("<br>");
        }
        printf("</fieldset><br>");
    }
    $_SESSION['category'] = $pk_category;
    printf("<button class='positive-btn' type=\"submit\" name=\"submit\">Check your answers</button>");
    printf("<button class='negative-btn' type=\"reset\" name=\"reset\">Reset</button>");
    printf("</form>");
}
?>
</body>
</html>