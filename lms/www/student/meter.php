<?php 
	include "auth_session.php";
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>
</head>
<body>
<style>
    @import url("https://fonts.googleapis.com/css?family=Lato:300");


/*** <--- CIRCLE STYLES ---> ***/


.circle {
  position: relative;
  width: 150px;
  height: 150px;
  margin: 0.5rem;
  border-radius: 50%;
  background: #FFCDB2;
  overflow: hidden;
}
.circle.per-25 {
  background-image: conic-gradient(#B5838D 10%, #FFCDB2 0);
}

.circle .inner {
  display: flex;
  justify-content: center;
  align-items: center;
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 115px;
  height: 115px;
  background: #000;
  border-radius: 50%;
  font-size: 1.85em;
  font-weight: 300;
  color: rgba(255, 255, 255, 0.75);
}
</style>

    <div class="wrap-circles">
  
  <div class="circle per-25">
    <div class="inner">25%</div>
  </div>
  
</div>

</body>
</html>