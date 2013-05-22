<html>
<head>	
<script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script>
<style type="text/css">
<!--
#Layer1 {
	position:absolute;
	width:19px;
	height:17px;
	z-index:0;
	left: 100px;
	top: 48px;
	background-color:#FF0000
}
-->
</style>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"></head>

<body>
<div id="fb-root"></div> 

<?

    if ($_GET['type'] == 1) {
        echo '<fb:like locale="en_EN" href="http://'.urldecode($_GET['p']).'" send="false" layout="button_count" width="50" show_faces="false"></fb:like>';
    } else {
        echo '<fb:like-box locale="en_EN" href="http://'.urldecode($_GET['p']).'" width="295" height="128" show_faces="false" stream="false" header="false"></fb:like-box>
';
    }
?>


</body>
</html>
