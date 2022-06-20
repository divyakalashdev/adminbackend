<?php
if(!isset($_COOKIE['mid'])){
    header('Location: ./login.php');exit(0);
}
include 'DB.class.php';
include 'functions.php';
$db = new DB;
include "header.php";

$concount = array(
    'return_type' => 'count'
); 

if(isset($_GET['c'])){
    $catid = $_GET['c'];
    $sql = 'SELECT tags.video_id, tags.tags, videos.*, categories.parent_id, categories.category, categories.priority FROM videos INNER JOIN categories ON videos.catid = categories.id LEFT JOIN tags ON tags.video_id = videos.id WHERE videos.catid = '.$catid.' ORDER BY videos.arrange_id';// DESC/* LIMIT '.$offset.', '.$limit*/
    $concount['where'] = array('catid' => $catid);
    //$rowCount = $db->getRows('videos', $concount);
    $videos = $db->customQuery($sql);
}/*else{
    $sql = 'SELECT tags.video_id, tags.tags, videos.*, categories.parent_id, categories.category, categories.priority FROM videos INNER JOIN categories ON videos.catid = categories.id LEFT JOIN tags ON tags.video_id = videos.id ORDER BY videos.arrange_id';//DESC LIMIT '.$offset.', '.$limit
    $rowCount = $db->getRows('videos', $concount);
}

$pagConfig = array(
    'baseURL' => 'arrange-videos.php',
    'totalRows' => $rowCount,
    'perPage' => $limit
);
$pagination = new Pagination($pagConfig);
*/




$conc = array(
    'order_by' => 'id ASC',
    'where' => array('parent_id' => 0)
);
$categories = $db->getRows('categories', $conc);
$random = rand(10,100);

?>
<style>
ul {
    padding: 20px; 
    list-style-type: decimal;

}
ul li {
    margin: 0 10px;
    padding: 0 10px;
}
.tab{
    width: 50px;
}

</style>
<!-- Begin Page Content -->
        <div class="container-fluid">

          <!-- Page Heading -->
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Arrange Explore Videos</h1>
          </div>
          <div class="row">
            <div class="col-xl-12 col-lg-12">
              <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Arrange Explore Videos</h6>
                  <div>
                    <!-- Button trigger modal -->
                    <!--<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#arrangeCategoryModal">
                      Arrange Category Order
                    </button>-->
                    <div class="col-xl-12 col-lg-12">
                          <label>Filter By</label>
                          <select class="form-select form-control" id="filter_category" name="filter_category" >
                            <option value="">Select category</option>
                            <?php
                            if(!empty($categories)){
                                foreach($categories as $cat){
                                    echo '<option value="'.$cat['id'].'">'.$cat['category'].'</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                  </div>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                  <div class="user_list">
                    <div class="container-fluid col-12">
                          <ul id="sortable">
                            <?php
                                $i = 1;
                                if(!empty($videos)){
                                    foreach($videos as $vid){
                                        $mediafile = '';
                                        if(stripos($vid['video_url'], 'youtu') > 0){
                                            $mediafile = '<iframe width="150" height="90" src="https://www.youtube.com/embed/'.$db->get_youtube_id_from_url($vid['video_url']).'"></iframe>';
                                            //$mediafile = '<video controls="true"><source src="'.$vid['video_url'].'" type="video/mp4" /></video>';
                                        }
                                        else if($vid['type'] == "live"){
                                            $mediafile = '<video width="150" height="90" controls>
                                                                <source src="'.$vid['video_url'].'" type="application/x-mpegURL">
                                                            </video>';
                                        }else{
                                            if(!empty($vid['video_url'])){
                                                $mediafile = '<video width="150" height="90" controls>
                                                                  <source src="'.$vid['video_url'].'?s='.$random.'" type="video/'.explode('.', $vid['video_url'])[1].'">
                                                                </video>';
                                            }else if(!empty($vid['audio_url'])){
                                                $mediafile = '<audio controls>
                                                                      <source src="'.$vid['audio_url'].'" type="audio/'.explode('.', $vid['audio_url'])[1].'">
                                                                    Your browser does not support the audio element.
                                                                </audio>';
                                            }
                                        }
                                        echo '<li id="item-'.$vid['id'].'" dir="rtl" class="row border">
                                        <div class="float-left col-1"><i class="tab fa fa-arrows-alt"></i></div>
                                        <div class="float-left col-1">'.$vid['id'].'</div>
                                        <div class="float-left col-3">'.$mediafile.'</div>
                                        <div class="float-left col-3"><img src="'.$vid['thumbnail'].'?s='.$random.'" width="90" /></div>
                                        <div class="float-left col-2" style="font-size:0.8em">'.$vid['title'].'</div>
                                        <div class="float-left col-1" style="font-size:0.8em">'.$vid['category'].'</div>
                                        <div class="float-left col-1">'.$i++.'</div>
                                        </li>';
                                    }
                                }else{
                                    echo "Select Category";
                                }
                                ?>
                                <div class="position-sticky"><button type="button" id="save_sorting" class="btn btn-primary" >Save</button>
                            </div>
                        </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        
        
<?php include "js_include.php";?>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<script>
init();
function touchHandler(event) {
    var touch = event.changedTouches[0];

    var simulatedEvent = document.createEvent("MouseEvent");
        simulatedEvent.initMouseEvent({
        touchstart: "mousedown",
        touchmove: "mousemove",
        touchend: "mouseup"
    }[event.type], true, true, window, 1,
        touch.screenX, touch.screenY,
        touch.clientX, touch.clientY, false,
        false, false, false, 0, null);

    touch.target.dispatchEvent(simulatedEvent);
    event.preventDefault();
}

function init() {
    document.addEventListener("touchstart", touchHandler, true);
    document.addEventListener("touchmove", touchHandler, true);
    document.addEventListener("touchend", touchHandler, true);
    document.addEventListener("touchcancel", touchHandler, true);
}

$(document).ready(function () {
    
    $('#save_sorting').on('click', function(){
        var data = $('#sortable').sortable('serialize')+"&arrange_videos=arrange";
        $.ajax({
                url: "ajax-videos.php",
                type: "post",
                data: data ,
                success: function (response) {
                    if(response == "OK"){
                        location.reload();
                    }else{
                        
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
    });
    $('ul').sortable({
        axis: 'y',
        stop: function (event, ui) {
	    }
    });
    
});

$('#filter_category').on('change', function(e){
    window.location = './arrange-videos.php?c=' + $('#filter_category').val();
});

$('#parent_cat_alert').on('click', function () {
    location.reload();
});
</script>
<?php include "footer.php"; ?>