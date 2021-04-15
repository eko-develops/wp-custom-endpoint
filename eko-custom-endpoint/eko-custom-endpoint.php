<?php
/**
* Plugin Name: EKO Custom Endpoint
* Description: The plugin creates the API custom endpoints and namespaces.
* Version: 1.0
* Author: ekoret
**/

/** 
 * Docs Used:
 * https://developer.wordpress.org/reference/functions/register_rest_route/
 * 
 */

/*  First we need to register a route. This will will tell the API
 to respond to a given request with our function. The callback
 function automatically gets the response to use.
 */
 add_action('rest_api_init', function(){
    /*
    register_rest_route( 
    string $namespace,          req
    string $route,              req
   array $args = array(),       opt
    bool $override = false )    opt
    */
        register_rest_route( 'eko/', 'search' , array(
            //There is also POST, UPDATE, DELETE, etc.
            'method' => 'GET',
            //The function that gets the data for the route
            'callback' => 'eko_search'
        ));
 }
);



function eko_search($request){

    //https://developer.wordpress.org/rest-api/requests/#parameters
    //https://github.com/woocommerce/woocommerce/wiki/wc_get_products-and-WC_Product_Query

    //If API is requesting a search
    if( isset($request['s']) ){

        //We'll be using the wc_get_products as it is the standard way to retrieve all products
        $products = wc_get_products(array(
            'status' => 'published',
            'limit' => -1,
            's' => $request['s']
        ));

        //Initialize an empty array to hold the products as objects
        $results = [];
        //For each product in products, we'll create an object with the data we want as properties.
        foreach($products as $product){
            $results[] = array(
                    'name' => $product->name,
                    'regular_price' => $product->regular_price,
                    'stock_status' => $product->stock_status
            );
        }//endforeach

        //We only want to return maximum 5 results
        $trimmedResults = array_slice($results, 0, 5);


        //Throw an error if there are no results
        if( empty($results)){
        $message = "No results found...";
        return rest_ensure_response($message);
        }

        //Checks the REST response is a response object
        return rest_ensure_response($trimmedResults);
    }//endif

    


}//end eko_search



/* 
These functions are not used anymore.
 */



/* 
This function is used if we want to set the parameters to a property.
We do not need it for this version of the plugin.
 */
// function eko_get_search_args() {
//     $args = [];
//     $args['s'] = [
//        'type'        => 'string',
//    ];
//    return $args;
// }