$(document).ready(function () {


    var table = $('#finding2-dt').DataTable({

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


    $('#finding2-dt').on('click', '.deleteBTN', function () {


        id = $(this).attr('data');

        $.ajax({
            url: '/bulgu-guncelle2', // Laravel route
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

    $('#finding2-dt').on('click', '.updateBTN', function () {

        id = $(this).attr('data');
        
        console.log("attr data "+id);

        $('#ID').val(id);

        questionID=$(this).attr('data-question-id');
        console.log("attr data-question "+questionID);


        let auditID = $("#audit_id").val();


        var formData = new FormData();
        formData.append('id', id);
        formData.append('auditID', auditID);
        formData.append('questionID', questionID);


        $.ajax({
            url: '/to-finding2-modal', // Laravel route
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (data) {

                console.log('Success (UpdateBtn):', data);

                $('#findingModal2').modal('show');

                let question = "";

                question = `<b>${data['question']}</b>`


                $('.modal-body #question').html(question);
                $('#finding').val(data['finding']);
                $('#finding_code').val(data['finding_code']);




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

    $("#finding2_save_form").submit(function (e) {
        e.preventDefault();

        
        let id = $("#ID").val();
        let auditID = $("#audit_id").val();


        console.log("id "+id);
        var formData = new FormData(this);
        formData.append('id', id);
        formData.append('auditID', auditID);
     

        $.ajax({

            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },

            url: '/save-finding2',

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

    $('#finding2-dt').on('click', '.showProofBTN', function () {

        let questionID = $(this).attr('data');

        let auditID = $(this).attr('data2');

        var formData = new FormData();
        formData.append('id', questionID);
        formData.append('auditID', auditID);
       

        $.ajax({
            url: '/show-files-aap', // Laravel route
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







});

