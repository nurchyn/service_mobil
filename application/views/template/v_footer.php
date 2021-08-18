<?php
                        if(isset($modal)) {
                            if(is_array($modal)){
                                foreach ($modal as $keys => $values) {
                                    echo $values;
                                }
                            }else{
                                echo $modal;
                            }
                        }

                        echo $modal_excel_upload;
                    ?>
                    <!-- begin:: Footer -->
                    <div class="kt-footer  kt-grid__item kt-grid kt-grid--desktop kt-grid--ver-desktop" id="kt_footer">
                        <div class="kt-container  kt-container--fluid ">
                            <div class="kt-footer__copyright">
                                2019&nbsp;&copy;&nbsp;<a href="https://wijayacarwash.com" target="_blank" class="kt-link">Wijaya Carwash</a>
                            </div>
                            <!-- <div class="kt-footer__menu">
                                <a href="http://keenthemes.com/metronic" target="_blank" class="kt-footer__menu-link kt-link">About</a>
                                <a href="http://keenthemes.com/metronic" target="_blank" class="kt-footer__menu-link kt-link">Team</a>
                                <a href="http://keenthemes.com/metronic" target="_blank" class="kt-footer__menu-link kt-link">Contact</a>
                            </div> -->
                        </div>
                    </div>
                    <!-- end:: Footer -->

                </div>

			</div>
			<!-- end:: KT-Page -->
		</div>
		<!-- end:: Page -->

		<!-- Quick Panel di panel_dashboard.php-->
		
		<!-- begin::Scrolltop -->
		<div id="kt_scrolltop" class="kt-scrolltop">
			<i class="fa fa-arrow-up"></i>
		</div>
        <!-- end::Scrolltop -->

        <!-- end::Global Config -->

        <!--begin::Global Theme Bundle(used by all pages) -->
        <script src="<?= base_url('assets/template/'); ?>assets/plugins/global/plugins.bundle.js" type="text/javascript"></script>
        <script src="<?= base_url('assets/template/'); ?>assets/js/scripts.bundle.js" type="text/javascript"></script>
        <!-- <script src="<?= base_url('assets/template/'); ?>assets/js/dropzone.js" type="text/javascript"></script> -->
        <script src="<?= base_url('assets/template/'); ?>assets/plugins/custom/datatables/datatables.bundle.js" type="text/javascript"></script>
        <script src="<?= base_url('assets/'); ?>plugins/ckeditor/ckeditor.js" type="text/javascript"></script>
        <script src="<?= base_url('assets/'); ?>plugins/ckeditor/adapters/jquery.js" type="text/javascript"></script>
        <script src="<?= base_url('assets/'); ?>plugins/jquery-mask-money/dist/jquery.maskMoney.min.js" type="text/javascript"></script>
        <script src="<?= base_url('assets/'); ?>plugins/inputmask/dist/jquery.inputmask.min.js" type="text/javascript"></script>
        <script src="<?= base_url('assets/'); ?>plugins/keyboard-js/dist/keyboard.min.js" type="text/javascript"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
        
        <!--end::Global Theme Bundle -->
        
        <!-- begin::Global Config(global config for global JS sciprts) -->
        <script>
            <?php 
                $obj_date = new DateTime(); 
            ?>

            let jam = "<?= (int)$obj_date->format('H');?>";
            let menit = "<?= (int)$obj_date->format('i');?>";
            let detik = "<?= (int)$obj_date->format('s');?>";
            
            $(window).on('load', function(){
                $('div#CssLoader').addClass('hidden');
                jamSistem();
            });
            
            let base_url = "<?= base_url(); ?>";
            var KTAppOptions = {
                "colors": {
                    "state": {
                        "brand": "#5d78ff",
                        "dark": "#282a3c",
                        "light": "#ffffff",
                        "primary": "#5867dd",
                        "success": "#34bfa3",
                        "info": "#36a3f7",
                        "warning": "#ffb822",
                        "danger": "#fd3995"
                    },
                    "base": {
                        "label": [
                            "#c5cbe3",
                            "#a1a8c3",
                            "#3d4465",
                            "#3e4466"
                        ],
                        "shape": [
                            "#f0f3ff",
                            "#d9dffa",
                            "#afb4d4",
                            "#646c9a"
                        ]
                    }
                }
            };

            const swalConfirm = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-md btn-primary',
                    cancelButton: 'btn btn-md btn-danger'
                },
                buttonsStyling: false
            });

            const swalConfirmDelete = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-md btn-danger',
                    cancelButton: 'btn btn-md btn-primary'
                },
                buttonsStyling: false
            });

            function jamSistem()
			{
				if (detik!=0 && detik%60==0) { menit++; detik=0; }
				second = Number(detik);
				if (menit!=0 && menit%60==0) { jam++; menit=0; }
				minute = Number(menit);
				if (jam!=0 && jam%24==0) { jam=0; }
				hour = Number(jam);
				if (detik<10) { second='0'+detik; }
				if (menit<10){ minute='0'+menit; }
				if (jam<10){ hour='0'+jam; }
				waktu = hour+':'+minute+':'+second;
                //  console.log(waktu);
                $('.jamServer').text("<?=$obj_date->format('d-m-Y');?>"+' '+waktu);
                
				detik++;
			}

			setInterval(jamSistem, 1000);

            function to_upper(objek) {
                var _a = objek.value;
                objek.value = _a.toUpperCase();
            }

            function reInitSelectMulti(){
                $('.select2_multi').select2({
                    placeholder: "Mohon Pilih Salah Satu",
                });
            }

            function reInitSelectSingle(){
                $('.select2').select2({
                    placeholder: "Mohon Pilih Salah Satu",
                });
            }

            function reInitInputMask(){
                $(".inputmask").inputmask({
                    prefix: "",
                    groupSeparator: ".",
                    radixPoint: ",",
                    alias: "currency",
                    placeholder: "0",
                    autoGroup: true,
                    digits: 2,
                    digitsOptional: false,
                    clearMaskOnLostFocus: false,
                    inputmode: "numeric",
                    onBeforeMask: function (value, opts) {
                        return value;
                    },
                });
            }

            $(document).ready(function () {
                $('.select2').select2({
                    allowClear: true,
                    placeholder: "Mohon Pilih Salah Satu"
                });

                $('.kt_datepicker').datepicker({
                    rtl: KTUtil.isRTL(),
                    todayHighlight: true,
                    format: "dd/mm/yyyy",
                    autoclose: true,
                    orientation: "bottom left",
                    templates: {
                        leftArrow: '<i class="la la-angle-left"></i>',
                        rightArrow: '<i class="la la-angle-right"></i>'
                    }
                });

                $('input.numberinput').bind('keypress', function (e) {
                    return (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57) && e.which != 46) ? false : true;
                });

                $(".inputmask").inputmask({
                    prefix: "",
                    groupSeparator: ".",
                    radixPoint: ",",
                    alias: "currency",
                    placeholder: "0",
                    autoGroup: true,
                    digits: 2,
                    digitsOptional: false,
                    clearMaskOnLostFocus: false,
                    inputmode: "numeric",
                    onBeforeMask: function (value, opts) {
                        return value;
                    },
                });
            });
        </script>
        <?php if(isset($link_js)) { ?>
        <?php if(is_array($link_js)){ ?>
        <?php foreach ($link_js as $keys => $values) { ?>
        <script src="<?= base_url("$values"); ?>" type="text/javascript"></script>
        <?php } ?>
        <?php }else{ ?>
        <script src="<?= base_url("$link_js"); ?>" type="text/javascript"></script>
        <?php } ?> 
        <?php } ?>
    </body>

	<!-- end::Body -->
</html>