$(document).ready(function() {


    //console.log($('#subGroupID').val());

    

    var table = $('#compensatory-control-forms-dt').DataTable({

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

 
});

