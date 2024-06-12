<?php

const FILEPATH = "data.json";


$choix = null;
while ($choix != 9){

    echo "Menu:\n";
    echo "1. Création de livre\n";
    echo "2. Modification d’un livre\n";
    echo "3. Suppression d’un livre \n";
    echo "4. Affichage de tout les livres \n";
    echo "5. Affichage d’un livre \n";
    echo "6. Tri des livres \n";
    echo "7. Recherche d’un livre \n";
    echo "8. Voir l'historique \n";
    echo "9. Quitter\n";

    echo "Entre votre choix: ";

    $choix = trim(readline());
    
    switch ($choix){
        case 1:
            bookCreate();
            break;
        case 2:
            bookModify();
            break;
        case 3:
            bookDelete();
            break;
        case 4:
            allBooks();
            break;
        case 5:
            bookShow();
            break;
        case 6:
            bookSort();
            break;
        case 7:
            bookSearch();
            break;
        case 8:
            seeHistory();
            break;
    }   
    
}


function seeHistory(){

    $history = getHistory();

    if ($history ){
        echo "\nVoici l'historique : \n\n";

        foreach($history as $move){
            foreach ($move as $key => $value){
                if (is_numeric($key)){
                    echo $value . "\n";
                }else{
                    echo $key . " => " . $value . "\n" ;
                }
            }
        }
    }else
        echo "\nAucun historique pour le moment \n";
    
}



function bookSearch(){
    $books = getBooks()["books"];

    $field = "";
    while($field !== "nom" && $field !== "description" && $field !== "disponible" && $field !== "id"){
        echo "Entrez le champ par lequel vous voulez chercher votre livre (id, nom, description, disponible) : " ;
        $field = trim(readline());    
    }

    if ($field === "id"){
        $pronom = "l'id";
    }elseif ($field === "nom"){
        $pronom = "le nom";
    }elseif ($field === "description"){
        $pronom = "la description";
    }elseif ($field === "disponible"){
        $pronom = "la disponibilité";
    }

    echo "Entrez " . $pronom . " du livre : ";
    $entry = strtolower(trim(readline()));

    $booksSorted["books"] = quickSort($books, $field);
    $bookIndex = binarySearch($booksSorted["books"], $entry, $field);

    if ($bookIndex !== -1 && $field !== "disponible"){
        echo "Livre trouvé à l'index : ". $booksSorted["books"][$bookIndex]["id"] . "\n";

        foreach ($booksSorted["books"][$bookIndex] as $key => $value){
            echo "$key => $value \n";
        }    
    }elseif ($field === "disponible"){
        $booksAvailable = filterByAvailability($books, $field);
        foreach($booksAvailable as $book){
            foreach($book as $key => $value){
                echo "$key => $value\n";
            }
            echo "---------------------------------\n";
        }
    }
    else{
        echo "Livre non trouvé." ;
    }

    saveHistory(["Une recherche de livre a etait effectuer sur la colonne " . $field . " avec " . $entry . " comme entrer"]);

}

function bookSort(){

    $books = getBooks()["books"];

    $history = getBooks()["history"];
    

    if (count($books) <= 1) {
        echo "La liste ne possède " . (count($books) < 1 ? "aucun livre" : "qu'un livre") . "\n";
        return;
    }

    $field = "";
    while($field !== "nom" && $field !== "description" && $field !== "disponible"){
        echo "Entrez le champ par lequel vous voulez triez la liste (nom, description, disponible) : " ;
        $field = trim(readline());    
    }

    $order = "";
    while($order !== "croissant" && $order !== "décroissant"){
        echo "Dans quelle ordre ? (croissant, decroissant) : " ;
        $order = strtolower(trim(readline()));    
    }

    $booksSorted["books"] = trieBooks($books, $field, $order);

    $booksSorted["history"] = $history;

    saveBooks($booksSorted);

    saveHistory(["Un tri a etait effectuer sur la colonne '" . $field . "' de maniere " . $order]);

}



function bookShow(){
    $book = "";
    while (empty($book)){
        // Si $book est une array vide sa veut dire que la searchBook() n'a pas trouvé de livre dans la bdd
        // donc on prévient l'utilisteur que le nom n'est pas bon sa nous evite d'avoir un compteur ou autre :)
        echo (is_array($book)) ? "Nom du livre incorrect réessayer : " : "Entrez le nom du livre : ";
        $bookName = trim(readline());  
        $book = searchBook($bookName);
    }
    echo "\nVoici le livre que vous avez demander : \n";
    foreach ($book as $key => $value){
        echo "$key => $value \n";
    }
}

function allBooks(){
    $books = getBooks();
    
    foreach($books["books"] as $book){
        echo "\n";
        foreach($book as $key => $value){
            echo "$key => $value \n";
        }
        echo "\n";
    }

    saveHistory(["Affichage de la liste de tout les livres"]);
}


function bookDelete(){
    $book = "";
    while (empty($book)){
        // Si $book est une array vide sa veut dire que la searchBook() n'a pas trouvé de livre dans la bdd
        // donc on prévient l'utilisteur que le nom n'est pas bon sa nous evite d'avoir un compteur ou autre :)
        echo (is_array($book)) ? "Nom du livre incorrect réessayer : " : "Entrez le nom du livre : ";
        $bookName = trim(readline());  
        $book = searchBook($bookName);
    }

    $confirm = null;
    while($confirm !== "oui" && $confirm !== "non"){
        echo "Êtes-vous sûr(e) de vouloir supprimer ce livre ( oui ou non ) :  ";
        $confirm = trim(readline());
    }
    
    if ($confirm === "oui") {
        dropBook($bookName);
    }

    saveHistory(["Suppression du livre $bookName"]);
}


function bookModify(){
    $book = "";
    while (empty($book)){
        // Si $book est une array vide sa veut dire que la searchBook() n'a pas trouvé de livre dans la bdd
        // donc on prévient l'utilisteur que le nom n'est pas bon sa nous evite d'avoir un compteur ou autre :)
        echo (is_array($book)) ? "Nom du livre incorrect réessayer : " : "Entrez le nom du livre : ";
        $bookName = trim(readline());  
        $book = searchBook($bookName);
    }
    


    $field = "";
    while($field !== "nom" && $field !== "description" && $field !== "disponibilité"){
        echo "Entrez le champ que vous desirez modifier (nom, description, disponibilité) : " ;
        $field = trim(readline());    
    }

    echo "Vous pouvez maintenant mettre à jour " , ($field === "nom") ? "le " : "la " , $field , " : " ;
    $fieldUpdate = trim(readline());


    modifyField($bookName, $field, $fieldUpdate);

    saveHistory(["Modification du champ $field sur le livre : $bookName"]);

}


function bookCreate(){
    $books = getBooks();

    $id = (isset($books["books"])) ? count($books["books"]) + 1 : 1;

    echo "Entrez le nom du livre : ";
    $name = trim(readline());
    echo "Entrez le nom du description : ";
    $description = trim(readline());

    $dispo = "";
    while ($dispo !== "disponible" && $dispo !== "indisponible"){
        echo "Entrez la disponibilité (disponible ou indisponible) : ";
        $dispo = trim(readline());
    }
    
    $book = [
        "id" => $id,
        "nom" => $name, 
        "description" => $description, 
        "disponible" => $dispo
    ];
    
    addBook($book);

    saveHistory(["Creation du livre $name"]);
}


// fonction secondaire : addBook, modifyField, dropBook, searchBook, saveBooks

function addBook($newBook){
    $books = getBooks();

    $books["books"][] = $newBook;

    saveBooks($books);
}

function modifyField($bookName, $field, $fieldUpdate){
    $books = getBooks();

    foreach ($books["books"] as &$book) { // Notez le & pour modifier par référence
        if ($book["nom"] === $bookName){
            $book[$field] = $fieldUpdate;
        }
    }

    saveBooks($books);
}

function dropBook($bookName){
    $books = getBooks()["books"];

    $history = getBooks()["history"];

    for ($i = 0; $i < count($books); $i++){
        if ($books[$i]["nom"] === $bookName){
            $bookId = $i;
        }
    }

    array_splice($books, $bookId, 1);

    $toto["books"] = $books;
    $toto["history"] = $history;

    saveBooks($toto);
}

function getBooks(){
    $data = file_get_contents(FILEPATH);

    $books = json_decode($data, true);

    return (isset($books["books"])) ? $books: ["books" => []];
}

function searchBook($bookName){
    $books = getBooks()["books"];

    foreach ($books as $book) {
        if ($book["nom"] === $bookName) {
            return $book;
        }
    }

    return [];
}

function saveBooks($books){
    $books = json_encode($books, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    file_put_contents(FILEPATH, $books);
}




// Historique

function saveHistory($move) {

    $data = getBooks();

    // Ajouter la nouvelle entrée à l'historique existant
    if (empty($data["history"])) {
        $data["history"] = [];
    }

    $data["history"][] = $move;

    $jsonData = json_encode($data, JSON_PRETTY_PRINT);
    file_put_contents(FILEPATH, $jsonData);
}


function getHistory(){
    $data = getBooks();

    if (!isset($data["history"])){
        return false;
    }
    return $data["history"];
}


// Trie par disponibilité 

function filterByAvailability($books, $isAvailable){
    $filtered = [];
    foreach($books as $book){
        if ($book["disponible"] == $isAvailable){
            $filtered[] = $book;
        }
    }
    return $filtered;
}



// binary search


function quickSort($books, $key){
    if (count($books) < 2){
        return $books;
    }    
    
    
    $left = $right = $middle = [];
    $pivot_index = floor(count($books) / 2);
    $pivot_value = $books[$pivot_index][$key];

    foreach ($books as $index => $book){
        if ($book[$key] < $pivot_value){
            $left[] = $book;
        } elseif ($book[$key] > $pivot_value){
            $right[] = $book;
        }else {
            $middle[] = $book;
        }
    }

    return array_merge(quickSort($left, $key), $middle, quickSort($right, $key));
}



function binarySearch($books, $value, $key){
    $low = 0;
    $high = count($books) - 1;

    while ($low <= $high){
        $mid = floor(($low + $high) / 2);
        if ($books[$mid][$key] < $value){
            $low = $mid + 1;
        } elseif ($books[$mid][$key] > $value){
            $high = $mid - 1;
        } else {
            return $mid; // retourne l'index où le livre est trouvé
        }
    }

    return -1; // retourne -1 si le livre n'est pas trouvé 
}


// trie des livres 


function trieBooks($books, $field, $order) {
    if (count($books) <= 1) {
        // echo "blmsqdkfjsqdmlfkjmsqldkfjmsqldfjmlsqdfjmlsqdfj";
        // echo "<br>";
        // echo "<br>";
        return $books;
    }

    $middle = floor(count($books) / 2);
    $left = array_slice($books, 0, $middle);
    $right = array_slice($books, $middle);
    
    // echo "<pre>";
    // var_dump($left);
    // echo "<br>";
    // echo "<br>";

    // echo "lefttttttttttt";
    // echo "<br>";
    // echo "<br>";
    // var_dump($right);
    // echo "<br>";
    // echo "righhhhhhhhhht";
    // echo "<br>";
    // echo "<br>";


    // echo "bonjour";
    // echo "<br>";
    // echo "bonjour";
    // echo "<br>";
    // echo "bonjour";
    // echo "<br>";
    // echo "bonjour";
    // echo "<br>";
    // echo "bonjour";
    // echo "<br>";
    // echo "bonjour";
    // echo "<br>";
    // echo "<br>";
    // echo "<br>";

    $left = trieBooks($left, $field, $order);
    $right = trieBooks($right, $field, $order);

    // echo "bonjour";
    // echo "<br>";
    // echo "bonjour";
    // echo "<br>";
    // echo "bonjour";
    // echo "<br>";
    // echo "bonjour";
    // echo "<br>";
    // echo "bonjour";
    // echo "<br>";
    // echo "lolo";
    // echo "<br>";
    // echo "<br>";
    // echo "<br>";

    // var_dump($left);
    // var_dump($right);

    // echo "<br>";
    // echo "bonjour";
    // echo "<br>";
    // echo "bonjour";
    // echo "<br>";

    // var_dump(merge($left, $right, $field, $order));

    return merge($left, $right, $field, $order);
    
}

function merge($left, $right, $property, $order) {
    $result = [];
    $leftIndex = 0;
    $rightIndex = 0;

    while ($leftIndex < count($left) && $rightIndex < count($right)) {
        if ($order === "croissant") {
            if (strcasecmp((string)$left[$leftIndex][$property], (string)$right[$rightIndex][$property]) <= 0) {
                $result[] = $left[$leftIndex];
                $leftIndex++;
            } else {
                $result[] = $right[$rightIndex];
                $rightIndex++;
            }
        } else {
            if (strcasecmp((string)$left[$leftIndex][$property], (string)$right[$rightIndex][$property]) >= 0) {
                $result[] = $left[$leftIndex];
                $leftIndex++;
            } else {
                $result[] = $right[$rightIndex];
                $rightIndex++;
            }
        }
    }

    while ($leftIndex < count($left)) {
        $result[] = $left[$leftIndex];
        $leftIndex++;
    }

    while ($rightIndex < count($right)) {
        $result[] = $right[$rightIndex];
        $rightIndex++;
    }

    return $result;
}
