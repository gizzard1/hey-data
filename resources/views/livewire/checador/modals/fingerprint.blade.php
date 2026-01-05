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
				
                <div id="loader" wire:loading.block> 
                    <div class="loader"></div>
                </div>

				<input type="file" id="upload" />
				<label for="thresholdRange" hidden>Threshold: <span id="thresholdValue">128</span></label>
				<input type="range" hidden id="thresholdRange" min="0" max="255" value="128" />
				<label for="circleSize" hidden>Circle Size: <span id="circleSizeValue">10</span></label>
				<input type="range" hidden id="circleSize" min="2" max="30" value="10" />
				<div class="d-flex fingerprintarea">
					<canvas id="canvas" class="mt-5"></canvas>
				</div>
				<canvas id="overlay" hidden></canvas>

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
	
input#circleSize {
    width: 80rem;
}
canvas#canvas {
    max-width: 29rem;
}
input#thresholdRange {
    width: 80rem;
}

.loader {
    border: 6px solid #e2e8f0;
    border-top: 6px solid #2b6cb0;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    animation: spin 1s linear infinite;
    margin-bottom: 1em;
    position: absolute;
    z-index: 1;
    justify-self: anchor-center;
    align-self: anchor-center;
}
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
.fingerprintarea {
    filter: blur(4px);
}
</style>
