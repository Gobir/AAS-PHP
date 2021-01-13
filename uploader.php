<?php
session_start();
if (isset($_SESSION["response"])) {
    echo $_SESSION["response"];
    unset($_SESSION["response"]);
}
?>
<!DOCTYPE html>
<html>
    <body>
        <form action="upload.php" method="post" enctype="multipart/form-data">
            Select image to upload:
            <input type="file" name="fileToUpload" id="fileToUpload">
            <input type="submit" value="Upload Image" name="submit">
        </form>
    </body>
</html>
