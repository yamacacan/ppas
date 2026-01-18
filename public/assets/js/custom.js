$(document).ready(function () {
    //Varlık Ana Gruba Göre Varlık Grubu Gelmesi
    selectedValue = $("#main_group_id").val();

    //Listelendiğinde hiddendaki değeri alıyor
    selectedSubGroupValue = $("#selected_sub_group").val();

    getSubGroups(selectedValue, selectedSubGroupValue);

    $("#main_group_id").change(function () {
        var selectedValue = $(this).val();

        getSubGroups(selectedValue, selectedSubGroupValue);
    });

    function getSubGroups(selectedValue, selectedSubGroupValue) {
        console.log(selectedSubGroupValue);

        $.ajax({
            url: "/get-sub-groups",
            method: "GET",
            data: {
                main_group_id: selectedValue,
                _token: $('meta[name="csrf-token"]').attr("content"), // CSRF belirteci
            },

            success: function (data) {
                $("#btnSelect").show();
                console.log(data);

                $("#sub_group_id").empty();

                // Gelen verileri alt seçim kutusuna ekle
                $.each(data, function (key, subGroup) {
                    $("#sub_group_id").append(
                        $("<option>", {
                            value: subGroup.id,
                            text: subGroup.group_no + " - " + subGroup.name,
                            selected:
                                selectedSubGroupValue == subGroup.id
                                    ? true
                                    : false,
                        })
                    );
                });
            },
        });
    }

    //Varlık Ana Gruba Göre Varlık Grubu Gelmesi
    $("#main_group_id_copy").change(function () {
        var selectedValue = $(this).val();

        console.log(selectedValue);

        $.ajax({
            url: "/get-sub-groups",
            method: "GET",
            data: {
                main_group_id: selectedValue,
                _token: $('meta[name="csrf-token"]').attr("content"), // CSRF belirteci
            },

            success: function (data) {
                console.log(data);

                $("#sub_group_id_copy").empty();

                // Gelen verileri alt seçim kutusuna ekle
                $.each(data, function (key, subGroup) {
                    $("#sub_group_id_copy").append(
                        $("<option>", {
                            value: subGroup.id,
                            text: subGroup.group_no + " - " + subGroup.name,
                        })
                    );
                });
            },
        });
    });

    $("#main_group_for_surveys").change(function () {
        var selectedValue = $(this).val();

        console.log(selectedValue);

        $.ajax({
            url: "/get-sub-groups",
            method: "GET",
            data: {
                main_group_id: selectedValue,
                _token: $('meta[name="csrf-token"]').attr("content"), // CSRF belirteci
            },

            success: function (data) {
                console.log(data);

                $("#sub_group_id").empty();

                //anket katılımcı sayfasında tüm grupları seçmek için
                if (data == 999) {
                    $("#sub_group_id").append(
                        $("<option>", {
                            value: 999,
                            text: "Tüm Grupları Seç",
                        })
                    );
                } else {
                    $("#sub_group_id").append(
                        $("<option>", {
                            value: 888,
                            text: "Tüm Alt Grupları Seç",
                        })
                    );
                    // Gelen verileri alt seçim kutusuna ekle
                    $.each(data, function (key, subGroup) {
                        $("#sub_group_id").append(
                            $("<option>", {
                                value: subGroup.id,
                                text: subGroup.group_no + " - " + subGroup.name,
                            })
                        );
                    });
                }
            },
        });
    });

    $("#precaution_title").hide();
    $("#precaution").hide();
    $("#question").hide();
    $("#solution_suggestion_ta").hide();
    $("#audit_suggestion_ta").hide();

    //Tedbir Ana Başlığına Göre Tedbir Alt Başlığı Gelmesi
    $("#precaution_main_title_id").change(function () {
        $("#precaution_title").show();
        $("#precaution").hide();
        $("#question").hide();
        $("#solution_suggestion_ta").hide();
        $("#audit_suggestion_ta").hide();
        var selectedValue = $(this).val();

        console.log(selectedValue);

        $.ajax({
            url: "/get-precaution-titles",
            method: "GET",
            data: {
                precaution_main_title_id: selectedValue,
                _token: $('meta[name="csrf-token"]').attr("content"), // CSRF belirteci
            },

            success: function (data) {
                $("#parent_id").empty();

                $("#parent_id").append(
                    $("<option>", { value: 0, text: "Seçiniz" })
                );

                // Gelen verileri alt seçim kutusuna ekle
                $.each(data, function (key, subTitle) {
                    //console.log(subTitle.title_no);
                    $("#parent_id").append(
                        $("<option>", {
                            value: subTitle.id,
                            text: subTitle.title_no + " - " + subTitle.title,
                        })
                    );
                });
            },
        });
    });

    //Tedbir Alt Başlığına Göre Tedbir Gelmesi
    $("#parent_id").change(function () {
        $("#precaution").show();
        $("#question").hide();
        $("#solution_suggestion_ta").hide();
        $("#audit_suggestion_ta").hide();
        var selectedValue = $(this).val();

        console.log(selectedValue);

        $.ajax({
            url: "/get-precautions",
            method: "GET",
            data: {
                precaution_title_id: selectedValue,
                _token: $('meta[name="csrf-token"]').attr("content"), // CSRF belirteci
            },

            success: function (data) {
                $("#precaution_id").empty();

                //console.log(data);
                $("#precaution_id").append(
                    $("<option>", { value: 0, text: "Seçiniz" })
                );

                // Gelen verileri alt seçim kutusuna ekle
                $.each(data, function (index, precaution) {
                    //console.log(precaution);
                    $("#precaution_id").append(
                        $("<option>", {
                            value: precaution.id,
                            text:
                                precaution.precaution_no +
                                " - " +
                                precaution.name,
                        })
                    );
                });
            },
        });
    });

    //Tedbire göre önerilerinin gelmesi
    $("#precaution_id").change(function () {
        var selectedValue = $(this).val();

        $.ajax({
            url: "/get-suggestions",
            method: "GET",
            data: {
                precaution_id: selectedValue,
                _token: $('meta[name="csrf-token"]').attr("content"), // CSRF belirteci
            },

            success: function (data) {
                //console.log(data);

                //Eğer çözüm önerisi varsa
                if ($("#solution_suggestion").length) {
                    $("#solution_suggestion").empty();
                    $("#solution_suggestion").text(data.solution_suggestion);
                    $("#solution_suggestion_ta").show();
                }

                //Eğer Denetim Önerisi varsa
                if ($("#audit_suggestion").length) {
                    $("#audit_suggestion").empty();
                    $("#audit_suggestion").text(data.audit_suggestion);
                    $("#audit_suggestion_ta").show();
                }
            },
        });
    });


    //tedbir etkinlik durumu için
    var auditID = $("#audit_id").val();
    var selectedValue = $("#PAS_main_group_id").val();


    //Tedbire atanmış varlık grupları gelmesi
    $("#PAS_main_group_id").change(function () {
        var selectedValue = $(this).val();

        // console.log("pas_main_group_id " + selectedValue);
        // console.log("auditID " + auditID);

        getPasSubGroups(selectedValue, selectedSubGroupValue, auditID);
    });


    //boşluk analizinde sayfa yüklendiğinde de çalışması için
    if (auditID != null) {
        //Sayfa yüklendiğinde de çalışması için
        getPasSubGroups(selectedValue, selectedSubGroupValue, auditID);
    }

    //Tedbire atanmış varlık grupları gelmesi
    function getPasSubGroups(selectedValue, selectedSubGroupValue, auditID) {

        $.ajax({
            url: "/get-PAS-subgroup",
            method: "GET",
            data: {
                mainGroupID: selectedValue,
                auditID: auditID,
                _token: $('meta[name="csrf-token"]').attr("content"), // CSRF belirteci
            },

            success: function (data) {
                $("#sub_group_id").empty();
                //console.log(JSON.stringify(data, null, 2));


                if (data.length == 0) {
                    $("#sub_group_id").append(
                        $("<option>", {
                            value: 0,
                            text: "Bu grupta denetime atanan tedbir yok",
                        })
                    );

                    $("#btnSelect").hide();
                } else {
                    $("#btnSelect").show();
                }
                // Gelen verileri alt seçim kutusuna ekle
                $.each(data, function (key, item) {
                    console.log(item.name);
                    $("#sub_group_id").append(
                        $("<option>", {
                            value: item.id,
                            text: item.group_no + " - " + item.name,
                            selected:
                                selectedSubGroupValue == item.id ? true : false,
                        })
                    );
                });
            },
        });
    }

    //uygulanabilirlik bildirgesi tümünü seç
    $('input[type="checkbox"]').change(function () {
        if ($(this).attr("id").startsWith("checkboxSelectAll")) {
            var str = $(this).attr("name");

            var number = str.split("_")[1];

            console.log(number);

            //Tümünü seç işaretlendiğinde aşağıdaki kod alt checkboxların değerini tümüne check de ne varsa o yapıyor
            $('input[checkboxGroup="' + number + '"]').prop(
                "checked",
                $(this).prop("checked")
            );
        }
    });

    //roller tümünü seç
    $("input[data-select-all]").on("change", function () {
        var parentId = $(this).data("parent-id");
        var checked = $(this).is(":checked");
        $(`#v-pills-${parentId} input[type=checkbox]`)
            .not(this)
            .prop("checked", checked);
    });

    //Rol değiştiği zaman rol izinlerin alt menüde gözükmesi
    //Rol değiştiği zaman rol izinlerin alt menüde gözükmesi
    // $("#role_id").change(function (e) {
    //     let roleId = e.target.value;
    //
    //     $(".form-check-input").prop("checked", false);
    //
    //     $.ajax({
    //         type: "get",
    //         url: `/roles/${roleId}/permissions`,
    //         dataType: "json",
    //         success: function (permissions) {
    //             //console.log(permissions)
    //
    //             permissions.forEach(function (permissionName) {
    //                 //$(`#switchCheck${permissionId}`).prop("checked", true);
    //                 $(`.form-check-input[data="${permissionName}"]`).prop(
    //                     "checked",
    //                     true
    //                 );
    //             });
    //         },
    //         error: function (xhr) {
    //             console.log("Hata oluştu: ", xhr.responseText);
    //         },
    //     });
    // });

    $("#users").change(function (e) {
        var selectedValue = $(this).val();

        //console.log(selectedValue);

        $.ajax({
            url: "/get-user",
            method: "GET",
            data: {
                userId: selectedValue,
                _token: $('meta[name="csrf-token"]').attr("content"), // CSRF belirteci
            },

            success: function (data) {
                //$('#sub_group_id').empty();
                //console.log(JSON.stringify(data, null, 2));
                //console.log(JSON.stringify(data));
                //console.log(data);

                $("#name").val(data["name"]);
                $("#last_name").val(data["last_name"]);
                $("#mail").val(data["email"]);
            },
        });
    });

    //denetim ekleme işlemleri için
    $("#auditor_label").hide();
    $("#auditor").hide();
    $("#auditor").val("");

    if ($("#auditor_type").val() == 1) {
        $("#auditor_label").hide();
        $("#auditor").hide();
        $("#auditor").val("");
    }

    $("#auditor_type").change(function () {
        if ($("#auditor_type").val() == 1) {
            $("#auditor_label").hide();
            $("#auditor").hide();
        } else {
            $("#auditor_label").show();
            $("#auditor").show();
        }
    });
    //denetim ekleme işlemleri için BİTTİ

    //Çalışma Formlarında Denetim değiştiği Zaman
    $("#audit_id").change(function (e) {
        let auditID = e.target.value;

        $.ajax({
            type: "get",
            url: `/calisma-formu/${auditID}`,
            dataType: "json",
            success: function (data) {
                console.log(data);

                // Gelen verileri alt seçim kutusuna ekle
                if (data.length != 0) {
                    $("#workFormID").empty();
                    $.each(data, function (index, value) {
                        $("#workFormID").append(
                            $("<option>", {
                                value: value.formNo,
                                text: `Çalışma Formu ${value.formNo} (${value.mainGroup})`,
                            })
                        );
                    });
                } else {
                    $("#workFormID").empty();
                }
            },
            error: function (xhr) {
                console.log("Hata oluştu: ", xhr.responseText);
            },
        });
    });


});
