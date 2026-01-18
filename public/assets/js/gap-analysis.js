$(document).ready(function () {
    var table = $("#gap-analysis-dt").DataTable({
        //"scrollY": "1600px",
        //"scrollX": true,
        scrollCollapse: true,

        // "initComplete": function (settings, json) {
        //     $('div.dataTables_scrollBody').addClass("custom-scrollbar");
        // },

        responsive: true,
        stateSave: true,
        language: { url: "/assets/json/turkish.json" },
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

    $("#gap-analysis-dt").on("click", "input", function () {
        id = $(this).attr("data");

        InputID = $(this).attr("id");

        if (InputID.startsWith("compensatory_control_form_id")) {
            showCompensetoryModal(id);
            selectCompensatoryControlForm(id);

            //TEKF'de hidden inputa gönderdim. kaydetmek için kullancağız
            $("#precaution_id").val(id);
        }

        if (InputID.startsWith("work_package_no")) {
            showWorkPackagesModal(id);
            selectWorkPackage(id);

            //TEKF'de hidden inputa gönderdim. kaydetmek için kullancağız
            $("#precaution_id2").val(id);

            console.log("#precaution_id2", $("#precaution_id2").val());
        }
    });

    $("#gap-analysis-dt").on("change", "input", function () {
        tedbir_id = $(this).attr("data");

        //Tıklanan inputun id'si
        InputID = $(this).attr("id");

        let institutionalDescription = $(this).val();
        localStorage.setItem(InputID, institutionalDescription);

        //console.log(institutionalDescription);

        let compensatoryControlForm = $(this).val();
        localStorage.setItem(InputID, compensatoryControlForm);

        let workPackageNo = $(this).val();
        localStorage.setItem(InputID, workPackageNo);

        //console.log(is_paket_form_no);
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
        var data_target_type = [];
        
        

        $(rows).each(function () {
            var rowData = table.row($(this)).data();

            //console.log(rowData[5]);

            //inputun data(target_id) attribute'nı aldık bu idlere göre verileri alacağız
            var id = $(rowData[5]).attr("data");
            data_attr.push(id);

            //target_type i aldık
            var data2 = $(rowData[5]).attr("data2");
            data_target_type.push(data2);
        });

        console.log("target_ids " + data_attr);
        console.log("targer_types " + data_target_type);

        let institutionalDescriptions = [];
        let precautionImplementationStatuses = [];
        let compensatoryControlForms = [];
        let targetedStates = [];
        let workPackageNos = [];

        data_attr.forEach(function (targetID, index) {

            const targetType = data_target_type[index];
            const typeShort = targetType.includes('Clause') ? 'clause' : 'precaution';
            const compositeKey = targetID + '_' + typeShort;

            console.log("compositeKey " + compositeKey);

            let institutionalDescription = localStorage.getItem(
                "institutionalDescription" + compositeKey
            );
            if (
                institutionalDescription === null ||
                institutionalDescription === undefined
            ) {
                institutionalDescription = $(
                    "#institutional_description" + compositeKey
                ).val();
            }
            institutionalDescriptions.push(institutionalDescription);

            let precautionImplementationStatus = localStorage.getItem(
                "precautionImplementationStatus" + compositeKey
            );
            if (
                precautionImplementationStatus === null ||
                precautionImplementationStatus === undefined
            ) {
                precautionImplementationStatus = $(
                    "#precaution_implementation_status" + compositeKey
                ).val();
            }
            precautionImplementationStatuses.push(
                precautionImplementationStatus
            );

            let compensatoryControlForm = localStorage.getItem(
                "compensatoryControlForm" + compositeKey
            );
            if (
                compensatoryControlForm === null ||
                compensatoryControlForm === undefined
            ) {
                compensatoryControlForm = $(
                    "#compensatory_control_form_id" + compositeKey
                ).val();
            }
            compensatoryControlForms.push(compensatoryControlForm);

            let targetedState = localStorage.getItem("hedeflenen_durum" + compositeKey);
            if (targetedState === null || targetedState === undefined) {
                targetedState = $("#targeted_status" + compositeKey).val();
            }
            targetedStates.push(targetedState);

            let workPackageNo = localStorage.getItem("workPackageNo" + compositeKey);
            if (workPackageNo === null || workPackageNo === undefined) {
                workPackageNo = $("#work_package_no" + compositeKey).val();
            }
            workPackageNos.push(workPackageNo);
        });

        console.log("uygulanma durumlari " + precautionImplementationStatuses);
        console.log("hedeflenen durumlar " + targetedStates);
        console.log("iş paket no lar " + workPackageNos);
        console.log("telafi edici no lar " + compensatoryControlForms);
        console.log("kurum açıklamaları " + institutionalDescriptions);

        let ana_grup_id = $("#main_group_id").val();
        let grup_id = $("#sub_group_id").val();

        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },

            url: "/bosluk_analizi",

            type: "POST",

            data: {
                ana_grup_id: ana_grup_id,
                grup_id: grup_id,
                precautionImplementationStatuses: precautionImplementationStatuses,
                targetedStates: targetedStates,
                workPackageNos: workPackageNos,
                compensatoryControlForms: compensatoryControlForms,
                institutionalDescriptions: institutionalDescriptions,
                target_ids: data_attr,
                target_types: data_target_type,
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

    function showCompensetoryModal(id) {
        $("#compensatoryModal").modal("show");

        //seçilen satırın ilgili tedbir değişkenini alıyor
        precaution = $("#precaution" + id).val();
        $(".modal-body #precaution").html("<b>" + precaution + "</b>");

        // $('#btn_is_paket').click(function() {

        //     $('.telafi_edici_kontrol_modal').modal('hide');
        //     $('.is_paketi_modal').modal('show');

        // });

        let textID = "#compensatory_control_form_id" + id;
        let compensatory_control_id = $(textID).val();

        if (compensatory_control_id) {
            $.ajax({
                url: "/telafi-edici-kontrol-formlari/fetch-data",
                type: "POST",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                data: { compensatory_control_id: compensatory_control_id },

                success: function (response) {
                    var date = response.duration;
                    if (date) {
                        var datePices = date.split("-");
                        var formattedDate =
                            datePices[2] +
                            "-" +
                            datePices[1] +
                            "-" +
                            datePices[0];
                    }

                    $(
                        '.modal-body textarea[name="security_precaution_requirements"]'
                    ).val(response.security_precaution_requirements);
                    $('.modal-body input[name="duration"]').val(formattedDate);
                    $('.modal-body textarea[name="defination"]').val(
                        response.defination
                    );
                    $('.modal-body textarea[name="risk"]').val(response.risk);
                    $('.modal-body textarea[name="reason"]').val(
                        response.reason
                    );
                    $('.modal-body textarea[name="verification_method"]').val(
                        response.verification_method
                    );
                    $('.modal-body textarea[name="description"]').val(
                        response.description
                    );
                },
                error: function (xhr, status, error) {},
            });
        } else {
            //daha önce dolu olan veri varsa silmek için

            $(
                '.modal-body textarea[name="security_precaution_requirements"]'
            ).val("");
            $('.modal-body input[name="duration"]').val("");
            $('.modal-body textarea[name="defination"]').val("");
            $('.modal-body textarea[name="risk"]').val("");
            $('.modal-body textarea[name="reason"]').val("");
            $('.modal-body textarea[name="verification_method"]').val("");
            $('.modal-body textarea[name="description"]').val("");
        }

        // AJAX çağrısı ile iş paketleri verilerini al ve select elementine ekle
        $.ajax({
            url: "/telafi-edici-kontrol-formlari/get-all",
            type: "GET",
            success: function (response) {
                let select = $("#selectCompensatoryControlForm");
                select.empty(); // Mevcut seçenekleri temizle

                console.log(response);

                select.append(new Option("Seçiniz", ""));

                // Yeni seçenekleri ekle
                response.forEach(function (item) {
                    console.log(item);
                    select.append(new Option(item.id));
                });

                // Eğer mevcut bir iş paketi numarası varsa, onu seçili yap
                if (compensatory_control_id) {
                    select.val(compensatory_control_id);
                }
            },
            error: function (xhr, status, error) {
                console.error(
                    "Telafi edici kontrol formları alınamadı:",
                    error
                );
            },
        });
    }

    function showWorkPackagesModal(id) {
        $("#workPackagesModal").modal("show");

        let textID = "#work_package_no" + id;
        let work_package_no = $(textID).val();

        console.log("work_package_no " + work_package_no);
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

                //console.log(response);

                select.append(new Option("Seçiniz", ""));

                // Yeni seçenekleri ekle
                let seen = new Set();
                response.forEach(function (item) {
                    if (!seen.has(item.work_package_no)) {
                        seen.add(item.work_package_no);
                        select.append(new Option(item.work_package_no));
                    }
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

        // AJAX çağrısı ile iş paketi aşamalarını al ve select elementine ekle
        $.ajax({
            url: "/is-paketleri/get-stages",
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            data: { work_package_no: work_package_no },
            success: function (response) {
                let select = $("#stage");
                select.empty(); // Mevcut seçenekleri temizle

                console.log(response);

                select.append(new Option("Seçiniz", ""));

                workPackageStages = response.workPackageStages;

                // Yeni seçenekleri ekle
                workPackageStages.forEach(function (item) {
                    //console.log(item);
                    select.append(new Option(item.name, item.id));
                });

                // Eğer mevcut bir iş paketi numarası varsa, onu seçili yap
                if (response.selectedStage) {
                    console.log(response.selectedStage.stage);
                    select.val(response.selectedStage.stage);
                }
            },
            error: function (xhr, status, error) {
                console.error("İş paketi aşamaları alınamadı:", error);
            },
        });

        //AJAX çağrısı ile sorumluları al ve select elementine ekle
        $.ajax({
            url: "/is-paketleri/get-users",
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            data: { work_package_no: work_package_no },
            success: function (response) {
                let select = $("#responsible");
                select.empty(); // Mevcut seçenekleri temizle

                console.log(response);

                select.append(new Option("Seçiniz", ""));

                users = response.users;
                //console.log(users);
                // Yeni seçenekleri ekle
                users.forEach(function (item) {
                    user = item.name + " " + item.last_name;
                    select.append(new Option(user, item.id));
                });

                selectedResponsible = response.selectedUser;
                //console.log("selectedResponsible",selectedResponsible);

                if (selectedResponsible) {
                    //console.log(response.selectedResponsible.responsible)
                    select.val(selectedResponsible.responsible);
                }
            },
            error: function (xhr, status, error) {
                console.error("Sorumlular alınamadı:", error);
            },
        });
    }

    function selectWorkPackage(id) {
        $("#selectWorkPackage")
            .off("change")
            .on("change", function () {
                //selectWorkPackageNo'nun seçili olan değerini alıyor
                let workPackageNo = $("#selectWorkPackage").val();

                //modalı kapatıyor
                $("#workPackagesModal").modal("hide");

                //seçilen değeri input alanına yazdırıyor
                $("#work_package_no" + id).val(workPackageNo);

                //aynı zamanda  bu iş paketini veri tabanı kaydeder

                let subGroupID = $("#sub_group_id").val();
                console.log("subGroupIDx " + subGroupID);
                let precautionID = $("#precaution_id2").val();
                console.log("precautionIDx " + precautionID);

                let formData = new FormData();
                formData.append("work_package_no", workPackageNo);
                formData.append("sub_group_id", subGroupID);
                formData.append("precaution_id", precautionID);

                //var olan iş paketini girmek için kullanacağız
                $.ajax({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },

                    url: "/is-paketleri/copy",

                    type: "POST",

                    data: formData,
                    processData: false,
                    contentType: false,

                    success: function (response) {
                        console.log("Responsey " + response);
                        if (response == 1) {
                            swal.fire({
                                title: "Başarılı",
                                icon: "success",
                                //html: response + " Numaralı İş Paketi Eklendi",
                            });
                        } else if (response == 0) {
                            swal.fire({
                                title: "Hata",
                                icon: "warning",
                                html: "Hata 151020241606",
                            });
                        }
                    },
                    error: function (xhr) {
                        swal.fire({
                            title: "Uyarı!",
                            icon: "warning",
                            text: "14140417 Bir Hata Oluştu!",
                        });
                    },
                });
            });
    }

    function selectCompensatoryControlForm(id) {
        $("#selectCompensatoryControlForm")
            .off("change")
            .on("change", function () {
                //selectCompensatoryControlForm'un seçili olan değerini alıyor
                let compensatoryControlFormNo = $(
                    "#selectCompensatoryControlForm"
                ).val();

                //modalı kapatıyor
                $("#compensatoryModal").modal("hide");

                //seçilen değeri input alanına yazdırıyor
                $("#compensatory_control_form_id" + id).val(
                    compensatoryControlFormNo
                );
            });
    }

    $(document).on("change", "#no_duration", function () {
        if ($(this).is(":checked")) {
            // Checkbox seçiliyse input alanını devre dışı bırak
            $("#duration").prop("disabled", true);
        } else {
            // Checkbox seçili değilse input alanını etkinleştir
            $("#duration").prop("disabled", false);
        }
    });

    $("#compensatory_save_form").submit(function (e) {
        e.preventDefault();

        let sub_group_id = $("#sub_group_id").val();

        let formData = $(this).serialize();
        formData += "&sub_group_id=" + sub_group_id;

        console.log(formData);

        let id = $("#precaution_id").val();
        console.log("id -> " + id);

        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },

            url: "/telafi-edici-kontrol-formlari",

            type: "POST",

            data: formData,

            success: function (response) {
                console.log(response);
                if (response > 0) {
                    //     responsive datatable'ın details kısmında değil ise bu şekilde eklemek zorunda kaldım gelen değeri
                    textID = "#compensatory_control_form_id" + id;
                    console.log("=> " + textID);
                    $(textID).val(response);

                    //     responsive datatable'ın details kısmında değil ise bu şekilde eklemek zorunda kaldım gelen değeri
                    //     var inputElement = $('li[data-dt-column="6"] input[type="text"][data="' + tedbir_id + '"]');
                    //     inputElement.val(e);

                    swal.fire({
                        title: "Başarılı",
                        icon: "success",
                        html:
                            response +
                            " Numaralı Telafi Edici Kontrol No Eklendi",
                    });
                    $("#compensatoryModal").modal("hide");
                } else if (response == 0) {
                    swal.fire({
                        title: "Başarılı",
                        icon: "success",
                        html: "Güncelleme Başarılı",
                    });
                } else {
                    swal.fire({
                        title: "Uyarı",
                        icon: "warning",
                        html: "Lütfen Tüm Alanları Dikkatli Bir Şekilde Doldurun",
                    });
                }
            },
            error: function (xhr) {
                if (xhr.status == 422) {
                    // Validasyon hatası durumu
                    var errors = xhr.responseJSON.errors;

                    console.log(errors);

                    swal.fire({
                        title: "Uyarı!",
                        icon: "warning",
                        text: "Lütfen Tüm Alanları Doldurun",
                    });
                }
            },
        });
    });

    $("#work_packages_save_form").submit(function (e) {
        e.preventDefault();

        let sub_group_id = $("#sub_group_id").val();

        let formData = $(this).serialize();
        formData += "&sub_group_id=" + sub_group_id;

        console.log(formData);

        let id = $("#precaution_id2").val();
        console.log("id" + id);

        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },

            url: "/is-paketleri",

            type: "POST",

            data: formData,

            success: function (response) {
                console.log("Response " + response);
                if (response > 0) {
                    //     responsive datatable'ın details kısmında değil ise bu şekilde eklemek zorunda kaldım gelen değeri
                    textID = "#work_package_no" + id;
                    $(textID).val(response);

                    //     responsive datatable'ın details kısmında değil ise bu şekilde eklemek zorunda kaldım gelen değeri
                    //     var inputElement = $('li[data-dt-column="6"] input[type="text"][data="' + tedbir_id + '"]');
                    //     inputElement.val(e);

                    swal.fire({
                        title: "Başarılı",
                        icon: "success",
                        html: response + " Numaralı İş Paketi Eklendi",
                        // didClose: () => {
                        //     location.reload();
                        // }
                    });
                } else if (response == 0) {
                    swal.fire({
                        title: "Başarılı",
                        icon: "success",
                        html: "Güncelleme Başarılı",
                    });
                } else {
                    swal.fire({
                        title: "Uyarı",
                        icon: "warning",
                        html: "Lütfen Tüm Alanları Dikkatli Bir Şekilde Doldurun",
                    });
                }
            },
            error: function (xhr) {
                swal.fire({
                    title: "Uyarı!",
                    icon: "warning",
                    text: "11011530 Bir Hata Oluştu!",
                });
            },
        });
    });

    $("#gap-analysis-dt").on("click", ".showProofBTN", function () {
        $(".modal-body #proofDocuments").html("");

        let id = $(this).attr("data");
        let formNo = $("#main_group_id").val();

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
                            '<td><button class="btn btn-sm" onclick="confirmDeleteFile(\'' +
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

    $("#gap-analysis-dt").on("click", ".proofBTN", function () {
        id = $(this).attr("data");
        $("#precaution").val(id);

        $("#proofDocumentModal").modal("show");
    });

    $("#upload_form").submit(function (e) {
        e.preventDefault();

        //precaution_id
        let id = $("#precaution").val();
        let formNo = $("#main_group_id").val();

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

        $.ajax({
            url: "/file-upload-gap", // Laravel route
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
});

function deleteFile(fileName) {
    $.ajax({
        url: "/delete-file-gap", // Laravel route
        type: "POST",
        data: { fileName: fileName },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
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

    console.log("Deleting file:", fileName);
}

function confirmDeleteFile(fileName) {
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
            deleteFile(fileName);
        }
    });
}

function showQuestionsModal(id, event) {
    event.preventDefault();
    $("#questionsModal").modal("show");

    //    // Tıklama olayının koordinatlarını al
    //    let clickX = event.clientX;
    //    let clickY = event.clientY;

    //    console.log(clickY);
    // Modalın konumunu ayarla
    // $("#questionsModal").css({
    //     position: "fixed",
    //     top: 0 + "px",
    //     left: 0+ "px",
    // });

    console.log("tedbir id " + id);
    if (id) {
    }
    $.ajax({
        url: "/bosluk-analizi/getQuestions",
        type: "POST",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        data: { id: id },

        success: function (response) {
            console.log(response);
            let questions = response.questions;
            let solutionSuggestions = response.solution_suggestions;

            let questionsDiv = $("#questions");
            questionsDiv.empty(); // Clear any existing content

            let solutionSuggestionsDiv = $("#solution_suggestions");
            solutionSuggestionsDiv.empty(); 

            let precautionDiv = $("#precautions");
            precautionDiv.empty();

            let precautionDescriptionDiv=$("#precaution_description");
            precautionDescriptionDiv.empty();

            let precaution_description=response.precaution_description;

            let precaution = response.precaution;
            console.log("precautions " + precaution);

            precautionDiv.append(`<b>${precaution}</b>`);

            precautionDescriptionDiv.append(`<b>Tanım :</b> <i>${precaution_description}</i>`);


            let list = $("<ul></ul>");
            questions.forEach((question, index) => {
                

                list.append(`<li>${index + 1}. ${question}</li>`);
            });
            questionsDiv.append(list);
           

            let html ="";
           
            html=response.solution_suggestion;
           
            solutionSuggestionsDiv.append(html);
        },
        error: function (xhr, status, error) {},
    });
}
