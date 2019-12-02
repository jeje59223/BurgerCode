<?php
    require 'database.php';

    if(!empty($_GET['id']))
    {
        $id = checkInput($_GET['id']);
    }

    // on initialise nos variables
    $nameError = $descriptionError = $priceError = $categoryError = $imageError = $name = $description = $price = $category = $image = "";

    if(!empty($_POST))
    {
        $name               = checkInput($_POST['name']);
        $description        = checkInput($_POST['description']);
        $price              = checkInput($_POST['price']);
        $category           = checkInput($_POST['category']);
        $image              = checkInput($_FILES['image']['name']); // pour recuperer une image on veut l'image et son nom*
        $imagePath          = '../images/' . basename($image); // la chemin de l'image
        $imageExtension     = pathinfo($imagePath, PATHINFO_EXTENSION); // extension de l'image
        $isSuccess          = true; // valider avec succes
        

        if(empty($name))
        {
            $nameError = "Ce champs ne peut pas être vide";
            $isSuccess = false;
        }
        if(empty($description))
        {
            $descriptionError = "Ce champs ne peut pas être vide";
            $isSuccess = false;
        }
        if(empty($price))
        {
            $priceError = "Ce champs ne peut pas être vide";
            $isSuccess = false;
        }
        if($price < 0) // vérifie que le pris n'est pas négatif
        {
            $priceError = "Le prix ne peut pas être négatif";
            $isSuccess = false;
        }
        if($price == 0) // vérifie que le prix est supérieur à 0
        {
            $priceError = "Le prix ne peut pas être nul";
            $isSuccess = false;
        }
        if(!is_numeric($price)) // vérifie si c'est une valeur numérique
        {
            $priceError = "Vous ne pouvez saisir que des chiffres";
            $isSuccess = false;
        }
        if(empty($category))
        {
            $categoryError = "Ce champs ne peut pas être vide";
            $isSuccess = false;
        }
        if(empty($image))
        {
            $isImageUpdated = false;
        }
        else
        {
            $isImageUpdated = true;
            $isUploadSuccess = true;
            if($imageExtension != "jpg" && $imageExtension != "png" && $imageExtension != "jpeg" && $imageExtension != "gif")
           {
               $imageError = "Les fichiers autorisés sont : .jpg, .jpeg, .png, .gif";
               $isUploadSuccess = false;
           } 
           if(file_exists($imagePath))
           {
               $imageError = "Le fichier existe déjà";
               $isUploadSuccess = false;
           }
           if($_FILES['image']['size'] > 500000)
           {
               $nameError = "Le fichier ne doit pas dépasser les 500KB";
               $isUploadSuccess = false;
           }
           if($isUploadSuccess)
           {
               if(!move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath))
               {
                   $imageError = "Il y a eu une erreur lors de l'upload";
                   $isUploadSuccess = false;
               }
           }
        }
        // si tout c'est bien passé
        if(($isSuccess && $isImageUpdated && $isUploadSuccess) || ($isSuccess && !$isImageUpdated))
        {
            $db = Database::connect();
            if($isImageUpdated)
            {
                $statement = $db->prepare("UPDATE items set name = ?, description = ?, price = ?, category = ?, image = ? WHERE id = ?");
                $statement->execute(array($name,$description,$price,$category,$image,$id));
            }
            else
            {
                $statement = $db->prepare("UPDATE items set name = ?, description = ?, price = ?, category = ? WHERE id = ?");
                $statement->execute(array($name,$description,$price,$category,$id));
            }
            
            Database::disconnect();
            header("Location: index.php"); // si tout c'est bien passé on est redirigé vers index.php
        }
        else if($isImageUpdated && !$isUploadSuccess)
        {
            $db =Database::connect();
            $statement = $db->prepare("SELECT image FROM items WHERE id = ?");
            $statement->execute(array($id));
            $item = $statement->fetch();
            $image = $item['image'];
            Database::disconnect();
        }
    }
    else
    {
        // ici on va récupérer les valeurs de la bdd et les afficher dans le formulaire de modif
        $db = Database::connect();            
        $statement = $db->prepare("SELECT * FROM items WHERE id = ?");
        $statement->execute(array($id));
        $item = $statement->fetch();
        $name           = $item['name'];
        $description    = $item['description'];
        $price          = $item['price'];
        $category       = $item['category'];
        $image          = $item['image'];
        Database::disconnect();
    }


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
                    <h1><strong>Modifier un item</strong></h1>
                    <br>
                    <form class="form" role="form" action="<?= 'update.php?id=' . $id; ?>" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="name">Nom:</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Nom" value="<?php echo $name; ?>">
                            <span class="help-inline"><?php echo $nameError; ?></span>
                        </div>
                        <div class="form-group">
                            <label for="description">Description:</label>
                            <input type="text" class="form-control" id="description" name="description" placeholder="Description" value="<?php echo $description; ?>">
                            <span class="help-inline"><?php echo $descriptionError; ?></span>
                        </div>
                        <div class="form-group">
                            <label for="price">Prix: (en  €)</label>
                            <input type="number" step="0.01" class="form-control" id="price" name="price" placeholder="Prix" value="<?php echo $price; ?>">
                            <span class="help-inline"><?php echo $priceError; ?></span>                        
                        </div>
                        <div class="form-group">
                        <label for="category">Catégorie:</label>
                            <select name="category" id="category" class="form-control">
                                <?php
                                    $db = Database::connect();
                                    foreach($db->query('SELECT * FROM categories') as $row)
                                    {
                                        if($row['id'] == $category)
                                            echo '<option selected="selected" value="' . $row['id'] . '">' . $row['name'] . '</option>';
                                        else
                                        echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                                    }
                                    Database::disconnect();
                                ?>
                            </select>
                            <span class="help-inline"><?php echo $categoryError; ?></span>
                        </div>
                        <div class="form-group">
                            <label>Image:</label>
                            <p><?php echo $image; ?></p>
                            <label for="image">Sélectionner une image:</label>
                            <input type="file" id="image" name="image">
                            <span class="help-inline"><?php echo $imageError; ?></span>
                        </div>
                    
                        <br>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-success"><i class="fas fa-pencil-alt"></i> Modifier</button>
                            <a href="index.php" class="btn btn-primary"><i class="fas fa-reply"></i> Retour</a>
                        </div>
                    </form>
                </div>
                <div class="col-sm-6 site">               
                    <div class="thumbnail">
                        <img src="<?= '../images/' . $image; ?>" alt="menu 1">
                        <div class="price"><?= number_format((float)$price,2,'.', '') . ' €'; ?></div>
                        <div class="caption">
                            <h4><?= $name; ?></h4>
                            <p><?= $description; ?></p>
                            <a href="#" class="btn btn-order" role="button"><i class="fas fa-cart-plus"></i> Commander</a>
                        </div>
                    </div>                        
                </div>   
            </div>  
        </div>
    </body>
</html>