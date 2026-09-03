<?php
class Report extends Dbh
{
    public function Inventory($has_minus)
    {
        $shop_id=$_SESSION["shop_id"];
        if ($has_minus == 1)
        {
            $sql="SELECT  P.ItemName,sum(INV.CurrentQty) AS CurrentQty,INV.BillQty,PH.SellingPrice,PH.PurchasePrice,(PH.SellingPrice * INV.BillQty) AS StockSale ,(PH.PurchasePrice * INV.CurrentQty) AS StockValue, INV.products_PDID FROM inventory INV 
                INNER JOIN products P ON INV.products_PDID = P.PDID 
                LEFT JOIN pricehistory PH ON INV.INID = PH.Inventory_INID  
                WHERE INV.shop_SHID='$shop_id' 
                    GROUP BY INV.products_PDID";
        }
        else
        {
            $sql="SELECT  P.ItemName,sum(INV.CurrentQty) AS CurrentQty,INV.BillQty,PH.SellingPrice,PH.PurchasePrice,(PH.SellingPrice * INV.BillQty) AS StockSale ,(PH.PurchasePrice * INV.CurrentQty) AS StockValue, INV.products_PDID FROM inventory INV 
                INNER JOIN products P ON INV.products_PDID = P.PDID 
                LEFT JOIN pricehistory PH ON INV.INID = PH.Inventory_INID  
                WHERE INV.shop_SHID='$shop_id' AND INV.CurrentQty >=0
                GROUP BY INV.products_PDID";
        }
        try {
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            die("Error: Unable to insert data to Supplier: " . $e->getMessage());
        }
    }//Inventory

    public function cheque($shop_id, $status)
    {
        try {
            $sql = "SELECT * FROM `custcheq` cc
                    INNER JOIN `custchqdetail` ccd ON ccd.CCDID = cc.CCQID
                    INNER JOIN user u ON u.USID=cc.user_USID
                    INNER JOIN customers c ON c.CTID=cc.cust_CTID
                    LEFT JOIN invoiceheader ih ON ih.IHID=cc.invoiceID
                    WHERE cc.shop_SHID = '$shop_id' AND cc.chq_stat = '$status'";
    
                    $stmt = $this->connect()->prepare($sql);
                    $stmt->execute();
                    return $stmt->fetchAll();
                } catch (PDOException $e) {
                    die("Error: Unable to fetch cheque data: " . $e->getMessage());
                }
            }
            public function suppliercheque($shop_id,$status)
            {
                try {
                    $sql = "SELECT * FROM supcheq se
                        INNER JOIN supchqdetail sd ON sd.SCDID = se.SCQID
                        INNER JOIN user u ON u.USID=se.user_USID
                        LEFT JOIN grnheader g ON g.GHID= se.GRNHeader_GHID
                        WHERE se.shop_SHID = '$shop_id'  AND se.chq_stat='$status';";
                            
                    $stmt = $this->connect()->prepare($sql);
                    $stmt->execute();
                    return $stmt->fetchAll();
                } catch (PDOException $e) {
                    die("Error: Unable to fetch cheque data: " . $e->getMessage());
         }
    } 

    function  transfercheque($shop_id)
    {
        try
        {
            $sql = "SELECT *, se.chq_no AS schq_no, cc.chq_no AS cchq_no, sd.chqAmount AS schqAmount, sd.chqDate AS schqDate,sd.chqDate AS RealizeDate, sd.chqNo AS sdchqNo FROM supcheq se
                        INNER JOIN supchqdetail sd ON sd.SCQID = se.SCQID
                        INNER JOIN suppliers s ON se.sup_SPID = s.SPID
                        INNER JOIN user u ON u.USID=se.user_USID
                        INNER JOIN custcheq cc ON cc.CCQID=se.transferedFrom
                        INNER JOIN customers c ON c.CTID=cc.cust_CTID
                        LEFT JOIN grnheader g ON g.GHID= se.GRNHeader_GHID
						WHERE se.shop_SHID=$shop_id AND sd.chqDate != '0000-00-00'";
                        $stmt = $this->connect()->prepare($sql);
                        $stmt->execute();
                        return $stmt->fetchAll();

                    } catch(PDOException $e)
                    {
                    die("Error: Unable to fetch cheque data: " . $e->getMessage());
        }
    }
        
    public function sales($shop_id)
    {
        try {
            $sql = "SELECT ih.*, sm.SalesmansName as SalesPerson, u.UserName AS cashier FROM `invoiceheader` ih 
                    left join salesmans sm ON ih.Salesmans_SLID = sm.SLID
                    left join user u ON ih.user_USID=u.USID
                    WHERE ih.shop_SHID=? && ih.InvStat = 1;";

                    $stmt = $this->connect()->prepare($sql);
                    $stmt->execute([$shop_id,]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            die("Error: Unable to insert data to Supplier: " . $e->getMessage());
        }
    }
    public function salesdate($shop_id,$start,$end)
    {
        try {
            $sql = "SELECT ih.*, sm.SalesmansName as SalesPerson, u.UserName AS cashier FROM `invoiceheader` ih 
                    left join salesmans sm ON ih.Salesmans_SLID = sm.SLID
                    left join user u ON ih.user_USID=u.USID
                    WHERE ih.shop_SHID=? && ih.InvStat = 1 && ih.EffectiveDate BETWEEN '$start' AND '$end';";

            $stmt = $this->connect()->prepare($sql);
            $stmt->execute([$shop_id,]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            die("Error: Unable to insert data to Supplier: " . $e->getMessage());
        }
    }
    public function supplierdetail($shop_id)
    {

        try {
            $sql = "SELECT ih.*, sm.SalesmansName as SalesPerson, u.UserName AS cashier FROM `invoiceheader` ih 
                    left join salesmans sm ON ih.Salesmans_SLID = sm.SLID
                    left join user u ON ih.user_USID=u.USID
                    WHERE ih.shop_SHID=? && ih.InvStat = 1";

            $stmt = $this->connect()->prepare($sql);
            $stmt->execute([$shop_id,]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            die("Error: Unable to insert data to Supplier: " . $e->getMessage());
        }
    }
    public function subcategories($shop_id)
    {

        try {
            $sql = "SELECT *, COUNT(p.PDID) AS pro_count FROM subcategories sc
                    INNER JOIN categories c ON c.CTID = sc.categories_CTID
                    LEFT JOIN products p ON p.Subcategories_SCID = sc.SCID
                    WHERE p.shop_SHID = ? GROUP BY sc.SCID";      
                    $stmt = $this->connect()->prepare($sql);
                    $stmt->execute([$shop_id]);
                    return $stmt->fetchAll();
        } catch (PDOException $e) {
            die("Error: Unable to insert data to Supplier: " . $e->getMessage());
        }
    }
    public function subcategory($shop_id)
    {

        try {
            $sql = "SELECT ih.*, sm.SalesmansName as SalesPerson FROM `invoiceheader` ih 
                    left join salesmans sm ON ih.Salesmans_SLID = sm.SLID
                    WHERE ih.shop_SHID=? && ih.InvStat = 1";
                                
                        $stmt = $this->connect()->prepare($sql);
                        $stmt->execute([$shop_id,]);
                        return $stmt->fetchAll();
        } catch (PDOException $e) {
            die("Error: Unable to insert data to Supplier: " . $e->getMessage());
        }
    }
    public function item($shop_id)
   {
    try {
        $sql = "SELECT Barcode,ItemName,ProdDescription,ProdPurchasePrice,ProdSellPrice,CartonQty ,subcategories.SubCatName,units.UnitName
                FROM `products`
                LEFT JOIN subcategories ON subcategories.SCID = products.Subcategories_SCID
                LEFT JOIN units ON units.UNID = products.SellingUnit
                WHERE products.shop_SHID=$shop_id";
                 $stmt = $this->connect()->prepare($sql);
                 $stmt->execute();
               return $stmt->fetchAll();
                  } catch (PDOException $e) {
                die("Error: Unable to fetch data from Product: " . $e->getMessage());
    }
}
    public function credit($shop_id)
   {
    try {
            $sql = "SELECT c.*, SUM(cc.CreditAmount) AS total_credit, SUM(cc.DebitAmount) AS total_debit, SUM(cc.CreditAmount) - SUM(cc.DebitAmount) AS BALANCE,cc.EffectiveDate,cc.DueDate  FROM customers c
                INNER JOIN creditcustomer cc ON c.CTID=cc.Customers_CTID 
                WHERE c.shop_SHID='$shop_id' AND cc.CreditStat=1 
                GROUP by cc.Customers_CTID HAVING (BALANCE < MaxCreditAmount OR BALANCE > MaxCreditAmount OR BALANCE = MaxCreditAmount)AND BALANCE !=0;";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        die("Error: Unable to retrieve data: " . $e->getMessage());
    }
   }

   public function customersale($shop_id)
   {
    try {
        $sql = "SELECT InvoiceNo,EffectiveDate,BillNo,GrossAmount,NetAmount,DiscountAmount,CustPayment,CustBalance
                    FROM invoiceheader 
                    WHERE invoiceheader.shop_SHID=$shop_id";
             $stmt = $this->connect()->prepare($sql);
             $stmt->execute();
             return $stmt->fetchAll();
         }  catch (PDOException $e) {
             die("Error: Unable to retrieve data: " . $e->getMessage());
        }
   }

   public function customerlist($shop_id)
   {
    try{
        $sql="SELECT CustomerNo,CustName,CustAddress,CustContact,MaxCreditAmount
            FROM  customers 
            WHERE customers.shop_SHID=$shop_id";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();

             }  catch (PDOException $e) {
                 die("Error: Unable to retrieve data: " . $e->getMessage());
             }
   }
   public function Supplierlist($shop_id)
   {
    try{
        $sql="SELECT SupplierNo,SupplierName,Distributer,Contact
            FROM suppliers
            WHERE suppliers.shop_SHID=$shop_id";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
            
             }  catch (PDOException $e) {
                 die("Error: Unable to retrieve data: " . $e->getMessage());
            }
   }

   public function salesman($shop_id)
   {
    try{
        $sql="SELECT SalesmanNo,SalesmansName,SalesmansContact
            FROM salesmans
            WHERE salesmans.shop_SHID=$shop_id";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
             }  catch (PDOException $e) {
                 die("Error: Unable to retrieve data: " . $e->getMessage());
            }
   }

   public function unitlist()
   {
    try{
        $sql="SELECT UnitName,ShortName
              FROM units";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
             }  catch (PDOException $e) {
                 die("Error: Unable to retrieve data: " . $e->getMessage());
            }
        }
   public function supplierreturn($shop_id,$from_date=null,$to_date=null)
   {
        try{
            $add="";
            if($from_date!=null && $to_date!=null)
            {
                $add.=" AND supplierreturn.EffectiveDate BETWEEN  '$from_date' AND '$to_date' ";
            }
            $sql = "SELECT supplierreturn.SRID ,supplierreturn.ReturnNo,supplierreturn.EffectiveDate,supplierreturn.ReturnAmount
            FROM supplierreturn  
            WHERE supplierreturn.shop_SHID=$shop_id $add";
                $stmt = $this->connect()->prepare($sql);
                $stmt->execute();
                return $stmt->fetchAll();
                }  catch (PDOException $e) {
            die("Error: Unable to retrieve data: " . $e->getMessage());
        }
    }

    public function sumReturnQty($SRID)
    {
        try{
            $sql = "SELECT SUM(ReturnQty) AS ReturnQty
            FROM supplierreturndetails  
            WHERE supplierreturn_SRID=$SRID";
                $stmt = $this->connect()->prepare($sql);
                $stmt->execute();
                return $stmt->fetchAll();
                }  catch (PDOException $e) {
            die("Error: Unable to retrieve data: " . $e->getMessage());
        }
    }

public function transfernote($shop_id)
{
 try{
     $sql = "SELECT TransferNo,EffectiveDate,TransferTotalCount,TransferTotalAmount
                FROM transferheader 
                INNER JOIN user ON user.USID = transferheader.user_USID
                WHERE transferheader.shop_SHID=$shop_id";
          $stmt = $this->connect()->prepare($sql);
          $stmt->execute();
          return $stmt->fetchAll();
         }  catch (PDOException $e) {
      die("Error: Unable to retrieve data: " . $e->getMessage());
 }
}
   

public function topproduct($shop_id, $start=null, $end=null)
{
    $add=" ";
    if($start!=null && $end!=null)
    {
        $add.=" AND ih.EffectiveDate BETWEEN '$start' AND '$end' ";
    }
 try{
     $sql = "SELECT sum(id.SellQty) AS SoldQty,p.ItemName, ih.EffectiveDate FROM invoicedetails id
            INNER JOIN products p ON p.PDID=id.products_PDID
            INNER JOIN invoiceheader ih ON id.InvoiceHeader_IHID= ih.IHID
            WHERE p.shop_SHID='$shop_id' ".$add."group by p.PDID;";
          $stmt = $this->connect()->prepare($sql);
          $stmt->execute();
          return $stmt->fetchAll();
         }  catch (PDOException $e) {
      die("Error: Unable to retrieve data: " . $e->getMessage());
 }
}


    public function expense($shop_id)
    {
        try {
            $sql = "SELECT ep.EPID, ep.EffectiveDate, ec.expense_ctg, ep.ExpenseAmount, ep.ExpenseReason, u.UserName, s.ShopName             
                    FROM expenses ep 
                    JOIN shop s ON ep.shop_SHID = s.SHID
                    JOIN user u ON ep.user_USID = u.USID 
                    JOIN expensecategory ec ON ep.ExpenseCategory = ec.ECID
                    WHERE ep.shop_SHID=?";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute([$shop_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            die("Error: Unable to insert data to expences: " . $e->getMessage());
        }
    }

    public function supplierpayment($shop_id, $start=null,$end=null)
    {
        try {
            $add="";
            if($start!=null && $end!=null)
            {
                $add.="AND gh.EffectiveDate BETWEEN '$start' AND '$end' ";
            }
            $sql = "SELECT *, SUM(st.TransferAmount) AS TotPurchase FROM `suppliertransactions` st 
                    INNER JOIN grnheader gh ON st.GRNHeader_GHID=gh.GHID
                    INNER JOIN suppliers s ON s.SPID = gh.Suppliers_SPID
                    WHERE st.TransactionStat=1 AND gh.GRNStat=2 AND gh.shop_SHID='$shop_id' $add
                    GROUP BY s.SPID";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute([]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            die("Error: Unable to insert data to expences: " . $e->getMessage());
        }
    }

    public function returninvoicereport($shop_id,$type=null, $from_date=null, $to_date=null)
    {
        try {
            $sql = "SELECT rih.*, u.UserName AS returnPerson, rt.SRT_Name, rt.SRTID FROM `retrun_invoice_header` rih
                    LEFT JOIN user u ON u.USID=rih.returnby
                    LEFT JOIN salesreturntype rt ON rt.SRTID=rih.return_type 
                    WHERE rih.shopID='$shop_id'";
            if(isset($type) && !empty($type))
            {
                $sql .="  AND rih.return_type='$type'";
            }
            else
            {

            }
            if($from_date!=null && $to_date!=null)
            {
                $sql .=" AND (rih.EffectiveDate BETWEEN '$from_date' AND '$to_date' OR rih.EffectiveDate='$from_date' OR rih.EffectiveDate='$to_date') ";
            }
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            die("Error: Unable to show data to return: " . $e->getMessage());
        }
    }

    public function duecustomer($id=null)
    {
        $shop_id = $_SESSION['shop_id'];
        try 
        {
            $add=" ";
            if($id!=null)
            {
                $add .="AND c.CTID='$id'";
            }           
            $sql = "SELECT c.*, SUM(cc.CreditAmount) AS total_credit, SUM(cc.DebitAmount) AS total_debit FROM customers c
                INNER JOIN creditcustomer cc ON c.CTID=cc.Customers_CTID 
                WHERE c.shop_SHID='$shop_id' ".$add."
                GROUP by cc.Customers_CTID;";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        }
        catch (PDOException $e) {
            die("Error: Unable to fetch Customer data " . $e->getMessage());
        }
    }
    public function expenseByDate($shop_id, $from_date, $to_date)
    {
        try {
            $sql = "SELECT ep.EPID, ep.EffectiveDate, ec.expense_ctg, ep.ExpenseAmount, ep.ExpenseReason, u.UserName, s.ShopName             
                    FROM expenses ep 
                    JOIN shop s ON ep.shop_SHID = s.SHID
                    JOIN user u ON ep.user_USID = u.USID 
                    JOIN expensecategory ec ON ep.expensecategory_id = ec.ECID
                    WHERE ep.shop_SHID=?";
            
            $params = [$shop_id];

            if ($from_date && $to_date) {
                $sql .= " AND ep.EffectiveDate BETWEEN ? AND ?";
                $params[] = $from_date;
                $params[] = $to_date;
            }

            $stmt = $this->connect()->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            die("Error: Unable to insert data to Expenses: " . $e->getMessage());
        }
    }
    public function usersale()
    {
        try {
            $sql = "SELECT sm.SalesmansName as SalesPerson, u.UserName AS cashier,GrossAmount,NetAmount,CustPayment,CustBalance
                FROM `invoiceheader` ih 
                left join salesmans sm ON ih.Salesmans_SLID = sm.SLID
                left join user u ON ih.user_USID=u.USID";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute([]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            die("Error: Unable to insert data to expences: " . $e->getMessage());
        }
    }
    public function category($shop_id)
    {
        try {

            $sql = "SELECT c.CTID, c.CategoryNo, c.CategoryName, COUNT(sc.SCID) AS sub_cat_count
                    FROM categories c
                    LEFT JOIN subcategories sc ON sc.categories_CTID = c.CTID
                    WHERE shop_SHID = ?
                    GROUP BY c.CTID
                    ORDER BY CategoryNo ASC";

            $stmt = $this->connect()->prepare($sql);
            $stmt->execute([$shop_id]);
            return $stmt->fetchAll();

        } catch (PDOException $e) {
            die('Error: Unable to load categories: ' . $e->getMessage());
        }
    }
    public function category2($shop_id, $category_id)
    {

        try {

            $sql = "SELECT 
                        c.CategoryNo AS cat_no,
                        c.CategoryName AS cat_name,
                        sc.SubCatNo AS sub_cat_no,
                        sc.SubCatName AS sub_cat_name,
                        COUNT(p.PDID) AS sub_pro_count,
                        (
                            SELECT COUNT(p2.PDID)
                            FROM subcategories sc2
                            INNER JOIN products p2 ON p2.Subcategories_SCID = sc2.SCID
                            WHERE sc2.categories_CTID = c.CTID
                            AND p2.shop_SHID = ?
                        ) AS cat_pro_count
                    FROM categories c
                    LEFT JOIN subcategories sc ON sc.categories_CTID = c.CTID
                    LEFT JOIN products p ON p.Subcategories_SCID = sc.SCID
                        AND p.shop_SHID = ?
                    WHERE c.shop_SHID = ? AND c.CTID = ?
                    GROUP BY c.CTID, sc.SCID
                    ORDER BY c.CTID ASC, sc.SCID ASC";

            $stmt = $this->connect()->prepare($sql);
            $stmt->execute([$shop_id, $shop_id, $shop_id, $category_id]);
            return $stmt->fetchAll();

        } catch (PDOException $e) {
            die("Error: Unable to load categories: " . $e->getMessage());
        }
    }
    
    public function variations()
    {
        try {
            $sql = "SELECT ItemName,variations.VariationName
                    FROM products
                    INNER JOIN variations ON products.PDID = variations.products_PDID";
             $stmt = $this->connect()->prepare($sql);
             $stmt->execute();
             return $stmt->fetchAll();
        }    catch (PDOException $e) {
             die("Error: Unable to insert data : " . $e->getMessage());
        }
    }  
}
?>