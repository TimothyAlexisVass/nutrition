<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>We reach liberation when we realize liberty.</title>
    <link rel="stylesheet" media="screen" href="http://www.freealization.com/style.css">
    <?php
    if(dirname($_SERVER['PHP_SELF']) != "/"){
        echo '<link rel="stylesheet" media="screen" href="http://www.freealization.com' . dirname($_SERVER['PHP_SELF']) . dirname($_SERVER['PHP_SELF']) . '.css">';
    }
    ?>
    <meta name="viewport" content="width = device-width, initial-scale = 1.0">
    <meta name="description" content="Love - Truth - Justice - Wiseness - Health - Life - Awareness - Peace - | For everyone and everywhere - Forever">
</head>
<?php include 'functions.php' ?>
<body style="background:white;font-family:'Ubuntu',sans-serif">
<center>
<?php
$field = $_GET['field'];
$amount = $_GET['amount'];
html("<h3>$field</h3>");
$nutrients = [
"Fatty acids C4:0-10:0" => "Fettsyra 4:0-10:0 (g)",
"Lauric acid C12:0" => "Laurinsyra C12:0 (g)",
"Myristic acid C14:0" => "Myristinsyra C14:0 (g)",
"Palmitic acid C16:0" => "Palmitinsyra C16:0 (g)",
"Stearic acid C18:0" => "Stearinsyra C18:0 (g)",
"Arachidic acid C20:0" => "Arakidinsyra C20:0 (g)",
"Monounsaturated" => "Summa enkelomättade fettsyror (g)",
"Palmitoic acid C16:1" => "Palmitoljesyra C16:1 (g)",
"Oleic acid C18:1" => "Oljesyra C18:1 (g)",
"Polyunsaturated" => "Summa fleromättade fettsyror (g)",
"Linoleic acid C18:2" => "Linolsyra C18:2 (g)",
"Arachidonic acid C20:4" => "Arakidonsyra C20:4 (g)",
"Linolenic acid C18:3" => "Linolensyra C18:3 (g)",
"EPA" => "EPA (C20:5) (g)",
"DPA" => "DPA (C22:5) (g)",
"DHA" => "DHA (C22:6) (g)",
"Vitamin A" => "Vitamin A (RE)",
"Vitamin D" => "Vitamin D (µg)",
"Vitamin E" => "Vitamin E (mg)",
"Vitamin K" => "Vitamin K (µg)",
"Thiamine" => "Tiamin (mg)",
"Riboflavin" => "Riboflavin (mg)",
"Vitamin C" => "Vitamin C (mg)",
"Niacin" => "Niacin (mg)",
"Niacin Equivalents" => "Niacinekvivalenter (NE)",
"Vitamin B6" => "Vitamin B6 (mg)",
"Vitamin B12" => "Vitamin B12 (µg)",
"Folate" => "Folat (µg)",
"Phosphorus" => "Fosfor (mg)",
"Iodine" => "Jod (µg)",
"Iron" => "Järn (mg)",
"Calcium" => "Kalcium (mg)",
"Potassium" => "Kalium (mg)",
"Copper" => "Koppar (mg)",
"Magnesium" => "Magnesium (mg)",
"Sodium" => "Natrium (mg)",
"Salt" => "Salt (g)",
"Selenium" => "Selen (µg)",
"Zinc" => "Zink (mg)"
];

$field = $nutrients[$field];

if( preg_match( '!\(([^\)]+)\)!', $field, $match ) ) {
    $unit = $match[1];
}

html("<p>Remaining daily need: $amount $unit</p>");

html("<table><tbody>\n");
html("    <tr>\n");
html("        <th>Food item</th>\n");
html("        <th>$field / 100 g</th>\n");
html("        <th>Need</th>\n");
html("   </tr>\n");

$nutritionData = getTopFoodsForNutrient($field);

foreach($nutritionData as $item) {
    html("   <tr>\n");
    foreach($item as $nutrient) {
        html("<td>$nutrient</td>");
    }
    $weight = round($amount/$nutrient*100,1);
    html("<td>$weight g</td>");
    html("   </tr>\n");
}
?>
</tbody></table>
</center>
</body>
</html>