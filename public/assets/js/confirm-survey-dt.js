$(document).ready(function () {

    var table = $('#confirm-survey-dt').DataTable({

        responsive: true,
        stateSave: true,
        language: { url: '/assets/json/turkish.json' },
        dom: 'Bfrtip',
        dom: 'Blfrtip',
        lengthChange: true,
        lengthMenu: [10, 25, 50, 100],
        buttons: [{
            text: 'Anketi Onayla',
            className: 'btn btn-success me-5 px-5 text-white',

            action: function (e, dt, node, config) {

                approveSurvey();

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

    //Anketi Onayla
    function approveSurvey() {

        //datatable tüm sayfalardaki verileri okuyor
        var rows = table.rows({
            page: 'all'
        }).nodes();

        var data_attr = [];
        var data2_attr = [];
        var surveyID=0;

        $(rows).each(function () {
            var rowData = table.row($(this)).data();

   

            //console.log($(rowData[0]).attr('data'));

            //inputun data attribute'nı aldık bu idlere göre verileri alacağız
            var id = $(rowData[3]).attr('data');
            data_attr.push(id);
            var id2 = $(rowData[3]).attr('data2');
            data2_attr.push(id2);
            surveyID=$(rowData[3]).attr('data3');


        })

        // console.log("data_attr " + data_attr);
        // console.log("data2_attr " + data2_attr);
        // console.log("surveyID " + surveyID);


        $.ajax({

            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },

            url: '/anket-onayla',
            type: 'POST',

            data: {
                subGroupIDs: data_attr,
                degreeOfCriticality: data2_attr,
                surveyID:surveyID

            },

            success: function (response) {

                console.log(response);

                if (response.trim() == "1") {
                    Swal.fire({
                        icon: 'success',
                        title: 'Başarılı!',
                        text: 'Kayıt başarıyla gerçekleşti.',
                        showConfirmButton: true
                    }).then(() => {
                        
                        window.location.href = '/anketler'; 
                    });
                }else{
                    Swal.fire({
                        icon: 'warning',
                        title: 'Uyarı!',
                        text: response + ' Adet cevaplamadığınız anket var. Lütfen tüm anketleri cevaplayınız',
                        showConfirmButton: true
                    })
                }


            },
            
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Hata!',
                    text: 'Kayıt sırasında bir hata oluştu.'
                });
            }
        });



    }


});

// function openAnswerAndDescriptionModal(sub_group_id, survey_id, participant_id) {
//     $('#answerAndDescriptionModal').modal('show');

//     //let sub_group_id=$(this).attr('sub_group_id');
//     console.log(participant_id);

//     $.ajax({
//         url: '/show-answer-and-description', // Laravel route
//         type: 'POST',
//         data: {
//             sub_group_id: sub_group_id,
//             survey_id: survey_id,
//             participant_id: participant_id
//         },
//         // processData: false,
//         // contentType: false,
//         headers: {
//             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//         },

//         success: function (response) {

//             console.log(response);

//             function generateTable(data) {
//                 var table = $('<table>').addClass('display');
//                 table.append('<thead><tr class="border-bottom-primary"><th>#</th><th>Soru ve Cevabı</th><th>Açıklama</th></tr></thead><tbody>');

//                 $.each(data, function (i, item) {
//                     var row = $('<tr class="border-bottom-success">').append(
//                         $('<td>').html('<span class="me-4">' + (i + 1) + '</span>'),

//                         $('<td class="p-2">').html('<strong>' + item.question + '</strong><br><span class="text-danger">' + item.answer + '</span>'),

//                         $('<td>').html(item.description)
//                     );
//                     table.append(row);
//                     table.append('</tbody>');
//                 });

//                 return table;
//             }

//             var table = generateTable(response);
//             $('#answerAndOptionTable').html(table);
//         }
//     });
// }

//anketi Cevapla

function openAnswerSurveyModal(sub_group_id, survey_id) {
   
    $('#answerSurveyModal').modal('show');
    $('#surveyID').val(survey_id);
    $('#subGroupID').val(sub_group_id);
    approvedByID = $('#approved_by_id').val();


    $.ajax({
        url: '/show-answer-survey', // Laravel route
        type: 'POST',
        data: {
            sub_group_id: sub_group_id,
            survey_id: survey_id,
            approvedByID: approvedByID,

        },
        // processData: false,
        // contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },

        success: function (response) {

            console.log(response);

            function generateTable(data) {

                var table = $('<table>').addClass('display');
                table.append('<thead><tr class="border-bottom-primary"><th>#</th><th>Sorular</th><th>Açıklama</th></tr></thead><tbody>');

                $.each(data, function (i, item) {


                    var optionHtml = "";
                    var number = parseInt(i) + 1;

                    //console.log(number);

                    $.each(item.options, function (key, value) {



                        if (item.optionsId[key] == item.answerOption) { checked = "checked"; } else { checked = "" }
                        optionHtml += `<input type="radio" name="option${number}" value="${item.optionsId[key]}" ${checked}> ${value}<br>`;

                    });

                    var row = $('<tr class="border-bottom-success">').append(


                        $('<td>').html('<span class="me-4">' + number + '</span>'),

                        $('<td class="p-3">').html('<strong>' + item.question + '</strong><br><br>' + optionHtml),

                        $('<td>').html('<textarea name="description' + number + '" rows="10">' + item.description + '</textarea>')
                    );
                    table.append(row);
                    table.append('</tbody>');
                    $('#subGroup').html('<h5>' + item.subGroup + '</h5>');

                });

                return table;
            }

            var table = generateTable(response);
            $('#answerSurveyTable').html(table);


        }
    });
}

$("#approved_survey_result_form").submit(function (e) {
    e.preventDefault();

    let approvedByID = $('#approved_by_id').val();


    var formData = new FormData(this);
    formData.append('approvedByID', approvedByID);


    //console.log(formData);

    $.ajax({

        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },

        url: '/save-approved_survey_result',

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
                }).then(function(){
                    window.location.reload();
                })

                $('#answerSurveyModal').modal('hide');

            }

        },
        error: function (xhr) {


            swal.fire({
                title: 'Uyarı!',
                icon: 'warning',
                text: 'Lütfen tüm alanları doldurduğunuzdan emin olun!'
            });



        }
    })

});



