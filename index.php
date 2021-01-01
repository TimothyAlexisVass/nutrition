<?php session_start(); ?>
<?php include '/www/webvol5/p0/dpin4ia8g4wxl2c/freealization.com/public_html/header.php' ?>
<?php include 'functions.php' ?>

<main>

<?php
# Handle $_POST
if($_SERVER[REQUEST_METHOD] == "POST") {
    if(isset($_POST[name])) {
        $_SESSION[name] = $_POST[name];
    }
    else {
        if(isset($_POST[age])) {
            setBodyInfo($_SESSION[name], $_POST[gender], $_POST[age], $_POST[weight], $_POST[activityLevel]);
        }
        if(isset($_POST[add])) {
            addItemToFoodList($_SESSION[name], $_POST[foodItem], $_POST[amount]);
            $_SESSION[foodList] = getFoodList($_SESSION[name]);
        }
        elseif(isset($_POST[clearItem])) {
            clearItemFromFoodList($_SESSION[name], $_POST[foodItem]);
            $_SESSION[foodList] = getFoodList($_SESSION[name]);
        }
        elseif(isset($_POST[clear])) {
            clearInfo($_SESSION[name]);
            unset($_SESSION[foodList]);
        }
    }
}

$bodyInfo = getBodyInfo($_SESSION[name]);
html('<h1>Nutrient insights</h1>');

if(!isset($_SESSION[name])) {
    include 'getName.php';
}
else {
    include 'getInput.php';

    if(!isset($_SESSION[foodList])) {
        $_SESSION[foodList] = getFoodList($_SESSION[name]);
    }
    $foodList = $_SESSION[foodList];

    if($foodList) {
    # Set data
        $foodListKeysString = implode(', ', array_map(
            function ($value, $key) {
                return "'" . $key . "'";
            }, 
            $foodList, 
            array_keys($foodList)
        ));
        $nutritionData = getNutritionData($foodListKeysString);
        $totals = getNutritionTotals($foodList, $nutritionData);
        $energyDistribution = getEnergyDistribution($totals);

        $macroNutrients = getMacroNutrients();
        $carbohydrates = getCarbohydrates();
        $saturatedFattyAcids = getSaturatedFattyAcids();
        $monounsaturatedFattyAcids = getMonoUnsaturatedFattyAcids();
        $polyunsaturatedFattyAcids = getPolyUnsaturatedFattyAcids();
        $proteins = getProteins();
        $contents = getContents();
        $microNutrients = getMicroNutrients();
        $units = getUnits();
        $dailyRecommendations = getDailyRecommendations($bodyInfo);
        $dailyRequirements = $dailyRecommendations[dailyRequirements];
        $upperLimits = $dailyRecommendations[upperLimits];
        $vitamins = getVitamins();
        $minerals = getMinerals();

    # Display data
        html('<h1>Items</h1>');
        html("<div class='wrapper'>
    <table width='100%'><thead>");
        html("    <tr>
            <th class='left'>Total</th>
            <th class='right'>" . $totals[Amount] . " g</th>
            <th class='right'>" . $totals[Energy] . " kcal</th>
            <th class='right'>" . calorieToJoule($totals[Energy]) . " kJ</th>
        </tr>
    </thead><tbody>");
        foreach($foodList as $foodItem => $amount) {
            $energy = $nutritionData[$foodItem][Energy] / 100 * $amount;
            html("    <tr>
            <td class='left'><form class='left' action='index.php' method='post'><input type='hidden' name='foodItem' value='$foodItem' /><input type='submit' class='x-button' name='clearItem' value='X ' /></form>  $foodItem</td>
            <td class='right'>$amount g</td>
            <td class='right'>$energy kcal</td>
            <td class='right'>" . calorieToJoule($energy) . " kJ</td>
        </tr>");
        }
        html('</tbody></table>');
        html('<form class="center" action="index.php" method="post"><input type="submit" name="clear" value="CLEAR" /></form>');
        html('</div>');


    # Nutritional Balancing
        html("<h1>Nutritional Balancing</h1>");
        html("<div class='wrapper'>");
        html("   <h2>Energy Distribution%</h2>");
    # Energy Distribution
        $carbohydratesPercent = percent($energyDistribution[Carbohydrates]);
        $fatPercent = percent($energyDistribution[Fat]);
        $proteinPercent = percent($energyDistribution[Protein]);
        $fiberPercent = percent($energyDistribution[Fiber]);
        if ($carbohydratesPercent < 15 or $fatPercent < 5 or $proteinPercent < 9 or $fiberPercent < 8) { 
            html("<div class='distribution-labels'>");
                if($carbohydratesPercent > 0) {
                    html("    <div class='right distribution-label'><span style='font-size: 140%; color: var(--Carbohydrates)'>&#9679;</span> Carbohydrates: <span class='bold' style='color: var(--Carbohydrates)'>$carbohydratesPercent</span></div>");
                }
                if($fatPercent > 0) {
                    html("    <div class='center distribution-label'><span style='font-size: 140%; color: var(--Fat)'>&#9679;</span> Fat: <span class='bold' style='color: var(--Fat)'>$fatPercent</span></div>");
                }
                if($proteinPercent > 0) {
                    html("    <div class='center distribution-label'><span style='font-size: 140%; color: var(--Protein)'>&#9679;</span> Protein: <span class='bold' style='color: var(--Protein)'>$proteinPercent</span></div>");
                }
                if($fiberPercent > 0) {
                    html("    <div class='right distribution-label'><span style='font-size: 140%; color: var(--Fiber)'>&#9679;</span> Fiber: <span class='bold' style='color: var(--Fiber)'>$fiberPercent</span></div>");
                }
            html("</div>");
            $showNutrient = false;
        }
        else {
            $showNutrient = true;
        }
        html("<table style='width: 100%; font-size: 16px;'><tbody>
        <tr>");
        foreach($macroNutrients as $nutrient) {
            $nutrientPercent = percent($energyDistribution[$nutrient]);
            if($nutrientPercent > 0) {
                if($showNutrient) {
                    html("        <th style='padding: 10px 0; background-color: var(--$nutrient); width: $nutrientPercent'>$nutrient</th>");
                }
                else {
                    html("        <th style='padding: 20px 0; background-color: var(--$nutrient); width: $nutrientPercent'></th>");
                }
            }
        }
        html("    </tr>
        <tr>");
        foreach($macroNutrients as $nutrient) {
            $nutrientPercent = percent($energyDistribution[$nutrient]);
            if($nutrientPercent > 0) {
                if($showNutrient) {
                    html("        <td class='bold' style='padding: 10px 0; color: var(--$nutrient); width: $nutrientPercent'>$nutrientPercent</td>");
                }
                else {
                    html("        <td class='bold' style='width: $nutrientPercent'></td>");
                }
            }
        }
        html("    </tr>");
        html("</tbody></table>
    <br>
    <table style='width: 100%; font-size: 16px;'><tbody>
        <tr>
            <th class='right' style='padding: 10px 0; background-color: var(--Carbohydrates); width: 50%'>Recomm</th>
            <th class='left' style='padding: 10px 0; background-color: var(--Fat); width: 30.5%'>endation</th>
            <th style='padding: 10px 0; background-color: var(--Protein); width: 12.5%'></th>
            <th style='padding: 10px 0; background-color: var(--Fiber); width: 7%'></th>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </tbody></table>");
    # Omega 6 : Omega 3 ratio
        foreach($polyunsaturatedFattyAcids as $item) {
            if($item != "Polyunsaturated") {
                $totalPolyunsaturatedFattyAcids += $totals[$item];
            }
        }
        if($totalPolyunsaturatedFattyAcids > 0) {
            html("<h2>Omega 6:3 Ratio</h2>");
            $omega6percent = percent(round(($totals["Linoleic acid C18:2"] + $totals["Arachidonic acid C20:4"]) / $totalPolyunsaturatedFattyAcids,3));
            if($omega6percent > 0) {
                $omega3percent = percent(round((100 - $omega6percent)/100),3);
            }
            else {
                $omega3percent = percent(round(($totals["Linolenic acid C18:3"] + $totals["EPA"] + $totals["DPA"] + $totals["DHA"]) / $totalPolyunsaturatedFattyAcids,3));
            }

            html("<br>
    <table style='width: 100%; font-size: 16px;'><tbody>
        <tr>");
            if($omega6percent > 0) {
                html("        <th style='padding: 10px 0; background-color: var(--Protein); width: $omega6percent'>Omega 6</th>");
            }
            if($omega3percent > 0) {
                html("        <th style='padding: 10px 0; background-color: var(--Fiber); width: $omega3percent'>Omega 3</th>");
            }
            html("    </tr>
        <tr>");
            if($omega6percent > 0) {
                html("        <td>$omega6percent</td>");
            }
            if($omega3percent > 0) {
                html("        <td>$omega3percent</td>");
            }
            html("    </tr>");
            html("</tbody></table>
    <br>
    <table style='width: 100%; font-size: 16px;'><tbody>
        <tr>
            <th class='right' style='padding: 10px 8.5%; background-color: var(--Protein); width: 66.7%'>Recommended ratio</th>
            <th class='left' style='padding: 10px 0; background-color: var(--Fiber); width: 33.3%'></th>
        </tr>
        <tr>
            <td></td>
            <td></td>
        </tr>
    </tbody></table>");
    }
        html("</div>
    <br><br>");
    # Micronutrient Estimates
        html("<table width='100%' class='estimates-table'><tbody>\n   <tr>");    
        html("        <th colspan='7' class='xx-large bold'>Micronutrient Estimates</th>");
        html("    </tr>");
        html("    <tr class='large bold'><td style='width:8px; padding: 0;'></td><td colspan='3' class='left extra-thick-bottom-border'>Total amount eaten</td><td colspan='2' class='right extra-thick-bottom-border'>" . $totals[Amount] . " g</td><td style='width:8px; padding: 0;'></td></tr>");
        html("    <tr class='small bold'><td style='width:8px; padding: 0;'></td><td class='left'>Totals for all food items</td><td class='right'>Requirement</td><td colspan='3' class='right'>Intake</td><td style='width:8px; padding: 0;'></td></tr>");
        html("    <tr class='xx-large bold'><td style='width:8px; padding: 0;'></td><td class='left thick-bottom-border' style='padding: 0;'>Energy</td><td class='right thick-bottom-border' style='padding: 0;'>" . $dailyRequirements[Energy] . " kcal</td><td colspan='3' class='right thick-bottom-border' style='padding: 0;'>" . $totals[Energy] . " kcal</td><td style='width:8px; padding: 0;'></td></tr>");
        html("    <tr class='medium bold'>
            <td style='width:8px; padding: 0;'></td>
            <td class='left thin-bottom-border'>Nutrient</td>
            <td class='right thin-bottom-border thin-right-border'>Requirement</td>
            <td colspan='2' class='thin-bottom-border thin-right-border shaded'>Intake</td>
            <td class='right thin-bottom-border'>Limit</td>
            <td style='width:8px; padding: 0;'></td>
        </tr>");
        foreach($microNutrients as $nutrient) {
            $totals[$nutrient] >= 20 ?
                $nutrientIntake = round($totals[$nutrient]) :
                $nutrientIntake = round($totals[$nutrient],1);
            $dailyRequirements[$nutrient] == "-" ?
                $intakePercent = "-" :
                $intakePercent = round($nutrientIntake/$dailyRequirements[$nutrient]*100) . " %";
            $nutrientIntake > 9999 ? $smallFont = " font-size: 16px;" : $smallFont = "";
            $colorIntake = "color: var(--background);";
            $recommendationButton = "";
            if($nutrientIntake < $dailyRequirements[$nutrient] and $dailyRequirements[$nutrient] != "-") {
                $colorPercent = " color: #fcc;";
                $amount = $dailyRequirements[$nutrient]-$nutrientIntake;
                $nutrientId = str_replace(" ", "_", $nutrient);
                $recommendationButton = "<a target='_blank' rel='noopener noreferrer' href='#' onclick='showhide(\"$nutrientId\");return false;' style='display:inline;color: #afa;'> &#11014;</a> <button id='close$nutrientId' class='recommendation-close' onclick='showhide(\"$nutrientId\");return false;'>X</button><iframe id='$nutrientId' class='recommendation' src='http://www.freealization.com/nutrition/top30.php?amount=$amount&field=$nutrient'></iframe>";
            }
            elseif ($nutrient > $upperLimits[$nutrient] and $upperLimits[$nutrient] != "-") {
                $colorPercent = " color: red;";
            }
            else {
                $colorIntake = $colorPercent = " color: #afa;";
            }
            html("    <tr>
            <td style='width:8px; padding: 0;'></td>
            <td class='left thin-bottom-border'>$nutrient$recommendationButton</td>
            <td class='right thin-bottom-border thin-right-border' style='width:248px;'>" . $dailyRequirements[$nutrient] . " " . $units[$nutrient] . "</td>
            <td class='right thin-bottom-border thin-right-border bold shaded' style='width:74px;$smallFont$colorIntake'>" . $nutrientIntake . " " . $units[$nutrient] . "</td>
            <td class='right thin-bottom-border thin-right-border bold shaded' style='width:74px;$colorPercent'>$intakePercent</td>
            <td class='right thin-bottom-border' style='width:90px'>" . $upperLimits[$nutrient] . " " . $units[$nutrient] . "</td>
            <td style='width:8px; padding: 0;'></td>
        </tr>");
        }
        html("</tbody></table>");
        html("<script>
    function showhide(id) {
        var e = document.getElementById(id);
        e.style.display = (e.style.display == 'block') ? 'none' : 'block';
        e = document.getElementById('close' + id);
        e.style.display = (e.style.display == 'block') ? 'none' : 'block';
    }
    </script>");
        html("<br>");
    # Macronutrient Estimates
        html("<table width='100%' class='estimates-table'><tbody>\n    <tr>");    
        html("        <th colspan='7' class='xx-large bold'>Macronutrient Estimates</th>");
        html("    </tr>");
        html("    <tr class='medium bold'>
            <td style='width:8px; padding: 0;'></td>
            <td class='left thin-bottom-border'>Nutrient</td>
            <td class='right thin-bottom-border thin-right-border'>Requirement</td>
            <td colspan='2' class='thin-bottom-border thin-right-border shaded'>Intake</td>
            <td class='right thin-bottom-border'>Limit</td>
            <td style='width:8px; padding: 0;'></td>
        </tr>");
        foreach(array_merge($contents, $carbohydrates, $saturatedFattyAcids, $monounsaturatedFattyAcids, $polyunsaturatedFattyAcids, $proteins) as $nutrient) {
            if(in_array($nutrient, getSums())) {
                $bold = "bold";
                $space = "";
            }
            else {
                $bold = "";
                $space = "&nbsp;&nbsp;";
            }
            $totals[$nutrient] >= 20 ?
                $nutrientIntake = round($totals[$nutrient]) :
                $nutrientIntake = round($totals[$nutrient],1);
            $dailyRequirements[$nutrient] == "-" ?
                $intakePercent = "-" :
                $intakePercent = round($nutrientIntake/$dailyRequirements[$nutrient]*100) . " %";
            $nutrientIntake > 9999 ? $smallFont = " font-size: 16px;" : $smallFont = "";
            $colorIntake = "color: var(--background);";
            if($nutrientIntake < $dailyRequirements[$nutrient] and $dailyRequirements[$nutrient] != "-") {
                $colorPercent = " color: #fcc;";
            }
            elseif ($nutrient > $upperLimits[$nutrient] and $upperLimits[$nutrient] != "-") {
                $colorPercent = " color: red;";
            }
            else {
                $colorIntake = $colorPercent = " color: #afa;";
            }
            html("    <tr>
            <td style='width:8px; padding: 0;'></td>
            <td class='left $bold thin-bottom-border'>$space$nutrient</td>
            <td class='right thin-bottom-border thin-right-border' style='width:248px;'>" . $dailyRequirements[$nutrient] . " " . $units[$nutrient] . "</td>
            <td class='right thin-bottom-border thin-right-border bold shaded' style='width:74px;$smallFont$colorIntake'>" . $nutrientIntake . " " . $units[$nutrient] . "</td>
            <td class='right thin-bottom-border thin-right-border bold shaded' style='width:74px;$colorPercent'>$intakePercent</td>
            <td class='right thin-bottom-border' style='width:90px'>" . $upperLimits[$nutrient] . " " . $units[$nutrient] . "</td>
            <td style='width:8px; padding: 0;'></td>
        </tr>");
        }
        html("</tbody></table>");
    } # /if($foodList) 
}
?>

</main>

<?php include '/www/webvol5/p0/dpin4ia8g4wxl2c/freealization.com/public_html/footer.php' ?>