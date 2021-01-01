<?php
    if(isset($_POST['foodItem'])){
        // Get connection details from file
        $connectionDetails = fopen("p", "r") or exit("Unable to open p!");
            $host = trim(fgets($connectionDetails));
            $user = trim(fgets($connectionDetails));
            $pass = trim(fgets($connectionDetails));
            $data = trim(fgets($connectionDetails));
        fclose($connectionDetails);
        // Connect to database
        $databaseConnection = new mysqli($host, $user, $pass, $data);

        mysqli_set_charset($databaseConnection, "utf8");
        $results = array('error' => false, 'data' => '');
 
        $querystr = $_POST['foodItem'];
 
        if(empty($querystr)){
            $results['error'] = true;
        }
        else
        {
            $query = "SELECT Livsmedel FROM slv_nutrition WHERE Livsmedel LIKE '%$querystr%'";
            $result = mysqli_query($databaseConnection, $query);
 
            if($result->num_rows > 0){
                while($dataRow = $result->fetch_assoc()){
                    $results['data'] .= "<li class='autocomplete-listitem' data-fooditem='".$dataRow['Livsmedel']."'>".$dataRow['Livsmedel']."</li>";
                }
            }
        }

        echo json_encode($results);
    }
?>