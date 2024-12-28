<?php
$conn = new mysqli("localhost", "root", "", "dizifilm");

$etkinlikler = [
    ['tiyatro', 'Hamlet', '../images/hamlet.jpeg'],
    ['tiyatro', 'Othello', '../images/othello.jpg'],
    ['tiyatro', 'Macbeth', '../images/macbeth.jpg'],
    ['tiyatro', 'A Midsummer Night\'s Dream', '../images/midsummer.jpg'],
    ['tiyatro', 'King Lear', '../images/king_lear.jpeg'],
    ['tiyatro', 'The Cherry Orchard', '../images/cherry_orchard.jpg'],
    ['tiyatro', 'Death of a Salesman', '../images/salesman.jpg'],
    ['tiyatro', 'Waiting for Godot', '../images/godot.jpeg'],
    ['tiyatro', 'A Streetcar Named Desire', '../images/streetcar.jpg'],
    ['tiyatro', 'The Crucible', '../images/crucible.jpg'],
    ['film', 'Inception', '../images/inception.png'],
    ['film', 'The Dark Knight', '../images/dark_knight.jpeg'],
    ['film', 'Parasite', '../images/parasite.jpg'],
    ['film', 'Interstellar', '../images/interstellar.jpeg'],
    ['film', 'The Godfather', '../images/godfather.jpeg'],
    ['film', 'Pulp Fiction', '../images/pulp_fiction.jpeg'],
    ['film', 'Schindler\'s List', '../images/schindlers_list.jpeg'],
    ['film', 'Fight Club', '../images/fight_club.jpeg'],
    ['film', 'Forrest Gump', '../images/forrest_gump.jpeg'],
    ['film', 'The Shawshank Redemption', '../images/shawshank.jpeg']
];

foreach ($etkinlikler as $etkinlik) {
    $sql = "INSERT INTO etkinlikler (tur, isim, resim_yolu) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $etkinlik[0], $etkinlik[1], $etkinlik[2]);
    $stmt->execute();
}

echo "Etkinlikler eklendi.";

?>
