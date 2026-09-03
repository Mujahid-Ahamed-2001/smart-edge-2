<?php 

class GRN extends Dbh
{
    public function setGRNHeader($GRNHeaderNo, $EffectiveDate, $InvoiceNo, $ItemCount, $TotalPurchasePrice, $TotalSellPrice, $GRNStartTime, $GRNEndTime, $GRNStat, $user_USID, $shop_SHID, $Suppliers_SPID, $SuppPayment , $SuppBalance , $excessAmount,$Reference)
    {
        try 
        {
            $sql="INSERT INTO grnheader(GRNHeaderNo, EffectiveDate, InvoiceNo, ItemCount, TotalPurchasePrice, TotalSellPrice, GRNStartTime, GRNEndTime, GRNStat, user_USID, shop_SHID, Suppliers_SPID , SuppPayment , SuppBalance ,excessAmount,refference) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);";
            $stmt = $this->connect()->prepare($sql);
            $result = $stmt->execute([$GRNHeaderNo, $EffectiveDate, $InvoiceNo, $ItemCount, $TotalPurchasePrice, $TotalSellPrice, $GRNStartTime, $GRNEndTime, $GRNStat, $user_USID, $shop_SHID, $Suppliers_SPID, $SuppPayment , $SuppBalance , $excessAmount,$Reference]);
            return $result;
        }//try
        catch (PDOException $e)
        {
            die("Error: Unable to insert data: " . $e->getMessage());
        }//catch
    }//save category

    public function editGRNHeaderStat($grn_stat, $grn_header_id, $SuppPayment, $SuppBalance, $excessAmount, $PurchDiscType, $PurchDisc, $TotalDisc)
    {
        try 
        {
            $sql = "UPDATE grnheader 
                    SET GRNStat=?, SuppPayment=?, SuppBalance=?, excessAmount=?, PurchDiscType=?, PurchDisc=?, TotalDisc=? 
                    WHERE GHID=?;";
            
            $stmt = $this->connect()->prepare($sql);
            
            // ✅ Corrected the parameter order
            $stmt->execute([$grn_stat, $SuppPayment, $SuppBalance, $excessAmount, $PurchDiscType, $PurchDisc, $TotalDisc, $grn_header_id]);           
            
            // echo "SaleDiscountType: " . htmlspecialchars($PurchDiscType) . 
            //     " SaleDiscount: " . htmlspecialchars($PurchDisc) . 
            //     " SaleDiscountTotal: " . htmlspecialchars($TotalDisc);

        } // try
        catch (PDOException $e)
        {
            die("Error: Unable to update data: " . $e->getMessage()); // ✅ Fixed error message
        } // catch
    } // editGRNHeaderStat


    Public function grnEditSupplier($grnid,$supplier_id)
    {
        try 
        {
            $sql="UPDATE grnheader SET Suppliers_SPID=? WHERE GHID=?;";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute([$supplier_id , $grnid]);
        }//try
        catch (PDOException $e)
        {
            die("Error: Unable to insert data: " . $e->getMessage());
        }//catch
    }

    public function editGRNHeaderVerify($effective_date, $item_count, $TotalPurchasePrice, $TotalOrignalPurchasePrice, $TotalSellPrice, $grn_header_id)
    {
        try 
        {
            $sql="UPDATE grnheader SET EffectiveDate=?, ItemCount=?, TotalPurchasePrice=?, TotalOriginalPurchase =?, TotalSellPrice=?, GRNStat=2 WHERE GHID=?;";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute([$effective_date, $item_count, $TotalPurchasePrice, $TotalOrignalPurchasePrice, $TotalSellPrice, $grn_header_id]);
        }//try
        catch (PDOException $e)
        {
            die("Error: Unable to insert data: " . $e->getMessage());
        }//catch
    }//edit grn header stat

    public function getGRNCount($shop_id="")
    {
        try{
            $sql = "SELECT count(GHID) AS GRNCount FROM grnheader ;";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        }
        catch(PDOException $e)
        {
            die("Error: Unable to read from table " . $e->getMessage());
        }
    }//get category count
    public function getGHID()
    {
        try{
            $sql = "SELECT GHID, GRNHeaderNo FROM grnheader ORDER BY GHID DESC LIMIT 1;";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute();
            return $row = $stmt->fetch();
        }
        catch(PDOException $e)
        {
            die("Error: Unable to read from table " . $e->getMessage());
        }
    }//get category count

    public function selectShop($userType,$user_id)
    {
        try
        {  
            if($userType==1)
            {
                $sql="SELECT * FROM `shop`";
            }  
            else  
            {
                $sql="SELECT * FROM shop s
                INNER JOIN shopusers su ON su.shop_SHID=s.SHID
                WHERE su.user_USID='$user_id'
                ";
            }      
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } 

        catch (PDOException $e) 
        {
            die("Error: Unable to insert data to Supplier: " . $e->getMessage());
        }
    }
    public function getAllGRNHeader($shop_id, $start = null, $end = null, $supplierID = null)
    {
        try{
            $stmt = " 1=1 "; // ✅ Initialize with a default condition
            if(!empty($start) && !empty($end))
            {
                $stmt .= " AND gh.EffectiveDate BETWEEN '$start' AND '$end' ";
            }
            if(!empty($supplierID))
            {
                $stmt .= " AND gh.Suppliers_SPID = '$supplierID' ";
            }
            if($shop_id == "all")
            {
                $stmt .= "";
            }
            else
            {
                $stmt .= " AND gh.shop_SHID = '$shop_id' ";
            }
            $sql = "SELECT 
                        gh.GHID, 
                        gh.GRNHeaderNo, 
                        gh.EffectiveDate, 
                        gh.InvoiceNo, 
                        SUM(gd.TotalPurchasePrice) AS TotalPurchasePrice, 
                        SUM(gd.TotalSellPrice) AS TotalSellPrice, 
                        gh.refference, 
                        gh.GRNStat, 
                        s.SupplierName,
                        u.UserName,
                        CONCAT(ss.ShopName, ' - ', ss.ShopNo) AS ShopName,
                        COUNT(gd.GDID) AS LineCount
                    FROM grnheader gh
                    LEFT JOIN grndetails gd 
                        ON gh.GHID = gd.GRNHeader_GHID
                    LEFT JOIN user u
                        ON u.USID = gh.user_USID
                    LEFT JOIN shop ss
                        ON ss.SHID = gh.shop_SHID
                    LEFT JOIN suppliers s
                        ON s.SPID = gh.Suppliers_SPID
                    WHERE 
                    $stmt
                    GROUP BY gh.GHID
                    ORDER BY gh.GHID DESC;";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        }
        catch(PDOException $e)
        {
            die("Error: Unable to read from table " . $e->getMessage());
        }
    }//get category count

    public function getOneGRNHeader($grn_header_id)
    {
        try{
            $sql = "SELECT * FROM grnheader 
            INNER JOIN user ON user.USID = grnheader.user_USID
            INNER JOIN suppliers ON suppliers.SPID = grnheader.Suppliers_SPID
            WHERE GHID = ?;";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute([$grn_header_id]);
            return $stmt->fetchAll();
        }
        catch(PDOException $e)
        {
            die("Error: Unable to read from table " . $e->getMessage());
        }
    }//get category count

    //=====================================================================================//
    //==================================== GRN Details ====================================//
    //=====================================================================================//
    public function check_grn_status($grn_header_id)
    {
        $sql="SELECT GRNStat FROM grnheader WHERE GHID=?";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([$grn_header_id]);
        return $stmt->fetchAll();
    }
    public function setGRNDetails($InitQty, $CurrentQty, $UnitPurchasePrice, $UnitLabelPrice, $UnitSellPrice, $TotalPurchasePrice, $TotalSellPrice, $MnfDate, $ExpDate, $GRNStat, $VariationID, $products_PDID, $GRNHeader_GHID, $Rack_RKID)
    {
        try 
        {
            $sql="INSERT INTO grndetails(InitQty, CurrentQty, UnitPurchasePrice, UnitLabelPrice, UnitSellPrice, TotalPurchasePrice, TotalSellPrice, MnfDate, ExpDate, GRNStat, VariationID, products_PDID, GRNHeader_GHID, Rack_RKID) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?);";
            $stmt = $this->connect()->prepare($sql);
            $query=$stmt->execute([$InitQty, $CurrentQty, $UnitPurchasePrice, $UnitLabelPrice, $UnitSellPrice, $TotalPurchasePrice, $TotalSellPrice, $MnfDate, $ExpDate, $GRNStat, $VariationID, $products_PDID, $GRNHeader_GHID, $Rack_RKID]);
            
        }//try
        catch (PDOException $e)
        {
            die("Error: Unable to insert data: " . $e->getMessage());
        }//catch
        if($query)
        {
            return 1;
        }
        else
        {
            return 0;
        }
    }//save category

    public function editGRNDetails($InitQty, $CurrentQty, $UnitPurchasePrice, $UnitLabelPrice, $UnitSellPrice, $TotalPurchasePrice, $TotalSellPrice, $MnfDate, $ExpDate, $VariationID, $products_PDID, $Rack_RKID, $grn_detail_id)
    {
        try 
        {
            $sql="UPDATE grndetails SET InitQty=?, CurrentQty=?, UnitPurchasePrice=?, UnitLabelPrice=?, UnitSellPrice=?, TotalPurchasePrice=?, TotalSellPrice=?, MnfDate=?, ExpDate=?, VariationID=?, products_PDID=?, Rack_RKID=? WHERE GDID=?;";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute([$InitQty, $CurrentQty, $UnitPurchasePrice, $UnitLabelPrice, $UnitSellPrice, $TotalPurchasePrice, $TotalSellPrice, $MnfDate, $ExpDate, $VariationID, $products_PDID, $Rack_RKID, $grn_detail_id]);
        }//try
        catch (PDOException $e)
        {
            die("Error: Unable to insert data: " . $e->getMessage());
        }//catch
    }//edit category

    public function editGRNDetailStat($grn_stat, $grn_header_id)
    {
        try 
        {
            $sql="UPDATE grndetails SET GRNStat=? WHERE GRNHeader_GHID = ?;";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute([$grn_stat, $grn_header_id]);
        }//try
        catch (PDOException $e)
        {
            die("Error: Unable to insert data: " . $e->getMessage());
        }//catch
    }//edit grn header stat

    public function deleteGRNDetails($grn_detail_id)
    {
        try 
        {
            $sql="DELETE FROM grndetails WHERE GDID = ?;";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute([$grn_detail_id]);
        }//try
        catch (PDOException $e)
        {
            die("Error: Unable to insert data: " . $e->getMessage());
        }//catch
    }//edit category

    public function setCreditDebitSupplier($EffectiveDate,$amount,$date,$invoice_header_id,$payment,$SupplierID,$user)
    {
        try 
        {
            $shop_SHID = $_SESSION['shop_id'];
            $sql="INSERT INTO `creditsupplier`(`EffectiveDate`, `DebitAmount`, `Balance`, `SubmitDate`, `invoice_header_id`, `pay_m_id`,  `Supplier_ID`, `user_USID`,`shop_SHID`) VALUES ('$EffectiveDate','$amount','$amount','$date','$invoice_header_id','$payment','$SupplierID','$user','$shop_SHID');";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute();
        }//try 
        catch (PDOException $e) 
        {
            die("Error: Unable to insert data: " . $e->getMessage());
        }//catch
    }


}//class GRN