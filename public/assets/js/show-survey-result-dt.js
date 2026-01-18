$(document).ready(function () {

    var table = $('#show-survey-result-dt').DataTable({

        responsive: true,
        stateSave: true,
        language: { url: '/assets/json/turkish.json' },
        dom: 'Bfrtip',
        dom: 'Blfrtip',
        lengthChange: true,
        lengthMenu: [10, 25, 50, 100],
        buttons: [


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

});

function openAnswerAndDescriptionModal(sub_group_id, survey_id, participant_id) {
    $('#answerAndDescriptionModal').modal('show');

    //let sub_group_id=$(this).attr('sub_group_id');
    console.log(participant_id);



    $.ajax({
        url: '/show-answer-and-description', // Laravel route
        type: 'POST',
        data: {
            sub_group_id: sub_group_id,
            survey_id: survey_id,
            participant_id: participant_id
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
                table.append('<thead><tr class="border-bottom-primary"><th>#</th><th>Soru ve Cevabı</th><th>Açıklama</th></tr></thead><tbody>');

                $.each(data, function (i, item) {
                    var row = $('<tr class="border-bottom-success">').append(
                        $('<td>').html('<span class="me-4">' + (i + 1) + '</span>'),

                        $('<td class="p-2">').html('<strong>' + item.question + '</strong><br><span class="text-danger">' + item.answer + '</span>'),

                        $('<td>').html(item.description)
                    );
                    table.append(row);
                    table.append('</tbody>');
                });

                return table;
            }

            var table = generateTable(response);
            $('#answerAndOptionTable').html(table);
        }
    });
}



