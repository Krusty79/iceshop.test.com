<?php
	foreach(glob("./src/*.class.php") as $class){
	    require_once $class;
    }
?>
<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="description" content="">
		<meta name="author" content="">
		<title>Iceshop test task</title>
		<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.1/css/bootstrap.css" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
		<link href="/open-iconic/font/css/open-iconic-bootstrap.css" rel="stylesheet">
		<!-- Custom styles for this template -->
		<link href="/css/cover.css" rel="stylesheet">
		<!-- Bootstrap core JavaScript
			================================================== -->
		<!-- Placed at the end of the document so the pages load faster -->
		<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
		<script src="/js/popper.min.js"></script>
		<script src="/js/bootstrap.min.js"></script>
		<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
		<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
		<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
	</head>
	<body>
		<div class="d-flex w-100 h-100 p-3 mx-auto flex-column">
			<main role="main" class="inner cover">
				<div class="modal" tabindex="-1" id="uploaderStatus" role="dialog">
					<div class="modal-dialog modal-lg" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title">Uploder status.</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
							</div>
							<div class="modal-body">
								<div id="output">&nbsp;</div>
							</div>
						</div>
					</div>
				</div>
                
                <form name="search_image" action="/search_image.php" method="post">
                    <input type="hidden" name="clientId" value="client_1"/>
                    <div class="input-group">
                        <input 
                            class="form-control"  
                            placeholder="Global Serach"
                            type="text" 
                            name="search"/>
                        <span class="input-group-btn">
                                <button class="btn btn-dark" type="submit">Go!</button>
                        </span>
                    </div>
                </form>

                <form>
                <div class="input-group">
                    <input 
                        class="form-control"  
                        placeholder="Search by hash:"
                        type="text" 
                        name="hash"/>
                    <span class="input-group-btn">
                            <button id="search" class="btn btn-dark" type="button">Go!</button>
                    </span>
                </div>
                </form>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="uploadAssetModalLabel">Upload images:</h5>
                    </div>
                    <form name="upload" action="upload.php" method="POST" enctype="multipart/form-data">
                       <div class="modal-body">
                            <div class="form-group">
                                <input 
                                    class="form-control"  
                                    placeholder="File Name"
                                    type="file" 
                                    name="userfile[]" 
                                    multiple accept="image/*"/>
                            </div>
                            <div class="form-group">
                                <select name="clientId">
                                    <option>client_1</option>
                                    <option>client_2</option>
                                    <option>client_3</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-dark">Upload Images</button>
                        </div>
                    </form>
                </div>
                <table id="dinamic_tab" class="table table-sm table-dark compact cell-border" style="width:100%">
                    <thead>
                        <tr>
                            <th> id </th>
                            <th> originalName </th>
                            <th> status </th>
                            <th> url </th>
                            <th> hash </th>
                            <th> date modified </th>
                        </tr>
                    </thead>
                </table>
                <div style="clear:both"></div>
				<script>
                    $(document).ready(function() {
                        var hashSearch;
                        $("input[name=hash]").on("keyup",function(){
                            $hash = $(this).val();
                            clearTimeout(hashSearch)
                            hashSearch = setTimeout(function(){
                                myTable.ajax.url("/ajax/images.php?clientId="+$("select[name=clientId]").val()+"&hash="+$hash).load();
                                console.log("/ajax/images.php?clientId="+$("select[name=clientId]").val()+"&hash="+$hash);
                            },1000);
                        });
                        $("select[name=clientId]").on("change",function(){
                            myTable.ajax.url('/ajax/images.php?clientId='+$(this).val() ).load();
                        });
                        var myTable = $('#dinamic_tab').DataTable( {
                            "ajax": "/ajax/images.php?clientId="+$("select[name=clientId]").val(),
                            stateSave: true,
                            "columns": [
                                { 
                                    "data"      : "id", 
                                    class       : "wrapok" 
                                },
                                { 
                                    "data"      : "originalName", 
                                    class       : "wrapok" 
                                },
                                { 
                                    "data"      : "status", 
                                    class       : "wrapok" 
                                },
                                { 
                                    "data"      : "url", 
                                    class       : "wrapok",
                                    "render"    : function(data, type, full, meta){
                                        return "<a href='"+full.url+"' target='_blank'>"+full.url+"</a>";
                                    } 
                                },
                                { 
                                    "data"      : "hash", 
                                    class       : "wrapok",
                                    "render"    : function(data, type, full, meta){
                                        return "<a href='/image.php?hash="+full.hash+"' target='_blank'>"+full.hash+"</a>";
                                    }
                                },
                                { 
                                    "data"      : "uploadedat", 
                                    class       : "wrapok" 
                                }
                            ],
                            "order": [[ 5, "desc" ]],
                        } );

                        $('form[name="upload"]').submit(function(e) {
                            e.preventDefault();
                            data = new FormData($(this)[0]);
                            $.ajax({
                                type: 'POST',
                                url: './upload.php',
                                data: data,
                                cache: false,
                                dataType : 'json',
                                contentType: false,
                                processData: false,
                                success: function( resp ){
                                    $("#output").html("");
                                    var log = "";
                                    $.each(resp, function(name, image) {;
                                        
                                        if(image.status == "Success"){
                                            console.log(image.DB);
                                            log+="<div class='name'>"+name+" saved as "+image.DB.name_saved+"</div>";
                                            log+="<div class='status'>upload status: "+image.status+"</div>";
                                        }else{
                                            log+="<div class='name'>"+name+"Is not saved: "+image.Error_message+"</div>";
                                            log+="<div class='status'>upload status: "+image.status+"</div>";
                                        }
                                    });
                                    $("#output").html(log);
                                    $('#uploaderStatus').modal('show');
                                    myTable.ajax.reload();
                                }
                            }).done(function(data) {                                                
                                
                            }).fail(function(jqXHR,status, errorThrown) {
                                $("#output").html("Upload ERROR");
                                $('#uploaderStatus').modal('show');
                            }); 
                            
                        });   

                        $('form[name="search_image"]').submit(function(e) {
                            e.preventDefault();
                            data = new FormData($(this)[0]);
                            $.ajax({
                                type: 'POST',
                                url: './/search_image.php',
                                data: data,
                                cache: false,
                                dataType : 'json',
                                contentType: false,
                                processData: false,
                                success: function( resp ){
                                    $("#output").html("");
                                    var log = "";
                                    $.each(resp.data, function(name, image) {;
                                        
                                        log+="<div class='name'>originalName: "+image.originalName+"</div>";
                                        log+="<div class='status'>link: "+image.link+"</div>";
                                        
                                    });
                                    $("#output").html(log);
                                    $('#uploaderStatus').modal('show');
                                }
                            }).done(function(data) {                                                
                                
                            }).fail(function(jqXHR,status, errorThrown) {
                                $("#output").html("Upload ERROR");
                                $('#uploaderStatus').modal('show');
                            }); 
                            
                        });   

                    } );
				</script>
			</main>
		</div>
	</body>
</html>