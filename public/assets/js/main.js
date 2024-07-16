var main_app = main_app || {};
var currentPage = $('meta[name="current-page"]').attr('content');
main_app = {
    jWindow: $(window),
    jBody: $('body'),
    timeReload: 1500,
    layout: {
        handleLayout() {
            // Handle add setting
            var type = $("input[type='radio'][name='type_setting']:checked").val();
            if(type == 'text'){
                $('.js_type_file').css('display', 'none');
            }
            $('.js_change_setting_type').on('change', function(){
                if($(this).val() == 'file'){
                    $('.js_type_text').css('display', 'none');
                    $('.js_type_file').css('display', 'block');
                }else{
                    $('.js_type_text').css('display', 'block');
                    $('.js_type_file').css('display', 'none');
                }
            });

            // Handle acctive class menu parent for detail page
            if(currentPage == 'admin.users.worker-detail'){
                $('ul li.has-treeview').addClass('menu-open');
                $('.js_worker_link').addClass('active');
            }

            if(currentPage == 'admin.users.customer-detail'){
                $('ul li.has-treeview').addClass('menu-open');
                $('.js_customer_link').addClass('active');
            }

            if(currentPage == 'admin.orders.detail'){
                $('.js_orders_link').addClass('active');
            }

            if(currentPage == 'admin.logs_views'){
                $('.js_logs_views_link').addClass('active');
            }
            // Handle active menu current
            var curentUrl = window.location;
            $('.js-nav-sidebar-menu a').filter(function() {
                return this.href == curentUrl;
            }).siblings().removeClass('active').end().addClass('active');

            $('.js-nav-treeview a').filter(function() {
                return this.href == curentUrl;
            }).parentsUntil(".nav-sidebar > .nav-treeview").siblings().removeClass('active menu-open').end().addClass('active menu-open');

            // Tooltip
            $('[data-toggle="tooltip"]').tooltip();

            // Show password
            $('.js_show_text_pw').on('click', function(e){
                $('.js_input_pw').attr('type', function(index, attr){
                    return attr == 'password' ? 'text' : 'password';
                });

                $('.js_show_text_pw i').attr('class', function(index, attr){
                    return attr == 'fas fa-eye' ? 'fas fa-eye-slash' : 'fas fa-eye';
                });
            })

            // Date range picker
            // Select daterangepicker dd-mm-yyyy
            var formatDateDMY = 'MM-DD-YYYY';
            $('.js_select_m_d_y').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    format: formatDateDMY
                },
            });
            $('input.js_select_m_d_y').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format(formatDateDMY) + ' - ' + picker.endDate.format(formatDateDMY));
            });
          
            $('input.js_select_m_d_y').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });

            var params = new window.URLSearchParams(window.location.search);
            var dates = params.get('dates');
            if(dates){
                dates = dates.split(' - ');
                if(dates.length){
                    $('.js_select_m_d_y').daterangepicker({
                        autoUpdateInput: true,
                        startDate: dates[0],
                        endDate: dates[1],
                        locale: {
                            format: formatDateDMY
                        },
                    });
                }
            }

            // Load select2
            $('.input_select2').select2();
        }
    },
    setupAjaxHeaders(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    },
    radomNumber(max = 999999, min = 100000){
        return Math.floor(Math.random() * (max - min) + min);
    },
    loadLozad(){
        const observer = lozad(); // lazy loads elements with default selector as ".lozad"
        observer.observe();
    },
    reloadDatatable(){
        $('.js_click_reload_datatable').on('click', function(e){
            e.preventDefault();
            $.ajax({
                url : '/admin/clear-cache',
                method: "POST",
                success: function(res){
                    if(res){
                        $('.js_reload_datatable').DataTable().ajax.reload();
                    }
                },
                error: function(err){

                }
            })
        });
    },
    reloadGridDatatable(){
        $('.js_reload_datatable').DataTable().ajax.reload();
    },
    showModalResetPassOnGird(){
        var self = this;
        $(document).on('click', '.js_show_modal_reset_pass', function(e){
            e.preventDefault();
            var url = $(this).attr('data-url');
            $('#grid-modal-reset-pass').modal('show');
            $('#grid-modal-reset-pass .js_reset_password').attr('data-url', url);
        });
    },
    createSweetAlertWarning(title = 'Are you sure to delete this?', callback, sweetType = 'warning'){
        var _this = $(this);
        Swal.fire({
            title: title,
            width: 350,
            type: sweetType,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ok'
        }).then(function(result) {
            if (result.value) {
                callback.call(_this);
            } 
        });
    },
    reloadPage(){
        var self = this;
        setTimeout(function(){
            location.reload();
        }, self.timeReload);  
    },
    reloadDatatableUser(){
        $('#table_data_user').DataTable().ajax.reload();
    },
    fixCrollbarModal(){
        $(document).on('hidden.bs.modal', '.modal', function () {
            $('.modal:visible').length && $(document.body).addClass('modal-open');
        });
    },
    updateUserStatus(){
        var self = this;
        $(document).on('click', '.js_update_status', function(e) {
            e.preventDefault(); 
            var id = $(this).attr('data-id');
            var url = $(this).attr('data-url');
            var status = $(`.js_select_status_${id}`).val();
            callAjaxUpdatStatus(url, status);
        });

        $('.js_update_status_single').on('click', function(e) {
            e.preventDefault(); 
            var url = $(this).attr('data-url');
            var status = $('.js_select_status_single').val();
            callAjaxUpdatStatus(url, status, 'single');
        });

        function callAjaxUpdatStatus(url, status, type = ''){
            self.createSweetAlertWarning('Are you sure to update this status?', function(){
                $.ajax({
                    url: url,
                    method: "POST",
                    data: {
                        status: status
                    },
                    success: function(res){
                        if(res){
                            toastr.success(res.data.message);
                            self.reloadDatatableUser();
                            $('.modal-update-status').modal('hide');
                            if(type == 'single'){
                                self.reloadPage();
                            }
                        }
                    },
                    error: function(err){
                        toastr.error(err.responseJSON.message);
                        self.reloadDatatableUser();
                        $('.modal-update-status').modal('hide');
                    }
                });
            });
        }
    },
    removeUser(){
        var self = this;
        $(document).on('click', '.js_remove_user', function(e){
            var name = $(this).attr('data-name');
            var url = $(this).attr('data-url');

            self.createSweetAlertWarning(`Are you sure to delete ${name} ?`, function(){
                $.ajax({
                    url: url,
                    method: "POST",
                    success: function(res){
                        if(res){
                            Swal.fire(
                                res.data.message,
                                'Your file has been deleted.',
                                'success'
                            ).then(function() {
                                self.reloadDatatableUser();
                            });
                        }
                    },
                    error: function(err){
                        Swal.fire({
                            type: 'error',
                            title: 'Error',
                            text: err.responseJSON.message,
                        }).then(function() {
                            self.reloadDatatableUser();
                        });
                    }
                });
            });
        });
    },
    updateProfileInformation(){
        var self = this;
        $('.js_update_profile').on('click', function(e){
            e.preventDefault();
            var formData = new FormData($('.js_form_profile_inforamtion')[0]);
            var url = $(this).attr('data-url');

            $.ajax({
                url: url,
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(res){
                    if(res){
                        toastr.success(res.data.message)
                        self.reloadPage();  
                    }
                },
                error: function(err){
                    toastr.error(err.responseJSON.message);
                    // self.reloadPage();
                }
            });
        });
    },
    resetPassword(){
        var self = this;
        var passwordRandom = '';
        // Handle random password
        $('.js_random_number').on('click', function(e){
            e.preventDefault();
            passwordRandom = self.radomNumber();
            $('.js_show_pw_random').text(passwordRandom);
            $('.js_pw_value').val(passwordRandom);
        });

        // Handle click reset password
        $('.js_reset_password').on('click', function(e){
            e.preventDefault(); 
            var password = $('.js_pw_value').val();
            var url = $(this).attr('data-url');
            if(password){
                $('.js_validate_pw_reset').css('display', 'none');
                self.createSweetAlertWarning('Are you sure to reset this password?', function(){
                    $.ajax({
                        url: url,
                        method: "POST",
                        data: {
                            new_password: password
                        },
                        success: function(res){
                            if(res){
                                toastr.success(res.data.message);
                                $('.js_show_pw_random').text('');
                                $('.js_pw_value').val('');
                                passwordRandom = '';
                                setTimeout(function(){
                                    $('#modal-reset-password').modal('hide');
                                }, self.timeReload);
                            }
                        },
                        error: function(err){
                            toastr.error(err.responseJSON.message);
                            $('#modal-reset-password').modal('hide');
                        }
                    });
                });
            }else{
                $('.js_validate_pw_reset').css('display', 'block');
            }
        })
    },
    handleRolePermission(){
        var self = this;
        $('.js_show_modal_permission').on('click', function(e){
            e.preventDefault();
            $('#modal-role-permission').modal({backdrop: 'static', keyboard: false});
        })

        $('.js_show_modal_create_role').on('click', function(e){
            e.preventDefault();
            $('#modal-create-role').modal({backdrop: 'static', keyboard: false});
        })

        // Creat role
        $('.js_create_role').on('click', function(e){
            e.preventDefault();
            var roleName = $('.js_get_role_name').val();
            $.ajax({
                url: '/admin/settings/roles/add-role',
                method: "POST",
                data: {
                    role_name: roleName
                },
                success: function(res){
                    if(res.data){
                        toastr.success('Add department success!');
                        $('#modal-create-role').modal('hide');
                        $('.js_wrap_option_role').html(res.data.html_option_role);
                    }
                },
                error: function(err){
                    if(err.status === 422){
                        var errors = err.responseJSON;
                        $('.help-block').html('');
                        $.each(errors['errors'], function (index, value) {
                            $('.'+index+'-error').html(value);
                        });
                    }else{
                        toastr.error('Error! Please try again.');
                    }  
                }
            });
        });

        // Setting role permision
        $(document).on('click', '.js_setting_role_permission', function(e){
            e.preventDefault();
            var formData = new FormData($('.js_fr_role_permission')[0]);

            $.ajax({
                url : '/admin/settings/roles/edit',
                method : 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res){
                    if(res.data){
                        toastr.success('Success decentralization!');
                        $('#modal-role-permission').modal('hide');
                        // self.reloadPage();
                    }
                },
                error: function(err){
                    toastr.error('Error! Please try again.');
                }
            });
        });

        // Change role
        $(document).on('change', '.js_change_role', function(e){
            var roleData = $(this).val();
            $.ajax({
                url: '/admin/settings/roles/ajax-data-edit-role',
                method: "POST",
                data: {
                    role_data: roleData
                },
                success: function(res){
                    if(res.data.html_page){
                        $('.js-wrap-pages').html(res.data.html_page);
                    }
                },
                error: function(err){
                    console.log(err);
                }
            });
        });

    },
    createUserAccount(){
        var self = this;
        $('.js_show_modal_create_account').on('click', function(e){
            e.preventDefault();
            $('#modal-create-user-account').modal({backdrop: 'static', keyboard: false});
        });

        $('.js_create_user_account').on('click', function(e){
            e.preventDefault();
            var formData = new FormData($('.js_fr_create_account')[0]);
            $.ajax({
                url : '/admin/users/create-account',
                method : 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res){
                    if(res.data){
                        toastr.success('Add account success!');
                        $('#modal-create-user-account').modal('hide');
                        self.reloadGridDatatable();
                    }
                },
                error: function(err){
                    if(err.status === 422){
                        var errors = err.responseJSON;
                        $('.help-block').html('');
                        $.each(errors['errors'], function (index, value) {
                            $('.'+index+'-error').html(value);
                        });
                    }else{
                        toastr.error('Error! Please try again.');
                    }  
                }
            });
        })
    },
    removeItem(){
        var self = this;
        $(document).on('click', '.js_remove_item', function(e){
            var url = $(this).attr('data-url');

            self.createSweetAlertWarning(`Are you sure you want to delete?`, function(){
                $.ajax({
                    url: url,
                    method: "POST",
                    success: function(res){
                        if(res){
                            Swal.fire(
                                'Delete success!',
                                'This has been deleted.',
                                'success'
                            ).then(function() {
                                self.reloadGridDatatable();
                            });
                        }
                    },
                    error: function(err){
                        Swal.fire({
                            type: 'error',
                            title: 'Error',
                            text: err.responseJSON.message,
                        }).then(function() {
                            self.reloadGridDatatable();
                        });
                    }
                });
            });
        });
    },
    updateProfileAccount(){
        var self = this;
        $(document).on('click', '.js_show_modal_update_account', function(e){
            e.preventDefault();
            $('#modal-update-profile-account').modal({backdrop: 'static', keyboard: false});
            var id = $(this).attr('data-id');
            $.ajax({
                url: '/admin/users/ajax/get-data-user-account',
                method: "GET",
                data: {
                    user_id: id
                },
                success: function(res){
                    if(res.data.html_form){
                        $('.js_wrap_fr_profile_account').html(res.data.html_form);
                    }
                },
                error: function(err){
                    console.log(err);
                }
            });
        });

        // Update
        $(document).on('click', '.js_update_profile_account', function(e){
            e.preventDefault();
            var url = $(this).attr('data-url');
            var formData = new FormData($('.js_fr_profile_account')[0]);
            $.ajax({
                url : url,
                method : 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res){
                    if(res.data){
                        toastr.success('Update account success!');
                        $('#modal-update-profile-account').modal('hide');
                        self.reloadGridDatatable();
                    }
                },
                error: function(err){
                    if(err.status === 422){
                        var errors = err.responseJSON;
                        $('.help-block').html('');
                        $.each(errors['errors'], function (index, value) {
                            $('.'+index+'-error').html(value);
                        });
                    }else{
                        toastr.error('Error! Please try again.');
                    }  
                }
            });
        })
    },
    removeTokenPayment(){
        var self = this;
        $('.js_remove_token_payment').on('click', function(e){
            e.preventDefault();
            var id = $(this).attr('data-id');
            var name = $(this).attr('data-name');
            var url = $(this).attr('data-url');
            self.createSweetAlertWarning(`Are you sure want to delete  ${name} card?`, function(){
                $.ajax({
                    url : url,
                    method : 'POST',
                    data: {card_id: id},
                    success: function(res){
                        if(res.data){
                            toastr.success('Delete card success!');
                            self.reloadPage();
                        }
                    },
                    error: function(err){
                        toastr.error('Error! Please try again.');
                    }
                });
            });
        })
    },
    approveCashout(){
        var self = this;
        $(document).on('click', '.js_approve_cashout', function(e){
            var id = $(this).attr('data-id');
            var url = $(this).attr('data-url');
            e.preventDefault();
            self.createSweetAlertWarning(`Are you sure want to approve it?`, function(){
                $('.js-loading-transfer-money').css('display', 'block');
                $.ajax({
                    url : url,
                    method : 'POST',
                    data: {cashout_id: id},
                    success: function(res){
                        if(res.data){
                            $('.js_show_cashout_waiting').html(res.data.html_badge_waiting);
                            if(res.data.success){
                                toastr.success(res.data.message);
                            } else {
                                toastr.error(res.data.message);
                            }
                            $('.js-loading-transfer-money').css('display', 'none');
                            self.reloadGridDatatable();
                        }
                    },
                    error: function(err){
                        self.reloadGridDatatable();
                        $('.js-loading-transfer-money').css('display', 'none');
                        toastr.error(err.responseJSON.message);
                    }
                });
            }, 'success')
        });
    },
    handleCancelCashout(){
        var self = this;

        // Show modal cancel cashout
        $(document).on('click', '.js_show_modal_cancel_cashout ', function(e){
            e.preventDefault();
            var id = $(this).attr('data-id');
            $('#modal-cancel-cashout').modal('show');
            $('.js_set_cashout_id').val(id);
        })

        // Handle cancel cashout
        $(document).on('click', '.js_submit_cancel_cashout', function(e){
            e.preventDefault();
            var url = $(this).attr('data-url');
            var cashoutId = $('.js_set_cashout_id').val();
            var reason = $('.js_get_reason').val();

            if(!reason){
                $('.reason-error').html('The reason is required!')
                return false;
            }

            $.ajax({
                url : url,
                method : 'POST',
                data: {cashout_id: cashoutId, reason: reason},
                success: function(res){
                    $('#modal-cancel-cashout').modal('hide');
                    if(res.data){
                        $('.js_show_cashout_waiting').html(res.data.html_badge_waiting);
                        toastr.success('Cancel cashout success!');
                        self.reloadGridDatatable();
                    }
                },
                error: function(err){
                    $('#modal-cancel-cashout').modal('hide');
                    toastr.error('Error! Please try again.');
                    console.log(err);
                }
            });
        })
    },
    createJobOverViewChart(){
        var areaChartCanvas = $('#jobOverViewChart').get(0).getContext('2d')

        var areaChartData = {
            labels  : ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
            datasets: [
                {
                    label               : 'Digital Goods',
                    backgroundColor     : 'rgba(60,141,188,0.9)',
                    borderColor         : 'rgba(60,141,188,0.8)',
                    pointRadius          : false,
                    pointColor          : '#3b8bba',
                    pointStrokeColor    : 'rgba(60,141,188,1)',
                    pointHighlightFill  : '#fff',
                    pointHighlightStroke: 'rgba(60,141,188,1)',
                    data                : [28, 48, 40, 19, 86, 27, 90]
                },
                {
                    label               : 'Electronics',
                    backgroundColor     : 'rgba(210, 214, 222, 1)',
                    borderColor         : 'rgba(210, 214, 222, 1)',
                    pointRadius         : false,
                    pointColor          : 'rgba(210, 214, 222, 1)',
                    pointStrokeColor    : '#c1c7d1',
                    pointHighlightFill  : '#fff',
                    pointHighlightStroke: 'rgba(220,220,220,1)',
                    data                : [65, 59, 80, 81, 56, 55, 40]
                },
            ]
        }

        var areaChartOptions = {
            maintainAspectRatio : false,
            responsive : true,
            legend: {
                display: false
        },
        scales: {
            xAxes: [{
                gridLines : {
                    display : false,
                }
            }],
            yAxes: [{
                gridLines : {
                    display : false,
                }
            }]
        }
        }

        // This will get the first returned node in the jQuery collection.
        new Chart(areaChartCanvas, {
            type: 'line',
            data: areaChartData,
            options: areaChartOptions
        })
    },
    createMapVisitor(){
        // jvectormap data
        var visitorsData = {
            US: 398, // USA
            SA: 400, // Saudi Arabia
            CA: 1000, // Canada
            DE: 500, // Germany
            FR: 760, // France
            CN: 300, // China
            AU: 700, // Australia
            BR: 600, // Brazil
            IN: 800, // India
            GB: 320, // Great Britain
            RU: 3000 // Russia
        }
        $('#world-map').vectorMap({
            map: 'usa_en',
            backgroundColor: 'transparent',
            regionStyle: {
                initial: {
                    fill: 'rgba(255, 255, 255, 0.7)',
                    'fill-opacity': 1,
                    stroke: 'rgba(0,0,0,.2)',
                    'stroke-width': 1,
                    'stroke-opacity': 1
                }
            },
            series: {
                regions: [{
                    values: visitorsData,
                    scale: ['#ffffff', '#0154ad'],
                    normalizeFunction: 'polynomial'
                }]
            },
            onRegionLabelShow: function (e, el, code) {
                if (typeof visitorsData[code] !== 'undefined') {
                    el.html(el.html() + ': ' + visitorsData[code] + ' new visitors')
                }
            }
        });
    },
    createSparkLineChart(){
        // Sparkline charts
        var sparklineVisitor = new Sparkline($('#sparkline-visitor')[0], { width: 80, height: 50, lineColor: '#92c1dc', endColor: '#ebf4f9' })
        var sparklineOnline = new Sparkline($('#sparkline-online')[0], { width: 80, height: 50, lineColor: '#92c1dc', endColor: '#ebf4f9' })
        var sparklineSale = new Sparkline($('#sparkline-sale')[0], { width: 80, height: 50, lineColor: '#92c1dc', endColor: '#ebf4f9' })

        sparklineVisitor.draw([1000, 1200, 920, 927, 931, 1027, 819, 930, 1021])
        sparklineOnline.draw([515, 519, 520, 522, 652, 810, 370, 627, 319, 630, 921])
        sparklineSale.draw([15, 19, 20, 22, 33, 27, 31, 27, 19, 30, 21])
    },
    loadChartDashboard(){
        var self = this;
        if(currentPage == 'admin.dashboard'){
            self.createJobOverViewChart();
            self.createMapVisitor();
            self.createSparkLineChart();
            self.runTimeClock();
        }
    },
    runTimeClock(){
        setInterval(function(){
            const today = new Date();
            let h = today.getHours();
            let m = today.getMinutes();
            let s = today.getSeconds();
            h = checkTime(h);
            m = checkTime(m);
            s = checkTime(s);
            document.getElementById('js_set_time').innerHTML =  h + ":" + m + ":" + s;
            function checkTime(i) {
                if (i < 10) {i = "0" + i};  
                return i;
            }
        }, 1000);
    },
    init: function () {
        this.layout.handleLayout();
        this.setupAjaxHeaders();
        this.fixCrollbarModal();
        this.updateUserStatus();
        this.removeUser();
        this.updateProfileInformation();
        this.resetPassword();
        this.loadChartDashboard();
        this.loadLozad();
        this.handleRolePermission();
        this.createUserAccount();
        this.updateProfileAccount();
        this.reloadDatatable();
        this.showModalResetPassOnGird();
        this.removeItem();
        this.removeTokenPayment();
        this.approveCashout();
        this.handleCancelCashout();
    }
}

// Ready App
$(document).ready(function () {
    if (main_app.jBody.hasClass("loaded")) return;
    main_app.jBody.addClass("loaded");
    main_app.init();
});
