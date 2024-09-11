<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Bt_Squiggles_Isbndb {

    private const API_BASE_URL = "https://api2.isbndb.com";
    private const API_SEARCH_ISBN = "/book/";
    private const API_SEARCH_TITLE = "/books/";
    private $auth_token;


	public function __construct() {
        $this->auth_token = "50736_272ee9c2c0a48bf0ad62c74652226ccf";
    }


    
    public function get_book_by_isbn($isbn){
        
        if(!empty($this->auth_token) && !empty($isbn)){

            $args = array(
                'headers'     => array(
                    'Authorization' =>  $this->auth_token,
                ),
            );

            $response = wp_remote_get( self::API_BASE_URL . self::API_SEARCH_ISBN . $isbn, $args );
           
            $body     = wp_remote_retrieve_body( $response );

            $resp = json_decode($body,true);
            
            
            if(isset($resp["book"])){
                $b= $this->get_book_object($resp["book"]);
                return $b;
            }

        }else{
            return null;
        }


    }

    public function get_book_by_title($title){
        
        $books = [];
        
        if(!empty($this->auth_token) && !empty($title)){

            $args = array(
                'headers'     => array(
                    'Authorization' =>  $this->auth_token,
                ),
            );

            $url = self::API_BASE_URL . self::API_SEARCH_TITLE . $title;

            $url = $url . "?column=title&page=1&pageSize=20"; 

            $response = wp_remote_get($url , $args );
           
            $body     = wp_remote_retrieve_body( $response );

            $resp = json_decode($body,true);
            
           
            if(isset($resp["books"]) && is_array($resp["books"])){
                
                foreach ($resp["books"]as $bk) {
                    $books[] = $this->get_book_object($bk);
                }

             
            }

        }else{
          
        }

        return $books;
    }

    private function get_book_object($data){
        $b= new Bitss_Squiggles_Book("");
        $b->source = "isbndb"; 

        if(isset($data["title_long"])){
            $b->book_title = $data["title_long"];
        }

        if(isset($data["isbn13"])){
            $b->isbn13 = $data["isbn13"];
        }

        if(isset($data["isbn"])){
            $b->isbn10 = $data["isbn"];
        }

        if(isset($data["dewey_decimal"])){
            $b->dewey_code = $data["dewey_decimal"];
        }

        if(isset($data["synopsis"])){
            $b->description = $data["synopsis"];
        }

        if(isset($data["binding"])){
            $binding = $data["binding"]; //not used at product level
        }

        $b->publishers = [];
        if(isset($data["publisher"])){
            $b->publishers[] = array(
                "name" =>  $data["publisher"],
                "description" => "",
                "image_url" => ""
            ); 
        }

        $b->book_languages = [];
        if(isset($data["language"])){
            $lang = "English";
            if($data["language"]=="en"){
                $lang = "English";
            }
            else if($data["language"]=="hn"){
                $lang = "Hindi";
            }
            $b->book_languages[] = array(
                "name" =>  $lang,
                "description" => "",
                "image_url" => ""
            ); 
        }
        
        if(isset($data["date_published"])){

            $dateString = $data["date_published"];

            $dateTimeObject = $this->convertToDateObject($dateString);
            if ($dateTimeObject) {
                $b->publication_date = $dateTimeObject->format(DateTime::ISO8601); 
            }

            
        }

        if(isset($data["edition"])){
            $b->edition = $data["edition"];
        }

        if(isset($data["pages"])){
            $b->pages = $data["pages"];
        }

        if(isset($data["synopsis"])){
            $b->description = $data["synopsis"];
        }

        if(isset($data["msrp"])){
            $b->mrp = 'USD $' . $data["msrp"];
        }

        if(isset($data["dimensions"])){
            $dimensions= $data["dimensions"];
        }

        if(isset($data["dimensions_structured"])){
            $length = 0;
            $width = 0;
            $height = 0;
            if(isset($data["dimensions_structured"]["length"]["value"])){
                $length = $data["dimensions_structured"]["length"]["value"];
            }
            if(isset($data["dimensions_structured"]["width"]["value"])){
                $width = $data["dimensions_structured"]["width"]["value"];
            }
            if(isset($data["dimensions_structured"]["height"]["value"])){
                $height = $data["dimensions_structured"]["height"]["value"];
            }

            $b->length = round($length*2.54,2);
            $b->width  = round($width*2.54,2);
            $b->height = round($height*2.54,2);

        }

        if(isset($data["overview"])){
            $b->description = $data["overview"];
        }

        if(isset($data["excerpt"])){
            $b->description = $data["excerpt"];
        }

        
        if(isset($data["overview"])){
            $b->description = $data["overview"];
        }

        if(isset($data["image"])){
            $b->image_url = $data["image"];
        }

        $b->authors = [];
        if(isset($data["image"])){
            $authors = $data["authors"];
            if(is_array($authors)){
                foreach ($authors as $author_name) {
                    $b->authors[] = array(
                        "name" => $author_name,
                        "description" => "",
                        "image_url" => ""
                    ); 
    
                }
            }
        }

        $b->book_subjects = [];
        if(isset($data["subjects"])){
            $book_subjects = $data["subjects"];
            if(is_array($book_subjects)){
                foreach ($book_subjects as $sub) {
                    $b->book_subjects[] = array(
                        "name" => $sub,
                        "description" => "",
                        "image_url" => ""
                    ); 
    
                }
            }
        }

       

        return $b;
    }

    private function convertToDateObject($dateString) {
        // Define an array of date formats that you expect
        $formats = [
            'Y-m-d', // e.g., 2024-02-27
            'm/d/Y', // e.g., 02/27/2024
            'd-m-Y', // e.g., 27-02-2024
            'd/m/Y', // e.g., 27/02/2024
            'Y'
            // Add more formats as needed
        ];

        foreach ($formats as $format) {
            $date = DateTime::createFromFormat($format, $dateString);
            if ($date && $date->format($format) === $dateString) {
                return $date;
            }
        }

        // Return null or handle the error if the format is not recognized
        return null;
    }


}
