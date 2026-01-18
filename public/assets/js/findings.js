$(document).ready(function () {


    var table = $('#finding-dt').DataTable({

        responsive: true,
        stateSave: true,
        language: { url: '../assets/json/turkish.json' },
        dom: 'Bfrtip',
        dom: 'Blfrtip',
        lengthChange: true,
        lengthMenu: [10, 25, 50, 100],
        buttons: [{
            text: 'Kaydet',
            className: 'btn btn-light me-2',

            action: function (e, dt, node, config) {

                save_current_page();

            }
        },


            // $.extend(true, {}, buttonCommon, {
            //     text: "EK-C3 indir (Excel)",
            //     extend: "excel",
            //     title: 'kurum_adi' + '_Boşluk_Analizi_EK-C3'
            // }),
            // $.extend(true, {}, buttonCommon, {
            //     text: "EK-C3 indir (Pdf)",
            //     extend: "pdf",
            //     title: kurum_adi + '_Boşluk_Analizi_EK-C3',
            //     orientation: 'landscape',
            //     pageSize: 'LEGAL'
            // })

        ]



    });


    $('#finding-dt').on('click', '.deleteBTN', function () {


        id = $(this).attr('data');

        $.ajax({
            url: '/bulgu-guncelle', // Laravel route
            type: 'POST',
            data: { id: id },
            // processData: false,
            // contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (data) {

                console.log('Success:', data);
                Swal.fire({
                    icon: 'success',
                    title: 'Başarılı!',
                    text: 'Bulgu başarıyla silindi.'
                }).then((result) => {
                    if (result.value) {
                        window.location.reload();
                    }
                });

            },
            error: function (xhr, status, error) {
                console.error('Error:', error);

                Swal.fire({
                    icon: 'error',
                    title: 'Hata!',
                    html: errorMessage // HTML destekli hata mesajı
                });
            }

        });
    });

    $('#finding-dt').on('click', '.updateBTN', function () {

        id = $(this).attr('data');
        let subgroupID = $(this).attr('data2');
         $("#subgroupID").val(subgroupID);
        console.log("attr data "+id);
        $("#precautionID").val(id);


        let auditID = $("#audit_id").val();


        var formData = new FormData();
        formData.append('id', id);
        formData.append('auditID', auditID);
        formData.append('subgroupID', subgroupID);


        $.ajax({
            url: '/to-finding-modal', // Laravel route
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (data) {

                console.log('Success (UpdateBtn):', data);

                $('#findingModal').modal('show');

                let htmlContent = "";
                let questions = "";

                if (data.length == 0) {
                    htmlContent = 'Tedbir Bulunamadı'
                } else {

                    precaution = `<b>${data['precaution']}</b>`
                    data['question'].forEach(item => {

                        questions += `<li>${item}</li>`
                    });




                }


                $('.modal-body #precaution').html(precaution);
                $('.modal-body #collapseQuestion').html(`<ol class="list-group">${questions}</ul>`);
                $('#finding').val(data['finding']);
                $('#finding_criticality_level').val(data['level']);




            },
            error: function (xhr, status, error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Hata!',
                    text: 'Bir hata oluştu.'
                });

            }
        });
    });

    $('#finding-dt').on('click', '.showProofBTN', function () {

        let id = $(this).attr('data');

        let formNo = $(this).attr('data2');

        var formData = new FormData();
        formData.append('id', id);
        formData.append('formNo', formNo);
       

        $.ajax({
            url: '/show-files', // Laravel route
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (data) {
                $('#proofDocumentModal').modal('hide');
                console.log('Success (Güncelleme):', data);

                var htmlContent = "";

                if (data.length == 0) {
                    htmlContent = 'Henüz kanıtlayıcı belge eklenmedi!'
                } else {
                    data.forEach(function (fileName) {

                        var filePath = "/uploads/proof_documents/" + fileName;
                        htmlContent += '<a href="' + filePath + '" target="_blank">' + fileName + '</a><br>';


                    });
                }


                $('.modal-body #proofDocuments').html(htmlContent);



            },
            error: function (xhr, status, error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Hata!',
                    text: 'Bir hata oluştu.'
                });

            }
        });
        $('#proofDocumentShowModal').modal('show');
    });

    $("#finding_save_form").submit(function (e) {
        e.preventDefault();

        //precaution_id
        let id = $("#precautionID").val();
        let auditID = $("#audit_id").val();
        let subgroupID = $("#subgroupID").val();


        console.log($(this));
        var formData = new FormData(this);
        formData.append('id', id);
        formData.append('auditID', auditID);
        formData.append('subgroupID', subgroupID);

        $.ajax({

            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },

            url: '/save-finding',

            type: 'POST',

            data: formData,

            contentType: false,
            processData: false,

            success: function (response) {

                console.log(response);

                if (response.trim() == "1") {
                    Swal.fire({
                        icon: 'success',
                        title: 'Başarılı!',
                        text: 'İşlem başarıyla gerçekleşti.'
                    }).then((result) => {
                        if (result.value) {
                            window.location.reload();
                        }
                    });

                }
            }
        })
    });







});

