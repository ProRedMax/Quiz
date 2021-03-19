<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Quiz</title>
    <script src="../main.js" defer></script>
    <link href="../main.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
</head>
<body>
<header>
    <nav>
        <ul class="head">
            <li class="head"><a href="../index.html">Home</a></li>
            <li class="head"><a href="#">Start the quiz</a></li>
            <li class="head"><a href="adminpage.php">Admin page</a></li>
        </ul>
    </nav>
</header>
<h1 class="headText centered">Welcome to the quiz! Choose a category below to start</h1>
<form method="post" action="quizpage.php">
    <div class="list">


        <select name="category" class="centered">
            <?php
            function printSubCategorys(PDO $conn, $id, $name, $indention)
            {
                $sth = $conn->prepare("SELECT DISTINCT category.pk_category_id, category.category_name, category.subcategoryof
    FROM category
             INNER JOIN category c ON category.subcategoryof = :pk ORDER BY category.pk_category_id;");

                $sth->bindParam(':pk', $id);
                $sth->execute();
                while ($val = $sth->fetch()) {
                    if (isset($val)) {
                        echo "<option name='" . $name . "' value=" . $val['pk_category_id'] . ">" . str_repeat("&nbsp;", $indention) . "&#8594;" . $val['category_name'] . "</option>";
                        $tempIndention = $indention + $indention;
                        printSubCategorys($conn, $val['pk_category_id'], $name, $tempIndention);
                    } else {
                        break;
                    }
                }
            }

            function connectToServer($name, $user, $password, $db)
            {
                return new PDO('mysql:host=' . $name . ';dbname=' . $db, $user, $password);
            }

            $conn = connectToServer("localhost", "root", "insy56!", "quiz");

            $sth = "SELECT pk_category_id,category_name FROM category WHERE subcategoryof is null";

            foreach ($conn->query($sth) as $row) {
                echo "<option name='category' value=" . $row['pk_category_id'] . ">" . $row['category_name'] . "</option>";
                printSubCategorys($conn, $row['pk_category_id'], "category", 3);
            }

            $sth = null;
            $conn = null;
            ?>
        </select>

        <button type="submit" name="select-and-start" class="positive-btn">Start</button>
    </div>
</form>
</body>
</html>