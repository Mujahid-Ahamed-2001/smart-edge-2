<?php

class newProduct extends Dbh
{
    public function addProduct(array $data, int $user_USID)
    {
        $ProductNo = $this->newProNo();

        $Barcode = !empty($data["Barcode"]) ? trim($data["Barcode"]) : $ProductNo;

        // Remove everything except A-Z, a-z, 0-9 and _
        $Barcode = preg_replace('/[^A-Za-z0-9_]/', '', $Barcode);

        // If barcode becomes empty after cleaning, use ProductNo
        if (empty($Barcode)) {
            $Barcode = $ProductNo;
        }

        $ItemName = isset($data["ItemName"])? trim($data["ItemName"]): "";

        // Allow letters, numbers, spaces and .
        $ItemName = preg_replace('/[^A-Za-z0-9 .]/', '', $ItemName);

        // Convert multiple spaces into one
        $ItemName = preg_replace('/\s+/', ' ', $ItemName);

        $ProdDescription = isset($data["ProdDescription"])? trim($data["ProdDescription"]): "";

        $SecondName = isset($data["SecondName"]) ? trim($data["SecondName"]) : "";

        $ProdPurchasePrice = isset($data["ProdPurchasePrice"]) ? trim($data["ProdPurchasePrice"]) : 0;

        $ProdSellPrice = isset($data["ProdSellPrice"]) ? trim($data["ProdSellPrice"]) : 0;

        $ProductStat = !empty($data["ProductStat"]) ? 1 : 0;

        $AddedDate = date('Y-m-d');
        $UpdatedDate = date('Y-m-d');

        $ItemType = isset($data["ItemType"]) ? "S" : "P";

        $UpdateUserID = $user_USID;

        $Subcategories_SCID = isset($data["Subcategories_SCID"]) ? trim($data["Subcategories_SCID"]) : "";

        $PurchaseUnit = isset($data["PurchaseUnit"]) ? trim($data["PurchaseUnit"]) : 1;

        $UnitConversion = isset($data["UnitConversion"]) ? trim($data["UnitConversion"]) : 1;

        $SellingUnit = isset($data["SellingUnit"]) ? trim($data["SellingUnit"]) : 1;

        $prodDiscount = isset($data["prodDiscount"]) ? trim($data["prodDiscount"]) : "";

        $is_fixedPrice = !empty($data["is_fixedPrice"]) ? 1 : 0;

        $is_lowStock = !empty($data["is_lowStock"]) ? 1 : 0;

        $low_stock_qty = isset($data["low_stock_qty"]) ? trim($data["low_stock_qty"]) : 0;

        $prodFlatDiscount = isset($data["prodFlatDiscount"]) ? trim($data["prodFlatDiscount"]) : "";

        $multi = !empty($data["multi"]) ? 1 : 0;
        if($multi==1)
        {
            $ProductStat = 1;
        }


        try {

            $pdo = $this->connect();

            $sql = "INSERT INTO products ( ProductNo, Barcode, ItemName, ProdDescription, SecondName, ProdPurchasePrice, ProdSellPrice, ProductStat, AddedDate, UpdatedDate, ItemType, user_USID, UpdateUserID, Subcategories_SCID, PurchaseUnit, UnitConversion, SellingUnit, prodDiscount, is_fixedPrice, is_lowStock, low_stock_qty, prodFlatDiscount, multi)
                VALUES
                (
                    :ProductNo,
                    :Barcode,
                    :ItemName,
                    :ProdDescription,
                    :SecondName,
                    :ProdPurchasePrice,
                    :ProdSellPrice,
                    :ProductStat,
                    :AddedDate,
                    :UpdatedDate,
                    :ItemType,
                    :user_USID,
                    :UpdateUserID,
                    :Subcategories_SCID,
                    :PurchaseUnit,
                    :UnitConversion,
                    :SellingUnit,
                    :prodDiscount,
                    :is_fixedPrice,
                    :is_lowStock,
                    :low_stock_qty,
                    :prodFlatDiscount,
                    :multi
                )
            ";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ':ProductNo' => $ProductNo,
                ':Barcode' => $Barcode,
                ':ItemName' => $ItemName,
                ':ProdDescription' => $ProdDescription,
                ':SecondName' => $SecondName,
                ':ProdPurchasePrice' => $ProdPurchasePrice,
                ':ProdSellPrice' => $ProdSellPrice,
                ':ProductStat' => $ProductStat,
                ':AddedDate' => $AddedDate,
                ':UpdatedDate' => $UpdatedDate,
                ':ItemType' => $ItemType,
                ':user_USID' => $user_USID,
                ':UpdateUserID' => $UpdateUserID,
                ':Subcategories_SCID' => $Subcategories_SCID,
                ':PurchaseUnit' => $PurchaseUnit,
                ':UnitConversion' => $UnitConversion,
                ':SellingUnit' => $SellingUnit,
                ':prodDiscount' => $prodDiscount,
                ':is_fixedPrice' => $is_fixedPrice,
                ':is_lowStock' => $is_lowStock,
                ':low_stock_qty' => $low_stock_qty,
                ':prodFlatDiscount' => $prodFlatDiscount,
                ':multi' => $multi
            ]);


            // Get inserted PDID
            $PDID = $pdo->lastInsertId();


            // Return ID + inserted product data
            return [
                'status' => true,
                'PDID' => (int) $PDID,
                'data' => [
                    'PDID' => (int) $PDID,
                    'ProductNo' => $ProductNo,
                    'Barcode' => $Barcode,
                    'ItemName' => $ItemName,
                    'ProdDescription' => $ProdDescription,
                    'SecondName' => $SecondName,
                    'ProdPurchasePrice' => $ProdPurchasePrice,
                    'ProdSellPrice' => $ProdSellPrice,
                    'ProductStat' => $ProductStat,
                    'AddedDate' => $AddedDate,
                    'UpdatedDate' => $UpdatedDate,
                    'ItemType' => $ItemType,
                    'user_USID' => $user_USID,
                    'UpdateUserID' => $UpdateUserID,
                    'Subcategories_SCID' => $Subcategories_SCID,
                    'PurchaseUnit' => $PurchaseUnit,
                    'UnitConversion' => $UnitConversion,
                    'SellingUnit' => $SellingUnit,
                    'prodDiscount' => $prodDiscount,
                    'is_fixedPrice' => $is_fixedPrice,
                    'is_lowStock' => $is_lowStock,
                    'low_stock_qty' => $low_stock_qty,
                    'prodFlatDiscount' => $prodFlatDiscount,
                    'multi' => $multi
                ],
                'message' => "Product Inserted Successfully" 
            ];

        } catch (PDOException $e) {

            return [
                'status' => false,
                'PDID' => null,
                'data' => null,
                'message' => $e->getMessage()
            ];
        }
    }

    public function getproductno(int $PDID)
    {
        $sql ="SELECT ";
    }

    public function updateProduct(
        int $PDID,
        array $data,
        int $user_USID
    ): array
    {
        try {

            if ($PDID <= 0) {
                throw new InvalidArgumentException(
                    "Invalid Product ID."
                );
            }


            $pdo = $this->connect();

            if (!$pdo) {
                throw new Exception(
                    "Database connection failed."
                );
            }


            // ==========================================
            // Get ProductNo
            // ==========================================

            $sql = "SELECT ProductNo
                FROM products
                WHERE PDID = :PDID
                LIMIT 1
            ";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ':PDID' => $PDID
            ]);

            $product = $stmt->fetch(PDO::FETCH_ASSOC);


            if (!$product) {
                throw new Exception(
                    "Product not found."
                );
            }


            $ProductNo = $product["ProductNo"];


            // ==========================================
            // Barcode
            // ==========================================

            $Barcode = isset($data["Barcode"])
                ? trim($data["Barcode"])
                : "";


            $Barcode = preg_replace(
                '/[^A-Za-z0-9_]/',
                '',
                $Barcode
            );


            // Barcode should never be empty
            if (empty($Barcode)) {
                $Barcode = $ProductNo;
            }


            // ==========================================
            // Product Name
            // ==========================================

            $ItemName = isset($data["ItemName"])
                ? trim($data["ItemName"])
                : "";


            $ItemName = preg_replace(
                '/[^A-Za-z0-9 .]/',
                '',
                $ItemName
            );


            $ItemName = preg_replace(
                '/\s+/',
                ' ',
                $ItemName
            );


            // ==========================================
            // Other Data
            // ==========================================

            $Subcategories_SCID =
                isset($data["Subcategories_SCID"])
                    ? (int) $data["Subcategories_SCID"]
                    : 0;


            $ProdPurchasePrice =
                isset($data["ProdPurchasePrice"])
                    ? (float) $data["ProdPurchasePrice"]
                    : 0;


            $ProdSellPrice =
                isset($data["ProdSellPrice"])
                    ? (float) $data["ProdSellPrice"]
                    : 0;


            $low_stock_qty =
                isset($data["low_stock_qty"])
                    ? (float) $data["low_stock_qty"]
                    : 0;


            // Negative values become 0
            $ProdPurchasePrice = max(0, $ProdPurchasePrice);
            $ProdSellPrice = max(0, $ProdSellPrice);
            $low_stock_qty = max(0, $low_stock_qty);


            // ==========================================
            // Update Product
            // ==========================================

            $sql = "UPDATE products
                SET
                    Barcode = :Barcode,
                    ItemName = :ItemName,
                    Subcategories_SCID = :Subcategories_SCID,
                    ProdPurchasePrice = :ProdPurchasePrice,
                    ProdSellPrice = :ProdSellPrice,
                    low_stock_qty = :low_stock_qty,
                    UpdatedDate = CURDATE(),
                    UpdateUserID = :UpdateUserID

                WHERE PDID = :PDID
            ";


            $stmt = $pdo->prepare($sql);


            $stmt->execute([

                ':Barcode' => $Barcode,

                ':ItemName' => $ItemName,

                ':Subcategories_SCID' =>
                    $Subcategories_SCID,

                ':ProdPurchasePrice' =>
                    $ProdPurchasePrice,

                ':ProdSellPrice' =>
                    $ProdSellPrice,

                ':low_stock_qty' =>
                    $low_stock_qty,

                ':UpdateUserID' =>
                    $user_USID,

                ':PDID' =>
                    $PDID
            ]);


            // ==========================================
            // Success
            // ==========================================

            return [
                "status" => true,

                "data" => [
                    "PDID" => $PDID,
                    "ProductNo" => $ProductNo,
                    "Barcode" => $Barcode,
                    "ItemName" => $ItemName,
                    "Subcategories_SCID" => $Subcategories_SCID,
                    "ProdPurchasePrice" => $ProdPurchasePrice,
                    "ProdSellPrice" => $ProdSellPrice,
                    "low_stock_qty" => $low_stock_qty
                ],

                "message" =>
                    "Product updated successfully."
            ];


        } catch (Throwable $e) {

            return [
                "status" => false,
                "data" => null,
                "message" => $e->getMessage()
            ];
        }
    }

    public function updateOpeningInventory(
        int $PDID,
        int $shop_SHID,
        float $CurrentQty
    ): array
    {
        try {

            $CurrentQty = max(0, $CurrentQty);

            $pdo = $this->connect();

            if (!$pdo) {
                throw new Exception("Database connection failed.");
            }


            $sql = "UPDATE inventory
                SET
                    CurrentQty = :CurrentQty
                WHERE products_PDID = :PDID
                AND shop_SHID = :shop_SHID
                AND is_openStock = 1
                AND is_default = 0
            ";


            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ':CurrentQty' => $CurrentQty,
                ':PDID' => $PDID,
                ':shop_SHID' => $shop_SHID
            ]);


            return [
                "status" => true,
                "message" => "Opening inventory updated successfully."
            ];

        } catch (Throwable $e) {

            return [
                "status" => false,
                "message" => $e->getMessage()
            ];
        }
    }
    public function updateOpeningPriceHistory(
        int $PDID,
        int $shop_SHID,
        array $data
    ): array
    {
        try {

            $PurchasePrice = isset($data["ProdPurchasePrice"])
                ? (float) $data["ProdPurchasePrice"]
                : 0;

            $SellingPrice = isset($data["ProdSellPrice"])
                ? (float) $data["ProdSellPrice"]
                : 0;


            $PurchasePrice = max(0, $PurchasePrice);
            $SellingPrice = max(0, $SellingPrice);


            $pdo = $this->connect();

            if (!$pdo) {
                throw new Exception("Database connection failed.");
            }


            $sql = "UPDATE pricehistory ph

                INNER JOIN inventory i
                    ON i.INID = ph.Inventory_INID

                SET
                    ph.PurchasePrice = :PurchasePrice,
                    ph.SellingPrice = :SellingPrice,
                    ph.labelPrice = :SellingPrice,
                    ph.EffectiveDate = NOW()

                WHERE ph.ProductID = :PDID
                AND i.shop_SHID = :shop_SHID
                AND i.is_openStock = 1
                AND i.is_default = 0
            ";


            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ':PurchasePrice' => $PurchasePrice,
                ':SellingPrice' => $SellingPrice,
                ':PDID' => $PDID,
                ':shop_SHID' => $shop_SHID
            ]);


            return [
                "status" => true,
                "message" => "Price history updated successfully."
            ];

        } catch (Throwable $e) {

            return [
                "status" => false,
                "message" => $e->getMessage()
            ];
        }
    }
    public function newProNo()
    {
        $sql = "SELECT MAX(PDID) AS num FROM products";

        $stmt = $this->connect()->prepare($sql);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // If there are no products yet, start from 1
        $number = ($result['num'] ?? 0) + 1;
        
        return 'PD_' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    public function newBatchNo(int $PDID): string
    {
        $sql = "SELECT COUNT(*) AS num FROM inventory WHERE products_PDID = :PDID ";

        $stmt = $this->connect()->prepare($sql);

        $stmt->execute([ ':PDID' => $PDID ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $number = ((int) ($result['num'] ?? 0)) + 1;

        return 'B' . str_pad($number, 9, '0', STR_PAD_LEFT);
    }
    
    public function create_inventory( int $PDID, int $shop_SHID, float $CurrentQty = 0, int $OpeningStock = 0, int $is_default = 0 ): array
    {
        try {

            // =========================================
            // Validation
            // =========================================

            if ($PDID <= 0) {
                throw new InvalidArgumentException("Invalid Product ID.");
            }

            if ($shop_SHID <= 0) {
                throw new InvalidArgumentException("Invalid Shop ID.");
            }

            $CurrentQty = max(0, $CurrentQty);

            // Only allow 0 or 1
            if (!in_array($OpeningStock, [0, 1], true)) {
                throw new InvalidArgumentException("OpeningStock must be 0 or 1.");
            }

            // Only allow 0 or 1
            if (!in_array($is_default, [0, 1], true)) {
                throw new InvalidArgumentException("is_default must be 0 or 1.");
            }


            // =========================================
            // Database Connection
            // =========================================

            $pdo = $this->connect();

            if (!$pdo) {
                throw new Exception("Database connection failed.");
            }


            // =========================================
            // Generate Batch ID
            // =========================================

            $BatchID = $this->newBatchNo($PDID);

            // =========================================
            // Insert Inventory
            // =========================================

            $sql = "INSERT INTO inventory ( CurrentQty, BillQty, ReturnQty, TransferInQty, TransferOutQty, Sup_Rtn, products_PDID, shop_SHID, RackID, is_default, is_openStock, BatchID, created_date ) VALUES ( :CurrentQty, 0, 0, 0, 0, 0, :PDID, :shop_SHID, 0, :is_default, :OpeningStock, :BatchID, NOW() )
            ";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':CurrentQty'  => $CurrentQty,
                ':PDID'        => $PDID,
                ':shop_SHID'   => $shop_SHID,
                ':is_default'  => $is_default,
                ':OpeningStock'=> $OpeningStock,
                ':BatchID'     => $BatchID
            ]);


            // =========================================
            // Get Inserted ID
            // =========================================

            $InventoryID = (int) $pdo->lastInsertId();


            // =========================================
            // Success Response
            // =========================================

            return [
                'status' => true,
                'data' => [
                    'INID' => $InventoryID,
                    'BatchID' => $BatchID
                ],

                'message' => "Inventory created successfully."
            ];


        } catch (Throwable $e) {

            return [
                'status' => false,
                'data' => null,
                'message' => $e->getMessage()
            ];
        }
    }

    public function create_pricehistory( int $INID, string $BatchID, int $PDID, array $data): array
    {
        try {

            // =========================================
            // Get & Clean Data
            // =========================================

            $PurchasePrice = isset($data["ProdPurchasePrice"]) ? (float) trim($data["ProdPurchasePrice"]) : 0;

            $SellingPrice = isset($data["ProdSellPrice"]) ? (float) trim($data["ProdSellPrice"]) : 0;

            $MnfDate = !empty($data["MnfDate"]) ? trim($data["MnfDate"]) : null;

            $ExpDate = !empty($data["ExpDate"]) ? trim($data["ExpDate"]) : null; 

            // Negative prices should be treated as 0
            $PurchasePrice = max(0, $PurchasePrice);
            $SellingPrice = max(0, $SellingPrice);


            // =========================================
            // Database Connection
            // =========================================

            $pdo = $this->connect();

            if (!$pdo) {
                throw new Exception("Database connection failed.");
            }


            // =========================================
            // Insert Price History
            // =========================================

            $sql = "INSERT INTO pricehistory
                (
                    ProductID,
                    EffectiveDate,
                    PurchasePrice,
                    SellingPrice,
                    labelPrice,
                    MnfDate,
                    ExpDate,
                    BatchID,
                    Inventory_INID
                )
                VALUES
                (
                    :ProductID,
                    NOW(),
                    :PurchasePrice,
                    :SellingPrice,
                    :labelPrice,
                    :MnfDate,
                    :ExpDate,
                    :BatchID,
                    :Inventory_INID
                )
            ";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ':ProductID'      => $PDID,
                ':PurchasePrice'  => $PurchasePrice,
                ':SellingPrice'   => $SellingPrice,
                ':labelPrice'     => $SellingPrice,
                ':MnfDate'        => $MnfDate,
                ':ExpDate'        => $ExpDate,
                ':BatchID'        => $BatchID,
                ':Inventory_INID' => $INID
            ]);


            // =========================================
            // Get Inserted ID
            // =========================================

            $PriceHistoryID = (int) $pdo->lastInsertId();


            // =========================================
            // Success Response
            // =========================================

            return [
                'status' => true,
                'data' => [
                    'PHID' => $PriceHistoryID,
                    'BatchID' => $BatchID
                ],
                'message' => "Price history created successfully."
            ];

        } catch (Throwable $e) {

            return [
                'status' => false,
                'PriceHistoryID' => null,
                'data' => null,
                'message' => $e->getMessage()
            ];
        }
    }
}