<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\Log;

class ProcurementExport implements FromArray, WithHeadings
{
    private $procurements;

    public function __construct($procurements)
    {
        $this->procurements = $procurements;
    }


    public function headings(): array
    {
        return [
            'Category',
            'Item Name',
            'Description',
            'SKU',
            'Brand Name',
            'Doc Code',
            'Product URL',
            'Height',
            'Depth',
            'Width',
            'Length',
            'Color',
            'Finish',
            'Quantity',
            'Supplier Company Name',
            'Supplier Name',
            'Supplier Email',
            'Supplier Phone Number',
            'Supplier Address',
            'Supplier Timezone',
            'Supplier Trade Discount',
            'Supplier Website',
            'Exchange Rate',
            'Retail Price',
            'Trade Discount',
            'Trade Price',
            'Budget',
            'Actual Price',
            'Trade Terms',
            'Production Lead Time',
            'Shipping Lead Time',
            'Order Date',
            'Shipping Date',
            'Delivery Date',
        ];
    }


    /**
     * Method to return the array of data.
     */
    public function array(): array
    {
        return $this->prepareData($this->procurements);
    }

    /**
     * Prepare the rows before exporting.
     */
    public function prepareData($procurements): array
    {
        $rows = [];

        try {
            foreach ($procurements as $area => $areaItem) {
                // Add a row for the area
                $rows[] = ['area' => $area]; // Adjust how the area is stored
                foreach ($areaItem as $item) {
                    $row = [];
                    $row['product_name'] = $item?->product?->product_name;
                    $row['description'] = $item?->product?->description;
                    $row['sku'] = $item?->product?->sku;
                    $row['brand_name'] = $item?->product?->brand_name;
                    $row['product_url'] = $item?->product?->product_url;
                    $row['height'] = $item?->product?->height;
                    $row['depth'] = $item?->product?->depth;
                    $row['width'] = $item?->product?->width;
                    $row['length'] = $item?->product?->length;
                    $row['color'] = $item?->product?->color;
                    $row['finish'] = $item?->product?->finish;
                    $row['material'] = $item?->product?->material;
                    $row['quantity'] = $item?->quantity;
                    $row['category'] = $item?->product?->category?->name;
                    $row['supplier_company_name'] = $item?->product?->supplier?->company_name;
                    $row['supplier_name'] = $item?->product?->supplier?->name;
                    $row['supplier_email'] = $item?->product?->supplier?->email;
                    $row['supplier_phone_number'] = $item?->product?->supplier?->phone_number;
                    $row['supplier_address'] = $item?->product?->supplier?->address;
                    $row['supplier_timezone'] = $item?->product?->supplier?->timezone;
                    $row['supplier_trade_discount'] = $item?->product?->supplier?->trade_discount;
                    $row['supplier_website'] = $item?->product?->supplier?->website;
                    $row["exchange_rate"] = $item?->exchange_rate;
                    $row["retail_price"] = $item?->retail_price;
                    $row["trade_discount"] = $item?->trade_discount;
                    $row["trade_price"] = $item?->trade_price;
                    $row["budget"] = $item?->budget;
                    $row["actual_price"] = $item?->actual_price;
                    $row["trade_terms"] = $item?->trade_terms;
                    $row["production_lead_time"] = $item?->production_lead_time;
                    $row["shipping_lead_time"] = $item?->shipping_lead_time;
                    $row["order_date"] = $item?->order_date;
                    $row["shipping_date"] = $item?->shipping_date;
                    $row["delivery_date"] = $item?->delivery_date;

                    $rows[] = $row;
                }
            }
        } catch (\Exception $e) {
            // Handle exception if necessary, or log the error
            Log::info('Error in preparing data in schedule export sheet: ' . $e->getMessage()); // Uncomment for debugging
        }

        return $rows;
    }
}
