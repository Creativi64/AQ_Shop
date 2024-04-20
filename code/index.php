<?php
http_response_code(404);
die();
print("Test123");

$plain_text_password = "9c293935de4f56ecd09defe10ec6cc8d"; // The password you want to hash
$hashed_password = password_hash($plain_text_password, PASSWORD_BCRYPT);

print("<p>");
print($hashed_password);
print("</p>");
if (password_verify("yourpassword", $hashed_password)) {
    print("<p>true</p>");
}else {
    print("<p>false</p>");}
    
    ?>
    <h1>test1</h1>