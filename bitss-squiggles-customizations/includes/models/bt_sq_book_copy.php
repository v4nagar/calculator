<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Bitss_Squiggles_Book_Copy {

    public string $woocommerce_product_id;
    public string $copy_post_id;
    public string $copy_smart_id;
    public string $copy_condition;
    public string $binding;
    public string $shelf_id;
    public string $invoice_number;
    public string $invoice_line_number;
    public string $purchase_price;
    public string $mrp;
    public string $invoice_date;
    public string $entry_date;
    public string $purchase_condition;
    public string $on_hold;
    public string $comments;
    public array $images;


    public function __construct( ) {
        $this->images=[];
	}

    public static  function GetCopyByID(int $product_id,int $copy_id){
        $b = new Bitss_Squiggles_Book_Copy();

        $product = wc_get_product(  $product_id );

        if ( has_term( 'book', 'product_cat',$product_id ) ) {

            
            $b->woocommerce_product_id = $product_id;
            $b->copy_post_id = $copy_id;
            $b->copy_smart_id = get_post_meta($copy_id,'wpcf-copy-id',true);
            $b->copy_condition = get_post_meta($copy_id,'wpcf-copy-condition',true);
            $b->binding = get_post_meta($copy_id,'wpcf-binding',true);
            $b->shelf_id = get_post_meta($copy_id,'wpcf-shelf-id',true);
            $b->invoice_number = get_post_meta($copy_id,'wpcf-invoice-number',true);
            $b->invoice_line_number = get_post_meta($copy_id,'wpcf-invoice-line-item-number',true);
            $b->purchase_price = get_post_meta($copy_id,'wpcf-purchase-price',true);
            $b->mrp = get_post_meta($copy_id,'wpcf-copy-mrp',true);

            $b->purchase_condition = get_post_meta($copy_id,'wpcf-purchase-condition',true);
            $b->on_hold = get_post_meta($copy_id,'wpcf-on-hold',true);
            $b->comments = get_post_meta($copy_id,'wpcf-copy-comments',true);
            $b->binding = get_post_meta($copy_id,'wpcf-binding',true);

            $entry_date = get_post_meta($copy_id,'wpcf-entry-date',true);
			if(!empty($entry_date)){
					$entry_date = date('d/m/y',$entry_date);
			}
            $b->entry_date = $entry_date;

			$invoice_date = get_post_meta($copy_id,'wpcf-invoice-date',true);
			if(!empty(trim($invoice_date)) && is_numeric($invoice_date)){
					try{
					$invoice_date = date('d/m/y',$invoice_date);
					}catch(Exception $e){
					//	echo $invoice_date;
					//	exit;
					}
			}
            $b->invoice_date = $invoice_date;

        }

        return $b;

    }



}
