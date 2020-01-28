<?php
    # if invoice query string param doesn't exist, send back to index.php. For security
    if (!isset($_GET["invoice"])) {
        header('Location: index.php');
        exit();
    }
    # sqlite specifices which type of db we are trying to open
    # we pass the connection string to PDO. PDO is a class built into PHP.
    $pdo = new PDO ('sqlite:chinook.db') ;
    $sql = '
        SELECT 
            invoice_items.UnitPrice,
            tracks.Name as TrackName,
            albums.Title as AlbumTitle,
            artists.Name as ArtistName
        FROM invoice_items
        INNER JOIN tracks
        ON tracks.TrackId = invoice_items.TrackId
        INNER JOIN albums
        ON tracks.AlbumId = albums.AlbumId
        INNER JOIN artists
        ON albums.ArtistId = artists.ArtistId
        WHERE InvoiceId = ?
    ';

    $statement = $pdo->prepare($sql) ;
    $statement->bindParam(1, $_GET['invoice']);
    $statement->execute();
    $invoiceItems = $statement->fetchAll(PDO::FETCH_OBJ) ;

?>
<table>
    <thead>
        <th>Track</th>
        <th>Album</th>
        <th>Artist</th>
        <th>Price</th>
    </thead>
    <tbody>
        <?php foreach ($invoiceItems as $invoiceItem) : ?>
            <tr>
                <td><?php echo $invoiceItem->TrackName ?></td> 
                <td><?php echo $invoiceItem->AlbumTitle ?></td>
                <td><?php echo $invoiceItem->ArtistName ?></td> 
                <td><?php echo $invoiceItem->UnitPrice ?></td> 
            </tr>
        <?php endforeach ?>
    </tbody>
</table>


