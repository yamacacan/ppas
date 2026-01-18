$(document).ready(function() {


    //console.log($('#subGroupID').val());

    

    var table = $('#work-packages-dt').DataTable({

        responsive: true,
        stateSave: true,
        language: { url: '../assets/json/turkish.json'},
        dom: 'Bfrtip',
        dom: 'Blfrtip',
        lengthChange: true,
        lengthMenu: [10, 25, 50, 100],
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

    function showWorkPackagesModal(id) {
        $("#workPackagesModal").modal("show");

        let textID = "#work_package_no" + id;
        let work_package_no = $(textID).val();

        console.log("---" + work_package_no);
        if (work_package_no) {
            $.ajax({
                url: "/is-paketleri/fetch-data",
                type: "POST",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                data: { work_package_no: work_package_no },

                success: function (response) {
                    var start_date = response.starting_date;
                    if (start_date) {
                        var datePices = start_date.split("-");
                        var formattedDate1 =
                            datePices[2] +
                            "-" +
                            datePices[1] +
                            "-" +
                            datePices[0];
                    }

                    var end_date = response.end_date;
                    if (end_date) {
                        var datePices = end_date.split("-");
                        var formattedDate2 =
                            datePices[2] +
                            "-" +
                            datePices[1] +
                            "-" +
                            datePices[0];
                    }

                    $('.modal-body select[name="work_package_no"]').hide();
                    $('.modal-body label[name="work_package_no_label"]').hide();
                    $('.modal-body input[name="starting_date"]').val(
                        formattedDate1
                    );
                    $('.modal-body input[name="end_date"]').val(formattedDate2);
                    $('.modal-body input[name="name"]').val(response.name);
                    $('.modal-body textarea[name="activity"]').val(
                        response.activity
                    );
                    $('.modal-body textarea[name="term_target"]').val(
                        response.term_target
                    );
                    $('.modal-body textarea[name="current_situation"]').val(
                        response.current_situation
                    );
                },
                error: function (xhr, status, error) {},
            });
        } else {
            $('.modal-body input[name="starting_date"]').val("");
            $('.modal-body input[name="end_date"]').val("");
            $('.modal-body input[name="name"]').val("");
            $('.modal-body textarea[name="activity"]').val("");
            $('.modal-body textarea[name="term_target"]').val("");
            $('.modal-body textarea[name="current_situation"]').val("");
        }

        // AJAX çağrısı ile iş paketleri verilerini al ve select elementine ekle
        $.ajax({
            url: "/is-paketleri/get-all",
            type: "GET",
            success: function (response) {
                let select = $("#selectWorkPackage");
                select.empty(); // Mevcut seçenekleri temizle

                console.log(response);
                select.append(new Option("Seçiniz", ""));

                // Yeni seçenekleri ekle
                response.forEach(function (item) {
                    console.log(item);
                    select.append(new Option(item.work_package_no));
                });

                // Eğer mevcut bir iş paketi numarası varsa, onu seçili yap
                if (work_package_no) {
                    select.val(work_package_no);
                }
            },
            error: function (xhr, status, error) {
                console.error("İş paketleri alınamadı:", error);
            },
        });
    }


 
});


function affectedPrecautionsModal(work_package_no) {
   

    console.log("work_package_no " + work_package_no);

    if (work_package_no) {
        $.ajax({
            url: "/is-paketleri/fetch-affected-precautions",
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                    "content"
                ),
            },
            data: { work_package_no: work_package_no },

            success: function (response) {
               console.log(response);

               $('#workPackageNo').html(work_package_no);

               $html="<table class='table'><th>Ana Grup</th><th>Alt Grup</th><th>Tedbir</th>";
            response.forEach(function (item) {
                $html += `<tr>
                            <td>${item.mainGroup}</td>
                            <td>${item.subGroup}</td>
                            <td>${item.precaution}</td>
                          </tr>`;
            });
            $html += "</table>";
            $('#affectedPrecautionsShowModal #affectedPrecautions').html($html);

            
              
               $("#affectedPrecautionsShowModal").modal("show");

                // $('.modal-body select[name="work_package_no"]').hide();
                // $('.modal-body label[name="work_package_no_label"]').hide();
                // $('.modal-body input[name="starting_date"]').val(
                //     formattedDate1
                // );
                // $('.modal-body input[name="end_date"]').val(formattedDate2);
                // $('.modal-body input[name="name"]').val(response.name);
                // $('.modal-body textarea[name="activity"]').val(
                //     response.activity
                // );
                // $('.modal-body textarea[name="term_target"]').val(
                //     response.term_target
                // );
                // $('.modal-body textarea[name="current_situation"]').val(
                //     response.current_situation
                // );
            },
            error: function (xhr, status, error) {},
        });
    } else {
        $('.modal-body input[name="starting_date"]').val("");
        $('.modal-body input[name="end_date"]').val("");
        $('.modal-body input[name="name"]').val("");
        $('.modal-body textarea[name="activity"]').val("");
        $('.modal-body textarea[name="term_target"]').val("");
        $('.modal-body textarea[name="current_situation"]').val("");
    }

    // // AJAX çağrısı ile iş paketleri verilerini al ve select elementine ekle
    // $.ajax({
    //     url: "/is-paketleri/get-all",
    //     type: "GET",
    //     success: function (response) {
    //         let select = $("#selectWorkPackage");
    //         select.empty(); // Mevcut seçenekleri temizle

    //         console.log(response);
    //         select.append(new Option("Seçiniz", ""));

    //         // Yeni seçenekleri ekle
    //         response.forEach(function (item) {
    //             console.log(item);
    //             select.append(new Option(item.work_package_no));
    //         });

    //         // Eğer mevcut bir iş paketi numarası varsa, onu seçili yap
    //         if (work_package_no) {
    //             select.val(work_package_no);
    //         }
    //     },
    //     error: function (xhr, status, error) {
    //         console.error("İş paketleri alınamadı:", error);
    //     },
    // });
}

