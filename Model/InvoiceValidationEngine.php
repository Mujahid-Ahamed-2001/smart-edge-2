<?php

require_once "InventoryEngine.php";

class InvoiceValidationEngine
{
    private InventoryEngine $inventory;

    public function __construct()
    {
        $this->inventory = new InventoryEngine();
    }

    public function validateInvoiceItems(array $items, array $settings)
    {
        $plans = [];

        $items = $this->normalizeInvoiceItems($items);

        foreach($items as $index=>$item)
        {
            /*
            |--------------------------------------------------------------------------
            | Build Inventory Snapshot
            |--------------------------------------------------------------------------
            */

            $snapshot = $this->inventory->buildInventorySnapshot(

                $item["product_id"],

                $item["selling_price"],

                $settings["shop_id"],

                $settings["company_id"],

                $settings["is_common_stock"],

                $settings["stock_type"],

                $settings["is_expire"]

            );

            /*
            |--------------------------------------------------------------------------
            | Validate Inventory
            |--------------------------------------------------------------------------
            */

            $validation = $this->inventory->validateInventoryItem(

                $snapshot,

                $item["qty"],

                $settings["is_minus"]

            );

            if(!$validation["status"])
            {
                return [

                    "status"=>false,

                    "item"=>$index,

                    "product_id"=>$item["product_id"],

                    "item_index"=>$index,

                    "message"=>$validation["message"]

                ];
            }

            /*
            |--------------------------------------------------------------------------
            | Build Consumption Plan
            |--------------------------------------------------------------------------
            */

            $plan = $this->inventory->buildInventoryConsumptionPlan(

                $snapshot,

                $validation,

                $item["qty"]

            );

            if(!$plan["status"])
            {
                return [

                    "status"=>false,

                    "item"=>$index,

                    "product_id"=>$item["product_id"],

                    "message"=>$plan["message"]

                ];
            }

            $plans[] = [

                "product_id"      => $item["product_id"],

                "selling_price"   => $item["selling_price"],

                "qty"             => $item["qty"],

                "validation"      => $validation,

                "snapshot"        => $snapshot,

                "plan"            => $plan

            ];
        }

        return [

            "status" => true,

            "plans" => $plans,

            "summary" => [

                "products" => count($plans),

                "validated_at" => date("Y-m-d H:i:s")

            ]

        ];
    }

    private function normalizeInvoiceItems(array $items)
    {
        $normalized = [];

        foreach($items as $item)
        {
            /*
            |--------------------------------------------------------------------------
            | Product + Selling Price
            |--------------------------------------------------------------------------
            */

            $key = implode("_", [

                $item["product_id"],

                $item["selling_price"],

                (isset($item["product_type"]) && !empty($item["product_type"]) ? $item["product_type"] : "")."_".

                (isset($item["serial"]) && !empty($item["serial"]) ? $item["serial"] : "")

            ]);

            if(!isset($normalized[$key]))
            {
                $normalized[$key] = $item;
            }
            else
            {
                $normalized[$key]["qty"] += $item["qty"];
            }
        }

        return array_values($normalized);
    }

}