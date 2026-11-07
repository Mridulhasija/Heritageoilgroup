<div class="modal fade" id="mediaSelectionModal" tabindex="-1" aria-labelledby="imageSelectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header px-4 px-md-4">
                <div class="d-flex align-items-center gap-3">
                    <h5 class="modal-title text-capitalize" id="SelectionModalLabel"></h5>
                    <label class="upload__btn">
                        Add New
                        <input type="file" multiple accept="image/*,application/pdf,video/*,.csv" id="add-new-media" name="file[]" class="upload__inputfile btn btn-primary">
                    </label>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body media-grid-modal row justify-content-center" id="media-grid-body">
                <div class="media-grid">
                    <div class="media-load-sentinel" role="status" aria-live="polite">
                        <span class="media-load-message"></span>
                        <button type="button" class="btn btn-outline-secondary btn-sm media-load-retry d-none">Try again</button>
                    </div>
                </div>
                <div class="col-lg-2 attachment-detail d-none">
                    <h6>Attachment Details</h6>
                    <div id="attachment-preview">
                        <img class="img-fluid">
                    </div>

                    <div class="form-group">
                        Alt Text
                        <input type="text" class="form-control" name="attachment-desc">
                        <input type="hidden" name="attachment-id">
                    </div>
                    <button type="button" class="btn btn-success w-100 mt-2" id="descSaveBtn">
                        <i class="fa fa-floppy-o"></i> Save
                    </button>
                </div>
            </div>
            <!-- Loader -->
            <div id="loader" class="loader d-none">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            <div class="modal-footer pt-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal" id="selectBTN">Select</button>
            </div>
        </div>
    </div>
</div>
<script>
    // Keep the fixed modal outside page layout stacking contexts, alongside Bootstrap's backdrop.
    document.body.appendChild(document.getElementById('mediaSelectionModal'));
</script>