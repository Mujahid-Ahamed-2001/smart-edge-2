<?php 

class Customer extends Dbh
{
    public function getOneCustomer($Cus_id)
    {
        try 
        {           
            $sql = "SELECT * FROM customers C INNER JOIN shop S ON C.shop_SHID = S.SHID WHERE C.CTID = ?";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute([$Cus_id]);
            return $stmt->fetchAll();
        }
        catch (PDOException $e) {
            die("Error: Unable to fetch Customer data " . $e->getMessage());
        }
    }//get one Customer
    public function getOneCustomerWholesale($Cus_id)
    {
        try 
        {           
            $sql = "SELECT * FROM customers WHERE CTID = ?";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute([$Cus_id]);
            return $stmt->fetchAll();
        }
        catch (PDOException $e) {
            die("Error: Unable to fetch Customer data " . $e->getMessage());
        }
    }//get one Customer

    public function getCustomers($shop_id,$multi,$company_id)
    {
        try 
        {
            $sql = "SELECT * FROM customers WHERE shop_SHID = '$shop_id';";
            if($multi==1)
            {
                $sql = "SELECT * FROM customers c
                INNER JOIN shop s ON s.SHID = c.shop_SHID                
                WHERE s.Company_CMID='$company_id';";
            }
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute();            
            $Customers = $stmt->fetchAll();
            return $Customers;
        } 
        catch (PDOException $e) {
            die("Error: Unable to fetch Customers. " . $e->getMessage());
        }
    }

    public function getCustomerNames() {
      
        $query = "SELECT CTID, CustName FROM customers ORDER BY CustName ASC"; // Replace 'customers' with your table name
        $result = $this->connect()->prepare($query);
        $customerNames = [];

        $result->execute();
        return $result->fetchAll();
       
    }

    public function getCustomerCount()
    {
        $sql = "SELECT max(CTID) as CusCount FROM customers;";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }//get Customer count

 

    public function setCustomer($CustomerNo, $CustName, $CustAddress, $CustContact, $MaxCreditAmount, $PaymentTerm, $CustStat, $shop_SHID, $DOB = null, $gender = null, $country_code = null, $email=null)
    {
        try
        {

            if ($DOB != null)
            {

                $sql = "INSERT INTO customers
                (
                    CustomerNo,
                    CustName,
                    CustAddress,
                    CustContact,
                    MaxCreditAmount,
                    PaymentTerm,
                    CustStat,
                    shop_SHID,
                    CustDOB,
                    CustGender,
                    country_code,
                    CustEmail
                )
                VALUES
                (
                    ?,?,?,?,?,?,?,?,?,?,?,?
                )";

                $params = [
                    $CustomerNo,
                    $CustName,
                    $CustAddress,
                    $CustContact,
                    $MaxCreditAmount,
                    $PaymentTerm,
                    $CustStat,
                    $shop_SHID,
                    $DOB,
                    $gender,
                    $country_code,
                    $email
                ];

            }
            else
            {

                $sql = "INSERT INTO customers
                (
                    CustomerNo,
                    CustName,
                    CustAddress,
                    CustContact,
                    MaxCreditAmount,
                    PaymentTerm,
                    CustStat,
                    shop_SHID,
                    CustGender,
                    country_code,
                    CustEmail
                )
                VALUES
                (
                    ?,?,?,?,?,?,?,?,?,?,?
                )";

                $params = [
                    $CustomerNo,
                    $CustName,
                    $CustAddress,
                    $CustContact,
                    $MaxCreditAmount,
                    $PaymentTerm,
                    $CustStat,
                    $shop_SHID,
                    $gender,
                    $country_code,
                    $email
                ];

            }

            $stmt = $this->connect()->prepare($sql);

            return $stmt->execute($params);

        }
        catch (PDOException $e)
        {

            die("Error: Unable to insert data to Customer: " . $e->getMessage());

        }
    }
    public function getlastCustomer()
    {
        try 
        {     
            $sql="SELECT * FROM customers ORDER BY CTID DESC LIMIT 1;";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute();   
            return $stmt->fetchAll();
        } 
        catch (PDOException $e) 
        {
            die("Error: Unable to fetch last Customer: " . $e->getMessage());
        }
        
    }//get last Customer
    public function getCustomerById($CTID)
    {
        try 
        {     
            $sql="SELECT * FROM customers WHERE CTID = ?;";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute([$CTID]);
            return $stmt->fetchAll();
        } 
        catch (PDOException $e) 
        {
            die("Error: Unable to fetch Customer by ID: " . $e->getMessage());
        }
        
    }//get Customer by ID

    public function updateCustomer($CustName, $cust_gender, $cust_dob, $CustAddress,$CustContact,$MaxCreditAmount, $PaymentTerm, $CustStat, $CusID, $country_code="+94", $CustEmail=null)
    {
        try 
        {
            $stmt = $this->connect()->prepare("UPDATE customers SET CustName =?, CustGender=?, CustDOB=?, CustAddress=?, CustContact=?, MaxCreditAmount=?, PaymentTerm=?, CustStat=?, country_code=?, CustEmail=? WHERE CTID=?;");           
            return $stmt->execute([$CustName, $cust_gender, $cust_dob, $CustAddress,$CustContact,$MaxCreditAmount, $PaymentTerm, $CustStat, $country_code, $CustEmail, $CusID]);
        } 
        catch (PDOException $e) 
        {
            die("Error: Unable to update the Customer" . $e->getMessage());
        }
    } //update Customer

    public function deleteCustomer($customer_id)
    {
        try 
        {
            $sql = "DELETE FROM `customers` WHERE CTID = ?;";
            $stmt = $this->connect()->prepare($sql);           
            $stmt->execute([$customer_id]);
        } 
        catch (PDOException $e) 
        {
            die("Error: Unable to update the Customer" . $e->getMessage());
        }
    } //delete Customer

}//class Customer