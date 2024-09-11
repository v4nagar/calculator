var app = angular.module('myApp', ["isteven-multi-select"]);


app.controller('new_product', function($scope, $http, $window,$timeout) {
    $scope.product = {
        product_upload: null,
        product_image_base64: '',
        book_categories:[],
        book_tags :"",
        book_characters:"",
        book_genres :"",
        book_subjects :"",
        book_series :"",
        book_languages :"",
        publishers :"",

        book_title:"",
        description:"",
        height :"",
        width :"",
        min_age :"",
        max_age:"",
        isbn10:"",
        isbn13 :"",
        product_option1 :"Product Classification",
        product_option2 :"Product Type",
        product_option3 :"Product Sub Type",
        authors:"",

        edition:"",
        credit :"",
        mrp:"",
        length:"",
        weight :"",
        pages:"",
        country_of_origin :"",
        dewey_code :"",
        lexile_code :"",
        publication_date : "",
    };

    $scope.copy = {
      
      images: '',
      woocommerce_product_id: "",
      copy_post_id: "",
      copy_smart_id: "",
      binding: "",
      invoice_line_number: "",
      invoice_number: "",
      shelf_id: "",
      invoice_date: "",
      entry_date: "",
      purchase_condition: "Purchase Condition",
      copy_condition: "Copy Condition",
      purchase_price: "",
      mrp: "",
      on_hold: "",
      comments: "",
  
    };

    $scope.sources = []; 
    $scope.isbnSearch = ''; 
    $scope.isSearchloading = false;
    $scope.isError = false;
    $scope.errorMessage = '';
    $scope.resultsCount = 0;

    $scope.pageStack = [];
    $scope.current_page = "search_page";
    $scope.previous_page = "";


    $scope.navigate_page = function (new_page) {
      $scope.pageStack.push($scope.current_page); // Push the current page
      $scope.previous_page = $scope.current_page;
      $scope.current_page = new_page;
      if(new_page == "product_detail"){
        $scope.pageStack=[];
      }
    }

    $scope.SelectFile = function(event) {
      var input = event.target;
      console.log(e.target.files[0]);
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $scope.product_image = e.target.result;
                $scope.$apply();
            };
            reader.readAsDataURL(input.files[0]);
        }
    };

    $scope.cat_media_upload = function () {
        // Create a proper popup for selecting an image
        var frame = wp.media({
            title:    'Select image',
            multiple: false,
            button: {
                text: 'Use this image'
            }
        });

        // Add a callback for when an item is selected
        frame.state( 'library' ).on( 'select', function(){
            var image = this.get( 'selection' ).first();
            // Inspect the image variable further
            //console.log( image.toJSON() );
            // Save the actual URL within the input
            $scope.$apply(function(){
              $scope.product.image_url = image.get( 'url' );;
              $scope.product.image_id = image.get( 'id' );
            });
        });

        // Finally, open the frame
        frame.open();
    }

    $scope.copy_media_upload = function () {
      // Create a proper popup for selecting an image
      var frame = wp.media({
          title:    'Select image',
          multiple: false,
          button: {
              text: 'Use this image'
          }
      });

      // Add a callback for when an item is selected
      frame.state( 'library' ).on( 'select', function(){
          var image = this.get( 'selection' ).first();
          // Inspect the image variable further
          //console.log( image.toJSON() );
          // Save the actual URL within the input
          $scope.$apply(function(){
            $scope.copy.image_url = image.get( 'url' );;
            $scope.copy.image_id = image.get( 'id' );
          });
      });

      // Finally, open the frame
      frame.open();
  }
	
    $scope.navigate_back = function () {
        if ($scope.pageStack.length > 0) {
            $scope.current_page = $scope.pageStack.pop(); // Pop the last page
        } else {
            $scope.current_page = ""; 
        }
    }

    $scope.searchISBN = function () {

      var invoice_data = $scope.searchCSVbyISBN ( $scope.isbnSearch);
     // console.log(invoice_data);
      //return;

      $scope.isError = false;
      $scope.errorMessage = '';
      $scope.resultsCount = 0;
      $scope.isSearchloading = true;
      var apiUrl = '/wp-admin/admin-ajax.php?action=phase_3_search_isbn';
      var requestData = {
        isbn: $scope.isbnSearch,
      };

    $http.post(apiUrl, requestData)
      .then(function (response) {
          $scope.isSearchloading = false;
          $scope.sources = [...invoice_data, ...response.data.data];
          $scope.resultsCount = $scope.sources ;
          if ($scope.sources.length > 0) {
            
          } else {
            $scope.isError = true;
            $scope.errorMessage = 'Product not found. Please try again.';
          }
      })
      .catch(function (error) {
          $scope.isSearchloading = false;
          $scope.isError = true;
          $scope.errorMessage = 'Error searching for product. Please try again.';
          console.error('Error:', error);
      });  
    };

    $scope.searchTitle = function () {

      var invoice_data = $scope.searchCSVbyTitle ( $scope.isbnTitle);

      $scope.isError = false;
      $scope.errorMessage = '';
      $scope.resultsCount = 0;
      $scope.isSearchloading = true;
      var apiUrl = '/wp-admin/admin-ajax.php?action=phase_3_search_title_author';
      var requestData = {
        title: $scope.isbnTitle,
      };

      $http.post(apiUrl, requestData)
        .then(function (response) {
          $scope.isSearchloading = false;
          $scope.sources = [...invoice_data, ...response.data.data];
          $scope.sources.sort((a, b) => {
            if (a.source == b.source) {
              return a.book_title > b.book_title;
            }else{
              if (a.source == "woocommerce") {
                return -1;
              }else{
                return 1;
              }
            }
          });
          $scope.resultsCount = $scope.sources.length ;
          if ($scope.sources.length > 0) {
            
          } else {
            $scope.isError = true;
            $scope.errorMessage = 'Product not found. Please try again.';
          }
        })
        .catch(function (error) {
            $scope.isSearchloading = false;
            $scope.isError = true;
            $scope.errorMessage = 'Error searching for product. Please try again.';
            console.error('Error:', error);
        });
    };

    $scope.product_detail = function () {
      console.log('Product Detail clicked');
      $scope.navigate_page("product_detail");
    };

    $scope.bt_isbn_search = function () {
        console.log('ISBN Search clicked');
        $scope.navigate_page("isbn_page");
    };

    $scope.bt_create_product = function(product_to_copy){
      var found=$scope.findProductInInventory(product_to_copy.isbn13);
      if(found){
        if(!confirm("This product already exist, are you sure you want to create another?")){
          return;
        }
      }
      $scope.resetProductform();
      $scope.product = angular.copy(product_to_copy);
      if($scope.product.publication_date){
        $scope.product.publication_date = new Date($scope.product.publication_date);
      }
      if($scope.product.book_categories.length<1){
        $scope.product.book_categories.push({name:"Book"});
      }
      console.log($scope.product);
     
      for (let index = 0; index <   $scope.all_categories.length; index++) {
        const element =   $scope.all_categories[index];
        var exist = $scope.product.book_categories.some(function (item) {
          return item['name'].toLowerCase() === element.name.toLowerCase();
        });
        if(exist){
          $scope.all_categories[index].ticked=true;
        }
      }

      for (let index = 0; index <   $scope.product_tags.length; index++) {
        const element =   $scope.product_tags[index];
        var exist = $scope.product.book_tags.some(function (item) {
          return item['name'].toLowerCase() === element.name.toLowerCase();
        });
        if(exist){
          $scope.product_tags[index].ticked=true;
        }
      }

      for (let index = 0; index <   $scope.all_characters.length; index++) {
        const element =   $scope.all_characters[index];
        var exist = $scope.product.book_characters.some(function (item) {
          return item['name'].toLowerCase() === element.name.toLowerCase();
        });
        if(exist){
          $scope.all_characters[index].ticked=true;
        }
      }

      for (let index = 0; index <   $scope.all_genres.length; index++) {
        const element =   $scope.all_genres[index];
        var exist = $scope.product.book_genres.some(function (item) {
          return item['name'].toLowerCase() === element.name.toLowerCase();
        });
        if(exist){
          $scope.all_genres[index].ticked=true;
        }
      }

      for (let index = 0; index <   $scope.all_subjects.length; index++) {
        const element =   $scope.all_subjects[index];
        var exist = $scope.product.book_subjects.some(function (item) {
          return item['name'].toLowerCase() === element.name.toLowerCase();
        });
        if(exist){
          $scope.all_subjects[index].ticked=true;
        }
      }

      for (let index = 0; index <   $scope.all_series.length; index++) {
        const element =   $scope.all_series[index];
        var exist = $scope.product.book_series.some(function (item) {
          return item['name'].toLowerCase() === element.name.toLowerCase();
        });
        if(exist){
          $scope.all_series[index].ticked=true;
        }
      }

      for (let index = 0; index <   $scope.all_languages.length; index++) {
        const element =   $scope.all_languages[index];
        var exist = $scope.product.book_languages.some(function (item) {
          return item['name'].toLowerCase() === element.name.toLowerCase();
        });
        if(exist){
          $scope.all_languages[index].ticked=true;
        }
      }

      for (let index = 0; index <   $scope.all_publishers.length; index++) {
        const element =   $scope.all_publishers[index];
        var exist = $scope.product.publishers.some(function (item) {
          return item['name'].toLowerCase() === element.name.toLowerCase();
        });
        if(exist){
          $scope.all_publishers[index].ticked=true;
        }
      }

      for (let index = 0; index <   $scope.all_authors.length; index++) {
        const element =   $scope.all_authors[index];
        var exist = $scope.product.authors.some(function (item) {
          return item['name'].toLowerCase() === element.name.toLowerCase();
        });
        if(exist){
          $scope.all_authors[index].ticked=true;
        }
      }

      $scope.navigate_page("product_page");
    }
	
    $scope.bt_create_copy = function(product_to_copy){
      if(product_to_copy.woocommerce_product_id == null){
        alert("Cannot create copy. Please add this book (via 'Create Product') to your inventory first.");
        return;
      }
      $scope.product = angular.copy(product_to_copy);
      var csvrow = $scope.product._csvrow;
      if(csvrow==null && $scope.product.isbn13){
        //search the csv using the isbn
        var csvproducts = $scope.searchCSVbyISBN ($scope.product.isbn13);
        if(csvproducts.length>0){
          csvrow = csvproducts[0]._csvrow;
        }
      
      }
      if(csvrow){
        $scope.product._csvrow = csvrow;
      }
      $scope.copy = {};
      $scope.copy.entry_date = new Date();
      $scope.copy.mrp = parseFloat($scope.product.mrp);
      if($scope.product._csvrow){
        $scope.copy.binding = $scope.product._csvrow[36];
        $scope.copy.invoice_line_number = $scope.product._csvrow[39];
        $scope.copy.invoice_number = $scope.product._csvrow[38];
        $scope.copy.shelf_id = $scope.product._csvrow[37];
        if($scope.product._csvrow[40]!=null && $scope.product._csvrow[40].trim()!=""){
          $scope.copy.invoice_date = new Date($scope.product._csvrow[40]);
        }
        $scope.copy.purchase_condition = $scope.product._csvrow[42];
        $scope.copy.copy_condition = $scope.product._csvrow[41];
        if($scope.product._csvrow[35]!=null && $scope.product._csvrow[35].trim()!=""){
          $scope.copy.purchase_price = parseFloat($scope.product._csvrow[35]);
        }
       
      }
      $scope.navigate_page("copy_page");
    }

    $scope.bt_save_product = function(){
      if($scope.product.book_categories.length==0){
        alert("Please select a category");
        return;
      }
      if(!confirm("Sure you want to save this book?")){
        return;
      }
      $scope.isSearchloading = true;
      console.log($scope.product);
      var apiUrl = '/wp-admin/admin-ajax.php?action=phase_3_create_product';
      
      $http.post(apiUrl, $scope.product)
      .then(function (response) {
        $scope.isSearchloading = false;
        if(response.data.status){
          $scope.product = response.data.data;
          var userConfirmed = confirm("Book saved successfully!  Do you want to create a copy?");
          if (userConfirmed) {
            $scope.bt_create_copy (response.data.data);
          } else {
            $scope.copy={};
            $scope.navigate_page("product_detail");
          }
        }else{
          alert("An error happened. " + response.data.message);
        }
    })
    .catch(function (error) {
        $scope.isSearchloading = false;
        console.error('Error:', error);
        alert('Error saving product. Please try again.');
    });
    }

    $scope.bt_save_copy = function(){
      
      if($scope.product.woocommerce_product_id != null){
        $scope.copy.woocommerce_product_id = $scope.product.woocommerce_product_id;
      }else{
        alert("Cannot create copy, please go back and start over.");
        return;
      }
      if(!confirm("Sure you want to save this copy?")){
        return;
      }
      $scope.isSearchloading = true;

      console.log($scope.copy);
      var apiUrl = '/wp-admin/admin-ajax.php?action=phase_3_add_book_copy';
      
      $http.post(apiUrl, $scope.copy)
      .then(function (response) {
        $scope.isSearchloading = false;
        console.log(response.data);
        if(response.data.status &&  response.data.data!=null){
          alert('Copy created successfully!');
          $scope.copy.copy_post_id = response.data.data.copy_id;
          $scope.copy.smart_copy_id = response.data.data.smart_copy_id;
          $scope.navigate_page("product_detail");
          $scope.pageStack = [];
        }else{
          alert(response.data.message);
        }
    })
    .catch(function (error) {
        $scope.isSearchloading = false;
        console.error('Error:', error);
        alert('Error saving product. Please try again.');
    });
    }

    $scope.bt_title_search = function () {
        console.log('Title and Author Search clicked');
        $scope.navigate_page("title_page");
    };

    $scope.bt_blank_form = function () {
      console.log('Blank Form clicked');
      $scope.resetProductform();

      $scope.navigate_page("product_page");
    };

    $scope.resetProductform = function(){
      $scope.product={};
      $scope.copy={};
      $scope.product.image_url = "https://worldofsquiggles.com/wp-content/uploads/2023/03/placeholder.png";

      for (let index = 0; index <   $scope.all_categories.length; index++) {
          $scope.all_categories[index].ticked=false;
      }
      for (let index = 0; index <   $scope.product_tags.length; index++) {
        $scope.product_tags[index].ticked=false;
      }
      for (let index = 0; index <   $scope.all_characters.length; index++) {
        $scope.all_characters[index].ticked=false;
      }
      for (let index = 0; index <   $scope.all_genres.length; index++) {
        $scope.all_genres[index].ticked=false;
      }
      for (let index = 0; index <   $scope.all_subjects.length; index++) {
        $scope.all_subjects[index].ticked=false;
      }
      for (let index = 0; index <   $scope.all_series.length; index++) {
        $scope.all_series[index].ticked=false;
      }
      for (let index = 0; index <   $scope.all_languages.length; index++) {
        $scope.all_languages[index].ticked=false;
      }
      for (let index = 0; index <   $scope.all_publishers.length; index++) {
        $scope.all_publishers[index].ticked=false;
      }
      for (let index = 0; index <   $scope.all_authors.length; index++) {
        $scope.all_authors[index].ticked=false;
      }
    

    };

    $scope.bt_product_data = function () {
        console.log('Product Data Import clicked');
        $window.location.href = '/wp-admin/edit.php?post_type=product&page=product_importer';
    };

    $scope.bt_home = function () {
      $scope.product={};
      $scope.copy={};
      $scope.sources=[];

      $scope.navigate_page("search_page");
      $scope.pageStack = [];
    };

    $scope.bt_reset_form = function(){
      console.log('Reset Form button clicked');
      console.log($scope.copy);

    }
    $scope.csvData = {
      headers: [],
      rows: []
    };

    $scope.onCSVFileSelected = function() {

      $scope.csvData = {
        headers: [],
        rows: []
      };
      const file = angular.element('#csv-file')[0].files[0];
      console.log(file);
      Papa.parse(file, {
        complete: function(results) {
          $scope.$apply(function() {
            $scope.csvData.headers = results.data[0];
            $scope.csvData.rows = results.data.slice(1);
            console.log($scope.csvData);
          });
        }
      });
    };

    $scope.searchCSVbyISBN = function(query) {
      
      var csvproducts = [];
      var filtered = $scope.csvData.rows.filter(function(row) {
          return row[0] === query;
      });

      filtered.forEach(f => {
        var p = {
          "book_title": f[1],
          "image_url": f[10],
          "source": "csv",
          "description": f[2],
          "copy_ids": [],
          "copy_list": [],
          "authors": $scope.csvStringToArray(f[12]),
          "publishers": $scope.csvStringToArray(f[13]),
          "book_categories": [
              {
                  "name": "Book"
              }
          ],
          "book_tags": $scope.csvStringToArray(f[9]),
          "book_characters": $scope.csvStringToArray(f[17]),
          "book_genres": $scope.csvStringToArray(f[16]),
          "book_subjects": $scope.csvStringToArray(f[15]),
          "book_series": $scope.csvStringToArray(f[11]),
          "book_languages": $scope.csvStringToArray(f[14]),
          "edition": f[28],
          "credits": f[1],
          "mrp": f[20],
          "height": f[6],
          "width": f[5],
          "length": f[4],
          "weight": f[3],
          "min_age": f[21],
          "max_age": f[22],
          "pages": f[19],
          "country_of_origin": f[30],
          "isbn10": f[31],
          "isbn13": f[0],
          "dewey_code": f[33],
          "lexile_code": f[32],
          "product_classification": f[25],
          "product_type": f[26],
          "product_sub_type": f[27],
          "publication_date": f[23],
          "woocommerce_product_id": "",
          "product_url": "",
          "_csvrow":f
      }

        csvproducts.push(p);
      });

      return csvproducts;
  };

  $scope.$watch('product.product_type', function(newValue, oldValue) {
    $scope.calculateCredits();
  });

  $scope.$watch('product.mrp', function(newValue, oldValue) {
    $scope.calculateCredits();
  });

  $scope.calculateCredits = function(){
    var credit = 1;
    if($scope.product.product_type && $scope.product.product_type.toLowerCase()==="novelty"){
      if($scope.product.mrp){
        if($scope.product.mrp*1.2<=250){
          credit=1;
        }else if($scope.product.mrp*1.2 > 250 && $scope.product.mrp*1.2<=550){
          credit=2;
        }else if($scope.product.mrp*1.2 > 550 && $scope.product.mrp*1.2<=1000){
          credit=3;
        }else if($scope.product.mrp*1.2 > 1000){
          credit=4;
        }
      }
    }
    else {
      if($scope.product.mrp){
        if($scope.product.mrp<=250){
          credit=1;
        }else if($scope.product.mrp > 250 && $scope.product.mrp <= 550){
          credit=2;
        }else if($scope.product.mrp > 550 && $scope.product.mrp <= 1000){
          credit=3;
        }else if($scope.product.mrp > 1000){
          credit=4;
        }
      }
    }
    $scope.product.credits = credit;
  }

  $scope.searchCSVbyTitle = function(query) {
      
    var csvproducts = [];
    var filtered = $scope.csvData.rows.filter(function(row) {
       return row[1].toLowerCase() .indexOf(query.toLowerCase() ) !== -1;
    });

    filtered.forEach(f => {
      var p = {
        "book_title": f[1],
        "image_url": f[10],
        "source": "csv",
        "description": f[2],
        "copy_ids": [],
        "copy_list": [],
        "authors": $scope.csvStringToArray(f[12]),
        "publishers": $scope.csvStringToArray(f[13]),
        "book_categories": [
            {
                "name": "Book"
            }
        ],
        "book_tags": $scope.csvStringToArray(f[9]),
        "book_characters": $scope.csvStringToArray(f[17]),
        "book_genres": $scope.csvStringToArray(f[16]),
        "book_subjects": $scope.csvStringToArray(f[15]),
        "book_series": $scope.csvStringToArray(f[11]),
        "book_languages": $scope.csvStringToArray(f[14]),
        "edition": f[28],
        "credits": f[1],
        "mrp": f[20],
        "height": f[6],
        "width": f[5],
        "length": f[4],
        "weight": f[3],
        "min_age": f[21],
        "max_age": f[22],
        "pages": f[19],
        "country_of_origin": f[30],
        "isbn10": f[31],
        "isbn13": f[0],
        "dewey_code": f[33],
        "lexile_code": f[32],
        "product_classification": f[25],
        "product_type": f[26],
        "product_sub_type": f[27],
        "publication_date": f[23],
        "woocommerce_product_id": "",
        "product_url": "",
        "_csvrow":f
    }

      csvproducts.push(p);
    });

    return csvproducts;
};

  $scope.csvStringToArray = function(csvString) {
      // Check for null or empty string
      if (!csvString || csvString.trim() === "") {
          return [];
      }

      // Split the string by commas and trim each element
      return csvString.split(',').map(function(item) {
          return {
            "name": item.trim(),
            "description": "",
            "image_url": ""
          };
      });
  };

  $scope.findProductInInventory = function(isbn13){
    var found=false;
    for (let index = 0; index < $scope.sources.length; index++) {
      const book = $scope.sources[index];
      if(book.source == "woocommerce" && book.isbn13==isbn13){
        found=true;
        break;
      }
      
    }
    return found;
  }

  $scope.refresh = function(who){
    if(who=="author"){
      var apiUrl = '/wp-admin/admin-ajax.php?action=phase_3_get_authors';
      var requestData = { };
      $scope.isSearchloading =true;
      $http.post(apiUrl, requestData)
      .then(function (response) {
        $scope.all_authors = response.data.data;
        $scope.isSearchloading =false;
      })
      .catch(function (error) {
        $scope.isSearchloading =false;
          console.error('Error:', error);
      });  
   
    }
  }

  $scope.AddNew = function(who){

    if(who=='author'){
		  var searchinput = jQuery('div[output-model="product.authors"]').find('input.inputFilter').val() ;
      var author = prompt("Enter Author Name",searchinput);
      if(author!="" && author!=null){
        var newauthor = {
          name: author,
          _addnew: true,
          ticked:true

        };
        $scope.all_authors.push(newauthor);
        
      }
    }else if(who=='publisher'){
      var searchinput = jQuery('div[output-model="product.publishers"]').find('input.inputFilter').val() ;
      var publisher = prompt("Enter Publisher Name",searchinput);
      if(publisher!="" && publisher!=null){
        var newpublisher = {
          name: publisher,
          _addnew: true,
          ticked:true

        };
        $scope.all_publishers.push(newpublisher);
        
      }
	}
	else if(who=='Characters'){
      var searchinput = jQuery('div[output-model="product.book_characters"]').find('input.inputFilter').val() ;
      var Characters = prompt("Enter Characters Name",searchinput);
      if(Characters!="" && Characters!=null){
        var newCharactersr = {
          name: Characters,
          _addnew: true,
          ticked:true

        };
        $scope.all_characters.push(newCharactersr);
        
      }
	}	 
	else if(who=='Book_Genres'){
      var searchinput = jQuery('div[output-model="product.book_genres"]').find('input.inputFilter').val() ;
      var Book_Genres = prompt("Enter Book Genres Name",searchinput);
      if(Book_Genres!="" && Book_Genres!=null){
        var newBook_Genresr = {
          name: Book_Genres,
          _addnew: true,
          ticked:true

        };
        $scope.all_genres.push(newBook_Genresr);
        
      }
	}
	  else if(who=='Book_Subjects'){
      var searchinput = jQuery('div[output-model="product.book_subjects"]').find('input.inputFilter').val() ;
      var Book_Subjects = prompt("Enter Book Subjects Name",searchinput);
      if(Book_Subjects!="" && Book_Subjects!=null){
        var newBook_Subjects = {
          name: Book_Subjects,
          _addnew: true,
          ticked:true

        };
        $scope.all_subjects.push(newBook_Subjects);
        
      }
	}
	  else if(who=='Book_Series'){
      var searchinput = jQuery('div[output-model="product.book_series"]').find('input.inputFilter').val() ;
      var Book_Series = prompt("Enter Book Series Name",searchinput);
      if(Book_Series!="" && Book_Series!=null){
        var newBook_Series = {
          name: Book_Series,
          _addnew: true,
          ticked:true

        };
        $scope.all_series.push(newBook_Series);
        
      }
	}
	  
	  else if(who=='Book_Languages'){
      var searchinput = jQuery('div[output-model="product.book_languages"]').find('input.inputFilter').val() ;
      var Book_Languages = prompt("Enter Book Languages Name",searchinput);
      if(Book_Languages!="" && Book_Languages!=null){
        var newBook_Languages = {
          name: Book_Languages,
          _addnew: true,
          ticked:true

        };
        $scope.all_languages.push(newBook_Languages);
        
      }
	  } 
    else if(who=='Tags'){
      var searchinput = jQuery('div[output-model="product.book_tags"]').find('input.inputFilter').val() ;
      var tag = prompt("Enter Tag Name",searchinput);
      if(tag!="" && tag!=null){
        var newTag = {
          name: tag,
          _addnew: true,
          ticked:true

        };
        $scope.product_tags.push(newTag);
        
      }
	  } 
	  
  };


    $scope.all_categories = window.all_categories ;
    $scope.product_tags = window.product_tags ;
    $scope.all_characters = window.all_characters ;
    $scope.all_genres = window.all_genres ;
    $scope.all_subjects = window.all_subjects ;
    $scope.all_series = window.all_series ;
    $scope.all_languages = window.all_languages ;
    $scope.all_publishers = window.all_publishers ;
    $scope.all_authors = window.all_authors ;

  });

app.directive('enterNext', function() {
    return {
      restrict: 'A',
      link: function(scope, element, attrs) {
        element.find('input, textarea').on('keydown keypress', function(event) {
          if (event.which === 13) { // 13 is the key code for Enter
            event.preventDefault();
            var fields = $(this).closest('form').find('input, textarea, select');
            var index = fields.index(this);
            if (index > -1 && (index + 1) < fields.length) {
              fields.eq(index + 1).focus();
            }
          }
        });
      }
    };
  });