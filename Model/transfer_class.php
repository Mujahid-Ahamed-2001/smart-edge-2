<?php

class Transfer extends Dbh
{
    public function setTransfer($TransferNo, $EffectiveDate, $TransferFrom, $TransferTo, $TransferTotalCount, $TransferTotalAmount, $TransferStat, $shop_SHID, $user_USID)
    {
        try 
        {
            $sql="INSERT INTO transferheader(TransferNo, EffectiveDate, TransferFrom, TransferTo, TransferTotalCount, TransferTotalAmount, TransferStat, shop_SHID, user_USID) VALUES(?,?,?,?,?,?,?,?,?);";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute([$TransferNo, $EffectiveDate, $TransferFrom, $TransferTo, $TransferTotalCount, $TransferTotalAmount, $TransferStat, $shop_SHID, $user_USID]);
        }//try 
        catch (PDOException $e) 
        {
            die("Error: Unable to insert data: " . $e->getMessage());
        }//catch
    }//set transfer header

    public function checktransferID($INID,$transferID)
    {
        try{
            $sql = "SELECT count(*) AS transferdetailsCount FROM transferdetails WHERE InventoryID = '$INID' AND TransferHeader_THID='$transferID';";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute();
            $row=$stmt->fetchAll();
            if($row[0]["transferdetailsCount"]>0)
            {
                return false;
            }
            return true;
        }
        catch(PDOException $e)
        {
            die("Error: Unable to read from table " . $e->getMessage());
        }
    }

    public function editTransferHeaderStat($transfer_header_stat, $transfer_header_id)
    {
        try 
        {
            $sql="UPDATE transferheader SET TransferStat = ? WHERE THID = ?;";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute([$transfer_header_stat, $transfer_header_id]);
        }//try 
        catch (PDOException $e) 
        {
            die("Error: Unable to insert data: " . $e->getMessage());
        }//catch
    }//edit transfer header

    public function editTransferTotals($transfer_date, $row_count, $transfer_amount, $transfer_header_id)
    {
        try 
        {
            $sql="UPDATE transferheader SET EffectiveDate=?, TransferTotalCount =?, TransferTotalAmount=? WHERE THID=?;";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute([$transfer_date, $row_count, $transfer_amount, $transfer_header_id]);
        }//try 
        catch (PDOException $e) 
        {
            die("Error: Unable to insert data: " . $e->getMessage());
        }//catch
    }//edit transfer header totals

    public function getTransferMax($shop_id)
    {
        try{
            $sql = "SELECT max(THID) as maxTransfer FROM transferheader WHERE shop_SHID = ?;";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute([$shop_id]);
            return $stmt->fetchAll();
        }
        catch(PDOException $e)
        {
            die("Error: Unable to read from table " . $e->getMessage());
        }
    }//get company type

    public function getAllTransfer($shop_id)
    {
        try{
            $sql = "SELECT * FROM transferheader 
            INNER JOIN user ON user.USID = transferheader.user_USID
            WHERE shop_SHID = ? ORDER BY THID DESC;";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute([$shop_id]);
            return $stmt->fetchAll();
        }
        catch(PDOException $e)
        {
            die("Error: Unable to read from table " . $e->getMessage());
        }
    }//get company type

    public function getOneTransferHeader($transfer_header_id)
    {
        try{
            $sql = "SELECT * FROM transferheader 
            INNER JOIN user ON user.USID = transferheader.user_USID
            WHERE THID = ?;";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute([$transfer_header_id]);
            return $stmt->fetchAll();
        }
        catch(PDOException $e)
        {
            die("Error: Unable to read from table " . $e->getMessage());
        }
    }//get one transfer header

//===============================================================================================//
//======================================= Transfer Detail =======================================//
//===============================================================================================//

    public function setTransferDetail($TransferQty, $ReceivedQty, $UnitPurchasePrice, $UnitSellingPrice, $MnfDate, $ExpDate, $TransferTotalAmount, $InventoryID, $products_PDID, $VariationID, $RackID, $TransferStat, $TransferHeader_THID, $Batch_ID)
    {
        try 
        {
            $sql="INSERT INTO transferdetails(TransferQty, ReceivedQty, UnitPurchasePrice, UnitSellingPrice, MnfDate, ExpDate, TransferTotalAmount, InventoryID, products_PDID, VariationID, RackID, TransferStat, TransferHeader_THID, Batch_ID) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?);";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute([$TransferQty, $ReceivedQty, $UnitPurchasePrice, $UnitSellingPrice, $MnfDate, $ExpDate, $TransferTotalAmount, $InventoryID, $products_PDID, $VariationID, $RackID, $TransferStat, $TransferHeader_THID, $Batch_ID]);
        }//try
        catch (PDOException $e)
        {
            die("Error: Unable to insert data: " . $e->getMessage());
        }//catch
    }//set category

    public function editTransferDetail($TransferQty, $ReceivedQty, $UnitPurchasePrice, $UnitSellingPrice, $MnfDate, $ExpDate, $TransferTotalAmount, $InventoryID, $products_PDID, $VariationID, $RackID, $Batch_ID, $transfer_detail_id)
    {
        try 
        {
            $sql="UPDATE transferdetails SET TransferQty=?, ReceivedQty=?, UnitPurchasePrice=?, UnitSellingPrice=?, MnfDate=?, ExpDate=?, TransferTotalAmount=?, InventoryID=?, products_PDID=?, VariationID=?, RackID=?, Batch_ID=? WHERE TDID=?;";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute([$TransferQty, $ReceivedQty, $UnitPurchasePrice, $UnitSellingPrice, $MnfDate, $ExpDate, $TransferTotalAmount, $InventoryID, $products_PDID, $VariationID, $RackID, $Batch_ID, $transfer_detail_id]);
        }//try 
        catch (PDOException $e) 
        {
            die("Error: Unable to insert data: " . $e->getMessage());
        }//catch
    }//edit transfer detail

    public function editTransferDetailStat($transfer_detail_stat, $transfer_detail_id)
    {
        try 
        {
            $sql="UPDATE transferdetails SET TransferStat=? WHERE TransferHeader_THID=?;";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute([$transfer_detail_stat, $transfer_detail_id]);
        }//try 
        catch (PDOException $e) 
        {
            die("Error: Unable to insert data: " . $e->getMessage());
        }//catch
    }//edit transfer header

    public function deleteTransferDetail($transfer_detail_id)
    {
        try 
        {
            $sql="DELETE FROM transferdetails WHERE TDID = ?;";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute([$transfer_detail_id]);
        }//try 
        catch (PDOException $e) 
        {
            die("Error: Unable to insert data: " . $e->getMessage());
        }//catch
    }//edit transfer detail

    public function getTransferDetailByHeader($transfer_header_id)
    {
        try
        {
            $sql = "SELECT * FROM transferdetails WHERE TransferHeader_THID = ?;";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute([$transfer_header_id]);
            return $stmt->fetchAll();
        }
        catch(PDOException $e)
        {
            die("Error: Unable to read from table " . $e->getMessage());
        }
    }//get transfer detail by header

//===============================================================================================//
//==================================== Transfer Transaction =====================================//
//===============================================================================================//
    public function setTransferTransaction($TrnTransactionAmount, $TrnTransactionStat, $transfer_header_id, $paymethod_id)
    {
        try 
        {
            $sql="INSERT INTO transfertransactions(TrnTransactionAmount, TrnTransactionStat, transfer_header_id, paymethod_id) VALUES (?,?,?,?);";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute([$TrnTransactionAmount, $TrnTransactionStat, $transfer_header_id, $paymethod_id]);
        }//try
        catch (PDOException $e)
        {
            die("Error: Unable to insert data: " . $e->getMessage());
        }//catch
    }//set category

}//class transfer