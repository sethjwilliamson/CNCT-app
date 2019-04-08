<?php
    include("Login/include/session.php");
    if(!$session->logged_in){
        header('Location: ' . 'Login/main.php');
        exit();
    }
?>

<html>

<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body {
  margin: 0;
  font-size: 28px;
  font-family: courier;
}

.header {
  background-color: #14DEED;
  padding: 0px;
  text-align: center;
  height:200px;
  font-size: 100px;
}

#navbar {
  overflow: hidden;
  background-color: #329EA1;
  z-index:10000;
}

#navbar a {
  float: left;
  display: block;
  color: #ffffff;
  text-align: center;
  padding: 14px 16px;
  text-decoration: none;
  font-size: 17px;

  
}

#navbar a:hover {
  background-color: #497F84;
  color: #ffffff;
}

#navbar a.active {
  background-color: #329EA1;
  color: #ffffff;
}

#navbar a.active:hover {
  background-color: #497F84;
  color: #ffffff;
}

.content {
  padding: 16px;
}

.sticky {
  position: fixed;
  top: 0;
  width: 100%;
}

.sticky + .content {
  padding-top: 60px;
}
</style>
</head>
<body>
    
<header>
    <div style="height:1px">
        <br style="color:#497F84">
    </div>
</header>

<div id="navbar">
  <a class="active" href="javascript:void(0)">Home</a>
  <a href="/site2/Scripts/Contact.cshtml">Contact</a>
  <a href="Login/process.php">Logout</a>
  <a style="position:absolute; width: 20%; margin-left: 40%; margin-right: 40%;; font-weight:bold; " href="javascript:void(0)">CNCT</a>
</div>



<script>
window.onscroll = function() {myFunction()};

var navbar = document.getElementById("navbar");
var sticky = navbar.offsetTop;

function myFunction() {
  if (window.pageYOffset >= sticky) {
    navbar.classList.add("sticky")
  } else {
    navbar.classList.remove("sticky");
  }
}
</script>

</body>
</html>

    <meta charset="utf-8" />
    <title>CNCT</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
</head>

<body style="background-color:#e1e1e3">
    <br>
        <div class="row">
            <div class="col-sm-3">
                <div class="panel panel-default" style="background-color:#f9f9f9; padding:15px" id="userInfo">
                    <strong>User Info</strong>
                    <hr>
                    <?php
                        echo($session->username);
                    ?>
                    <br>
                    <button id="authenticateButton" type="button" class="btn btn-primary" onclick="authenticateClicked()">Authenticate</button>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="panel panel-default" style="background-color:#f9f9f9">
                    <div class="gridContainer" id="courseContainer" style="background-color:#f9f9f9">
                        <div class="div results" style="padding-top:20px">
                            
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="panel panel-default" style="background-color:#f9f9f9; padding:15px">
                    <h4>
                        Filters
                    </h4>
                    <form>
                        <input type="checkbox" id="youtubeCheckbox"> Youtube Posts <br>
                        <input type="checkbox" id="instagramCheckbox"> Instagram Posts <br>
                        <input type="text" placeholder="1111/1/1" class="form-control" id="startDate">
                        <input type="text" placeholder="2019/3/29" class="form-control" id="endDate">
                        <button type="button" style="width:100%" onclick="onSubmit()">Refresh</button>
                    </form>
                </div>
            </div>
        </div>
</body>
</html>

<script type="text/javascript">
    var start = 0;
    var limit = 20;
    var query = '';
    var reachedMax = false;
    var searched = false;
    var username = "<?php echo $session->username ?>";

    $(document).ready(function () {
        onSubmit();
    });

    $(window).scroll(function () {
        if ($(window).scrollTop() + $(window).innerHeight() > $(document).height() - 1) {
            getData();
        }
    });
    
    function authenticateClicked() {
        $("#userInfo").append(`
        <div class="row">
            <a onclick="authenticateInstaClicked()" href="https://www.instagram.com/oauth/authorize/?client_id=acbd4e04e0fe487bbb0e9d7f54dbd124&redirect_uri=http://cnctsocials.com&response_type=token"
            target="_blank"><img src="/site2/Images/instagramlogo.jpg" width="50" height="50" /></a>
            <a onclick="authenticateYoutubeClicked()" href="https://accounts.google.com/o/oauth2/auth?response_type=code&access_type=offline&client_id=750014165959-mp4i064fhp1sbag5r1i8l4n39oejk59m.apps.googleusercontent.com&redirect_uri=https%3A%2F%2Fwww.cnct-socials.com%2Fsite2%2F&state&scope=https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fyoutube.readonly&approval_prompt=auto" target="_blank"><img src="/site2/Images/youtubelogo.jpg" width="50" height="50" /></a>
        </div>
        `);
    }
    
    function authenticateInstaClicked() {
        $("#userInfo").append(`
        <form onsubmit="return false;">
            Copy and Paste URL here:<br>
            <input type="text" id="userInput"/><br>
            <input type="submit" onclick="onInstaAuthenticationClick()"/>
        </form>
        `)
    }
    
    function authenticateYoutubeClicked() {
        $("#userInfo").append(`
        <form onsubmit="return false;">
            Copy and Paste URL here:<br>
            <input type="text" id="userInput"/><br>
            <input type="submit" onclick="onYoutubeAuthenticationClick()"/>
        </form>
        `)
    }

    function onSubmit() {
        searched = true;
        start = 0;
        reachedMax = false;
        query = "";
        $(".results").html("");
        getData();
    }

    function onInstaAuthenticationClick() {
        $.ajax({
            url: '/site2/Scripts/SQL/insertInstaAccessToken.php',
            type: 'POST',
            dataType: 'text',
            data: {
                url: document.getElementById("userInput").value,
                userid: username
            },
            success: function (response) {
                $.ajax({
                url: '/site2/Scripts/SQL/insertPosts.php',
                type: 'POST',
                dataType: 'text',
                data: {
                    userid: username
                },
                success: function (response) {
                    alert("worked ?")
                }
            });
            }
        });
    }
    
    function onYoutubeAuthenticationClick() {
        $.ajax({
            url: '/site2/Scripts/SQL/insertYoutubeAccessToken.php',
            type: 'POST',
            dataType: 'text',
            data: {
                url: document.getElementById("userInput").value,
                userid: username
            },
            success: function (response) {
                $.ajax({
                url: '/site2/Scripts/SQL/insertYoutubePosts.php',
                type: 'POST',
                dataType: 'text',
                data: {
                    userid: username
                },
                success: function (response) {
                    alert("worked ?")
                }
            });
            }
        });
    }

    function getData() {
        if (reachedMax) {
            return;
        }

        $.ajax({
            url: '/site2/Scripts/SQL/getData.php',
            type: 'POST',
            dataType: 'text',
            data: {
                start: start,
                limit: limit,
                query : {
                    'isInstagram' : document.getElementById("instagramCheckbox").checked,
                    'isYoutube' : document.getElementById("youtubeCheckbox").checked,
                    'timeStart' : document.getElementById("startDate").value,
                    'timeEnd' : document.getElementById("endDate").value
                }
            },
            success: function (response) {
                if (response == "reachedMax") {
                    console.log("reached Max");
                    reachedMax = true;
                }
                else {
                    start += limit;
                    $(".results").append(response);
                }
            }
        });
    }
</script>

