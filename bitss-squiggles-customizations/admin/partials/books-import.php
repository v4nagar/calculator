<?php
  $taxonomy     = 'product_cat';
  $orderby      = 'name';  
  $show_count   = 1;      // 1 for yes, 0 for no
  $pad_counts   = 0;      // 1 for yes, 0 for no
  $hierarchical = 1;      // 1 for yes, 0 for no  
  $title        = '';  
  $empty        = 0;

  $args = array(
         'taxonomy'     => $taxonomy,
         'orderby'      => $orderby,
         'show_count'   => $show_count,
         'pad_counts'   => $pad_counts,
         'hierarchical' => $hierarchical,
         'title_li'     => $title,
         'hide_empty'   => $empty
  );
 $all_categories = get_categories( $args );

 $product_tags = get_terms( 'product_tag' );

 $characters = get_terms( 'character' );
 $book_genres = get_terms( 'book-genre' );
 $book_subjects = get_terms( 'book-subject' );
 $book_series = get_terms( 'product-series' );
 $book_languages = get_terms( 'book-language' );
 $book_authors = get_terms( 'book-author' );
 $book_publishers = get_terms( 'book-publisher' );

$fields = get_option('wpcf-fields');
$product_classifications = $fields['product-classification']['data']['options'];
$product_types = $fields['product-type']['data']['options'];
$product_sub_types = $fields['product-sub-type']['data']['options'];

?>
<script>
  window.all_categories = <?= json_encode( $all_categories ); ?>;
  window.product_tags = <?= json_encode( $product_tags ); ?>;
  window.all_characters = <?= json_encode(array_values($characters)); ?>;//step 1
  window.all_genres = <?= json_encode(array_values($book_genres)); ?>;
  window.all_subjects = <?= json_encode(array_values($book_subjects)); ?>;
  window.all_series = <?= json_encode(array_values($book_series)); ?>;
  window.all_languages = <?= json_encode(array_values($book_languages)); ?>;
  window.all_publishers = <?= json_encode(array_values($book_publishers)); ?>;
  window.all_authors = <?= json_encode(array_values($book_authors)); ?>;
</script>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.8.2/angular.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.3.0/papaparse.min.js"></script>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <div  ng-app="myApp" style="width:100%">
      <section id="new_product_controller" class="section new_product" ng-controller="new_product">

        <button class="is-dark" ng-click="navigate_back()" ng-show="current_page!='search_page' && pageStack.length>0">
            <span class="icon">
                <i class="fas fa-arrow-left"></i>
            </span>
            <span>Back</span>
        </button><br><br>

        <!-- Search Page -->
        <div class="container has-text-centered is-desktop" ng-show="current_page=='search_page'" style="background-color: rgb(228, 228, 228);">
          <div class="buttons">
            <!-- Button 1 -->
            <button class="button is-dark" ng-click="bt_isbn_search()">
              <span class="icon">
                <i class="fas fa-plus"></i>
              </span>
              <span>ISBN Search</span>
            </button>
          
            <!-- Button 2 -->
            <button class="button is-dark" ng-click="bt_title_search()">
              <span class="icon">
                <i class="fas fa-plus"></i>
              </span>
              <span>Title and Author Search </span>
            </button>
          
            <!-- Button 3 -->
            <button class="button is-dark" ng-click="bt_blank_form()">
              <span class="icon">
                <i class="fas fa-plus"></i>
              </span>
              <span>Blank Form</span>
            </button>
          
            <!-- Button 4 -->
            <button class="button is-dark" ng-click="bt_product_data()">
              <span class="icon">
                <i class="fas fa-plus"></i>
              </span>
              <span>Product Data Import</span>
            </button>

          </div>
        </div><!-- page end -->

        <div class="container box" ng-show="current_page=='isbn_page' || current_page=='title_page'">
          <div class="field">
            
            <div class="control ">
              <label class="label">Select Invoice CSV</label>
              <div class="file has-name is-fullwidth">
                <label class="file-label">
                  <input class="file-input" type="file" id="csv-file">
                  <span class="file-cta">
                    <span class="file-icon">
                      <i class="fas fa-upload"></i>
                    </span>
                    <span class="file-label">
                      Choose a file…
                    </span>
                  </span>
                  <span class="file-name" ng-show="csvData.rows.length">
                    {{csvData.rows.length}} rows loaded.
                  </span>
                  <span class="file-name" ng-show="!csvData.rows.length">
                    No invoice data loaded yet.
                  </span>
                </label>
              </div>
            </div>
          </div>
        </div>

        <!-- Isbn Page -->
        <div class="container box  is-desktop" ng-show="current_page=='isbn_page'" style="background-color: rgb(228, 228, 228);">
         <div class="field is-grouped">
            <p class="control is-expanded has-icons-left">
                <input class="input" type="text" ng-model="isbnSearch" placeholder="Search for ISBN..."
                      ng-keyup="$event.keyCode === 13 && searchISBN()">
                <span class="icon is-left">
                    <i class="fas fa-search"></i>
                </span>
            </p>
            <button class="button is-dark is-normal" ng-class="{'is-loading': isSearchloading}" ng-click="searchISBN()">
                <span class="icon">
                    <i class="fas fa-search"></i>
                </span>
                <span>Search</span>
            </button>
          </div>

          <div ng-show="isError" class="notification is-danger">
            {{ errorMessage }}
          </div>
          <div ng-show="!isError" class="notification is-info">
              {{ sources.length }} result(s) found.
          </div>


          <div class="box" ng-repeat="source in sources">
            <strong>
              <p ng-if="source.source == 'csv'">CSV (Invoice)</p>
              <p ng-if="source.source == 'isbndb'">API (ISBNDB)</p>
              <p ng-if="source.source == 'woocommerce'">Existing Inventory (WOOCOMMERCE)</p>
            </strong>
            <div class="columns">
              <div class="column is-one-fifth">
                <figure class="image is-1by1" >
                    <img ng-src="{{ source.image_url }}">
                </figure>            
              </div>
              <div class="column is-one-third" >
                <h4>
                  <strong>
                    <a ng-href="{{ source.product_url }}" target="_blank">{{ source.book_title }}</a>
                  </strong>
                </h4>
                <div ng-repeat="author in source.authors">
                    <p>{{ author.name }} </p>
                </div>
                <p>{{ source.description | limitTo: 50 }}</p>
                <p>{{ source.pages }}</p>
                <p>{{ source.copy_ids }} </p>
              </div>
              <div class="column is-flex is-align-items-center is-justify-content-center">
                <div class="field is-grouped">
                  <div class="control">
                    <button class="button is-dark" ng-click="bt_create_product(source)">
                      <span class="icon">
                        <i class="fas fa-plus"></i>
                      </span>
                      <span>Create Product</span>
                    </button>
                  </div>
                  <div class="control" ng-show="source.woocommerce_product_id" >
                    <button class="button is-dark" ng-click="bt_create_copy(source)" >
                      <span class="icon">
                        <i class="fas fa-plus"></i>
                      </span>
                      <span>Create Copy</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div><!-- Isbn Page End -->

       
        <!-- Title & Author Page -->
        <div class="container box" ng-show="current_page=='title_page'" style="background-color: rgb(228, 228, 228);">
          <div class="field is-grouped">
            <p class="control is-expanded has-icons-left">
                <input class="input" type="text" ng-model="isbnTitle" placeholder="Search for Title..."
                      ng-keyup="$event.keyCode === 13 && searchTitle()">
                <span class="icon is-left">
                    <i class="fas fa-search"></i>
                </span>
            </p>
            <button class="button is-dark is-normal" ng-class="{'is-loading': isSearchloading}" ng-click="searchTitle()" ng-disabled="!isbnTitle.trim()">
                <span class="icon">
                    <i class="fas fa-search"></i>
                </span>
                <span>Search</span>
            </button>
          </div>
          <div ng-show="isError" class="notification is-danger">
            {{ errorMessage }}
          </div>
          <div ng-show="!isError" class="notification is-info">
              {{ sources.length }} result(s) found.
          </div>

          <div class="box" ng-repeat="source in sources">
            <strong>
              <p ng-if="source.source == 'csv'">CSV (Invoice)</p>
              <p ng-if="source.source == 'isbndb'">API (ISBNDB)</p>
              <p ng-if="source.source == 'woocommerce'">Existing Inventory (WOOCOMMERCE)</p>
            </strong>
            <div class="columns">
              <div class="column is-one-fifth">
                <figure class="image is-1by1" >
                    <img ng-src="{{ source.image_url }}">
                </figure>            
              </div>
              <div class="column is-one-third" >
                <h4>
                  <strong>
                    <a ng-href="{{ source.product_url }}" target="_blank">{{ source.book_title }}</a>
                  </strong>
                </h4>
                <div ng-repeat="author in source.authors">
                    <p>{{ author.name }} </p>
                </div>
                <p>{{ source.description | limitTo: 50 }}</p>
                <p>{{ source.pages }}</p>
                <p>{{ source.copy_ids }} </p>
              </div>
              <div class="column is-flex is-align-items-center is-justify-content-center">
                <div class="field is-grouped">
                  <div class="control">
                    <button class="button is-dark" ng-click="bt_create_product(source)">
                      <span class="icon">
                        <i class="fas fa-plus"></i>
                      </span>
                      <span>Create Product</span>
                    </button>
                  </div>
                  <div class="control" ng-show="source.woocommerce_product_id">
                    <button class="button is-dark" ng-click="bt_create_copy(source)">
                      <span class="icon">
                        <i class="fas fa-plus"></i>
                      </span>
                      <span>Create Copy</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div><!-- Title & Author Page End -->


        <!-- Product Page -->
        <div class="container" ng-show="current_page=='product_page'">	
          <form enter-next ng-submit="bt_save_product()">

            <div class="columns is-multiline is-desktop box " style="background-color: rgb(206, 205, 205);">
            
              <!-- Part 1 -->
              <div class="column is-one-fifth">
                
                <figure class="image is-1by1">
                    <img ng-src="{{ product.image_url }}">
                </figure>
                <div class="file is-medium is-boxed is-fullwidth">
                    <label class="file-label">
                      <input class="file-input" type="button" value="<?php esc_attr_e('Upload Image', 'text-domain'); ?>" ng-click="cat_media_upload()" />
                     
                        <span class="file-cta button is-flex is-align-items-center" style="height: 5rem;">
                            <span class="file-icon">
                                <i class="fas fa-upload"></i>
                            </span>
                            <span class="file-label">
                                Upload Cover Image
                            </span>
                        </span>
                    </label>
                </div>

                <div class="field">
                  <label>Product Category</label>  
                  <div class="control">
                    <div class="select is-multiple is-fullwidth  is-link"     
                        isteven-multi-select
                        input-model="all_categories"
                        output-model="product.book_categories"
                        button-label="name"
                        item-label="name"
                        tick-property="ticked"
                        max-height="250px"
                        ng-required="product.book_categories.length === 0"
                    >
                    </div>
                  </div>
                </div>

                <div class="field">
                  <label>Product Tags</label> <span style="font-size:10px"><a ng-click="AddNew('Tags')" href="">(Add New)</a></span>
                  <div class="control">
                    <div  class="select is-multiple is-fullwidth  is-link"     
                        isteven-multi-select
                        input-model="product_tags"
                        output-model="product.book_tags"
                        button-label="name"
                        item-label="name"
                        tick-property="ticked"
                        max-height="250px"
                        ng-required="product.book_categories.length === 0"
                    >
                    </div>
                  </div>
                </div>

                <div class="field">
                  <label>Characters</label> <span style="font-size:10px"><a ng-click="AddNew('Characters')" href="">(Add New)</a></span>
                  <div class="control">
                      <!-- Step 3 -->
                      <div  class="select is-multiple is-fullwidth  is-link"     
                        isteven-multi-select
                        input-model="all_characters"
                        output-model="product.book_characters"
                        button-label="name"
                        item-label="name"
                        tick-property="ticked"
                        max-height="250px"
                    >
                    </div>
                  </div>
                </div>

                <div class="field">
                  <label>Book Genres</label> <span style="font-size:10px"><a style="display:none" ng-click="AddNew('Book_Genres')" href="">(Add New)</a></span>
                  <div class="control">
                      <div  class="select is-multiple is-fullwidth  is-link"     
                          isteven-multi-select
                          input-model="all_genres"
                          output-model="product.book_genres"
                          button-label="name"
                          item-label="name"
                          tick-property="ticked"
                          max-height="250px"
                      >
                      </div>
                  </div>
                </div>

                <div class="field">
                  <label>Book Subjects</label>  <span style="font-size:10px"><a ng-click="AddNew('Book_Subjects')" href="">(Add New)</a></span>
                  <div class="control">
                  <div  class="select is-multiple is-fullwidth  is-link"     
                        isteven-multi-select
                        input-model="all_subjects"
                        output-model="product.book_subjects"
                        button-label="name"
                        item-label="name"
                        tick-property="ticked"
                        max-height="250px"
                    >
                    </div>
                  </div>
                </div>

                <div class="field">
                  <label>Book Series</label> <span style="font-size:10px"><a ng-click="AddNew('Book_Series')" href="">(Add New)</a></span>
                  <div class="control">
                  <div  class="select is-multiple is-fullwidth  is-link"     
                        isteven-multi-select
                        input-model="all_series"
                        output-model="product.book_series"
                        button-label="name"
                        item-label="name"
                        tick-property="ticked"
                        max-height="250px"
                    >
                    </div>
                  </div>
                </div>

                <div class="field">
                  <label>Book Languages</label>  <span style="font-size:10px"><a ng-click="AddNew('Book_Languages')" href="">(Add New)</a></span>
                  <div class="control">
                  <div  class="select is-multiple is-fullwidth  is-link"     
                        isteven-multi-select
                        input-model="all_languages"
                        output-model="product.book_languages"
                        button-label="name"
                        item-label="name"
                        tick-property="ticked"
                        max-height="250px"
                    >
                    </div>
                  </div>
                </div>

              </div><!-- end -->

              <!-- Part 2 -->
              <div class="column">

                    <div class="columns">
                      <div class="column">
                        <div class="control">
                            <label>Product Name *</label>
                            <input id="productName" ng-required="true" class="input" type="text" ng-model="product.book_title" placeholder="Product Name (Text)" >
                        </div><br>
                        <div class="field">
                            <label>Description *</label>
                            <div class="control">
                                <textarea id="description" class="textarea is-medium" ng-model="product.description" placeholder="Description (text area)" rows="3" ng-required="true"></textarea>
                            </div>
                        </div>
                      </div>

                      <div class="column">
                        <div class="control">
                          <label>Edition</label>
                          <input class="input" type="text" ng-model="product.edition" placeholder="Edition (Text)">
                        </div><br>
                        <div class="control">
                          <label>Credit *</label>
                          <input class="input" type="text" ng-model="product.credits" placeholder="Credit (Number)" ng-required="true">
                        </div><br>
                        <div class="control">
                          <label>MRP *</label>
                          <input class="input" type="text" ng-model="product.mrp" placeholder="MRP (Decimal)" ng-required="true">
                        </div>
                      </div>
                    </div>

                    <div class="columns is-desktop">
                      <div class="column">
                        <div class="control">
                          <label>Height, cm *</label>
                          <input id="height" class="input" ng-required="true" type="text" ng-model="product.height" placeholder="Height (cm)(Decimal)">
                        </div>
                      </div>
                      <div class="column">
                        <div class="control">
                          <label>Width, cm *</label>
                          <input id="width" class="input" type="text" ng-required="true" ng-model="product.width" placeholder="Width (cm)(Decimal)">
                        </div>
                      </div>
                      <div class="column">
                        <div class="control">
                          <label>Length, cm *</label>
                          <input class="input" type="text" ng-required="true" ng-model="product.length" placeholder="Length (cm)(Decimal)">
                        </div>
                      </div>
                      <div class="column">
                        <div class="control">
                          <label>Weight, gm *</label>
                          <input class="input" type="text" ng-required="true" ng-model="product.weight" placeholder="Weight (Grams)(Decimal)">
                        </div>
                      </div>
                    </div>
    
                    <div class="columns is-desktop">
                        <div class="column">
                          <div class="control">
                            <label>Min Age *</label>
                            <input id="minAge" class="input" ng-required="true" type="text" ng-model="product.min_age" placeholder="Min Age (Number)">
                          </div>
                        </div>
                        <div class="column">
                          <div class="control">
                            <label>Max Age *</label>
                            <input id="maxAge" class="input" ng-required="true" type="text" ng-model="product.max_age" placeholder="Max Age (Number)">
                          </div>
                        </div>
                        <div class="column">
                          <div class="control">
                            <label>Pages *</label>
                            <input class="input" type="text" ng-required="true" ng-model="product.pages" placeholder="Pages (Number)">
                          </div>
                        </div>
                        <div class="column">
                          <div class="control">
                            <label>Country of Origin:</label>
                            <input class="input" type="text" ng-model="product.country_of_origin" placeholder="Country of Origin (Text)">
                          </div>
                        </div>
                    </div>
    
                    <div class="columns is-desktop">
                        <div class="column">
                          <div class="control">
                            <label>ISBN10</label>
                            <input id="isbn10" class="input" type="text" ng-model="product.isbn10" placeholder="ISBN10 (Text)">
                          </div>
                        </div>
                        <div class="column">
                          <div class="control">
                            <label>ISBN13 *</label>
                            <input id="isbn13" class="input" type="text" ng-required="true" ng-model="product.isbn13" placeholder="ISBN13 Name (Text)">
                          </div>
                        </div>
                        <div class="column">
                          <div class="control">
                            <label>Dewey</label>
                            <input class="input" type="text" ng-model="product.dewey_code" placeholder="Dewey (Text)">
                          </div>
                        </div>
                        <div class="column">
                          <div class="control">
                            <label>Lexile Code</label>
                            <input class="input" type="text" ng-model="product.lexile_code" placeholder="Lexile Code (Text)">
                          </div>
                        </div>
                    </div>
    
                    <div class="columns">
                      <div class="column">
                          <div class="field is-grouped is-grouped-multiline has-addons has-addons-centered">
						              <div class="control is-expanded">
                            <label>Illustrator</label>
                            <input class="input" type="text" ng-model="product.illustrator" placeholder="illustrator (Text)">
                          </div>
                          <div class="control is-expanded">
							              <label>Product Classification</label>
                              <div class="select is-fullwidth  is-link">
                                <select ng-model="product.product_classification" ng-required="true">
                                  <option value="">Product Classification *</option>
                                  <?php
                                    foreach ($product_classifications as $p) {
                                      if(isset($p['title'] )){
                                        echo "<option value='".$p['value']."'>". $p['title']  ."</option>";
                                      }
                                    
                                    }
                                  ?>
                                </select>
                              </div>
                            </div>
                            <div class="control is-expanded">
								              <label>Product Type</label>
                              <div class="select is-fullwidth  is-link">
                                <select ng-model="product.product_type" ng-required="true">
                                <option value="">Product Type *</option>
                                  <?php
                                    foreach ($product_types as $p) {
                                      if(isset($p['title'] )){
                                        echo "<option value='".$p['value']."'>". $p['title']  ."</option>";
                                      }
                                    }
                                  ?>
                                </select>
                              </div>
                            </div>
                            <div class="control is-expanded">
								              <label>Product Sub Type</label>
                              <div class="select is-fullwidth is-link">
                                <select ng-model="product.product_sub_type" ng-required="true">
                                <option value="">Product Sub Type *</option>
                                  <?php
                                    foreach ($product_sub_types as $p) {
                                      if(isset($p['title'] )){
                                        echo "<option value='".$p['value']."'>". $p['title']  ."</option>";
                                      }
                                    }
                                  ?>
                                </select>
                              </div>
                            </div>
                          </div>
                      </div>
                    </div>
    
                    <div class="columns">
                      <div class="column">
                        <div class="field is-grouped is-grouped-multiline has-addons has-addons-centered">
                            <div class="control is-expanded">
                              <label>Authors</label> <span style="font-size:10px"><a style="display:none;" ng-click="AddNew('author')" href="">(Add New)</a> <a href="/wp-admin/edit-tags.php?taxonomy=book-author&post_type=product" target="_blank">(Add New) </a> <button class="button is-ghost" type="button" ng-click="refresh('author')" href="" ng-class="{'is-loading': isSearchloading}"><span class="icon"><i class="fas fa-refresh"></i></span></button></span>
                                <p class="control has-icons-left">
                                    <div  class="select is-multiple is-fullwidth  is-link"     
                                        isteven-multi-select
                                        input-model="all_authors"
                                        output-model="product.authors"
                                        button-label="name"
                                        item-label="name"
                                        tick-property="ticked"
                                        max-height="250px"
                                        >
                                    </div>
                                </p>
                            </div>
                            <div class="control is-expanded">
                            <label>Publisher</label> <span style="font-size:10px"><a ng-click="AddNew('publisher')" href="">(Add New)</a></span>
                                <p class="control has-icons-left">
                                    <div  class="select is-multiple is-fullwidth  is-link"     
                                        isteven-multi-select
                                        input-model="all_publishers"
                                        output-model="product.publishers"
                                        button-label="name"
                                        item-label="name"
                                        tick-property="ticked"
                                        max-height="250px"
                                        >
                                    </div>
                                </p>
                            </div>
                            <div class="control is-expanded">
                            <label>Publication Date</label> 
                            <div class="control">
                                <input class="input" ng-model="product.publication_date" type="date" ng-init="product.publication_date = getCurrentDate()">
                            </div>
                            </div>
                        </div>
                      </div>
                    </div>

              </div>

            </div>

            <!-- Button to Save Book -->
            <button class="button is-black is-pulled-right" ng-class="{'is-loading': isSearchloading}" type="submit">
              <span class="icon">
                <i class="fas fa-plus"></i>
              </span>
              <span>Save Book</span>
            </button>

          </form>
        </div><!-- Page end -->
    
    
        <!-- Copy Page -->
        <div class="container" style="background-color: rgb(206, 205, 205);" ng-show="current_page=='copy_page'">
          <form enter-next  ng-submit="bt_save_copy()">

            <!-- Part 1 -->
            <div class="columns is-desktop" style="margin: 1%; padding-top: 1%; background-color: rgb(206, 205, 205);" >  
              <!-- First column -->
              <div class="column is-half">
                <div class="field">
                  <div class="field has-addons">
                    <p class="control has-icons-left" style="width: 100%;">
                      <input class="input" type="text" ng-model="copy.supplier" placeholder="Supplier">
                      <span class="icon is-small is-left">
                        <i class="fas fa-search"></i>
                      </span>
                    </p>
                  </div>
                </div>
              </div>
              <!-- Second column -->
              <div class="column is-half">
                <div class="field">
                  <div class="field has-addons">
                    <p class="control has-icons-left" style="width: 100%;">
                      <input class="input" type="text" ng-model="copy.invoice_" placeholder="Invoice Number">
                      <span class="icon is-small is-left">
                        <i class="fas fa-search"></i>
                      </span>
                    </p>
                  </div>
                </div>
              </div>
            </div>
            <!-- Button to Reset Form -->
            <button class="button is-black is-pulled-right " style="margin: 1%;" ng-click="bt_reset_form()">
              <span class="icon">
                <i class="fas fa-plus"></i>
              </span>
              <span>Reset Form</span>
            </button>
            <br><br><hr>

            <!-- Part 2 -->
            <div class="columns is-desktop" style="margin: 1%; background-color: rgb(206, 205, 205);">
              <div class="column is-one-fifth is-flex is-justify-content-center">  
                <figure>
                  <img ng-src="{{ product.image_url }}">
                </figure>
              </div>
              <div class="column">  
                <h1><strong>{{ copy.woocommerce_product_id }}</strong></h1>
                <span>{{ product.book_title }}</span>
                <p>{{ product.description | limitTo: 500 }}</p>
              </div>
            </div>
            <hr>

            <!-- Part 3 -->
            <div style="margin: 1%; background-color: rgb(206, 205, 205);" >
                  
              <div class="columns is-desktop">
                    <!-- First column -->
                    <div class="column">
                      <div class="control">
                        <label>Copy Binding *</label>
                        <div class="select  is-link" style="width: 100%;" >
                          <select ng-model="copy.binding" ng-required="true" style="width: 100%;">
                            <option value="">--- not set ---</option>
                            <option value="Paperback">Paperback</option>
                            <option value="Boardbook">Boardbook</option>
                            <option value="Hardbound">Hardbound</option>
                            <option value="Other">Other</option>
                          </select>
                        </div>
                      </div>
                    </div>
                    <!-- Second column -->
                    <div class="column">
                      <div class="control">
                        <label>Invoice Line Item No.</label>
                        <input class="input" type="text" ng-model="copy.invoice_line_number" placeholder="Invoice Line Item No.">
                      </div>
                    </div>
                    <!-- Third column -->
                    <div class="column">
                      <div class="control">
                        <label>Invoice No.</label>
                        <input class="input" type="text" ng-model="copy.invoice_number" placeholder="Invoice No.">
                      </div>
                    </div>
                    <!-- Fourth column -->
                    <div class="column">
                      <div class="control">
                        <label>Shelf ID</label>
                        <input class="input" type="text" ng-model="copy.shelf_id" placeholder="Shelf ID">
                      </div>
                    </div>
              </div>
                  
              <div class="columns is-desktop">
                    <!-- First column -->
                    <div class="column">
                      <div class="field">
                        <label>Invoice Date</label>
                        <div class="control">
                          <input class="input" ng-model="copy.invoice_date" type="date">
                        </div>
                      </div>
                    </div>
                    <!-- Second column -->
                    <div class="column">
                      <div class="field">
                        <label>Entry Date</label>
                        <div class="control">
                          <input class="input" ng-model="copy.entry_date" type="date" required>
                        </div>
                      </div>
                    </div>
              </div>

              <div class="columns is-desktop">
                    <!-- First column -->
                    <div class="column" style="width: 100%;">
                      <label>Purchase Condition *</label>
                      <div class="select  is-link" style="width: 100%;">
                        <select ng-model="copy.purchase_condition" ng-required="true" style="width: 100%;">
                          <option value="">--- not set ---</option>
                          <option value="New">New</option>
                          <option value="Pre-Owned">Pre-Owned</option>
                        </select>
                      </div>
                    </div>
                    <!-- Second column -->
                    <div class="column" style="width: 100%;">
                      <label>Copy Condition *</label>
                      <div class="select  is-link" style="width: 100%;">
                        <select ng-model="copy.copy_condition" ng-required="true" style="width: 100%;">
                          <option value="">--- not set ---</option>
                          <option value="1">1</option>
                          <option value="2">2</option>
                          <option value="3">3</option>
                          <option value="4">4</option>
                          <option value="5">5</option>
                        </select>
                      </div>
                    </div>
              </div>

              <div class="columns is-desktop">
                    <!-- First column -->
                    <div class="column">
                      <div class="control">
                        <label>Purchase Price *</label>
                        <input class="input" type="number"  step="0.01" min="0" max="99999" ng-required="true" ng-model="copy.purchase_price" placeholder="Purchase Price">
                      </div>
                    </div>
                    <!-- Second column -->
                    <div class="column">
                      <div class="control">
                        <label>Copy MRP</label>
                        <input class="input" type="number"  step="0.01" min="0" max="10000" ng-model="copy.mrp" placeholder="Copy MRP">
                      </div>
                    </div>
              </div>
                  
              <div class="columns is-desktop">
                <div class="column is-one-fifth file is-boxed" >
                  <figure class="image is-1by1">
                      <img ng-src="{{ copy.image_url }}">
                  </figure>
                  <div class="file is-medium is-boxed is-fullwidth">
                    <label class="file-label">
                      <input class="file-input" type="button" value="Upload latest Image of copy" ng-click="copy_media_upload()" />
                     
                        <span class="file-cta button is-flex is-align-items-center" style="height: 5rem;">
                            <span class="file-icon">
                                <i class="fas fa-upload"></i>
                            </span>
                            <span class="file-label">
                                Upload Cover Image
                            </span>
                        </span>
                    </label>
                  </div>
                </div>
                <div class="column">
                  <label class="label">Comments</label>
                  <textarea class="textarea" ng-model="copy.comments" placeholder="Comments"></textarea>
                </div>
              </div>     
              
              <div class="columns is-desktop">
                <div class="column">
                  <label class="switch">
                    <input type="checkbox" ng-model="copy.on_hold">
                    <span class="slider"></span>
                  </label>
                  <span>On Hold</span>
                </div>
              </div>
              
              <div class="columns is-desktop">
                <div class="column">
                  <button class="button is-text" ng-click="navigate_page('product_detail');">
                  <span><h2><strong>Continue without creating copy</strong></h2></span>
                  </button>
                </div>

                <div class="column">
                  <button class="button is-black is-pulled-right"  ng-class="{'is-loading': isSearchloading}" type="submit">
                    <span class="icon">
                      <i class="fas fa-plus"></i>
                    </span>
                    <span>Save Copy</span>
                  </button>
                </div>
              </div>

            </div>

          </form>
        </div><!-- Page end -->
        

        <!-- Product Details Page -->
        <div class="container product-details-box" ng-show="current_page=='product_detail'">
          <h1>ISBN: <strong>{{ product.isbn13 }}</strong></h1><br>
          <h1>Product ID: <strong>{{ product.smart_product_id }}</strong></h1><br>
          Copy Created: <strong>{{ copy.smart_copy_id }}</strong><br><br>

          <div class="box" style="background-color: rgb(228, 228, 228);">
              <strong>
                  <p ng-if="product.source == 'isbndb'">API (ISBNDB)</p>
                  <p ng-if="product.source == 'woocommerce'">Existing Inventory (WOOCOMMERCE)</p>
              </strong>

              <div class="columns">
                  <div class="column is-one-fifth">
                      <figure class="image is-1by1">
                          <img ng-src="{{ product.image_url }}">
                      </figure>
                  </div>

                  <div class="column">
                      <h4>
                          <strong>
                              <a ng-href="/wp-admin/post.php?post={{ product.woocommerce_product_id }}&action=edit" target="_blank">{{ product.book_title }}</a>
                          </strong>
                      </h4>
                      <div ng-repeat="author in product.authors">
                          <p>{{ author.name }} </p>
                      </div>
                      <p>{{ product.description | limitTo: 50 }}</p>
                      <p>{{ product.pages }}</p>
                      <p>{{ product.copy_ids }}</p><br>
                      <div class="control">
                          <a class="button is-dark is-medium" target="_blank" ng-href="{{ product.product_url }}">
                              <span class="icon">
                                  <i class="fas fa-plus"></i>
                              </span>
                              <span>View Product</span>
                          </a>
                      </div>
                  </div>
              </div>
          </div><br><br>

          <div class="column is-flex is-justify-content-center">
              <button class="button is-dark is-medium" ng-click="bt_home()">
                  <span class="icon">
                      <i class="fas fa-home"></i>
                  </span>
                  <span>Home</span>
              </button>
          </div>
        </div>

      </section>
    </div>


<style>
  .select select {
    background: unset;
      background-color: unset;
  }
  .select button{
      width:100%;
  }

  .switch {
    font-size: 17px;
    position: relative;
    display: inline-block;
    width: 3.5em;
    height: 2em;
  }

  .switch input {
    opacity: 0;
    width: 0;
    height: 0;
  }

  .slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #fff;
    border: 1px solid #adb5bd;
    transition: .4s;
    border-radius: 30px;
  }

  .slider:before {
    position: absolute;
    content: "";
    height: 1.4em;
    width: 1.4em;
    border-radius: 20px;
    left: 0.27em;
    bottom: 0.25em;
    background-color: #adb5bd;
    transition: .4s;
  }

  input:checked + .slider {
    background-color: #007bff;
    border: 1px solid #007bff;
  }

  input:focus + .slider {
    box-shadow: 0 0 1px #007bff;
  }

  input:checked + .slider:before {
    transform: translateX(1.4em);
    background-color: #fff;
  }
</style>
<script>
  const fileInput = document.querySelector('#csv-file');
  fileInput.onchange = () => {
    if (fileInput.files.length > 0) {
      angular.element(document.getElementById('new_product_controller')).scope().onCSVFileSelected(); 
    }
  }
</script>