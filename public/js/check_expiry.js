var exp = null;
$(function() {
    exp = {
        init() {
            // this.get_module_details();
            // this.get_exam_details();
        },
        get_module_details() {
            $.ajax({
                type: 'GET',
                url: '/admin/module_details/get'
            })
            .done((res) => {
                jQuery.each(res.module_details, function(i, val) {
                    if (val.end_date < exp.current_date() && val.is_opened == 0) {

                        exp.update_module_status(val.module_detail_id, 'unopened_ended');
                        exp.disabling_module(val.module_detail_id);
                    }
                    else if (val.end_date < exp.current_date() && val.is_opened == 1) {

                        exp.update_module_status(val.module_detail_id, 'opened_ended');
                        exp.disabling_module(val.module_detail_id);
                    }
                    else if (val.start_date <= exp.current_date() && val.is_opened == 1) {

                        exp.update_module_status(val.module_detail_id, 'opened');
                    }
                    else {

                        exp.update_module_status(val.module_detail_id, 'waiting');
                    }
                });
            })
            .fail((err) => {
                console.log(err['responseJSON']);
            });
        },
        // ---------------------------------------
        get_exam_details() {
            $.ajax({
                type: 'GET',
                url: '/admin/exam_details/get'
            })
            .done((res) => {
                jQuery.each(res.exam_details, function(i, val) {
                    if (val.date_available < exp.current_date()) exp.update_exam_status(val.exam_detail_id, 'ended');
                    else if (val.date_available == exp.current_date()) exp.update_exam_status(val.exam_detail_id, 'on progress');
                    else exp.update_exam_status(val.exam_detail_id, 'waiting');
                });
            })
            .fail((err) => {
                console.log(err['responseJSON']);
            });
        },

        // API for udpating each ModuleDetail's status
        update_module_status(module_detail_id, status) {
            $.ajax({
                type: 'PUT',
                url: '/admin/update_module_status/' + module_detail_id,
                data: { status: status }
            })
            .done((res) => {})
            .fail((err) => {
                console.log(err['responseJSON']);
            });
        },

        disabling_module(module_detail_id) {
            $.ajax({
                type: 'PUT',
                url: `/admin/module_details/disabling_module/${module_detail_id}`
            })
            .done((res) => {})
            .fail((err) => {
                console.log(err['responseJSON']);
            });
        },

        update_module_schedule_status(module_id, status) {
            $.ajax({
                type: 'PUT',
                url: `/admin/module_schedules/update_status/${module_id}`,
                data: { status: status }
            })
            .done((res) => {})
            .fail((err) => {
                console.log(err['responseJSON']);
            });
        },
        
        update_exam_status(exam_detail_id, status) {
            $.ajax({
                type: 'PUT',
                url: '/admin/update_exam_status/' + exam_detail_id,
                data: { status: status }
            })
            .done((res) => {})
            .fail((err) => {
                console.log(err['responseJSON']);
            });
        },

        current_date() {
            var today = new Date();
            var dd = today.getDate();
            var mm = today.getMonth()+1; //January is 0!
            var yyyy = today.getFullYear();
            if(dd<10) dd = '0'+dd
            if(mm<10) mm = '0'+mm
            today = yyyy + '-' + mm + '-' + dd;

            return today;
        }
    }
    exp.init();
});