$(document).ready(function () {
    var table = $("#pas-dt").DataTable({
        // "scrollY": "2000px",
        // "scrollX": true,
        scrollCollapse: true,

        // "initComplete": function (settings, json) {
        //     $('div.dataTables_scrollBody').addClass("custom-scrollbar");
        // },

        responsive: true,
        stateSave: true,
        language: { url: "../assets/json/turkish.json" },

        dom: '<"top-buttons" Blf>rt<"bottom-buttons" Blfip>',
        
        lengthChange: true,
        lengthMenu: [10, 25, 50, 100],
        buttons: [
            {
                text: "Kaydet",
                className: "btn btn-primary text-white me-2",

                action: function (e, dt, node, config) {
                    save_current_page();
                },
            },

       
        ],
        initComplete: function () {
            // Butonları bir kez oluştur
            var buttons = this.api().buttons().container();
    
            // Üst kısma butonları bir kez ekle
            $('.top-buttons-container').html(buttons.clone(true));
    
            // Alt kısma butonları bir kez ekle
            $('.bottom-buttons-container').html(buttons.clone(true));
        },
    });

    $("#pas-dt").on("click", ".findingBTN", function () {
        id = $(this).attr("data");
        console.log(id);
        $("#precautionID").val(id);

        let auditID = $("#audit_id").val();
        let subgroupID = $("#sub_group_id").val();

        var formData = new FormData();
        formData.append("id", id);
        formData.append("auditID", auditID);
        formData.append("subgroupID", subgroupID);

        $.ajax({
            url: "/to-finding-modal", // Laravel route
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (data) {
                console.log("Success:", data);

                $("#findingModal").modal("show");

                if (data.length != 1) {
                    let htmlContent = "";
                    let questions = "";

                    if (data.length == 0) {
                        htmlContent = "Tedbir Bulunamadı";
                    } else {
                        data["question"].forEach((item) => {
                            questions += `<li>${item}</li>`;
                        });

                        $(".modal-body #collapseQuestion").html(
                            `<ol class="list-group">${questions}</ul>`
                        );
                        $("#finding_code").val(data["finding_code"]);
                        $("#finding").val(data["finding"]);
                        $("#finding_criticality_level").val(data["level"]);
                    }
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

    $("#pas-dt").on("click", ".showProofPasBTN", function () {
        let id = $(this).attr("data");
        let formNo = $("#PAS_main_group_id").val();
        let auditID = $("#audit_id").val();

        var formData = new FormData();
        formData.append("id", id);
        formData.append("formNo", formNo);
        formData.append("auditID", auditID);

        $.ajax({
            url: "/show-files?" + new Date().getTime(),
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
                    htmlContent += '<table class="table">';

                    data.forEach(function (fileName) {
                        var filePath = "/uploads/proof_documents/" + fileName;
                        htmlContent +=
                            "<tr>" +
                            '<td><a href="' +
                            filePath +
                            '" target="_blank">' +
                            fileName +
                            "</a> </td>" +
                            '<td><button class="btn btn-sm" onclick="confirmDeleteFilePas(\'' +
                            fileName +
                            '\')"><i class="icon-trash"></i></button></td>' +
                            "</tr>";
                    });

                    htmlContent += "</table>";
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

    $("#upload_form_pas").submit(function (e) {
        e.preventDefault();

        //precaution_id
        let auditID = $("#audit_id").val();
        let id = $("#precaution").val();
        let formNo = $("#PAS_main_group_id").val();

        var fileInput = $("#proof_document_file")[0];
        var files = fileInput.files;
        //console.log(file);
        var formData = new FormData();
        //formData.append('proof_document_file', file);

        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            formData.append("proof_document_files[]", file, file.name);
        }
        formData.append("id", id);
        formData.append("formNo", formNo);
        formData.append("auditID", auditID);

        $.ajax({
            url: "/file-upload", // Laravel route
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

    $("#pas-dt").on("click", ".showProofBTN", function () {
        $(".modal-body #proofDocuments").html("");

        let id = $(this).attr("data");
        let formNo = $("#PAS_main_group_id").val();

        var formData = new FormData();
        formData.append("id", id);
        formData.append("formNo", formNo);

        $.ajax({
            url: "/show-files-gap?" + new Date().getTime(),
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

    $("#pas-dt").on("click", ".proofPasBTN", function () {
        id = $(this).attr("data");
        $("#precaution").val(id);

        $("#proofDocumentModal").modal("show");
    });

    //Denetleyici mesaj göndermek için modalı açıyor
    $("#pas-dt").on("click", ".showMessageModalBtn", function () {
        id = $(this).attr("data");
        $("#precautionID").val(id);

        auditID = $(this).attr("data-auditID");
        $("#auditID").val(auditID);
        $("#messageContent").val("");
        $("#messageArea").html("");
        $("#sendTo").val("");

        $("#messageSystemModal").modal("show");
    });

    $("#sendTo").on("change", function () {
        let recipientID = $(this).val();
        let auditID = $("#auditID").val();
        let precautionID = $("#precautionID").val();

        var formData = new FormData();
        formData.append("recipientID", recipientID);
        formData.append("auditID", auditID);
        formData.append("precautionID", precautionID);

        $.ajax({
            url: "/get-pas-messages", // Laravel route
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },

            success: function (data) {
                console.log("Success:", data);

                let htmlContent = `
                    <table style="border-collapse: collapse; width: 100%;">
                        <thead>
                            <tr>
                                <th style="border: 0;">Gönderen</th>
                                <th style="border: 0;">Mesaj</th>
                                <th style="border: 0;">Zaman</th>
                                <th style="border: 0;">Tarih</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                let addedMessageIds = new Set(); // Eklenen mesaj ID'lerini takip etmek için Set kullan

                data.forEach(function (item) {
                    // Eğer mesaj daha önce eklenmişse, tekrar eklemeyi atla
                    if (addedMessageIds.has(item.id)) return;
                    addedMessageIds.add(item.id);

                    // Ana mesajı ekle
                    let message = item.content;
                    let date = new Date(item.created_at);

                    let formattedDate =
                        date.getDate().toString().padStart(2, "0") +
                        "/" +
                        (date.getMonth() + 1).toString().padStart(2, "0") +
                        "/" +
                        date.getFullYear();

                    let formattedTime =
                        date.getHours().toString().padStart(2, "0") +
                        ":" +
                        date.getMinutes().toString().padStart(2, "0") +
                        ":" +
                        date.getSeconds().toString().padStart(2, "0");

                    let senderName = item.sender
                        ? `${item.sender.name} ${item.sender.last_name}`
                        : "Bilinmeyen";

                    htmlContent += `
                        <tr>
                            <td style="border: 0; font-weight: bold;">${senderName}</td>
                            <td style="border: 0; font-weight: bold;">${message}</td>
                            <td style="border: 0;">${formattedTime}</td>
                            <td style="border: 0;">${formattedDate}</td>
                        </tr>
                    `;

                    // Yanıt mesajlarını sadece bir kez ekle
                    if (item.replies && item.replies.length > 0) {
                        item.replies.forEach(function (reply) {
                            if (addedMessageIds.has(reply.id)) return;
                            addedMessageIds.add(reply.id);

                            let replyMessage = reply.content;
                            let replyDate = new Date(reply.created_at);

                            let replySenderName = reply.sender
                                ? `${reply.sender.name} ${reply.sender.last_name}`
                                : "Bilinmeyen";

                            let formattedReplyDate =
                                replyDate
                                    .getDate()
                                    .toString()
                                    .padStart(2, "0") +
                                "/" +
                                (replyDate.getMonth() + 1)
                                    .toString()
                                    .padStart(2, "0") +
                                "/" +
                                replyDate.getFullYear();

                            let formattedReplyTime =
                                replyDate
                                    .getHours()
                                    .toString()
                                    .padStart(2, "0") +
                                ":" +
                                replyDate
                                    .getMinutes()
                                    .toString()
                                    .padStart(2, "0") +
                                ":" +
                                replyDate
                                    .getSeconds()
                                    .toString()
                                    .padStart(2, "0");

                            htmlContent += `
                                <tr>
                                    <td style="border: 0; padding-left: 20px; color: #555;">↳ ${replySenderName}</td>
                                    <td style="border: 0; padding-left: 20px; color: #555;">${replyMessage}</td>
                                    <td style="border: 0;">${formattedReplyTime}</td>
                                    <td style="border: 0;">${formattedReplyDate}</td>
                                </tr>
                            `;
                        });
                    }
                });

                htmlContent += `
                        </tbody>
                    </table>
                `;

                $("#messageArea").html(htmlContent);
            },

            error: function (xhr, status, error) {
                console.error("Error:", error);
            },
        });
    });

    function save_current_page() {
        // e.preventDefault();

        //datatable da geçerli sayfanın verilerini okuyor
        var rows = table
            .rows({
                page: "current",
            })
            .nodes();

        var data_attr = [];

        $(rows).each(function () {
            var rowData = table.row($(this)).data();

            //console.log(rowData[4]);

            //inputun data attribute'nı aldık bu idlere göre verileri alacağız
            var id = $(rowData[9]).attr("data");
            data_attr.push(id);
        });

        console.log("data_attr " + data_attr);

        let precautionActivityStatuses = [];
        let auditMethods = [];
        let descriptions = [];

        data_attr.forEach(function (val) {
            let precautionActivityStatus = localStorage.getItem(
                "precautionActivityStatus" + val
            );
            if (
                precautionActivityStatus === null ||
                institutiprecautionActivityStatusnalDescription === undefined
            ) {
                precautionActivityStatus = $(
                    "#precaution_activity_status" + val + ""
                ).val();
            }
            precautionActivityStatuses.push(precautionActivityStatus);

            let auditMethod = localStorage.getItem("auditMethod" + val);
            if (auditMethod === null || auditMethod === undefined) {
                // '#audit_method' + val öğesinden seçilen tüm değerleri al
                let selectedOptions = $("#audit_method" + val).val();

                // Seçilen değerleri birleştir
                auditMethod = selectedOptions ? selectedOptions.join("+") : "";
            }
            auditMethods.push(auditMethod);

            let description = localStorage.getItem("description" + val);
            if (description === null || description === undefined) {
                description = $("#description" + val + "").val();
            }
            descriptions.push(description);
        });

        console.log("precautionActivityStatuses " + precautionActivityStatuses);
        console.log("auditMethods " + auditMethods);
        console.log("description " + descriptions);

        let auditID = $("#audit_id").val();
        let subgroupID = $("#sub_group_id").val();

        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },

            url: "/tedbir-etkinlik-durumu",

            type: "POST",

            data: {
                auditID: auditID,
                subgroupID: subgroupID,
                precautionActivityStatuses: precautionActivityStatuses,
                auditMethods: auditMethods,
                descriptions: descriptions,
                precaution_ids: data_attr,
            },

            success: function (response) {
                console.log(response);

                if (response.trim() == "1") {
                    Swal.fire({
                        icon: "success",
                        title: "Başarılı!",
                        text: "Kayıt başarıyla gerçekleşti.",
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: "error",
                    title: "Hata!",
                    text: "Kayıt sırasında bir hata oluştu.",
                });
            },
        });
    }

    $("#finding_save_form").submit(function (e) {
        e.preventDefault();

        //precaution_id
        let id = $("#precautionID").val();
        let auditID = $("#audit_id").val();
        let subgroupID = $("#sub_group_id").val();

        var formData = new FormData(this);
        formData.append("id", id);
        formData.append("auditID", auditID);
        formData.append("subgroupID", subgroupID);

        console.log(formData);

        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },

            url: "/save-finding",

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

                    $("#findingModal").modal("hide");
                }
            },
            error: function (xhr) {
                swal.fire({
                    title: "Uyarı!",
                    icon: "error",
                    text: "155602022024 Bir Hata Oluştu!",
                });
            },
        });
    });

    //Denetime Atanan Personele Mesaj Gönderme
    $("#messageForm").submit(function (e) {
        e.preventDefault();
        let id = $("#precautionID").val();
        let message = $("#messageContent").val();
        let auditID = $("#auditID").val();
        let recipientID = $("#sendTo").val();
        let subgroupID = $("#sub_group_id").val();
        let precautionID = $("#precautionID").val();
        let parentID = $("#parentID").val(); // Parent ID'yi ekleyin, eğer yanıt mesajıysa

        var formData = new FormData();
        formData.append("id", id);
        formData.append("message", message);
        formData.append("auditID", auditID);
        formData.append("recipientID", recipientID);
        formData.append("subgroupID", subgroupID);
        formData.append("precautionID", precautionID);
        formData.append("parentID", parentID); // parentID'yi ekleyin

        $.ajax({
            url: "/create-pas-messages", // Laravel route
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (data) {
                console.log("Success:", data);

                let htmlContent = `
                    <table style="border-collapse: collapse; width: 100%;">
                        <thead>
                            <tr>
                                <th style="border: 0;">Gönderen</th>
                                <th style="border: 0;">Mesaj</th>
                                <th style="border: 0;">Zaman</th>
                                <th style="border: 0;">Tarih</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                let addedMessageIds = new Set(); // Eklenen mesaj ID'lerini takip etmek için Set kullan

                data.forEach(function (item) {
                    // Eğer mesaj daha önce eklenmişse, tekrar eklemeyi atla
                    if (addedMessageIds.has(item.id)) return;
                    addedMessageIds.add(item.id);

                    // Ana mesajı ekle
                    let message = item.content;
                    let date = new Date(item.created_at);

                    let formattedDate =
                        date.getDate().toString().padStart(2, "0") +
                        "/" +
                        (date.getMonth() + 1).toString().padStart(2, "0") +
                        "/" +
                        date.getFullYear();

                    let formattedTime =
                        date.getHours().toString().padStart(2, "0") +
                        ":" +
                        date.getMinutes().toString().padStart(2, "0") +
                        ":" +
                        date.getSeconds().toString().padStart(2, "0");

                    let senderName = item.sender
                        ? `${item.sender.name} ${item.sender.last_name}`
                        : "Bilinmeyen";

                    htmlContent += `
                        <tr>
                            <td style="border: 0;">${senderName}</td>
                            <td style="border: 0;">${message}</td>
                            <td style="border: 0;">${formattedTime}</td>
                            <td style="border: 0;">${formattedDate}</td>
                        </tr>
                    `;

                    // Yanıt mesajlarını sadece bir kez ekle
                    if (item.replies && item.replies.length > 0) {
                        item.replies.forEach(function (reply) {
                            if (addedMessageIds.has(reply.id)) return;
                            addedMessageIds.add(reply.id);

                            let replyMessage = reply.content;
                            let replyDate = new Date(reply.created_at);

                            let replySenderName = reply.sender
                                ? `${reply.sender.name} ${reply.sender.last_name}`
                                : "Bilinmeyen";

                            let formattedReplyDate =
                                replyDate
                                    .getDate()
                                    .toString()
                                    .padStart(2, "0") +
                                "/" +
                                (replyDate.getMonth() + 1)
                                    .toString()
                                    .padStart(2, "0") +
                                "/" +
                                replyDate.getFullYear();

                            let formattedReplyTime =
                                replyDate
                                    .getHours()
                                    .toString()
                                    .padStart(2, "0") +
                                ":" +
                                replyDate
                                    .getMinutes()
                                    .toString()
                                    .padStart(2, "0") +
                                ":" +
                                replyDate
                                    .getSeconds()
                                    .toString()
                                    .padStart(2, "0");

                            htmlContent += `
                                <tr>
                                    <td style="border: 0; padding-left: 20px; color: #555;">↳ ${replySenderName}</td>
                                    <td style="border: 0; padding-left: 20px; color: #555;">${replyMessage}</td>
                                    <td style="border: 0;">${formattedReplyTime}</td>
                                    <td style="border: 0;">${formattedReplyDate}</td>
                                </tr>
                            `;
                        });
                    }
                });

                htmlContent += `
                        </tbody>
                    </table>
                `;

                $("#messageArea").html(htmlContent);
                $("#messageContent").val(""); // Mesaj kutusunu temizle
            },

            error: function (xhr, status, error) {
                console.error("Error:", error);
            },
        });
    });

    // $("#messageForm").submit(function (e) {
    //     e.preventDefault();
    //     let id = $("#precautionID").val();
    //     let message = $("#messageContent").val();
    //     let auditID = $("#auditID").val();
    //     let recipientID = $("#sendTo").val();
    //     let precautionID = $("#precautionID").val();

    //     var formData = new FormData();
    //     formData.append("id", id);
    //     formData.append("message", message);
    //     formData.append("auditID", auditID);
    //     formData.append("recipientID", recipientID);
    //     formData.append("precautionID", precautionID);

    //     $.ajax({
    //         url: "/create-pas-messages", // Laravel route
    //         type: "POST",
    //         data: formData,
    //         processData: false,
    //         contentType: false,
    //         headers: {
    //             "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    //         },
    //         success: function (data) {
    //             console.log("Success:", data);

    //             let htmlContent = `
    //                 <table style="border-collapse: collapse; width: 100%;">
    //                     <thead>
    //                         <tr>
    //                             <th style="border: 0;">Mesaj</th>
    //                             <th style="border: 0;">Zaman</th>
    //                             <th style="border: 0;">Tarih</th>
    //                         </tr>
    //                     </thead>
    //                     <tbody>
    //             `;

    //             let addedMessageIds = new Set(); // Eklenen mesaj ID'lerini takip etmek için Set kullan

    //             data.forEach(function (item) {
    //                 // Eğer mesaj daha önce eklenmişse, tekrar eklemeyi atla
    //                 if (addedMessageIds.has(item.id)) return;
    //                 addedMessageIds.add(item.id);

    //                 // Ana mesajı ekle
    //                 let message = item.content;
    //                 let date = new Date(item.created_at);

    //                 let formattedDate =
    //                     date.getDate().toString().padStart(2, "0") +
    //                     "/" +
    //                     (date.getMonth() + 1).toString().padStart(2, "0") +
    //                     "/" +
    //                     date.getFullYear();

    //                 let formattedTime =
    //                     date.getHours().toString().padStart(2, "0") +
    //                     ":" +
    //                     date.getMinutes().toString().padStart(2, "0") +
    //                     ":" +
    //                     date.getSeconds().toString().padStart(2, "0");

    //                 htmlContent += `
    //                     <tr>
    //                         <td style="border: 0;">${message}</td>
    //                         <td style="border: 0;">${formattedTime}</td>
    //                         <td style="border: 0;">${formattedDate}</td>
    //                     </tr>
    //                 `;

    //                 // Yanıt mesajlarını sadece bir kez ekle
    //                 if (item.replies && item.replies.length > 0) {
    //                     item.replies.forEach(function (reply) {
    //                         if (addedMessageIds.has(reply.id)) return;
    //                         addedMessageIds.add(reply.id);

    //                         let replyMessage = reply.content;
    //                         let replyDate = new Date(reply.created_at);

    //                         let formattedReplyDate =
    //                             replyDate.getDate().toString().padStart(2, "0") +
    //                             "/" +
    //                             (replyDate.getMonth() + 1).toString().padStart(2, "0") +
    //                             "/" +
    //                             replyDate.getFullYear();

    //                         let formattedReplyTime =
    //                             replyDate.getHours().toString().padStart(2, "0") +
    //                             ":" +
    //                             replyDate.getMinutes().toString().padStart(2, "0") +
    //                             ":" +
    //                             replyDate.getSeconds().toString().padStart(2, "0");

    //                         htmlContent += `
    //                             <tr>
    //                                 <td style="border: 0; padding-left: 20px; color: #555;">↳ ${replyMessage}</td>
    //                                 <td style="border: 0;">${formattedReplyTime}</td>
    //                                 <td style="border: 0;">${formattedReplyDate}</td>
    //                             </tr>
    //                         `;
    //                     });
    //                 }
    //             });

    //             htmlContent += `
    //                     </tbody>
    //                 </table>
    //             `;

    //             $("#messageArea").html(htmlContent);
    //         },

    //         error: function (xhr, status, error) {
    //             console.error("Error:", error);
    //         },
    //     });
    // });
});

function deleteFilePas(fileName) {
    console.log("burada");
    $.ajax({
        url: "/delete-file-pas", // Laravel route
        type: "POST",
        data: { fileName: fileName },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            console.log("Deleting file:", fileName);
            console.log("File deleted successfully:", data);
            Swal.fire({
                icon: "success",
                title: "Başarılı!",
                text: "Dosya başarıyla silindi.",
            });
            // Remove the file link from the modal
            $('a[href="/uploads/proof_documents/' + fileName + '"]')
                .closest("tr")
                .remove();
        },
        error: function (xhr, status, error) {
            console.error("Error deleting file:", error);
            Swal.fire({
                icon: "error",
                title: "Hata!",
                text: "Dosya silinirken bir hata oluştu.",
            });
        },
    });
}

function confirmDeleteFilePas(fileName) {
    Swal.fire({
        title: "Emin misiniz?",
        text: "Bu dosyayı silmek istediğinizden emin misiniz?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Evet, sil!",
        cancelButtonText: "Hayır, iptal et",
    }).then((result) => {
        if (result.isConfirmed) {
            deleteFilePas(fileName);
        }
    });
}

function showQuestionsModalPas(id, event) {
    event.preventDefault();
    $("#questionsModal").modal("show");

    console.log("tedbir id " + id);
    if (id) {
    }
    $.ajax({
        url: "/pas/getQuestions",
        type: "POST",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        data: { id: id },

        success: function (response) {
            console.log(response);
            let questions = response.questions;
            let auditSuggestions = response.audit_suggestions;

            let questionsDiv = $("#questions");
            questionsDiv.empty(); // Clear any existing content

            let auditSuggestionsDiv = $("#audit_suggestions");
            auditSuggestionsDiv.empty();

            let precautionDiv = $("#precautions");
            precautionDiv.empty();

            let precautionDescriptionDiv = $("#precaution_description");
            precautionDescriptionDiv.empty();

            let precaution_description = response.precaution_description;

            let precaution = response.precaution;
            console.log("precautions " + precaution);

            precautionDiv.append(`<b>${precaution}</b>`);

            precautionDescriptionDiv.append(
                `<b>Tanım :</b> <i>${precaution_description}</i>`
            );

            let list = $("<ul></ul>");
            questions.forEach((question, index) => {
                list.append(`<li>${index + 1}. ${question}</li>`);
            });
            questionsDiv.append(list);

            let html = "";

            html = response.audit_suggestion;

            auditSuggestionsDiv.append(html);
        },
        error: function (xhr, status, error) {},
    });
}
