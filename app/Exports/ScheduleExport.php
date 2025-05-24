<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\Log;

class ScheduleExport implements FromArray, WithHeadings
{
    private $schedules;

    public function __construct($schedules)
    {
        $this->schedules = $schedules;
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
            'Supplier Profile Picture',
        ];
    }


    /**
     * Method to return the array of data.
     */
    public function array(): array
    {
        return $this->prepareData($this->schedules);
    }

    /**
     * Prepare the rows before exporting.
     */
    public function prepareData($schedules): array
    {
        $rows = [];

        try {
            foreach ($schedules as $category => $categoryItems) {
                // Add a row for the category
                $rows[] = ['category' => $category]; // Adjust how the category is stored

                foreach ($categoryItems as $item) {
                    $row = [];
                    $row['item_name'] = $item?->material?->item_name;
                    $row['description'] = $item?->material?->description;
                    $row['sku'] = $item?->material?->sku;
                    $row['brand_name'] = $item?->material?->brand_name;
                    $row['doc_code'] = $item?->material?->doc_code;
                    $row['product_url'] = $item?->material?->product_url;
                    $row['height'] = $item?->material?->height;
                    $row['depth'] = $item?->material?->depth;
                    $row['width'] = $item?->material?->width;
                    $row['length'] = $item?->material?->length;
                    $row['color'] = $item?->material?->color;
                    $row['finish'] = $item?->material?->finish;
                    $row['quantity'] = $item?->quantity;
                    $row['category'] = $item?->material?->category?->name;
                    $row['supplier_company_name'] = $item?->material?->supplier?->company_name;
                    $row['supplier_name'] = $item?->material?->supplier?->name;
                    $row['supplier_email'] = $item?->material?->supplier?->email;
                    $row['supplier_phone_number'] = $item?->material?->supplier?->phone_number;
                    $row['supplier_address'] = $item?->material?->supplier?->address;
                    $row['supplier_timezone'] = $item?->material?->supplier?->timezone;
                    $row['supplier_trade_discount'] = $item?->material?->supplier?->trade_discount;
                    $row['supplier_website'] = $item?->material?->supplier?->website;
                    $row['supplier_profile_picture'] = $item?->material?->supplier?->profile_picture;

                    $rows[] = $row;
                }
            }
        } catch (\Exception $e) {
            // Handle exception if necessary, or log the error
            Log::info('Error in preparing data in schedule export sheet: '.$e->getMessage()); // Uncomment for debugging
        }

        return $rows;
    }
}
