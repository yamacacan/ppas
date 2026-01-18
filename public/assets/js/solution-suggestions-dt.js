$(document).ready(function () {
    var table = $("#solution-suggestions-dt").DataTable({
        responsive: true,
        stateSave: true,
        language: { url: "../assets/json/turkish.json" },
        dom: "Blrtip", // DOM yapısını optimize ettim
        lengthChange: true,
        ordering: false,
        lengthMenu: [10, 25, 50, 100, 1000],
        buttons: [],

        initComplete: function () {
            var table = this.api();

            // Filtreleme kutularını yalnızca belirli sütunlara ekle
            table.columns().every(function (index) {
                // Filtre eklemek istediğiniz sütunları belirtin (örneğin: 1, 2, ve 3. sütunlar)
                if ([1, 2, 3].includes(index)) {
                    var column = this;
                    var input = $(
                        '<input type="text" class="form-control" placeholder="' +
                            $(column.header()).text() +
                            ' Filtrele">'
                    )
                        .appendTo($(column.header()).empty())
                        .on("keyup change clear", function () {
                            if (column.search() !== this.value) {
                                column.search(this.value).draw();
                            }
                        });
                } else {
                    // Filtre eklemek istemediğiniz sütunlar için başlık metnini koruyun
                    $(this.header()).text($(this.header()).text());
                }
            });
        }
    });
});
