$(document).ready(function () {
    var table = $("#pas-messages-dt").DataTable({
        responsive: true,
        stateSave: true,
        language: { url: "../assets/json/turkish.json" },
        dom: "Bfrtip",
        dom: "Blfrtip",
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

    //Personele gelen mesajları görmek için
    $("#pas-messages-dt").on("click", ".showMessageModalBtn", function () {
        pasMessageID = $(this).attr("data");
        console.log("pasMessageID " + pasMessageID);

        let messageTitle = $(this).attr("data1")+' '+$(this).attr("data2")+' '+$(this).attr("data3");
        
        $("#messageTitle").text(messageTitle);

        $.ajax({
            url: "/pas-messages/fetch-data",
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            data: { pasMessageID: pasMessageID },

            success: function (response) {
                console.log(response);
                $("#sendTo").hide();
                $(".pasMessageBTN").text("Cevapla");
                $("#messageSystemModal").modal("show");
                $("#parentID").val(response[0].id);

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

                response.forEach((item) => {
                    let message = item.content;
                    let date = new Date(item.created_at);

                    // Tarihi formatla
                    let formattedDate =
                        date.getDate().toString().padStart(2, "0") +
                        "/" +
                        (date.getMonth() + 1).toString().padStart(2, "0") +
                        "/" +
                        date.getFullYear();

                    // Zamanı formatla
                    let formattedTime =
                        date.getHours().toString().padStart(2, "0") +
                        ":" +
                        date.getMinutes().toString().padStart(2, "0") +
                        ":" +
                        date.getSeconds().toString().padStart(2, "0");

                    // Yanıt mesajları için ok işareti ekleyin
                    let senderName = item.parent_id
                        ? `↳ ${item.sender.name} ${item.sender.last_name}`
                        : `${item.sender.name} ${item.sender.last_name}`;
                    let messageContent = item.parent_id
                        ? `${message}`
                        : message;

                    // Mesajı tabloya ekle (ana veya yanıt mesajı fark etmeksizin)
                    htmlContent += `
                        <tr>
                            <td style="border: 0; ${
                                item.parent_id
                                    ? "padding-left: 20px; color: #555;"
                                    : "font-weight: bold;"
                            }">${senderName}</td>
                            <td style="border: 0; ${
                                item.parent_id
                                    ? "padding-left: 20px; color: #555;"
                                    : ""
                            }">${messageContent}</td>
                            <td style="border: 0;">${formattedTime}</td>
                            <td style="border: 0;">${formattedDate}</td>
                        </tr>
                    `;
                });

                htmlContent += `
                        </tbody>
                    </table>
                `;

                $("#messageArea").html(htmlContent);
                $("#subGroupID").val(response[0].sub_group_id);
                $("#precautionID").val(response[0].precaution_id);
                $("#auditID").val(response[0].audit_id);
                $("#replyTo").val(response[0].sender_id);
            },
        });

        $("#messageContent").val("");

      
    });

    //Personel kendisine gelen mesajları cevaplamak için
    $("#messageForm").submit(function (e) {
        e.preventDefault();

        let message = $("#messageContent").val();
        let auditID = $("#auditID").val();
        let recipientID = $("#replyTo").val();
        let subGroupID= $("#subGroupID").val();
        let precautionID = $("#precautionID").val();
        let parentID = $("#parentID").val(); // Cevaplanan mesajın ID'sini alın

        console.log("message: " + message);
        console.log("auditID: " + auditID);
        console.log("recipientID: " + recipientID);
        console.log("precautionID: " + precautionID);
        console.log("parentID: " + parentID);
        console.log("subGroupID: " + subGroupID);
        

        var formData = new FormData();

        formData.append("message", message);
        formData.append("auditID", auditID);
        formData.append("recipientID", recipientID);
        formData.append("subgroupID", subGroupID);
        formData.append("precautionID", precautionID);
        formData.append("parentID", parentID); // parentID'yi ekleyin

        $.ajax({
            url: "/reply-pas-messages", // Laravel route
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

                data.forEach(function (item) {
                    let message = item.content;
                    let date = new Date(item.created_at);

                    // Tarihi formatla: gün/ay/yıl
                    let formattedDate =
                        date.getDate().toString().padStart(2, "0") +
                        "/" +
                        (date.getMonth() + 1).toString().padStart(2, "0") +
                        "/" +
                        date.getFullYear();

                    // Zamanı formatla: saat:dakika:saniye
                    let formattedTime =
                        date.getHours().toString().padStart(2, "0") +
                        ":" +
                        date.getMinutes().toString().padStart(2, "0") +
                        ":" +
                        date.getSeconds().toString().padStart(2, "0");

                    // Yanıt mesajları için ok işareti ekle ve girinti uygula
                    let senderName = item.parent_id
                        ? `↳ ${item.sender.name} ${item.sender.last_name}`
                        : `${item.sender.name} ${item.sender.last_name}`;
                    let messageContent = item.parent_id
                        ? `↳ ${message}`
                        : message;
                    let indentStyle = item.parent_id
                        ? "padding-left: 20px; color: #555;"
                        : "font-weight: bold;";

                    // Mesajı tabloya ekle (ana veya yanıt mesajı fark etmeksizin)
                    htmlContent += `
                        <tr>
                            <td style="border: 0; ${indentStyle}">${senderName}</td>
                            <td style="border: 0; ${indentStyle}">${messageContent}</td>
                            <td style="border: 0;">${formattedTime}</td>
                            <td style="border: 0;">${formattedDate}</td>
                        </tr>
                    `;
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
});
