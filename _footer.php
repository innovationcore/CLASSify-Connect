<?php global $rootURL;
/** @var UserSession $userSession */
?>
    <div class="modal fade" id="termsModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="termsModalLabel">Citation Acknowledgement&nbsp;</h5>
                </div>
                <div class="modal-body" id="termsModalBody">
                    <p>By using CLASSify and any models trained or data generated, I agree to cite the following paper in any related research or publications: <a href="https://arxiv.org/abs/2310.03618/">CLASSify: A Web-based Tool for Machine Learning</a>. The link to this paper is also available in the User Guide at any time. </p>
                    <p>Proper citation acknowledges the work that went into the development of CLASSify and supports continued development of the tools we provide at CAAI. Thank you for helping us continue to advance AI/ML research.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="decline-terms">Decline</button>
                    <button type="button" class="btn btn-primary" id="accept-terms">Accept</button>
                </div>
            </div>
        </div>
    </div>
    </main>
    </div>
    </div>
    <script src="<?= $rootURL?>/js/bootstrap.min.js"></script>
    <script src="<?= $rootURL?>/js/jquery.inputmask.min.js"></script>
    <script src="<?= $rootURL?>/js/font-awesome.min.js"></script>
    <script src="<?= $rootURL?>/js/moment.min.js"></script>
    <script src="<?= $rootURL?>/js/daterangepicker.min.js"></script>
    <script src="<?= $rootURL?>/js/jquery.dataTables.min.js"></script>
    <script src="<?= $rootURL?>/js/dataTables.bootstrap4.min.js"></script>
    <script src="<?= $rootURL?>/js/dataTables.buttons.min.js"></script>
    <script src="<?= $rootURL?>/js/buttons.bootstrap4.min.js"></script>
    <script src="<?= $rootURL?>/js/buttons.colVis.min.js"></script>
    <script src="<?= $rootURL?>/js/toastify.min.js"></script>
    <script src="<?= $rootURL?>/js/modals.js"></script>
    <script src="<?= $rootURL?>/js/bootstrap-select.js"></script>
    <script type="text/javascript">
        var user_info = null;
        $(function() {
            <?php if (isset($_SESSION['FLASH_ERROR'])): ?>
            showError('<?php echo $_SESSION['FLASH_ERROR']; unset($_SESSION['FLASH_ERROR']); ?>');
            <?php endif; ?>
            $('.modal').on('shown.bs.modal', function() {
                $(this).find('[autofocus]').trigger('focus');
            });
            $.ajax({
                url: '<?= $rootURL ?>/users/getUser',
                method: 'get',
                success: function(data) {
                    if (!data.user.accepted_terms) { //If user has not accepted terms yet, show modal
                        user_info = data.user;
                        let current_page = window.location.pathname;
                        if (current_page === '/classify/') {
                            $('#termsModal').modal('show');
                        }
                        else {
                            window.location.href = "<?= $rootURL ?>/";
                        }

                    }
                },
                error : function(request,error) {
                    console.error("Request: "+JSON.stringify(request));
                }
            });
        });

        $('#accept-terms').click(function() {
            user_info.accepted_terms = true;
            $.ajax({
                url: '<?= $rootURL ?>/users/update',
                type: 'POST',
                data: {'profile':user_info},
                dataType: 'json',
                success: function(data) {
                    showSuccess('Acknowledgement accepted');
                    $('#termsModal').modal('hide');
                },
                error : function(request,error) {
                    showError('Could not accept acknowledgement for this user.');
                }
            });
        });

        $('#decline-terms').click(function() {
            showError('Acknowledgement must be accepted to use CLASSify.');
        });

    </script>
    </body>
    </html>
