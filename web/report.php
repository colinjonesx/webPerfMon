<?php
$m = new Mongo();
$db = $m->wpm;
$collection = $db->basic;

if(isset($_GET['svr'])){
  $mathCalcs = array();
  $fileCreate = array();
  $fileWrite = array();
  $fileDelete = array();
  
  
  foreach($collection->find(array('server'=>$_GET['svr'])) as $obj){
    $ts = $obj['serverTime'];
    $mathCalcs[] = array($ts, $obj['mathCalcs']);
    $fileCreate[] = array($ts, $obj['fileCreate']);
    $fileWrite[] = array($ts, $obj['fileWrite']);
    $fileDelete[] = array($ts, $obj['fileDelete']);
    
  }
  $results = array(
    array('label'=>'mathCalcs','data'=>$mathCalcs),
    array('label'=>'fileCreate','data'=>$fileCreate),
    array('label'=>'fileWrite','data'=>$fileWrite),
    array('label'=>'fileDelete','data'=>$fileDelete)  
  );
  echo json_encode($results);
}else{?>
<html>
<head><title>Till Results</title>
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
  <script type="text/javascript" src="js/flot/jquery.flot.js"></script>
  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.9/jquery-ui.js"></script>
  <link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.9/themes/redmond/jquery-ui.css" >
  <style type="text/css">
body{font-family:Tahoma; font-size:10pt;padding:50px;}
    </style>
</head>
<body>
<h3>Log Sessions</h3>
<ul class="serverList">

<?php
//phpinfo();

$servers = $db->command(array("distinct" => "basic", "key" => "server"));

foreach ($servers['values'] as $server) {
  echo "<li >$server</li>";
}

?>
</ul>

<div id="chart" style="height:600px;width:900px;"></div>;
<script type="text/javascript">
$(document).ready(function(){
  $('ul.serverList li').click(function(e){
    $(this).append('<img class="ajaxLoading" src="img/ajaxLoading.gif">');
    console.log('tot',$(e.currentTarget).text());
    $.get('report.php',{svr:$(e.currentTarget).text()},function(data, status, req){
      $('ul.serverList img.ajaxLoading').remove();
      plotopts = {
          legend:{position:'nw'},
          xaxis: { mode: "time" },
          series: {
              lines: { show: true },
              points: { show: false }
          },
          grid: { hoverable: true, clickable: true }
          //legend:{container:'#legend'}
        };

    $.plot('#chart',data,plotopts);
    },'json');
  });
});
</script>


</body>
</html>
<?php }