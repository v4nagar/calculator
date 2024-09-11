<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Bitss_Squiggles_Book {


    public string $book_title;
    public string $smart_product_id;

    
    public string $image_url;
    public string $source;
    public string $description;
  
    //copy post ids
    public array $copy_ids;

    public array $copy_list;

    public array $authors;
    public array $publishers;

    public array $book_categories;
    public array $book_tags;
    public array $book_characters;
    public array $book_genres;
    public array $book_subjects;
    public array $book_series;
    public array $book_languages;

    public string $edition;
    public int $credits;
    public string $mrp;
    public string $height;
    public string $width;
    public string $length;
    public string $weight;
    public string $min_age;
    public string $max_age;
    public string $pages;
    public string $country_of_origin;
    public string $isbn10;
    public string $isbn13;
    public string $dewey_code;
    public string $lexile_code;
	public string $illustrator;
    
    public string $product_classification;
    public string $product_type;
    public string $product_sub_type;

    public string $publication_date;


    public string $woocommerce_product_id;
    public string $product_url;

    public function __construct(  string $book_title ) {
        $this->book_title = $book_title;

         $this->copy_ids=[];
         $this->copy_list=[];

         $this->authors=[];
         $this->publishers=[];

         $this->book_categories=[];
         $this->book_tags=[];
         $this->book_characters=[];
         $this->book_genres=[];
         $this->book_subjects=[];
         $this->book_series=[];
         $this->book_languages=[];

	}

    public static  function GetBookByProductId(int $product_id){
        $book = null;

        $product = wc_get_product(  $product_id );

        if ( has_term( 'book', 'product_cat',$product_id ) ) {
            $book = Bitss_Squiggles_Book::GetBookFromProduct( $product);

        }

        return $book;

    }

    public static  function GetBookFromProduct(WC_Product $product){
        $b= new Bitss_Squiggles_Book("");
        $b->source = "woocommerce"; 
        $b->book_title = $product->get_title();
        $b->woocommerce_product_id = $product->get_id();
        $b->description = $product->get_description();
        
        $b->isbn13 =  get_post_meta( $product->get_id(), 'wpcf-'.'isbn13',true);
        $featured_image_id = $product->get_image_id();
        $b->image_url = wp_get_attachment_image_url($featured_image_id, 'full');

        $book_shelf_obj = new Bitss_Squiggles_Customizations_Book_Shelf( "", "" );
        $b->copy_ids = $book_shelf_obj->find_available_copies_of_book($product->get_id());
        foreach( $b->copy_ids as $pid){
            $b->copy_list[] =  Bitss_Squiggles_Book_Copy::GetCopyByID($product->get_id(),$pid);
        }

        $b->authors = [];
        $authors = get_the_terms( $product->get_id(), 'book-author' );	
        if(is_array($authors)){
            foreach ($authors as $key => $term) {
                $author_name = $term->name;
                $author_description =$term->description;
                $a_image_url = get_term_meta($term->term_id,'wpcf-'.'author-image',true);
                if(empty($a_image_url)){
                    $a_image_url = "/wp-content/uploads/2021/08/woocommerce-placeholder.png";
                }
                $b->authors[] = array(
                    "name" => $author_name,
                    "description" => $author_description,
                    "image_url" => $a_image_url
                ); 

            }
        }

        $b->publishers = [];
        $publishers = get_the_terms( $product->get_id(), 'book-publisher' );	
        if(is_array($publishers)){
            foreach ($publishers as $key => $term) {
                $name = $term->name;
                $description =$term->description;
                $a_image_url = get_term_meta($term->term_id,'wpcf-'.'publisher-image',true);
                if(empty($a_image_url)){
                    $a_image_url = "/wp-content/uploads/2021/08/woocommerce-placeholder.png";
                }
                $b->publishers[] = array(
                    "name" => $name,
                    "description" => $description,
                    "image_url" => $a_image_url
                ); 

            }
        }

        $b->book_categories = [];
        $book_categories = get_the_terms( $product->get_id(), 'product_cat' );	
        if(is_array($book_categories)){
            foreach ($book_categories as $key => $term) {
                $name = $term->name;
                $description =$term->description;
                $b->book_categories[] = array(
                    "name" => $name
                ); 
            }
        }

        $b->book_tags = [];
        $book_tags = get_the_terms( $product->get_id(), 'product_tag' );	
        if(is_array($book_tags)){
            foreach ($book_tags as $key => $term) {
                $name = $term->name;
                $description =$term->description;
                $b->book_tags[] = array(
                    "name" => $name
                ); 
            }
        }

        $b->book_characters = [];
        $book_characters = get_the_terms( $product->get_id(), 'character' );	
        if(is_array($book_characters)){
            foreach ($book_characters as $key => $term) {
                $name = $term->name;
                $description =$term->description;
                $b->book_characters[] = array(
                    "name" => $name
                ); 
            }
        }

        $b->book_genres = [];
        $book_genres = get_the_terms( $product->get_id(), 'book-genre' );	
        if(is_array($book_genres)){
            foreach ($book_genres as $key => $term) {
                $name = $term->name;
                $description =$term->description;
                $b->book_genres[] = array(
                    "name" => $name
                ); 
            }
        }

        $b->book_subjects = [];
        $book_subjects = get_the_terms( $product->get_id(), 'book-subject' );	
        if(is_array($book_subjects)){
            foreach ($book_subjects as $key => $term) {
                $name = $term->name;
                $description =$term->description;
                $b->book_subjects[] = array(
                    "name" => $name
                ); 
            }
        }

        $b->book_series = [];
        $book_series = get_the_terms( $product->get_id(), 'product-series' );	
        if(is_array($book_series)){
            foreach ($book_series as $key => $term) {
                $name = $term->name;
                $description =$term->description;
                $b->book_series[] = array(
                    "name" => $name
                ); 
            }
        }
        
        $b->book_languages = [];
        $book_languages = get_the_terms( $product->get_id(), 'book-language' );	
        if(is_array($book_languages)){
            foreach ($book_languages as $key => $term) {
                $name = $term->name;
                $description =$term->description;
                $b->book_languages[] = array(
                    "name" => $name
                ); 
            }
        }

        $b->pages = get_post_meta($product->get_id(),'wpcf-'.'pages',true);
        $b->edition = get_post_meta($product->get_id(),'wpcf-'.'edition',true);
        if(!empty($product->get_price())){
            $b->credits = $product->get_price();
        }
        $b->mrp = get_post_meta($product->get_id(),'wpcf-'.'mrp',true);

        $b->height =  $product->get_height();
        $b->width =   $product->get_width();
        $b->length =  $product->get_length();
        $b->weight =  $product->get_weight();

        $b->smart_product_id = get_post_meta($product->get_id(),'wpcf-'.'smart_product_id',true);

        $b->min_age = get_post_meta($product->get_id(),'wpcf-'.'min-age',true);
        $b->max_age = get_post_meta($product->get_id(),'wpcf-'.'max-age',true);

        $b->country_of_origin = get_post_meta($product->get_id(),'wpcf-'.'country-of-print',true);
        $b->isbn10 = get_post_meta($product->get_id(),'wpcf-'.'isbn10',true);
        $b->isbn13 = get_post_meta($product->get_id(),'wpcf-'.'isbn13',true);

        $b->dewey_code = get_post_meta($product->get_id(),'wpcf-'.'dewey-decimal',true);
        $b->lexile_code = get_post_meta($product->get_id(),'wpcf-'.'lexile-code',true);
		$b->illustrator = get_post_meta($product->get_id(),'wpcf-'.'illustrator',true);

        $b->publication_date = get_post_meta($product->get_id(),'wpcf-'.'publication-date',true);
        $b->product_classification = get_post_meta($product->get_id(),'wpcf-'.'product-classification',true);
        $b->product_type = get_post_meta($product->get_id(),'wpcf-'.'product-type',true);
        $b->product_sub_type = get_post_meta($product->get_id(),'wpcf-'.'product-sub-type',true);

        $b->product_url =  get_permalink( $product->get_id() );

        return  $b;
    }


    public static function CreateBook(array $jsonArray){

        $product = new WC_Product_Simple();

        $product->set_name($jsonArray["book_title"]); // product title

        $product->set_regular_price($jsonArray["credits"]); // in current shop currency

        $product->set_description( $jsonArray["description"] );

        $product_catids = [];
        $arr = $jsonArray["book_categories"];
        if(is_array( $arr) && sizeof( $arr)>0){
            foreach ($arr as $key => $value) {
                $product_catids[] = $value["term_id"];
            }
        }
        $product->set_category_ids(  $product_catids);

        $product_tags = [];
        $arr = $jsonArray["book_tags"];
        if(is_array( $arr) && sizeof( $arr)>0){
            foreach ($arr as $key => $value) {
                if(isset($value["term_id"]) && $value["term_id"]>0){
                    $product_tags[] = $value["term_id"];
                } else if (isset($value["_addnew"]) && $value["_addnew"]==true){
                    $term_id = Bitss_Squiggles_Book::add_new_term_to_taxonomy('product_tag',$value["name"],"");
                    $product_tags[] = $term_id;
                }
            }
        }
        $product->set_tag_ids($product_tags);

        $product->set_height($jsonArray["height"]);
        $product->set_width($jsonArray["width"]);
        $product->set_length($jsonArray["length"]);
        $product->set_weight($jsonArray["weight"]);

        $product->save();

        $product_id = $product->get_id();

        Bitss_Squiggles_Book::SaveProductTerms( $product_id, $jsonArray["book_tags"] , "book-author" );
        Bitss_Squiggles_Book::SaveProductTerms( $product_id, $jsonArray["authors"] , "book-author" );
        Bitss_Squiggles_Book::SaveProductTerms( $product_id, $jsonArray["publishers"] , "book-publisher" );
        Bitss_Squiggles_Book::SaveProductTerms( $product_id, $jsonArray["book_characters"] , "character" );
        Bitss_Squiggles_Book::SaveProductTerms( $product_id, $jsonArray["book_genres"] , "book-genre" );
        Bitss_Squiggles_Book::SaveProductTerms( $product_id, $jsonArray["book_subjects"] , "book-subject" );
        Bitss_Squiggles_Book::SaveProductTerms( $product_id, $jsonArray["book_series"] , "product-series" );
        Bitss_Squiggles_Book::SaveProductTerms( $product_id, $jsonArray["book_languages"] , "book-language" );

        $book_smart_id = Bitss_Squiggles_Book::GetNewBookSmartID();
        update_post_meta( $product_id, 'wpcf-'.'smart_product_id', $book_smart_id );
        update_post_meta( $product_id, 'wpcf-'.'pages', wc_clean( $jsonArray["pages"]  ) );
        update_post_meta( $product_id, 'wpcf-'.'edition', wc_clean( $jsonArray["edition"]  ) );
        update_post_meta( $product_id, 'wpcf-'.'mrp', wc_clean( $jsonArray["mrp"]  ) );
        update_post_meta( $product_id, 'wpcf-'.'min-age', wc_clean( $jsonArray["min_age"]  ) );
        update_post_meta( $product_id, 'wpcf-'.'max-age', wc_clean( $jsonArray["max_age"]  ) );
        update_post_meta( $product_id, 'wpcf-'.'country-of-print', wc_clean( $jsonArray["country_of_origin"]  ) );
        update_post_meta( $product_id, 'wpcf-'.'isbn10', wc_clean( $jsonArray["isbn10"]  ) );
        update_post_meta( $product_id, 'wpcf-'.'isbn13', wc_clean( $jsonArray["isbn13"]  ) );
        update_post_meta( $product_id, 'wpcf-'.'dewey-decimal', wc_clean( $jsonArray["dewey_code"]  ) );
        update_post_meta( $product_id, 'wpcf-'.'lexile-code', wc_clean( $jsonArray["lexile_code"]  ) );
		update_post_meta( $product_id, 'wpcf-'.'illustrator', wc_clean( $jsonArray["illustrator"]  ) );
        update_post_meta( $product_id, 'wpcf-'.'publication-date', strtotime(wc_clean( $jsonArray["publication_date"]  ) ));
        update_post_meta( $product_id, 'wpcf-'.'product-classification', wc_clean( $jsonArray["product_classification"]  ) );
        update_post_meta( $product_id, 'wpcf-'.'product-type', wc_clean( $jsonArray["product_type"]  ) );
        update_post_meta( $product_id, 'wpcf-'.'product-sub-type', wc_clean( $jsonArray["product_sub_type"]  ) );

        // if(isset( $jsonArray["product_image_base64"]) && !empty( $jsonArray["product_image_base64"])){
        //     $base64_image = $jsonArray["product_image_base64"];
        //     $attachment_id = Bitss_Squiggles_Book::set_product_image_from_base64($product_id, $base64_image);
        //     $product->set_image_id(  $attachment_id);
        // }

        if(isset( $jsonArray["image_id"]) && $jsonArray["image_id"]>0){
            set_post_thumbnail($product_id, $jsonArray["image_id"]);
            $product->set_image_id($jsonArray["image_id"]);
        }else if(isset( $jsonArray["image_url"])){
			$attachment_id = media_sideload_image($jsonArray["image_url"], $product_id, $jsonArray["book_title"],'id');
            if ($attachment_id) {
				set_post_thumbnail($product_id, $attachment_id);
				 $product->set_image_id($attachment_id);
			}
           
        }


        return $product;

    }

    private static function SaveProductTerms(int $product_id,array $arr,string $taxonomy_name){
        $authors = [];
        if(is_array( $arr) && sizeof( $arr)>0){
            foreach ($arr as $key => $value) {
                if(isset($value["term_id"]) && $value["term_id"]>0){
                    $authors[] = $value["term_id"];
                } else if (isset($value["_addnew"]) && $value["_addnew"]==true){
                    $term_id = Bitss_Squiggles_Book::add_new_term_to_taxonomy($taxonomy_name,$value["name"],"");
                    $authors[] = $term_id;
                }
            }
        }
        wp_set_object_terms( $product_id,  $authors, $taxonomy_name );
    }

    private static function add_new_term_to_taxonomy( $taxonomy,$term_name,$term_description) {
    
        // Check if the term already exists
        $term = term_exists( $term_name, $taxonomy );
    
        // If the term does not exist, add it
        if ( !$term ) {
            $term = wp_insert_term(
                $term_name, // the term
                $taxonomy, // the taxonomy
                array(
                    'description'=> $term_description,
                )
            );
            return $term["term_id"];
        }else{
            return $term["term_id"];
        }
    }



    private static function Set_product_image_from_base64($product_id, $base64_image) {
        // Decode base64 string to image data
        $image_data = base64_decode($base64_image);
    
        // Create a unique filename for the image
        $filename = $product_id . '_product_image.jpg';
    
        // Get the uploads directory path
        $upload_dir = wp_upload_dir();
    
        // Set the full path for the image
        $image_path = $upload_dir['path'] . '/' . $filename;
    
        // Save the image file to the uploads directory
        file_put_contents($image_path, $image_data);
    
        // Set the image as the product featured image
        $attachment_id = wp_insert_attachment(array(
            'post_title'     => '',
            'post_mime_type' => 'image/jpeg',
            'post_status'    => 'inherit',
        ), $image_path, $product_id);
    
        // Set the product thumbnail
        set_post_thumbnail($product_id, $attachment_id);
        return  $attachment_id;
    }


    private static function Set_product_image_from_base64_2($product_id, $base64_image) {
        // Decode base64 string
        $decoded_image = base64_decode($base64_image);
      
        // Extract image data and MIME type
        preg_match('/^data:(image\/[^\;]+);base64,(.+)$/', $base64_image, $matches);
        $mime_type = $matches[1];
      
        // Create temporary file
        $temp_file = wp_tempnam('product_image');
        file_put_contents($temp_file, $decoded_image);
       
        // Upload temporary file to media library
        $media_id = media_handle_upload(array(
          'file' => $temp_file,
          'filename' => $product_id . '_product_image.jpg', // You can set a custom filename
          'type' => "image/jpeg",
        ),$product_id);
      
        // Remove temporary file
        unlink($temp_file);
      
        // Check for upload error
        if (is_wp_error($media_id)) {
            echo  $media_id->get_error_message();exit;
          return $media_id->get_error_message();
        }
      
        // Set the product image
        update_post_meta($product_id, '_thumbnail_id', $media_id);
      
    }   

    public static function AddCopy(int $product_id, array $data){

        $product = wc_get_product(  $product_id );

        if ( has_term( 'book', 'product_cat',$product_id ) && !empty($data)) {
            
           
            if(empty($data['copy_smart_id'])){
                $book_smart_id = get_post_meta($product_id,'wpcf-'.'smart_product_id' ,true);
                if(!empty($book_smart_id)){
                    $query = new WP_Query( 
                        array(
                            'post_type' => 'copy', //Child post type slug
                            'numberposts' => -1,
                            'toolset_relationships' => array(
                                'role' => 'child',
                                'related_to' => $product_id, // ID of starting post
                                'relationship' =>'copy',
                            ),
                        )
                    );
                    $copy_posts = $query->posts;
                    $existing_copy_count = sizeof( $copy_posts);
                    $formatted_string = sprintf("%02d", $existing_copy_count+1);
                    $data['copy_smart_id'] =  $book_smart_id .  $formatted_string ;
                }
                
            }
            $bono = array(
                'post_type'    => 'copy',
                'post_title'    => $data['copy_smart_id'],
                'post_status'   => 'publish',
            );
            
            $postid=wp_insert_post( $bono );
            add_post_meta( $postid, 'wpcf-'.'copy-id', $data['copy_smart_id'], true);
            add_post_meta( $postid, 'wpcf-'.'copy-comments', $data['comments'], true);
            add_post_meta( $postid, 'wpcf-'.'copy-condition', $data['copy_condition'], true);
            add_post_meta( $postid, 'wpcf-'.'purchase-condition', $data['purchase_condition'], true);
            add_post_meta( $postid, 'wpcf-'.'copy-mrp', $data['mrp'], true);
            add_post_meta( $postid, 'wpcf-'.'purchase-price', $data['purchase_price'], true);
            add_post_meta( $postid, 'wpcf-'.'shelf-id', $data['shelf_id'], true);
            add_post_meta( $postid, 'wpcf-'.'binding', $data['binding'], true);
            add_post_meta( $postid, 'wpcf-'.'invoice-date', strtotime($data['invoice_date']), true);
            add_post_meta( $postid, 'wpcf-'.'invoice-line-item-number', $data['invoice_line_number'], true);
            add_post_meta( $postid, 'wpcf-'.'invoice-number', $data['invoice_number'], true);
            //entry-date
            //on-hold
            add_post_meta( $postid, 'wpcf-'.'entry-date', strtotime($data['entry_date']), true);

            $onhold= "No";
            if($data['on_hold']==true){
                $onhold= "Yes";
            }
            add_post_meta( $postid, 'wpcf-'.'on-hold',$onhold, true);

            if(isset( $data["image_url"]) && !empty($data["image_url"])){
                //set_post_thumbnail($postid, $data["image_id"]);
                $bono = array(
                    'post_type'    => 'latest-pictures',
                    'post_title'    => "Copy Image",
                    'post_status'   => 'publish',
                );
                
                $picture_postid = wp_insert_post( $bono );

                add_post_meta( $picture_postid, 'wpcf-'.'picture', $data['image_url'], true);

                toolset_connect_posts( 'latest-pictures', $postid,$picture_postid);
               
            }


            toolset_connect_posts( 'copy', $product_id,$postid);

            return array(
                "copy_id" => $postid,
                "smart_copy_id" =>  $data['copy_smart_id']     
            );
        }
        return 0;
    }

    public static function GetNewBookSmartID(){
        global $wpdb;
        $table_name = $wpdb->prefix . 'postmeta'; // Replace with the actual table name

        $query = "SELECT max(meta_value) AS min_value FROM $table_name WHERE `meta_key` LIKE '%smart_product_id%' and meta_value>1000000;";
      //  echo $query;exit;
        $min_value = $wpdb->get_var($query);

        if ($min_value !== null) {
            return $min_value+1;
        } else {
            return 0;
        }
    }
}

