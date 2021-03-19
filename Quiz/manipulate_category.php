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

<?php

function connectToServer($name, $user, $password, $db)
{
    return new PDO('mysql:host=' . $name . ';dbname=' . $db, $user, $password);
}

if (isset($_POST['add_category'])) {


    $conn = connectToServer("localhost", "root", "insy56!", "quiz");

    $sql = "INSERT INTO category(category_name, subcategoryof) VALUES (:category, :sub);";

    $sth = $conn->prepare($sql);

    if ($_POST['subcategory'] == 'none') {
        $sub = null;
    } else {
        $sub = $_POST['subcategory'];
    }


    $sth->bindParam(':category', $_POST['category']);
    $sth->bindParam(':sub', $sub, PDO::PARAM_INT);

    $sth->execute();

    $_POST = null;
    header("Location: adminpage.php");
    die();

} elseif (isset($_POST['remove_category'])) {

    print_r($_POST);

    $conn = connectToServer("localhost", "root", "insy56!", "quiz");

    $sql = "DELETE FROM category WHERE pk_category_id = :pk;";

    $sth = $conn->prepare($sql);
    $sth->bindParam(':pk', $_POST['rem_category'], PDO::PARAM_INT);
    $sth->execute();

    $_POST = null;


    header("Location: adminpage.php");
    die();
} else {
    printf("An error occurred");
    die();
}

?>

</body>
</html>


