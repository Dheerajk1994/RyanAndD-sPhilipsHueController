<!DOCTYPE HTML>
<html>
	<head>
      	<title>Lab 2</title>
      	<meta charset="utf-8">
      	<style type="text/css">
        	.hidden 	{display: none;}
          	.visible 	{display: block;}
      	</style>
      	<script type="text/javascript">
			var lightOn = true;
            var bri = 255;
            var authCode = "lH7nZmlzj1fMbsjzOAyrs6sO24zAsMY--IiF59vH";
            var lightID = 4;
            var urlStr = "http://130.166.45.108/api/" + authCode + "/lights/" + lightID + "/state";
            var sleepTime = 200;
          	var brightnessChange = 25;
            var hueVal = 5000;
        
        	function lightFade () {
              	var lightSelection =  document.getElementById("lightSelection");
              	lightID = lightSelection.options[lightSelection.selectedIndex].value;
              	hueVal = document.getElementById("hueField").value;
              	var funcSelection = document.getElementById("functionSelection");
              	var functionToCall = funcSelection.options[funcSelection.selectedIndex].value;
              	urlStr = "http://130.166.45.108/api/" + authCode + "/lights/" + lightID + "/state";
              	console.log(lightID);
            		console.log(urlStr);
              	console.log(hueVal);
            		sendAJAX(urlStr, "PUT", JSON.stringify( {"hue" : hueVal, "bri" : bri, "on" : lightOn})); 
              	if(functionToCall == '1'){
                  console.log("fade executed");
                  fade();
                }
              	else if(functionToCall == '2'){
                  console.log("flicker executed");
                  flicker();
                }
        	}//end light fade
			
          	function fade(){
              for(var totalFlicks = 0; totalFlicks < 4; totalFlicks++){
                  for(var fadeOut = 0; fadeOut < 12; fadeOut++){
                    if(bri <= 0) { lightOn = false; }
                    sendAJAX(urlStr, "PUT", JSON.stringify( {"hue" : hueVal, "bri" : bri, "on" : lightOn})); 
                    bri -= brightnessChange;
                    sleepMs(sleepTime);
                  }//end fadeout
                  for(var fadeIn = 0; fadeIn < 12; fadeIn++){
                    if(bri > 0) { lightOn = true; }
                    sendAJAX(urlStr, "PUT", JSON.stringify( {"hue" : hueVal, "bri" : bri, "on" : lightOn})); 
                    bri += brightnessChange;
                    sleepMs(sleepTime);
                  }//end fade in
                }//end total flicks
            }
          
          	function flicker(){
              var tempOn = true;
              for(var flickerCnt = 0; flickerCnt < 200; flickerCnt++){
                sendAJAX(urlStr, "PUT", JSON.stringify( {"hue" : hueVal, "bri" : bri, "on" : tempOn}));
                sleepMs(200);
                tempOn = !tempOn;
              }
            }
          
            function sendAJAX (url, method, str) {
            	var req = new XMLHttpRequest();
                req.open(method, url, true);
              	req.setRequestHeader("Content-Type", "application/json");
              	req.send(str);
            }//end sendajax

            function sleepMs (msec) {
             	var start = new Date().getTime();
              	while( (new Date().getTime()) < (start + msec));
            }//end sleepms

            function hide() {
              	var loginDiv = document.getElementById("formWindow");
              	loginDiv.setAttribute("class", "hidden");
            }//end hide

            function initHandler() {
              var FadeButton = document.getElementById("fadeButton");
              var LoginButton = document.getElementById("login");
                if(FadeButton){
                  FadeButton.addEventListener("click", lightFade); 
                }
            	if(LoginButton){
                  LoginButton.addEventListener("click", hide);
                }  	
            }//end inithandler

            window.addEventListener("load", initHandler);
		</script>
	</head>
  	<body>
  	<?php
    	$rootUserName = "root";
    	$databaseName = "ryan_db";
    	$databasePassword = "password123";
    	$tableName = "users";

		$usern = $pw = "";
    
    	$pageNum = "1";
				
    	if($_GET["page"]){
        	$pageNum = $_GET["page"];
        }
        
    	if($pageNum === "1"){
        ?>
        	<div id="formWindow" class ="visible">
                <form method = "post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                    <!-- INPUT FIELDS -->
                    Username:<br>
                    <input type="text" name="usernameField"><br>
                    Password:<br>
                    <input type="password" name="passwordField"><br>
                    <!-- BUTTONS -->
                                    <br>
                    <input type="hidden" value = "1" name = "page">
                    <input id = "login" type="submit" value="Login" name="login"> 
                    <input type="submit" value="Register" name="register">
                </form>
  			</div>	 
    		<?php
        }
    
    	if($_POST["login"]){
            $conn = new mysqli("localhost",$rootUserName, $databasePassword, $databaseName);
          	$usern = $_POST["usernameField"];
          	$pw = $_POST["passwordField"];
          	$sql = "SELECT * FROM $tableName WHERE username='$usern' AND password='$pw'";
          	$result = $conn->query($sql);
          	if($result->num_rows === 1){
				if($usern === "admin"){
                	DisplayAdminPage();
              	}
              	else{
                	DisplayLightPage();
              	}
            }
          	else {
            	echo "Invalid credentials!";
            }
        }
    	if($_POST["register"]){
            $conn = new mysqli("localhost",$rootUserName, $databasePassword, $databaseName);
  			$usern = $_POST["usernameField"];
          	$pw = $_POST["passwordField"];
            if($usern != "" && $pw != "") {
              	$sql = "SELECT * FROM $tableName WHERE username='$usern'";
                $result = $conn->query($sql);
                if($result->num_rows != 0) { 
                	echo "That username already exists!";              
                }
                else {
                	$sqlQ = "INSERT INTO $tableName (username, password) VALUES ('$usern', '$pw')";
          			if (!$conn->query($sqlQ)) {
               			echo "Error while registering!";
            		}  
                	else {
                	   echo "Successfully registered!";
                	}
                }
            }
		}
    		
    	function DisplayLightPage(){
        	$usern = $_POST["usernameField"];
          	?>
    			<div id="lightWindow">
            	<?php echo "<h1>Welcome ". $usern ."!</h1>" ?>
                	<form  method = "post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                        <input type="hidden" value = "2" name="page">
                      	Light:
                      	<select id="lightSelection">
  							<option value=1>1</option>
  							<option value=2>2</option>
  							<option value=3>3</option>
  							<option value=4>4</option>
                         	<option value=5>5</option>
                          	<option value=6>6</option>
						</select>
                      	Hue:
                      	<input type="text" id="hueField" value ="500000" maxLength=6>
                        Function:
                        <select id="functionSelection">
  							  <option value=1>Fade</option>
                              <option value=2>Flicker</option>
						</select>
                      	<br><br>
                        <input id ="fadeButton" type="button" value="Run" >
                        <input id ="logoutButton" type="submit" value="Logout">
                  	</form>
         	 	</div> 
    	<?php
        }
    		
    	function DisplayAdminPage() {
            //$conn = new mysqli("localhost",$rootUserName, $databasePassword, $databaseName);
          	$conn = new mysqli("localhost","root", "password123", "ryan_db");
            $sql = "SELECT username, password FROM users ORDER BY username";
            $result = $conn->query($sql);
          	echo("<table>");
          	echo("<tr><td><strong>Username</strong></td><td><strong>Password</strong></td></tr>");
            while($row = $result->fetch_assoc()){
              	echo "<tr>";
            	echo "<td>". $row["username"]."</td><td>".$row["password"]."</td>";
              	echo "</tr>";
            }
          	echo("</table>");
        ?>
      		<div>
      			<form method = "post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                 	<input type="hidden" value = "2" name="page">
                  	<input id ="logoutButton" type="submit" value="Logout">
      			</form>
      		</div>
      	<?php
          
        }  
   	 	?>
    
	</body>
</html>
