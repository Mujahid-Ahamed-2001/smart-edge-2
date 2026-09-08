<?php 

class DBTransactions extends Dbh
{
    //for Select functions   
    public function getData($sql)
    {               
        try
        {
            $stmt = $this->connect()->prepare($sql);           
            $stmt->execute();
            return $stmt->fetchAll();
        }
        catch(PDOException $e)
        {
            die("Error: Unable to read from table " . $e->getMessage());
        }   
    }
    public function getSequence($num) 
    {
        return sprintf("%'.06d", $num);
    }
    public function updateData( string $table, array $data, array $where ): int 
    {
        if (empty($data)) {
            throw new InvalidArgumentException(
                "Update data cannot be empty."
            );
        }

        /*
        * A WHERE condition is mandatory to prevent accidentally
        * updating every row in the table.
        */
        if (empty($where)) {
            throw new InvalidArgumentException(
                "Update conditions cannot be empty."
            );
        }

        /*
        * Table and column names cannot be parameterized,
        * so validate them before adding them to the SQL.
        */
        if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $table)) {
            throw new InvalidArgumentException(
                "Invalid table name."
            );
        }

        $setParts   = [];
        $whereParts = [];
        $parameters = [];

        foreach ($data as $column => $value) {
            if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $column)) {
                throw new InvalidArgumentException(
                    "Invalid update column: {$column}"
                );
            }

            $placeholder = ":set_{$column}";

            $setParts[] = "`{$column}` = {$placeholder}";
            $parameters[$placeholder] = $value;
        }

        foreach ($where as $column => $value) {
            if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $column)) {
                throw new InvalidArgumentException(
                    "Invalid condition column: {$column}"
                );
            }

            /*
            * Handle NULL conditions correctly.
            */
            if ($value === null) {
                $whereParts[] = "`{$column}` IS NULL";
                continue;
            }

            $placeholder = ":where_{$column}";

            $whereParts[] = "`{$column}` = {$placeholder}";
            $parameters[$placeholder] = $value;
        }

        $sql = sprintf(
            "UPDATE `%s` SET %s WHERE %s",
            $table,
            implode(", ", $setParts),
            implode(" AND ", $whereParts)
        );

        try {
            $pdo = $this->connect();
            $stmt = $pdo->prepare($sql);
            $stmt->execute($parameters);

            return $stmt->rowCount();
        } catch (PDOException $e) {
            throw new PDOException(
                "Unable to update data in the requested table.",
                (int) $e->getCode(),
                $e
            );
        }
    }
    /**
     * Insert a row and return its auto-increment ID.
     *
     * Example:
     * $id = $dbObj->insertAndGetId('products', [
     *     'ItemName' => 'Test Product',
     *     'Barcode'  => '10001',
     *     'status'   => 1
     * ]);
     *
     * @throws InvalidArgumentException
     * @throws PDOException
     */
    public function insertAndGetId(string $table, array $data): int
    {
        if (empty($data)) {
            throw new InvalidArgumentException(
                'Insert data cannot be empty.'
            );
        }

        /*
        * Table and column names cannot be bound as PDO parameters.
        * Therefore, only safe SQL identifier characters are allowed.
        */
        if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $table)) {
            throw new InvalidArgumentException(
                'Invalid table name.'
            );
        }

        foreach (array_keys($data) as $column) {
            if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $column)) {
                throw new InvalidArgumentException(
                    "Invalid column name: {$column}"
                );
            }
        }

        $columns = [];
        $placeholders = [];
        $parameters = [];

        foreach ($data as $column => $value) {
            $columns[] = "`{$column}`";

            $placeholder = ":{$column}";
            $placeholders[] = $placeholder;
            $parameters[$placeholder] = $value;
        }

        $sql = sprintf(
            "INSERT INTO `%s` (%s) VALUES (%s)",
            $table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        try {
            $pdo = $this->connect();
            $stmt = $pdo->prepare($sql);
            $stmt->execute($parameters);

            return (int) $pdo->lastInsertId();
        } catch (PDOException $e) {
            /*
            * Let the calling file decide whether to roll back,
            * log the error, or return a JSON response.
            */
            throw new PDOException(
                'Unable to insert data into '.$table.' .',
                (int) $e->getCode(),
                $e
            );
        }
    }
    public function getMultipleData($sql, $data)
    {               
        try
        {
            $stmt = $this->connect()->prepare($sql);
            foreach ($data as $key => $value) {
                $stmt->bindParam($key + 1, $data[$key]); 
            }
            
            $stmt->execute($data);
            return $stmt->fetchAll();
        }
        catch(PDOException $e)
        {
            die("Error: Unable to read from table " . $e->getMessage());
        }   
    }

    public function getColumnWithData($sql, $data)
    {               
        try
        {
            $stmt = $this->connect()->prepare($sql);
            foreach ($data as $key => $value) {
                $stmt->bindParam($key + 1, $data[$key]); 
            }
            
            $stmt->execute($data);
            return $stmt->fetchColumn();
        }
        catch(PDOException $e)
        {
            die("Error: Unable to read from table " . $e->getMessage());
        }   
    }

    public function getDataWithBoolean($sql,$data)
    {   
        try 
        {
            $stmt = $this->connect()->prepare($sql);
            
            // Bind parameters dynamically
            foreach ($data as $key => $value) {
                $stmt->bindParam($key + 1, $data[$key]); 
            }
            
            if ($stmt->execute($data)) 
            {
                return true;
            } 
            else 
            {
                return false;
            }
        } 
        catch (PDOException $e) 
        {
            die("Error: Unable to read data: " . $e->getMessage());
        }
    }

    //For Insert,update,delete functions
    public function executeTransaction($sql)
    {
        try 
        {
            $stmt = $this->connect()->prepare($sql);                    
            $stmt->execute();
            return true;
        } 
        catch (PDOException $e) 
        {
            die("Error: Unable to insert data: " . $e->getMessage());
        }
    }

    public function executeTransactionWithArray($sql, $data)
    {
        try 
        {
            $stmt = $this->connect()->prepare($sql);
                        
            foreach ($data as $key => $value) {
                $stmt->bindParam($key + 1, $data[$key]); 
            }
            
            $stmt->execute($data);
        } 
        catch (PDOException $e) 
        {
            die("Error: Unable to insert data: " . $e->getMessage());
        }
    }

    public function executeTransactionAndReturnLastInsertID($sql, $data)
    {
        try
        {
            $pdo = $this->connect();

            $stmt = $pdo->prepare($sql);
            $stmt->execute($data);

            return $pdo->lastInsertId();
        }
        catch (PDOException $e)
        {
            die("Error: Unable to insert data: " . $e->getMessage());
        }
    }

     // Begin transaction
    public function beginTransaction()
    {
        try
        {
            $this->connect()->beginTransaction();
            echo "Transaction started\n";
        }
        catch(PDOException $e)
        {
            die("Error: Unable to begin transaction: " . $e->getMessage());
        }
    }

    // Insert data into a table
    public function insertData($table, $data)
    {
        try
        {
            // Construct column names and placeholders
            $columns = implode(", ", array_keys($data));
            $placeholders = ":" . implode(", :", array_keys($data));

            // Prepare the SQL query
            $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
            $stmt = $this->connect()->prepare($sql);

            // Bind values
            foreach ($data as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }

            // Execute the query
            $stmt->execute();
            echo "Data inserted successfully\n";
        }
        catch(PDOException $e)
        {
            die("Error: Unable to insert data: " . $e->getMessage());
        }
    }

    // Commit transaction
    public function commitTransaction()
    {
        try
        {
            $this->connect()->commit();
            echo "Transaction committed\n";
        }
        catch(PDOException $e)
        {
            die("Error: Unable to commit transaction: " . $e->getMessage());
        }
    }

    // Rollback transaction
    public function rollbackTransaction()
    {
        try
        {
            $this->connect()->rollBack();
            echo "Transaction rolled back\n";
        }
        catch(PDOException $e)
        {
            die("Error: Unable to rollback transaction: " . $e->getMessage());
        }
    }
}