<?php
    # sqlite specifices which type of db we are trying to open
    # we pass the connection string to PDO. PDO is a class built into PHP.
    $pdo = new PDO ('sqlite:chinook.db') ;
    # sql is our sql query 
    $sql = '
    SELECT InvoiceId,
        InvoiceDate,
        Total,
        customers.FirstName as CustomerFirstName,
        customers.LastName as CustomerLastName
    FROM invoices
    INNER JOIN customers 
    ON customers.CustomerId = invoices.CustomerId' ;

    # check to see if query string param exists
    if (isset($_GET['search'])){
        #if exists add WHERE clause to sql statement
        #'?' is for a prepared statement (avoiding hacking)
        $sql = $sql . " WHERE customers.FirstName LIKE ?" ;
    }

    # pdo prepare returns our statement to be executed
    $statement = $pdo->prepare($sql);
    # check to see if query string param exists
    if (isset($_GET['search'])){
        $boundSearchParam = '%' . $_GET['search'] . '%' ;
        # add variable to sql statement. 1 means bind param to 1st '?' mark
        $statement->bindParam(1, $boundSearchParam) ;
    }
    # execute the sql statement
    $statement->execute() ;
    # to retreive the returned statement, FETCH_OBJ --> returns an array of objects:
    $invoices = $statement->fetchAll(PDO::FETCH_OBJ);
    # Alternative: 
    # $invoices = $statement->fetchAll();
    //var_dump($invoices) ;
?>
<form action="index.php" method="get">
    <input 
    type="text"
    name="search"
    placeholder="Search..."
    value="<?php echo isset($_GET['search']) ? $_GET['search'] : '' ?>">
    <button type="submit">
        Search
    </button>
    <a href="/">Clear</a>
</form>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Date</th>
            <th>Total</th>
            <th colspan="2">Customer Name</th>
        </tr>
    </thead>
    <tbody> 
        <?php foreach($invoices as $invoice) : // short tag loop?>
            <tr>
                <td>
                    <?php echo $invoice->InvoiceId ?>
                </td>
                <td>
                    <?php echo $invoice->InvoiceDate ?>
                </td>
                <td>
                    <?php echo $invoice->Total ?>
                </td>
                <td>
                    <?php echo $invoice->CustomerFirstName . " " . $invoice->CustomerLastName ?>
                </td>
                <td>
                    <a href = "invoice-details.php?invoice=<?php echo $invoice->InvoiceId ?>">View Invoice</a>
                </td>
            </tr>
        <?php endforeach // end short tag loop?>
        <?php if (count($invoices) === 0) :?>
            <tr>
                <td colspan="4">No results</td>
            </tr>
        <?php endif ?>
    </tbody>
</table