<?php
require 'database.php';

// on va récuperer l'id :
if(!empty($_GET['id']))
{
    $id = checkInput($_GET['id']); // on check pour la sécurité pour ne pas donner aux hacker une porte d'entrée dans nos infos

}

$db = Database::connect(); // on se connect

// on prepare et on fait notre requete 
$statement = $db->prepare('SELECT items.id, items.name, items.description, items.price, items.image, categories.name AS category 
                           FROM items LEFT JOIN categories ON items.category = categories.id
                           WHERE items.id = ?');
// on execute           
$statement->execute(array($id));
// on recupere la ligne
$item =$statement->fetch();
// on se deconnecte
Database::disconnect();

// cette fonction nettoie au cas ou une personne mal attentionnée serait passé par là
function checkInput($data)
{
    $data = trim($data);
    $data = stripcslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Burger Code</title>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
        <link href='http://fonts.googleapis.com/css?family=Holtwood+One+SC' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="../css/style.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.11.2/css/all.css" integrity="sha384-KA6wR/X5RY4zFAHpv/CnoG2UW1uogYfdnP67Uv7eULvTveboZJg0qUpmJZb5VqzN" crossorigin="anonymous">
    </head>

    <body>
        <h1 class="text-logo"><i class="fas fa-utensils"></i> BurgerCode <i class="fas fa-utensils"></i></h1>
        <div class="container admin">
            <div class="row">
                <div class="col-sm-6">
                    <h1><strong>Voir un item</strong></h1>
                    <br>
                    <form>
                        <div class="form-group">
                            <label>Nom:</label><?php echo ' ' . $item['name']; ?>
                        </div>
                        <div class="form-group">
                            <label>Description:</label><?php echo ' ' . $item['description']; ?>
                        </div>
                        <div class="form-group">
                            <label>Prix:</label><?php echo ' ' . number_format((float)$item['price'],2,'.', '') . ' €'; ?>
                        </div>
                        <div class="form-group">
                            <label>Catégorie:</label><?php echo ' ' . $item['category']; ?>
                        </div>
                        <div class="form-group">
                            <label>Image:</label><?php echo ' ' . $item['image']; ?>
                        </div>
                    </form>
                    <br>
                    <div class="form-actions">
                        <a href="index.php" class="btn btn-primary"><i class="fas fa-reply"></i> Retour</a>
                    </div>
                </div>
                <div class="col-sm-6 site">               
                    <div class="thumbnail">
                        <img src="<?= '../images/' . $item['image']; ?>" alt="menu 1">
                        <div class="price"><?= number_format((float)$item['price'],2,'.', '') . ' €'; ?></div>
                        <div class="caption">
                            <h4><?= $item['name']; ?></h4>
                            <p><?= $item['description']; ?></p>
                            <a href="#" class="btn btn-order" role="button"><i class="fas fa-cart-plus"></i> Commander</a>
                        </div>
                    </div>                        
                </div>               
            </div>
        </div>
    </body>
</html>