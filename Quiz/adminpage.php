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
            <li class="head"><a href="quiz.php">Start the quiz</a></li>
            <li class="head"><a href="#">Admin page</a></li>
        </ul>
    </nav>
</header>
<h1 class="headText">Quiz admin page!</h1>

<form action="manipulate_category.php" method="post">
    <fieldset>
        <legend>Add a category!</legend>
        <label>
            Category Name
            <input type="text" name="category" required>
        </label>

        <label>
            Is the category a subcategory of another category?
            <select name="subcategory">
                <option name="subcategory" value="none">None</option>
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
                    echo "<option name='subcategory' value=" . $row['pk_category_id'] . ">" . $row['category_name'] . "</option>";
                    printSubCategorys($conn, $row['pk_category_id'], "subcategory", 3);
                }

                $sth = null;
                $conn = null;
                ?>
            </select>
        </label>
        <button type="submit" class="positive-btn" name="add_category" value="add">Add Category</button>
    </fieldset>
</form>

<form action="manipulate_category.php" method="post">
    <fieldset>
        <legend>Remove a category!</legend>
        <label>
            Remove the following category
            <select name="rem_category">
                <option name="rem_category" value="none">None</option>
                <?php


                $conn = connectToServer("localhost", "root", "insy56!", "quiz");

                $sth = "SELECT pk_category_id,category_name FROM category WHERE subcategoryof is null";

                foreach ($conn->query($sth) as $row) {
                    echo "<option name='rem_category' value=" . $row['pk_category_id'] . ">" . $row['category_name'] . "</option>";
                    printSubCategorys($conn, $row['pk_category_id'], "rem_category", 3);
                }

                $sth = null;
                $conn = null;
                ?>
            </select>
        </label>
        <button type="submit" class="negative-btn" name="remove_category" value="remove">Remove Category</button>
    </fieldset>
</form>

<form action="add_question.php" method="post">
    <fieldset>
        <legend>Add a question!</legend>
        <label>
            Question Name
            <input type="text" name="question" required>
        </label>

        <label>
            For Category:
            <select name="for_category">
                <?php
                $conn = connectToServer("localhost", "root", "insy56!", "quiz");

                $sth = "SELECT pk_category_id,category_name FROM category WHERE subcategoryof is null";

                foreach ($conn->query($sth) as $row) {
                    echo "<option name='for_category' value=" . $row['pk_category_id'] . ">" . $row['category_name'] . "</option>";
                    printSubCategorys($conn, $row['pk_category_id'], "for_category", 3);
                }

                $sth = null;
                $conn = null;
                ?>
            </select>
        </label>

        <div style="display: flex;
                    justify-content: center;
                    align-items: center;  ">
            <button class="addItem defaultbutton centered" onclick="addItem()" type="button">Add new answer
                possibility<span></span></button>
            <button class="removeItem defaultbutton centered" onclick="removeItem()" type="button">Remove last
                answer<span></span></button>

        </div>

        <div class="list">

        </div>

        <button type="submit" class="positive-btn" name="submit_question" value="add">Add question</button>
        <button type="reset" class="negative-btn" name="submit">Reset</button>

    </fieldset>
</form>

<form method="post" action="adminpage.php">
    <fieldset>
        <legend>Modify a question!</legend>
        <label>Select a Category to begin
            <select name="category">
                <?php
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
        </label>
        <div style="display: flex;
                    justify-content: center;
                    align-items: center;  ">
            <button type="submit" name="submit" value="submit" class="defaultbutton centered">Open question list
                <span></span>
            </button>
        </div>

    </fieldset>
</form>

<form method="post" action="remove_question.php">
    <fieldset>
        <legend>Remove a question!</legend>
        <div class="list">
            <?php
            if (isset($_POST['submit'])) {
                $conn = connectToServer("localhost", "root", "insy56!", "quiz");

                $sql = "SELECT * FROM question WHERE fk_pk_category = :pk;";
                $sth = $conn->prepare($sql);
                $sth->bindParam(':pk', $_POST['category']);
                $sth->execute();

                foreach ($sth->fetchAll() as $value) {
                    printf("<div class='answerEntry'>");
                    printf("<input size='50' type='text' disabled name='questions[]' placeholder='" . $value['question_name'] . "'>");
                    printf("<label>Delete<input type='checkbox' name='delete[]' value='" . $value['pk_question_id'] . "'></label>");
                    printf("</div>");
                }

                printf("<button type='submit' class='negative-btn'>Remove questions!</button>");
            }
            ?>
        </div>

    </fieldset>
</form>

</body>
</html>

<?php
