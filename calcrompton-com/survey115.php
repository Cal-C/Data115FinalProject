<!DOCTYPE html>
<html>

<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js">
        //enabling jQuerry using google's copy
    </script>

    <link rel="stylesheet" type="text/css" href="survey-styles.css" />

</head>

<body>

    <?php
    $name = $email = $letterboxd = $compx = $compy = "";
    $nameERR = $emailERR = $letterERR = $compxERR = $compyERR = $thankyou = "";
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = test_input($_POST["name"]);
        // check if name only contains letters and whitespace
        if (!preg_match("/^[a-zA-Z-' ]*$/", $name)) {
            $nameERR = "Only letters and white space allowed";
        }



        if (!empty($_POST["email"])) { //only check if email is valid if there was an email given. Email is not required so no error if blank
            $email = test_input($_POST["email"]);
            // check if e-mail address is well-formed
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $emailERR = "Invalid email format";
            }
        }


        if (empty($_POST["letterboxd-url"])) {
            $letterERR = "Letterboxd Url is required";
        } else {
            $letterboxd = test_input($_POST["letterboxd-url"]);

            // check if URL address syntax is valid
            if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $letterboxd)) {
                $letterERR = "Invalid URL";
            } else {
                $needle = test_input("https://letterboxd.com/"); //checking that the url given is a letterboxd account, this check could be probably stricter
                if (!str_starts_with($letterboxd, $needle)) {
                    $letterERR = "This is not a letterboxd user URL. A correct url should look like https://letterboxd.com/USERNAME/films/reviews/,  https://letterboxd.com/USERNAME/films/ or https://letterboxd.com/USERNAME/";
                }
            }
        }

        if (empty($_POST["political-compass-x-entered"])) {
            $compxERR = "Economic Left/Right Numeral Required";
        } else {
            $compx = test_input($_POST["political-compass-x-entered"]);
            if (!($compx >= -10 && $compx <= 10)) {
                $compxERR = "Please enter a number from -10 to 10";
            }
        }

        if (empty($_POST["political-compass-y-entered"])) {
            $compyERR = "Social Libertarian/Authoritarian Numeral Required";
        } else {
            $compy = test_input($_POST["political-compass-y-entered"]);
            if (!($compy >= -10 && $compy <= 10)) {
                $compyERR = "Please enter a number from -10 to 10";
            }
        }

        if (($nameERR == "") and ($emailERR == "") and ($letterERR == "") and ($compxERR == "")  and ($compyERR == "")) {
            $thankyou = "thank you for your submission";

            //getting a little more data
            $time = time();
            $ip = getenv("REMOTE_ADDR");
            $iptwo = $_SERVER['REMOTE_ADDR'];

            //writing to CSV
            $csvlineARR = array($name,  $email, $letterboxd, $compx, $compy, $time, $ip, $iptwo); //adding our new data to the csv
            $file = fopen("survey-responses.csv","a");
            fputcsv($file, $csvlineARR);
            fclose($file);

        }
    }

    function test_input($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    ?>


    <h1>Letterboxd and Political Compass Survey</h1>
    <p> This survey is made to.... If you include your email we will send you the results of the study </p>
    <br>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name">
        <span class="error"> <?php echo $nameERR; ?></span>
        <br>
        <br>
        <label for="email">Email:</label>
        <input type="text" id="email" name="email">
        <span class="error"> <?php echo $emailERR; ?></span>
        <br>
        <br>
        <label for="letterboxd-url">Letterboxd URL:</label>
        <input type="text" id="letterboxd-url" name="letterboxd-url">
        <span class="error">* <?php echo $letterERR; ?></span>
        <br>
        <span> </span>
        <br>
        <br>
        <label for="political-compass-x-entered">Economic Left/Right:</label>
        <input type="text" id="political-compass-x-entered" name="political-compass-x-entered">
        <span class="error">* <?php echo $compxERR; ?></span>
        <br>
        <br>
        <label for="political-compass-y-entered">Social Libertarian/Authoritarian:</label>
        <input type="text" id="political-compass-y-entered" name="political-compass-y-entered">
        <span class="error">* <?php echo $compyERR; ?></span>
        <br>
        <br>
        <br>
        <input type="submit" value="Submit">
        <br>
        <span class="thanks"> <?php echo $thankyou; ?></span>
    </form>

    <br>
    <!-- <img src="demonsoulsarbys.jpg" alt="Old Man Gamer"> -->
    <br>

    <h2>Political Compass Estimator</h2>
    <p>Please use this tool to estimate your Poltical Compass if you took the test in the past. Make sure to copy and paste the values it gives you into the survey and submit the survey.</p>
    <br>
    <span> Economic Left/Right: </span>
    <span id="polcompx"> 0.0 </span>
    <br>
    <span> Social Libertarian/Authoritarian: </span>
    <span id="polcompy"> 0.0 </span>
    <br>
    <img id="polcomp" src="pol-compass.jpg" alt="Political Compass. Click me to estimate what your political compass was" height="500" width="500">

    <!-- attempt to make a red dot over the political compass, renabling even sections of this can break the site right now
<div class="image-container"> 
    <img id="polcomp" src="pol-compass.jpg" alt="Political Compass. Click me to estimate what your political compass was" height = "500" width = "500" class="bottom-image">
    <img id="dot" src="dot.png" height="10" width="10" class="top-image" x="250" y="250">
</div>
 -->
    <script>
        document.getElementById("polcompy").innerHTML = "0.0";
        $("#polcompx").text("0.0"); //this line is the jQuerry version of the previous line, https://www.w3schools.com/jquery/jquery_syntax.asp

        $("#polcomp").click(function(evt) {

            var jThis = $(this);
            var offsetFromParent = jThis.position();
            var topThickness = (jThis.outerHeight(true) - jThis.height()) / 2;
            var leftThickness = (jThis.outerWidth(true) - jThis.width()) / 2;

            //--- (x,y) coordinates of the mouse click relative to the image.
            var clickedx = evt.pageX
            var clickedy = evt.pageY
            var x = clickedx - offsetFromParent.left - leftThickness;
            var y = clickedy - offsetFromParent.top - topThickness;
            w = $("#polcomp").width();
            h = $("#polcomp").height();
            //drawing a red circle so the user knows where they clicked
            /* TODO Does not work right now 
            var canvas = document.getElementById("imgCanvas");
            var context = canvas.getContext("2d");
            var rect = canvas.getBoundingClientRect();
            var posx = clickedx - rect.left;
            var posy = clickedy - rect.top;

            context.fillStyle = "#FF0000";
            context.beginPath();
            context.arc(posx, posy, 50, 0, 2 * Math.PI);
            context.fill();
            */
            // converting the click into a political compass score
            var borderMultiplier = 21.37 //since the image has a small white boarder, so clicking the edge of the compass square should give abs(10)
            var polcompx = (x / w - .5) * borderMultiplier;
            var polcompy = -(y / h - .5) * borderMultiplier;
            polcompx = Math.round(polcompx * 100) / 100; //rounding both to two places beacuse that is where the orignal PC test ends
            polcompy = Math.round(polcompy * 100) / 100;
            $("#polcompx").text(polcompx);
            $("#polcompy").text(polcompy);
        });
    </script>

    <noscript> you do not have JavaScript installed and cannot use the political compass estimator </noscript>


</body>

</html>