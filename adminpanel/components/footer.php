</div>
<!--ckeditor-5-->
<script src="plugins/ckeditor/ckeditor.js"></script>
<script src="plugins/ckeditor/config.js"></script>
<!-- main -->
<script src="assets/js/admin-shell.js"></script>
<script>
    // ctrl+s keybind to save
    document.addEventListener('keydown', event => {
        if (event.ctrlKey && event.key === 's') {
            event.preventDefault();
            let saveButton = document.getElementById('saveButton');
            if (saveButton) {
                saveButton.click();
            } else {
                console.log("No save button found");
                // You can handle the absence of the button here
            }
        }
    });
</script>
</body>

</html>