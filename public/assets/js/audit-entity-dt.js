$(document).ready(function () {


    //console.log($('#subGroupID').val());



    if ($('#audit-entity-dt').length) {
        var table = $('#audit-entity-dt').DataTable({

            responsive: true,
            stateSave: true,
            language: { url: '../assets/json/turkish.json' },
            dom: 'Bfrtip',
            dom: 'Blfrtip',
            lengthChange: true,
            lengthMenu: [10, 25, 50],
            buttons: [],


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





        });
    }

    //Denetim seçince aktif hale gelecek
    $('#save').hide();

    //Çalışma Formlarında Denetim değiştiği Zaman
    $("#audit_id").change(function (e) {

       

        let auditID = e.target.value;

        if (auditID==99){
            $('#save').hide();
        }else{
            $('#save').show();
        }

        $('#checkbox_2').prop("checked", true);

        console.log(auditID);

        $.ajax({
            type: "get",
            url: `/denetime-atanan-varliklar/${auditID}`,
            dataType: "json",
            success: function (data) {

                console.log(data)

                $('input[type="checkbox"]').prop('checked', false);
                data.forEach(function (id) {

                    // checkbox id'sini oluşturuyoruz
                    var checkboxId = "#checkbox_" + id;

                    // checkbox'ı seçili hale getirmek için
                    $(checkboxId).prop("checked", true);
                });


            },
            error: function (xhr) {

                console.log("Hata oluştu: ", xhr.responseText);
            }

        });

    });


});

