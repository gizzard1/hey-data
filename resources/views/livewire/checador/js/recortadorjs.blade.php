<script>
    
function cropImage(imageSrc, callback) {
    const img = new Image();
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');

    img.onload = function () {
        const originalWidth = img.width;
        const originalHeight = img.height;

        const cropWidth = originalWidth * 0.65;
        const cropHeight = originalHeight * 0.80;
        const startX = (originalWidth - cropWidth) / 2;
        const startY = (originalHeight - cropHeight) / 2;

        canvas.width = cropWidth;
        canvas.height = cropHeight;

        ctx.drawImage(img, startX, startY, cropWidth, cropHeight, 0, 0, cropWidth, cropHeight);
        const croppedImageBase64 = canvas.toDataURL();
        callback(croppedImageBase64);

    };

    img.src = imageSrc;
}
</script>