<!DOCTYPE html>
<html>
<head>
    <?php
	require_once("./php/load_config.php");
    ?>
    <title>NaviCorpus</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-type">
    <link href="./img/ico.png" rel="icon" type="image/x-icon">
    <link href="css/jquery-ui-1.10.3.custom.min.css" rel="stylesheet" type=
    "text/css">
    <link href="css/gexfjs.css" rel="stylesheet" type="text/css">
    <link href="css/gexfjs-override.css" rel="stylesheet" type="text/css">
    <link href="css/navicorpus.css" rel="stylesheet" type="text/css">
    <?php
	if(isset($GLOBALS['additionalHeader'])){
		include($GLOBALS['additionalHeader']);
	}
    ?>
    <?php
	//cookie that detects if the user comes for the first time (in order to load the help pannel)
        if (!isset($_COOKIE['firsttime']))
        {
            setcookie("firsttime", "no", time()+60*60*24*30  ); //30days
            echo "<script>var first_time = 1</script>";
        }
        else
        {
            echo "<script>var first_time = 0</script>";
        };
    ?>
    <script>
             var corpus = <?php echo "'".$_GET['corpus']."'"; ?> ;
             var corporaPath = <?php echo "'".$GLOBALS['corporaPath']."'"; ?> ;
    </script>
    <script src=
        "https://ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js" type=
        "text/javascript">
    </script>
    <script type="text/javascript">
        // Fallback in case JQuery CDN isn't available
            if (typeof jQuery == 'undefined') {
                document.write(unescape("%3Cscript type='text/javascript' src='js/jquery-2.0.2.min.js'%3E%3C/script%3E"));
            }
    </script>
    <script src="js/jquery.mousewheel.min.js" type="text/javascript">
    </script>
    <script src="js/jquery-ui-1.10.3.custom.min.js" type="text/javascript">
    </script>
    <script src="js/navicorpus.js" type="text/javascript">
    </script>
    <script src="js/gexfjs.js" type="text/javascript">
    </script>
    <script src="js/default_config.js" type="text/javascript">
    </script>
</head>
<body>
    <div class="gradient" id="zonecentre">
        <canvas height="0" id="carte" width="0"></canvas>
	<?php
		if(isset($GLOBALS['additionalElementsOnMap'])){
			include($GLOBALS['additionalElementsOnMap']);
		}
	?>
        <div id="occurencies_bckgnd">
            <div id="occurencies_pannel"></div>
        </div>
    </div>
    <div class="gradient" id="overviewzone">
        <canvas height="0" id="overview" width="0"></canvas>
    </div>
    <div id="howtouse_bckgnd">
        <div id="howtouse_pannel"></div>
    </div>
    <div id="leftcolumn">
        <div id="unfold">
            <a class="rightarrow" href="#" id="aUnfold"></a>
        </div>
        <div id="leftcontent"></div>
    </div>
    <div id="titlebar">
        <div id="maintitle">
            <a href="./" id="logo" title="NaviCorpus"></a> 
        </div>
        <form id="recherche" name="recherche">
            <input autocomplete="off" class="grey" id="searchinput"> <input id=
            "searchsubmit" type="submit">
        </form>
        <ul id="ctlzoom">
            <li>
                <a href="#" id="zoomMinusButton" title="S'approcher"></a>
            </li>
            <li id="zoomSliderzone">
                <div id="zoomSlider"></div>
            </li>
            <li>
                <a href="#" id="zoomPlusButton" title="S'éloigner"></a>
            </li>
            <li>
                <a href="#" id="lensButton"></a>
            </li>
            <li>
                <a href="#" id="edgesButton"></a>
            </li>
        </ul>
    </div>
    <div id="textNavigation">
        <div id="textContent"></div>
        <div id="unfold2">
            <canvas class="joystick" id="aUnfold2"></canvas>
        </div>
    </div>
    <ul id="autocomplete"></ul>
    <?php
	if(isset($GLOBALS['additionalElementsOnBottom'])){
		include($GLOBALS['additionalElementsOnBottom']);
	}
    ?>
    <script>
        var size_is_ok = 1
        function control_window_size(){
            if(size_is_ok){
                if((window.innerWidth < 750) || (window.innerHeight < 450)){
                    alert("Sorry, this website is (...really) not optimized for small windows or mobile phones. \nPlease visit this website from a computer,\na mouse is also highly recommended to navigate through the maps...")  
                    size_is_ok = 0
                }else{
                	size_is_ok = 1
            	}
            }
        }
        setInterval(control_window_size,4*3600)
    </script>
</body>
</html>
