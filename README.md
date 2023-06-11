# Http-Manager
 php 8 Attributes based Http Routing. Can be used for all cases, MVC and Middleware -  Class/Method/Function


*** QUICK HOW TO:
===================

```
<?php

include dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . "autoloader.php"; //composer autoloader

// <!-- Controller Class file -->
use Emma\Http\Mappings\RequestMapping;
use Emma\Http\Mappings\PostMapping;
use Emma\Http\Mappings\GetMapping;
use Emma\Http\Request\Method;

#[RequestMapping(routes: '/index', httpRequestMethod: [Method::POST, Method::GET])]
class IndexController
{

    /**
     *   Full Routing to this method is: '/index/login' -> Class level routing plus the method level
     *   Class Method can only be accessed via HTTP - POST
     */
    #[PostMapping('/login')] 
    public function login()
    {

    }

    /**
     * Class Method can be accessed via HTTP - POST and GET
     */ 
    #[RequestMapping('/logout', [Method::POST, Method::GET])]
    public function logout()
    {

    }

    /**
     * Class Method can be accessed via HTTP - GET
     * Auto-Map expected url parameter to the method
     */ 
    #[GetMapping('/count-trades/{status:[\w]+}')]
    public function countTradesByStatus(string $status)
    {

    }

    /**
    * Other Request Mapping Attributes exist....For example:
    *
    *   #[HeadMapping('/head-method-routing')]
    *   #[PutMapping('/upload')]
    *   #[PatchMapping('//summary/{id:[0-9]*}')]
    *   #[DeleteMapping('/delete-all')]
    *   #[OptionsMapping('/option/')]
    */

}

/**
 * REGISTER ALL ROUTES HANDLERS IN :  ./Definition/Config.php
 * ===========================================
 * 
 * @return array 
 * Register your Controllers, classes and/or Middleware and/or Functions here...

    use \Emma\Http\Mappings\PatchMapping;

    return [
        IndexController::class,

        ...

//example: Adding your function directly to the array.  

        #[PatchMapping('/update/summary/{id:[0-9]*}')]
        function middlewareQuickPatch(): void {
            $result = ['status' => true, 'data' => 'ABCD'];
            die(json_encode($result));
        },
    ];

 *    ADVANCED USERS can have there arrays of functions and/or classes in different file and includes those file with array_merge()
 *    For Example:
 
    return array_merge(
        (array) include "directory_to_array_file/class_file.php",
        (array) include "directory_to_array_file/direct_method_file.php",
        (array) include "directory_to_array_file/functions_file.php",
    );

* THEN, class_file.php:
* =====================
    return [
        IndexController::class,
        ...
    ];

    Other file will simply follow class_file example...
 */


// <!-- URL front entry point...Assuming you already setup your .htaccess as needed. 
// This service is independent of .htaccess. -->

$testHttpManager = new \Emma\Http\HttpManager();

try {
    $route = $testHttpManager->boot()->matchRequestRoutes();

    var_dump($route);

    /** Feel free to use https://github.com/debascoguy/Di For Autowiring(that is, Injecting) your classes/Methods/Function Dependencies... */

} catch (Exception $e) {

    die($e->getMessage());

}

