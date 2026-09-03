<?php 

class InventoryEngine extends Dbh
{
    public function getShopInventory( $product_id, $selling_price, $shop_id, $company_id, $is_common_stock, $stock_type, $is_expire)
    {
        try
        {
            $date = date("Y-m-d");

            $condition = "i.products_PDID = :product
                AND ph.SellingPrice = :price
                AND i.CurrentQty > 0
                AND i.is_default = 0
            ";

            if($is_expire)
            {
                $condition .= " AND
                (
                    ph.ExpDate IS NULL
                    OR ph.ExpDate='0000-00-00'
                    OR ph.ExpDate > :today
                )";
            }

            if($is_common_stock == 1)
            {
                $condition .= " AND EXISTS
                (
                    SELECT 1
                    FROM shop s
                    WHERE s.SHID=i.shop_SHID
                    AND s.Company_CMID=:company
                )";
            }
            else
            {
                $condition .= " AND i.shop_SHID=:shop";
            }

            $order = ($stock_type == 3) ? "DESC" : "ASC";

            $sql = "SELECT

                i.*,

                ph.PHID,
                ph.SellingPrice,
                ph.ExpDate

            FROM inventory i

            INNER JOIN pricehistory ph
            ON ph.Inventory_INID=i.INID

            WHERE $condition

            ORDER BY

            i.INID $order";

            $stmt = $this->connect()->prepare($sql);

            $stmt->bindValue(":product",$product_id);
            $stmt->bindValue(":price",$selling_price);

            if($is_common_stock)
            {
                $stmt->bindValue(":company",$company_id);
            }
            else
            {
                $stmt->bindValue(":shop",$shop_id);
            }

            if($is_expire)
            {
                $stmt->bindValue(":today",$date);
            }

            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public function getDefaultInventory( $product_id, $selling_price, $stock_type, $is_expire )
    {
        try
        {
            $date = date("Y-m-d");

            $condition = "i.products_PDID=:product
                AND ph.SellingPrice=:price
                AND i.CurrentQty>0
                AND i.is_default=1
            ";

            if($is_expire)
            {
                $condition .= " AND
                (
                    ph.ExpDate IS NULL
                    OR ph.ExpDate='0000-00-00'
                    OR ph.ExpDate>:today
                )";
            }

            $order = ($stock_type == 3) ? "DESC" : "ASC";

            $sql = "SELECT

                i.*,

                ph.PHID,
                ph.SellingPrice,
                ph.ExpDate

            FROM inventory i

            INNER JOIN pricehistory ph
            ON ph.Inventory_INID=i.INID

            WHERE

            $condition

            ORDER BY

            i.INID $order";

            $stmt = $this->connect()->prepare($sql);

            $stmt->bindValue(":product",$product_id);
            $stmt->bindValue(":price",$selling_price);

            if($is_expire)
            {
                $stmt->bindValue(":today",$date);
            }

            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public function buildInventorySnapshot( $product_id, $selling_price, $shop_id, $company_id, $is_common_stock, $stock_type, $is_expire
    )
    {
        try
        {
            // Get normal inventory
            $shopInventory = $this->getShopInventory( $product_id, $selling_price, $shop_id, $company_id, $is_common_stock, $stock_type, $is_expire );

            // Get default inventory
            $defaultInventory = $this->getDefaultInventory( $product_id, $selling_price, $stock_type, $is_expire );

            $shopQty = 0;
            foreach($shopInventory as $row)
            {
                $shopQty += (float)$row["CurrentQty"];
            }

            $defaultQty = 0;
            foreach($defaultInventory as $row)
            {
                $defaultQty += (float)$row["CurrentQty"];
            }

            return [
                "status" => true,

                "shop_inventory" => $shopInventory,

                "default_inventory" => $defaultInventory,

                "shop_stock" => $shopQty,

                "default_stock" => $defaultQty,

                "total_stock" => $shopQty + $defaultQty
            ];
        }
        catch(PDOException $e)
        {
            return [
                "status" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    public function validateInventoryItem($snapshot, $requiredQty, $is_minus)
    {
        if(!$snapshot["status"])
        {
            return [
                "status" => false,
                "message" => "Unable to load inventory."
            ];
        }

        $shopStock = (float)$snapshot["shop_stock"];
        $defaultStock = (float)$snapshot["default_stock"];

        /*
        |--------------------------------------------------------------------------
        | Enough Shop Stock
        |--------------------------------------------------------------------------
        */

        if($shopStock >= $requiredQty)
        {
            return [
                "status" => true,
                "use_default" => false,
                "remaining_qty" => 0,
                "message" => "Inventory Available"
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Minus Stock Disabled
        |--------------------------------------------------------------------------
        */

        if(!$is_minus)
        {
            return [
                "status" => false,
                "message"=>"Insufficient stock. Available : ".$shopStock.", Requested : ".$requiredQty
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Remaining Quantity
        |--------------------------------------------------------------------------
        */

        $remaining = $requiredQty - $shopStock;


        return [
            "status" => true,
            "use_default" => true,
            "remaining_qty" => $remaining,
            "message" => "Inventory Available"
        ];
    }

    public function buildInventoryConsumptionPlan($snapshot, $validation, $requiredQty)
    {
        if(!$validation["status"])
        {
            return [
                "status" => false,
                "message" => $validation["message"]
            ];
        }

        $consume = [];

        $remaining = $requiredQty;

        /*
        |--------------------------------------------------------------------------
        | Consume Shop Inventory
        |--------------------------------------------------------------------------
        */

        foreach($snapshot["shop_inventory"] as $row)
        {
            if($remaining <= 0)
            {
                break;
            }

            $available = (float)$row["CurrentQty"];

            $deductQty = min($available, $remaining);

            if($deductQty <= 0)
            {
                continue;
            }

            $consume[] = [

                "inventory_id"      => $row["INID"],
                "product_id"        => $row["products_PDID"],
                "shop_id"           => $row["shop_SHID"],
                "pricehistory_id"   => $row["PHID"],

                "is_default"        => $row["is_default"],

                "current_qty"       => $available,
                "consume_qty"       => $deductQty,
                "new_qty"           => $available - $deductQty,

                "selling_price"     => $row["SellingPrice"],
                "expiry_date"       => $row["ExpDate"]

            ];

            $remaining -= $deductQty;
        }

        /*
        |--------------------------------------------------------------------------
        | Consume Default Inventory
        |--------------------------------------------------------------------------
        */

        if($validation["use_default"])
        {
            foreach($snapshot["default_inventory"] as $row)
            {
                if($remaining <= 0)
                {
                    break;
                }

                $available = (float)$row["CurrentQty"];

                /*
                * Default inventory is allowed to become negative.
                */

                $consume[] = [

                    "inventory_id"      => $row["INID"],
                    "product_id"        => $row["products_PDID"],
                    "shop_id"           => $row["shop_SHID"],
                    "pricehistory_id"   => $row["PHID"],

                    "is_default"        => $row["is_default"],

                    "current_qty"       => $available,

                    "consume_qty"       => $remaining,

                    "new_qty"           => $available - $remaining,

                    "selling_price"     => $row["SellingPrice"],

                    "expiry_date"       => $row["ExpDate"]

                ];

                $remaining = 0;
            }

            /*
            * No default inventory row exists.
            * This should never happen, but we'll detect it.
            */

            if($remaining > 0 && empty($snapshot["default_inventory"]))
            {
                return [
                    "status" => false,
                    "message" => "No default inventory found."
                ];
            }
        }

        return [

            "status" => true,

            "consume" => $consume,

            "summary" => [

                "shop_qty_used" => $requiredQty - $validation["remaining_qty"],

                "default_qty_used" => $validation["remaining_qty"],

                "total_qty" => $requiredQty

            ]

        ];
    }
    public function consumeInventory(PDO $conn, $consumptionPlan)
    {
        try
        {
            if(!$consumptionPlan["status"])
            {
                return $consumptionPlan;
            }


            foreach($consumptionPlan["consume"] as $row)
            {
                $sql = "
                    UPDATE inventory
                    SET CurrentQty = :qty
                    WHERE INID = :inid
                ";

                $stmt = $conn->prepare($sql);

                $stmt->bindValue(":qty",$row["new_qty"],PDO::PARAM_STR);
                $stmt->bindValue(":inid",$row["inventory_id"],PDO::PARAM_INT);

                if(!$stmt->execute())
                {
                    throw new Exception(
                        "Unable to update inventory ID : ".$row["inventory_id"]
                    );
                }
            }

            return [
                "status" => true,
                "message" => "Inventory consumed successfully."
            ];
        }
        catch(Exception $e)
        {
            return [
                "status" => false,
                "message" => $e->getMessage()
            ];
        }
    }
    public function insertInventoryHistory(PDO $conn,$consumptionPlan,$invoice_no)
    {

    }
}
?>