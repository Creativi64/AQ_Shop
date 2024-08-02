<h1>test</h1>
<?php 

#phpinfo();

$requestExisitng = json_encode(array(
    "function" => "getArticle",
    "paras" => array(
      "user" => "Test",
      "pass" => "Test12345!",
      "products_id" => 1047, #1047,
      "external_id" => "",
      "indivFieldList" => []
    )
  ));

$responseExisting = CallAPI('POST','https://shop.aquarus.net/index.php?page=xt_api', $requestExisitng);

$ex = json_decode($responseExisting);
 

print("<hr/>");
var_dump ( isset($_POST['products_url']) ? $_POST['products_url']: null );
 
print("<hr/>");

print("<pre>");
print_r($ex);
$z = null;

print_r($ex->{"productsItemExport"}->{"products_name"}->{"en"});

if(isset($ex->{"productsItemExport"})) {
    $z= $ex->{"productsItemExport"};
}
#$request = "asdddddddddd";
$request = null;
#$db ="asd";
$db =null;

print_r($db);

print("<hr/>");
$db = ($request!=null) ? $request : (($existing!=null) ? $existing->{""} : null);
print_r(date("Y-m-d H:i:s"));

print("</pre>");


function GetValues($fielnName, $existing) {
    $postValue = isset($_POST[$fielnName]) ? $_POST[$fielnName]: null;
    $dbValue = isset($existing->{$fielnName}) ? $existing->{$fielnName}: null;
    return ($postValue!=null) ? $postValue : (($dbValue!=null) ? $dbValue : null);
}


// print("<hr/>");
// $existing = $ex->{"productsItemExport"};
// $existing -> {"productDescriptionsList"} = null;
// $existing -> {"productCategoriesList"} = null;


// print(json_encode($existing));
// print("<pre>");
// print_r($existing);
// print("</pre>");




function CallAPI($method, $url, $data = false)
{
    $curl = curl_init();

    switch ($method)
    {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);

            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
        case "PUT":
            curl_setopt($curl, CURLOPT_PUT, 1);
            break;
        default:
            if ($data)
                $url = sprintf("%s?%s", $url, json_encode($data));
    }

    // Optional Authentication:
   // curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    //curl_setopt($curl, CURLOPT_USERPWD, "finosScript:Superkarl123!");
    // -----------------------
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    // ---------
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($curl);

    print_r(curl_error($curl));
    
    curl_close($curl);

    return $result;
}

?>



    