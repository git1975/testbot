<?php

/**
 * Created by PhpStorm.
 * User: gmananton
 * Date: 03.12.2015
 * Time: 23:42
 */
class WSClient{

 function test(){
     $soapClient = new SoapClient("http://www.webservicex.com/globalweather.asmx?WSDL");


 }
}