<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    /**
     * @OA\Info(
     *      version="1.0.0",
     *      title="Documentation Api For Helpinghnds App ",
     *      description="L5 Swagger OpenApi description",
     *      @OA\Contact(
     *          email="vdphong1995@gmail.com"
     *      )
     * )
     * @OA\Server(
     *      url=L5_SWAGGER_CONST_HOST,
     *      description="Demo Local"
     * )
     */
    //------- FOR USERS ---------
    /**
        * @OA\Get(
        * path="/api/v1/users/user",
        * description="Get user detail",
        * summary="Get user detail",
        * tags={"Users"},
        * @OA\SecurityScheme(
        * securityScheme="bearerAuth",
        * in="header",
        * name="bearerAuth",
        * type="http",
        * scheme="bearer",
        * bearerFormat="JWT",
        * ),
        * security={
        *   {"bearerAuth": {}}
        * },
        * @OA\Response(response="200", description="Success", @OA\JsonContent()),
        * @OA\Response(response="401", description="Unauthenticated"),
        *)
     */  
    /**
        * @OA\Get(
        * path="/api/v1/users/list-location",
        * description="Get top 20 location for user",
        * summary="Get top 20 location for user",
        * tags={"Users"},
        * @OA\SecurityScheme(
        * securityScheme="bearerAuth",
        * in="header",
        * name="bearerAuth",
        * type="http",
        * scheme="bearer",
        * bearerFormat="JWT",
        * ),
        * security={
        *   {"bearerAuth": {}}
        * },
        * @OA\Response(response="200", description="Success", @OA\JsonContent()),
        * @OA\Response(response="401", description="Unauthenticated"),
        *)
     */  
    /**
        * @OA\Get(
        * path="/api/v1/users/workers",
        * description="Get user worker for cline",
        * summary="Get worker for user client",
        * tags={"Users"},
        * @OA\SecurityScheme(
        * securityScheme="bearerAuth",
        * in="header",
        * name="bearerAuth",
        * type="http",
        * scheme="bearer",
        * bearerFormat="JWT",
        * ),
        * security={
        *   {"bearerAuth": {}}
        * },
        * @OA\RequestBody(
            * required=true, 
            * @OA\JsonContent( 
            * type="object",
            * required={"latitude","longtitude"}, 
            * @OA\Property(property="latitude", type="string", example="10.77624100"), 
            * @OA\Property(property="longtitude", type="string", example="106.639244"), 
            * ), 
        * ),
        * @OA\Response(response="200", description="Success", @OA\JsonContent()),
        * @OA\Response(response="401", description="Unauthenticated"),
        *)
     */ 
    /**
    * @OA\Post(
            * path="/api/v1/users/send-otp",
            * description="Get OTP",
            * summary="Get OTP",
            * tags={"Users"},
            * @OA\RequestBody( 
                * required=true, 
                * @OA\JsonContent( 
                * type="object",
                * required={"recaptcha_token","phone"}, 
                * @OA\Property(property="phone", type="string", example="+84123456789"), 
                * @OA\Property(property="recaptcha_token", type="string", example="AIzaSyDwmeqwijVzJS04VIHw5v2wMEisY2qzdmMAIzaSyDwmeqwijVzJS04VIHw5v2wMEisY2qzdmM"), 
                * ), 
            * ),
            * @OA\Response(response="200", description="Success", @OA\JsonContent()),
            * @OA\Response(response="422", description="The given data was invalid.")
        *)
     */  
    /**
    * @OA\Post(
            * path="/api/v1/users/verify-otp",
            * description="Verify OTP",
            * summary="Verify OTP",
            * tags={"Users"},
            * @OA\RequestBody( 
                * required=true, 
                * @OA\JsonContent( 
                * type="object",
                * required={"recaptcha_token","phone"}, 
                * @OA\Property(property="phone", type="string", example="+84123456789"), 
                * @OA\Property(property="token", type="string", example="AIzaSyDwmeqwijVzJS04VIHw5v2wMEisY2qzdmMAIzaSyDwmeqwijVzJS04VIHw5v2wMEisY2qzdmM"), 
                * ), 
            * ),
            * @OA\Response(response="200", description="Success", @OA\JsonContent()),
            * @OA\Response(response="422", description="The given data was invalid.")
        *)
     */  
    /**
    * @OA\Post(
            * path="/api/v1/users/signup/user",
            * description="Register Client",
            * summary="Signup user client",
            * tags={"Users"},
            * @OA\RequestBody( 
                * required=true, 
                * @OA\JsonContent( 
                * type="object",
                * required={"first_name","last_name","password","phone"}, 
                * @OA\Property(property="first_name", type="string", example="Jon"), 
                * @OA\Property(property="last_name", type="string", example="Smith"), 
                * @OA\Property(property="password", type="string", format="password", example="PassWord12345"), 
                * @OA\Property(property="phone", type="string", example="+84123456789"), 
                * ), 
            * ),
            * @OA\Response(response="200", description="Success", @OA\JsonContent()),
            * @OA\Response(response="422", description="The given data was invalid.")
        *)
     */
    /**
    * @OA\Post(
            * path="/api/v1/users/signup/worker",
            * description="Register Worker",
            * summary="Signup user worker",
            * tags={"Users"},
            * @OA\RequestBody( 
                * required=true, 
                * @OA\JsonContent(
                * type="object",
                * required={"first_name","last_name","password","phone","address","gender","number_id","type_number_id","img_id_before","img_id_after"}, 
                * @OA\Property(property="first_name", type="string", example="Jon"), 
                * @OA\Property(property="last_name", type="string", example="Smith"), 
                * @OA\Property(property="password", type="string", format="password", example="PassWord12345"), 
                * @OA\Property(property="phone", type="string", example="+84123456789"), 
                * @OA\Property(property="address", type="string", example="168 Khuong viet, Q.Tan Phu, P.Phu Trung"), 
                * @OA\Property(property="gender", type="string", example="f"), 
                * @OA\Property(property="number_id", type="number", example="251039383"), 
                * @OA\Property(property="type_number_id", type="number", example="1(cmnd),2(cccd),3(GPLX)"), 
                * @OA\Property(property="img_id_before", type="string", example="base 64 encode image"), 
                * @OA\Property(property="img_id_after", type="string", example="base 64 encode image"), 
                * ), 
            * ),
            * @OA\Response(response="200", description="Success", @OA\JsonContent()),
            * @OA\Response(response="422", description="The given data was invalid.")
        *)
     */
    /**
    * @OA\Post(
            * path="/api/v1/users/login",
            * description="User login",
            * summary="Login",
            * tags={"Users"},
            * @OA\RequestBody( 
                * required=true, 
                * @OA\JsonContent( 
                * type="object",
                * required={"password","phone"}, 
                * @OA\Property(property="phone", type="string", example="+84123456789"), 
                * @OA\Property(property="password", type="string", format="password", example="PassWord12345"), 
                * ), 
            * ),
            * @OA\Response(response="200", description="Success", @OA\JsonContent()),
            * @OA\Response(response="401", description="Unauthenticated!"),
            * @OA\Response(response="422", description="The given data was invalid.")
        *)
     */
    /**
        * @OA\Post(
        * path="/api/v1/users/user/update-location",
        * description="Update location of user",
        * summary="Update location for user",
        * tags={"Users"},
        * @OA\SecurityScheme(
        * securityScheme="bearerAuth",
        * in="header",
        * name="bearerAuth",
        * type="http",
        * scheme="bearer",
        * bearerFormat="JWT",
        * ),
        * security={
        *   {"bearerAuth": {}}
        * },
        * @OA\RequestBody(
            * required=true, 
            * @OA\JsonContent( 
            * type="object",
            * required={"latitude","longtitude"}, 
            * @OA\Property(property="latitude", type="string", example="10.77624100"), 
            * @OA\Property(property="longtitude", type="string", example="106.639244"), 
            * ), 
        * ),
        * @OA\Response(response="200", description="Success", @OA\JsonContent()),
        * @OA\Response(response="401", description="Unauthenticated"),
        *)
     */

    //--------- FOR COUNTRIES ---------
    /**
        * @OA\Get(
        * path="/api/v1/countries",
        * description="Get list countries",
        * summary="List countries",
        * tags={"Countries"},
        * @OA\Response(response="200", description="Success"),
        *)
     */
    //--------- FOR SERVICES ---------
    /**
        * @OA\Get(
        * path="/api/v1/services/list",
        * description="Get list services",
        * summary="List services",
        * tags={"Services"},
        * @OA\Response(response="200", description="Success"),
        * @OA\Response(response="401", description="Unauthenticated"),
        *)
     */
    //--------- FOR ORDERS ---------
    /**
        * @OA\Get(
        * path="/api/v1/orders/1/status",
        * description="Get status for order",
        * summary="Get status for order",
        * tags={"Orders"},
        * @OA\Response(response="200", description="Success"),
        *)
     */
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
