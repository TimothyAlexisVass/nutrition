<?php

/*
function consoleLog($output) {
    echo '<script>console.log(' . json_encode($output, JSON_HEX_TAG) . ');</script>';
} 
*/

// Get connection details from file
$connectionDetails = fopen("p", "r") or exit("Unable to open p!");
    $host = trim(fgets($connectionDetails));
    $user = trim(fgets($connectionDetails));
    $pass = trim(fgets($connectionDetails));
    $data = trim(fgets($connectionDetails));
fclose($connectionDetails);
// Connect to database
$_SESSION["databaseConnection"] = new mysqli($host, $user, $pass, $data);
mysqli_set_charset($_SESSION["databaseConnection"], "utf8");
// Check connection
/*
if (mysqli_error($_SESSION["databaseConnection"])) {
    exit(consoleLog("Connection failed: " . $_SESSION["databaseConnection"]->connect_error));
}
else {
    consoleLog("Connected successfully");  
}
*/
function queryDatabase($query, $logMessage) {
    $result = mysqli_query($_SESSION["databaseConnection"], $query);
/*
    if($result) {
        consoleLog($logMessage);
    }
    else {
        consoleLog(mysqli_error($_SESSION["databaseConnection"]));
    }
*/
}

function getBodyInfo($name) {
//    consoleLog("Getting body info");
    $query = "SELECT `gender`, `age`, `weight`, `activityLevel` FROM `bodyInfo` WHERE `name` = '$name'";
    if ($result = mysqli_query($_SESSION["databaseConnection"], $query)) {
        $row = $result->fetch_assoc();
        if ($row["age"] != '') {
            $bodyInfo["gender"] = $row["gender"];
            $bodyInfo["age"] = $row["age"];
            $bodyInfo["weight"] = $row["weight"];
            $bodyInfo["activityLevel"] = $row["activityLevel"];
        }
        else {
            $bodyInfo["gender"] = 1.25;
            $bodyInfo["age"] = 30;
            $bodyInfo["weight"] = 80;
            $bodyInfo["activityLevel"] = 1.12;
        }   
    }
    return $bodyInfo;
}

function setBodyInfo($name, $gender, $age, $weight, $activityLevel) {
    $query = "INSERT INTO `bodyInfo`(`name`, `gender`, `age`, `weight`, `activityLevel`) VALUES ('$name', $gender, $age, $weight, $activityLevel) ON DUPLICATE KEY UPDATE `name` = '$name', `gender` = $gender, `age` = $age, `weight` = $weight, `activityLevel` = $activityLevel;";
    queryDatabase($query, "Body info successfully set.");
}

function addItemToFoodList($name, $foodItem, $amount) {
    $query = "INSERT INTO `foodList`(`name`, `foodItem`, `amount`) VALUES ('$name', '$foodItem', $amount) ON DUPLICATE KEY UPDATE `amount` = $amount;";
    queryDatabase($query, "Food item added successfully.");
}

function clearItemFromFoodList($name, $foodItem) {
    $query = "DELETE FROM `foodList` WHERE `name` = '$name' AND `foodItem` = '$foodItem';";
    queryDatabase($query, "Food item cleared successfully.");
}

function clearInfo($name) {
    $query = "DELETE FROM `foodList` WHERE `name` = '$name';";
    queryDatabase($query, "Cleared foodList.");
    // $query = "DELETE FROM `bodyInfo` WHERE `name` = '$name';";
    // queryDatabase($query, "Cleared bodyInfo.");
}

function getFoodList($name) {
//    consoleLog("Getting foodList");
    $query = "SELECT `foodItem`, `amount` FROM `foodList` WHERE `name` = '$name';";
    if ($result = mysqli_query($_SESSION["databaseConnection"], $query)) {
        while ($row = $result->fetch_assoc()) {
            $foodList[$row["foodItem"]] = $row["amount"];
        }
    }
    return $foodList;
}

function getNutritionData($foodListKeysString) {
//    consoleLog("Getting nutritionData"); 
    $query = "SELECT * FROM `nutrition` WHERE `Livsmedel` IN ($foodListKeysString)";
    if ($result = mysqli_query($_SESSION["databaseConnection"], $query)) {
        while ($row = $result->fetch_assoc()) {
            $nutritionData[ $row["Livsmedel"] ] = [
                "item" => $row["Livsmedel"],
                "Energy" => $row["Energi (kcal)"],
                "Carbohydrates" => $row["Kolhydrater (g)"],
                "Fat" => $row["Fett (g)"],
                "Protein" => $row["Protein (g)"],
                "Fiber" => $row["Fibrer (g)"],
                "Water" => $row["Vatten (g)"],
                "Monosaccharides" => $row["Monosackarider (g)"],
                "Sucrose" => $row["Sackaros (g)"],
                "Other Disaccharides" => $row["Disackarider (g)"] - $row["Sackaros (g)"],
                "Whole Grain" => $row["Fullkorn totalt (g)"],
                "Saturated" => $row["Summa mättade fettsyror (g)"],
                "Fatty acids C4:0-10:0" => $row["Fettsyra 4:0-10:0 (g)"],
                "Lauric acid C12:0" => $row["Laurinsyra C12:0 (g)"],
                "Myristic acid C14:0" => $row["Myristinsyra C14:0 (g)"],
                "Palmitic acid C16:0" => $row["Palmitinsyra C16:0 (g)"],
                "Stearic acid C18:0" => $row["Stearinsyra C18:0 (g)"],
                "Arachidic acid C20:0" => $row["Arakidinsyra C20:0 (g)"],
                "Monounsaturated" => $row["Summa enkelomättade fettsyror (g)"],
                "Palmitoic acid C16:1" => $row["Palmitoljesyra C16:1 (g)"],
                "Oleic acid C18:1" => $row["Oljesyra C18:1 (g)"],
                "Polyunsaturated" => $row["Summa fleromättade fettsyror (g)"],
                "Linoleic acid C18:2" => $row["Linolsyra C18:2 (g)"],
                "Arachidonic acid C20:4" => $row["Arakidonsyra C20:4 (g)"],
                "Linolenic acid C18:3" => $row["Linolensyra C18:3 (g)"],
                "EPA" => $row["EPA (C20:5) (g)"],
                "DPA" => $row["DPA (C22:5) (g)"],
                "DHA" => $row["DHA (C22:6) (g)"],
                "Cholesterol" => $row["Kolesterol (mg)"],
                /*"Retinol" => $row["Retinol (µg)"],
                "Vitamin A" => round(round($row["Vitamin A (RE)"], 1) - round($row["β-Karoten (µg)"] / 12, 1) - $row["Retinol (µg)"], 1),
                "β-Carotene" => round($row["β-Karoten (µg)"] / 12, 1),*/
                "Vitamin A" => $row["Vitamin A (RE)"],
                "Vitamin D" => $row["Vitamin D (µg)"],
                "Vitamin E" => $row["Vitamin E (mg)"],
                "Vitamin K" => $row["Vitamin K (µg)"],
                "Thiamine" => $row["Tiamin (mg)"],
                "Riboflavin" => $row["Riboflavin (mg)"],
                "Vitamin C" => $row["Vitamin C (mg)"],
                "Niacin" => $row["Niacin (mg)"],
                "Niacin Equivalents" => $row["Niacinekvivalenter (NE)"],
                "Vitamin B6" => $row["Vitamin B6 (mg)"],
                "Vitamin B12" => $row["Vitamin B12 (µg)"],
                "Folate" => $row["Folat (µg)"],
                "Phosphorus" => $row["Fosfor (mg)"],
                "Iodine" => $row["Jod (µg)"],
                "Iron" => $row["Järn (mg)"],
                "Calcium" => $row["Kalcium (mg)"],
                "Potassium" => $row["Kalium (mg)"],
                "Copper" => $row["Koppar (mg)"],
                "Magnesium" => $row["Magnesium (mg)"],
                "Sodium" => $row["Natrium (mg)"],
                "Salt" => $row["Salt (g)"],
                "Selenium" => $row["Selen (µg)"],
                "Zinc" => $row["Zink (mg)"],
                "Starch" => $row["Stärkelse (g)"],
            ];
        }
        $result->free();
    }
    return $nutritionData;
}

function getTopFoodsForNutrient($field) {
//    consoleLog("Getting top 30 foods for $field"); 
    /*if ($field == "Vitamin A (RE)") {
        $specifySelection = "WHERE `$field` < 1000";
    }
    //elseif ($field == "Riboflavin (mg)") {
    //    $specifySelection = "WHERE `$field` < 2";
    //}
    elseif ($field == "Folat (µg)") {
        $specifySelection = "WHERE `$field` < 1000";
    }
    elseif ($field == "Jod (µg)") {
        $specifySelection = "WHERE `$field` < 140";
    }
    else {*/
        $specifySelection = "";
    //}
    $query = "SELECT `Livsmedel`, `$field` FROM `nutrition` $specifySelection ORDER BY `$field` DESC LIMIT 30";
    if ($result = mysqli_query($_SESSION["databaseConnection"], $query)) {
        while ($row = $result->fetch_assoc()) {
            $nutritionData[ $row["Livsmedel"] ] = [
                "item" => $row["Livsmedel"],
                "$field" => $row["$field"],
            ];
        }
        $result->free();
    }
    return $nutritionData;
}

function getNutritionTotals($foodList, $nutritionData) {
    //$foodList contains all foods that have been added to the calculation
    //$nutritionData contains all nutritional data for all food items in the database
    $totals[Amount] = array_sum($foodList);
    //loop through the food list
    foreach($foodList as $foodItem => $amount) {
    //and add the nutritional value 
        foreach($nutritionData[$foodItem] as $nutrient => $value) {
            $totals[$nutrient] += $value / 100 * $amount;
            if($totals[$nutrient] > 20) {
                $totals[$nutrient] = round($totals[$nutrient]);
            }
            else if($totals[$nutrient] > 10) {
                $totals[$nutrient] = round($totals[$nutrient], 1);
            }
        }
    }
    return $totals;
}

function getEnergyDistribution($totals) {
    $energyDistribution[Carbohydrates] = round($totals[Carbohydrates] * kcalPerGram(Carbohydrates) / $totals[Energy], 3);
    $energyDistribution[Fat] = round($totals[Fat] * kcalPerGram(Fat) / $totals[Energy], 3);
    $energyDistribution[Protein] = round($totals[Protein] * kcalPerGram(Protein) / $totals[Energy], 3);
    $energyDistribution[Fiber] = 1 - $energyDistribution[Carbohydrates] - $energyDistribution[Fat] - $energyDistribution[Protein];
    return $energyDistribution;
}

function getMacroNutrients() {
    return ["Carbohydrates", "Fat", "Protein", "Fiber"];
}

function getMicroNutrients() {
    return ["Vitamin A", "Vitamin B6", "Vitamin B12", "Vitamin C", "Vitamin D", "Vitamin E", "Vitamin K", "Thiamine", "Riboflavin", "Niacin Equivalents", "Folate", "Phosphorus", "Iodine", "Iron", "Calcium", "Potassium", "Copper", "Magnesium", "Sodium", "Selenium", "Zinc"];
}

function getSums() {
    return ["Water", "Salt", "Cholesterol", "Carbohydrates", "Fat", "Saturated", "Monounsaturated", "Polyunsaturated", "Protein"];
}

function getContents() {
    return ["Water", "Salt", "Cholesterol"];
}

function getCarbohydrates() {
    return ["Carbohydrates", "Monosaccharides", "Sucrose", "Other Disaccharides", "Starch"];
}

function getSaturatedFattyAcids() {
    return ["Fat", "Saturated", "Fatty acids C4:0-10:0", "Lauric acid C12:0", "Myristic acid C14:0", "Palmitic acid C16:0", "Stearic acid C18:0", "Arachidic acid C20:0"];
}

function getMonoUnsaturatedFattyAcids() {
    return ["Monounsaturated", "Palmitoic acid C16:1", "Oleic acid C18:1"];
}

function getPolyUnsaturatedFattyAcids() {
    return ["Polyunsaturated", "Linoleic acid C18:2", "Arachidonic acid C20:4", "Linolenic acid C18:3", "EPA", "DPA", "DHA"];
}

function getProteins() {
    return ["Protein"];
}

function getVitamins() {
    return ["Retinol", "Vitamin A", "β-Carotene", "Vitamin D", "Vitamin E", "Vitamin K", "Thiamine", "Riboflavin", "Vitamin C", "Niacin", "Niacin Equivalents", "Vitamin B6", "Vitamin B12", "Folate"];
}

function getMinerals() {
    return ["Phosphorus", "Iodine", "Iron", "Calcium", "Potassium", "Copper", "Magnesium", "Sodium", "Selenium", "Zinc"];
}

function getDailyRecommendations($bodyInfo) {
    $gender = $bodyInfo[gender];
    $age = $bodyInfo[age];
    $weight = $bodyInfo[weight];
    $activityLevel = $bodyInfo[activityLevel];

    $array[dailyRequirements]["Energy"] = 2000 * $gender * $activityLevel;
    $MJenergy = calorieToJoule(2000 * $gender * $activityLevel)/1000.0;
    $array[dailyRequirements]["Carbohydrates"] = round(1000 * $gender * $activityLevel / kcalPerGram("Carbohydrates"), 1);
    $array[dailyRequirements]["Fat"] = round(640 * $gender * $activityLevel / kcalPerGram("Fat"), 1);
    // $array[dailyRequirements]["Protein"] = 1.1 * $weight;
    $array[dailyRequirements]["Protein"] = round(253 * $gender * $activityLevel / kcalPerGram("Protein"), 1);
    $array[dailyRequirements]["Fiber"] = round(3 * $MJenergy,1);
    $array[dailyRequirements]["Water"] = round(34 * $activityLevel * $gender * $weight); //3.4% of body weight for female at 2000 kcal
    $array[dailyRequirements]["Monosaccharides"] = "-";
    $array[dailyRequirements]["Sucrose"] = "-";
    $array[dailyRequirements]["Other Disaccharides"] = "-";
    $array[dailyRequirements]["Whole Grain"] = "-";
    $array[dailyRequirements]["Saturated"] = "-";
    $array[dailyRequirements]["Fatty acids C4:0-10:0"] = "-";
    $array[dailyRequirements]["Lauric acid C12:0"] = "-";
    $array[dailyRequirements]["Myristic acid C14:0"] = "-";
    $array[dailyRequirements]["Palmitic acid C16:0"] = "-";
    $array[dailyRequirements]["Stearic acid C18:0"] = "-";
    $array[dailyRequirements]["Arachidic acid C20:0"] = "-";
    $array[dailyRequirements]["Monounsaturated"] = "-";
    $array[dailyRequirements]["Palmitoic acid C16:1"] = "-";
    $array[dailyRequirements]["Oleic acid C18:1"] = "-";
    $array[dailyRequirements]["Polyunsaturated"] = "-";
    $array[dailyRequirements]["Linoleic acid C18:2"] = "-";
    $array[dailyRequirements]["Arachidonic acid C20:4"] = "-";
    $totals[Energy] < $array[dailyRequirements]["Energy"] ? $array[dailyRequirements]["Linolenic acid C18:3"] = round(calorieToJoule($array[dailyRequirements]["Energy"])/100/37,2) : $array[dailyRequirements]["Linolenic acid C18:3"] = round(calorieToJoule($totals[Energy])/100/37,1);
    $array[dailyRequirements]["EPA"] = "-";
    $array[dailyRequirements]["DPA"] = "-";
    $array[dailyRequirements]["DHA"] = 0.2;
    $array[dailyRequirements]["Cholesterol"] = "-";
    $array[dailyRequirements]["β-Carotene"] = "-";
    80 * $MJenergy <= 800 ? $array[dailyRequirements]["Vitamin A"] = 800 : $array[dailyRequirements]["Vitamin A"] = round(80 * $MJenergy);
    0.13 * $MJenergy <= 800 ? $array[dailyRequirements]["Vitamin B6"] = 1.4 : $array[dailyRequirements]["Vitamin B6"] = round(0.13 * $MJenergy,1);
    0.2 * $MJenergy <= 2.5 ? $array[dailyRequirements]["Vitamin B12"] = 2.5 : $array[dailyRequirements]["Vitamin B12"] = round(0.2 * $MJenergy,1);
    8 * $MJenergy <= 80 ? $array[dailyRequirements]["Vitamin C"] = 80 : $array[dailyRequirements]["Vitamin C"] = round(8 * $MJenergy);
    1.4 * $MJenergy <= 10 ? $array[dailyRequirements]["Vitamin D"] = 10 : $array[dailyRequirements]["Vitamin D"] = round(1.4 * $MJenergy,1);
    0.9 * $MJenergy <= 12 ? $array[dailyRequirements]["Vitamin E"] = 12 : $array[dailyRequirements]["Vitamin E"] = round(0.9 * $MJenergy,1);
    9 * $MJenergy <= 75 ? $array[dailyRequirements]["Vitamin K"] = 75 : $array[dailyRequirements]["Vitamin K"] = round(9 * $MJenergy);
    $array[dailyRequirements]["Retinol"] = "-";
    0.12 * $MJenergy <= 1.1 ? $array[dailyRequirements]["Thiamine"] = 1.1 : $array[dailyRequirements]["Thiamine"] = round(0.12 * $MJenergy,1);
    0.14 * $MJenergy <= 1.4 ? $array[dailyRequirements]["Riboflavin"] = 1.4 : $array[dailyRequirements]["Riboflavin"] = round(0.14 * $MJenergy,1);
    $array[dailyRequirements]["Niacin"] = "-";
    1.6 * $MJenergy <= 19 ? $array[dailyRequirements]["Niacin Equivalents"] = 19 : $array[dailyRequirements]["Niacin Equivalents"] = round(1.6 * $MJenergy);
    // 5.97 * $MJenergy <= 50 ? Biotine
    // 0.72 * $MJenergy <= 6 ? Pantothenic acid
    45 * $MJenergy <= 300 ? $array[dailyRequirements]["Folate"] = 300 : $array[dailyRequirements]["Folate"] = round(45 * $MJenergy);
    80 * $MJenergy <= 700 ? $array[dailyRequirements]["Phosphorus"] = 700 : $array[dailyRequirements]["Phosphorus"] = round(80 * $MJenergy);
    17 * $MJenergy <= 150 ? $array[dailyRequirements]["Iodine"] = 150 : $array[dailyRequirements]["Iodine"] = round(17 * $MJenergy);
    1.6 * $MJenergy <= 14 ? $array[dailyRequirements]["Iron"] = 14 : $array[dailyRequirements]["Iron"] = round(1.6 * $MJenergy,1);
    100 * $MJenergy <= 800 ? $array[dailyRequirements]["Calcium"] = 800 : $array[dailyRequirements]["Calcium"] = round(100 * $MJenergy);
    $array[dailyRequirements]["Potassium"] = round(350 * $MJenergy);
    0.1 * $MJenergy <= 1 ? $array[dailyRequirements]["Copper"] = 1 : $array[dailyRequirements]["Copper"] = round(0.1 * $MJenergy,1);
    // 0.24 * $MJenergy <= 2 ? Manganese
    // 0.42 * $MJenergy <= 3.5 ? Flouride
    // 4.78 * $MJenergy <= 40 ? Chromium
    // 5.97 * $MJenergy <= 50 ? Molybdenium
    32 * $MJenergy <= 375 ? $array[dailyRequirements]["Magnesium"] = 375 : $array[dailyRequirements]["Magnesium"] = round(32 * $MJenergy);
    $array[dailyRequirements]["Sodium"] = "-";
    $array[dailyRequirements]["Salt"] = "-";
    5.7 * $MJenergy <= 60 ? $array[dailyRequirements]["Selenium"] = 60 : $array[dailyRequirements]["Selenium"] = round(5.7 * $MJenergy);
    1.2 * $MJenergy <= 13 ? $array[dailyRequirements]["Zinc"] = 13 : $array[dailyRequirements]["Zinc"] = round(1.2 * $MJenergy,1);
    $array[dailyRequirements]["Starch"] = "-";
    $array[upperLimits]["Energy"] = "-";
    $array[upperLimits]["Carbohydrates"] = "-";
    $array[upperLimits]["Fat"] = "-";
    $array[upperLimits]["Protein"] = "-";
    $array[upperLimits]["Fiber"] = "-";
    $array[upperLimits]["Water"] = "-";
    $array[upperLimits]["Monosaccharides"] = "-";
    $array[upperLimits]["Sucrose"] = "-";
    $array[upperLimits]["Other Disaccharides"] = "-";
    $array[upperLimits]["Whole Grain"] = "-";
    $array[upperLimits]["Saturated"] = round(200 * $gender * $activityLevel / kcalPerGram("Fat"), 1);
    $array[upperLimits]["Fatty acids C4:0-10:0"] = "-";
    $array[upperLimits]["Lauric acid C12:0"] = "-";
    $array[upperLimits]["Myristic acid C14:0"] = "-";
    $array[upperLimits]["Palmitic acid C16:0"] = "-";
    $array[upperLimits]["Stearic acid C18:0"] = "-";
    $array[upperLimits]["Arachidic acid C20:0"] = "-";
    $array[upperLimits]["Monounsaturated"] = "-";
    $array[upperLimits]["Palmitoic acid C16:1"] = "-";
    $array[upperLimits]["Oleic acid C18:1"] = "-";
    $array[upperLimits]["Polyunsaturated"] = "-";
    $array[upperLimits]["Linoleic acid C18:2"] = "-";
    $array[upperLimits]["Arachidonic acid C20:4"] = "-";
    $array[upperLimits]["Linolenic acid C18:3"] = "-";
    $array[upperLimits]["EPA"] = "-";
    $array[upperLimits]["DPA"] = "-";
    $array[upperLimits]["DHA"] = "-";
    $array[upperLimits]["Cholesterol"] = "-";
    $array[upperLimits]["β-Carotene"] = "-";
    // upperLimits calculation for vitamins and minerals:
    // average upper limit * requirement by energy intake / average daily requirement
    $array[upperLimits]["Vitamin A"] = round(3000 * $array[dailyRequirements]["Vitamin A"] / 800);
    $array[upperLimits]["Vitamin B6"] = round(100 * $array[dailyRequirements]["Vitamin B6"] / 1.4);
    $array[upperLimits]["Vitamin B12"] = "-";
    $array[upperLimits]["Vitamin C"] = round(2000 * $array[dailyRequirements]["Vitamin C"] / 80);
    $array[upperLimits]["Vitamin D"] = round(100 * $array[dailyRequirements]["Vitamin D"] / 10);
    $array[upperLimits]["Vitamin E"] = round(300 * $array[dailyRequirements]["Vitamin E"] / 12);
    $array[upperLimits]["Vitamin K"] = "-";
    $array[upperLimits]["Retinol"] = "-";
    $array[upperLimits]["Thiamine"] = "-";
    $array[upperLimits]["Riboflavin"] = "-";
    $array[upperLimits]["Niacin"] = "-";
    $array[upperLimits]["Niacin Equivalents"] = round(35 * $array[dailyRequirements]["Niacin Equivalents"] / 19);
    $array[upperLimits]["Folate"] = round(1000 * $array[dailyRequirements]["Folate"] / 300);
    $array[upperLimits]["Phosphorus"] = round(3000 * $array[dailyRequirements]["Phosphorus"] / 700);
    $array[upperLimits]["Iodine"] = round(1100 * $array[dailyRequirements]["Iodine"] / 150);
    $array[upperLimits]["Iron"] = round(60 * $array[dailyRequirements]["Iron"] / 14);
    $array[upperLimits]["Calcium"] = round(2500 * $array[dailyRequirements]["Calcium"] / 800);
    $array[upperLimits]["Potassium"] = "-";
    $array[upperLimits]["Copper"] = round(5 * $array[dailyRequirements]["Copper"]);
    $array[upperLimits]["Magnesium"] = round(3500 * $array[dailyRequirements]["Magnesium"] / 375);
    $array[upperLimits]["Sodium"] = 2400;
    $array[upperLimits]["Salt"] = 6;
    $array[upperLimits]["Selenium"] = round(400 * $array[dailyRequirements]["Selenium"] / 60);
    $array[upperLimits]["Zinc"] = round(40 * $array[dailyRequirements]["Zinc"] / 13);
    $array[upperLimits]["Starch"] = "-";
    return $array;
}

function getUnits() {
    $array["Carbohydrates"] = g;
    $array["Fat"] = g;
    $array["Protein"] = g;
    $array["Fiber"] = g;
    $array["Water"] = g;
    $array["Monosaccharides"] = g;
    $array["Sucrose"] = g;
    $array["Other Disaccharides"] = g;
    $array["Whole Grain"] = g;
    $array["Saturated"] = g;
    $array["Fatty acids C4:0-10:0"] = g;
    $array["Lauric acid C12:0"] = g;
    $array["Myristic acid C14:0"] = g;
    $array["Palmitic acid C16:0"] = g;
    $array["Stearic acid C18:0"] = g;
    $array["Arachidic acid C20:0"] = g;
    $array["Monounsaturated"] = g;
    $array["Palmitoic acid C16:1"] = g;
    $array["Oleic acid C18:1"] = g;
    $array["Polyunsaturated"] = g;
    $array["Linoleic acid C18:2"] = g;
    $array["Linolenic acid C18:3"] = g;
    $array["Arachidonic acid C20:4"] = g;
    $array["EPA"] = g;
    $array["DPA"] = g;
    $array["DHA"] = g;
    $array["Cholesterol"] = mg;
    $array["Retinol"] = µg;
    $array["Vitamin A"] = RE;
    $array["β-Carotene"] = RE;
    $array["Vitamin D"] = µg;
    $array["Vitamin E"] = mg;
    $array["Vitamin K"] = µg;
    $array["Thiamine"] = mg;
    $array["Riboflavin"] = mg;
    $array["Vitamin C"] = mg;
    $array["Niacin"] = mg;
    $array["Niacin Equivalents"] = NE;
    $array["Vitamin B6"] = mg;
    $array["Vitamin B12"] = µg;
    $array["Folate"] = µg;
    $array["Phosphorus"] = mg;
    $array["Iodine"] = µg;
    $array["Iron"] = mg;
    $array["Calcium"] = mg;
    $array["Potassium"] = mg;
    $array["Copper"] = mg;
    $array["Magnesium"] = mg;
    $array["Sodium"] = mg;
    $array["Salt"] = g;
    $array["Selenium"] = µg;
    $array["Zinc"] = mg;
    $array["Starch"] = g;
    return $array;
}

function kcalPerGram($type) {
    $kcalPerGram[Carbohydrates] = 4.06;
    $kcalPerGram[Fat] = 8.84;
    $kcalPerGram[Protein] = 4;
    $kcalPerGram[Fiber] = 2;
    return $kcalPerGram[$type];
}

function percent($value) {
    return 100 * $value . '%';
}

function calorieToJoule($energy) {
    return round($energy * 4.184, 0);
}

function html($line) {
    echo "\n" . $line;
}

?>