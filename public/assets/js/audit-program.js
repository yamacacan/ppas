$(document).ready(function () {
    $("#auditProgramForm").submit(function (e) {
        e.preventDefault();

        //precaution_id
        let auditID = $("#audit_id").val();

        var formData = new FormData(this);

        formData.append("auditID", auditID);

        $.ajax({
            url: "/save-audit-program", // Laravel route
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (data) {
                console.log("Success:", data);
                Swal.fire({
                    icon: "success",
                    title: "Başarılı!",
                    text: "Dosyalar başarıyla eklendi.",
                });
            },
            error: function (xhr, status, error) {
                let errorMessage =
                    xhr.responseJSON && xhr.responseJSON.message
                        ? xhr.responseJSON.message
                        : "Beklenmeyen bir hata oluştu!";

                Swal.fire({
                    icon: "error",
                    title: "Hata!",
                    html: errorMessage,
                });
            },
        });
    });
});
