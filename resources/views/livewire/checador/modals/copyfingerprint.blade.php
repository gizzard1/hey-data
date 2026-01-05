<div wire:ignore.self id="modalFingerprint" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Contenido del modal-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title">Registrar Huella</h4>
            </div>

            <!-- Sección de captura -->
            <div id="content-capture" class="p-3">
                <div id="status"></div>
                <canva id="imagediv" class="my-3" hidden></canva>
                <canvas id="canvas" class="my-3" hidden></canvas>
                <canvas id="gaborCanvas" class="my-3"></canvas> 
                <div class="mb-3" hidden>
                    <form name="myForm" class="border p-3">
                        <div class="form-check">
                            <input disabled class="form-check-input" type="checkbox" name="PngImage" id="PngImage" value="4" checked onclick="checkOnly(this)" hidden>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
#imagediv {
	padding:0px;
	margin: 0 auto;
	width: 470px;
	height: 500px;
	overflow: hidden;
    border: 1px solid rgba(51, 122, 183, 0.39);
    display: table;
}

#imagediv img {
	width: 100%;
	height: 500px;
}

#imagediv div{
	text-align: center;
	vertical-align: middle;
	height: 500px;
	background: #d3d3d3;
	display: table-cell;
}
#gaborCanvas {
	padding:0px;
	margin: 0 auto;
    border: 1px solid rgba(51, 122, 183, 0.39);
    display: table;
}

#gaborCanvas img {
	width: 100%;
	height: 500px;
}

#gaborCanvas div{
	text-align: center;
	vertical-align: middle;
	height: 500px;
	background: #d3d3d3;
	display: table-cell;
}
#canvas {
	padding:0px;
	margin: 0 auto;
	width: 470px;
	height: 500px;
	overflow: hidden;
    border: 1px solid rgba(51, 122, 183, 0.39);
    display: table;
}

#canvas img {
	width: 100%;
	height: 500px;
}

#canvas div{
	text-align: center;
	vertical-align: middle;
	height: 500px;
	background: #d3d3d3;
	display: table-cell;
}


#image {
	width: 100%;
}

#imageGallery {
	padding:0px;
	margin: 0 auto;
	width: 100%;
	height: 100px;
	overflow: hidden;
}
</style>
