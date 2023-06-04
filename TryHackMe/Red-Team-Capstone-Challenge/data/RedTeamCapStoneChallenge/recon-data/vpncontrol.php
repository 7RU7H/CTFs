<?php
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: index.php");
    exit;
  }
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>VPN Request Server v14.2</title>
  </head>
  <style media="screen">
    .padding-container {
      padding-right: 75%;
    }
    .left-container {
      border-style: solid;
    }
    .version {
      text-align: center;
    }
    .title {
      padding-left: 3%;
    }
    .welcome-container {
      border-style: solid none solid none;
      background-color: #F0F0F0;
    }
    .date {
      padding-left: 3%;
    }
    .welcome {
      padding-left: 3%;
    }
    .clock {
      text-align: center;
    }
    .menu-container {
      padding-left: 3%;
      color: blue;
      text-decoration: underline;
    }
    .logout {
      text-align: center;
      color: blue;
      text-decoration: underline;
    }
    .title-container {
      border-style: none none solid none;
    }
    .property {
      text-align: center;
      color: #525252;
    }
    .warning-container {
      padding: 1%;
      border-style: none none solid none;
      background-color: #fffdc4;
    }
  </style>
  <body>
    <div class='padding-container'>
      <div class='left-container'>
        <div class='title-container'>
        <center>
        <h3 class='title'>VPN Request Server v14.2</h3>
        <h5>
          <?php
          $date = date('m/d/Y h:i:s a', time());
          echo $date;
          ?>
          <br>
          <?php echo "Welcome: ",$_SESSION["username"];?></h5>
            </center>
          </h5>
        </div>
        <div class='clock'>
          <img src='thereserve.png' class='clock'></img>
        </div>
        <div class='warning-container'>
          <p class='warning'>This server is to be accessed only by TheReserve employees to request internal access.</p>
        </div>


        <div class='menu-container'>
          <center>
                <form class="form-inline" action="/requestvpn.php">
            <label for="user">Account:</label>
            <input type="user" id="filename" value="<?php echo $_SESSION["username"];?>" name="filename">
            <div class='submit-container'>
              <label>
              <button type="submit">Submit</button>
              </label>
            </div>
          </form>
          <h5>Help & Support</h5>
        </div>
        <h5 class='version'>TheReserve</h5>
        <h4 class='logout'><a href="logout.php">Log Out</a></h4>
      </center>
      </div>
    </div>
  </body>
</html>
