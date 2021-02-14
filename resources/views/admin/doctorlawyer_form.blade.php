@extends('admin.app')

@section('title' , __('messages.add_new_doctors&lawyers'))
@push('styles')
<style>
    .wizard > .content > .body .select2-search input {
        border : none
    }
    input[disabled] {
        background-color: #eeeeee !important;
    }
    input[name="final_price[]"],
    input[name="total_amount[]"],
    input[name="remaining_amount[]"],
    input[name="barcodes[]"],
    input[name="stored_numbers[]"],
    input[disabled] {
        font-size: 10px
    }
    #properties-items .col-sm-5 {
        margin-bottom: 20px
    }
    .time-range,
    .wizard > .content > .body input[type="checkbox"],
    .add-range {
        display: none
    }
    .addtime,
    .deletetime {
        font-size: 50px;
        cursor: pointer;
    }
    .time-range .col-lg-3
    {
        margin-bottom: 20px
    }
</style>
    
@endpush
@push('scripts')
<script>
    var previous = "{{ __('messages.previous') }}",
        next = "{{ __('messages.next') }}",
        finish = "{{ __('messages.finish') }}"

    // translate three buttons
    $(".actions ul").find('li').eq(0).children('a').text(previous)
    $(".actions ul").find('li').eq(1).children('a').text(next)
    $(".actions ul").find('li').eq(2).children('a').text(finish)

    var ss = $(".tags").select2({
        tags: true,
    });
    var f4 = flatpickr($(".flatpickr"), {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        defaultDate: "13:45"
    });
    var lang = "{{ Config::get('app.locale') }}",
            select = "{{ __('messages.select') }}"
    $("#typeSelect").on('change', function() {
        var type = $(this).val()
        $("#categorySelect").parent(".form-group").show()
        $("#categorySelect").html("")
        $.ajax({
            url : "/admin-panel/services/fetchcategoriesbytype/" + type,
            type : 'GET',
            success : function (data) {
                $("#categorySelect").append(`
                        <option disabled selected>${select}</option>
                        `)
                data.forEach(function (row) {
                    var title = row.title_en
                    if (lang == 'en') {
                        title = row.title_ar
                    }
                    $("#categorySelect").append(`
                    <option value="${row.id}">${title}</option>
                    `)
                })
            }
        })
    })

    $("#categorySelect").on("change", function() {
        var category = $(this).val()
        
        $("#servicesSelect").parent(".form-group").show()
        $("#servicesSelect").html("")

        $.ajax({
            url : "/admin-panel/doctorslawyers/fetchservicesbycategory/" + category,
            type : 'GET',
            success : function (data) {
                
                data.forEach(function (row) {
                    var title = row.title_en
                    if (lang == 'ar') {
                        title = row.title_ar
                    }
                    $("#servicesSelect").append(`
                    <option value="${row.id}">${title}</option>
                    `)
                })
            }
        })

    })

    $(".day_work").change(function(){
        var day = $(this).data('day'),
            reservationType = $("#reservation_type").val()
            
            console.log(reservationType)
        if($(this).is(':checked')){
            $("." + day).css({"display" : "flex"})
            if (reservationType == "attendance") {
                $(this).parents(".row").next().find('.count').show()
            }else {
                $(this).parents(".row").next().find('.count').hide()
            }
        }else{
            $("." + day).css({"display" : "none"})
        }
    })

    
    $(".time-range").on("click", '.addtime', function() {
        $(this).hide()
        $(this).after(`
        <a class="deletetime">x</a>
        `)
        var cloned = $(this).parent(".time-range").clone(true).find("input").val("").end()
        $(this).show()
        $(this).next('.deletetime').hide()

        $(this).parent(".time-range").after(cloned)
    })

    $(".time-range").on("click", ".deletetime", function() {
        $(this).parent(".time-range").remove()
    })


    // phone existance validation
    $("input[name='phone']").on("keyup", function() {
        var inputVal = $(this).val()
        $.ajax({
            url: "/admin-panel/doctorslawyers/checkphoneexist/" + inputVal + "/" + 1
        }).done(function(data) {
            var required = "{{ __('messages.phone_exist') }}"
            if (data == 1) {
                $("input[name='phone']").addClass("prevent")
                if ($(".phone_required").length) {
                    
                }else {
                    $("input[name='phone']").after(`
                    <div style="margin-top:20px" class="alert alert-outline-danger mb-4 phone_required" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close" data-dismiss="alert"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button><i class="flaticon-cancel-12 close" data-dismiss="alert"></i> ${required}</div>
                    `)
                }
                
            }else {
                $("input[name='phone']").removeClass("prevent")
                $(".phone_required").remove()
            }
        })
    })

    $("input[name='recieving_reservation_phone']").on("keyup", function() {
        var inputVal = $(this).val()
        $.ajax({
            url: "/admin-panel/doctorslawyers/checkphoneexist/" + inputVal + "/" + 2
        }).done(function(data) {
            var required = "{{ __('messages.phone_exist') }}"
            if (data == 1) {
                $("input[name='recieving_reservation_phone']").addClass("prevent")
                if ($(".recieving_reservation_phone_required").length) {
                    
                }else {
                    $("input[name='recieving_reservation_phone']").after(`
                    <div style="margin-top:20px" class="alert alert-outline-danger mb-4 recieving_reservation_phone_required" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close" data-dismiss="alert"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button><i class="flaticon-cancel-12 close" data-dismiss="alert"></i> ${required}</div>
                    `)
                }
                
            }else {
                $("input[name='recieving_reservation_phone']").removeClass("prevent")
                $(".recieving_reservation_phone_required").remove()
            }
        })
    })


    /*
    * validation
    */
    // section 1
    if ($(".steps ul").find("li").eq(0).hasClass("current")) {
        $(".actions ul").find('li').eq(1).on("mouseover", "a", function() {
            var personal_image = $("input[name='personal_image']").val(),
                first_name = $("input[name='first_name']").val(),
                last_name = $("input[name='last_name']").val(),
                app_name_en = $("input[name='app_name_en']").val(),
                app_name_ar = $("input[name='app_name_ar']").val(),
                password = $("input[name='password']").val(),
                type = $("select[name='type']").val(),
                email = $("input[name='email']").val(),
                phone = $("input[name='phone']").val(),
                category_id = $("select[name='category_id']").val(),
                gender = $('input[name="gender"]:checked').val()
                

            if (gender == 0 || gender == 1) {
                console.log("test")
                console.log(gender)
            }
            
            
            if ( (personal_image.length > 0 
            && first_name.length > 0 
            && last_name.length > 0 
            && app_name_en.length > 0
            && app_name_ar.length > 0
            && password.length > 0
            && type
            && email.length > 0
            && phone.length > 0
            && category_id)
            && $("input[name='phone']").hasClass('prevent') == false
            && (gender == 0 || gender == 1) ) {
                $(this).attr('href', '#next')
            }else {
                $(this).attr('href', '#')
            }
        })
        
        $("input[name='personal_image']").on("change", function() {
            var personal_image_required = "{{ __('messages.personal_image_required') }}",
                personal_image = $("input[name='personal_image']").val()
            
            // personal_image
            if (personal_image.length == 0) {
                
                if ($(".personal_image_required").length) {
                    
                }else {
                    $("input[name='personal_image']").after(`
                    <div style="margin-top:20px" class="alert alert-outline-danger mb-4 personal_image_required" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close" data-dismiss="alert"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button><i class="flaticon-cancel-12 close" data-dismiss="alert"></i> ${personal_image_required}</div>
                    `)
                }
            }else {
                $(".personal_image_required").remove()
            }
        })

        $("input[name='image_professional_title']").on("change", function() {
            var image_professional_title_required = "{{ __('messages.image_professional_title_required') }}",
                image_professional_title = $("input[name='image_professional_title']").val()
            
            // personal_image
            if (image_professional_title.length == 0) {
                
                if ($(".image_professional_title_required").length) {
                    
                }else {
                    $("input[name='image_professional_title']").after(`
                    <div style="margin-top:20px" class="alert alert-outline-danger mb-4 image_professional_title_required" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close" data-dismiss="alert"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button><i class="flaticon-cancel-12 close" data-dismiss="alert"></i> ${image_professional_title_required}</div>
                    `)
                }
            }else {
                $(".image_professional_title_required").remove()
            }
        })

        $("input[name='image_profession_license']").on("change", function() {
            var image_profession_license_required = "{{ __('messages.image_profession_license_required') }}",
                image_profession_license = $("input[name='image_profession_license']").val()
            
            // personal_image
            if (image_profession_license.length == 0) {
                
                if ($(".image_profession_license_required").length) {
                    
                }else {
                    $("input[name='image_profession_license']").after(`
                    <div style="margin-top:20px" class="alert alert-outline-danger mb-4 image_profession_license_required" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close" data-dismiss="alert"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button><i class="flaticon-cancel-12 close" data-dismiss="alert"></i> ${image_profession_license_required}</div>
                    `)
                }
            }else {
                $(".image_profession_license_required").remove()
            }
        })

        // inputs validation
        $("input").each(function() {
            var ele = $(this).attr('name')

            $("input[name='" + ele + "']").on("keyup", function() {
                
                var required =  "",
                    input = $(this).val()

                
                switch (ele) {
                    case "first_name":
                    required = "{{ __('messages.first_name_required') }}";
                    break;

                    case "last_name":
                    required = "{{ __('messages.last_name_required') }}"
                    break;

                    case "app_name_en":
                    required = "{{ __('messages.app_name_en_required') }}"
                    break;

                    case "app_name_ar":
                    required = "{{ __('messages.app_name_ar_required') }}"
                    break;

                    case "password":
                    required = "{{ __('messages.password_required') }}"
                    break;

                    case "email":
                    required = "{{ __('messages.email_required') }}"
                    break;

                    case "phone":
                    required = "{{ __('messages.phone_required') }}"
                    break;

                    case "gender":
                    required = "{{ __('messages.gender_required') }}"
                    break;

                    case "professional_title_en":
                    required = "{{ __('messages.professional_title_en_required') }}";
                    break;

                    case "professional_title_ar":
                    required = "{{ __('messages.professional_title_ar_required') }}"
                    break;

                    case "city_en":
                    required = "{{ __('messages.city_en_required') }}"
                    break;

                    case "city_ar":
                    required = "{{ __('messages.city_ar_required') }}"
                    break;

                    case "address_en":
                    required = "{{ __('messages.address_en_required') }}"
                    break;

                    case "address_ar":
                    required = "{{ __('messages.address_ar_required') }}"
                    break;

                    case "location_link":
                    required = "{{ __('messages.location_link_required') }}"
                    break;

                    case "recieving_reservation_phone":
                    required = "{{ __('messages.recieving_reservation_phone_required') }}"
                    break;

                    case "reservation_cost":
                    required = "{{ __('messages.reservation_cost_required') }}"
                    break;
                }
                
                if (input.length == 0) {
                    
                    if ($("." + ele + "_required").length) {
                        
                    }else {
                        $("input[name='" + ele + "']").after(`
                        <div style="margin-top:20px" class="alert alert-outline-danger mb-4 ${ele}_required" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close" data-dismiss="alert"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button><i class="flaticon-cancel-12 close" data-dismiss="alert"></i> ${required}</div>
                        `)
                    }
                }else {
                    console.log("remove")
                    $("." + ele + "_required").remove()
                }
            })
        })

        // gender validation
        {{-- if ($('input[name="gender"]:checked') == 0 || $('input[name="gender"]:checked') == 1) {
            var genderRequired = "{{ __('messages.gender_required') }}"

            if ($(".gender_required").length) {
                        
            }else {
                $('input[name="gender"]:checked').parent('.new-control').parent('.n-chk').parent('.col-md-3').parent('.form-group').after(`
                <div style="margin-top:20px" class="alert alert-outline-danger mb-4 gender_required" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close" data-dismiss="alert"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button><i class="flaticon-cancel-12 close" data-dismiss="alert"></i> ${genderRequired}</div>
                `)
            }
        }else {
            $(".gender_required").remove()
        } --}}

        // select validation
        $("select").each(function() {
            var ele = $(this).attr('name')

            $("select[name='" + ele + "']").on("change", function() {
                var required =  "",
                    input = $("select[name='" + ele + "']").val()

                switch (ele) {
                    case "type":
                    required = "{{ __('messages.type_required') }}"
                    break;
    
                    case "category_id":
                    required = "{{ __('messages.category_id_required') }}"
                    break;

                    case "reservation_type":
                    required = "{{ __('messages.reservation_type_required') }}"
                    break;
                }
            })
            
        })

        // textarea validation
        $("textarea").each(function() {
            var ele = $(this).attr('name')
            
            $("textarea[name='" + ele + "']").on("keyup", function() {
                
                var required =  "",
                    input = $("textarea[name='" + ele + "']").val()

                
                switch (ele) {
                    case "about_en":
                    required = "{{ __('messages.about_en_required') }}"
                    break;

                    case "about_ar":
                    required = "{{ __('messages.about_ar_required') }}"
                    break;
                }

                
                if (input.length == 0) {
                    if ($("." + ele + "_required").length) {
                        
                    }else {
                        $("textarea[name='" + ele + "']").after(`
                        <div style="margin-top:20px" class="alert alert-outline-danger mb-4 ${ele}_required" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close" data-dismiss="alert"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button><i class="flaticon-cancel-12 close" data-dismiss="alert"></i> ${required}</div>
                        `)
                    }
                }else {
                    $("." + ele + "_required").remove()
                }
            })
        })
        
    }

    
    $(".actions ul").find('li').eq(1).on("mouseover", "a", function() {
        // section 2
        if ($(".steps ul").find("li").eq(1).hasClass("current")) {
            
            var image_professional_title = $("input[name='image_professional_title']").val(),
                image_profession_license = $("input[name='image_profession_license']").val(),
                professional_title_en = $("input[name='professional_title_en']").val(),
                professional_title_ar = $("input[name='professional_title_ar']").val(),
                service_id = $("select[name='service_id[]']").val(),
                about_en = $("textarea[name='about_en']").val(),
                about_ar = $("textarea[name='about_ar']").val()

            
            if ( image_professional_title.length > 0 
            && image_profession_license.length > 0 
            && professional_title_en.length > 0 
            && professional_title_ar.length > 0
            && service_id.length > 0
            && about_en.length > 0
            && about_ar.length > 0
            ) {
                $(this).attr('href', '#next')
            }else {
                $(this).attr('href', '#')
            }

        }

        // section 3
        if ($(".steps ul").find("li").eq(2).hasClass("current")) {
            var city_en = $("input[name='city_en']").val(),
                city_ar = $("input[name='city_ar']").val(),
                address_en = $("input[name='address_en']").val(),
                address_ar = $("input[name='address_ar']").val(),
                location_link = $("input[name='location_link']").val()

            
            if ( city_en.length > 0 
            && city_ar.length > 0 
            && address_en.length > 0 
            && address_ar.length > 0
            && location_link.length > 0
            ) {
                $(this).attr('href', '#next')
            }else {
                $(this).attr('href', '#')
            }

        }

        // section 4
        if ($(".steps ul").find("li").eq(3).hasClass("current")) {
            console.log("section 4")
            var reservation_type = $("select[name='reservation_type']").val(),
                recieving_reservation_phone = $("input[name='recieving_reservation_phone']").val(),
                reservation_cost = $("input[name='reservation_cost']").val()

            
            if ( (reservation_type && reservation_type.length > 0)
            && $("input[name='recieving_reservation_phone']").hasClass('prevent') == false
            && reservation_cost.length > 0
            ) {
                $(this).attr('href', '#next')
            }else {
                $(this).attr('href', '#')
            }
        }
    })

    $(".actions ul").find('li').eq(1).on("click", "a", function() {
        
        // section 2
        if ($(".steps ul").find("li").eq(0).hasClass("current")) {
            var personal_image_required = "{{ __('messages.personal_image_required') }}",
                personal_image = $("input[name='personal_image']").val()
                
            // personal_image
            if (personal_image.length == 0) {
                
                if ($(".personal_image_required").length) {
                    
                }else {
                    $("input[name='personal_image']").after(`
                    <div style="margin-top:20px" class="alert alert-outline-danger mb-4 personal_image_required" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close" data-dismiss="alert"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button><i class="flaticon-cancel-12 close" data-dismiss="alert"></i> ${personal_image_required}</div>
                    `)
                }
            }else {
                $(".personal_image_required").remove()
            }


            // inputs validation
            $("section").eq(0).find('input').each(function() {
                var ele = $(this).attr('name')

                
                var required =  "",
                    input = $(this).val()

                switch (ele) {
                    case "first_name":
                    required = "{{ __('messages.first_name_required') }}";
                    break;

                    case "last_name":
                    required = "{{ __('messages.last_name_required') }}"
                    break;

                    case "app_name_en":
                    required = "{{ __('messages.app_name_en_required') }}"
                    break;

                    case "app_name_ar":
                    required = "{{ __('messages.app_name_ar_required') }}"
                    break;

                    case "password":
                    required = "{{ __('messages.password_required') }}"
                    break;

                    case "email":
                    required = "{{ __('messages.email_required') }}"
                    break;

                    case "phone":
                    required = "{{ __('messages.phone_required') }}"
                    break;

                    case "gender":
                    required = "{{ __('messages.gender_required') }}"
                    break;

                }
                
                if (input.length == 0) {
                    console.log("in")
                    
                    if ($("." + ele + "_required").length) {
                        
                    }else {
                        $("input[name='" + ele + "']").after(`
                        <div style="margin-top:20px" class="alert alert-outline-danger mb-4 ${ele}_required" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close" data-dismiss="alert"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button><i class="flaticon-cancel-12 close" data-dismiss="alert"></i> ${required}</div>
                        `)
                    }
                }else {
                    console.log("remove")
                    $("." + ele + "_required").remove()
                }
                
            })

        }

        // section 2
        if ($(".steps ul").find("li").eq(1).hasClass("current")) {
            var image_professional_title_required = "{{ __('messages.image_professional_title_required') }}",
                image_professional_title = $("input[name='image_professional_title']").val(),
                image_profession_license_required = "{{ __('messages.image_profession_license_required') }}",
                image_profession_license = $("input[name='image_profession_license']").val()
            
            
            // personal_image
            if (image_professional_title.length == 0) {
                
                if ($(".image_professional_title_required").length) {
                    
                }else {
                    $("input[name='image_professional_title']").after(`
                    <div style="margin-top:20px" class="alert alert-outline-danger mb-4 image_professional_title_required" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close" data-dismiss="alert"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button><i class="flaticon-cancel-12 close" data-dismiss="alert"></i> ${image_professional_title_required}</div>
                    `)
                }
            }else {
                $(".image_professional_title_required").remove()
            }
        
            
            // personal_image
            if (image_profession_license.length == 0) {
                
                if ($(".image_profession_license_required").length) {
                    
                }else {
                    $("input[name='image_profession_license']").after(`
                    <div style="margin-top:20px" class="alert alert-outline-danger mb-4 image_profession_license_required" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close" data-dismiss="alert"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button><i class="flaticon-cancel-12 close" data-dismiss="alert"></i> ${image_profession_license_required}</div>
                    `)
                }
            }else {
                $(".image_profession_license_required").remove()
            }

            // inputs validation
            $("section").eq(1).find('input').each(function() {
                var ele = $(this).attr('name')

                
                var required =  "",
                    input = $(this).val()

                switch (ele) {
                    case "professional_title_en":
                    required = "{{ __('messages.professional_title_en_required') }}";
                    break;

                    case "professional_title_ar":
                    required = "{{ __('messages.professional_title_ar_required') }}"
                    break;

                }
                
                
                if (input.length == 0) {
                    console.log("in")
                    
                    if ($("." + ele + "_required").length) {
                        
                    }else {
                        $("input[name='" + ele + "']").after(`
                        <div style="margin-top:20px" class="alert alert-outline-danger mb-4 ${ele}_required" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close" data-dismiss="alert"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button><i class="flaticon-cancel-12 close" data-dismiss="alert"></i> ${required}</div>
                        `)
                    }
                }else {
                    console.log("remove")
                    $("." + ele + "_required").remove()
                }
                
                
            })

            // textarea validation
            $("section").eq(1).find('textarea').each(function() {
                var ele = $(this).attr('name')  
                var required =  "",
                    input = $("textarea[name='" + ele + "']").val()

                
                switch (ele) {
                    case "about_en":
                    required = "{{ __('messages.about_en_required') }}"
                    break;

                    case "about_ar":
                    required = "{{ __('messages.about_ar_required') }}"
                    break;
                }

                
                if (input.length == 0) {
                    if ($("." + ele + "_required").length) {
                        
                    }else {
                        $("textarea[name='" + ele + "']").after(`
                        <div style="margin-top:20px" class="alert alert-outline-danger mb-4 ${ele}_required" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close" data-dismiss="alert"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button><i class="flaticon-cancel-12 close" data-dismiss="alert"></i> ${required}</div>
                        `)
                    }
                }else {
                    $("." + ele + "_required").remove()
                }
                
            })
        }

        // section 3
        if ($(".steps ul").find("li").eq(2).hasClass("current")) {
            
            // inputs validation
            $("section").eq(2).find('input').each(function() {
                
                var ele = $(this).attr('name')
 
                var required =  "",
                    input = $(this).val()

                
                switch (ele) {

                    case "city_en":
                    required = "{{ __('messages.city_en_required') }}"
                    break;

                    case "city_ar":
                    required = "{{ __('messages.city_ar_required') }}"
                    break;

                    case "address_en":
                    required = "{{ __('messages.address_en_required') }}"
                    break;

                    case "address_ar":
                    required = "{{ __('messages.address_ar_required') }}"
                    break;

                    case "location_link":
                    required = "{{ __('messages.location_link_required') }}"
                    break;
                }
                
                if (input.length == 0) {
                    console.log("in")
                    
                    if ($("." + ele + "_required").length) {
                        
                    }else {
                        $("input[name='" + ele + "']").after(`
                        <div style="margin-top:20px" class="alert alert-outline-danger mb-4 ${ele}_required" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close" data-dismiss="alert"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button><i class="flaticon-cancel-12 close" data-dismiss="alert"></i> ${required}</div>
                        `)
                    }
                }else {
                    console.log("remove")
                    $("." + ele + "_required").remove()
                }
                
            })
        }

        // section 4
        if ($(".steps ul").find("li").eq(3).hasClass("current")) {
            
            // inputs validation
            $("section").eq(3).find('input').each(function() {
                var ele = $(this).attr('name')
 
                var required =  "",
                    input = $(this).val()

                
                switch (ele) {

                    case "recieving_reservation_phone":
                    required = "{{ __('messages.recieving_reservation_phone_required') }}"
                    break;

                    case "reservation_cost":
                    required = "{{ __('messages.reservation_cost_required') }}"
                    break;
                }
                
                if (input.length == 0) {
                    
                    if ($("." + ele + "_required").length) {
                        
                    }else {
                        $("input[name='" + ele + "']").after(`
                        <div style="margin-top:20px" class="alert alert-outline-danger mb-4 ${ele}_required" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close" data-dismiss="alert"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button><i class="flaticon-cancel-12 close" data-dismiss="alert"></i> ${required}</div>
                        `)
                    }
                }else {
                    console.log("remove")
                    $("." + ele + "_required").remove()
                }
            })

        }

    })
    

    // submit form on click finish
    $(".actions ul").find('li').eq(2).on("click", 'a[href="#finish"]', function () {
        $("form").submit()
    })
</script>
    
@endpush

@section('content')
    <div class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>{{ __('messages.add_new_doctors&lawyers') }}</h4>
                 </div>
        </div>
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="list-unstyled mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="" method="post" enctype="multipart/form-data" >
            @csrf
            <div class="statbox widget box box-shadow">
                <div class="widget-content widget-content-area">
                    <div id="circle-basic" class="">
                        <h3>{{ __('messages.personal_data') }}</h3>
                        <section>
                            
                            <div class="custom-file-container" data-upload-id="myThirdImage">
                                <label>{{ __('messages.upload') }} ({{ __('messages.personal_image') }}) <a href="javascript:void(0)" class="custom-file-container__image-clear" title="Clear Image">x</a></label>
                                <label class="custom-file-container__custom-file" >
                                    <input type="file" required name="personal_image" class="custom-file-container__custom-file__custom-file-input" accept="image/*">
                                    <input type="hidden" name="MAX_FILE_SIZE" value="10485760" />
                                    <span class="custom-file-container__custom-file__custom-file-control"></span>
                                </label>
                                <div class="custom-file-container__image-preview"></div>
                            </div>
                            <div class="form-group mb-4">
                                <label for="first_name">{{ __('messages.first_name') }}</label>
                                <input required type="text" name="first_name" class="form-control" id="first_name" placeholder="{{ __('messages.first_name') }}" value="" >
                            </div>
                            <div class="form-group mb-4">
                                <label for="last_name">{{ __('messages.last_name') }}</label>
                                <input required type="text" name="last_name" class="form-control" id="last_name" placeholder="{{ __('messages.last_name') }}" value="" >
                            </div>
                            <div class="form-group mb-4">
                                <label for="app_name_en">{{ __('messages.app_name_en') }}</label>
                                <input required type="text" name="app_name_en" class="form-control" id="app_name_en" placeholder="{{ __('messages.app_name_en') }}" value="" >
                            </div>
                            <div class="form-group mb-4">
                                <label for="app_name_ar">{{ __('messages.app_name_ar') }}</label>
                                <input required type="text" name="app_name_ar" class="form-control" id="app_name_ar" placeholder="{{ __('messages.app_name_ar') }}" value="" >
                            </div>
                            <div class="form-group mb-4">
                                <label for="password">{{ __('messages.password') }}</label>
                                <input required type="password" name="password" class="form-control" id="password" placeholder="{{ __('messages.password') }}" value="" >
                            </div>
                            <div class="form-group mb-4">
                                <label for="type">{{ __('messages.type') }}</label>
                                <select id="typeSelect" required name="type" class="form-control" >
                                    <option selected disabled >{{ __('messages.select') }}</option>
                                    <option value="doctor" >{{ __('messages.doctor') }}</option>
                                    <option value="lawyer" >{{ __('messages.lawyer') }}</option>
                                </select>
                            </div>
                            <div style="display: none" class="form-group mb-4">
                                <label for="type">{{ __('messages.category') }}</label>
                                <select id="categorySelect" required name="category_id" class="form-control" >
                                    <option selected disabled >{{ __('messages.select') }}</option>
                                </select>
                            </div> 
                            <div class="form-group mb-4">
                                <label for="email">{{ __('messages.email') }}</label>
                                <input required type="text" name="email" class="form-control" id="email" placeholder="{{ __('messages.email') }}" value="" >
                            </div>
                            <div class="form-group mb-4">
                                <label for="phone">{{ __('messages.phone') }}</label>
                                <input required type="text" name="phone" class="form-control" id="phone" placeholder="{{ __('messages.phone') }}" value="" >
                            </div>
                            <div class="form-group mb-4">
                                <div class="col-12" >
                                    <label> {{ __('messages.gender') }} </label>
                                </div>
                                <div class="col-md-3" >
                                        <div class="n-chk">
                                        <label class="new-control new-checkbox new-checkbox-text checkbox-primary">
                                            <input  type="radio" name="gender" value="1" class="new-control-input all-permisssions">
                                            <span class="new-control-indicator"></span><span class="new-chk-content">{{ __('messages.male') }}</span>
                                        </label>
                                    </div>     
                                </div>
                                <div class="col-md-3" >
                                    <div class="n-chk">
                                        <label class="new-control new-checkbox new-checkbox-text checkbox-primary">
                                            <input  type="radio" name="gender" value="0" class="new-control-input all-permisssions">
                                            <span class="new-control-indicator"></span><span class="new-chk-content">{{ __('messages.female') }}</span>
                                        </label>
                                    </div>     
                                </div>
                            </div>
                            
                        </section>
                        <h3>{{ __('messages.professional_data') }}</h3>
                        <section>
                            <div class="custom-file-container" data-upload-id="myFirstImage">
                                <label>{{ __('messages.upload') }} ({{ __('messages.image_professional_title') }}) <a href="javascript:void(0)" class="custom-file-container__image-clear" title="Clear Image">x</a></label>
                                <label class="custom-file-container__custom-file" >
                                    <input type="file" required name="image_professional_title" class="custom-file-container__custom-file__custom-file-input" accept="image/*">
                                    <input type="hidden" name="MAX_FILE_SIZE" value="10485760" />
                                    <span class="custom-file-container__custom-file__custom-file-control"></span>
                                </label>
                                <div class="custom-file-container__image-preview"></div>
                            </div>
                
                            <div class="custom-file-container" data-upload-id="mySecondImage">
                                <label>{{ __('messages.upload') }} ({{ __('messages.image_profession_license') }}) <a href="javascript:void(0)" class="custom-file-container__image-clear" title="Clear Image">x</a></label>
                                <label class="custom-file-container__custom-file" >
                                    <input type="file" required name="image_profession_license" class="custom-file-container__custom-file__custom-file-input" accept="image/*">
                                    <input type="hidden" name="MAX_FILE_SIZE" value="10485760" />
                                    <span class="custom-file-container__custom-file__custom-file-control"></span>
                                </label>
                                <div class="custom-file-container__image-preview"></div>
                            </div>
                            <div class="form-group mb-4">
                                <label for="professional_title_en">{{ __('messages.professional_title_en') }}</label>
                                <input required type="text" name="professional_title_en" class="form-control" id="professional_title_en" placeholder="{{ __('messages.professional_title_en') }}" value="" >
                            </div>
                            <div class="form-group mb-4">
                                <label for="professional_title_ar">{{ __('messages.professional_title_ar') }}</label>
                                <input required type="text" name="professional_title_ar" class="form-control" id="professional_title_ar" placeholder="{{ __('messages.professional_title_ar') }}" value="" >
                            </div>
                            <div style="display: none" class="form-group" >
                                <div class="col-12" >
                                    <label> {{ __('messages.services') }} </label>
                                </div>
                                <select id="servicesSelect" name="service_id[]" class="form-control tags" multiple="multiple">
                                
                                </select>
                            </div>
                            
                            <div class="form-group mb-4 arabic-direction">
                                <label for="about_en">{{ __('messages.about_en') }}</label>
                                <textarea id="about_en" required name="about_en" class="form-control" rows="5"></textarea>
                            </div>
                            <div class="form-group mb-4 arabic-direction">
                                <label for="about_ar">{{ __('messages.about_ar') }}</label>
                                <textarea id="about_ar" required name="about_ar" class="form-control" rows="5"></textarea>
                            </div>

                        </section>
                        <h3>{{ __('messages.location') }}</h3>
                        <section>
                            <div class="form-group mb-4">
                                <label for="city_en">{{ __('messages.city_en') }}</label>
                                <input required type="text" name="city_en" class="form-control" id="city_en" placeholder="{{ __('messages.city_en') }}" value="" >
                            </div>
                            <div class="form-group mb-4">
                                <label for="city_ar">{{ __('messages.city_ar') }}</label>
                                <input required type="text" name="city_ar" class="form-control" id="city_ar" placeholder="{{ __('messages.city_ar') }}" value="" >
                            </div>
                            <div class="form-group mb-4">
                                <label for="address_en">{{ __('messages.address_en') }}</label>
                                <input required type="text" name="address_en" class="form-control" id="address_en" placeholder="{{ __('messages.address_en') }}" value="" >
                            </div>
                            <div class="form-group mb-4">
                                <label for="address_ar">{{ __('messages.address_ar') }}</label>
                                <input required type="text" name="address_ar" class="form-control" id="address_ar" placeholder="{{ __('messages.address_ar') }}" value="" >
                            </div>
                            <div class="form-group mb-4">
                                <label for="location_link">{{ __('messages.location_link') }}</label>
                                <input required type="text" name="location_link" class="form-control" id="location_link" placeholder="{{ __('messages.location_link') }}" value="" >
                            </div>
                            <div class="custom-file-container" data-upload-id="myFourthImage">
                                <label>{{ __('messages.upload') }} ({{ __('messages.place_images') }}) <a href="javascript:void(0)" class="custom-file-container__image-clear" title="Clear Image">x</a></label>
                                <label class="custom-file-container__custom-file" >
                                    <input type="file" required name="place_image[]" class="custom-file-container__custom-file__custom-file-input" multiple accept="image/*">
                                    <input type="hidden" name="MAX_FILE_SIZE" value="10485760" />
                                    <span class="custom-file-container__custom-file__custom-file-control"></span>
                                </label>
                                <div class="custom-file-container__image-preview"></div>
                            </div>
                        </section>
                        <h3>{{ __('messages.reservation_data') }}</h3>
                        <section>
                            <div class="form-group mb-4">
                                <label for="reservation_type">{{ __('messages.reservation_type') }}</label>
                                <select id="reservation_type" required name="reservation_type" class="form-control" >
                                    <option selected disabled >{{ __('messages.select') }}</option>
                                    <option value="attendance" >{{ __('messages.attendance') }}</option>
                                    <option value="intime" >{{ __('messages.intime') }}</option>
                                </select>
                            </div>
                            <div style="display: none" class="form-group mb-4">
                                <label for="visits_count">{{ __('messages.visits_count') }}</label>
                                <input required type="number" name="recieving_reservation_phone" class="form-control" id="visits_count" placeholder="{{ __('messages.visits_count') }}" value="" >
                            </div>
                            <div class="form-group mb-4">
                                <label for="recieving_reservation_phone">{{ __('messages.recieving_reservation_phone') }}</label>
                                <input required type="text" name="recieving_reservation_phone" class="form-control" id="recieving_reservation_phone" placeholder="{{ __('messages.recieving_reservation_phone') }}" value="" >
                            </div>
                            <div class="form-group mb-4">
                                <label for="reservation_cost">{{ __('messages.reservation_cost') }}</label>
                                <input required type="number" step="any" min="0" name="reservation_cost" class="form-control" id="reservation_cost" placeholder="{{ __('messages.reservation_cost') }}" value="" >
                            </div>
                        </section>
                        <h3>{{ __('messages.times_of_work') }}</h3>
                        <section>
                            {{-- sunday --}}
                            <div class="row" >
                                <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                                    {{ __('messages.sunday') }}
                                </div>    

                                <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                                    <label class="switch s-icons s-outline  s-outline-success  mr-2">
                                        <input data-day="sunday" class="day_work" name="sunday_work" type="checkbox" >
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                
                            {{-- sunday work time --}}
                            <div class="row time-range sunday" >
                                <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                                    <label for="sunday_from">{{ __('messages.from') }}</label>
                                    <input  type="time" name="sunday_from[]" class="form-control" id="sunday_from"  value="" >    
                                </div>    

                                <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                                    <label for="sunday_to">{{ __('messages.to') }}</label>
                                    <input  type="time" name="sunday_to[]" class="form-control" id="sunday_to"  value="" >    
                                </div>
                                <div style="display: none" class="col-lg-3 col-md-3 col-sm-4 col-6 count">
                                    <label for="">{{ __('messages.visits_count') }}</label>
                                    <input  type="number" name="sunday_count[]" class="form-control"  value="" >    
                                </div>
                                <a class="addtime">+</a>
                            </div>
                            
                
                
                
                            {{-- monday --}}
                            <div class="row" >
                                <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                                    {{ __('messages.monday') }}
                                </div>    

                                <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                                    <label class="switch s-icons s-outline  s-outline-success  mb-4 mr-2">
                                        <input data-day="monday" class="day_work" name="monday_work" type="checkbox" >
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                                
                            </div>
                
                            {{-- monday work time --}}
                            <div class="row time-range monday" >
                                <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                                    <label for="monday_from">{{ __('messages.from') }}</label>
                                    <input  type="time" name="monday_from[]" class="form-control"  value="" >    
                                </div>    

                                <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                                    <label for="monday_to">{{ __('messages.to') }}</label>
                                    <input  type="time" name="monday_to[]" class="form-control"  value="" >    
                                </div>
                                <div style="display: none" class="col-lg-3 col-md-3 col-sm-4 col-6 count">
                                    <label for="">{{ __('messages.visits_count') }}</label>
                                    <input  type="number" name="monday_count[]" class="form-control"  value="" >    
                                </div>
                                <a class="addtime">+</a>
                            </div>
                
                
                            {{-- tuesday --}}
                            <div class="row" >
                                <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                                    {{ __('messages.tuesday') }}
                                </div>    

                                <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                                    <label class="switch s-icons s-outline  s-outline-success  mb-4 mr-2">
                                        <input data-day="tuesday" class="day_work" name="tuesday_work" type="checkbox" >
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                
                
                            {{-- tuesday work time --}}
                            <div class="row time-range tuesday" >
                                <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                                    <label for="tuesday_from">{{ __('messages.from') }}</label>
                                    <input  type="time" name="tuesday_from[]" class="form-control"  value="" >    
                                </div>    

                                <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                                    <label for="tuesday_to">{{ __('messages.to') }}</label>
                                    <input  type="time" name="tuesday_to[]" class="form-control"  value="" >    
                                </div>
                                <div style="display: none" class="col-lg-3 col-md-3 col-sm-4 col-6 count">
                                    <label for="">{{ __('messages.visits_count') }}</label>
                                    <input  type="number" name="tuesday_count[]" class="form-control"  value="" >    
                                </div>
                                <a class="addtime">+</a>
                            </div>
                            
                
                            {{-- wednesday --}}
                            <div class="row" >
                                <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                                    {{ __('messages.wednesday') }}
                                </div>    

                                <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                                    <label class="switch s-icons s-outline  s-outline-success  mb-4 mr-2">
                                        <input data-day="wdnesday" class="day_work" name="wednesday_work" type="checkbox" >
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                
                
                            {{-- wednesday work time --}}
                            <div class="row time-range wdnesday" >
                                <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                                    <label for="wednesday_from">{{ __('messages.from') }}</label>
                                    <input   type="time" name="wednesday_from[]" class="form-control"  value="" >    
                                </div>    

                                <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                                    <label for="wednesday_to">{{ __('messages.to') }}</label>
                                    <input  type="time" name="wednesday_to[]" class="form-control"  value="" >    
                                </div>
                                <div style="display: none" class="col-lg-3 col-md-3 col-sm-4 col-6 count">
                                    <label for="">{{ __('messages.visits_count') }}</label>
                                    <input  type="number" name="wednesday_count[]" class="form-control"  value="" >    
                                </div>
                                <a class="addtime">+</a>
                            </div>
                            
                
                
                            {{-- thursday --}}
                            <div class="row" >
                                <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                                    {{ __('messages.thursday') }}
                                </div>    

                                <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                                    <label class="switch s-icons s-outline  s-outline-success  mb-4 mr-2">
                                        <input data-day="thrusday" class="day_work" name="thursday_work" type="checkbox" >
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                
                
                            {{-- thursday work time --}}
                            <div class="row time-range thrusday" >
                                <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                                    <label for="thursday_from">{{ __('messages.from') }}</label>
                                    <input  type="time" name="thursday_from[]" class="form-control"  value="" >    
                                </div>    

                                <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                                    <label for="thursday_to">{{ __('messages.to') }}</label>
                                    <input  type="time" name="thursday_to[]" class="form-control"  value="" >    
                                </div>
                                <div style="display: none" class="col-lg-3 col-md-3 col-sm-4 col-6 count">
                                    <label for="">{{ __('messages.visits_count') }}</label>
                                    <input  type="number" name="thursday_count[]" class="form-control"  value="" >    
                                </div>
                                <a class="addtime">+</a>
                            </div>
                            
                
                
                            {{-- friday --}}
                            <div class="row" >
                                <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                                    {{ __('messages.friday') }}
                                </div>    

                                <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                                    <label class="switch s-icons s-outline  s-outline-success  mb-4 mr-2">
                                        <input data-day="friday" class="day_work" name="friday_work" type="checkbox" >
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                
                
                            {{-- friday work time --}}
                            <div class="row time-range friday" >
                                <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                                    <label for="friday_from">{{ __('messages.from') }}</label>
                                    <input   type="time" name="friday_from[]" class="form-control"  value="" >    
                                </div>    

                                <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                                    <label for="friday_to">{{ __('messages.to') }}</label>
                                    <input   type="time" name="friday_to[]" class="form-control"  value="" >    
                                </div>
                                <div style="display: none" class="col-lg-3 col-md-3 col-sm-4 col-6 count">
                                    <label for="">{{ __('messages.visits_count') }}</label>
                                    <input  type="number" name="friday_count[]" class="form-control"  value="" >    
                                </div>
                                <a class="addtime">+</a>
                            </div>
                            
                
                
                            {{-- saturday --}}
                            <div class="row" >
                                <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                                    {{ __('messages.saturday') }}
                                </div>    

                                <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                                    <label class="switch s-icons s-outline  s-outline-success  mb-4 mr-2">
                                        <input data-day="saturday" class="day_work" name="saturday_work" type="checkbox" >
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                            
                
                            {{-- saturday work time --}}
                            <div class="row time-range saturday" >
                                <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                                    <label for="saturday_from">{{ __('messages.from') }}</label>
                                    <input   type="time" name="saturday_from[]" class="form-control"  value="" >    
                                </div>    

                                <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                                    <label for="saturday_to">{{ __('messages.to') }}</label>
                                    <input   type="time" name="saturday_to[]" class="form-control"  value="" >    
                                </div>
                                <div style="display: none" class="col-lg-3 col-md-3 col-sm-4 col-6 count">
                                    <label for="">{{ __('messages.visits_count') }}</label>
                                    <input  type="number" name="saturday_count[]" class="form-control"  value="" >    
                                </div>
                                <a class="addtime">+</a>
                            </div>
                            
                        </section>

                    </div>
        
                </div>
            </div>
            
        </form>
        
    </div>
@endsection