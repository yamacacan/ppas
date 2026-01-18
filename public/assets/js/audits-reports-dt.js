$(document).ready(function() {


    //console.log($('#subGroupID').val());

    

    var table = $('#audits-reports-dt').DataTable({

        responsive: true,
        stateSave: true,
        language: { url: '../assets/json/turkish.json'},
        dom: 'Bfrtip',
        dom: 'Blfrtip',
        lengthChange: true,
        lengthMenu: [10, 25, 50],
        buttons: [],
        
   

       
       
    });

  
});

