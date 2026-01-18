$(document).ready(function () {
    $("#auditApplicationProcessForm").submit(function (e) {
        e.preventDefault();

        //precaution_id
        let auditID = $("#audit_id").val();
        let count = $("#count").val();

        var formData = new FormData(this);

        formData.append("auditID", auditID);

        for (var i = 1; i <= count; i++) {
            $("#audit_method" + i + " option:selected").each(function () {
                formData.append("audit_method" + i + "[]", $(this).val());
            });
        }

        //console.log($('#audit_method1').val());
        //console.log(formData.audit_method1);

        //let auditMethods = $('#audit_method' + val).val();

        // Seçilen değerleri birleştir
        // auditMethod = selectedOptions ? selectedOptions.join('+') : '';

        //auditMethods.push(auditMethod);

        $.ajax({
            url: "/save-audit-application-process", // Laravel route
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
                    text: "İşlem başarıyla gerçekleşti.",
                });
            },
            error: function (xhr, status, error) {
                console.error("Error:", error);

                Swal.fire({
                    icon: "error",
                    title: "Hata!",
                    html: "Bir hata oluştu!",
                });
            },
        });
    });

    $("#audit-application-process").on("click", ".proofBTN", function () {
        id = $(this).attr("data");
        $("#question_id").val(id);

        $("#proofDocumentModal").modal("show");
    });

    $("#audit-application-process").on("click", ".showProofBTN", function () {
        let id = $(this).attr("data");
        let auditID = $("#audit_id").val();

        var formData = new FormData();
        formData.append("id", id);
        formData.append("auditID", auditID);

        $.ajax({
            url: "/show-files-aap", // Laravel route
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (data) {
                $("#proofDocumentModal").modal("hide");
                console.log("Success:", data);

                var htmlContent = "";

                if (data.length == 0) {
                    htmlContent = "Henüz kanıtlayıcı belge eklenmedi!";
                } else {
                    data.forEach(function (fileName) {
                        var filePath = "/uploads/proof_documents/" + fileName;
                        htmlContent +=
                            '<a href="' +
                            filePath +
                            '" target="_blank">' +
                            fileName +
                            "</a><br>";
                    });
                }

                $(".modal-body #proofDocuments").html(htmlContent);
            },
            error: function (xhr, status, error) {
                console.error("Error:", error);
                Swal.fire({
                    icon: "error",
                    title: "Hata!",
                    text: "Bir hata oluştu.",
                });
            },
        });
        $("#proofDocumentShowModal").modal("show");
    });

    $("#upload_form").submit(function (e) {
        e.preventDefault();

        let auditID = $("#audit_id").val();

        //question id
        let id = $("#question_id").val();

        console.log(id);

        var fileInput = $("#proof_document_file")[0];
        var files = fileInput.files;

        var formData = new FormData();

        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            formData.append("proof_document_files[]", file, file.name);
        }
        formData.append("id", id);
        formData.append("auditID", auditID);

        $.ajax({
            url: "/file-upload-aap", // Laravel route
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (data) {
                $("#proofDocumentModal").modal("hide");
                console.log("Success:", data);
                Swal.fire({
                    icon: "success",
                    title: "Başarılı!",
                    text: "Dosyalar başarıyla eklendi.",
                });
                $("#proof_document_file").val("");
            },
            error: function (xhr, status, error) {
                console.error("Error:", error);
                var errorMessage = "Dosya Eklerken bir hata oluştu."; // Varsayılan hata mesajı
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    // Özel hata mesajları varsa bunları kullan
                    errorMessage = Object.values(xhr.responseJSON.errors)
                        .map(function (errorList) {
                            return errorList.join("<br>");
                        })
                        .join("<br>");
                }
                Swal.fire({
                    icon: "error",
                    title: "Hata!",
                    html: errorMessage, // HTML destekli hata mesajı
                });
            },
        });
    });

    $("#audit-application-process").on("click", ".findingBTN", function (e) {
        e.preventDefault();
        id = $(this).attr("data");
        console.log("id " + id);

        questionID = $(this).attr("data2");

        //hidden id değeri kaydederken kullanılıyor
        $("#ID").val(id);

        let auditID = $("#audit_id").val();

        var formData = new FormData();
        formData.append("id", id);
        formData.append("auditID", auditID);
        formData.append("questionID", questionID);

        $.ajax({
            url: "/to-finding2-modal", // Laravel route
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (data) {
                console.log("Success:", data);

                $("#findingModal2").modal("show");

                if (data.length != 1) {
                    $("#question").html(`<b>${data["question"]}</b>`);
                    $("#finding_code").val(data["finding_code"]);
                    $("#finding").val(data["finding"]);
                    $("#finding_criticality_level").val(data["level"]);
                }

                precaution = `<b>${data["precaution"]}</b>`;
                $(".modal-body #precaution").html(precaution);
            },
            error: function (xhr, status, error) {
                console.error("Error:", error);
                Swal.fire({
                    icon: "error",
                    title: "Hata!",
                    text: "Bir hata oluştu.",
                });
            },
        });
    });


    $("#finding2_save_form").submit(function (e) {
        e.preventDefault();

        //formdaki hidden id değeri
        let id = $("#ID").val();

        let auditID = $("#audit_id").val();

        var formData = new FormData(this);
        formData.append("id", id);
        formData.append("auditID", auditID);

        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },

            url: "/save-finding2",

            type: "POST",

            data: formData,

            contentType: false,
            processData: false,

            success: function (response) {
                console.log(response);

                if (response.trim() == "1") {
                    Swal.fire({
                        icon: "success",
                        title: "Başarılı!",
                        text: "İşlem başarıyla gerçekleşti.",
                    });

                    $("#findingModal2").modal("hide");
                }
            },
            error: function (xhr) {
                swal.fire({
                    title: "Uyarı!",
                    icon: "error",
                    text: "07472727122024 Bir Hata Oluştu!",
                });
            },
        });
    });
});
